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
        
        public function __construct($ip, $password, $categoryId) {
            $this->ip = $ip;
            $this->password = $password;
            $this->connection = null;
            $this->isConnected = false;
            if(!IPS_CategoryExists($categoryId)) {
                throw new Exception("Category $categoryId does not exist");
            }
            $this->categoryIdRoot = $categoryId;
        }
        
        private function setupConnectionAndLogin() {
            if($this->isConnected === true) {
                return true;
            }
            
            $sidVariable = new Variable("SID", $this->categoryIdRoot, VARIABLE_TYPE_STRING, 10);
            $lastChanged = $sidVariable->getVariableMetadata()["VariableChanged"];
            $now = time();
            $sidExpired = $lastChanged < $now - (60 * self::$sessionValidity);
            $sidValid = $sidVariable->value != 0 && !$sidExpired;
            
            // setup connection
            $this->connection = curl_init("http://" . $this->ip . "/cgi-bin/webcm");
            curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, 1);
            
            if($sidValid) {
                if(!isset($this->SID) || $this->SID == null) {
                    // use stored session variable
                    $this->SID = $sidVariable->value;
                }
                $diff = (int) ($now - $lastChanged);
                //IPSLogger_Trc(__file__, "Already connected with SID ".$this->SID." for ".date("i:s", $diff)."min");
                
                $this->isConnected = true;
            } else {
                curl_setopt($this->connection, CURLOPT_POSTFIELDS, "getpage=".$this->pageLogin);
                
                $login = curl_exec($this->connection);
                $session_status_simplexml = simplexml_load_string($login);

                if ($session_status_simplexml->iswriteaccess == 1) {
                    //IPSLogger_Inf(__file__, "Already connected with SID ".$this->SID." for ".date("H:i", $sidVariable->value)."h");
                    $this->SID = $session_status_simplexml->SID;
                    $sidVariable->value = $this->SID;
                    
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
                        $sidVariable->value = $this->SID;
                        
                        //IPSLogger_Trc(__file__, "Aquired new SID ".$newSID);
                        $this->isConnected = true;
                    } else {
                        $this->isConnected = false;
                    }
                }
            }
            
            //IPSLogger_Trc(__file__, "Using SID: ".$this->SID);
            if(!isset($this->SID) || $this->SID == null) {
                echo "Error: 'SID' is not defined";
                return false;
            }
            
            return $this->isConnected;
        }
        
        public function requestPage($page, $var) {
            if($page == null || $var == null) {
                echo "Error: 'page' or 'var' parameter is null";
                return false;
            }
            
            if($this->setupConnectionAndLogin()) {
                curl_setopt($this->connection, CURLOPT_POSTFIELDS, "getpage={$page}&sid=".$this->SID."&".$var);
                try {
                    $data = curl_exec($this->connection);
                } catch (Exception $e) {
                    return false;
                }
                return $data;
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
                $data = $this->getTdContent($element);
                
                $return[] = array($elementName, $data);
            }
            return $return;
        }
        
        public function getDectMonitorData() {
            $data = $this->requestPage("../html/de/dect/dectmonidaten.xml", "");
            
            $data = utf8_decode($data);
            $doc = new DOMDocument("2.0", "UTF-8");
            @$doc->loadHTML($data);
            
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