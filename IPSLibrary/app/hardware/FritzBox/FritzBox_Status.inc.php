<?

    IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ('IPSInstaller.inc.php',      'IPSLibrary::install::IPSInstaller');
    IPSUtils_Include ('FritzBox.inc.php',      'IPSLibrary::app::hardware::FritzBox');
    IPSUtils_Include ('FritzBox_Configuration.inc.php',      'IPSLibrary::config::hardware::FritzBox');
    IPSUtils_Include ('Utils_Variable.inc.php',      'IPSLibrary::app::modules::Utils');
    
    // prevent simultaneous logins to the devices 
    $semaphoreName = "SemaphoreFritzBoxStatus";
    if(!IPS_SemaphoreEnter($semaphoreName, 1)) {
        return;
    }
    
    function CreateProfile_Unit($Name, $unit) {
        @IPS_DeleteVariableProfile($Name);
        IPS_CreateVariableProfile($Name, 1);
        IPS_SetVariableProfileText($Name, "", $unit);
        IPS_SetVariableProfileValues($Name, 0, 0, 0);
        IPS_SetVariableProfileDigits($Name, 0);
        IPS_SetVariableProfileIcon($Name, "");
    }
    
    function createVariableWithUnitProfile($varName, $categoryId, $unitProfiles, $unitName) {
        $unitNameClean = str_replace("/", "", $unitName);
        if(!array_key_exists($unitNameClean, $unitProfiles)) {
            $unitProfiles[$unitNameClean] = 'FritzBox_'.$unitNameClean;
            CreateProfile_Unit($unitProfiles[$unitNameClean], $unitName);
        }
        $variable = new Variable($varName, $categoryId, 1, 10, $unitProfiles[$unitNameClean]);
        $variable->setLogging(true);
        
        return $variable;
    }
    
    function evaluateShouldUpdate($unitName, $variable, $newValue) {
        $logSettings = get_FritzBoxSettings()["LOG"];
    
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
        $varId = @IPS_GetVariableIDByName($varName, $categoryId);
        if ($varId === false) {
            $variable = createVariableWithUnitProfile($varName, $categoryId, $unitProfiles, $unitName);
        } else {
            $variable = new Variable($varId);
        }
        
        $shouldUpdateValue = evaluateShouldUpdate($unitName, $variable, $newValue);
        if($shouldUpdateValue) {
            $variable->value = $newValue;
        }
    }
    
    function FritzBox_ReadDslInformation($fritzBox) {
        $CategoryIdStateReceive = $fritzBox->getStateCategoryId("Receive");
        $CategoryIdStateSend = $fritzBox->getStateCategoryId("Send");
        
        $internetDslStatus = $fritzBox->getInternetDSL();
        $profiles = array();
        foreach($internetDslStatus as $measure) {
            $varName = $measure[0];
            $data = $measure[1];
            if(count($data) < 4) continue;
            $unit = $data[1];
            
            handleVariable($varName, $CategoryIdStateReceive, $profiles, (int) $data[2], $unit);
            handleVariable($varName, $CategoryIdStateSend, $profiles, (int) $data[3], $unit);
        }
    }
    
    function FritzBox_ReadDectMonitorData($fritzBox) {
        $data = $fritzBox->getDectMonitorData();
        $xpath = new DOMXPath($data);
        $CategoryIdDect = $fritzBox->getStateCategoryId("Dect");
        
        function copyVar($sourceElement, $varName, $parentId, $type, $profile = '') {
            $value = $sourceElement->nodeValue;
            $variable = new Variable($varName, $parentId, $type, 10, $profile);
            $variable->value = $value;
            return $variable;
        }
        
        $visibleElements = array("State", "FullName", "Quality");
        // get active dect nodes
        $pos = 0;
        foreach($xpath->query('//DectMoniInfo/DECTHG[Subscribed = "1"]') as $e) {
            $id = $e->getAttribute("id");
            $instanceId = CreateDummyInstance($id, $CategoryIdDect, $pos++);
            
            foreach($e->childNodes as $childNode) {
                if($childNode->nodeType !== XML_ELEMENT_NODE) continue;
                
                $nodeName = $childNode->nodeName;
                if($nodeName == "State") {
                    $var = copyVar($childNode, $nodeName, $instanceId, 1, "FritzBox_DectStatus");
                } else {
                    $var = copyVar($childNode, $nodeName, $instanceId, 3);
                }
                IPS_SetHidden($var->variableId, !in_array($nodeName, $visibleElements));
            }
        }
    }
    
    function FritzBox_ReadStatus($deviceName, $deviceConfig) {
        $fritzBox = new FritzBox($deviceName, $deviceConfig[DEVICE_IP], $deviceConfig[DEVICE_PASSWORD]);
        
        FritzBox_ReadDslInformation($fritzBox);
        // execute this based on a boolean switch for people without call monitoring
        FritzBox_ReadDectMonitorData($fritzBox);
    }
    
    if(isset($IPS_SENDER)) {
        if ($IPS_SENDER == "RunScript" || $IPS_SENDER == "Execute" || $IPS_SENDER == "TimerEvent") {
            $devices = get_FritzBoxDevices();
            foreach($devices as $deviceName => $deviceConfig) {
                FritzBox_ReadStatus($deviceName, $deviceConfig);
            }
        } else {
            IPSLogger_Wrn(__file__, "Unhandled IPS_SENDER: ".$IPS_SENDER);
        }
    }
    
    IPS_SemaphoreLeave($semaphoreName);

?>