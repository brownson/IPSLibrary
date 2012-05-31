<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_FS20.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_FS20
    *
    * Definiert ein IPSComponentShutter_FS20 Object, das ein IPSComponentShutter Object f�r FS20 implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_FS20 extends IPSComponentShutter {

		private $instanceId;
		private $isRunningId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_FS20 Objektes
		 *
		 * @param integer $instanceId InstanceId des FS20 Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			$this->isRunningId  = @IPS_GetObjectIDByIdent('isrunning', $this->instanceId);
			if($this->isRunningId===false) {
				$this->isRunningId = IPS_CreateVariable(0);
				IPS_SetParent($this->isRunningId, $this->instanceId);
				IPS_SetName($this->isRunningId, 'IsRunning');
				IPS_SetIdent($this->isRunningId, 'isrunning');
				IPS_SetInfo($this->isRunningId, "This Variable was created by Script IPSComponentShutter_FS20");
			}
		}

		/**
		 * @public
		 *
		 * Funktion liefert String IPSComponent Constructor String.
		 * String kann dazu ben�tzt werden, das Object mit der IPSComponent::CreateObjectByParams
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
		 * @param integer $variable ID der ausl�senden Variable
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
				FS20_SwitchMode($this->instanceId, true);
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
				FS20_SwitchMode($this->instanceId, false);
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
				$value = GetValue(IPS_GetObjectIDByIdent("StatusVariable", $this->instanceId));
				FS20_SwitchMode($this->instanceId, $value);
				SetValue($this->isRunningId, false);
			}
		}
	}

	/** @}*/
?>