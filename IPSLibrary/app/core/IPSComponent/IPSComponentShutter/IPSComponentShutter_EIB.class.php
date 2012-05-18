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

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_EIB Objektes
		 *
		 * @param integer $instanceId InstanceId des EIB Devices
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
		 * Hinauffahren der Beschattung
		 */
		public function MoveUp(){
			EIB_Move($this->instanceId, 0); //0 = Open, 2 = Stop, 4 = Close
		}
		
		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			EIB_Move($this->instanceId, 4); //0 = Open, 2 = Stop, 4 = Close
		}
		
		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			EIB_Move($this->instanceId, 2); //0 = Open, 2 = Stop, 4 = Close
		}

	}

	/** @}*/
?>