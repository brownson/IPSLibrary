<?
	/**@defgroup entertainment_configuration Entertainment Konfiguration
	 * @ingroup entertainment
	 * @{
	 *
	 * Konfigurations File der Entertainment Steuerung.
	 *
	 * Hier werden folgende Komponenten definiert:
	 * - Kommunikations Schnittstellen
	 * - Räume
	 * - Geräte
	 * - Geräte Zuordnungen
	 *
	 * @file          Entertainment_AudioMaxConfiguration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
    */

	IPSUtils_Include ("AudioMax.inc.php",        "IPSLibrary::app::hardware::AudioMax");
	IPSUtils_Include ("Entertainment_Constants.inc.php",   "IPSLibrary::app::modules::Entertainment");
	IPSUtils_Include ("NetPlayer_Constants.inc.php",       "IPSLibrary::app::modules::NetPlayer");
	IPSUtils_Include ("NetPlayer_Configuration.inc.php",   "IPSLibrary::config::modules::NetPlayer");

	define ("c_Comm_WinLIRC",							"WinLIRC");
	define ("c_Comm_Onkyo",								"Onkyo");
	define ("c_Comm_NetPlayer",						"NetPlayer");
	define ("c_Comm_AudioMax",							"AudioMax");

	define ("c_Room_1",									"Raum 1");
	define ("c_Room_2",									"Raum 2");
	define ("c_Room_3",									"Raum 3");
	define ("c_Room_4",									"Raum 4");

	define ("c_Device_NetPlayer",						"NetPlayer");
	define ("c_Device_AudioMax1",						"AudioMax1");
	define ("c_Device_AudioMax2",						"AudioMax2");
	define ("c_Device_AudioMax3",						"AudioMax3");
	define ("c_Device_AudioMax4",						"AudioMax4");

	// ========================================================================================================================
	// Defintion of Communication Data
	// ========================================================================================================================
	function get_CommunicationConfiguration () {
	   return array (
			c_Comm_WinLIRC => array (
				c_Property_ScriptSnd 			=> 'Entertainment_InterfaceWinLIRCSnd.inc.php',
				c_Property_ScriptRcv  			=> 'Entertainment_InterfaceWinLIRCRcv.ips.php',
				c_Property_FunctionSnd 			=> 'WinLIRC_SendData',
				c_Property_Instance     		=> null,
				c_Property_MessageTypes       => array(),
				c_Property_InpTranslationList	=> array(),
				c_Property_OutTranslationList => array(),
			),
			c_Comm_Onkyo => array (
				c_Property_ScriptSnd 			=> 'Entertainment_InterfaceOnkyoSnd.inc.php',
				c_Property_ScriptRcv  			=> 'Entertainment_InterfaceOnkyoRcv.ips.php',
				c_Property_FunctionSnd 			=> 'Onkyo_SendData',
				c_Property_Register     		=> null,
				c_Property_Instance     		=> null,
				c_Property_IPAddress          => '192.168.0.12',
				c_Property_Timeout				=> 30,
			),
			c_Comm_NetPlayer => array (
				c_Property_ScriptSnd 			=> 'Entertainment_InterfaceNetPlayerSnd.inc.php',
				c_Property_ScriptRcv 			=> 'Entertainment_InterfaceNetPlayerRcv.ips.php',
				c_Property_FunctionSnd 			=> 'Entertainment_NetPlayer_SendData',
				c_Property_Instance     		=> 'Program.IPSLibrary.data.modules.NetPlayer',
				c_Property_Variables     		=> 'MobileControl,RemoteControl,ControlType,Power',
			),
			c_Comm_AudioMax => array (
				c_Property_ScriptSnd 			=> 'Entertainment_InterfaceIPSComponentAVControl.inc.php',
				//c_Property_ScriptRcv  			=> 'Entertainment_InterfaceIPSComponentAVControl.inc.php',
				c_Property_FunctionSnd 			=> 'Entertainment_IPSComponent_SendData',
				c_Property_ComponentParams    => '',
			),
		);
	}

	// ========================================================================================================================
	// Defintion of Room Configuration
	// ========================================================================================================================
	function get_RoomConfiguration () {
	   return array (
	      // -------------------------------------------------------------------------------------------------------
			c_Room_1 => array(
				c_Property_Name 		=> AM_CONFIG_ROOMNAME1,
				c_Control_RoomPower 		=> array(c_Property_Name 	=> 'Power'),
				c_Control_Muting 			=> array(c_Property_Name 	=> 'Mute'),
				c_Control_Volume 			=> array(c_Property_Name 	=> 'Lautstärke'),
				c_Control_Group			=> array(c_Property_Name 	=> 'Klangeinstellungen', c_Property_Icon => 'Gear'),
				c_Control_Balance			=> array(c_Property_Name 	=> 'Balance',	c_Property_Group => 'Klangeinstellungen'),
				c_Control_Treble			=> array(c_Property_Name 	=> 'Höhen',		c_Property_Group => 'Klangeinstellungen'),
				c_Control_Middle			=> array(c_Property_Name 	=> 'Mitten',	c_Property_Group => 'Klangeinstellungen'),
				c_Control_Bass				=> array(c_Property_Name 	=> 'Bass',		c_Property_Group => 'Klangeinstellungen'),
				c_Control_Source 			=> array(c_Property_Name 	=> 'Eingang'),
				c_Control_RemoteSource 	=> array(c_Property_Name 	=> 'Fernbedienung'),
				c_Control_iRemoteSource => array(c_Property_Name 	=> 'iFernbedienung'),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Room_2 => array(
				c_Property_Name 		=> AM_CONFIG_ROOMNAME2,
				c_Control_RoomPower 		=> array(c_Property_Name 	=> 'Power'),
				c_Control_Muting 			=> array(c_Property_Name 	=> 'Mute'),
				c_Control_Volume 			=> array(c_Property_Name 	=> 'Lautstärke'),
				c_Control_Group			=> array(c_Property_Name 	=> 'Klangeinstellungen', c_Property_Icon => 'Gear'),
				c_Control_Balance			=> array(c_Property_Name 	=> 'Balance',	c_Property_Group => 'Klangeinstellungen'),
				c_Control_Treble			=> array(c_Property_Name 	=> 'Höhen',		c_Property_Group => 'Klangeinstellungen'),
				c_Control_Middle			=> array(c_Property_Name 	=> 'Mitten',	c_Property_Group => 'Klangeinstellungen'),
				c_Control_Bass				=> array(c_Property_Name 	=> 'Bass',		c_Property_Group => 'Klangeinstellungen'),
				c_Control_Source 			=> array(c_Property_Name 	=> 'Eingang'),
				c_Control_RemoteSource 	=> array(c_Property_Name 	=> 'Fernbedienung'),
				c_Control_iRemoteSource => array(c_Property_Name 	=> 'iFernbedienung'),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Room_3 => array(
				c_Property_Name 		=> AM_CONFIG_ROOMNAME3,
				c_Control_RoomPower 		=> array(c_Property_Name 	=> 'Power'),
				c_Control_Muting 			=> array(c_Property_Name 	=> 'Mute'),
				c_Control_Volume 			=> array(c_Property_Name 	=> 'Lautstärke'),
				c_Control_Group			=> array(c_Property_Name 	=> 'Klangeinstellungen', c_Property_Icon => 'Gear'),
				c_Control_Balance			=> array(c_Property_Name 	=> 'Balance',	c_Property_Group => 'Klangeinstellungen'),
				c_Control_Treble			=> array(c_Property_Name 	=> 'Höhen',		c_Property_Group => 'Klangeinstellungen'),
				c_Control_Middle			=> array(c_Property_Name 	=> 'Mitten',	c_Property_Group => 'Klangeinstellungen'),
				c_Control_Bass				=> array(c_Property_Name 	=> 'Bass',		c_Property_Group => 'Klangeinstellungen'),
				c_Control_Source 			=> array(c_Property_Name 	=> 'Eingang'),
				c_Control_RemoteSource 	=> array(c_Property_Name 	=> 'Fernbedienung'),
				c_Control_iRemoteSource => array(c_Property_Name 	=> 'iFernbedienung'),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Room_4 => array(
				c_Property_Name 		=> AM_CONFIG_ROOMNAME4,
				c_Control_RoomPower 		=> array(c_Property_Name 	=> 'Power'),
				c_Control_Muting 			=> array(c_Property_Name 	=> 'Mute'),
				c_Control_Volume 			=> array(c_Property_Name 	=> 'Lautstärke'),
				c_Control_Group			=> array(c_Property_Name 	=> 'Klangeinstellungen', c_Property_Icon => 'Gear'),
				c_Control_Balance			=> array(c_Property_Name 	=> 'Balance',	c_Property_Group => 'Klangeinstellungen'),
				c_Control_Treble			=> array(c_Property_Name 	=> 'Höhen',		c_Property_Group => 'Klangeinstellungen'),
				c_Control_Middle			=> array(c_Property_Name 	=> 'Mitten',	c_Property_Group => 'Klangeinstellungen'),
				c_Control_Bass				=> array(c_Property_Name 	=> 'Bass',		c_Property_Group => 'Klangeinstellungen'),
				c_Control_Source 			=> array(c_Property_Name 	=> 'Eingang'),
				c_Control_RemoteSource 	=> array(c_Property_Name 	=> 'Fernbedienung'),
				c_Control_iRemoteSource => array(c_Property_Name 	=> 'iFernbedienung'),
			),
	      // -------------------------------------------------------------------------------------------------------
		);
	}

	// ========================================================================================================================
	// Defintion of Device Configuration
	// ========================================================================================================================
	function get_DeviceConfiguration () {
		return array (
	      // -------------------------------------------------------------------------------------------------------
			c_Device_AudioMax1 	=> array(
				c_Property_Name 			=> 'AudioMax 1',
				c_Control_DevicePower 	=> array(
					c_Property_Name 			=> 'Power',
					c_Property_CommPowerOn	=> array(c_Comm_AudioMax, 'SetPower', '0', true),
					c_Property_CommPowerOff	=> array(c_Comm_AudioMax, 'SetPower', '0', false),
				),
				c_Control_Muting 			=> array(
					c_Property_Name 			=> 'Mute',
					c_Property_CommMuteOn 	=> array(c_Comm_AudioMax, 'SetMute', '0', true),
					c_Property_CommMuteOff 	=> array(c_Comm_AudioMax, 'SetMute', '0', false),
				),
				c_Control_Volume 			=> array(
					c_Property_Name 			=> 'Volume', c_Property_MinValue => 0, c_Property_MaxValue => 100, c_Property_Limit => 80,
					c_Property_CommVol 		=> array(c_Comm_AudioMax, 'SetVolume', '0', c_Template_Value),
				),
				c_Control_Balance 		=> array(
					c_Property_Name 			=> 'Balance', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommBal 		=> array(c_Comm_AudioMax, 'SetBalance', '0', c_Template_Value),
				),
				c_Control_Treble 			=> array(
					c_Property_Name 			=> 'Höhen', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommTre 		=> array(c_Comm_AudioMax, 'SetTreble', '0', c_Template_Value),
				),
				c_Control_Middle 			=> array(
					c_Property_Name 			=> 'Mitten', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommMid 		=> array(c_Comm_AudioMax, 'SetMiddle', '0', c_Template_Value),
				),
				c_Control_Bass 			=> array(
					c_Property_Name 			=> 'Bass', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommBas 		=> array(c_Comm_AudioMax, 'SetBass', '0', c_Template_Value),
				),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Device_AudioMax2	=> array(
				c_Property_Name 			=> 'AudioMax 2',
				c_Control_DevicePower 	=> array(
					c_Property_Name 			=> 'Power',
					c_Property_CommPowerOn	=> array(c_Comm_AudioMax, 'SetPower', '1', true),
					c_Property_CommPowerOff	=> array(c_Comm_AudioMax, 'SetPower', '1', false),
				),
				c_Control_Muting 			=> array(
					c_Property_Name 			=> 'Mute',
					c_Property_CommMuteOn 	=> array(c_Comm_AudioMax, 'SetMute', '1', true),
					c_Property_CommMuteOff 	=> array(c_Comm_AudioMax, 'SetMute', '1', false),
				),
				c_Control_Volume 			=> array(
					c_Property_Name 			=> 'Volume', c_Property_MinValue => 0, c_Property_MaxValue => 100, c_Property_Limit => 80,
					c_Property_CommVol 		=> array(c_Comm_AudioMax, 'SetVolume', '1', c_Template_Value),
				),
				c_Control_Balance 		=> array(
					c_Property_Name 			=> 'Balance', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommBal 		=> array(c_Comm_AudioMax, 'SetBalance', '1', c_Template_Value),
				),
				c_Control_Treble 			=> array(
					c_Property_Name 			=> 'Höhen', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommTre 		=> array(c_Comm_AudioMax, 'SetTreble', '1', c_Template_Value),
				),
				c_Control_Middle 			=> array(
					c_Property_Name 			=> 'Mitten', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommMid 		=> array(c_Comm_AudioMax, 'SetMiddle', '1', c_Template_Value),
				),
				c_Control_Bass 			=> array(
					c_Property_Name 			=> 'Bass', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommBas 		=> array(c_Comm_AudioMax, 'SetBass', '1', c_Template_Value),
				),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Device_AudioMax3 	=> array(
				c_Property_Name 			=> 'AudioMax 3',
				c_Control_DevicePower 	=> array(
					c_Property_Name 			=> 'Power',
					c_Property_CommPowerOn	=> array(c_Comm_AudioMax, 'SetPower', '2', true),
					c_Property_CommPowerOff	=> array(c_Comm_AudioMax, 'SetPower', '2', false),
				),
				c_Control_Muting 			=> array(
					c_Property_Name 			=> 'Mute',
					c_Property_CommMuteOn 	=> array(c_Comm_AudioMax, 'SetMute', '2', true),
					c_Property_CommMuteOff 	=> array(c_Comm_AudioMax, 'SetMute', '2', false),
				),
				c_Control_Volume 			=> array(
					c_Property_Name 			=> 'Volume', c_Property_MinValue => 0, c_Property_MaxValue => 100, c_Property_Limit => 80,
					c_Property_CommVol 		=> array(c_Comm_AudioMax, 'SetVolume', '2', c_Template_Value),
				),
				c_Control_Balance 		=> array(
					c_Property_Name 			=> 'Balance', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommBal 		=> array(c_Comm_AudioMax, 'SetBalance', '2', c_Template_Value),
				),
				c_Control_Treble 			=> array(
					c_Property_Name 			=> 'Höhen', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommTre 		=> array(c_Comm_AudioMax, 'SetTreble', '2', c_Template_Value),
				),
				c_Control_Middle 			=> array(
					c_Property_Name 			=> 'Mitten', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommMid 		=> array(c_Comm_AudioMax, 'SetMiddle', '2', c_Template_Value),
				),
				c_Control_Bass 			=> array(
					c_Property_Name 			=> 'Bass', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommBas 		=> array(c_Comm_AudioMax, 'SetBass', '2', c_Template_Value),
				),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Device_AudioMax4 	=> array(
				c_Property_Name 			=> 'AudioMax 4',
				c_Control_DevicePower 	=> array(
					c_Property_Name 			=> 'Power',
					c_Property_CommPowerOn	=> array(c_Comm_AudioMax, 'SetPower', '3', true),
					c_Property_CommPowerOff	=> array(c_Comm_AudioMax, 'SetPower', '3', false),
				),
				c_Control_Muting 			=> array(
					c_Property_Name 			=> 'Mute',
					c_Property_CommMuteOn 	=> array(c_Comm_AudioMax, 'SetMute', '3', true),
					c_Property_CommMuteOff 	=> array(c_Comm_AudioMax, 'SetMute', '3', false),
				),
				c_Control_Volume 			=> array(
					c_Property_Name 			=> 'Volume', c_Property_MinValue => 0, c_Property_MaxValue => 100, c_Property_Limit => 80,
					c_Property_CommVol 		=> array(c_Comm_AudioMax, 'SetVolume', '3', c_Template_Value),
				),
				c_Control_Balance 		=> array(
					c_Property_Name 			=> 'Balance', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommBal 		=> array(c_Comm_AudioMax, 'SetBalance', '3', c_Template_Value),
				),
				c_Control_Treble 			=> array(
					c_Property_Name 			=> 'Höhen', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommTre 		=> array(c_Comm_AudioMax, 'SetTreble', '3', c_Template_Value),
				),
				c_Control_Middle 			=> array(
					c_Property_Name 			=> 'Mitten', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommMid 		=> array(c_Comm_AudioMax, 'SetMiddle', '3', c_Template_Value),
				),
				c_Control_Bass 			=> array(
					c_Property_Name 			=> 'Bass', c_Property_MinValue => 0, c_Property_MaxValue => 100,
					c_Property_CommBas 		=> array(c_Comm_AudioMax, 'SetBass', '3', c_Template_Value),
				),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Device_NetPlayer 		=> array(
				c_Property_Name 			=> 'NetPlayer',
				c_Control_DevicePower 	=> array(
					c_Property_Name 			=> 'Power',
					c_Property_CommPowerOn		=> array(c_Comm_NetPlayer, 'netplayer', 'poweron'),
					c_Property_CommPowerOff		=> array(c_Comm_NetPlayer, 'netplayer', 'poweroff'),
				),
				c_Control_RemoteSource 	=> array(
					c_Property_Name 			=> 'Source Control',
					c_Property_CommRemSrc	=> array(c_Comm_NetPlayer, 'netplayertype', c_Template_Value),
					c_Property_Names       	=> array(NP_RC_MP3CONTROL,
					                                 NP_RC_MP3SELECTION,
					                                 NP_RC_RADIOCONTROL),
				),
				c_Control_iRemoteSource 	=> array(
					c_Property_Name 			=> 'Mobile Control',
					c_Property_Names       	=> array(NP_RC_MOBILE),
				),
				c_Control_RemoteSourceType => array(
					c_Property_Name 			=> 'Source Control Type',
				),
			),
	      // -------------------------------------------------------------------------------------------------------
		);
	}

	// ========================================================================================================================
	// Defintion of Source Configuration
	// ========================================================================================================================
	function get_SourceConfiguration() {
	   return array (
	      // -------------------------------------------------------------------------------------------------------
			c_Room_1 => array(
				0 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME1,
					c_Property_Input => 		array(c_Property_Device 	=> c_Device_NetPlayer),
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax1,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '0', '0')),
				),
				1 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME2,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax1,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '0', '1')),
				),
				2 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME3,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax1,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '0', '2')),
				),
				3 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME4,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax1,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '0', '3')),
				),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Room_2 => array(
				0 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME1,
					c_Property_Input => 		array(c_Property_Device 	=> c_Device_NetPlayer),
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax2,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '1', '0')),
				),
				1 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME2,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax2,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '1', '1')),
				),
				2 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME3,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax2,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '1', '2')),
				),
				3 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME4,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax2,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '1', '3')),
				),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Room_3 => array(
				0 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME1,
					c_Property_Input => 		array(c_Property_Device 	=> c_Device_NetPlayer),
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax3,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '2', '0')),
				),
				1 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME2,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax3,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '2', '1')),
				),
				2 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME3,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax3,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '2', '2')),
				),
				3 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME4,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax3,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '2', '3')),
				),
			),
	      // -------------------------------------------------------------------------------------------------------
			c_Room_4 => array(
				0 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME1,
					c_Property_Input => 		array(c_Property_Device 	=> c_Device_NetPlayer),
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax4,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '3', '0')),
				),
				1 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME2,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax4,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '3', '1')),
				),
				2 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME3,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax4,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '3', '2')),
				),
				3 	=> array(
					c_Property_Name 	=> AM_CONFIG_INPUTNAME4,
		 			c_Property_Output => 	array(c_Property_Device 	=> c_Device_AudioMax4,
															c_Property_CommSrc	=> array(c_Comm_AudioMax, 'SetSource', '3', '3')),
				),
			),
	      // -------------------------------------------------------------------------------------------------------
		);
	}

  /** @}*/
?>