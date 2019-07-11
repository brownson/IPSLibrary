<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	  *
	 * @file		  IPSComponentShutter_Tasmota.class.php
	 * @author		Andreas Brauneis
	 *
	 *
	 */

   /**
	* @class IPSComponentShutter_Tasmota
	*
	* Definiert ein IPSComponentShutter_Tasmota Object, das ein IPSComponentShutter Object für Enocean implementiert.
	*
	* @author Andreas Brauneis
	* @version
	* Version 2.50.1, 01.07.2019<br/>
	*/

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_Tasmota extends IPSComponentShutter {

		private $instanceId;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_Tasmota Objektes
		 *
		 * @param integer $instanceId InstanceId des Enocean Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
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
			Tasmota_setPower($this->instanceId, 1, true);
		}

		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			Tasmota_setPower($this->instanceId, 2, true);
		}

		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			Tasmota_setPower($this->instanceId, 1, false);
		}
	}

	/** @}*/
?>