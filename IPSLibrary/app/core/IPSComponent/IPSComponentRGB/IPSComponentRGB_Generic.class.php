<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentRGB_Generic.class.php
	 * @author        Joerg Kling
	 *
	 *
	 */

   /**
    * @class IPSComponentRGB_Generic
    *
    * Definiert ein generischen IPSComponentRGB, welcher �ber User-eigenen Code angesteuert wird. Damit ist es m�glich, auch "exotische" Lampen 
	* zu steueren, f�r die sich die Erstellung einer eigene Klasse in der Library nicht lohnen w�rde.
	*
	* Die individuelle Steuerung der Lampe muss in der Datei IPSComponentRGBGeneric.inc.php im Config-Verzeichniss 
	* implementiert werden.
	*
    * @author Joerg Kling
    * @version
    * Version 2.50.1, 01.06.2014<br/>
    */

	IPSUtils_Include ('IPSComponentRGB.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentRGB');
	IPSUtils_Include ('IPSComponentRGB_Generic_Custom.inc.php', 'IPSLibrary::config::core::IPSComponent');
	
	class IPSComponentRGB_Generic extends IPSComponentRGB {

		private $device_name;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentRGB_Generic Objektes
		 *
		 * @param integer $instanceId InstanceId des Generic Devices
		 */
		public function __construct($device_name) {
			$this->device_name = $device_name;
		}

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der ausl�senden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleRGB $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleRGB $module){
		
			return IPSComponentRGB_Generic_HandleEvent($this->device_name, $variable, $value, $module);
		
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
		 * Zustand Setzen 
		 *
		 * @param boolean $power RGB Ger�t On/Off
		 * @param integer $color RGB Farben (Hex Codierung)
		 * @param integer $level Dimmer Einstellung der RGB Beleuchtung (Wertebereich 0-100)
		 */
		public function SetState($power, $color, $level) {

			return IPSComponentRGB_Generic_SetState($this->device_name, $power, $color, $level);
		
		}

	}

	/** @}*/
?>