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

	/**@addtogroup IPSWecker
	 * @{
	 *
	 * @file          IPSWecker_Event.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 *
	 */

	include_once "IPSWecker.inc.php";

	$eventId 	=  $_IPS['EVENT'];
	$CircleName = IPS_GetName($eventId);

	if (c_WeckerCircle == substr($CircleName,0,strlen(c_WeckerCircle))){
			$CircleId 	= get_CirclyIdByCircleIdent($CircleName, WECKER_ID_WECKZEITEN);
			$eventTime 	= IPS_GetEvent($eventId)['CyclicTimeFrom'];

			$wecker     = AddConfiguration($CircleId);
			IPSLogger_Trc(__file__, ''.$wecker['Property'][c_Property_Name]);
			IPSWecker_Log('STOP Auslösung für '.$wecker['Property']['Name'].' ('.$wecker['Circle']['Name'].')');

			if (function_exists($CircleName)) {
					IPSLogger_Trc(__file__, 'Weckerfunktion '.$wecker['Circle']['Name'].' Existiert in IPSWecker_Custom.');
					// --------------- Neue Eventzeit setzen -------------------
					set_TimerEvents(0,$CircleId);

					if ($wecker['Circle'][c_Control_End] == true){
							// --------------- Aktion -------------------
							$eventMode = "StopEvent";
							IPSLogger_Inf(__file__, 'STOP ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
							$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
					}
			} else {
					IPSLogger_Err(__file__, "WeckerAktion $CircleName in IPSWecker_Custom existiert nicht. ".$wecker['Property'][c_Property_Name]);
			}
	}

	if ($CircleName == c_Control_LTag){
			$wecker = AddActiveControl();
		   IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, $wecker[c_Control_LTag],"", -1);
	}

	if ($CircleName == c_Control_LStunde){
			$wecker = AddActiveControl();
		   IPS_SetVariableProfileAssociation('IPSWecker_Stunde', 0, $wecker[c_Control_LStunde],"", -1);
	}

	if ($CircleName == c_Control_LMinute){
			$wecker = AddActiveControl();
		   IPS_SetVariableProfileAssociation('IPSWecker_Minute', 0, $wecker[c_Control_LMinute],"", -1);
	}
	/** @}*/
?>