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


	/**@addtogroup ipstwilight_configuration
	 * @{
	 *
	 * File mit Callback Methoden von IPSTwilight
	 *
	 * Für jede Variable von IPSTwilight kann eine entsprechende Callback Funktion angelegt werden.
	 *
	 * Das Programm kontrolliert beim Berechnen der Dämmerungszeiten, ob eine entsprechende Funktion
	 * vorhanden ist und legt gegebenenfalls einen ensprechenden Timer an.
	 *
	 * Folgende Callback Funktionen werden unterstützt:
	 *  - IPSTwilight_SunriseBegin()
	 *  - IPSTwilight_SunriseEnd()
	 *  - IPSTwilight_CivilBegin()
	 *  - IPSTwilight_CivilEnd()
	 *  - IPSTwilight_NauticBegin()
	 *  - IPSTwilight_NauticEnd()
	 *  - IPSTwilight_AstronomicBegin()
	 *  - IPSTwilight_AstronomicEnd()
	 *  - IPSTwilight_SunriseBeginLimited()
	 *  - IPSTwilight_SunriseEndLimited()
	 *  - IPSTwilight_CivilBeginLimited()
	 *  - IPSTwilight_CivilEndLimited()
	 *  - IPSTwilight_NauticBeginLimited()
	 *  - IPSTwilight_NauticEndLimited()
	 *  - IPSTwilight_AstronomicBeginLimited()
	 *  - IPSTwilight_AstronomicEndLimited()
	 *
	 * @file          IPSTwilight_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 14.02.2012<br/>
	 *
	 */

	/** Callback Methode, die beim Sonnenaufgang aufgerufen wird
	 *
	 */
	function IPSTwilight_SunriseBegin() {
		IPSLogger_Dbg(__file__, 'Call to customer specific Function "IPSTwilight_SunriseBegin"');

		$anwesend = GetValueBoolean(35206 );
		$scharf = GetValueBoolean(23793 );

//		echo "Es müssen alle 2 Bedingungen erfüllt sein:\n";
//		if ($anwesend == false) echo " - Keine Person anwesend\n";
//		if ($scharf == true) echo " - Haus ist scharf\n";

		if ( ($anwesend == false) and ($scharf == true))
		{
			HM_WriteValueFloat(26178 , "LEVEL", 100);
			HM_WriteValueFloat(53703 , "LEVEL", 100);
			HM_WriteValueFloat(32138 , "LEVEL", 100);
		}

	}

	/** Callback Methode, die beim Sonnenuntergang aufgerufen wird
	 *
	 */
	function IPSTwilight_SunriseEnd() {
		IPSLogger_Dbg(__file__, 'Call to customer specific Function "IPSTwilight_SunriseEnd"');

		$anwesend = GetValueBoolean(35206 );
		$scharf = GetValueBoolean(23793 );

//		echo "Es müssen alle 2 Bedingungen erfüllt sein:\n";
//		if ($anwesend == false) echo " - Keine Person anwesend\n";
//		if ($scharf == true) echo " - Haus ist scharf\n";

		if ( ($anwesend == false) and ($scharf == true))
		{
			HM_WriteValueFloat(26178 , "LEVEL", 0);
			HM_WriteValueFloat(53703 , "LEVEL", 0);
			HM_WriteValueFloat(32138 , "LEVEL", 0);
		}
	}

	function IPSTwilight_CivilBegin() {
		IPSLogger_Dbg(__file__, 'Call to customer specific Function "IPSTwilight_CivilBegin"');
	}

	function IPSTwilight_CivilEnd() {
		IPSLogger_Dbg(__file__, 'Call to customer specific Function "IPSTwilight_CivilEnd"');
	}

	function IPSTwilight_NauticBegin() {
		IPSLogger_Dbg(__file__, 'Call to customer specific Function "IPSTwilight_NauticBegin"');
	}

	function IPSTwilight_NauticEnd() {
		IPSLogger_Dbg(__file__, 'Call to customer specific Function "IPSTwilight_NauticEnd"');
	}

	function IPSTwilight_AstronomicBegin() {
		IPSLogger_Dbg(__file__, 'Call to customer specific Function "IPSTwilight_AstronomicBegin"');
	}

	function IPSTwilight_AstronomicEnd() {
		IPSLogger_Dbg(__file__, 'Call to customer specific Function "IPSTwilight_AstronomicEnd"');
		HM_WriteValueBoolean(29668, "STATE", false);
	}




	/** @}*/

?>