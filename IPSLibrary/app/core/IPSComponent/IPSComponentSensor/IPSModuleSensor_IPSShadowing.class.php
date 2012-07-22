<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleSensor_IPSShadowing.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleSensor_IPSShadowing
	 *
	 * Definiert ein IPSModuleSensor Object, das als Wrapper für Sensoren in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.06.2012<br/>
	 */

	IPSUtils_Include ("IPSShadowing.inc.php", "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ('IPSModuleSensor.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSensor');

	class IPSModuleSensor_IPSShadowing extends IPSModuleSensor {

		private $instanceId;
		private $movementId;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSModuleSensor_IPSShadowing Objektes
		 *
		 * @param integer $instanceId InstanceId des Homematic Devices
		 * @param boolean $movementId Movement Command
		 */
		public function __construct($instanceId, $movementId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			$this->movementId = $movementId;
		}
	
	
		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation von Sensorwerten mit Modulen
		 *
		 * @param string $value Sensorwert
		 * @param IPSComponentSensor $component Sensor Komponente
		 */
		public function SyncButton($value, IPSComponentSensor $component) {
			$device = new IPSShadowing_Device($this->instanceId);
			$movementId = GetValue(IPS_GetObjectIDByIdent(c_Control_Movement, $this->instanceId));
			if ($movementId==c_MovementId_MovingIn or $movementId==c_MovementId_MovingOut or $movementId==c_MovementId_Up or $movementId==c_MovementId_Down) {
				$device->MoveByControl(c_MovementId_Stop);
			} else {
				$device->MoveByControl($this->movementId);
			}

		}


	}

	/** @}*/
?>