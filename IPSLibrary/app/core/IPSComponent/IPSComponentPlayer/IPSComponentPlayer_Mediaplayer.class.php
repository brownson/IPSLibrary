<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentPlayer_MediaPlayer.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	IPSUtils_Include ('IPSComponentPlayer.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentPlayer');
	
   /**
    * @class IPSComponentPlayer_MediaPlayer
    *
    * Definiert ein IPSComponentPlayer_MediaPlayer Object, das ein IPSComponentPlayer Object mit Hilfe des IPS Mediaplayers implementiert
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	class IPSComponentPlayer_MediaPlayer extends IPSComponentPlayer{

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung des IPSComponentPlayer_MediaPlayer
		 *
		 * @param integer $instanceId InstanceId des MediaPlayers
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
		}

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
			switch($name) {
				case 'Titel': // Sync current Titel of Player
					$module->SyncTitel($value);
					break;
				default:
					throw new IPSComponentException('Event Handling NOT supported for Variable '.$variable.'('.$name.')');
			}
		}

		/**
		 * @public
		 *
		 * Abspielen der aktuellen Source 
		 */
		public function Play() {
			if (WAC_GetPlaylistLength($this->instanceId) > 0){
				WAC_Play($this->instanceId);
				$result = true;
			} else {
				$result = false;
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Stop 
		 */
		public function Stop(){
			WAC_Stop($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Pause
		 */
		public function Pause(){
			WAC_Pause($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Nächster Titel
		 */
		public function Next(){
			@WAC_Next($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Voriger Titel 
		 */
		public function Prev(){
			@WAC_Prev($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Titel zur Playlist hinzufügen
		 *
		 * @param string $titel Titel der zur Playlist hinzugefügt werden soll
		 */
		public function AddPlaylist($titel){
			WAC_AddFile($this->instanceId, $titel);
		}

		/**
		 * @public
		 *
		 * Alle Titel der Playlist löschen
		 */
		public function ClearPlaylist(){
			WAC_ClearPlaylist($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Bestimmten Titel der Playlist setzen
		 *
		 * @param integer $position Nummer des Titels der abgespielt werden soll 
		 */
		public function SetPlaylistPosition($position){
			if (WAC_GetPlaylistLength($this->instanceId) > 0 and 
			    $position < WAC_GetPlaylistLength($this->instanceId) and 
				$position > 0){
				WAC_SetPlaylistPosition($this->instanceId, (int)$position);
				WAC_Play($this->instanceId);
				$result = true;
			} else {
				$result = false;
			}
			return $result;
		}
		
		/**
		 * @public
		 *
		 * Retouniert aktuelle Position der Playlist
		 *
		 * @return integer Nummer des Titels der gerade abgespielt wird (0-n), false falls kein Titel vorhanden ist
		 */
		public function GetPlaylistPosition() {
			if (WAC_GetPlaylistLength($this->instanceId) > 0) {
				$result = WAC_GetPlaylistPosition($this->instanceId);
			} else {
				$result = false;
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Function retouniert Länge der Playlist
		 *
		 * @return integer Länge der Playlist
		 */
		public function GetPlaylistLength(){
			return WAC_GetPlaylistLength($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Liefert Titel des gerade abgespielten Tracks
		 *
		 * @return string Name des Titels der gerade abgespielt wird
		 */
		public function GetTrackName() {
			return GetValue(IPS_GetVariableIDByName('Titel',$this->instanceId));
		}

		/**
		 * @public
		 *
		 * Liefert Länge des gerade abgespielten Tracks
		 *
		 * @return string Länge des Titels der gerade abgespielt wird
		 */
		public function GetTrackLength() {
			return GetValue(IPS_GetVariableIDByName('Titellänge',$this->instanceId));
		}

		/**
		 * @public
		 *
		 * Liefert Position des gerade abgespielten Tracks
		 *
		 * @return string Position des Titels der gerade abgespielt wird
		 */
		public function GetTrackPosition() {
			return GetValue(IPS_GetVariableIDByName('Titelposition',$this->instanceId));
		}

	}

	/** @}*/
?>