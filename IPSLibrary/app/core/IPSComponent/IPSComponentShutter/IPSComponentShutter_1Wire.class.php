<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_1Wire.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_1Wire
    *
    * Definiert ein IPSComponentShutter_1Wire Object, das ein IPSComponentShutter Object für 1Wire implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSComponentShutter_1Wire extends IPSComponentShutter {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_1Wire Objektes
		 *
		 * @param integer $instanceId InstanceId des 1Wire Devices
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
			@TMEX_F29_SetStrobe($this->instanceId, True);
			@TMEX_F29_SetPort((integer)$this->instanceId, (integer)120+128);
		}
		
		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			@TMEX_F29_SetStrobe($this->instanceId, True);
			@TMEX_F29_SetPort((integer)$this->instanceId, (integer)120);
		}
		
		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			@TMEX_F29_SetStrobe($this->instanceId, True);
			@TMEX_F29_SetPort((integer)$this->instanceId, (integer)0);
		}

	}

	/** @}*/
?>