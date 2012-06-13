<?

    /**@addtogroup Utils
     * @{
     *
     * @file          Utils_Variable.inc.php
     * @author        Dominik Zeiger
     * @version
     * Version 2.50.1, 30.05.2012<br/>
     *
     * Helper class to manage IPS variables more easily
    */
    
    IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');

    define("VARIABLE_TYPE_BOOLEAN", 0);
    define("VARIABLE_TYPE_INTEGER", 1);
    define("VARIABLE_TYPE_FLOAT", 2);
    define("VARIABLE_TYPE_STRING", 3);

    class Variable {
        private $variableName;
        private $variableId;
        
        public function __construct($variableIdentifier, $parentCategory = null, $variableType = VARIABLE_TYPE_INTEGER, $order = 0, $profileName = '') {
            $categoryMissing = $parentCategory == null || !IPS_CategoryExists($parentCategory);
            $variableIsNumeric = is_numeric($variableIdentifier);
            $variableMissing = $variableIsNumeric && !IPS_VariableExists($variableIdentifier);
            
            if(is_null($variableType) || !is_numeric($variableType) || $variableType > 5 || $variableType < 0) {
                IPSLogger_Err(__file__, "Invalid variable type $variableType. Variable type needs to be a value from 0 - 5. ");
                return;
            }
            
            if(($categoryMissing && $variableIsNumeric && $variableMissing) || (!$variableIsNumeric && strlen($variableIdentifier) == 0)) {
                IPSLogger_Err(__file__, "When variableIdentifier is not a valid variable ID, you need to supply a variableName and a valid parentCategory id");
                return;
            }
            
            if($variableIsNumeric) {
                $this->variableId = $variableIdentifier;
                $this->variableName = IPS_GetName($variableIdentifier);
            } else {
                // initial value based on the type
                $initialValue = 0;
                //if($variableType == VARIABLE_TYPE_INTEGER) {
                    
                //}
                
                $this->variableName = $variableIdentifier;
                $this->variableId = CreateVariable($variableIdentifier, $variableType, $parentCategory, $order, $profileName, null, $initialValue, null);
            }
        }
        
        private function getValue() {
            return GetValue($this->variableId);
        }
        
        public function __get($name) {
            if(isset($this->$name)) {
                return $this->$name;
            } else {
                if($name == "value") {
                    return $this->getValue();
                } else {
                    throw new Exception("Variable or Handler for $name does not exist.",$name);
                }
            }
        }
        
        private function setValue($value) {
            return SetValue($this->variableId, $value);
        }
        
        public function __set($name,$value) {
            if($name == "value") {
                $this->setValue($value);
            } else {
                throw new Exception("Variable or Handler for $name does not exist.",$name);
            }
        }
        
        public function getVariableMetadata() {
            return IPS_GetVariable($this->variableId);
        }
        
        public function getVariableId() {
            return $this->variableId;
        }
        
        public function getVariableName() {
            return $this->variableName;
        }
        
        public function setLogging($enabled) {
            $archiveHandlerId = IPS_GetInstanceIDByName("Archive Handler", 0);
            AC_SetLoggingStatus($archiveHandlerId, $this->variableId, $enabled);
            @IPS_ApplyChanges($this->variableId);
        }
        
        /**
         * Returns true if the current value is an empty string, 0 or null.
         */
        public function isInitial() {
            $val = $this->getValue();
            return $val == "" || $val == 0 || $val == null;
        }
    }

    /** @}*/
?>