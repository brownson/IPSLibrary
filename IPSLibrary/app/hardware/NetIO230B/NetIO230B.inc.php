<?

IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
IPSUtils_Include ('HeatingControl_RoomStateVariable.inc.php',      'IPSLibrary::app::modules::HeatingControl');

// variable names for username, password and interface
define("v_USERNAME", "Username");
define("v_PASSWORD", "Password");
define("v_IP", "IP");
define("v_NETIO_SETTINGS", "Settings");
define("MAX_RETRIES", 3);
define("CONNECTION_ERROR_LOG_INTERVAL", 60); // in minutes

/**
 * Extracts and returns the login credentials for the given netIO230 device from variables with the names defined in v_USERNAME and v_PASSWORD.
 *
 * @author Dominik Zeiger
 */
function readSettingsFromCategory($objectID) {
    $usernameID = IPS_GetVariableIDByName(v_USERNAME, $objectID);
    $passwordID = IPS_GetVariableIDByName(v_PASSWORD, $objectID);
    $ipID = IPS_GetVariableIDByName(v_IP, $objectID);

    if($usernameID == false || $passwordID == false) {
        $msg = v_USERNAME." or ".v_PASSWORD." variable missing in category ".$objectID;
        IPSLogger_Err(__file__, $msg);
        throw new Exception($msg);
    }
    if($ipID == false) {
        $msg = v_IP." variable missing in category ".$objectID;
        IPSLogger_Err(__file__, $msg);
        throw new Exception($msg);
    }
    $username = GetValue($usernameID);
    $password = GetValue($passwordID);
    $ip = GetValue($ipID);
    
    return array(v_USERNAME => $username, v_PASSWORD => $password, v_IP => $ip);
}

/**
 * Sets the status of the given portid. PortId represents the "interface" register variable of the netio230 device.
 *
 * @author Dominik Zeiger
 */
function NetIO_setPortStatus($portID, $status) {
    $parentID = IPS_GetParent($portID);
    $portVarObject = IPS_GetObject($portID);
    $portName = $portVarObject["ObjectName"];
    $portNumber = substr($portName, 4, strlen($portName));
    
    $moduleID = IPS_GetObjectIDByName(v_NETIO_SETTINGS, $parentID);
    if($status === null) {
        $oldStatus = GetValue($portID);
        IPSLogger_Wrn(__file__, "Overridding power status ".$status." with ".$oldStatus);
        $status = !$oldStatus;
    }
    $statusInt = (int) $status;
    
    $stateStr = "";
    for($i = 1; $i <= 4; $i++) {
        $stateStr .= ($portNumber == $i ? $statusInt : "u");
    }
    if(loginAndSendCommands($moduleID, $stateStr)) {
        SetValue($portID, $status);
    }
}

/**
 * Logins into the netio instance and sets the given parameter.
 *
 * @author Dominik Zeiger
 */
function loginAndSendCommands($netIORegisterVariableID, $parameter) {
    if(!IPS_ObjectExists($netIORegisterVariableID)) {
        IPSLogger_Err(__file__, "Category with $netIORegisterVariableID does not exist. Unable to send commands to NetIO device.");
        return;
    }
    $settings = readSettingsFromCategory($netIORegisterVariableID);
    
    // check if device is available at port 80
    $resp = @fsockopen($settings[v_IP], 80, $errno, $errstr, 1);
    $lastWarningVar = new RoomStateVariable("LAST_CMD_WARNING", $netIORegisterVariableID);
    $lastWarningTime = $lastWarningVar->value;
    $lastWarningIsInitial = $lastWarningVar->isInitial();
    // if not connection could be made, log an error every CONNECTION_ERROR_LOG_INTERVAL minutes
    $now = time();
    if(!$resp) {
        $nextErrorLogTime = $lastWarningTime + (CONNECTION_ERROR_LOG_INTERVAL * 60);
        if($lastWarningIsInitial || $now > $nextErrorLogTime) {
            $lastWarningVar->value = $now;
            IPSLogger_Wrn(__file__, "No response from NetIO device @ ".$settings[v_IP]);
        } else if(!$lastWarningIsInitial) {
            // error was already sent within the last CONNECTION_ERROR_LOG_INTERVAL hours
        }
        return false;
    } else if($resp && !$lastWarningIsInitial) {
        // reset last warning value, when a connection could be made again
        $lastWarningVar->value = 0;
    }
    
    $basePath = "http://".$settings[v_IP]."/tgi/control.tgi?";
    
    $result = false;
    $retries = 0;
    while($result == false && $retries < MAX_RETRIES) {
        $loginURL = $basePath."login=p:".$settings[v_USERNAME].":".$settings[v_PASSWORD];
        $response = Sys_GetURLContent($loginURL);
        $result = parseResponse($response);
        
        $parameterURL = $basePath."p=".$parameter;
        $response = Sys_GetURLContent($parameterURL);
        $result &= parseResponse($response, $netIORegisterVariableID);
        
        $logoutURL = $basePath."quit=quit";
        $response = Sys_GetURLContent($logoutURL);
        $result &= parseResponse($response);
        
        if(!$result) {
            usleep(500000); // 500ms
        }
    }
    return $result;
}

function parseResponse($text, $categoryID = null) {
    //IPSLogger_Wrn(__file__, $text);
    $webmode = false;
    if(strstr($text, "<html>")) {
        $webmode = true;
        $text = strip_tags($text);
    }
    
    $datasets = str_split($text, 4);
    if(strstr($text,"250 OK") != false) {
        // request was executed successfully
        return true;
    } else if($datasets[0] == "250 " || preg_match("/\d \d \d \d/", $text)) {
        if($webmode) {
            $portStatuses = str_replace(" ", "", $text);
        } else {
            $portStatuses = $datasets[1];
        }
        $portCount = strlen($portStatuses);
        if($categoryID == null) {
            throw new Exception("Missing parent category id, which contains the port variables with the name \"PortX\"");
        }
        $parentID = IPS_GetParent($categoryID);
        for($i = 1; $i <= $portCount; $i++) {
            $varName = "Port".$i;
            $varID = IPS_GetVariableIDByName($varName, $parentID);
            if($varID == false) {
                IPSLogger_Wrn(__file__, "Found ".$portCount." port statuses, but missing variable with name ".$varName);
                continue;
            }
            $rawPortStatus = substr($portStatuses, $i - 1, 1);
            if($rawPortStatus != "1" && $rawPortStatus != "0") {
                throw new Exception("Found incompatible status value: ".$rawPortStatus." [Full response:".(print_r($text, true))."]");
            }
            SetValue($varID, $rawPortStatus == "1");
        }
        return true;
    } else if($datasets[0] == "504 ") {
        // already logged in => ignore
        return true;
    } else if($datasets[0] == "100 " && $datasets[1] == "HELL") {
        // succesful login
        //IPSLogger_Inf(__file__, "Login successful");
        return true;
    } else if($datasets[0] == "110 ") {
        // succesful logout
        //IPSLogger_Inf(__file__, "Logout successful");
        return true;
    } else if($datasets[0] == "550 ") {
        // succesful logout
        IPSLogger_Wrn(__file__, "Parameter format wrong");
        return false;
    } else {
        IPSLogger_Wrn(__file__, "Unknown NetIO230 response: ".print_r($text, true));
        return false;
    }
}

if(isset($IPS_SENDER)) {
    if ($IPS_SENDER == "RunScript") {
        if($action == "getStatus") {
            loginAndSendCommands($source, "l");
        } else if($action == "poweroff") {
            NetIO_setPortStatus($IPS_VARIABLE, false);
        } else if($action == "poweron") {
            NetIO_setPortStatus($IPS_VARIABLE, true);
        } else {
            // toggle
            IPSLogger_Inf(__file__, "Unknown action ".$action.". Toggleing power.");
            NetIO_setPortStatus($IPS_VARIABLE, !GetValue($IPS_VARIABLE));
        }
    } else if($IPS_SENDER == "WebFront") {
        NetIO_setPortStatus($IPS_VARIABLE, $IPS_VALUE);
    } else if($IPS_SENDER == "Execute") {
        // trigger installation
        //include_once "IPSInstaller.ips.php";
        
        $DevicePath = 'ROOMS.LIVING';
        $DeviceName = 'IP Steckdose (Weihnachten)';
        $ActionScriptId = 13545;
        $CategoryId = CreateCategoryPath($DevicePath);
        $CategoryIdScripts = CreateCategoryPath("Scripts");
        $newIOInstanceID = CreateDummyInstance($DeviceName, $CategoryId, 2000);
        $netIOScriptID = CreateScript('Device_NetIO230','Device_NetIO230.inc.php', $CategoryIdScripts, 10);
        
        // create settings
        $settingsID = CreateDummyInstance(v_NETIO_SETTINGS, $newIOInstanceID, 1);
        $Order = 10;
        CreateVariable(v_USERNAME, 3 /*String*/, $settingsID, $Order++, "");
        CreateVariable(v_PASSWORD, 3 /*String*/, $settingsID, $Order++, "");
        CreateVariable(v_IP, 3 /*String*/, $settingsID, $Order++, "");
        
        // create "get state" event
        $TimerID = CreateTimer_CyclicBySeconds('Refresh State', $settingsID, 30);
        IPS_SetEventScript($TimerID, "IPS_RunScriptEx(".$netIOScriptID.", array(\"action\" => \"getStatus\", \"source\" => ".$settingsID."));");
        IPS_SetPosition($TimerID, $Order++);
        
        // create the port variables
        $Order = 10;
        for($i = 1; $i <= 4; $i++) {
            $Name = "Port".$i;
            $ControlId = CreateVariable($Name,  0 /*Boolean*/, $newIOInstanceID, $Order, '~Switch', $netIOScriptID, null, 'Power');
            $Order += 10;
        }
    } else if($IPS_SENDER == "Variable" || $IPS_SENDER == "TimerEvent") {
        // probably just doing an include somewhere
    } else {
        IPSLogger_Wrn(__file__, "Unhandled IPS_SENDER: ".$IPS_SENDER);
    }
}

?>