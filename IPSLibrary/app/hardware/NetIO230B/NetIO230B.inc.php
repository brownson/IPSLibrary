<?

    /**@addtogroup hardware
     * @{
     *
     * @file          NetIO230B.inc.php
     * @author        Dominik Zeiger
     * @version
     * Version 2.50.1, 30.05.2012<br/>
     *
     * Control interface for NetIO230B devices
    */
    
    IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
    IPSUtils_Include ('Utils_Variable.inc.php',      'IPSLibrary::app::modules::Utils');

    // variable names for username, password and interface
    define("v_USERNAME", "Username");
    define("v_PASSWORD", "Password");
    define("v_IP", "IP");
    define("v_NETIO_SETTINGS", "Settings");
    define("NETIO_PORT_NOCHANGE", "u");
    
    IPSLogger_SetLoggingLevel(__file__, c_LogLevel_Information);

    class NetIO230B {
        private static $maxRetries = 10;
        private static $milliSecondsBetweenRetries = 500;
        // in minutes
        private static $connectionErrorLogInterval = 60;
        private static $cgiControlPath = "http://%s/tgi/control.tgi?";
        
        private $useEncryptedPassword = true;
        private $basePath;
        private $settings;
        private $settingsLoaded = false;
        private $loggedIn = false;
        
        public function __construct() {
        }
        
        public function __destruct() {
            if($this->loggedIn) {
                $this->logout();
            }
        }
        
        /**
         * Extracts and returns the login credentials for the given netIO230 device from variables with the names defined in v_USERNAME, v_PASSWORD and V_IP.
         */
        private function loadSettings($objectID) {
            if($this->settingsLoaded) {
                return true;
            }
            
            $usernameID = IPS_GetVariableIDByName(v_USERNAME, $objectID);
            $passwordID = IPS_GetVariableIDByName(v_PASSWORD, $objectID);
            $ipID = IPS_GetVariableIDByName(v_IP, $objectID);

            if($usernameID == false || $passwordID == false) {
                $msg = v_USERNAME." or ".v_PASSWORD." variable missing in category ".$objectID;
                IPSLogger_Err(__file__, $msg);
                return false;
            }
            if($ipID == false) {
                $msg = v_IP." variable missing in category ".$objectID;
                IPSLogger_Err(__file__, $msg);
                return false;
            }
            $username = GetValue($usernameID);
            $password = GetValue($passwordID);
            $ip = GetValue($ipID);
            if(!filter_var($ip, FILTER_VALIDATE_IP)) {
                $msg = "Value '".$ip."' is not a valid ip address.";
                IPSLogger_Err(__file__, $msg);
                return false;
            }
            
            $this->basePath = sprintf(self::$cgiControlPath, $ip);
            
            $this->settings = array(v_USERNAME => $username, v_PASSWORD => $password, v_IP => $ip);
            $this->settingsLoaded = true;
            
            return true;
        }
        
        /**
         * Sets the status of the given portid. PortId represents the "interface" register variable of the netio230 device.
         */
        public function setPortStatus($portID, $status) {
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
                $stateStr .= ($portNumber == $i ? $statusInt : NETIO_PORT_NOCHANGE);
            }
            if($this->sendRequest($moduleID, $stateStr)) {
                SetValue($portID, $status);
            }
        }
        
        /**
         * Requests the current port status from the device and syncs it with the local state.
         */
        public function updateStatus($netIORegisterVariableID) {
            $this->sendRequest($netIORegisterVariableID, "list");
        }
        
        /**
         * Parses the response from the device and evaluates the return code.
         */
        private function parseResponse($text) {
            IPSLogger_Dbg(__file__, "ParseReponse: ".$text);
            
            $matchCount = preg_match("/(\d\d\d)\s?(.+)?/", $text, $matches);
            if($matchCount == 0) {
                // data response
                return $text;
            } else {
                // confirmation response
                $responseCode = $matches[1];
                switch ($responseCode) {
                    case 100:
                        // succesful login
                        IPSLogger_Dbg(__file__, "Login successful");
                        break;
                    case 110:
                        // succesful logout
                        IPSLogger_Dbg(__file__, "Logout successful");
                        break;
                    case 250:
                        // request successful
                        IPSLogger_Dbg(__file__, "Request successful");
                        break;
                    case 504:
                        // already logged in => ignore
                        IPSLogger_Dbg(__file__, "Already logged in");
                        break;
                    case 550:
                        IPSLogger_Wrn(__file__, "Parameter format wrong");
                        break;
                    default:
                        IPSLogger_Wrn(__file__, "Unknown NetIO230 response: ".print_r($text, true));
                }
                return array('code' => $responseCode, 'content' => $matches[2]);
            }
        }
        
        private function login() {
            if($this->loggedIn === true) {
                IPSLogger_Dbg(__file__, "Already logged in");
                return true;
            }
            
            $hash = $this->getContents("hash=hash");
            if($this->useEncryptedPassword) {
                $password = md5($this->settings[v_USERNAME].$this->settings[v_PASSWORD].$hash);
            } else {
                $password = $this->settings[v_PASSWORD];
            }
            
            $response = $this->getContents("login=c:".$this->settings[v_USERNAME].":".$password);
            $result = $this->parseResponse($response);
            if($result['code'] == 100) {
                $this->loggedIn = true;
            } else {
                IPSLogger_Not(__file__, "Login failed. Returncode: ".$result['code']);
            }
            
            return $this->loggedIn;
        }
        
        private function logout() {
            $response = $this->getContents("quit=quit");
            $result = $this->parseResponse($response);
            if($result['code'] == 110) {
                 $this->loggedIn = false;
            }
            return !$this->loggedIn;
        }
        
        
        private function getContents($request) {
            $url = $this->basePath.$request;
            $rawResponse = Sys_GetURLContent($url);
            $response = strip_tags($rawResponse);
            return $response;
        }
        
        private function isDeviceAvailable($netIORegisterVariableID) {
            $result = true;
            
            $lastWarningVar = new Variable("LAST_CMD_WARNING", $netIORegisterVariableID);
            $lastWarningIsInitial = $lastWarningVar->isInitial();
            
            // check if device is available at port 80
            $resp = @fsockopen($this->settings[v_IP], 80, $errno, $errstr, 1);
            if(!$resp) {
                // if no connection could be made, log an error every $connectionErrorLogInterval minutes
                $nextErrorLogTime = $lastWarningVar->value + (self::$connectionErrorLogInterval * 60);
                $now = time();
                if($lastWarningIsInitial || $now > $nextErrorLogTime) {
                    $lastWarningVar->value = $now;
                    IPSLogger_Wrn(__file__, "No response from NetIO device @ ".$this->settings[v_IP]);
                } else if(!$lastWarningIsInitial) {
                    // error was already sent within the last $connectionErrorLogInterval hours
                }
                $result = false;
            } else if($resp && !$lastWarningIsInitial) {
                // reset last warning value, when a connection could be made again
                $lastWarningVar->value = 0;
            }
            return $result;
        }
        
        private function requestPort($netIORegisterVariableID, $parameter) {
            $content = $this->getContents("port=".$parameter);
            $response = $this->parseResponse($content);
            if(!is_array($response)) {
                $matchCount = preg_match("/\d \d \d \d/", $response);
                if($matchCount == 0) {
                    IPSLogger_Wrn(__file__, "Port status response has the wrong format. Expected: '1 1 1 1'. Actual: '".$response."'");
                    return false;
                }
            
                $portStatuses = str_replace(" ", "", $response);
                $portCount = strlen($portStatuses);
                if($netIORegisterVariableID == null) {
                    throw new Exception("Missing parent category id, which contains the port variables with the name \"PortX\"");
                }
                $parentID = IPS_GetParent($netIORegisterVariableID);
                for($i = 1; $i <= $portCount; $i++) {
                    $varName = "Port".$i;
                    $varID = IPS_GetVariableIDByName($varName, $parentID);
                    if($varID == false) {
                        IPSLogger_Wrn(__file__, "Found ".$portCount." port statuses, but missing variable with name ".$varName);
                        continue;
                    }
                    $rawPortStatus = substr($portStatuses, $i - 1, 1);
                    if($rawPortStatus != "1" && $rawPortStatus != "0") {
                        throw new Exception("Found incompatible status value: ".$rawPortStatus." [Full response:".(print_r($response, true))."]");
                    }
                    SetValue($varID, $rawPortStatus == "1");
                }
                return true;
            } else if($response['code'] == 250) {
                return true;
            }
            
            return false;
        }
        
        /**
         * Sends a request to the netio device. In case of an error retry $maxRetries times.
         */
        private function sendRequest($netIORegisterVariableID, $parameter) {
            if(!IPS_ObjectExists($netIORegisterVariableID)) {
                IPSLogger_Err(__file__, "Category with $netIORegisterVariableID does not exist. Unable to send commands to NetIO device.");
                return;
            }
            
            if(!$this->loadSettings($netIORegisterVariableID)) {
                return false;
            }
            
            if(!$this->isDeviceAvailable($netIORegisterVariableID)) {
                return false;
            }
            
            $retries = self::$maxRetries;
            $result = false;
            while($result == false) {
                if($this->login()) {
                    IPSLogger_Trc(__file__, "Request port @ ".$this->settings[v_IP]);
                    $result = $this->requestPort($netIORegisterVariableID, $parameter);
                }
                
                if(!$result) {
                    usleep(self::$milliSecondsBetweenRetries);
                    --$retries;
                    if($retries <= 0) {
                        IPSLogger_Trc(__file__, "Unable to execute request '".$parameter."' after ".self::$maxRetries." retries.");
                        break;
                    }
                }
            }
            
            return $result;
        }
    }
    
    /** @}*/
?>