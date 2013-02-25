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

		$L1          = $sensorList[0];
		$L2          = $sensorList[1];
		$L3          = $sensorList[2];
		$homeControl = IPSPowerControl_KWH2Watt(3, 1/20000);
		$washing     = IPSPowerControl_KWH2Watt(4, 1/20000);
		$dryer       = IPSPowerControl_KWH2Watt(5, 1/20000);
		$heating     = IPSPowerControl_KWH2Watt(6, 1/20000);

		$ventilation = 50;
		$sauna = 0;
		$light = 0;
		$L1 = $L1 - $heating;
		$L3 = $L3 - $dryer;
		$L2 = $L2 - $homeControl;
		$L2 = $L2 - $washing;
		$L3 = $L3 - $ventilation;
		
		// Calculate Light
		IPSUtils_Include ("IPSLight.inc.php", "IPSLibrary::app::modules::IPSLight");
		$lightManager = new IPSLight_Manager();
		$lightL1 = $lightManager->GetPowerConsumption('L1');
		$lightL2 = $lightManager->GetPowerConsumption('L2');
		$lightL3 = $lightManager->GetPowerConsumption('L3');
		$light   = $lightL1 + $lightL2 + $lightL3;
		$L1      = $L1 - $lightL1;
		$L2      = $L2 - $lightL2;
		$L3      = $L3 - $lightL3;

		// Calculate Sauna
		if ($L1>=2000 and $L2>=2000 and $L3>=2000) {
			$sauna= 6000;
			$L1 = $L1 - 2000;
			$L2 = $L2 - 2000;
			$L3 = $L3 - 2000;
		}

		// Correct Values
		if ($L1<0) { $L1=0; }
		if ($L2<0) { $L2=0; }
		if ($L3<0) { $L3=0; }
		
		// Store calculated KWH Values
		IPSPowerControl_AddCalculatedValue('SAUNA_KWH', $sauna/1000/60);
		IPSPowerControl_AddCalculatedValue('LIGHT_KWH', $light/1000/60);
		IPSPowerControl_AddCalculatedValue('KWL_KWH',   $ventilation/1000/60);
		IPSPowerControl_AddCalculatedValue('L1_KWH',    $L1/1000/60);
		IPSPowerControl_AddCalculatedValue('L2_KWH',    $L2/1000/60);
		IPSPowerControl_AddCalculatedValue('L3_KWH',    $L3/1000/60);

		// Build Return List
		$returnList[0]  = $sensorList[0] + $sensorList[1] + $sensorList[2];
		$returnList[1]  = $sensorList[0];
		$returnList[2]  = $sensorList[1];
		$returnList[3]  = $sensorList[2];
		$returnList[4]  = $homeControl;
		$returnList[5]  = $washing;
		$returnList[6]  = $dryer;
		$returnList[7]  = $heating;
		$returnList[8]  = $sauna;
		$returnList[9]  = $light;
		$returnList[10] = $ventilation;
		$returnList[11] = $L1;
		$returnList[12] = $L2;
		$returnList[13] = $L3;

		return $returnList;
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
		$returnList[0]  = $sensorList[0]+$sensorList[1]+$sensorList[2];
		$returnList[1]  = $sensorList[0];
		$returnList[2]  = $sensorList[1];
		$returnList[3]  = $sensorList[2];
		$returnList[4]  = IPSPowerControl_Value2KWH(3, 1/20000);
		$returnList[5]  = IPSPowerControl_Value2KWH(4, 1/20000);
		$returnList[6]  = IPSPowerControl_Value2KWH(5, 1/20000);
		$returnList[7]  = IPSPowerControl_Value2KWH(6, 1/20000);
		$returnList[8]  = IPSPowerControl_GetCalculatedValue('SAUNA_KWH');
		$returnList[9]  = IPSPowerControl_GetCalculatedValue('LIGHT_KWH');
		$returnList[10] = IPSPowerControl_GetCalculatedValue('KWL_KWH');
		$returnList[11] = IPSPowerControl_GetCalculatedValue('L1_KWH');
		$returnList[12] = IPSPowerControl_GetCalculatedValue('L2_KWH');
		$returnList[13] = IPSPowerControl_GetCalculatedValue('L3_KWH');
		$returnList[14] = IPSPowerControl_Value2M3(7, 1/10, true);

		return $returnList;
	}

	/** @}*/

?>