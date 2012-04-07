<?
	/**
	 * This file is part of the IPSLibrary.
	 *
	 * The IPSLibrary is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published
	 * by the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * The IPSLibrary is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
	 */    

	 /**@defgroup audiomax_protocol AudioMax Kommunikations Protokol
	 * @ingroup audiomax
	 * @{
	 *
	 * @page audiomax_protocol_types Übersicht über die verschiedenen Befehlstypen
	 * Grundsätzlich gibt es vier verschiedene Befehlsarten
	 * - SET:  Setzen von Werten im AudioMax
	 * - GET:  Abfrage von Serverwerten
	 * - EVT:  Nachrichten die vom AudioMax selbsttätig gesendet werden
	 * - MOD:  Setzen von Betriebsarten
	 *
	 * @page audiomax_protocol_commands Befehlsaufbau
	 * -  Alle Befehle können in Groß- oder Kleinbuchstaben geschrieben werden.
	 * -  Die einzelnen Befehlsteile sind durch Leerzeichen getrennt.
	 * -  Alle Befehle werden mit „Carriage Return“ („CR“, Dezimal 13) beendet
	 * -  Das System gibt auf jeden Befehl eine Bestätigung
	 * -  Der AudioMax Server gibt per „GET SVR ?“ alle verfügbaren Befehle aus
	 * -  Der AudioServer sendet alle 60 Sekunden eine „Keep Alive“ Meldung
	 * -  Der AudioMax Server erwartet alle 60 Sekunden eine „Keep Alive“ Meldung vom PC.
	 * -  Es können Texte auf dem LCD Display ausgegeben werden (Geräte mit Display)
	 * -  Nach dem Start des AudioMax Servers werden die letzten Audioeinstellungen vor dem letzten Abschalten wiederhergestellt.
	 *
	 * @page audiomax_protocol_set SET Kommando
	 * Werte an AudioMax Server senden:
	 * - SET SVR AUD [RAUMNUMMER] [AUDIO] [STATUS]    AUDIO (STATUS)
	 *                             + VOL = Volume (40–0, 40 = Mute)
	 *                             + INP = Input Analog Signal (0-3)
	 *                             + GAI = Gain, Verstärkung  (0-15, 0 = 0dB Verstärkung)
	 *                             + BAL = Balance (0-15, 15 = Rechts)
	 *                             + BAS = Bass (0-15, 15 = +14dB)
	 *                             + MID = Middle (0-15, 15 = +14dB)
	 *                             + TRE = Treble (0-15, 15 = +14dB)
	 * - SET SVR ROO [RAUMNUMMER] [STATUS]     Steuerung Raumverstärker (0 – 3)
	 * - SET SVR PWR [STATUS]                  AudioMax Server Ein/Aus, (1=On 0=Off)
	 * - SET SVR TEX [TEXT1] [TEXT2] [TEXT3]   Textausgabe auf LCD Display, max 20 Zeichen pro Zeile, TEXT1 = 2.Zeile, TEXT2 = 3.Zeile, TEXT3 = 4.Zeile
	 * - SET SVR KAL 0                         “Keep Alive” Signal an PC
	 * - SET SVR MOD [WERT]   Setze von Betriebsarten (0 = Acknolage, 1 = Debug On/Off, 2 = Button Room Amp, 3 = KAL von PC)
	 *
	 * @page audiomax_protocol_get GET Kommando
	 * Werte von AudioMax Server abholen
	 * - GET SVR AUD [RAUMNUMMER] [AUDIO] [STATUS]    Werte der Audioeinstellungen
	 * - GET SVR ROO [RAUMNUMMER] [STATUS]            Status der Raumverstärker
	 * - GET SVR PWR [STATUS]                         Status des Servers
	 * - GET SVR VER [VERSION]                        Ausgabe der Firmwareversion
	 * - GET SVR MOD [MODEL]                          Ausgabe AudioMax Version (S404, S408, ..)
	 * - GET SVR HAR [HARDWARE VERSION, YEAR]         Hardware Version, Herstelljahr
	 * - GET SVR ?                                    Ausgabe des Hilfetext => Alle Befehle
	 *
	 * @page audiomax_protocol_evt EVT Kommando
	 * AudioMax Server sendet selbständig Statusmeldungen
	 * - EVT SRV PWR [STATUS]     Statusmeldung nach Power On
	 * - EVT SVR KAL [STATUS]     „Keep Alive“ Meldung an PC, 0=Standby, 1=Server On
	 * - EVT SVR ROO [NUMMER] 1   Taste des Raumverstärkers [NUMMER] betätigt (1 = betätigt)
	 *
	 * @page audiomax_protocol_mod MOD Kommando
	 * Mit diesen Befehlen können verschiedene Betriebsarten für den AudioServer gesetzt werden
	 * EVT SRV MOD [MODE] [STATUS]
	 * - MODE 0    Acknowledge (Default = 0, 0= Acknowledge 0 bis 5, 1 = Echo des Befehls)
	 * - MODE 1    Debug Ausgaben (Default = 0, 0 = keine Debug Ausgaben, 1 = Debug Ausgaben)
	 * - MODE 2    Tasterfunktion Raumverstärker (Default = 0, 0 = nur Meldung an PC, 1 = Der Raumverstärker wird direkt geschalten +  Meldung an PC)
	 * - MODE 3    KAL von PC aktiviert/deaktiviert (Default = 0, 0 = KAL von PC erwartet, 1 = keine KAL Meldung von PC erwartet)
	 *
	 * @page audiomax_protocol_error Acknowledge und Error Code
	 * Acknowledge:
	 * MODE0 = 0
	 * “0“  => Befehl erkannt
	 *
	 * MODE0 = 1    Bei erkanntem Befehl wird der gesendete Befehl als Echo ausgegeben. Fehler werden wie nachfolgend ausgegeben
	 * Error Code:
	 * “1”  => Error 1, Command Array 1, Unknown Command  => Fehler im 1. Befehlsteil
	 * “2”  => Error 2, Command Array 2, Unknown Command => Fehler im 2. Befehlsteil
	 * “3”  => Error 3, Command Array 3, Unknown Command => Fehler im 3. Befehlsteil
	 * “4”  => Error 4, Command Array 4, Out Of Range  => Fehler im 4. Befehlsteil
	 * “5”  => Error 5, Command Array 5, Out Of Range => Fehler im 5. Befehlsteil
	 *
	 */

	 /**@defgroup audiomax_install AudioMax Installation
	 * @ingroup audiomax
	 * @{
	 *
	 * AudioMax Installations File
	 *
	 * @file          AudioMax_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 18.02.2012<br/>
	 *
	 * Script zur kompletten Installation der AudioMax Entertainment Steuerung.
	 *
	 * Vor der Installation sollte noch das File AudioMax_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page rquirements_audiomax Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 *
	 * @page install_audiomax Installations Schritte
	 * Folgende Schritte sind zur Installation der AudioMax Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'."\n";
		$moduleManager = new IPSModuleManager('AudioMax');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",             "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("AudioMax_Constants.inc.php",       "IPSLibrary::app::hardware::AudioMax");
	IPSUtils_Include ("AudioMax_Configuration.inc.php",   "IPSLibrary::config::hardware::AudioMax");

	$IgnoreIOPortInstanceError    = $moduleManager->GetConfigValueBool('IgnoreIOPortInstanceError');
	$AudioMaxRoomInstallation     = $moduleManager->GetConfigValueBool('AudioMaxRoomInstallation');

	/* ---------------------------------------------------------------------- */
	/* AudioMax Installation                                                  */
	/* ---------------------------------------------------------------------- */

	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');
	$CategoryIdHardware = CreateCategoryPath('Hardware.AudioMax');

	$id_ScriptReceive         = IPS_GetScriptIDByName('AudioMax_Receive',        $CategoryIdApp);
	$id_ScriptSettings        = IPS_GetScriptIDByName('AudioMax_ChangeSettings', $CategoryIdApp);
	$id_ScriptKeepAlive       = IPS_GetScriptIDByName('AudioMax_KeepAlive',      $CategoryIdApp);

	if (AM_CONFIG_COM_PORT) {
		$id_IOComPort   = CreateSerialPort('AudioMax_ComPort', AM_CONFIG_COM_PORT, 19200, 1, 8, 'None',0,$IgnoreIOPortInstanceError);
		$id_Cutter      = CreateVariableCutter('AudioMax_Cutter', $id_IOComPort, '', chr(13));
		$id_Register    = CreateRegisterVariable('AudioMax_Register', $CategoryIdHardware, $id_ScriptReceive, $id_Cutter);
	}
	CreateTimer_CyclicBySeconds ('SendAlive',  $id_ScriptKeepAlive, AM_COM_KEEPALIVE,   true);
	CreateTimer_CyclicBySeconds ('CheckAlive', $id_ScriptKeepAlive, AM_COM_KEEPALIVE+5, true);

	CreateProfile_Count ('AudioMax_Volume',       AM_VAL_VOLUME_MIN,       1, AM_VAL_VOLUME_MAX,       "", "%");
	CreateProfile_Count ('AudioMax_Treble',       AM_VAL_TREBLE_MIN,       1, AM_VAL_TREBLE_MAX,       "", "%");
	CreateProfile_Count ('AudioMax_Middle',       AM_VAL_MIDDLE_MIN,       1, AM_VAL_MIDDLE_MAX,       "", "%");
	CreateProfile_Count ('AudioMax_Bass',         AM_VAL_BASS_MIN,         1, AM_VAL_BASS_MAX,         "", "%");
	CreateProfile_Count ('AudioMax_Balance',      AM_VAL_BALANCE_MIN,      1, AM_VAL_BALANCE_MAX,      "", "%");
	CreateProfile_Count ('AudioMax_InputGain',    AM_VAL_INPUTGAIN_MIN,    1, AM_VAL_INPUTGAIN_MAX,    "", "%");
	CreateProfile_Associations ('AudioMax_InputSelect', array(AM_CONFIG_INPUTNAME1, AM_CONFIG_INPUTNAME2, AM_CONFIG_INPUTNAME3, AM_CONFIG_INPUTNAME4));
	CreateProfile_Switch ('AudioMax_Mute',            'Aus', 'An', "", -1, 0x00ff00);
	CreateProfile_Switch ('AudioMax_KeepAliveFlag',   'Waiting', 'OK', "", -1, 0x00ff00);
	CreateProfile_Switch ('AudioMax_KeepAliveStatus', 'KeepAlive Error', 'KeepAlive OK', "", 0xaa0000, 0x00ff00);
	CreateProfile_Switch ('AudioMax_Busy',            'Bereit', 'Aktiv', "", -1, 0x0000ff);
	CreateProfile_Switch ('AudioMax_Connection',      'Verbindung Deaktiviert', 'Verbindung Aktiv', "", 0xaa0000, 0x0000ff, 'LockOpen', 'LockClosed');

	$id_AudioMaxServerId = CreateDummyInstance("AudioMax_Server", $CategoryIdData, 10);
	$id_Power            = CreateVariable(AM_VAR_MAINPOWER,       0 /*Boolean*/, $id_AudioMaxServerId,  10, '~Switch',              $id_ScriptSettings, false, 'Power');
	$id_Busy             = CreateVariable(AM_VAR_BUSY,            0 /*Boolean*/, $id_AudioMaxServerId,  20, 'AudioMax_Busy',        null,               false, 'Distance');
	$id_Connection       = CreateVariable(AM_VAR_CONNECTION,      0 /*Boolean*/, $id_AudioMaxServerId,  30, 'AudioMax_Connection',  $id_ScriptSettings, true,  '');
	$id_LastError        = CreateVariable(AM_VAR_LASTERROR,       3 /*String*/,  $id_AudioMaxServerId,  40, '~String',              null,               '',    'Warning');
	$id_LastCommand      = CreateVariable(AM_VAR_LASTCOMMAND,     3 /*String*/,  $id_AudioMaxServerId,  50, '~String',              null,               '',    'Information');
	$id_InputBuffer      = CreateVariable(AM_VAR_INPUTBUFFER,     3 /*String*/,  $id_AudioMaxServerId,  60, '~String',              null,               '',    'Information');
	$id_KeepAliveFlag    = CreateVariable(AM_VAR_KEEPALIVEFLAG,   0 /*Boolean*/, $id_AudioMaxServerId,  70, 'AudioMax_KeepAliveFlag',   null,           false, 'Lightning');
	$id_KeepAliveStatus  = CreateVariable(AM_VAR_KEEPALIVESTATUS, 0 /*Boolean*/, $id_AudioMaxServerId,  80, 'AudioMax_KeepAliveStatus', null,           true,  'Repeat');
	$id_ModePowerRequest = CreateVariable(AM_VAR_MODEPOWERREQUEST,0 /*Boolean*/, $id_AudioMaxServerId,  90, '~Switch',               $id_ScriptSettings,true,  'Gear');
	$id_ModeServerDebug  = CreateVariable(AM_VAR_MODESERVERDEBUG, 0 /*Boolean*/, $id_AudioMaxServerId, 100, '~Switch',               $id_ScriptSettings,true,  'Gear');
	$id_ModeEmulateState = CreateVariable(AM_VAR_MODEEMULATESTATE,0 /*Boolean*/, $id_AudioMaxServerId, 100, '~Switch',               $id_ScriptSettings,true,  'Gear');
	$id_Port             = CreateVariable(AM_VAR_PORTID,          1 /*Integer*/, $id_AudioMaxServerId, 300, '',                      null,              0,     '');
	$id_RoomIds          = CreateVariable(AM_VAR_ROOMIDS,         3 /*String*/,  $id_AudioMaxServerId, 310, '',                      null,              '',    '');
	$id_RoomCount        = CreateVariable(AM_VAR_ROOMCOUNT,       1 /*Integer*/, $id_AudioMaxServerId, 320, '',                      null,              0,     '');

	if ($AudioMaxRoomInstallation) {
		$RoomIds = array();
		for ($RoomId=1;$RoomId<=AM_CONFIG_ROOM_COUNT;$RoomId++) {
			$RoomInstanceId = CreateDummyInstance("AudioMax_Room".$RoomId, $CategoryIdData, 100+$RoomId);
			$RoomIds[]      = $RoomInstanceId;

			$PowerId        = CreateVariable(AM_VAR_ROOMPOWER,   0 /*Boolean*/, $RoomInstanceId,  10, '~Switch',              $id_ScriptSettings, AM_VAL_POWER_DEFAULT, 'Power');
			$SelectId       = CreateVariable(AM_VAR_INPUTSELECT, 1 /*Integer*/, $RoomInstanceId,  20, 'AudioMax_InputSelect', $id_ScriptSettings, AM_VAL_INPUTSELECT_DEFAULT, 'Gear');
			$GainId         = CreateVariable(AM_VAR_INPUTGAIN,   1 /*Integer*/, $RoomInstanceId,  30, 'AudioMax_InputGain',   $id_ScriptSettings, AM_VAL_INPUTGAIN_DEFAULT, 'Lightning');
			$VolumeId       = CreateVariable(AM_VAR_VOLUME,      1 /*Integer*/, $RoomInstanceId,  40, 'AudioMax_Volume',      $id_ScriptSettings, AM_VAL_VOLUME_DEFAULT, 'Intensity');
			$MutingId       = CreateVariable(AM_VAR_MUTE,        0 /*Boolean*/, $RoomInstanceId,  50, 'AudioMax_Mute',        $id_ScriptSettings, AM_VAL_MUTE_DEFAULT, 'Speaker');
			$BalanceId      = CreateVariable(AM_VAR_BALANCE,     1 /*Integer*/, $RoomInstanceId,  60, 'AudioMax_Balance',     $id_ScriptSettings, AM_VAL_BALANCE_DEFAULT, 'Speaker');
			$TrebleId       = CreateVariable(AM_VAR_TREBLE,      1 /*Integer*/, $RoomInstanceId,  70, 'AudioMax_Treble',      $id_ScriptSettings, AM_VAL_TREBLE_DEFAULT, 'Speaker');
			$MiddleId       = CreateVariable(AM_VAR_MIDDLE,      1 /*Integer*/, $RoomInstanceId,  80, 'AudioMax_Middle',      $id_ScriptSettings, AM_VAL_MIDDLE_DEFAULT, 'Speaker');
			$BassId         = CreateVariable(AM_VAR_BASS,        1 /*Integer*/, $RoomInstanceId,  90, 'AudioMax_Bass',        $id_ScriptSettings, AM_VAL_BASS_DEFAULT, 'Speaker');
		}

		SetValue($id_RoomIds, implode(',',$RoomIds));
		SetValue($id_Port,      $id_IOComPort);
		SetValue($id_RoomCount, AM_CONFIG_ROOM_COUNT);
	}


	/** @}*/
?>