<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleShutter.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleShutter
	 *
	 * Definiert ein IPSModuleShutter Object, das als Wrapper für Beschattungsgeräte in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	abstract class IPSModuleShutter extends IPSLibraryModule {

		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation der aktuellen Position der Beschattung
		 *
		 * @param string $position Aktuelle Position der Beschattung (Wertebereich 0-100)
		 */
		abstract public function SyncPosition($position, IPSComponentShutter $component);


	}

	/** @}*/
?>