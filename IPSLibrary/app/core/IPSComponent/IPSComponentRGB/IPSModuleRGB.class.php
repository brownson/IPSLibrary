<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleRGB.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleRGB
	 *
	 * Definiert ein IPSModuleRGB Object, das als Wrapper für RGB Steuergeräte in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	abstract class IPSModuleRGB extends IPSModule {

		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation des aktuellen Zustands 
		 *
		 * @param boolean $power RGB Gerät On/Off
		 * @param integer $color RGB Farben
		 * @param integer $level Dimmer Einstellung der RGB Beleuchtung
		 */
		abstract public function SyncState($power, $color, $level);

	}

	/** @}*/
?>