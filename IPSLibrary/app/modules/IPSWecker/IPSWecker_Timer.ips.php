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
	 * Version 1.00.0, 01.04.2012<br/>
	 *
	 *
	 */

	include_once "IPSWecker.inc.php";


//print $feiertag;
//print_r($Fdays);

	switch ($_IPS['SENDER']) {
		case 'WebFront':
			break;
		case 'TimerEvent':
			$eventId 	=  $_IPS['EVENT'];
			$eventName 	= substr(IPS_GetName($eventId),0, strlen(IPS_GetName($eventId))-2);
			$eventTime 	= IPS_GetEvent($eventId)['CyclicTimeFrom'];
			$eventNow	= Date('H:i',$eventTime);

			IPSWecker_Log('Wecker Event: Auslösung prüfen für '.$eventName);

			for ($i = 1; $i < 10; $i++){
				if ((c_WeckerCircle.'_'.$i == $eventName) and (function_exists('c_WeckerCircle_'.$i))) {
					$eventCircle 		= 'c_WeckerCircle_'.$i;
					$eventCircleId 	= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$i, WECKER_ID_WECKZEITEN);
					$eventCircleTimeA	= get_ControlValue(get_DateTranslation(Date('l')),	$eventCircleId);
					$Hour 				= substr($eventCircleTimeA,0,2);
					$Minute 				= substr($eventCircleTimeA,3,2);
					$eventCircleTime 	= mktime($Hour, $Minute, 0);

					$WeckerConfig     = get_WeckerConfiguration();
					$ParamsSnoozeTime = $WeckerConfig[$eventName][c_Property_SnoozeTime];
					$ParamsEndTime 	= $WeckerConfig[$eventName][c_Property_EndTime];
					$ParamsName		 	= $WeckerConfig[$eventName][c_Property_Name];
					$ParamsFrostTime 	= $WeckerConfig[$eventName][c_Property_FrostTime];
					$ParamsSensorId	= $WeckerConfig[$eventName][c_Property_FrostSensor];
					$ParamsFrostTemp 	= $WeckerConfig[$eventName][c_Property_FrostTemp];

					$objectIds 			= explode(',',get_ControlValue(c_Control_Optionen, $eventCircleId));
					$WDay             = Date('N');
					$WDay--;
					$Active           = $objectIds[$WDay];
					$Freeze 				= $objectIds[9];
					$Global 				= $objectIds[10];
					$Snooze 				= $objectIds[11];
					$End     			= $objectIds[12];

            	$Feiertag = get_Feiertag($objectIds[7]);
					$Urlaub = get_Urlaub($objectIds[8], get_ControlValue(c_Control_Urlaubszeit, $eventCircleId));
					
					$eventCircleMode= '';


					// --------------- Weckbedingung -------------------
					if ($Urlaub == false and $Feiertag == false and $Active == true and $Global == true){

							// --------------- FrostTime -------------------
							if (get_TimeToleranz($eventTime+($ParamsFrostTime*60), $eventCircleTime)){
									if ( getvalue($ParamsSensorId)  < $ParamsFrostTemp) {
									// --------------- Neue Eventzeit setzen -------------------
											if ($Snooze == true){
													IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime-($ParamsFrostTime*60)+($ParamsSnoozeTime*60), 0);
											}
											elseif($End == true){
													IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime-($ParamsFrostTime*60)+($ParamsEndTime*60), 0);
											}
											// --------------- Aktion -------------------
											$eventMode = "AlarmTime";
											IPSWecker_Log('Wecker ausgelöst:  '.$ParamsName.', Aktion: '.$eventMode);
											$eventCircle($eventCircleId, $ParamsName, $eventMode);
									}
									else {
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime, 0);
									}
							}

							// --------------- AlarmTime -------------------
							if (get_TimeToleranz($eventTime, $eventCircleTime)){
									// --------------- Neue Eventzeit setzen -------------------
									if ($Snooze == true){
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime+($ParamsSnoozeTime*60), 0);
									}
									elseif($End == true){
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime+($ParamsEndTime*60), 0);
									}
									elseif($Freeze == true){
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime-($ParamsFrostTime*60), 0);
									}
									// --------------- Aktion -------------------
									$eventMode = "AlarmTime";
									IPSWecker_Log('Wecker ausgelöst:  '.$ParamsName.', Aktion: '.$eventMode);
									$eventCircle($eventCircleId, $ParamsName, $eventMode);
							}

							// --------------- SnoozeTime -------------------
							if ((get_TimeToleranz($eventTime, $eventCircleTime+($ParamsSnoozeTime*60)))
							or ( get_TimeToleranz($eventTime, $eventCircleTime-($ParamsFrostTime*60)+($ParamsSnoozeTime*60)))){
									// --------------- Neue Eventzeit setzen -------------------
									if ($End == true){
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime+($ParamsEndTime*60), 0);
									}
									elseif ($Freeze == true) {
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime-($ParamsFrostTime*60), 0);
									}
									else {
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime, 0);
									}
									if ($Snooze == true){
											// --------------- Aktion -------------------
											$eventMode = "SnoozeTime";
											IPSWecker_Log('Wecker ausgelöst:  '.$ParamsName.', Aktion: '.$eventMode);
											$eventCircle($eventCircleId, $ParamsName, $eventMode);
									}
							}

							// --------------- EndTime -------------------
							if ((get_TimeToleranz($eventTime, $eventCircleTime+($ParamsEndTime*60)))
							or ( get_TimeToleranz($eventTime, $eventCircleTime-($ParamsFrostTime*60)+($ParamsEndTime*60)))){
									// --------------- Neue Eventzeit setzen -------------------
									if ($Freeze == true){
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime-($ParamsFrostTime*60), 0);
									}
									else {
											IPS_SetEventCyclicTimeBounds($eventId, $eventCircleTime, 0);
									}
									if ($End == true){
											// --------------- Aktion -------------------
											$eventMode = "EndTime";
											IPSWecker_Log('Wecker ausgelöst:  '.$ParamsName.', Aktion: '.$eventMode);
											$eventCircle($eventCircleId, $ParamsName, $eventMode);
									}
							}
					}
				}
			}
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