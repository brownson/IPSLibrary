<?
	/**@ingroup entertainment 
	 * @{
	 *
	 * @file          Entertainment_PostInstallation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 31.01.2012<br/>
	 *
	 * Dieses Script wird nach jeder Installation der Entertainment Steuerung ausgeführt und
	 * führt einige kleinere Nacharbeiten aus.
	 */

	//Sync Devices to Roomes
	echo "--- Default Settings -------------------------------------------------------------------\n";
	IPSUtils_Include ("Entertainment.inc.php", "IPSLibrary::app::modules::Entertainment");

	Entertainment_SyncAllRoomControls();

	$RoomIds = IPS_GetChildrenIDs(c_ID_Roomes);
	foreach ($RoomIds as $RoomId) {
		$RoomPowerId = get_ControlIdByRoomId($RoomId, c_Control_RoomPower);
		Entertainment_SetRoomVisible($RoomPowerId, GetValue($RoomPowerId));
	}

	/** @}*/
?>