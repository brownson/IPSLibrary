<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_EIB.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter_EIB
    *
    * Definiert ein IPSComponentShutter_EIB Object, das ein IPSComponentShutter Object für EIB implementiert.
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 18.05.2012<br/>
    */

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_EIB extends IPSComponentShutter {

		private $instanceId1;
		private $instanceId2;
		private $reverseControl;
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_EIB Objektes
		 *
		 * @param integer $instanceId1 InstanceId des EIB Devices 
		 * @param integer $instanceId2 InstanceId 2 des EIB Devices (Richtungs Relais für den Fall das normale EIB Switches verwendet werden)
		 * @param boolean $reverseControl Richtungs Schalter (default=false)
		 */
		public function __construct($instanceId1, $instanceId2=null, $reverseControl=false) {
			$this->instanceId1     = IPSUtil_ObjectIDByPath($instanceId1);
			$this->instanceId2     = IPSUtil_ObjectIDByPath($instanceId2);
			$this->reverseControl  = IPSUtil_ObjectIDByPath($reverseControl);
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
		 * Funktion liefert String IPSComponent Constructor String.
		 * String kann dazu benützt werden, das Object mit der IPSComponent::CreateObjectByParams
		 * wieder neu zu erzeugen.
		 *
		 * @return string Parameter String des IPSComponent Object
		 */
		public function GetComponentParams() {
			return get_class($this).','.$this->instanceId1.','.$this->instanceId2.','.$this->reverseControl;
		}

		/**
		 * @public
		 *
		 * Hinauffahren der Beschattung
		 */
		public function MoveUp(){
			if ($this->instanceId2==null) {
				if ($this->reverseControl) {
					EIB_Move($this->instanceId1, 4); //0 = Open, 2 = Stop, 4 = Close
				} else {
					EIB_Move($this->instanceId1, 0); //0 = Open, 2 = Stop, 4 = Close
				}
			} else {
				EIB_Switch($this->instanceId1, true);
				EIB_Switch($this->instanceId2, $this->reverseControl);
			}
		}

		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			if ($this->instanceId2==null) {
				if ($this->reverseControl) {
					EIB_Move($this->instanceId1, 0); //0 = Open, 2 = Stop, 4 = Close
				} else {
					EIB_Move($this->instanceId1, 4); //0 = Open, 2 = Stop, 4 = Close
				}
			} else {
				EIB_Switch($this->instanceId1, true);
				EIB_Switch($this->instanceId2, !$this->reverseControl);
			}
		}

		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			if ($this->instanceId2==null) {
				EIB_Move($this->instanceId1, 2); //0 = Open, 2 = Stop, 4 = Close
			} else {
				EIB_Switch($this->instanceId1, false); 
				EIB_Switch($this->instanceId2, false); 
			}
		}

	}

	/** @}*/
?>