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

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_1Wire extends IPSComponentShutter {

		private $instanceId;
		private $reverseControl;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_1Wire Objektes
		 *
		 * @param integer $instanceId InstanceId des 1Wire Devices
		 * @param boolean $reverseControl Reverse Ansteuerung des Devices
		 */
		public function __construct($instanceId, $reverseControl=false) {
			$this->instanceId     = IPSUtil_ObjectIDByPath($instanceId);
			$this->reverseControl = $reverseControl;
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
			if ($this->reverseControl) {
				@TMEX_F29_SetStrobe($this->instanceId, True);
				@TMEX_F29_SetPort((integer)$this->instanceId, (integer)120);
			} else {
				@TMEX_F29_SetStrobe($this->instanceId, True);
				@TMEX_F29_SetPort((integer)$this->instanceId, (integer)120+128);
			}
		}
		
		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			if ($this->reverseControl) {
				@TMEX_F29_SetStrobe($this->instanceId, True);
				@TMEX_F29_SetPort((integer)$this->instanceId, (integer)120+128);
			} else {
				@TMEX_F29_SetStrobe($this->instanceId, True);
				@TMEX_F29_SetPort((integer)$this->instanceId, (integer)120);
			}
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