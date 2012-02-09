<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModulePlayer.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModulePlayer
	 *
	 * Definiert ein IPSModulePlayer Object, das als Wrapper für Abspielgeräte in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	abstract class IPSModulePlayer extends IPSModule {

		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation des aktuellen Titels, der auf dem referenzierten Player gerade gespielt wird
		 *
		 * @param string $titel aktueller Titel
		 */
		abstract public function SyncTitel($titel);


	}

	/** @}*/
?>