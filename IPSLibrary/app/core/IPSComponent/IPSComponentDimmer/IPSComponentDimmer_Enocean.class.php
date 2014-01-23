<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentDimmer_Enocean.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentDimmer_Enocean
    *
    * Definiert ein IPSComponentDimmer_Enocean Object, das ein Dummy IPSComponentDimmer Object implementiert.
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 06.11.2012<br/>
    */

	IPSUtils_Include ('IPSComponentDimmer.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentDimmer');

	class IPSComponentDimmer_Enocean extends IPSComponentDimmer {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentDimmer_Enocean Objektes
		 *
		 * @param integer $instanceId InstanceId des Dummy Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
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
			return get_class($this).','.$this->instanceId;
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
				ENO_DimSet($this->instanceId, 0);
			} else {
				ENO_DimSet($this->instanceId, $level);
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
			return GetValue(IPS_GetVariableIDByName('Intensity', $this->instanceId));
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Power Zustand des Dimmers
		 *
		 * @return boolean Gerätezustand On/Off des Dimmers
		 */
		public function GetPower() {
			return GetValue(IPS_GetVariableIDByName('Intensity', $this->instanceId)) > 0;
		}

	}

	/** @}*/
?>