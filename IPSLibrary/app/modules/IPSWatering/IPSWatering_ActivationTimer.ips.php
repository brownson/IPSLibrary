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

	/**@addtogroup ipswatering
	 * @{
	 *
	 * @file          IPSWatering_ActivationTimer.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 11.03.2012<br/>
	 *
	 * Starten der Bewässerungs Kreise zu den vorgegebenen Zeiten
	 *
	 */

	 /** @}*/

	include_once "IPSWatering.inc.php";

   $CircleName         = IPS_GetName($_IPS['EVENT']);
   $categoryId_Circles = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWatering.WaterCircles');
   $CircleId           = IPS_GetCategoryIDByName($CircleName, $categoryId_Circles);
   $ControlId          = get_ControlId(c_Control_Active, $CircleId);

	$WaterConfig = get_WateringConfiguration();

	// Wert von Bewässerungs Sensor ermitteln
	$SensorLimit = GetValue(get_ControlId(c_Control_Sensor, $CircleId));
	$SensorValue = false;
	if (array_key_exists(c_Property_Sensor, $WaterConfig[$CircleName])) {
		$SensorPath  = $WaterConfig[$CircleName][c_Property_Sensor];
		if ($SensorPath <> '') {
			$SensorId    = IPSUtil_ObjectIDByPath($SensorPath);
			if ($SensorId===false) {
			   IPSLogger_Wrn(__file__, "Specified Sensor '$SensorPath' could NOT be found");
			} else {
				$SensorValue = GetValue($SensorId);
			}
		}
	}

	if ($SensorValue===false and $SensorLimit>0) {
		IPSLogger_Err(__file__, "Rainfall Sensor NOT defined for Circle '$CircleName'");
		Exit;
	}

	// Bewässerung Starten
	if ($SensorLimit > 0 and $SensorValue!==false and $SensorLimit<=$SensorValue) {
		IPSWatering_LogNoActivationByRainfall($CircleId, $SensorLimit, $Rainfall);
		IPSWatering_CalcNextScheduleDateTime($CircleId);
	} else {
		IPSWatering_SetActive($ControlId, true, c_Mode_StartAutomatic);
	}

?>