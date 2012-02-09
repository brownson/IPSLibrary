<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_IPSShutterControl.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_IPSShutterControl
    *
    * Definiert ein IPSComponentShutter_IPSShutterControl Object, das ein IPSComponentShutter Object für IPSShutterControl implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSComponentShutter_IPSShutterControl extends IPSComponentShutter {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_IPSShutterControl Objektes
		 *
		 * @param integer $instanceId InstanceId des IPSShutterControl Devices
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
			$name = IPS_GetName($variable);
			throw new IPSComponentException('Event Handling NOT supported for Variable '.$variable.'('.$name.')');
		}

		/**
		 * @public
		 *
		 * Hinauffahren der Beschattung
		 */
		public function MoveUp(){
			SC_MoveUp($this->instanceId,0);
		}
		
		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			SC_MoveDown($this->instanceId,0);
		}
		
		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			SC_Stop($this->instanceId);
		}

	}

	/** @}*/
?>