<?
	/**@defgroup netplayer_configuration NetPlayer Konfiguration
	 * @ingroup netplayer
	 * @{
	 *
	 * Konfiguration der NetPlayers
	 *
	 * @file          NetPlayer\NetPlayer_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Script mit NetPlayer Konfigurations Parametern
	 *
	 */

   /** NetPlayer Player Component Definition
	 *
	 * Definiert Verbindung zur Hardware Komponente des Players
	 *
	 * Alle Parameter zur Erzeugung des Player Objektes werden durch Comma getrennt.
	 * - Param1 ist die Klasse des Players (muss von IPSComponentPlayer abgeleitet sein)
	 * - Param2-n spezifizieren die Parameter, die zum Erzeugen des spezifischen Player Objektes notwendig sind.
	 *
	 * Beispiele:
	 *   define ("NETPLAYER_COMPONENT", 'IPSComponentPlayer_Mediaplayer,36728');
	 *   define ("NETPLAYER_COMPONENT", 'IPSComponentPlayer_Mediaplayer,Hardware.NetPlayer.MediaPlayer');
	 *   define ("NETPLAYER_COMPONENT", 'IPSComponentPlayer_Sonos,192.168.0.12');
	 *
	 * Im Falle des MediaPlayers wird zum Erzeugen eines Objectes die ID der Mediaplayer Instanz
	 * benötigt. Alternativ kann auch der IPS Pfad zum Player angegeben werden (Beispiel 2).
	 * Beispiel 3 definiert einen Sonos Player, der als Parameter die IP des Players benötigt.
	 *
	 */
	define ("NETPLAYER_COMPONENT", 'IPSComponentPlayer_Mediaplayer,Hardware.NetPlayer.MediaPlayer');
	//define ("NETPLAYER_COMPONENT", 'IPSComponentPlayer_Mediaplayer,36728');

   /** Input Directory für NetPlayer
	 *
	 * In diesem Verzeichnis sucht der NetPlayer nach MP3 Files, die beinhaltenden Verzeichnisse und
	 * Files sollten folgende Form haben:
	 * .../Interpret [Album]/LiedNummer Titel.mp3
	 *
	 * Beispiele:
	 * C:/Music/Metallica [S&M]/01 - Battery.mp3
	 * C:/Music/_Sampler/Bravo Hits 54 []/01 - Yana Yana.mp3
	 *
	 * Zusätzlich werden auch noch Musik Kategorien unterstützt, diese erlauben es die Musik in verschiedene
	 * Gruppen einzuteilen.
	 * Kategorien werden durch ein "_" am Beginn des Verzeichnisses definiert, die Verzeichnisse werden bei
	 * der Musik Auswahl automatisch in der Navigations Leiste aufgelistet.
	 *
	 * Beispiele:
	 * _Sampler
	 * _Soundtracks
	 * _Kinderlieder
	 *
	 * CD Covers werden immer im aktuell gewählten Song Verzeichnis gesucht. Als erstes wird ein JPG Datei
	 * gesucht, die ein "front" im Namen hat. Wird keine Datei gefunden wird die erste gefundene JPG Datei
	 * angezeigt.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, erneute Installation des Players ist nicht
	 * erforderlich.
	 */

	define ("NETPLAYER_DIRECTORY", 		"C:/Music/");

   /** Liste der Radio Sender
	 *
	 * Hier wird die Liste der Radio Sender definiert, die im WebFront zur Auswahl steht.
	 *
	 * @return string[] Liste von RadioName/RadioUrl Paaren
	 */
	function NetPlayer_GetRadioList() {
		$radiolist = array(
			'OE 3' 				=> "mms://apasf.apa.at/OE3_Live_Audio",
			'KroneHit Radio' 	=> "http://onair.krone.at:80/kronehit.mp3",
			'Radio Wien' 		=> "http://mp3stream2.apasf.apa.at:8000",
			'Radio 88.6' 		=> "http://www.radiostream.de/stream/36889.asx",
			'Radio Arabella' 	=> "http://stream01.arabella-at.vss.kapper.net:8000/;stream.mp3",
			'FM4' 				=> "mms://apasf.apa.at/fm4_live_worldwide",
			'Radio Harmonie' 	=> "http://92.63.218.120:9630",
			'Hit FM' 			=> "http://hitfm.biosnet.at:8000",
			'Lounge FM' 		=> "http://stream.lounge.fm/loungefm128.m3u",
			'Fan Radio' 		=> "http://onair.krone.at/krone-fanradio.mp3.m3u",
		);
		return $radiolist;
	}

   /**
	 * Anzahl der CDs, die im HTML Interface angezeigt werden.
	 */
	define ("NP_COUNT_CDHTML",               16);

   /**
	 * Anzahl der CDs, die im Mobile Interface angezeigt werden.
	 */
	define ("NP_COUNT_CDMOBILE",             10);

   /**
	 * Anzahl der CDs, die im Webfront Interface (Variablen deren Profil dynamisch angepasst wird) angezeigt werden.
	 */
	define ("NP_COUNT_CDVARIABLE",           6);

   /**
	 * Anzahl der Radiosender, die im HTML Interface angezeigt werden.
	 */
	define ("NP_COUNT_RADIOHTML",            16);

   /**
	 * Anzahl der Radiosender, die im Mobile Interface angezeigt werden.
	 */
	define ("NP_COUNT_RADIOMOBILE",          16);

   /**
	 * Anzahl der Radiosender, die im Webfront Interface (Variablen deren Profil dynamisch angepasst wird) angezeigt werden.
	 */
	define ("NP_COUNT_RADIOVARIABLE",        6);

   /**
	 * Anzahl der Tracknamen, die im Webfront Interface (Variablen deren Profil dynamisch angepasst wird) angezeigt werden.
	 */
	define ("NP_COUNT_TRACKVARIABLE",        6);

   /**
	 * Definition des HTML Player Interfaces
	 */
  	define ("NP_RC_MP3CONTROL",       			'src="../user/NetPlayer/NetPlayer_MP3Control.php"    height=260px');

   /**
	 * Definition des HTML CD Auswahl Interfaces
	 */
  	define ("NP_RC_MP3SELECTION",    			'src="../user/NetPlayer/NetPlayer_MP3Selection.php"  height=252px');

   /**
	 * Definition des HTML Radioplayer Interfaces
	 */
  	define ("NP_RC_RADIOCONTROL",     			'src="../user/NetPlayer/NetPlayer_RadioControl.php"  height=182px');

   /**
	 * Definition des Mobile Interfaces
	 */
  	define ("NP_RC_MOBILE",           			'src="../user/NetPlayer/NetPlayer_Mobile.php"        height=3000px');

   /** @}*/
?>