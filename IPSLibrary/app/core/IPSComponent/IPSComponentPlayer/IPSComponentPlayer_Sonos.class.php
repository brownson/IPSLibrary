<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	 * @file          IPSComponentPlayer_Sonos.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ('IPSComponentPlayer.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentPlayer');

	/**
	 * @class IPSComponentPlayer_Sonos
	 *
	 * Definiert ein IPSComponentPlayer_Sonos Object, das ein IPSComponentPlayer Object mit Hilfe eines Sonos Players implementiert
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	class IPSComponentPlayer_Sonos extends IPSComponentPlayer{

		private $address = "";
		private $sonos   = null;

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModulePlayer $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModulePlayer $module) {
			$name = IPS_GetName($variable);
			throw new IPSComponentException('Event Handling NOT supported for Variable '.$variable.'('.$name.')');
		}

		/**
		 * @public
		 *
		 * Initialisierung des IPSComponentPlayer_Sonos Players
		 *
		 * @param string $address IP Addresse des Sonos Players 
		 */
		public function __construct( $address ) {
			if (file_exists(IPS_GetKernelDir().'scripts\\PHPSonos.inc.php')) {
			   include_once IPS_GetKernelDir().'scripts\\PHPSonos.inc.php';
			} else {
				IPSUtils_Include ('PHPSonos.class.php', 'IPSLibrary::app::hardware::Sonos');
			}
		   $this->address = $address;
		   $this->sonos   = new PHPSonos($address);
		}

		/**
		 * @public
		 *
		 * Abspielen der aktuellen Source 
		 */
		public function Play() {
		   $this->sonos->Play();
		}

		/**
		 * @public
		 *
		 * Stop 
		 */
		public function Stop(){
		   $this->sonos->Stop();
		}

		/**
		 * @public
		 *
		 * Pause
		 */
		public function Pause(){
		   $this->sonos->Pause();
		}

		/**
		 * @public
		 *
		 * Nächster Titel
		 */
		public function Next(){
		   $this->sonos->Next();
		}

		/**
		 * @public
		 *
		 * Voriger Titel 
		 */
		public function Prev(){
		   $this->sonos->Previous();
		}

		/**
		 * @public
		 *
		 * Titel zur Playlist hinzufügen
		 *
		 * @param string $titel Titel der zur Playlist hinzugefügt werden soll
		 */
		public function AddPlaylist($titel){
		   $this->sonos->AddToQueue();
		}

		/**
		 * @public
		 *
		 * Playlist löschen
		 */
		public function ClearPlaylist(){
		   $this->sonos->ClearQueue();
		}

		/**
		 * @public
		 *
		 * Bestimmten Titel der Playlist setzen
		 *
		 * @param integer $position Nummer des Titels der abgespielt werden soll (0-n)
		 */
		public function SetPlaylistPosition($position){
			$track = $position + 1;
		   $this->sonos->SetTrack($track);
		}

		/**
		 * @public
		 *
		 * Retouniert aktuelle Position der Playlist
		 *
		 * @return integer Nummer des Titels der gerade abgespielt wird (0-n), false falls kein Titel vorhanden ist
		 */
		public function GetPlaylistPosition() {
		}

		/**
		 * @public
		 *
		 * Function retouniert Länge der Playlist
		 *
		 * @return integer Länge der Playlist (0-n)
		 */
		public function GetPlaylistLength(){
		   $playlist = $this->sonos->GetCurrentPlaylist();
		   return count($playlist);
		}

		/**
		 * @public
		 *
		 * Liefert Titel des gerade abgespielten Tracks
		 *
		 * @return string Name des Titels der gerade abgespielt wird
		 */
		public function GetTrackName() {
		}

		/**
		 * @public
		 *
		 * Liefert Länge des gerade abgespielten Tracks
		 *
		 * @return string Länge des Titels der gerade abgespielt wird
		 */
		public function GetTrackLength() {
		}

		/**
		 * @public
		 *
		 * Liefert Position des gerade abgespielten Tracks
		 *
		 * @return string Position des Titels der gerade abgespielt wird
		 */
		public function GetTrackPosition() {
		}

	}

	/** @}*/
?>