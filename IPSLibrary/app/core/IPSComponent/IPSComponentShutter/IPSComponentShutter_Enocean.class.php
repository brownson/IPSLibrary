<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_Enocean.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_Enocean
    *
    * Definiert ein IPSComponentShutter_Enocean Object, das ein IPSComponentShutter Object für Enocean implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_Enocean extends IPSComponentShutter {

		private $instanceId;
		private $isRunningId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_Enocean Objektes
		 *
		 * @param integer $instanceId InstanceId des Enocean Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			$this->isRunningId  = @IPS_GetObjectIDByIdent('isrunning', $this->instanceId);
			if($this->isRunningId===false) {
				$this->isRunningId = IPS_CreateVariable($this->instanceId);
				IPS_SetParent($this->isRunningId, $id);
				IPS_SetName($this->isRunningId, 'IsRunning');
				IPS_SetIdent($this->isRunningId, 'isrunning');
				IPS_SetInfo($this->isRunningId, "This Variable was created by Script IPSComponentShutter_FS20");
			}
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
			if(!GetValue($this->isRunningId)) {
				ENO_SwitchMode($this->instanceId, true);
				SetValue($this->isRunningId, true);
			}
		}
		
		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			if(!GetValue($this->isRunningId)) {
				ENO_SwitchMode($this->instanceId, false);
				SetValue($this->isRunningId, true);
			}
		}
		
		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			if(GetValue($this->isRunningId)) {
				$value = GetValue(IPS_GetObjectIDByIdent($this->instanceId, "StatusVariable"));
				ENO_SwitchMode($this->instanceId, $value);
				SetValue($this->isRunningId, false);
			}
		}

	}

	/** @}*/
?>