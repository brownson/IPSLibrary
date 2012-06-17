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

	/**@addtogroup IPSHealth
	 * @{
	 *
	 * @file          IPSHealth_Timer.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 *
	 */

	include_once "IPSHealth.inc.php";


	switch ($_IPS['SENDER']) {
		case 'TimerEvent':
			$eventId 	=  $_IPS['EVENT'];
			$strpos  	= strrpos(IPS_GetName($eventId), '-', 0);
			$EventName 	= substr(IPS_GetName($eventId),0, $strpos);
			$EventMode 	= substr(IPS_GetName($eventId), $strpos+1, strlen(IPS_GetName($eventId))-$strpos-1);
			$AppId  		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSHealth');

			if ($EventMode == "Server"){
					set_SysInfo_Server();
					Check_VarTimeout();
			}

			if ($EventMode == "DBHealth") set_SysInfo_DBHealth();

			if ($EventMode == "Day") {
					set_SysInfo_Statistik();
					IPS_RunScript(IPS_GetScriptIDByName("IPSHealth_HMInventory",$AppId));
			}

//			if ($EventMode == "Timeout")	Check_VarTimeout($EventName);

			break;

		case 'WebFront':
			break;

		case 'Execute':
			$ControlId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_SysInfo);
			set_SysInfo_Statistik($ControlId);

			break;

		case 'RunScript':
			break;

		default:
			IPSLogger_Err(__file__, 'Unknown Sender '.$_IPS['SENDER']);
			break;
	}


	/** @}*/
?>