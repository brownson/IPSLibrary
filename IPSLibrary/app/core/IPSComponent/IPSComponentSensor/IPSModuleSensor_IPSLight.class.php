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
	 * Definiert ein IPSModuleSensor Object, das als Wrapper f�r Sensoren in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.06.2012<br/>
	 */

	IPSUtils_Include ('IPSLight.inc.php',          'IPSLibrary::app::modules::IPSLight');
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
		 * @param string $lightFunction Function die ausgef�hrt werden soll
		 * @param string $lightValue Wert f�r Beleuchtungs �nderung
		 */
		public function __construct($lightFunction, $lightObject, $lightValue=null) {
			$this->lightObject   = $lightObject;
			$this->lightFunction = $lightFunction;
			$this->lightValue    = $lightValue;
		}
	
	
		/**
		 * @public
		 *
		 * Erm�glicht die Synchronisation von Sensorwerten mit Modulen
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
		 * Erm�glicht das Verarbeiten eines Taster Signals
		 *
		 */
		public function ExecuteButton () {
			switch ($this->lightFunction) {
				case 'IPSLight_DimAbsoluteByName':
					IPSLight_DimAbsoluteByName($this->lightObject, $this->lightValue);
					break;
				case 'IPSLight_DimRelativByName':
					IPSLight_DimRelativByName($this->lightObject, $this->lightValue);
					break;

				case 'IPSLight_SetSwitchByName':
					IPSLight_SetSwitchByName($this->lightObject, $this->lightValue);
					break;
				case 'IPSLight_ToggleSwitchByName':
					IPSLight_ToggleSwitchByName($this->lightObject);
					break;


				case 'IPSLight_SetGroupByName':
					IPSLight_SetGroupByName($this->lightObject, $this->lightValue);
					break;
				case 'IPSLight_ToggleGroupByName':
					IPSLight_ToggleGroupByName($this->lightObject);
					break;

				case 'IPSLight_SetProgramNextByName':
					IPSLight_SetProgramNextByName($this->lightObject);
					break;
				case 'IPSLight_SetProgramName':
					IPSLight_SetProgramName($this->lightObject, $this->lightValue);
					break;
				default:
					IPSLogger_Wrn(__file__, 'Unknown Button Function "'.$this->lightFunction.'"');
			}
		}

	}

	/** @}*/
?>