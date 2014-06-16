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

	/**@addtogroup ipshighcharts_configuration
	 * @{
	 *
	 * Es gibt derzeit 2 Callback Methoden, diese ermöglichen es die Generierung von Charts zu beeinflußen
	 *
	 * Funktionen:
	 *  - function IPSHighcharts_BeforeBuildChart()
	 *  - function IPSHighcharts_AfterBuildChart()
	 *
	 * @file          IPSHighcharts_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 06.01.2014<br/>
	 *
	 * Callback Methoden für IPSHighcharts
	 *
	 */

	/**
	 * Diese Funktion wird vor der Anzeige eines Charts aufgerufen
	 *
	 * Hier ist es zum Beispiel diverse Highchartsparameter wie die Stack Konfiguration zu ändern
	 *
	 * Parameters:
	 *   @param integer $varID  Variable, die zur Highcharts Anzeige verwendet wird
	 *   @param integer $chartID  Chart Variable, die zur Highcharts Anzeige verwendet wird
	 *   @param array $CfgDaten  Highcharts Konfigurations Daten
	 *
	 */
	function IPSHighcharts_BeforeBuildChart($varID, $chartID, &$CfgDaten) {
	}

	/**
	 * Diese Funktion wird nach der Anzeige eines Charts aufgerufen
	 *
	 * Parameters:
	 *   @param integer $varID  Variable, die zur Highcharts Anzeige verwendet wird
	 *   @param integer $chartID  Chart Variable, die zur Highcharts Anzeige verwendet wird
	 *   @param array $CfgDaten  Highcharts Konfigurations Daten
	 *
	 */
	function IPSHighcharts_AfterBuildChart($varID, $chartID, $CfgDaten) {
	}

	/** @}*/

?>