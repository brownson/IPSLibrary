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

	/**@addtogroup IPSWecker_configuration
	 * @{
	 *
	 * File mit Callback Methoden von IPSWecker
	 *	Für jeden Wecker von IPSWecker ist eine Callback Methode angelegt.
	 * Bei auslösung des Weckers wird diese Funktion ausgeführt und parameter übergeben.
	 * Durch die Parameter kann der Grund festgestellt werden.
	 *
	 * @file          IPSWecker_Custom.inc.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 *
	 * Callback Methoden für IPSWecker
	 *
	 */

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_1($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':
			// Wecker aktion
			break;
		case 'SnoozeTime':
			// Wecker wird lauter
			break;
		case 'StopEvent':
		case 'EndTime':
			// Wecker ausschalten.
			break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_2($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':
			// Wecker aktion
			break;
		case 'SnoozeTime':
			// Wecker wird lauter
			break;
		case 'StopEvent':
		case 'EndTime':
			// Wecker ausschalten.
			break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_3($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':
			// Wecker aktion
			break;
		case 'SnoozeTime':
			// Wecker wird lauter
			break;
		case 'StopEvent':
		case 'EndTime':
			// Wecker ausschalten.
			break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_4($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_5($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_6($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_7($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_8($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_9($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_10($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_11($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei Wecker auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden Weckers ( Program.IPSWecker.Weckzeiten.Weckzeit_1)
	//   $WeckerName - Name des auslösenden Weckers
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "StopEvent", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Weckzeit_12($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'StopEvent':
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}



	/** @}*/
?>