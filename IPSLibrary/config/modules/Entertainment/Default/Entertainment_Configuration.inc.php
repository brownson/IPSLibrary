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
	 * @file          Entertainment_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
    */

	IPSUtils_Include ("Entertainment_Constants.inc.php",   "IPSLibrary::app::modules::Entertainment");
	IPSUtils_Include ("NetPlayer_Constants.inc.php",       "IPSLibrary::app::modules::NetPlayer");
	IPSUtils_Include ("NetPlayer_Configuration.inc.php",   "IPSLibrary::config::modules::NetPlayer");

	define ("c_Comm_WinLIRC",							"WinLIRC");
	define ("c_Comm_Onkyo",								"Onkyo");
	define ("c_Comm_NetPlayer",						"NetPlayer");
	define ("c_Comm_AudioMax",							"AudioMax");


	// ========================================================================================================================
	// Defintion of Communication Data
	// ========================================================================================================================
	function get_CommunicationConfiguration () {
	   return array (
			c_Comm_WinLIRC => array (
				c_Property_ScriptSnd 			=> 'Entertainment_InterfaceWinLIRCSnd.inc.php',
				c_Property_ScriptRcv  			=> 'Entertainment_InterfaceWinLIRCRcv.ips.php',
				c_Property_FunctionSnd 			=> 'WinLIRC_SendData',
				c_Property_Instance     		=> 0,
				c_Property_MessageTypes       => array(),
				c_Property_InpTranslationList	=> array(),
				c_Property_OutTranslationList => array(),
			),
			c_Comm_Onkyo => array (
				c_Property_ScriptSnd 			=> 'Entertainment_InterfaceOnkyoSnd.inc.php',
				c_Property_ScriptRcv  			=> 'Entertainment_InterfaceOnkyoRcv.ips.php',
				c_Property_FunctionSnd 			=> 'Onkyo_SendData',
				c_Property_Register     		=> 0,
				c_Property_Instance     		=> 0,
				c_Property_IPAddress          => '0.0.0.0',
				c_Property_Timeout				=> 30,
			),
			c_Comm_NetPlayer => array (
				c_Property_ScriptSnd 			=> 'Entertainment_InterfaceNetPlayerSnd.inc.php',
				c_Property_ScriptRcv 			=> 'Entertainment_InterfaceNetPlayerRcv.ips.php',
				c_Property_FunctionSnd 			=> 'Entertainment_NetPlayer_SendData',
				//c_Property_Instance     		=> 'Program.IPSLibrary.app.modules.NetPlayer',
				//c_Property_Variables     		=> 'MobileControl,RemoteControl,ControlType,Power',
			),
		);
	}

	// ========================================================================================================================
	// Defintion of Room Configuration
	// ========================================================================================================================
	function get_RoomConfiguration () {
	   return array (
		);
	}
	
	// ========================================================================================================================
	// Defintion of Device Configuration
	// ========================================================================================================================
	function get_DeviceConfiguration () {
		return array (
		);
	}
	
	// ========================================================================================================================
	// Defintion of Source Configuration
	// ========================================================================================================================
	function get_SourceConfiguration() {
	   return array (
		);
	}

  /** @}*/
?>