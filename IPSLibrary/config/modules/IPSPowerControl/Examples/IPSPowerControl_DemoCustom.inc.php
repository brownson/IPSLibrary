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

		$id = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSPowerControl.Custom');
		$kwh_HomeControl = GetValue(IPS_GetVariableIDByName('HC_KWH',$id));
		$kwh_Washing     = GetValue(IPS_GetVariableIDByName('WM_KWH',$id));
		$kwh_Dryer       = GetValue(IPS_GetVariableIDByName('TR_KWH',$id));
		$kwh_Heating     = GetValue(IPS_GetVariableIDByName('HZ_KWH',$id));

		$L1          = $sensorList[0];
		$L2          = $sensorList[1];
		$L3          = $sensorList[2];
		$homeControl = ($sensorList[3]/10/2/1000 - $kwh_HomeControl)*1000*60;
		$washing     = ($sensorList[4]/10/2/1000 - $kwh_Washing)*1000*60;
		$dryer       = ($sensorList[5]/10/2/1000 - $kwh_Dryer)*1000*60;
		$heating     = ($sensorList[6]/10/2/1000 - $kwh_Heating)*1000*60;
		$ventilation = 50;
		$sauna = 0;
		$light = 0;
		$L1 = $L1 - $heating;
		$L3 = $L3 - $dryer;
		$L2 = $L2 - $homeControl;
		$L2 = $L2 - $washing;
		$L3 = $L3 - $ventilation;
		
		IPSUtils_Include ("IPSLight.inc.php", "IPSLibrary::app::modules::IPSLight");
		$lightManager = new IPSLight_Manager();
		$lightL1 = $lightManager->GetPowerConsumption('L1');
		$lightL2 = $lightManager->GetPowerConsumption('L2');
		$lightL3 = $lightManager->GetPowerConsumption('L3');
		$light   = $lightL1 + $lightL2 + $lightL3;
		$L1      = $L1 - $lightL1;
		$L2      = $L2 - $lightL2;
		$L3      = $L3 - $lightL3;

		if ($L1>=2000 and $L2>=2000 and $L3>=2000) {
			$sauna= 6000;
			$L1 = $L1 - 2000;
			$L2 = $L2 - 2000;
			$L3 = $L3 - 2000;
		}

		$txt = "LiL1=$lightL1,LiL2=$lightL2,LiL3=$lightL3,Sa=$sauna,WM=$washing,TR=$dryer,HZ=$heating,HC=$homeControl,L1=$L1,L2=$L2,L3=$L3";
		if ($L1<0) {
			$L1=0;
		}
		if ($L2<0) {
			$L2=0;
		}
		if ($L3<0) {
			$L3=0;
		}
		
		// Store KWH Values
		SetValue(IPS_GetVariableIDByName('HC_KWH',$id), $sensorList[3]/10/2/1000);
		SetValue(IPS_GetVariableIDByName('WM_KWH',$id), $sensorList[4]/10/2/1000);
		SetValue(IPS_GetVariableIDByName('TR_KWH',$id), $sensorList[5]/10/2/1000);
		SetValue(IPS_GetVariableIDByName('HZ_KWH',$id), $sensorList[6]/10/2/1000);
		SetValue(IPS_GetVariableIDByName('SAUNA_KWH',$id), GetValue(IPS_GetVariableIDByName('SAUNA_KWH',$id)) + $sauna/1000/60);
		SetValue(IPS_GetVariableIDByName('LIGHT_KWH',$id), GetValue(IPS_GetVariableIDByName('LIGHT_KWH',$id)) + $light/1000/60);
		SetValue(IPS_GetVariableIDByName('KWL_KWH',  $id), GetValue(IPS_GetVariableIDByName('KWL_KWH',  $id)) + $ventilation/1000/60);
		SetValue(IPS_GetVariableIDByName('L1_KWH',   $id), GetValue(IPS_GetVariableIDByName('L1_KWH',   $id)) + $L1/1000/60);
		SetValue(IPS_GetVariableIDByName('L2_KWH',   $id), GetValue(IPS_GetVariableIDByName('L2_KWH',   $id)) + $L2/1000/60);
		SetValue(IPS_GetVariableIDByName('L3_KWH',   $id), GetValue(IPS_GetVariableIDByName('L3_KWH',   $id)) + $L3/1000/60);

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
		$returnList = $valueList;
		
		$id = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSPowerControl.Custom');
		$returnList[0]  = $sensorList[0]+$sensorList[1]+$sensorList[2];
		$returnList[1]  = $sensorList[0];
		$returnList[2]  = $sensorList[1];
		$returnList[3]  = $sensorList[2];
		$returnList[4]  = $sensorList[3]/10/2/1000;
		$returnList[5]  = $sensorList[4]/10/2/1000;
		$returnList[6]  = $sensorList[5]/10/2/1000;
		$returnList[7]  = $sensorList[6]/10/2/1000;
		$returnList[8]  = GetValue(IPS_GetVariableIDByName('SAUNA_KWH',$id));
		$returnList[9]  = GetValue(IPS_GetVariableIDByName('LIGHT_KWH',$id));
		$returnList[10] = GetValue(IPS_GetVariableIDByName('KWL_KWH',$id));
		$returnList[11] = GetValue(IPS_GetVariableIDByName('L1_KWH',$id));
		$returnList[12] = GetValue(IPS_GetVariableIDByName('L2_KWH',$id));
		$returnList[13] = GetValue(IPS_GetVariableIDByName('L3_KWH',$id));

		return $returnList;
	}

	/** @}*/

?>