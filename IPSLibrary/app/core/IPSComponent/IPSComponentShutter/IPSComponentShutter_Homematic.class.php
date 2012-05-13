<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_Homematic.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_Homematic
    *
    * Definiert ein IPSComponentShutter_Homematic Object, das ein IPSComponentShutter Object für Homematic implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_Homematic extends IPSComponentShutter {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_Homematic Objektes
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
		 * @param IPSModuleShutter $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleShutter $module){
		}

		/**
		 * @public
		 *
		 * Hinauffahren der Beschattung
		 */
		public function MoveUp(){
			HM_WriteValueFloat($this->InstanceId , 'LEVEL', 1);
		}
		
		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			HM_WriteValueFloat($this->InstanceId , 'LEVEL', 0);
		}
		
		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			HM_WriteValueFloat($this->InstanceId , 'STOP', true);
		}

	}

	/** @}*/
?>