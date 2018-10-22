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

	 /**@defgroup audiomax_protocol AudioMax Kommunikations Protokoll
	 * @ingroup audiomax
	 * @{
	 *
	 * Prinzipieller Aufbau der Kommunikation:
	 *   CommandType "SVR" Command [Room] [Audio] Value
	 *
	 * @page audiomax_protocol_types Übersicht über die verschiedenen Befehlstypen
	 * Grundsätzlich gibt es vier verschiedene Befehlsarten
	 * - SET:  Setzen von Werten im AudioMax
	 * - GET:  Abfrage von Serverwerten
	 * - EVT:  Nachrichten die vom AudioMax selbsttätig gesendet werden
	 *
	 * @page audiomax_protocol_commands Befehlsaufbau
	 * -  Alle Befehle können in Groß- oder Kleinbuchstaben geschrieben werden.
	 * -  Die einzelnen Befehlsteile sind durch ein Semikolon voneinander getrennt.
	 * -  Alle Befehle werden mit „Carriage Return“ („CR“, Dezimal 13) beendet
	 * -  Das System gibt auf jeden Befehl eine Bestätigung 
	 * -  Der AudioServer sendet alle 60 Sekunden eine „Keep Alive“ Meldung
	 * -  Der AudioMax Server erwartet alle 60 Sekunden eine „Keep Alive“ Meldung vom Kommunikations Partner.
	 * -  Es können Texte auf dem LCD Display ausgegeben werden (Geräte mit Display)
	 *
	 * @page audiomax_protocol_set SET Kommando
	 * Werte an AudioMax Server senden:
	 * - SET SVR AUD [RAUMNUMMER] [AUDIO] [VALUE]    
	 *                             + VOL = Volume (40–0, 40 = Mute)
	 *                             + INP = Input Analog Signal (0-3)
	 *                             + GAI = Gain, Verstärkung  (0-15, 0 = 0dB Verstärkung)
	 *                             + BAL = Balance (0-15, 15 = Rechts)
	 *                             + BAS = Bass (0-15, 15 = +14dB)
	 *                             + MID = Middle (0-15, 15 = +14dB)
	 *                             + TRE = Treble (0-15, 15 = +14dB)
	 * - SET SVR ROO [RAUMNUMMER] [VALUE]      Steuerung Raumverstärker (0 – 3)
	 * - SET SVR PWR [STATUS]                  AudioMax Server Ein/Aus, (1=On 0=Off)
	 * - SET SVR TEX [TEXT1] [TEXT2] [TEXT3]   Textausgabe auf LCD Display, max 20 Zeichen pro Zeile, TEXT1 = 2.Zeile, TEXT2 = 3.Zeile, TEXT3 = 4.Zeile
	 * - SET SVR KAL 0                         “Keep Alive” Signal an PC
	 * - SET SVR MOD [MODE] [VALUE]            Setzen von Betriebsarten (0 = Acknowledge, 1 = Debug On/Off, 2 = Button Room Amp, 3 = KAL von PC)
	 *
	 * @page audiomax_protocol_get GET Kommando
	 * Werte von AudioMax Server abholen
	 * - GET SVR AUD [RAUMNUMMER] [AUDIO]     Werte der Audioeinstellungen
	 * - GET SVR ROO [RAUMNUMMER]             Status der Raumverstärker
	 * - GET SVR PWR                          Status des Servers
	 * - GET SVR VER                          Ausgabe der Firmwareversion
	 * - GET SVR MOD [TYP]                    Ausgabe AudioMax Version (S404, S408, ..)
	 * - GET SVR HAR                          Hardware Version, Herstelljahr
	 *
	 * @page audiomax_protocol_evt EVT Kommando
	 * AudioMax Server sendet selbständig Statusmeldungen
	 * - EVT SRV [COMMAND] [ROOM] [AUDIO] [VALUE]    Statusmeldungen nach Power On
	 * - EVT SVR KAL [STATUS]                        „Keep Alive“ Meldung an PC, 0=Standby, 1=Server On
	 * - EVT SVR ROO [ROOM] 1                        Taste des Raumverstärkers [NUMMER] betätigt (1 = betätigt)
	 *
	 * @page audiomax_protocol_mod MOD Kommando
	 * Mit diesen Befehlen können verschiedene Betriebsarten für den AudioServer gesetzt werden
	 * SET SRV MOD [MODE] [STATUS]
	 * - MODE 0    Acknowledge (Default = 0, 0= Acknowledge 0 bis 5, 1 = Echo des Befehls)
	 * - MODE 1    Debug Ausgaben (Default = 0, 0 = keine Debug Ausgaben, 1 = Debug Ausgaben)
	 * - MODE 2    Tasterfunktion Raumverstärker (Default = 0, 0 = nur Meldung an PC, 1 = Der Raumverstärker wird direkt geschalten +  Meldung an PC)
	 * - MODE 3    KAL von PC aktiviert/deaktiviert (Default = 0, 0 = KAL von PC erwartet, 1 = keine KAL Meldung von PC erwartet)
	 *
	 * @page audiomax_protocol_error Acknowledge und Error Code
	 * Acknowledge:
	 *   Error Code:
	 *    "1"  => Error 1, Command Array 1, Unknown Command  => Fehler im 1. Befehlsteil
	 *    "2"  => Error 2, Command Array 2, Unknown Command => Fehler im 2. Befehlsteil
	 *    "3"  => Error 3, Command Array 3, Unknown Command => Fehler im 3. Befehlsteil
	 *    "4"  => Error 4, Command Array 4, Out Of Range  => Fehler im 4. Befehlsteil
	 *    "5"  => Error 5, Command Array 5, Out Of Range => Fehler im 5. Befehlsteil
	 *  Acknowledge Code:
	 *   MODE0 = 0
	 *    "0"  => Befehl erkannt
	 *
	 *   MODE0 = 1    Bei erkanntem Befehl wird der gesendete Befehl als Echo ausgegeben. 
	 *
	 */
	/** @}*/

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

	$WFC10_Enabled        = $moduleManager->GetConfigValue('Enabled', 'WFC10');
	$WFC10_ConfigId       = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
	$WFC10_Path           = $moduleManager->GetConfigValue('Path', 'WFC10');
	$WFC10_TabPaneItem    = $moduleManager->GetConfigValue('TabPaneItem', 'WFC10');
	$WFC10_TabPaneParent  = $moduleManager->GetConfigValue('TabPaneParent', 'WFC10');
	$WFC10_TabPaneName    = $moduleManager->GetConfigValue('TabPaneName', 'WFC10');
	$WFC10_TabPaneIcon    = $moduleManager->GetConfigValue('TabPaneIcon', 'WFC10');
	$WFC10_TabPaneOrder   = $moduleManager->GetConfigValueInt('TabPaneOrder', 'WFC10');
	$WFC10_TabItem        = $moduleManager->GetConfigValue('TabItem', 'WFC10');
	$WFC10_TabName        = $moduleManager->GetConfigValue('TabName', 'WFC10');
	$WFC10_TabIcon        = $moduleManager->GetConfigValue('TabIcon', 'WFC10');
	$WFC10_TabOrder       = $moduleManager->GetConfigValueInt('TabOrder', 'WFC10');

	$Mobile_Enabled       = $moduleManager->GetConfigValue('Enabled', 'Mobile');
	$Mobile_Path          = $moduleManager->GetConfigValue('Path', 'Mobile');
	$Mobile_PathOrder     = $moduleManager->GetConfigValueInt('PathOrder', 'Mobile');
	$Mobile_PathIcon      = $moduleManager->GetConfigValue('PathIcon', 'Mobile');
	$Mobile_Name          = $moduleManager->GetConfigValue('Name', 'Mobile');
	$Mobile_Order         = $moduleManager->GetConfigValueInt('Order', 'Mobile');
	$Mobile_Icon          = $moduleManager->GetConfigValue('Icon', 'Mobile');
	
	$IgnoreIOPortInstanceError    = $moduleManager->GetConfigValueBool('IgnoreIOPortInstanceError');
	$AudioMaxRoomInstallation     = $moduleManager->GetConfigValueBool('AudioMaxRoomInstallation');

	/* ---------------------------------------------------------------------- */
	/* AudioMax Installation                                                  */
	/* ---------------------------------------------------------------------- */

	function GetAudioMaxDeviceType() {
		if (defined('AM_CONFIG_DEVICE_TYPE')) {
			return AM_CONFIG_DEVICE_TYPE;
		}
		return AM_DEVICE_404;
	}
	
	function GetAudioMaxRoomCount() {
		if (GetAudioMaxDeviceType()==AM_DEVICE_404) {
			return 4;
		} else {
			return 6;
		}
	}
	
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');
	$CategoryIdHardware = CreateCategoryPath('Hardware.AudioMax');
	$roomCount          = GetAudioMaxRoomCount();
	$roomNames          = array(1=>AM_CONFIG_ROOMNAME1, 2=>AM_CONFIG_ROOMNAME2, 3=>AM_CONFIG_ROOMNAME3, 4=>AM_CONFIG_ROOMNAME4);
	if ($roomCount > 4)
	   $roomNames       = array(1=>AM_CONFIG_ROOMNAME1, 2=>AM_CONFIG_ROOMNAME2, 3=>AM_CONFIG_ROOMNAME3, 4=>AM_CONFIG_ROOMNAME4, 5=>AM_CONFIG_ROOMNAME5, 6=>AM_CONFIG_ROOMNAME6);

	$id_ScriptReceive         = IPS_GetScriptIDByName('AudioMax_Receive',        $CategoryIdApp);
	$id_ScriptSettings        = IPS_GetScriptIDByName('AudioMax_ChangeSettings', $CategoryIdApp);
	$id_ScriptKeepAlive       = IPS_GetScriptIDByName('AudioMax_KeepAlive',      $CategoryIdApp);

	if (AM_CONFIG_COM_PORT<>'') {
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
	CreateProfile_Switch ('AudioMax_KeepAliveStatus', 'KeepAlive Error', 'KeepAlive OK', "", 0xaa0000, 0x00ff00);
	CreateProfile_Switch ('AudioMax_Busy',            'Bereit', 'Aktiv', "", -1, 0x0000ff);
	CreateProfile_Switch ('AudioMax_Connection',      'Deaktiviert', 'Aktiv', "", 0xaa0000, 0x0000ff, 'LockOpen', 'LockClosed');

	$id_AudioMaxServerId = CreateDummyInstance("AudioMax_Server", $CategoryIdData, 10);
	$id_Power            = CreateVariable(AM_VAR_MAINPOWER,       0 /*Boolean*/, $id_AudioMaxServerId,  10, '~Switch',              $id_ScriptSettings, false, 'Power');
	$id_Busy             = CreateVariable(AM_VAR_BUSY,            0 /*Boolean*/, $id_AudioMaxServerId,  20, 'AudioMax_Busy',        null,               false, 'Distance');
	$id_Connection       = CreateVariable(AM_VAR_CONNECTION,      0 /*Boolean*/, $id_AudioMaxServerId,  30, 'AudioMax_Connection',  $id_ScriptSettings, true,  '');
	$id_LastError        = CreateVariable(AM_VAR_LASTERROR,       3 /*String*/,  $id_AudioMaxServerId,  40, '',              null,               '',    'Warning');
	$id_LastCommand      = CreateVariable(AM_VAR_LASTCOMMAND,     3 /*String*/,  $id_AudioMaxServerId,  50, '',              null,               '',    'Information');
	$id_InputBuffer      = CreateVariable(AM_VAR_INPUTBUFFER,     3 /*String*/,  $id_AudioMaxServerId,  60, '',              null,               '',    'Information');
	$id_KeepAliveCount   = CreateVariable(AM_VAR_KEEPALIVECOUNT,  1 /*Integer*/, $id_AudioMaxServerId,  70, '',                     null,               0, '');
	$id_KeepAliveStatus  = CreateVariable(AM_VAR_KEEPALIVESTATUS, 0 /*Boolean*/, $id_AudioMaxServerId,  80, 'AudioMax_KeepAliveStatus', null,           true,  'Repeat');
	$id_ModePowerRequest = CreateVariable(AM_VAR_MODEPOWERREQUEST,0 /*Boolean*/, $id_AudioMaxServerId,  90, '~Switch',               $id_ScriptSettings,true,  'Gear');
	$id_ModeServerDebug  = CreateVariable(AM_VAR_MODESERVERDEBUG, 0 /*Boolean*/, $id_AudioMaxServerId, 100, '~Switch',               $id_ScriptSettings,true,  'Gear');
	$id_ModeEmulateState = CreateVariable(AM_VAR_MODEEMULATESTATE,0 /*Boolean*/, $id_AudioMaxServerId, 110, '~Switch',               $id_ScriptSettings,true,  'Gear');
	$id_ModeAcknowledge  = CreateVariable(AM_VAR_MODEACKNOWLEDGE, 0 /*Boolean*/, $id_AudioMaxServerId, 120, '~Switch',               $id_ScriptSettings,true,  'Gear');
	$id_Port             = CreateVariable(AM_VAR_PORTID,          1 /*Integer*/, $id_AudioMaxServerId, 300, '',                      null,              0,     '');
	$id_RoomIds          = CreateVariable(AM_VAR_ROOMIDS,         3 /*String*/,  $id_AudioMaxServerId, 310, '',                      null,              '',    '');
	$id_RoomCount        = CreateVariable(AM_VAR_ROOMCOUNT,       1 /*Integer*/, $id_AudioMaxServerId, 320, '',                      null,              0,     '');

	$id_KeepAliveFlag    = @IPS_GetObjectIDByIdent('KEEP_ALIVE_FLAG', $id_AudioMaxServerId);
	if ($id_KeepAliveFlag!==false) {
		IPS_DeleteVariable($id_KeepAliveFlag);
	}
	
	if ($AudioMaxRoomInstallation) {
		$RoomIds = array();
		for ($RoomId=1;$RoomId<=$roomCount;$RoomId++) {
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
		SetValue($id_RoomCount, $roomCount);
		if (AM_CONFIG_COM_PORT<>'') {
			SetValue($id_Port,      $id_IOComPort);
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$categoryIdWebFront         = CreateCategoryPath($WFC10_Path);
		EmptyCategory($categoryIdWebFront);
		$categoryIdWebFrontLeft   = CreateCategory('Left',   $categoryIdWebFront, 100);
		$categoryIdWebFrontRight  = CreateCategory('Right', $categoryIdWebFront, 200);

		$instanceIdServer  = CreateDummyInstance('AudioMax Server', $categoryIdWebFrontLeft, 10);
		CreateLink('Power',                $id_Power,            $instanceIdServer, 10);
		CreateLink('Verbindung',           $id_Connection,       $instanceIdServer, 20);
		CreateLink('Gerät Aktiv',          $id_Busy,             $instanceIdServer, 30);
		CreateLink('Eingangs Buffer',      $id_InputBuffer,      $instanceIdServer, 40);
		CreateLink('Letzter Befehl',       $id_LastCommand,      $instanceIdServer, 50);
		CreateLink('Letzter Fehler',       $id_LastError,        $instanceIdServer, 60);
		CreateLink('"KeepAlive" Status',   $id_KeepAliveStatus,  $instanceIdServer, 80);
		CreateLink('Acknowledge Modus',    $id_ModeAcknowledge,  $instanceIdServer, 90);
		CreateLink('EmulateState Modus',   $id_ModeEmulateState, $instanceIdServer, 100);
		CreateLink('Debug Modus',          $id_ModeServerDebug,  $instanceIdServer, 110);
		CreateLink('PowerRequest Modus',   $id_ModePowerRequest, $instanceIdServer, 120);

		if ($AudioMaxRoomInstallation) {
			for ($roomId=1;$roomId<=$roomCount;$roomId++) {
				$roomCategoryId = CreateCategory('AudioMax'.$roomId, $categoryIdWebFrontRight, 10*$roomId);
				$roomInstanceId = IPS_GetObjectIdByIdent("AudioMax_Room".$roomId, $CategoryIdData);

				CreateLink('AudioMax'.$roomId. ' ('.$roomNames[$roomId].')', IPS_GetObjectIDByIdent(AM_VAR_ROOMPOWER, $roomInstanceId),   $categoryIdWebFrontRight, $roomId);

				CreateLink('Power',                IPS_GetObjectIDByIdent(AM_VAR_ROOMPOWER,   $roomInstanceId),   $roomCategoryId, 10);
				CreateLink('Eingang',              IPS_GetObjectIDByIdent(AM_VAR_INPUTSELECT, $roomInstanceId),   $roomCategoryId, 20);
				CreateLink('Verstärkung',          IPS_GetObjectIDByIdent(AM_VAR_INPUTGAIN,   $roomInstanceId),   $roomCategoryId, 30);
				CreateLink('Lautstärke',           IPS_GetObjectIDByIdent(AM_VAR_VOLUME,      $roomInstanceId),   $roomCategoryId, 40);
				CreateLink('Muting',               IPS_GetObjectIDByIdent(AM_VAR_MUTE,        $roomInstanceId),   $roomCategoryId, 50);
				CreateLink('Balance',              IPS_GetObjectIDByIdent(AM_VAR_BALANCE,     $roomInstanceId),   $roomCategoryId, 60);
				CreateLink('Höhen',                IPS_GetObjectIDByIdent(AM_VAR_TREBLE,      $roomInstanceId),   $roomCategoryId, 70);
				CreateLink('Mitten',               IPS_GetObjectIDByIdent(AM_VAR_MIDDLE,      $roomInstanceId),   $roomCategoryId, 80);
				CreateLink('Bass',                 IPS_GetObjectIDByIdent(AM_VAR_BASS,        $roomInstanceId),   $roomCategoryId, 90);
			}
		}
		$instanceIdPlayer = IPSUtil_ObjectIDByPath('Hardware.NetPlayer.MediaPlayer', true);
		if ($instanceIdPlayer!==false) {
			CreateLink('MediaPlayer', IPS_GetObjectIDByName('Status', $instanceIdPlayer),   $categoryIdWebFrontRight, 100);

			$categoryIdPlayer = CreateCategory('MediaPlayer', $categoryIdWebFrontRight, 100);
			CreateLink('Lautstärke',   IPS_GetObjectIDByName('Lautstärke',   $instanceIdPlayer),   $categoryIdPlayer, 10);
			CreateLink('Status',       IPS_GetObjectIDByName('Status',       $instanceIdPlayer),   $categoryIdPlayer, 20);
			CreateLink('Titel',        IPS_GetObjectIDByName('Titel',        $instanceIdPlayer),   $categoryIdPlayer, 30);
			CreateLink('Titeldatei',   IPS_GetObjectIDByName('Titeldatei',   $instanceIdPlayer),   $categoryIdPlayer, 40);
			CreateLink('Titellänge',   IPS_GetObjectIDByName('Titellänge',   $instanceIdPlayer),   $categoryIdPlayer, 50);
			CreateLink('Titelposition',IPS_GetObjectIDByName('Titelposition',$instanceIdPlayer),   $categoryIdPlayer, 60);
			CreateLink('Wiederholen',  IPS_GetObjectIDByName('Wiederholen',  $instanceIdPlayer),   $categoryIdPlayer, 70);
			CreateLink('Zufall',       IPS_GetObjectIDByName('Zufall',       $instanceIdPlayer),   $categoryIdPlayer, 80);
			CreateLink('Lautstärke',   IPS_GetObjectIDByName('Lautstärke',   $instanceIdPlayer),   $categoryIdPlayer, 90);
			CreateLink('Lautstärke',   IPS_GetObjectIDByName('Lautstärke',   $instanceIdPlayer),   $categoryIdPlayer, 100);
		}
		
		$tabItem = $WFC10_TabPaneItem.$WFC10_TabItem;
		DeleteWFCItems($WFC10_ConfigId, $tabItem);
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem, $WFC10_TabPaneParent,  $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $tabItem,           $WFC10_TabPaneItem,    $WFC10_TabOrder,     $WFC10_TabName,     $WFC10_TabIcon, 1 /*Vertical*/, 40 /*Width*/, 0 /*Target=Pane1*/, 0/*UsePerc*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $tabItem.'_Left',   $tabItem,   10, '', '', $categoryIdWebFrontLeft   /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $tabItem.'_Right',  $tabItem,   20, '', '', $categoryIdWebFrontRight   /*BaseId*/, 'true' /*BarBottomVisible*/);

		ReloadAllWebFronts();
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled ) {
		$mobileId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		$mobileId  = CreateCategoryPath($Mobile_Path.'.'.$Mobile_Name, $Mobile_Order, $Mobile_Icon);
		EmptyCategory($mobileId);

		$instanceIdServer = CreateCategory('AudioMax Server', $mobileId, 0);
		CreateLink('Power',                $id_Power,            $instanceIdServer, 10);
		CreateLink('Verbindung',           $id_Connection,       $instanceIdServer, 20);
		CreateLink('Gerät Aktiv',          $id_Busy,             $instanceIdServer, 30);
		CreateLink('Eingangs Buffer',      $id_InputBuffer,      $instanceIdServer, 40);
		CreateLink('Letzter Befehl',       $id_LastCommand,      $instanceIdServer, 50);
		CreateLink('Letzter Fehler',       $id_LastError,        $instanceIdServer, 60);
		CreateLink('"KeepAlive" Status',   $id_KeepAliveStatus,  $instanceIdServer, 80);
		CreateLink('Acknowledge Modus',    $id_ModeAcknowledge,  $instanceIdServer, 90);
		CreateLink('EmulateState Modus',   $id_ModeEmulateState, $instanceIdServer, 100);
		CreateLink('Debug Modus',          $id_ModeServerDebug,  $instanceIdServer, 110);
		CreateLink('PowerRequest Modus',   $id_ModePowerRequest, $instanceIdServer, 120);

		CreateLink('AudioMax Server',   $id_Power,            $mobileId, 0);
		if ($AudioMaxRoomInstallation) {
			for ($roomId=1;$roomId<=$roomCount;$roomId++) {
				$roomCategoryId = CreateCategory('AudioMax'.$roomId. ' ('.$roomNames[$roomId].')', $mobileId, 10*$roomId);
				$roomInstanceId = IPS_GetObjectIdByIdent("AudioMax_Room".$roomId, $CategoryIdData);

				CreateLink('AudioMax'.$roomId. ' ('.$roomNames[$roomId].')', IPS_GetObjectIDByIdent(AM_VAR_ROOMPOWER, $roomInstanceId),   $mobileId, $roomId);

				CreateLink('Power',                IPS_GetObjectIDByIdent(AM_VAR_ROOMPOWER,   $roomInstanceId),   $roomCategoryId, 10);
				CreateLink('Eingang',              IPS_GetObjectIDByIdent(AM_VAR_INPUTSELECT, $roomInstanceId),   $roomCategoryId, 20);
				CreateLink('Verstärkung',          IPS_GetObjectIDByIdent(AM_VAR_INPUTGAIN,   $roomInstanceId),   $roomCategoryId, 30);
				CreateLink('Lautstärke',           IPS_GetObjectIDByIdent(AM_VAR_VOLUME,      $roomInstanceId),   $roomCategoryId, 40);
				CreateLink('Muting',               IPS_GetObjectIDByIdent(AM_VAR_MUTE,        $roomInstanceId),   $roomCategoryId, 50);
				CreateLink('Balance',              IPS_GetObjectIDByIdent(AM_VAR_BALANCE,     $roomInstanceId),   $roomCategoryId, 60);
				CreateLink('Höhen',                IPS_GetObjectIDByIdent(AM_VAR_TREBLE,      $roomInstanceId),   $roomCategoryId, 70);
				CreateLink('Mitten',               IPS_GetObjectIDByIdent(AM_VAR_MIDDLE,      $roomInstanceId),   $roomCategoryId, 80);
				CreateLink('Bass',                 IPS_GetObjectIDByIdent(AM_VAR_BASS,        $roomInstanceId),   $roomCategoryId, 90);
			}
		}
		$instanceIdPlayer = IPSUtil_ObjectIDByPath('Hardware.NetPlayer.MediaPlayer', true);
		if ($instanceIdPlayer!==false) {
			CreateLink('MediaPlayer', IPS_GetObjectIDByName('Status', $instanceIdPlayer),   $mobileId, 100);

			$categoryIdPlayer = CreateCategory('MediaPlayer', $mobileId, 100);
			CreateLink('Lautstärke',   IPS_GetObjectIDByName('Lautstärke',   $instanceIdPlayer),   $categoryIdPlayer, 10);
			CreateLink('Status',       IPS_GetObjectIDByName('Status',       $instanceIdPlayer),   $categoryIdPlayer, 20);
			CreateLink('Titel',        IPS_GetObjectIDByName('Titel',        $instanceIdPlayer),   $categoryIdPlayer, 30);
			CreateLink('Titeldatei',   IPS_GetObjectIDByName('Titeldatei',   $instanceIdPlayer),   $categoryIdPlayer, 40);
			CreateLink('Titellänge',   IPS_GetObjectIDByName('Titellänge',   $instanceIdPlayer),   $categoryIdPlayer, 50);
			CreateLink('Titelposition',IPS_GetObjectIDByName('Titelposition',$instanceIdPlayer),   $categoryIdPlayer, 60);
			CreateLink('Wiederholen',  IPS_GetObjectIDByName('Wiederholen',  $instanceIdPlayer),   $categoryIdPlayer, 70);
			CreateLink('Zufall',       IPS_GetObjectIDByName('Zufall',       $instanceIdPlayer),   $categoryIdPlayer, 80);
			CreateLink('Lautstärke',   IPS_GetObjectIDByName('Lautstärke',   $instanceIdPlayer),   $categoryIdPlayer, 90);
			CreateLink('Lautstärke',   IPS_GetObjectIDByName('Lautstärke',   $instanceIdPlayer),   $categoryIdPlayer, 100);
		}
	}

	/** @}*/
?>