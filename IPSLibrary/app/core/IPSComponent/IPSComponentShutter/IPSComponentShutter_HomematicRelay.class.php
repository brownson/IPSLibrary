<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_HomematicRelay.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_HomematicRelay
    *
    * Definiert ein IPSComponentShutter_HomematicRelay Object, das ein IPSComponentShutter Object für Homematic mittels Relais implementiert.
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 10.08.2012<br/>
    */

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

    class IPSComponentShutter_HomematicRelay extends IPSComponentShutter {

        private $instanceId1;
        private $instanceId2;
        private $directionSwitch;


        /**
         * @public
         *
         * Initialisierung eines IPSComponentShutter_HomematicRelay Objektes
         *
         * @param integer $instanceId1 InstanceId 1 des HM Devices (Movement)
         * @param integer $instanceId2 InstanceId 2 des HM Devices (Direction)
         * @param boolean $directionSwitch Richtungs Schalter (default=false)
         */
        public function __construct($instanceId1, $instanceId2, $directionSwitch=false) {
            $this->instanceId1     = IPSUtil_ObjectIDByPath($instanceId1);
            $this->instanceId2     = IPSUtil_ObjectIDByPath($instanceId2);
            $this->directionSwitch = $directionSwitch;
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
		public function MoveUp() {
			HM_WriteValueBoolean($this->instanceId1,  'STATE', true);
			HM_WriteValueBoolean($this->instanceId2, 'STATE', $this->directionSwitch);
		}

		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown() {
			HM_WriteValueBoolean($this->instanceId1,  'STATE', true);
			HM_WriteValueBoolean($this->instanceId2, 'STATE', !$this->directionSwitch);
		}

		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			HM_WriteValueBoolean($this->instanceId1,  'STATE', false);
		}
	}

    /** @}*/
?>  