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

	/**@ingroup ipshomematic
	 * @{
	 *
	 * @file          IPSHomematic_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 14.07.2012<br/>
	 *
	 */

	/**
	 *
	 * Defintion der Homematic Instanzen
	 * 
	 * Die Konfiguration erfolgt in Form eines Arrays, für jede Homematic Instanz wird ein Eintrag im Array erzeugt.
	 *   Name                  - Name des jeweiligen Homematic Devices
	 *
	 *   HM_PROPERTY_SERIAL    - Seriennummer des Homematic Devices
	 *
	 *   HM_PROPERTY_CHANNEL   - Kanal des Homematic Devices
	 *
	 *   HM_PROPERTY_PROTOCOL  - Protokoll des Homematic Devices (Wired oder Funk), mögliche Werte:
	 *                             HM_PROTOCOL_BIDCOSRF - Funk
	 *                             HM_PROTOCOL_BIDCOSWI - Wired
	 *
	 *   HM_PROPERTY_TYPE      - Type des Homematic Devices, mögliche Werte:
	 *                             HM_TYPE_LIGHT
	 *                             HM_TYPE_SHUTTER
	 *                             HM_TYPE_DIMMER
	 *                             HM_TYPE_BUTTON
	 *                             HM_TYPE_SMOKEDETECTOR
	 *                             HM_TYPE_SWITCH
	 *
	 * Eine ausführliche Beispielliste findet sich auch im Example Ordner
	 *
	 * Beispiel:
	 * @code
        function IPSPowerControl_GetSensorConfiguration() {
          return array(
            'Name1'    => array(HM_PROPERTY_SERIAL      => 'IEQ0004711',
                                HM_PROPERTY_CHANNEL     => 1,
                                HM_PROPERTY_PROTOCOL    => HM_PROTOCOL_BIDCOSRF,
                                HM_PROPERTY_TYPE        => HM_TYPE_LIGHT,
                          ),
            'Name2'    => array(HM_PROPERTY_SERIAL      => 'IEQ0004712',
                                HM_PROPERTY_CHANNEL     => 1,
                                HM_PROPERTY_PROTOCOL    => HM_PROTOCOL_BIDCOSRF,
                                HM_PROPERTY_TYPE        => HM_TYPE_DIMMER,
                          ),
             );
        }
	 * @endcocde
	 *
	 * @return string Liefert Array mit den Daten der Mess Sensoren
	 */

	function get_HomematicConfiguration() {
		return array(
            'Licht_Küche'    => array(HM_PROPERTY_SERIAL      => 'IEQ0004711',
                                      HM_PROPERTY_CHANNEL     => 1,
                                      HM_PROPERTY_PROTOCOL    => HM_PROTOCOL_BIDCOSRF,
                                      HM_PROPERTY_TYPE        => HM_TYPE_LIGHT,
                                ),
			);
		);
	}

	/** @}*/
?>