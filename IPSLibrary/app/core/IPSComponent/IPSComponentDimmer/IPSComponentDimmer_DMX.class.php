<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentDimmer_DMX.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentDimmer_DMX
    *
    * Definiert ein IPSComponentDimmer_DMX Object, das ein IPSComponentDimmer Object für DMX implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 12.01.2014<br/>
    */

	IPSUtils_Include ('IPSComponentDimmer.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentDimmer');

	class IPSComponentDimmer_DMX extends IPSComponentDimmer {

		private $instanceId;
		private $channel;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentDimmer_DMX Objektes
		 *
		 * @param integer $instanceId InstanceId des DMX Devices
		 */
		public function __construct($instanceId, $channel=1) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			$this->channel  = (int)$channel;
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
			return get_class($this).','.$this->instanceId.','.$this->channel;
		}

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleDimmer $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleDimmer $module){
		}

		/**
		 * @public
		 *
		 * Zustand Setzen 
		 *
		 * @param integer $power Geräte Power
		 * @param integer $level Wert für Dimmer Einstellung (Wertebereich 0-100)
		 */
		public function SetState($power, $level) {
			if (!$power) {
				DMX_SetValue ($this->instanceId, $this->channel, 0);
			} else {
				$levelDMX = $level / 100 * 255;
				DMX_SetValue ($this->instanceId, $this->channel, $levelDMX);
			}
			
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Level des Dimmers
		 *
		 * @return integer aktueller Dimmer Level
		 */
		public function GetLevel() {
			return GetValue(IPS_GetVariableIDByIdent('ChannelValue'.$this->channel, $this->instanceId))*100/255;
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Power Zustand des Dimmers
		 *
		 * @return boolean Gerätezustand On/Off des Dimmers
		 */
		public function GetPower() {
			return GetValue(IPS_GetVariableIDByIdent('ChannelValue'.$this->channel, $this->instanceId)) > 0;
		}

	}

	/** @}*/
?>