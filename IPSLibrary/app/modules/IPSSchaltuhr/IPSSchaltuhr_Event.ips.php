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

	/**@addtogroup IPSSchaltuhr
	 * @{
	 *
	 * @file          IPSSchaltuhr_Event.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 *
	 */

	include_once "IPSSchaltuhr.inc.php";

			$eventId 	=  $_IPS['EVENT'];

			$strpos  	= strrpos(IPS_GetName($eventId), '-', 0);
			$CircleName = substr(IPS_GetName($eventId),0, $strpos);
//			$EventMode 	= substr(IPS_GetName($eventId), $strpos+1, strlen(IPS_GetName($eventId))-$strpos-1);
			$CircleId 	= get_CirclyIdByCircleIdent($CircleName, ZSU_ID_ZSUZEITEN);
			$Properts   = get_ZSUConfiguration()[$CircleName];
			$Name = $Properts[c_Property_Name];


			if (function_exists($CircleName)) {
					IPSLogger_Dbg(__file__, 'Zeitschaltuhr CallBack Funktion '.$Name.' Existiert in IPSSchaltuhr_Custom.');
//					IPSSchaltuhr_Log('Zeitschaltuhr gestartet:  '.$Name.', Aktion: Sensor');
//IPS_LogMessage('IPSSchaltuhr_Event','Zeitschaltuhr gestartet:  '.$Name.', Aktion: '.IPS_GetName($eventId));

					$OVId = get_ControlId(c_Control_RunAktiv, $CircleId);
					$LastDate = IPS_GetVariable($OVId);
					if ($LastDate['VariableUpdated']+30 < time()){
						set_Overview($CircleId);
					}

					$result = 0;
					$RunAktiv =  explode(',', get_ControlValue(c_Control_RunAktiv, $CircleId));
					$i=1;
					foreach ($Properts[c_Property_RunSensoren] as $PropName=>$PropData) {
						$SensorName = $PropData[c_Property_Name];
						$SensorID 	= $PropData[c_Property_SensorID];
						$SensorCo 	= $PropData[c_Property_Condition];
						$Value 		= $PropData[c_Property_Value];

						if ((bool)$RunAktiv[$i] == true){
								switch ($SensorCo){
								case '>':
									if (GetValue($SensorID) > $Value) $result++;
									 break;
								case '=':
									if (GetValue($SensorID) == $Value) $result++;
									 break;
								case '<':
									if (GetValue($SensorID) < $Value) $result++;
									 break;
								}
						} else {
								$result++;
						}
						$i++;
					}

					if (count($Properts[c_Property_RunSensoren]) == $result and get_ControlValue(c_Control_SollAusgang, $CircleId) == true){
							if (get_ControlValue(c_Control_IstAusgang, $CircleId) == false){
									// --------------- Aktion -------------------
									set_ControlValue(c_Control_IstAusgang, $CircleId, true);
									IPSLogger_Inf(__file__, 'Starte Callback Aktion für:  '.$Name.', Mode: SensorStart');
									IPSSchaltuhr_Log('Starte Callback Aktion für:  '.$Name.', Mode: SensorStart');
									$CircleName($CircleId, 'Start');
			 				}

					} elseif (get_ControlValue(c_Control_SollAusgang, $CircleId) == true ){
							if (get_ControlValue(c_Control_IstAusgang, $CircleId) == true){
									// --------------- Aktion -------------------
									set_ControlValue(c_Control_IstAusgang, $CircleId, false);
									IPSLogger_Inf(__file__, 'Starte Callback Aktion für:  '.$Name.', Mode: SensorStop');
									IPSSchaltuhr_Log('Starte Callback Aktion für:  '.$Name.', Mode: SensorStop');
									$CircleName($CircleId, 'Stop');
							}
					}

			} else {
					IPSLogger_Err(__file__, "Zeitschaltuhr CallBack Funktion $CircleName in IPSSchaltuhr_Custom existiert nicht. Schaltuhr: ".$Name);
			}


	/** @}*/
?>