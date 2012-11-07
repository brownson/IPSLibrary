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

	/**@defgroup ipspowercontrol_configuration IPSPowerControl Konfiguration
	 * @ingroup ipspowercontrol
	 * @{
	 *
	 * @file          IPSPowerControl_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.09.2012<br/>
	 *
	 * Konfigurations File für IPSPowerControl
	 *
	 */

	/**
	 *
	 * Defintion der Mess Sensoren
	 * 
	 * Die Konfiguration erfolgt in Form eines Arrays, für jeden Sensor wird ein Eintrag im Array erzeugt.
	 *
	 *   IPSPC_PROPERTY_NAME    - Name des Sensors
	 *
	 *   IPSPC_PROPERTY_VARWATT - Variable ID die zum Lesen der aktuellen "Watt" Werte verwendet werden soll
	 *
	 *   IPSPC_PROPERTY_VARKWH  - Variable ID die zum Lesen der aktuellen "kWh" Werte verwendet werden soll
	 *
	 * Eine ausführliche Beispielliste findet sich auch im Example Ordner
	 *
	 * Beispiel:
	 * @code
        function IPSPowerControl_GetSensorConfiguration() {
          return array(
            0    => array(IPSPC_PROPERTY_NAME        => 'L1',
                          IPSPC_PROPERTY_VARWATT     => 32902,
                          IPSPC_PROPERTY_VARKWH      => 40061,
                          ),
            1    => array(IPSPC_PROPERTY_NAME        => 'L2',
                          IPSPC_PROPERTY_VARWATT     => 44599,
                          IPSPC_PROPERTY_VARKWH      => 41795,
                          ),
            2    => array(IPSPC_PROPERTY_NAME        => 'L3',
                          IPSPC_PROPERTY_VARWATT     => 26373,
                          IPSPC_PROPERTY_VARKWH      => 21487,
                          ),
             );
        }
	 * @endcocde
	 *
	 * @return string Liefert Array mit den Daten der Mess Sensoren
	 */
	function IPSPowerControl_GetSensorConfiguration() {
		return array(
			0    => array(IPSPC_PROPERTY_NAME        => 'L1',
			              IPSPC_PROPERTY_VARWATT     => 11111,
			              IPSPC_PROPERTY_VARKWH      => 22222,
			              ),
	}
	

	/**
	 *
	 * Defintion der Visualisierungs Werte
	 * 
	 * Die Konfiguration erfolgt in Form eines Arrays, für jede Visualisierungs Variable wird ein Eintrag im Array erzeugt.
	 *
	 *   IPSPC_PROPERTY_NAME      - Name der Visualisierungs Variable
	 *
	 *   IPSPC_PROPERTY_DISPLAY   - Spezifiziert ob der Wert in der GUI visualisiert werden soll
	 *
	 *   IPSPC_PROPERTY_VALUETYPE - Werte Type der Variable, mögliche Werte:
	 *                                IPSPC_VALUETYPE_TOTAL  ... definiert die Variable als Summenwert über alle Stromkreise
	 *                                IPSPC_VALUETYPE_DETAIL ... definiert die Variable als Detailwert eines Stromkreises
	 *                                IPSPC_VALUETYPE_OTHER  ... übrige Werte (weder Total noch Detail)
	 *
	 * Eine ausführliche Beispielliste findet sich auch im Example Ordner
	 *
	 *
	 * Beispiel:
	 * @code
        function IPSPowerControl_GetValueConfiguration() {
          return array(
             0    => array(IPSPC_PROPERTY_NAME        => 'Total',
                           IPSPC_PROPERTY_DISPLAY     => true,
                           IPSPC_PROPERTY_VALUETYPE   => IPSPC_VALUETYPE_TOTAL,
                          ),
             1    => array(IPSPC_PROPERTY_NAME        => 'Waschmaschine',
                           IPSPC_PROPERTY_DISPLAY     => true,
                           IPSPC_PROPERTY_VALUETYPE   => IPSPC_VALUETYPE_DETAIL,
                          ),
             );
        }
	 * @endcocde
	 *
	 * @return string Liefert Array mit den Daten der Visualisierungs Werte
	 */
	function IPSPowerControl_GetValueConfiguration() {
		return array(
			1    => array(IPSPC_PROPERTY_NAME        => 'L1',
			              IPSPC_PROPERTY_DISPLAY     => true,
			              IPSPC_PROPERTY_VALUETYPE   => IPSPC_VALUETYPE_OTHER,
			              ),
		);
	}

	/** IPSPowerControl Stromkosten  
	 *
	 * Definiert die Stromkosten in Cents per kWh, die für die Berechnung der Werte verwendet
	 * werden soll.
	 *
	 */
	define ("IPSPC_ELECTRICITYRATE",    18 /*Cents per KWh*/);


	/** IPSPowerControl Aktualisierungs Interval Watt 
	 *
	 * Definiert das Interval für die Aktualisierung der berechneten Watt Verbrauchswerte.
	 * Die Angabe erfolgt in Sekunden
	 *
	 */
	define ("IPSPC_REFRESHINTERVAL_WATT",   60);

	/** IPSPowerControl Aktualisierungs Interval kWh 
	 *
	 * Definiert das Interval für die Aktualisierung der berechneten kWh Verbrauchswerte.
	 * Die Angabe erfolgt in Minuten
	 *
	 */
	define ("IPSPC_REFRESHINTERVAL_KWH",   60);

	/** @}*/
?>