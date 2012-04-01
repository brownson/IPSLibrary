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
	 * Es gibt derzeit 2 Callback Methoden, diese ermöglichen es eigene Hardware einzubinden und bevor bzw. nach der Aktivierung 
	 * einer Bewässerung zB. die Wasserzufuhr zu steuern.
	 * 
	 * Funktionen:
	 * - function IPSWatering_BeforeActivateWatering($CycleId ,$Value, $Mode) {
	 * - function IPSWatering_AfterActivateWatering($CycleId ,$Value, $Mode) {
	 * 
	 * So ist es zum Beispiel möglich in der Funktion "IPSWatering_BeforeActivateWatering" durch Rückgabe von false die Bewässerung 
	 * anhand eines Feuchtigkeitssensors zu unterbinden.
	 *
	 * @file          IPSWatering_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 11.03.2012<br/>
	 *
	 * Callback Methoden für IPSWatering
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

	/** @}*/
?>