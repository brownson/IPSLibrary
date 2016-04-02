<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleDimmer.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleDimmer
	 *
	 * Definiert ein IPSModuleDimmer Object, das als Wrapper für Dimmer in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	abstract class IPSModuleDimmer extends IPSLibraryModule {

		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation des aktuellen Dimmer Levels
		 *
		 * @param integer $level aktueller Status des Gerätes (Wertebereich 0-100)
		 */
		abstract public function SyncDimLevel($level);

	}

	/** @}*/
?>