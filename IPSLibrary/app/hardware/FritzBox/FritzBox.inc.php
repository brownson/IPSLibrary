<?
    /**@addtogroup hardware
     * @{
     *
     * @file          FritzBox.inc.php
     * @author        Dominik Zeiger
     * @version
     * Version 2.50.1, 30.05.2012<br/>
     *
     * Control interface for FritzBox devices
    */

    IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ('Utils_Variable.inc.php',      'IPSLibrary::app::modules::Utils');
    
    class FritzBox {
        private $pageLogin = "../html/login_sid.xml";
        private $fritzSeite = "../html/de/menus/menu2.html";
        // validity of session in minutes
        private static $sessionValidity = 30;
        
        public function __construct($deviceName, $ip, $password) {
            $this->ip = $ip;
            $this->password = $password;
            $this->connection = null;
            $this->isConnected = false;
            
            $this->deviceName = $deviceName;
            $this->categoryPath = FRITZBOX_DATA_BASE_PATH.".".$this->deviceName;
            $this->categoryId = IPSUtil_ObjectIDByPath($this->categoryPath);
            if(!IPS_CategoryExists($this->categoryId)) {
                throw new Exception("Category ".$this->categoryId." does not exist");
            }
            
            $this->categoryStatePath = $this->categoryPath.".State";
            $this->categoryStateId = IPSUtil_ObjectIDByPath($this->categoryStatePath);
        }
        
        public function getStateCategoryId($name) {
            $path = $this->categoryStatePath.".".$name;
            $categoryId = IPSUtil_ObjectIDByPath($path);
            return $categoryId;
        }
        
        private function setupConnectionAndLogin() {
            if($this->isConnected === true) {
                return true;
            }
            
            $this->sidVariable = new Variable("SID", $this->categoryId, VARIABLE_TYPE_STRING, 10);
            $lastChanged = $this->sidVariable->getVariableMetadata()["VariableChanged"];
            $now = time();
            $sidExpired = $lastChanged < $now - (60 * self::$sessionValidity);
            $sidValid = $this->sidVariable->value != 0 && !$sidExpired;
            
            // setup connection
            $this->connection = curl_init("http://" . $this->ip . "/cgi-bin/webcm");
            curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, 1);
            
            if($sidValid) {
                if(!isset($this->SID) || $this->SID == null) {
                    // use stored session variable
                    $this->SID = $this->sidVariable->value;
                }
                
                $this->isConnected = true;
            } else {
                curl_setopt($this->connection, CURLOPT_POSTFIELDS, "getpage=".$this->pageLogin);
                
                $login = curl_exec($this->connection);
                $session_status_simplexml = simplexml_load_string($login);

                if ($session_status_simplexml->iswriteaccess == 1) {
                    IPSLogger_Inf(__file__, "Already connected with SID ".$this->SID." for ".date("H:i", $this->sidVariable->value)."h");
                    $this->SID = $session_status_simplexml->SID;
                    $this->sidVariable->value = $this->SID;
                    
                    $this->isConnected = true;
                } else {
                    //IPSLogger_Inf(__file__, "Initiating login");
                    $challenge = $session_status_simplexml->Challenge;
                    $response = $challenge . '-' . md5(mb_convert_encoding($challenge . '-' . $this->password, "UCS-2LE", "UTF-8"));
                    curl_setopt($this->connection, CURLOPT_POSTFIELDS, "login:command/response={$response}&getpage=".$this->fritzSeite);
                    preg_match('/name="sid" value="([0-9a-f]*)"/', curl_exec($this->connection), $matches);
                    if (isset($matches[1]) && $matches[1] != '0000000000000000') {
                        $newSID = $matches[1];
                        $this->SID = $newSID;
                        $this->sidVariable->value = $this->SID;
                        
                        //IPSLogger_Trc(__file__, "Aquired new SID ".$newSID);
                        $this->isConnected = true;
                    } else {
                        $this->isConnected = false;
                    }
                }
            }
            
            if(!isset($this->SID) || $this->SID == null) {
                echo "Error: 'SID' is not defined";
                return false;
            }
            
            return $this->isConnected;
        }
        
        public function requestPage($page, $var = null) {
            if($page == null) {
                echo "Error: 'page' parameter is null";
                return false;
            }
            
            $retries = 2;
            while($retries > 0 && $this->setupConnectionAndLogin()) {
                curl_setopt($this->connection, CURLOPT_POSTFIELDS, "getpage={$page}&sid=".$this->SID.($var != null && count($var) > 0 ? "&".$var : ""));
                try {
                    $data = curl_exec($this->connection);
                    if(preg_match("/FRITZ!Box Anmeldung/", $data)) {
                        $this->sidVariable->value = "";
                        $this->isConnected = false;
                    } else {
                        //echo "Received data: $page\n";
                        return $data;
                    }
                } catch (Exception $e) {
                    return false;
                }
                --$retries;
            }
            return false;
        }
        
        public function getInternetDSLRaw() {
            $dslDetails = $this->requestPage($this->fritzSeite, "var:menu=internet&var:pagename=adsl");
            return $dslDetails;
        }
        
        private function getTdContent($element) {
            $ret = array();
            foreach($element->childNodes as $childNode) {
                if($childNode->nodeType === XML_ELEMENT_NODE) {
                    $ret[] = utf8_decode($childNode->nodeValue);
                }
            }
            return $ret;
        }
        
        public function getInternetDSL() {
            $dslInfo = $this->getInternetDSLRaw();
            if($dslInfo == null || $dslInfo == false) {
                IPSLogger_Wrn(__file__, "No 'DSL Information' was returned");
                return false;
            }
            $dslInfo = utf8_decode($dslInfo);
            $doc = new DOMDocument("2.0", "UTF-8");
            @$doc->loadHTML($dslInfo);
            
            $elementNames = array("DslMaxDataRate", "DslCableCapacity", "DslAtmRate", "DslSignalNoiseDistance", "DslLineLoss");
            $return = array();
            foreach($elementNames as $elementName) {
                $element = $doc->getElementById($elementName);
                if($element == null) continue;
                
                $data = $this->getTdContent($element);
                
                $return[] = array($elementName, $data);
            }
            return $return;
        }
        
        public function getDectMonitorData() {
            $data = $this->requestPage("../html/de/dect/dectmonidaten.xml", "");
            
            //$data = utf8_decode($data);
            $doc = new DOMDocument("2.0", "UTF-8");
            @$doc->loadXML($data);
            
            return $doc;
        }
        
        public function getOverview() {
            if(!$this->setupConnectionAndLogin()) {
                return;
            }
            
            $home = file("http://fritz.box/home/home.lua?sid=".$this->SID);
            print_r($home);
            $tag = substr(trim($home[567]), -10);
            $time = substr(trim($home[568]), -5);
            echo "Verbunden seit ".$tag." ".$time;
        }
    }

?>