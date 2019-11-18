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
	 * @file          IPSShadowing_ChangeSettings.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 21.03.2012<br/>
	 *
	 * Action Script von IPSShadowing um Änderungen aus dem WebFront bzw. der Mobile GUI zu ermöglichen 
	 */

	include_once "IPSShadowing.inc.php";

	// ----------------------------------------------------------------------------------------------------------------------------
	if ($_IPS['SENDER']=='WebFront' || $_IPS['SENDER']=='Action') {
		$controlId   = $_IPS['VARIABLE'];
		$value       = $_IPS['VALUE'];
		$controlType = IPS_GetIdent($controlId);

		$scenarioManager = new IPSShadowing_ScenarioManager();
		$profileManager  = new IPSShadowing_ProfileManager();

		switch($controlType) {
			// Devices
			// -----------------------------------------------------------------------------------------------------
			case c_Control_Movement:
				$deviceId = IPS_GetParent($controlId);
				$device = new IPSShadowing_Device($deviceId);
				$device->MoveByControl($value);
				break;

			case c_Control_Position:
				$deviceId = IPS_GetParent($controlId);
				$device = new IPSShadowing_Device($deviceId);
				$device->MoveByLevel($value);
				break;

			case c_Control_ProgramNight:
			case c_Control_ProgramDay:
			case c_Control_ProgramTemp:
			case c_Control_ProgramPresent:
			case c_Control_ProgramWeather:
			case c_Control_ProfileTemp:
			case c_Control_ProfileSun:
			case c_Control_ProfileWeather:
			case c_Control_ProfileBgnOfDay:
			case c_Control_ProfileEndOfDay:
			case c_Control_ManualChange:
			case c_Control_TempChange:
			case c_Control_Automatic:
				$deviceId = IPS_GetParent($controlId);
				$device = new IPSShadowing_Device($deviceId);
				$device->ChangeSetting($controlId, $value);
				break;

			// Common Settings
			// -----------------------------------------------------------------------------------------------------
			case c_Control_MsgPrioTemp:
			case c_Control_MsgPrioProg:
				SetValue($controlId, $value);
				break;

			// Scenario Settings
			// -----------------------------------------------------------------------------------------------------
			case c_Control_ScenarioEdit:
				$scenarioManager->SetEditMode(IPS_GetParent($controlId), $value);
				break;
			case c_Control_ScenarioActivate:
				$scenarioManager->Activate($value);
				break;
			case c_Control_ScenarioSelect:
				$scenarioManager->Select($value);
				break;
			case c_Control_ScenarioName:
				$scenarioManager->Rename(IPS_GetParent($controlId), $value);
				break;
			
			// Profile Settings
			// -----------------------------------------------------------------------------------------------------
			case c_Control_ProfileTempSelect:
				$profileManager->SelectTemp($value);
				break;
			case c_Control_ProfileSunSelect:
				$profileManager->SelectSun($value);
				break;
			case c_Control_ProfileWeatherSelect:
				$profileManager->SelectWeather($value);
				break;
			case c_Control_ProfileBgnOfDaySelect:
				$profileManager->SelectBgnOfDay($value);
				break;
			case c_Control_ProfileEndOfDaySelect:
				$profileManager->SelectEndOfDay($value);
				break;

			case c_Control_ProfileName:
				$profileManager->Rename($controlId, $value);
				break;

			case c_Control_TempLevelOutShadow:
			case c_Control_TempLevelOutClose:
			case c_Control_TempLevelOutOpen:
			case c_Control_TempLevelInShadow:
			case c_Control_TempLevelInClose:
			case c_Control_TempLevelInOpen:
			case c_Control_BrightnessLow:
			case c_Control_BrightnessHigh:
			case c_Control_AzimuthBgn:
			case c_Control_AzimuthEnd:
			case c_Control_Elevation:
			case c_Control_Date:
			case c_Control_Simulation:
			case c_Control_Orientation:
			case c_Control_WorkdayMode:
			case c_Control_WorkdayTime:
			case c_Control_WorkdayOffset:
			case c_Control_WeekendMode:
			case c_Control_WeekendTime:
			case c_Control_WeekendOffset:
			case c_Control_RainCheck:
			case c_Control_WindLevel:
				$profileManager->SetValue($controlId, $value);
				break;

			
			// Scenario Settings
			// -----------------------------------------------------------------------------------------------------
			default:
				$categoryIdent = IPS_GetIdent(IPS_GetParent(IPS_GetParent($controlId)));
				if ($categoryIdent=='Scenarios') {
					$scenarioManager->SetValue($controlId, $value);
				} else {
					throw new Exception ("Error Unknown ControlType $controlType");
				}
		}
	}

	/** @}*/
?>
