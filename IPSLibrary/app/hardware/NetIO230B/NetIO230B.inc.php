<?

    /**@addtogroup hardware
     * @{
     *
     * @file          NetIO230B.inc.php
     * @author        Dominik Zeiger
     * @version
     * Version 2.50.1, 13.07.2012<br/>
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
    define("v_NETIO_LAST_ACTION", "LastAction");
    define("v_NETIO_LAST_WARNING", "LAST_CMD_WARNING");
    
    define("NETIO_PORT_NOCHANGE", "u");
    
    // actions
    define("ACTION_LOGIN", 1);
    define("ACTION_LIST_PORT", 2);
    define("ACTION_SET_PORT", 3);
    define("ACTION_LIST_TRIGGER_PORT", 4);
    
    IPSLogger_SetLoggingLevel(__file__, c_LogLevel_Information);

    class NetIO230B {
        // in minutes
        private static $connectionErrorLogInterval = 60;
        private static $PORTS = 4;
        
        private $regVarId;
        private $settings;
        private $settingsLoaded = false;
        private $loggedIn = false;
        private $lastActionVar;
        
        public function __construct($regVarId) {
            // validate register variable id
            if(!IPS_ObjectExists($regVarId)) {
                IPSLogger_Err(__file__, "RegisterVariable with id $regVarId does not exist. Unable to send commands to NetIO device.");
                throw new Exception("Unable to create NetIO230B instance");
            }
            $this->regVarId = $regVarId;
            
            // read settings
            $settingsId = IPS_GetObjectIDByName(v_NETIO_SETTINGS, $regVarId);
            if($settingsId == false || !IPS_ObjectExists($settingsId)) {
                IPSLogger_Err(__file__, "Category with $settingsId does not exist. Unable to send commands to NetIO device.");
                throw new Exception("Unable to create NetIO230B instance");
            }
            
            if(!$this->loadSettings($settingsId)) {
                throw new Exception("Unable to create NetIO230B instance");
            }
            
            // read last action variable
            $this->lastActionVar = new Variable(v_NETIO_LAST_ACTION, $regVarId);
            
            // check whether the device is available on the network
            if(!$this->isDeviceAvailable($settingsId)) {
                IPSLogger_Trc(__file__, "No device available at ".$this->settings[v_IP]);
                throw new Exception("Unable to create NetIO230B instance");
            }
            
            // extract the socket id
            $socketName = "NetIO".str_replace(".", "", $this->settings[v_IP]);
            $this->socketId = IPS_GetObjectIDByName($socketName, 0);
            if($this->socketId === false || $this->socketId === 0) {
                IPSLogger_Err(__file__, "Socket '".$socketName."' does not exist.");
                throw new Exception("Unable to create NetIO230B instance");
            }
        }
        
        /**
         * Extracts and returns the login credentials for the given netIO230 device from variables with the names defined in v_USERNAME, v_PASSWORD and V_IP.
         */
        private function loadSettings($settingsId) {
            if($this->settingsLoaded) {
                return true;
            }
            
            $usernameID = IPS_GetVariableIDByName(v_USERNAME, $settingsId);
            $passwordID = IPS_GetVariableIDByName(v_PASSWORD, $settingsId);
            $ipID = IPS_GetVariableIDByName(v_IP, $settingsId);

            if($usernameID == false || $passwordID == false) {
                $msg = v_USERNAME." or ".v_PASSWORD." variable missing in category ".$settingsId;
                IPSLogger_Err(__file__, $msg);
                return false;
            }
            if($ipID == false) {
                $msg = v_IP." variable missing in category ".$settingsId;
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
            
            $this->settings = array(v_USERNAME => $username, v_PASSWORD => $password, v_IP => $ip);
            $this->settingsLoaded = true;
            
            return true;
        }
        
        private function isDeviceAvailable($settingsID) {
            $result = true;
            
            $lastWarningVar = new Variable(v_NETIO_LAST_WARNING, $settingsID);
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
        
        private function sendCommand($action, $command) {
            $this->lastActionVar->value = $action;
            @CSCK_SendText($this->socketId, $command."\x0D\x0A");
            //RegVar_SendText($this->regVarId, $command."\x0D\x0A");
        }
        
        /**
         * Read hash and execute login
         */
        private function login($data) {
            $hash = substr($data, 6, 8);
            $password = md5($this->settings[v_USERNAME].$this->settings[v_PASSWORD].$hash);
            
            $cmd = "clogin ".$this->settings[v_USERNAME]." ".$password;
            
            $this->sendCommand(ACTION_LOGIN, $cmd);
        }
        
        /**
         * Query the current port status
         */
        public function updateStatus() {
            $this->sendCommand(ACTION_LIST_PORT, "port list");
        }
        
        private function parsePortStatus($data) {
            for($i = 1; $i <= self::$PORTS; $i++) {
                $portID = IPS_GetObjectIDByName("Port".$i, $this->regVarId);
                $status = ((int) $data[$i - 1]) == 1 ? true : false;
                if(GetValue($portID) <> $status) {
                    IPSLogger_Dbg(__file__, "Status of port $i has changed. New value: ".$status);
                    SetValue($portID, $status);
                }
            }
        }
        
        public function handleResponse($value) {
            $statusCode = substr($value, 0, 3);
            $data = substr($value, 4);
            $lastAction = $this->lastActionVar->value;
            switch ($statusCode) {
                case "100":
                    $this->login($data);
                    break;
                case "250":
                    // request successful
                    if($lastAction == ACTION_LOGIN) {
                        $this->loggedIn = true;
                    } else if ($lastAction == ACTION_LIST_PORT) {
                        $this->parsePortStatus($data);
                    } else if($lastAction == ACTION_LIST_TRIGGER_PORT) {
                        $this->updateStatus();
                    }
                    break;
                case "504":
                    // already logged in
                    $this->loggedIn = true;
                    IPSLogger_Dbg(__file__, "Already logged in");
                    break;
                case "505":
                    // forbidden -> close port, open port -> will force status "100"
                    $this->loggedIn = false;
                    CSCK_SetOpen($this->socketId, false);
                    IPS_ApplyChanges($this->socketId);
                    CSCK_SetOpen($this->socketId, true);
                    IPS_ApplyChanges($this->socketId);
                    break;
                case "550":
                    // parameter format wrong
                    IPSLogger_Wrn(__file__, "Wrong parameter format");
                    break;
                case "553":
                    // invalid login
                    IPSLogger_Not(__file__, "Invalid login ".$statusCode);
                    break;
            }
        }
        
        /**
         * Set status of the given port id. 
         */
        public function setPortStatusByPortId($portID, $status) {
            if(!is_numeric($portID) || $portID < 0) {
                IPSLogger_Not(__file__, "Unable to set port status. Invalid port id $portID");
                return;
            }
            $regVarId = IPS_GetParent($portID);
            if($this->regVarId != $regVarId) {
                IPSLogger_Wrn(__file__, "Port with id $portID is not child of netIO device ".$this->regVarId);
                return;
            }
        
            $portVarObject = IPS_GetObject($portID);
            $portName = $portVarObject["ObjectName"];
            $portNumber = substr($portName, 4, strlen($portName));
            if(!is_numeric($portNumber) || $portNumber < 1 || $portNumber > self::$PORTS) {
                IPSLogger_Not(__file__, "Port number '".$portNumber."' is not in range [1,".self::$PORTS."]");
                return;
            }
            
            if($status === null) {
                $oldStatus = GetValue($portID);
                IPSLogger_Wrn(__file__, "Overridding power status ".$status." with ".$oldStatus);
                $status = !$oldStatus;
            }
            
            $stateStr = "";
            for($i = 1; $i <= self::$PORTS; $i++) {
                $stateStr .= ($portNumber == $i ? (int) $status : NETIO_PORT_NOCHANGE);
            }
            $cmd = "port list ".$stateStr;
            $this->sendCommand(ACTION_LIST_TRIGGER_PORT, $cmd);
        }
        
        /**
         * Sets the status of the given portid.
         */
        public static function getInstanceFromPortIdAndSetStatus($portID, $status) {
            $regVarId = IPS_GetParent($portID);
            $netIO = new NetIO230B($regVarId);
            $netIO->setPortStatusByPortId($portID, $status);
        }
    }
    
    /** @}*/
?>