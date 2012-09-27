<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	 *
	 * @file        IPSComponentShutter_digitalStrom.class.php
	 * @author      Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSComponentShutter_digitalStrom
	 *
	 * Definiert ein IPSComponentShutter_digitalStrom Object, das ein IPSComponentShutter Object f�r digitalStrom implementiert.
	 *
	 * @author Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 31.01.2012<br/>
	 */

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_digitalStrom extends IPSComponentShutter {

		private $instanceId;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_digitalStrom Objektes
		 *
		 * @param integer $instanceId InstanceId des digitalStrom Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
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
			DS_ShutterMoveUp($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
			DS_ShutterMoveDown($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			DS_ShutterStop($this->instanceId);
		}

	}

	/** @}*/
?> 