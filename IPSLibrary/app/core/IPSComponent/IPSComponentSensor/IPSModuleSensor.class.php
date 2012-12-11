<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleSensor.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleSensor
	 *
	 * Definiert ein IPSModuleSensor Object, das als Wrapper f�r Sensoren in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.06.2012<br/>
	 */

	IPSUtils_Include ('IPSModule.class.php', 'IPSLibrary::app::core::IPSComponent');

	abstract class IPSModuleSensor extends IPSModule {

		/**
		 * @public
		 *
		 * Erm�glicht die Synchronisation von Sensorwerten mit Modulen
		 *
		 * @param string $value Sensorwert
		 * @param IPSComponentSensor $component Sensor Komponente
		 */
		abstract public function SyncButton($value, IPSComponentSensor $component);

		/**
		 * @public
		 *
		 * Erm�glicht das Verarbeiten eines Taster Signals
		 *
		 */
		abstract public function ExecuteButton();


	}

	/** @}*/
?>