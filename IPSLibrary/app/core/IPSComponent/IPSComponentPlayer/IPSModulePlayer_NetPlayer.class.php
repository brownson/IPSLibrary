<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModulePlayer_NetPlayer.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	IPSUtils_Include ("NetPlayer.inc.php", "IPSLibrary::app::modules::NetPlayer");
	include_once 'IPSModulePlayer.class.php';

	/**
	 * @class IPSModulePlayer_NetPlayer
	 *
	 * Klasse zur Ansteuerung des Netplayer Modules
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	class IPSModulePlayer_NetPlayer extends IPSModulePlayer {

		/**
		 * @public
		 *
		 * Initialisierung des IPSModulePlayer_NetPlayer
		 *
		 */
		public function __construct() {
		}

		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation des aktuellen Titels, der auf dem referenzierten Player gerade gespielt wird
		 *
		 * @param string $titel aktueller Titel
		 */
		public function SyncTitel($titel) {
			NetPlayer_RefreshTrackListValue();
		}

	}

	/** @}*/
?>