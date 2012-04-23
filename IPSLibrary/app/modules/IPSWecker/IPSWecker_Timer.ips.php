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
	 * @file          IPSWecker_Timer.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 *
	 */

	include_once "IPSWecker.inc.php";

	switch ($_IPS['SENDER']) {
		case 'TimerEvent':
			$eventId 	=  $_IPS['EVENT'];
			if (IPS_GetName($eventId) =='Timer_Event') {
					break;
			}

			$CircleName = substr(IPS_GetName($eventId),0, strlen(IPS_GetName($eventId))-2);
			$CircleId 	= get_CirclyIdByCircleIdent($CircleName, WECKER_ID_WECKZEITEN);
			$eventTime 	= IPS_GetEvent($eventId)['CyclicTimeFrom'];

			$wecker     = AddConfiguration($CircleId);
			IPSLogger_Trc(__file__, 'Event: Auslösung prüfen für '.$wecker['Property']['Name'].' ('.$wecker['Circle']['Name'].')');

			if (function_exists($CircleName)) {
					IPSLogger_Trc(__file__, 'Weckerfunktion '.$wecker['Circle']['Name'].' Existiert in IPSWecker_Custom.');
					$CircleTime 			= mktime(substr($wecker['ActiveTime'],0,2), substr($wecker['ActiveTime'],3,2), 0);
					$CircleNameMode= '';

					// --------------- Weckbedingungen -------------------
					if ($wecker['RetUrlaub'] == false and $wecker['RetFeiertag'] == false and $wecker['Active'] == true and $wecker['Circle'][c_Control_Global] == true){
							IPSLogger_Trc(__file__, 'Weckbedingungen für Active, Global, Urlaub, Feiertag gültig.'.$wecker['Property'][c_Property_Name]);

							// --------------- FrostTime -------------------
							if (get_TimeToleranz($eventTime+($wecker['Property'][c_Property_FrostTime]*60), $CircleTime)){
									IPSLogger_Trc(__file__, 'FrostTime auslösung '.$wecker['Property'][c_Property_Name]);
									if ( getAviableSensor($wecker['Property'][c_Property_FrostSensor]) == 2 ) {
											if ( getvalue($wecker['Property'][c_Property_FrostSensor])  < $wecker['Property'][c_Property_FrostTemp]) {
													IPSLogger_Trc(__file__, 'Temperatur < FrostTemp = Frostwecken'.$wecker['Property'][c_Property_Name]);

													// --------------- Neue Eventzeit setzen -------------------
													if ($wecker['Circle'][c_Control_Schlummer] == true){
															IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_SnoozeTime]*60), 0);
													}
													elseif($wecker['Circle'][c_Control_End] == true){
															IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_EndTime]*60), 0);
													}
													IPSLogger_Trc(__file__, 'Neue EventTime: '.Date('H:i',IPS_GetEvent($eventId)['CyclicTimeFrom']).' für '.IPS_GetName($eventId));
													// --------------- Aktion -------------------
													$eventMode = "FrostTime";
													IPSLogger_Inf(__file__, 'Wecker ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
													IPSWecker_Log('Wecker ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
													$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
											} else {
													IPSLogger_Trc(__file__, 'Wecker auf Normalzeit, da kein Frost '.$wecker['Property'][c_Property_Name]);
													IPS_SetEventCyclicTimeBounds($eventId, $CircleTime, 0);
											}
									} else {
											IPSLogger_Err(__file__, 'Frostsensor '.$wecker['Property'][c_Property_FrostSensor].' nicht vorhanden '.$wecker['Property'][c_Property_Name]);
											IPSWecker_Log('FEHLER: Frostsensor '.$wecker['Property'][c_Property_FrostSensor].' nicht vorhanden '.$wecker['Property'][c_Property_Name]);
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime, 0);
									}
							}

							// --------------- AlarmTime -------------------
							if (get_TimeToleranz($eventTime, $CircleTime)){
									IPSLogger_Trc(__file__, 'AlarmTime auslösung '.$wecker['Property'][c_Property_Name]);
									// --------------- Neue Eventzeit setzen -------------------
									if ($wecker['Circle'][c_Control_Schlummer] == true){
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime+($wecker['Property'][c_Property_SnoozeTime]*60), 0);
									}
									elseif($wecker['Circle'][c_Control_End] == true){
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime+($wecker['Property'][c_Property_EndTime]*60), 0);
									}
									elseif($wecker['Circle'][c_Control_Frost] == true){
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60), 0);
									}
									IPSLogger_Trc(__file__, 'Neue EventTime: '.Date('H:i',IPS_GetEvent($eventId)['CyclicTimeFrom']).' für '.IPS_GetName($eventId));
									if ($wecker['Active'] == true){
											// --------------- Aktion -------------------
											$eventMode = "AlarmTime";
											IPSLogger_Inf(__file__, 'Wecker ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											IPSWecker_Log('Wecker ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
									}
							}

							// --------------- SnoozeTime -------------------
							if ((get_TimeToleranz($eventTime, $CircleTime+($wecker['Property'][c_Property_SnoozeTime]*60)))
							or ( get_TimeToleranz($eventTime, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_SnoozeTime]*60)))){
									IPSLogger_Trc(__file__, 'SnoozeTime auslösung '.$wecker['Property'][c_Property_Name]);
									// --------------- Neue Eventzeit setzen -------------------
									if ($wecker['Circle'][c_Control_End] == true){
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime+($wecker['Property'][c_Property_EndTime]*60), 0);
									}
									elseif ($wecker['Circle'][c_Control_Frost] == true) {
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60), 0);
									}
									else {
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime, 0);
									}
									IPSLogger_Trc(__file__, 'Neue EventTime: '.Date('H:i',IPS_GetEvent($eventId)['CyclicTimeFrom']).' für '.IPS_GetName($eventId));
									if ($wecker['Circle'][c_Control_Schlummer] == true){
											// --------------- Aktion -------------------
											$eventMode = "SnoozeTime";
											IPSLogger_Inf(__file__, 'Wecker ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											IPSWecker_Log('Wecker ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
									}
							}

							// --------------- EndTime -------------------
							if ((get_TimeToleranz($eventTime, $CircleTime+($wecker['Property'][c_Property_EndTime]*60)))
							or ( get_TimeToleranz($eventTime, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60)+($wecker['Property'][c_Property_EndTime]*60)))){
									IPSLogger_Trc(__file__, 'EndTime auslösung '.$wecker['Property'][c_Property_Name]);
									// --------------- Neue Eventzeit setzen -------------------
									if ($wecker['Circle'][c_Control_Frost] == true){
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime-($wecker['Property'][c_Property_FrostTime]*60), 0);
									}
									else {
											IPS_SetEventCyclicTimeBounds($eventId, $CircleTime, 0);
									}
									IPSLogger_Trc(__file__, 'Neue EventTime: '.Date('H:i',IPS_GetEvent($eventId)['CyclicTimeFrom']).' für '.IPS_GetName($eventId));
									if ($wecker['Circle'][c_Control_End] == true){
											// --------------- Aktion -------------------
											$eventMode = "EndTime";
											IPSLogger_Inf(__file__, 'Wecker ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											IPSWecker_Log('Wecker ausgelöst:  '.$wecker['Property'][c_Property_Name].', Aktion: '.$eventMode);
											$CircleName($CircleId, $wecker['Property'][c_Property_Name], $eventMode);
									}
							}
				}
			} else {
					IPSLogger_Err(__file__, "WeckerAktion $CircleName in IPSWecker_Custom existiert nicht. ".$wecker['Property'][c_Property_Name]);
			}
			break;
		case 'WebFront':
			break;
		case 'Execute':
			break;
		case 'RunScript':
			break;
		default:
			IPSLogger_Err(__file__, 'Unknown Sender '.$_IPS['SENDER']);
			break;
	}


	/** @}*/
?>