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

	/**@addtogroup ipswatering_configuration
	 * @{
	 * 
	 * Es gibt derzeit 2 Callback Methoden, diese erm�glichen es eigene Hardware einzubinden und bevor bzw. nach der Aktivierung 
	 * einer Bew�sserung zB. die Wasserzufuhr zu steuern.
	 * 
	 * Funktionen:
	 * - function IPSWatering_BeforeActivateWatering($CycleId ,$Value, $Mode) {
	 * - function IPSWatering_AfterActivateWatering($CycleId ,$Value, $Mode) {
	 * 
	 * So ist es zum Beispiel m�glich in der Funktion "IPSWatering_BeforeActivateWatering" durch R�ckgabe von false die Bew�sserung 
	 * anhand eines Feuchtigkeitssensors zu unterbinden.
	 *
	 * @file          IPSWatering_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 11.03.2012<br/>
	 *
	 * Callback Methoden f�r IPSWatering
	 *
	 */

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function will be called before a Watering Cycle is switched (means activated and also deactivated)
	//
	// Watering of current Cycle can be prevented by returning Value false.
	//
	// Parameters:
	//   $CycleId    - ID of current Watering Device (means Program.IPSWatering.WaterCircles.MyCurrentCycle)
	//   $Value      - true for "Switch On", false for "Switch Off"
	//   $Mode       - Mode for Activation, possible Values: "Automatik Start" or "Manueller Start"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function IPSWatering_BeforeActivateWatering($CycleId ,$Value, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		//if ($Mode==c_Mode_StartAutomatic and $Value and $GroundHumidity) {
		//   return false;
		//}

		return true; // Return false to prevent Watering
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function will be called after a Watering Cycle is switched (means activated and also deactivated)
	//
	// Parameters:
	//   $CycleId    - ID of current Watering Device (means Program.IPSWatering.WaterCircles.MyCurrentCycle)
	//   $Value      - true for "Switch On", false for "Switch Off"
	//   $Mode       - Mode for Activation, possible Values: "Automatik Start" or "Manueller Start"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function IPSWatering_AfterActivateWatering($CycleId ,$Value, $Mode) {
		$CircleName = IPS_GetName($CycleId);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function will be called when Refresh of Display Data is taking Place
	//
	// Parameters:
	//   $CycleId         - ID of current Watering Device (means Program.IPSWatering.WaterCircles.MyCurrentCycle)
	//   $Duration        - Total Duration in Minutes
	//   $TimeDiffMinutes - Minutes aktually to be done 
	//   $TimeDiffSeconds - Seconds altually to be done
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function IPSWatering_AfterRefresh($circleId, $duration, $timeDiffMinutes, $timeDiffSeconds) {
		IPSLogger_Dbg(__file__, "Refresh for CircleID=$circleId, Duration=$duration, Mintues=$timeDiffMinutes, Seconds=$timeDiffSeconds");
	}

	/** @}*/
?>