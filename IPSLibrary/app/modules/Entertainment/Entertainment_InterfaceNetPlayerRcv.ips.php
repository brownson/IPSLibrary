<?
	/**@addtogroup entertainment_interface
	 * @ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_InterfaceNetPlayerRcv.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Script zur Anbindung des NetPlayers. Dieses Script ist in der Entertainment Konfiguration
	 * Entertainment_Configuration.ips.php als Empfangs Script für den NetPlayer hinterlegt
	 * und wird immer aufgerufen sobald sich die spezifizierte Variable des NetPlayers ändert.
	 *
    * Anbindung des NetPlayers
	 *
	 */

	include_once "Entertainment.inc.php";
	IPSUtils_Include ("NetPlayer.inc.php",                     "IPSLibrary::app::modules::NetPlayer");

	
	IPS_SemaphoreEnter('NetPlayer', 1000);

	if($IPS_SENDER == "Variable") {
	   $variableName = IPS_GetName($IPS_VARIABLE);
		switch ($variableName) {
		   case 'Power':
		      if ($IPS_VALUE) {
					Entertainment_ReceiveData(array(c_Comm_NetPlayer, 'netplayer', 'poweron'), c_MessageType_Info);
				} else {
					Entertainment_ReceiveData(array(c_Comm_NetPlayer, 'netplayer', 'poweroff'), c_MessageType_Info);
				}
		   	break;
		   case 'RemoteControl':
		      $ControlId = get_ControlIdByDeviceName(c_Device_NetPlayer, c_Control_RemoteSourceType);
		   	if (GetValue(NP_ID_CONTROLTYPE) <> GetValue($ControlId)) {
		      	IPSLogger_Com(__file__, "Receive RemoteControlType ".GetValue(NP_ID_CONTROLTYPE)." for NetPlayer");
					Entertainment_ReceiveData(array(c_Comm_NetPlayer, 'netplayertype', (string)GetValue(NP_ID_CONTROLTYPE)), c_MessageType_Info);
				} else {
		      	Entertainment_RefreshRemoteControlByDeviceName(c_Device_NetPlayer);
				}
		   	break;
		   case 'MobileControl':
		      //Entertainment_RefreshRemoteControlByDeviceName(c_Device_NetPlayer, c_Control_iRemoteSource);
		   	break;
		   case 'ControlType':
		      //IPSLogger_Com(__file__, "Receive RemoteControlType $IPS_VALUE for NetPlayer");
				//Entertainment_ReceiveData(array(c_Comm_NetPlayer, 'netplayertype', (string)$IPS_VALUE), c_MessageType_Info);
		   	break;
		   default:
		      IPSLogger_Err(__file__, "Unknown Variable $variableName");
		      Exit;
		}
	}

	IPS_SemaphoreLeave('NetPlayer');

  /** @}*/
?>