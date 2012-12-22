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

	/**@ingroup ipspowercontrol
	 * @{
	 *
	 * @file          IPSPowerControl_Utils.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.09.2012<br/>
	 *
	 * Utility Funktionen für IPSPowerControl
	 *
	 */

	/** 
	 * Umrechnung von KWH Werten 
	 *
	 * Diese Funktion wird zur Berechnung der Verbrauchswerte in KWH aufgerufen
	 *
	 * Mit dem Korrekturfaktor kann der Sensor Wert vor der Speicherung korrigiert werden.
	 * Beispiel: Sensor liefert 2 Impulse pro KWH, mit einen Faktor von 1/2 kann der Sensorwert wieder 
	 *           korrigiert werden.
	 *
	 * Mit dem Parameter $correctNegativDifferences werden nur die positiven Differenzen zum letzten Sensorwert
	 * ausgewertet (dieses Feature sollte aktiviert werden, wenn der Sensor nach einem Stromausfall wieder bei 
	 * 0 beginnt).
	 *
	 * Parameters:
	 *   @param integer $sensorIdx Nummer des Sensors im Konfigurations Array
	 *   @param float $factor Korrekturfaktor für die Berechung des KWH Wertes 
	 *   @param boolean $correctNegativDifferences bei TRUE wird nur die Differenz zum letzten Wert ausgewertet
	 *   @result float berechneter Wert
	 *
	 */
	 
	function IPSPowerControl_Value2KWH ($sensorIdx, $factor=1, $correctNegativDifferences=false) {
		$sensorConfig   = IPSPowerControl_GetSensorConfiguration();
		$sensorData     = $sensorConfig[$sensorIdx];
		$variableIdKWH  = $sensorData[IPSPC_PROPERTY_VARKWH];
		$result         = GetValue($variableIdKWH) * $factor;
		
		if ($correctNegativDifferences) {
			$currSensor            = $result;
			$variableIdLastSensor  = IPSPowerControl_GetCustomVariableId($variableIdKWH, 'Value2KWH_Sensor');
			$variableIdLastValue   = IPSPowerControl_GetCustomVariableId($variableIdKWH, 'Value2KWH_Value');
			$lastSensor            = GetValue($variableIdLastSensor);
			$lastValue             = GetValue($variableIdLastValue);
			$diff                  = $currSensor - $lastSensor;
			if ($diff < 0) {
				$diff = 0;
			}
			$result = $lastValue + $diff;

			SetValue($variableIdLastSensor, $currSensor);
			SetValue($variableIdLastValue, $result);
		}
		
		return $result;
	}

	/** 
	 * Berechung von Watt Werten aus den KWH Werten
	 *
	 * Diese Funktion kann aus den Werten eines Sensors der KWH Werte liefert, den aktuellen Watt 
	 * Verbrauch ermittlen (Durchschnitt der letzten 60 Sekunden).
	 *
	 * Parameters:
	 *   @param integer $sensorIdx Nummer des Sensors im Konfigurations Array
	 *   @param float $factor Korrekturfaktor für die Berechung des KWH Wertes 
	 *   @result float berechneter Wert
	 *
	 */

	function IPSPowerControl_Watt2KWH ($sensorIdx, $factor=1) {
		$sensorConfig   = IPSPowerControl_GetSensorConfiguration();
		$sensorData     = $sensorConfig[$sensorIdx];
		$variableIdWatt = $sensorData[IPSPC_PROPERTY_VARWATT];
		
		$variableIdLast = IPSPowerControl_GetCustomVariableId($variableIdWatt, 'Watt2KWH');
		$valueLast      = GetValue($variableIdLast); 

		$result         = $valueLast + GetValue($variableIdWatt) * $factor / 1000 * IPSPC_REFRESHINTERVAL_WATT;
		SetValue($variableIdLast, $result);

		return $result;
	}

	/** 
	 * Berechung von KWH Werten aus Watt Werten
	 *
	 * Diese Funktion kann aus den Werten eines Sensors der Watt Werte liefert, den Verbrauch  
	 * in KWH ermittlen 
	 *
	 * Parameters:
	 *   @param integer $sensorIdx Nummer des Sensors im Konfigurations Array
	 *   @param float $factor Korrekturfaktor für die Berechung des KWH Wertes 
	 *   @result float berechneter Wert
	 *
	 */

	function IPSPowerControl_KWH2Watt ($sensorIdx, $factor=1) {
		$sensorConfig   = IPSPowerControl_GetSensorConfiguration();
		$sensorData     = $sensorConfig[$sensorIdx];
		$variableIdKWH  = $sensorData[IPSPC_PROPERTY_VARKWH];
		$variableIdLast = IPSPowerControl_GetCustomVariableId($variableIdKWH, 'KWH2Watt');
		$valueKWH       = GetValue($variableIdKWH) * $factor; 
		$valueLast      = GetValue($variableIdLast); 
		SetValue($variableIdLast, $valueKWH);

		$result         = ($valueKWH - $valueLast) * 1000 / IPSPC_REFRESHINTERVAL_WATT;
		if ($result < 0 ) {
			$result = 0;
		}
		return $result;
	}

	function IPSPowerControl_GetCustomVariableId($id, $suffix) {
		$customId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSPowerControl.Custom');
		$ident    = $id.'_'.$suffix;
		$variableId = @IPS_GetObjectIDByIdent($ident, $customId);
		if ($variableId===false) {
			IPSUtils_Include ("IPSInstaller.inc.php",                  "IPSLibrary::install::IPSInstaller");
			$variableId = CreateVariable($ident, 2 /*float*/,   $customId,  10, '',  null,  0);
			SetValue($variableId, GetValue($id));
		}
		return $variableId;
	}
	
	/** @}*/
?>