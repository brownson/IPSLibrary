<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentDimmer_Generic.class.php
	 * @author        Joerg Kling
	 *
	 *
	 */

   /**
    * @class IPSComponentDimmer_Generic
    *
    * Definiert ein generischen IPSComponentDimmer, welcher �ber User-eigenen Code angesteuert wird. Damit ist es m�glich, auch "exotische" Lampen 
	* zu steueren, f�r die sich die Erstellung einer eigene Klasse in der Library nicht lohnen w�rde.
	*
	* Die individuelle Steuerung der Lampe muss in der Datei IPSComponentDimmerGeneric.inc.php im Config-Verzeichniss 
	* implementiert werden.
    *
    * @author Joerg Kling
    * @version
    * Version 2.50.1, 01.06.2014<br/>
    */

	IPSUtils_Include ('IPSComponentDimmer.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentDimmer');
	IPSUtils_Include ('IPSComponentDimmer_Generic_Custom.inc.php', 'IPSLibrary::config::core::IPSComponent');
	
	class IPSComponentDimmer_Generic extends IPSComponentDimmer {

		private $device_name;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentDimmer_Generic Objektes
		 *
		 * @param integer $instanceId InstanceId des Generic Devices
		 */
		public function __construct($device_name) {
			$this->device_name = $device_name;
		}

		/**
		 * @public
		 *
		 * Funktion liefert String IPSComponent Constructor String.
		 * String kann dazu ben�tzt werden, das Object mit der IPSComponent::CreateObjectByParams
		 * wieder neu zu erzeugen.
		 *
		 * @return string Parameter String des IPSComponent Object
		 */
		public function GetComponentParams() {
			return get_class($this).','.$this->device_name;
		}

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der ausl�senden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleDimmer $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleDimmer $module){
		
			return IPSComponentDimmer_Generic_HandleEvent($this->device_name, $variable, $value, $module);
			
		}

		/**
		 * @public
		 *
		 * Zustand Setzen 
		 *
		 * @param integer $power Ger�te Power
		 * @param integer $level Wert f�r Dimmer Einstellung (Wertebereich 0-100)
		 */
		public function SetState($power, $level) {
		
			return IPSComponentDimmer_Generic_SetState($this->device_name, $power, $level);
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Level des Dimmers
		 *
		 * @return integer aktueller Dimmer Level
		 */
		public function GetLevel() {
			return GetValue(IPS_GetVariableIDByName('Intensity', $this->instanceId));
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Power Zustand des Dimmers
		 *
		 * @return boolean Ger�tezustand On/Off des Dimmers
		 */
		public function GetPower() {
			return GetValue(IPS_GetVariableIDByName('Intensity', $this->instanceId)) > 0;
		}

	}

	/** @}*/
?>