<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
 	 *
	 * @file          IPSConfigHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSConfigurationException
    *
    * Definiert eine Konfigurations Exception
    *
    */
   class IPSConfigurationException extends Exception {
   }

   /**
    * @class IPSConfigHandler
    *
    * Definiert einen IPSConfigHandler, dieser bietet die M�glichkeit Konfigurations
    * Parameter zu lesen und zu speichern
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSConfigHandler {
		const SCRIPTVERSION    = 'Version';           // Current Module Verison (Script)
		const INSTALLVERSION   = 'InstallVersion';    // Current Module Verison (Installation)
		const MODULENAMESPACE  = 'ModuleNamespace';   // Module Namespace
		const SOURCEREPOSITORY = 'SourceRepository';  // Pfad/Url zum Source Repository
		const LOGDIRECTORY     = 'LogDirectory';      // Logging Directory
		const CHANGELIST       = 'ChangeList';        // ChangeList
		const REQUIREDMODULES  = 'RequiredModules';   // Required Modules

		protected $configData              = array();

		/**
		 * @public
		 *
		 * Retourniert Existenz eines Parameters
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return boolean liefert Existenz des �bergebenen Parameters
		 */
		public function ExistsValue($key, $section=null) {
			if ($section==null) {
				return array_key_exists($key,$this->configData);
			} else {
				return (array_key_exists($section,$this->configData) and array_key_exists($key,$this->configData[$section]));
			}
		}

		/**
		 * @public
		 *
		 * Liefert den Wert eines �bergebenen Parameters
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return string liefert den Wert des �bergebenen Parameters
		 * @throws IPSConfigurationException wenn der betroffene Parameter nicht gefunden wurde
		 */
		public function GetValue($key, $section=null) {
			if (!$this->ExistsValue($key, $section)) {
				throw new IPSConfigurationException('Configuration Value with Key='.$key.' could NOT be found (Section="'.$section.'")',
				                                    E_USER_ERROR);
			} elseif ($section==null) {
				return $this->configData[$key];
			} else {
				return $this->configData[$section][$key];
			}
		}

		/**
		 * @public
		 *
		 * Liefert den integer Wert eines �bergebenen Parameters
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return integer liefert den Wert des �bergebenen Parameters
		 * @throws IPSConfigurationException wenn der betroffene Parameter nicht gefunden wurde
		 */
		public function GetValueInt ($key, $section=null) {
			return (int)$this->GetValue($key, $section);
		}

		/**
		 * @public
		 *
		 * Liefert den boolean Wert eines �bergebenen Parameters
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return boolean liefert den Wert des �bergebenen Parameters
		 * @throws IPSConfigurationException wenn der betroffene Parameter nicht gefunden wurde
		 */
		public function GetValueBool ($key, $section=null) {
			$value = $this->GetValue($key, $section);
			if ($value=='false') {
				return false;
			} elseif ($value=='true') {
				return true;
			} else {
				return (boolean)$value;
			}
		}

		/**
		 * @public
		 *
		 * Liefert den float Wert eines �bergebenen Parameters
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return float liefert den Wert des �bergebenen Parameters
		 * @throws IPSConfigurationException wenn der betroffene Parameter nicht gefunden wurde
		 */
		public function GetValueFloat ($key, $section=null) {
			return (float)$this->GetValue($key, $section);
		}

		/**
		 * @public
		 *
		 * Liefert den Wert eines �bergebenen Parameters, konnte der Wert des �bergebenen Parameters nicht
		 * gefunden werden, wird der �bergebene Default Wert retourniert.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @param string $defaultValue Default Wert wenn Parameter nicht gefunden wurde
		 * @return string liefert den Wert des �bergebenen Parameters
		 */
		public function GetValueDef($key, $section=null, $defaultValue="") {
			if ($section==null) {
				if ($this->ExistsValue($key, $section)) {
					$result = $this->configData[$key];
				} else {
					$result = $defaultValue;
				}
			} else {
				if ($this->ExistsValue($key, $section)) {
					$result = $this->configData[$section][$key];
				} else {
					$result = $defaultValue;
				}
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Liefert den integer Wert eines �bergebenen Parameters, konnte der Wert des �bergebenen Parameters nicht
		 * gefunden werden, wird der �bergebene Default Wert retourniert.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @param string $defaultValue Default Wert wenn Parameter nicht gefunden wurde
		 * @return integer retouniert den Wert des �bergebenen Parameters
		 */
		public function GetValueIntDef($key, $section=null, $defaultValue="") {
			return (int)$this->GetValueDef($key, $section, $defaultValue);
		}

		/**
		 * @public
		 *
		 * Liefert den boolean Wert eines �bergebenen Parameters, konnte der Wert des �bergebenen Parameters nicht
		 * gefunden werden, wird der �bergebene Default Wert retourniert.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @param string $defaultValue Default Wert wenn Parameter nicht gefunden wurde
		 * @return boolean retouniert den Wert des �bergebenen Parameters
		 */
		public function GetValueBoolDef ($key, $section=null, $defaultValue="") {
			$value = $this->GetValueDef($key, $section, $defaultValue);
			if ($value=='false') {
				return false;
			} elseif ($value=='true') {
				return true;
			} else {
				return (boolean)$value;
			}
		}

		/**
		 * @public
		 *
		 * Liefert den float Wert eines �bergebenen Parameters, konnte der Wert des �bergebenen Parameters nicht
		 * gefunden werden, wird der �bergebene Default Wert retourniert.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @param string $defaultValue Default Wert wenn Parameter nicht gefunden wurde
		 * @return float retouniert den Wert des �bergebenen Parameters
		 */
		public function GetValueFloatDef ($key, $section=null, $defaultValue="") {
			return (float)$this->GetValueDef($key, $section, $defaultValue);
		}

	}

	/** @}*/
?>