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

	/**@addtogroup IPSSchaltuhr_configuration
	 * @{
	 *
	 * File mit Callback Methoden von IPSSchaltuhr
	 *	Für jeden ZSU von IPSSchaltuhr ist eine Callback Methode angelegt.
	 * Bei auslösung des ZSUs wird diese Funktion ausgeführt und parameter übergeben.
	 * Durch die Parameter kann der Grund festgestellt werden.
	 *
	 * @file          IPSSchaltuhr_Custom.inc.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 *
	 * Callback Methoden für IPSSchaltuhr
	 *
	 */

	// ----------------------------------------------------------------------------------------------------------------------------
	//
	// Function wird aufgerufen bei ZSU auslösung
	//
	// Parameters:
	//   $CycleId    - ID des auslösenden ZSUs ( Program.IPSSchaltuhr.ZSUzeiten.ZSUzeit_1)
	//   $Mode       - Grund der auslösung, mögliche Werte: "Start", "Stop"
	//
	// ----------------------------------------------------------------------------------------------------------------------------

	function Zeitschaltuhr_1($CycleId, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'Start':

			break;

		case 'Stop':

			break;
		}
	}

	function Zeitschaltuhr_2($CycleId, $Mode) {
		$CircleName = IPS_GetName($CycleId);

		switch($Mode){
		case 'Start':

			break;

		case 'Stop':

			break;
		}
	}


	/** @}*/
?>