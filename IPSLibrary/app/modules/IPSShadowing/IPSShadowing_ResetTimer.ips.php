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
	 * @file          IPSShadowing_ResetTimer.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 21.03.2012<br/>
	 *
	 * Reset von "ManualChange" und "TempChange" Flag und Berechung der neuen Tages und End Zeiten
	 */

	include_once "IPSShadowing.inc.php";

	// Reset Manual Change Flags
	$categoryIdDevices = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Devices');
	$DeviceIds = IPS_GetChildrenIds($categoryIdDevices);
	foreach($DeviceIds as $DeviceId) {
		if (GetValue(IPS_GetObjectIDByIdent(c_Control_ManualChange, $DeviceId))) {
			SetValue(IPS_GetObjectIDByIdent(c_Control_ManualChange, $DeviceId), false);
		}
		if (GetValue(IPS_GetObjectIDByIdent(c_Control_TempChange, $DeviceId))) {
			SetValue(IPS_GetObjectIDByIdent(c_Control_TempChange, $DeviceId), false);
		}
	}

	/** @}*/
?>