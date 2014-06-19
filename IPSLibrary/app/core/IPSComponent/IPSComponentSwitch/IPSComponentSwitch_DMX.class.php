<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentSwitch_DMX.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentSwitch_DMX
    *
    * Definiert ein IPSComponentSwitch_DMX Object, das ein IPSComponentSwitch Object für DMX implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 12.01.2014<br/>
    */

	IPSUtils_Include ('IPSComponentSwitch.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	class IPSComponentSwitch_DMX extends IPSComponentSwitch {

		private $instanceId;
		private $channelId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentSwitch_DMX Objektes
		 *
		 * @param integer $instanceId InstanceId des DMX Devices
		 * @param integer $channelId Kanal des DMX Devices
		 */
		public function __construct($instanceId, $channelId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			$this->channelId  = (int)$channelId;
		}

		/**
		 * @public
		 *
		 * Funktion liefert String IPSComponent Constructor String.
		 * String kann dazu benützt werden, das Object mit der IPSComponent::CreateObjectByParams
		 * wieder neu zu erzeugen.
		 *
		 * @return string Parameter String des IPSComponent Object
		 */
		public function GetComponentParams() {
			return get_class($this).','.$this->instanceId.','.$this->channelId;
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
		 * @param integer $onTime Zeit in Sekunden nach der der Aktor automatisch ausschalten soll (nicht unterstützt)
		 */
		public function SetState($value, $onTime=false) {
			if ($value) {
				DMX_SetValue ($this->instanceId, $this->channel1, 255);
			} else {
				DMX_SetValue ($this->instanceId, $this->channel1, 0);
			}
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