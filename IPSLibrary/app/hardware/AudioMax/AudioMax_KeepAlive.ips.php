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

	 /**@addtogroup audiomax
	 * @{
	 *
	 * @file          AudioMax_KeepAlive.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Dieses Script wird von Timer aufgerufen, um die "Keep Alive" Message zu senden bzw.
	 * das Signal vom Server zu überprüfen.
	 *
	 */

 	include_once 'AudioMax.inc.php';

	$serverId  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.AudioMax.AudioMax_Server');
	$eventName = IPS_GetName($_IPS['EVENT']);
	
	// Alle 60 Sek wird KeepAlive Message zum Server gesendet
	if ($eventName == 'SendAlive') {
		$server = new AudioMax_Server($serverId);
		$server->SendData(AM_TYP_SET, AM_CMD_KEEPALIVE, null, null, '0');
	}

	// Alle 65 Sekunden wird überprüft, ob eine KeepAlive Message vom Server erhalten wurde.
	// Keep Alive Count wird alle 65 Sekunden erhöht und muss innerhalb des nächsten
	// Zyklus durch eine Message vom Server wieder auf 0 gesetzt werden.
	if ($eventName == 'CheckAlive') {
		// Read KeepAliveStatus: false=Error, true=OK
		$id_Status = IPS_GetVariableIDByName(AM_VAR_KEEPALIVESTATUS, $serverId);

		// Read KeepAliveFlag: ">0"=Waiting, 0=OK
		$id_Count  = IPS_GetVariableIDByName(AM_VAR_KEEPALIVECOUNT,  $serverId);

		// Count not reseted by KeepAlive Message and Status=OK -> Status=Error
		if (GetValue($id_Count) > 1 and GetValue($id_Status)) {
			SetValue($id_Status, false);
			IPSLogger_Wrn(__file__, 'AudioMax KeepAlive Message Stream is broken');

		// Count cleared by KeepAlive and Status Error -> Status=OK
		} else if (GetValue($id_Count)==0 and !GetValue($id_Status)) {
			SetValue($id_Status, true);
			IPSLogger_Inf(__file__, 'AudioMax KeepAlive Message Stream is online again');

		} else {
		}

		// KeepAliveFlag=Waiting
		SetValue($id_Count, GetValue($id_Count)+1);
	}

	/** @}*/
?>