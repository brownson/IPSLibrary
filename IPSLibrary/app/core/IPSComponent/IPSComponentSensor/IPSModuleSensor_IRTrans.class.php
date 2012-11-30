<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleSensor_IRTrans.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleSensor_IRTrans
	 *
	 * Definiert ein IPSModuleSensor Object, das als Wrapper für Sensoren in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.06.2012<br/>
	 */

	IPSUtils_Include ("IRTrans_InterfaceIPSComponentSensor.inc.php", "IPSLibrary::app::modules::IRTrans");
	IPSUtils_Include ('IPSModuleSensor.class.php',                   'IPSLibrary::app::core::IPSComponent::IPSComponentSensor');

	class IPSModuleSensor_IRTrans extends IPSModuleSensor {

		private $instanceId;
		private $device;
		private $button;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSModuleSensor_IRTrans Objektes
		 *
		 * @param integer $instanceId InstanceId
		 * @param string $device Device
		 * @param string $button Button
		 */
		public function __construct($instanceId, $device='', $button='') {
			$this->instanceId = $instanceId;
			$this->device     = $device;
			$this->button     = $button;
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
			$this->ExecuteButton();
		}

		/**
		 * @public
		 *
		 * Ermöglicht das Verarbeiten eines Taster Signals
		 *
		 */
		public function ExecuteButton () {
			IRT_SendOnce($this->instanceId, $this->device, $this->button);
		}

	}

	/** @}*/
?>