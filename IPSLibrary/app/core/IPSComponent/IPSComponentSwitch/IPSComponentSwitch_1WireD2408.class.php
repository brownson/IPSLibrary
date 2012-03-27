<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentSwitch_1WireD2408.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentSwitch_1WireD2408
    *
    * Definiert ein IPSComponentSwitch_1WireD2408 Object, das ein IPSComponentSwitch Object für 1WireD2408 implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponentSwitch.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	class IPSComponentSwitch_1WireD2408 extends IPSComponentSwitch {

		private $instanceId;
		private $channelId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentSwitch_1WireD2408 Objektes
		 *
		 * @param integer $instanceId InstanceId des 1WireD2408 Devices
		 */
		public function __construct($instanceId, $channelId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			$this->channelId  = $channelId;
		}

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleSwitch $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleSwitch $module){
		}

		/**
		 * @public
		 *
		 * Zustand Setzen 
		 *
		 * @param boolean $value Wert für Schalter
		 */
		public function SetState($value) {
			TMEX_F29_SetPin($this->instanceId, $this->channelId, $value); 
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Zustand
		 *
		 * @return boolean aktueller Schaltzustand  
		 */
		public function GetState() {
			return null;
		}

	}

	/** @}*/
?>