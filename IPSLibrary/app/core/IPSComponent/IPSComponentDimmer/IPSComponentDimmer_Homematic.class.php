<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentDimmer_Homematic.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentDimmer_Homematic
    *
    * Definiert ein IPSComponentDimmer_Homematic Object, das ein IPSComponentDimmer Object für Homematic implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSComponentDimmer_Homematic extends IPSComponentDimmer {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentDimmer_Homematic Objektes
		 *
		 * @param integer $instanceId InstanceId des Homematic Devices
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
				HM_WriteValueFloat($this->instanceId, "LEVEL", 0);
			} else {
				$levelHM = $level / 100;
				HM_WriteValueFloat($this->instanceId, "LEVEL", $levelHM);
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
			return GetValue(IPS_GetVariableIDByName('LEVEL', $this->instanceId))*100;
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Power Zustand des Dimmers
		 *
		 * @return boolean Gerätezustand On/Off des Dimmers
		 */
		public function GetPower() {
			return GetValue(IPS_GetVariableIDByName('LEVEL', $this->instanceId)) > 0;
		}

	}

	/** @}*/
?>