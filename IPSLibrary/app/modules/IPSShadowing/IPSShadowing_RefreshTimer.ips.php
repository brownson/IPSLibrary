<?
	/*
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

	/**@addtogroup ipsshadowing
	 * @{
	 *
	 * @file          IPSShadowing_RefreshTimer.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 21.03.2012<br/>
	 *
	 * Refresh Timer Script - Update der ausstehenden Fahrzeiten
	 */

	include_once "IPSShadowing.inc.php";

	$result = IPS_SemaphoreEnter('IPSShadowing_Refresh', 500);

	if ($result) {
		$categoryIdDevices      = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Devices');
		$deviceIds              = IPS_GetChildrenIds($categoryIdDevices);
		$oneOrMoreDevicesActive = false;
		
		foreach($deviceIds as $deviceId) {
			$device = new IPSShadowing_Device($deviceId);
			$deviceActive = $device->Refresh();
			$oneOrMoreDevicesActive = ($oneOrMoreDevicesActive or $deviceActive);
		}
		
		if (!$oneOrMoreDevicesActive) {
			$refreshTimerId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_RefreshTimer.Refresh');
			IPS_SetEventActive($refreshTimerId, false);
		}

		IPS_SemaphoreLeave('IPSShadowing_Refresh');
	}
	
	/** @}*/
?>