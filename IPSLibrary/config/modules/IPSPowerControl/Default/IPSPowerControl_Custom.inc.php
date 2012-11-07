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

	/**@addtogroup ipspowercontrol_configuration
	 * @{
	 * 
	 * Es gibt derzeit 2 Callback Methoden, diese ermöglichen es eigene Werte für
	 * die Visualisierung zu Berechnen.
	 * So ist es zum Beispiel möglich den Gesamtverbrauch zu berechnen oder den Verbrauch 
	 * einzelner Gewerke herauszurechnen von denen man weiß, dass sie gerade in Betrieb sind.
	 * 
	 * Funktionen:
	 *  - function IPSPowerControl_CalculateValuesWatt() 
	 *  - function IPSPowerControl_CalculateValuesKWH() 
	 * 
	 * @file          IPSPowerControl_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 29.09.2012<br/>
	 *
	 * Callback Methoden für IPSPowerControl
	 *
	 */

	/** 
	 * Diese Funktion wird zur Berechnung der "Watt" Verbrauchswerte aufgerufen
	 *
	 * Man kann hier ausgehend von den Sensor Werten noch zusätzliche Werte Berechnen und für die Visualisierung 
	 * bereitstellen.
	 *
	 * Parameters:
	 *   @param array $sensorList Liste mit Sensor Werten
	 *   @param array $valueList Liste der zu berechnenden Werte
	 *   @result array Liste der berechneten Visualisierungs Werte
	 *
	 */

	function IPSPowerControl_CalculateValuesWatt($sensorList, $valueList) {
		$returnList = $valueList;
		foreach ($sensorList as $idx=>$value) {
			$returnList[$idx] = $value;
		}
		return = $returnList;
	}

	/** 
	 * Diese Funktion wird zur Berechnung der Verbrauchswerte in KWH aufgerufen
	 *
	 * Man kann hier ausgehend von den Sensor Werten noch zusätzliche Werte Berechnen und für die Visualisierung 
	 * bereitstellen.
	 *
	 * Parameters:
	 *   @param array $sensorList Liste mit Sensor Werten
	 *   @param array $valueList Liste der zu berechnenden Werte
	 *   @result array Liste der berechneten Visualisierungs Werte
	 *
	 */
	function IPSPowerControl_CalculateValuesKWH($sensorList, $valueList) {
		$returnList = $valueList;
		foreach ($sensorList as $idx=>$value) {
			$returnList[$idx] = $value;
		}
		return = $returnList;
	}

	/** @}*/

?>