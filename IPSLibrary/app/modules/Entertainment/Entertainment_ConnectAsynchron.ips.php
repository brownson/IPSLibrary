<?
	/**@ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_ConnectAsynchron.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Herstellen einer Geräte Verbindung im asynchronen Modus.
	 *
	 * Dieses Script wird gegebenenfalls von Entertainment_Connect.inc.php durch Verwendung von
	 * IPS_Execute aufgerufen, um asynchron eine Verbindung aufzubauen.
	 *
	 */
	include_once "Entertainment_Connect.inc.php";

 	Entertainment_Connect($DeviceName, $Value);

  /** @}*/
?>