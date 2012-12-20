<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentSwitch_ModBus.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentSwitch_ModBus
    *
    * Definiert ein IPSComponentSwitch_ModBus Object, das ein IPSComponentSwitch Object für ModBus implementiert.
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 18.12.2012<br/>
    */

	IPSUtils_Include ('IPSComponentSwitch.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	class IPSComponentSwitch_ModBus extends IPSComponentSwitch {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentSwitch_ModBus Objektes
		 *
		 * @param integer $instanceId InstanceId des ModBus Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
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
			$module->SyncState($value, $this);
		}

		/**
		 * @public
		 *
		 * Zustand Setzen 
		 *
		 * @param boolean $value Wert für Schalter
		 */
		public function SetState($value) {
			ModBus_WriteCoil($this->instanceId, $value);
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Zustand
		 *
		 * @return boolean aktueller Schaltzustand  
		 */
		public function GetState() {
			GetValue(IPS_GetObjectIDByIdent('Value', $this->instanceId));
		}

	}

	/** @}*/
?>