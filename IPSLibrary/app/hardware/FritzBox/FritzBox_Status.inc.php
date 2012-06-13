<?

    IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ('IPSInstaller.inc.php',      'IPSLibrary::install::IPSInstaller');
    IPSUtils_Include ('FritzBox.inc.php',      'IPSLibrary::app::hardware::FritzBox');
    IPSUtils_Include ('FritzBox_Configuration.inc.php',      'IPSLibrary::config::hardware::FritzBox');
    IPSUtils_Include ('Utils_Variable.inc.php',      'IPSLibrary::app::modules::Utils');
    
    define("FRITZBOX_DATA_BASE_PATH", "Program.IPSLibrary.data.hardware.FritzBox");
    
    // prevent simultaneous logins to the devices 
    $semaphoreName = "SemaphoreFritzBoxStatus";
    if(!IPS_SemaphoreEnter($semaphoreName, 1)) {
        return;
    }
    
    $i18n["DslMaxDataRate"] = array("de" => "Datenrate Max.");
    $i18n["DslCableCapacity"] = array("de" => "Leitungskapazität");
    $i18n["DslAtmRate"] = array("de" => "Aktuelle Datenrate");
    $i18n["DslSignalNoiseDistance"] = array("de" => "Störabstandsmarge");
    $i18n["DslLineLoss"] = array("de" => "Leitungsdämpfung");
    
    $fritzBoxSettings = array(
        "LOG"                   => array(
            "MINUTES_BETWEEN_LOGS"          => 30,
            "dB"        => array(
                // log when the value has changed at least by the given amount
                //"MIN_CHANGE"                => 3,
                // log when the value has changed at least by the given amount
                "MIN_CHANGE_PERCENT"         => 35,
                // force log when the value has changed at least by the given amount
                "MIN_CHANGE_PERCENT_FORCE"   => 70,
                // force log every x minutes
                "MINUTES_BETWEEN_LOGS"       => 5,
                // log when the value has changed at least by the given amount
                "MINUTES_BETWEEN_LOGS_MIN_CHANGE"         => 20,
            ),
        ),
    );
    
    function getTdContent($element) {
        $ret = array();
        foreach($element->childNodes as $childNode) {
            if($childNode->nodeType === XML_ELEMENT_NODE) {
                $ret[] = utf8_decode($childNode->nodeValue);
            }
        }
        return $ret;
    }
    
    function setVariableLogging($varId, $enabled = true) {
        $archiveHandlerId = IPS_GetInstanceIDByName("Archive Handler", 0);
        AC_SetLoggingStatus($archiveHandlerId, $varId, $enabled);
        @IPS_ApplyChanges($varId);
    }
    
    function CreateProfile_Unit($Name, $unit) {
        @IPS_DeleteVariableProfile($Name);
        IPS_CreateVariableProfile($Name, 1);
        IPS_SetVariableProfileText($Name, "", $unit);
        IPS_SetVariableProfileValues($Name, 0, 0, 0);
        IPS_SetVariableProfileDigits($Name, 0);
        IPS_SetVariableProfileIcon($Name, "");
    }
    
    function getI18N($key, $lang = "de") {
        global $i18n;
        
        return utf8_decode($i18n[$key][$lang]);
    }
    
    function createVariableWithUnitProfile($varName, $categoryId, $unitProfiles, $unitName) {
        $unitNameClean = str_replace("/", "", $unitName);
        if(!array_key_exists($unitNameClean, $unitProfiles)) {
            $unitProfiles[$unitNameClean] = 'FritzBox_'.$unitNameClean;
            CreateProfile_Unit($unitProfiles[$unitNameClean], $unitName);
        }
        $variable = new Variable($varName, $categoryId, 1, 10, $unitProfiles[$unitNameClean]);
        setVariableLogging($variable->variableId, true);
        
        return $variable;
    }
    
    function evaluateShouldUpdate($logSettings, $unitName, $variable, $newValue) {
        $oldValue = $variable->value;
        $minValue = max(1, min($oldValue, $newValue));
        $percChange = round(abs($newValue - $oldValue) / $minValue, 2);
        $reasonForceUpdate = "";
        $reasonShouldUpdate = "";
        
        // force an update every X minutes
        $variableUpdated = $variable->getVariableMetadata()["VariableUpdated"];
        $forceUpdate = $variableUpdated < time() - (60 * $logSettings["MINUTES_BETWEEN_LOGS"]);
        if($forceUpdate) $reasonForceUpdate .= 1;
        
        // check if variable has changed at all
        $shouldUpdate = $oldValue!== $newValue;
        if($shouldUpdate) $reasonShouldUpdate .= 1;
        
        if(isset($logSettings[$unitName])) {
            $unitSettings = $logSettings[$unitName];
            
            // should
            if(isset($unitSettings["MIN_CHANGE"])) {
                $shouldUpdate &= abs($oldValue- $newValue) >= $unitSettings["MIN_CHANGE"];
                if($shouldUpdate) $reasonShouldUpdate .= 2;
            } else if(isset($unitSettings["MIN_CHANGE_PERCENT"])) {
                $shouldUpdate &= $percChange >= $unitSettings["MIN_CHANGE_PERCENT"] / 100;
                if($shouldUpdate) $reasonShouldUpdate .= 3;
            }
            if(isset($unitSettings["MIN_CHANGE_PERCENT_FORCE"])) {
                $shouldUpdate |= $percChange >= $unitSettings["MIN_CHANGE_PERCENT_FORCE"] / 100;
                if($shouldUpdate) $reasonShouldUpdate .= 4;
            }
            
            // force
            if(isset($unitSettings["MINUTES_BETWEEN_LOGS"])) {
                $forceUpdate |= $variableUpdated < time() - (60 * $unitSettings["MINUTES_BETWEEN_LOGS"]);
                if($forceUpdate) $reasonForceUpdate .= 2;
            }
            if(isset($unitSettings["MINUTES_BETWEEN_LOGS_MIN_CHANGE"])) {
                $forceUpdate |= $percChange >= $unitSettings["MINUTES_BETWEEN_LOGS_MIN_CHANGE"] / 100;
                if($forceUpdate) $reasonForceUpdate .= 3;
            }
        } else {
            //$shouldUpdate &= true;
        }
        return $forceUpdate || $shouldUpdate;
    }
    
    function handleVariable($varName, $categoryId, $unitProfiles, $newValue, $unitName) {
        global $fritzBoxSettings;
        
        $varId = @IPS_GetVariableIDByName($varName, $categoryId);
        if ($varId === false) {
            $variable = createVariableWithUnitProfile($varName, $categoryId, $unitProfiles, $unitName);
        } else {
            $variable = new Variable($varId);
        }
        
        $shouldUpdateValue = evaluateShouldUpdate($fritzBoxSettings["LOG"], $unitName, $variable, $newValue);
        if($shouldUpdateValue) {
            $variable->value = $newValue;
        }
    }
    
    function handleDevice($device) {
        $ip = $device[DEVICE_IP];
        
        $PathToDevice = FRITZBOX_DATA_BASE_PATH.".IP".str_replace(".", "-", $ip);
        $PathToDeviceState = $PathToDevice.".State";
        $PathToDeviceStateReceive = $PathToDeviceState.".Receive";
        $PathToDeviceStateSend = $PathToDeviceState.".Send";
        $CategoryIdRoot = IPSUtil_ObjectIDByPath($PathToDevice);
        $CategoryIdState = IPSUtil_ObjectIDByPath($PathToDeviceState);
        $CategoryIdStateReceive = IPSUtil_ObjectIDByPath($PathToDeviceStateReceive);
        $CategoryIdStateSend = IPSUtil_ObjectIDByPath($PathToDeviceStateSend);
        
        $fritzBox = new FritzBox($ip, $device[DEVICE_PASSWORD], $CategoryIdRoot);
        $internetDslStatus = $fritzBox->getInternetDSL();
        $profiles = array();
        foreach($internetDslStatus as $measure) {
            $varName = $measure[0];
            $data = $measure[1];
            $unit = $data[1];
            
            handleVariable($varName, $CategoryIdStateReceive, $profiles, (int) $data[2], $unit);
            handleVariable($varName, $CategoryIdStateSend, $profiles, (int) $data[3], $unit);
        }
    }
    
    if(isset($IPS_SENDER)) {
        if ($IPS_SENDER == "RunScript" || $IPS_SENDER == "Execute" || $IPS_SENDER == "TimerEvent") {
            $devices = get_FritzBoxDevices();
            foreach($devices as $device) {
                handleDevice($device);
            }
        } else {
            IPSLogger_Wrn(__file__, "Unhandled IPS_SENDER: ".$IPS_SENDER);
        }
    }
    
    IPS_SemaphoreLeave($semaphoreName);

?>