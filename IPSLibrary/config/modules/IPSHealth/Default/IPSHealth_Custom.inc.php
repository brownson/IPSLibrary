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

	/**@addtogroup IPSHealth_configuration
	 * @{
	 *
	 * File mit Callback Methoden von IPSHealth
	 *	Für jeden Health von IPSHealth ist eine Callback Methode angelegt.
	 * Bei auslösung des Healths wird diese Funktion ausgeführt und parameter übergeben.
	 * Durch die Parameter kann der Grund festgestellt werden.
	 *
	 * @file          IPSHealth_Custom.inc.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 *
	 * Callback Methoden für IPSHealth
	 *
	 */

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Health auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Healths ( Program.IPSHealth.Weckzeiten.Weckzeit_1)
	//   $HealthName - Name des auslösenden Healths
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Circle_1($CycleId, $HealthName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Health auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Healths ( Program.IPSHealth.Weckzeiten.Weckzeit_1)
	//   $HealthName - Name des auslösenden Healths
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Circle_2($CycleId, $HealthName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Health auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Healths ( Program.IPSHealth.Weckzeiten.Weckzeit_1)
	//   $HealthName - Name des auslösenden Healths
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Circle_3($CycleId, $HealthName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

	}




	/** @}*/
?>