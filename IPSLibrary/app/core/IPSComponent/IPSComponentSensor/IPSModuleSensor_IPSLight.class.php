<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleSensor_IPSLight.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleSensor_IPSLight
	 *
	 * Definiert ein IPSModuleSensor Object, das als Wrapper für Sensoren in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.06.2012<br/>
	 */

	include_once IPS_GetKernelDir().'scripts\\IPSLight.ips.php';
	IPSUtils_Include ('IPSModuleSensor.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSensor');

	class IPSModuleSensor_IPSLight extends IPSModuleSensor {

		private $lightObject;
		private $lightFunction;
		private $lightValue;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSModuleSensor_IPSLight Objektes
		 *
		 * @param string $lightObject Licht Object/Name (Leuchte, Gruppe, Programm, ...)
		 * @param string $lightFunction Function die ausgeführt werden soll
		 * @param string $lightValue Wert für Beleuchtungs Änderung
		 */
		public function __construct($lightFunction, $lightObject, $lightValue=null) {
			$this->lightObject   = $lightObject;
			$this->lightFunction = $lightFunction;
			$this->lightValue    = $lightValue;
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
			switch ($this->lightFunction) {
				case 'DimRelativByName':
					DimRelativByName($this->lightObject, $this->lightValue);
					break;
				case 'ToggleSwitchByName':
					ToggleSwitchByName($this->lightObject);
					break;
				case 'ToggleGroupByName':
					ToggleGroupByName($this->lightObject);
					break;
				case 'SetProgramNext':
					SetProgramNext($this->lightObject);
					break;
				default:
					IPSLogger_Wrn(__file__, 'Unknown Button Function "'.$this->lightFunction.'"');
			}
		}


	}

	/** @}*/
?>