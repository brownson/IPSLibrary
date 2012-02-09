<?
	/**@addtogroup entertainment_interface
	 * @ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_InterfaceNetPlayerSnd.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Anbindung des NetPlayers
	 *
	 * Script zur Anbindung des NetPlayers. Dieses Script ist in der Entertainment Konfiguration
	 * Entertainment_Configuration.ips.php als Kommunikations Script für den NetPlayer hinterlegt
	 * und wird immer aufgerufen sobald eine Aktion dür den NetPlayer über die Entertainment Steuerung
	 * getätigt wurde (zB Power On/Off).
	 *
	 */

	include_once "Entertainment.inc.php";
	IPSUtils_Include ("NetPlayer.inc.php",                     "IPSLibrary::app::modules::NetPlayer");

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_NetPlayer_SendData($Data) {
	   $Control = $Data[1];
	   $Command = $Data[2];
     	IPSLogger_Com(__file__, "Send Data to NetPlayer, Control='$Control', Command='$Command'");
		if ($Command=='poweron') {
			NetPlayer_Power(true);
		} else if ($Command=='poweroff') {
			NetPlayer_Power(false);
		} else {
		   IPSLogger_Err(__file__, "Received unknown Command '$Command' from Entertainment-->Check Configuration!");
		}
	}

  /** @}*/
?>