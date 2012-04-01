<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_Dummy.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_Dummy
    *
    * Definiert ein IPSComponentShutter_Dummy Object, das ein IPSComponentShutter Object für Dummy implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSComponentShutter_Dummy extends IPSComponentShutter {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_Dummy Objektes
		 *
		 * @param integer $instanceId InstanceId des Dummy Devices
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
			IPSLogger_Trc(__file__, 'Activate "MoveUp" of "DummyShutter" "'.$this->instanceId.'"');
		}
		
		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			IPSLogger_Trc(__file__, 'Activate "MoveDown" of "DummyShutter" "'.$this->instanceId.'"');
		}
		
		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			IPSLogger_Trc(__file__, 'Activate "Stop" of "DummyShutter" "'.$this->instanceId.'"');
		}

	}

	/** @}*/
?>