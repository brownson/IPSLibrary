<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_LCN.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_LCN
    *
    * Definiert ein IPSComponentShutter_LCN Object, das ein IPSComponentShutter Object für LCN implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_LCN extends IPSComponentShutter {

		private $instanceId1;
		private $instanceId2;
		private $directionSwitch;
		private $unitType;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_LCN Objektes
		 *
		 * @param integer $instanceId1 InstanceId 1 des LCN Devices (Movement)
		 * @param integer $instanceId2 InstanceId 2 des LCN Devices (Direction)
		 * @param boolean $directionSwitch Richtungs Schalter (default=false)
		 */
		public function __construct($instanceId1, $instanceId2, $directionSwitch=false) {
			$this->instanceId1     = IPSUtil_ObjectIDByPath($instanceId1);
			$this->instanceId2     = IPSUtil_ObjectIDByPath($instanceId2);
			$this->directionSwitch = $directionSwitch;
			$this->unitType        = LCN_GetUnit($this->instanceId1);
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
			return get_class($this).','.$this->instanceId1.','.$this->instanceId2;
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
			switch($this->unitType) {
				case 0:
					LCN_SetIntensity($this->instanceId1,100,4);
					break;
				case 2:
					LCN_SwitchRelay($this->instanceId1, true);
					LCN_SwitchRelay($this->instanceId2, $this->directionSwitch); 
					break;
				default:
					throw new IPSComponentException('Unknown Unittype '.$this->unitType.' for LCN Device with ID='.$this->instanceId1);
			}
		}
		
		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			switch($this->unitType) {
				case 0:
					LCN_SetIntensity($this->instanceId1,100,4);
					break;
				case 2:
					LCN_SwitchRelay($this->instanceId1, true);
					LCN_SwitchRelay($this->instanceId2, $this->directionSwitch); 
					break;
				default:
					throw new IPSComponentException('Unknown Unittype '.$this->unitType.' for LCN Device with ID='.$this->instanceId1);
			}
		}
		
		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			switch($this->unitType) {
				case 0:
					LCN_SetIntensity($this->instanceId1,0,0);
					LCN_SetIntensity($this->instanceId2,0,0);
					break;
				case 2:
					LCN_SwitchRelay($this->instanceId1,false);
					break;
				default:
					throw new IPSComponentException('Unknown Unittype '.$this->unitType.' for LCN Device with ID='.$this->instanceId1);
			}
		}

	}

	/** @}*/
?>