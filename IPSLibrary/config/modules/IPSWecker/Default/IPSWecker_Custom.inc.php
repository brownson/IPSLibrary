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
	 * Version 1.00.0, 01.04.2012<br/>
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_1($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':
			// Wecker aktion
		break;
		case 'SnoozeTime':
			// Wecker wird lauter
		break;
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_2($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_3($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);


		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':
				IPS_LogMessage('DEBUG',"ALARM");
			// Wecker aktion
		break;
		case 'SnoozeTime':
				IPS_LogMessage('DEBUG',"SNOOZE");
			// Wecker wird lauter
		break;
		case 'EndTime':
				IPS_LogMessage('DEBUG',"END");
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_4($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_5($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_6($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_7($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_8($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_9($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
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
	//   $Mode       - Grund der auslösung, mögliche Werte: "FreezeTime", "AlarmTime", "SnoozeTime", "EndTime"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function c_WeckerCircle_10($CycleId, $WeckerName, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'FreezeTime':
		case 'AlarmTime':

			// Wecker aktion
		break;
		case 'SnoozeTime':

			// Wecker wird lauter
		break;
		case 'EndTime':

			// Wecker ausschalten da nicht da.
		break;
		}
	}



	/** @}*/
?>