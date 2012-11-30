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

	/**@defgroup ipscam_configuration IPSCam Konfiguration
	 * @ingroup ipscam
	 * @{
	 *
	 * @file          IPSCam_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.08.2012<br/>
	 *
	 * Konfigurations File für IPSCam
	 *
	 */

	/**
	 *
	 * Definition der Kameras
	 *
	 * Die Konfiguration erfolgt in Form eines Arrays, für jede Kamera wird ein Eintrag im Array erzeugt.
	 *   IPSCAM_PROPERTY_NAME - Name der Kamera
	 *
	 *   IPSCAM_PROPERTY_TYPE        - Type der Kamera (unterstüzte Werte: IPSCAM_TYPE_MOVABLECAM und IPSCAM_TYPE_FIXEDCAM)
	 *
	 *   IPSCAM_PROPERTY_COMPONENT   - Kamera Component Definition 
	 *
	 *   IPSCAM_PROPERTY_SWITCHPOWER - ID der Varible, die für das Schalten der Stromversorgung verwendet wird [optional]
	 *   IPSCAM_PROPERTY_SWITCHWLAN  - ID der Varible, die für das Schalten des WLAN verwendet wird [optional]
	 *
	 *   IPSCAM_PROPERTY_PREDEFPOS1  - Bezeichnung für vordefinierte Kameraposition 1 [optional]
	 *   IPSCAM_PROPERTY_PREDEFPOS2  - Bezeichnung für vordefinierte Kameraposition 2 [optional]
	 *   IPSCAM_PROPERTY_PREDEFPOS3  - Bezeichnung für vordefinierte Kameraposition 3 [optional]
	 *   IPSCAM_PROPERTY_PREDEFPOS4  - Bezeichnung für vordefinierte Kameraposition 4 [optional]
	 *
	 *   IPSCAM_PROPERTY_COMMAND1    - Name für vordefinierte Kamera Action 1 [optional]
	 *   IPSCAM_PROPERTY_COMMAND2    - Name für vordefinierte Kamera Action 2 [optional]
	 *   IPSCAM_PROPERTY_COMMAND3    - Name für vordefinierte Kamera Action 3 [optional]
	 *   IPSCAM_PROPERTY_COMMAND4    - Name für vordefinierte Kamera Action 4 [optional]
	 *
	 *   IPSCAM_PROPERTY_ACTION1     - ActionScript für vordefinierte Kamera Action 1 (alternativ auch IPSComponentSensor Definition möglich) [optional]
	 *   IPSCAM_PROPERTY_ACTION2     - ActionScript für vordefinierte Kamera Action 2 (alternativ auch IPSComponentSensor Definition möglich) [optional]
	 *   IPSCAM_PROPERTY_ACTION3     - ActionScript für vordefinierte Kamera Action 3 (alternativ auch IPSComponentSensor Definition möglich) [optional]
	 *   IPSCAM_PROPERTY_ACTION4     - ActionScript für vordefinierte Kamera Action 4 (alternativ auch IPSComponentSensor Definition möglich) [optional]
	 *
	 * Eine ausführliche Beispielliste findet sich auch im Example Ordner
	 *
	 * Beispiel:
	 * @code
        function IPSCam_GetCamConfiguration() {
          return array(
            0    => array(IPSCAM_PROPERTY_NAME        => 'Wohnzimmer',
                          IPSCAM_PROPERTY_TYPE        => IPSCAM_TYPE_MOVABLECAM,
                          IPSCAM_PROPERTY_COMPONENT   => 'IPSComponentCam_Edimax,192.168.0.14,username,password',
                          IPSCAM_PROPERTY_SWITCHPOWER => '11625',
                          IPSCAM_PROPERTY_SWITCHWLAN  => '52861',
                          IPSCAM_PROPERTY_PREDEFPOS1  => 'Decke',
                          IPSCAM_PROPERTY_PREDEFPOS2  => 'Wohnzimmer',
                          IPSCAM_PROPERTY_PREDEFPOS3  => 'Stiege',
                          IPSCAM_PROPERTY_COMMAND1    => 'Licht Esstisch',
                          IPSCAM_PROPERTY_ACTION1     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,Esstisch',
                          IPSCAM_PROPERTY_COMMAND2    => 'Licht Wohnzimmer',
                          IPSCAM_PROPERTY_ACTION2     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,Wohnzimmer',
                          IPSCAM_PROPERTY_COMMAND3    => 'Licht Wohnbereich',
                          IPSCAM_PROPERTY_ACTION3     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,Wohnbereich',
                          IPSCAM_PROPERTY_COMMAND4    => 'Terrasse',
                          IPSCAM_PROPERTY_ACTION4     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,TerrasseHauswand',
             );
        }
	 * @endcocde
	 *
	 * @return string Liefert Array mit den Kameras
	 */
	function IPSCam_GetConfiguration() {
		return array(
			0    => array(IPSCAM_PROPERTY_NAME        => 'Eingangstor',
			              IPSCAM_PROPERTY_TYPE        => IPSCAM_TYPE_FIXEDCAM,
			              IPSCAM_PROPERTY_COMPONENT   => 'IPSComponentCam_Axis,192.168.0.22,xxx,xxx',
			              IPSCAM_PROPERTY_SWITCHPOWER => '',
			              IPSCAM_PROPERTY_SWITCHWLAN  => '52861',
			              ),
			1    => array(IPSCAM_PROPERTY_NAME        => 'Garten',
			              IPSCAM_PROPERTY_TYPE        => IPSCAM_TYPE_FIXEDCAM,
			              IPSCAM_PROPERTY_COMPONENT   => 'IPSComponentCam_Vivotek,192.168.0.4,xxx,xxx',
			              IPSCAM_PROPERTY_SWITCHPOWER => '',
			              IPSCAM_PROPERTY_SWITCHWLAN  => '',
			              IPSCAM_PROPERTY_COMMAND1    => 'Licht Aussenbereich',
			              IPSCAM_PROPERTY_ACTION1     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleGroupByName,Aussen',
			              IPSCAM_PROPERTY_COMMAND2    => 'Licht Garten',
			              IPSCAM_PROPERTY_ACTION2     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,TerrasseGarten',
			              IPSCAM_PROPERTY_COMMAND3    => 'Licht Terrasse',
			              IPSCAM_PROPERTY_ACTION3     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,TerrasseHauswand',
			              IPSCAM_PROPERTY_COMMAND4    => 'Licht Pergola',
			              IPSCAM_PROPERTY_ACTION4     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,TerrassePergola',
			              ),
			2    => array(IPSCAM_PROPERTY_NAME        => 'Wohnzimmer',
			              IPSCAM_PROPERTY_TYPE        => IPSCAM_TYPE_MOVABLECAM,
			              IPSCAM_PROPERTY_COMPONENT   => 'IPSComponentCam_Edimax,192.168.0.14,xxx,xxx',
			              IPSCAM_PROPERTY_SWITCHPOWER => '11625',
			              IPSCAM_PROPERTY_SWITCHWLAN  => '52861',
			              IPSCAM_PROPERTY_PREDEFPOS1  => 'Decke',
			              IPSCAM_PROPERTY_PREDEFPOS2  => 'Wohnzimmer',
			              IPSCAM_PROPERTY_PREDEFPOS3  => 'Stiege',
			              IPSCAM_PROPERTY_COMMAND1    => 'Licht Esstisch',
			              IPSCAM_PROPERTY_ACTION1     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,Esstisch',
			              IPSCAM_PROPERTY_COMMAND2    => 'Licht Wohnzimmer',
			              IPSCAM_PROPERTY_ACTION2     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,Wohnzimmer',
			              IPSCAM_PROPERTY_COMMAND3    => 'Licht Wohnbereich',
			              IPSCAM_PROPERTY_ACTION3     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,Wohnbereich',
			              IPSCAM_PROPERTY_COMMAND4    => 'Terrasse',
			              IPSCAM_PROPERTY_ACTION4     => 'IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,TerrasseHauswand',
			             ),
		);
	}

	/**
	 * Höhe des HTML Elements für kleine Streams im WebFront
	 */
	define ("IPSCAM_HEIGHT_SMALL",    340);

	/**
	 * Höhe des HTML Elements für normale Streams im WebFront
	 */
	define ("IPSCAM_HEIGHT_MIDDLE",   520);

	/**
	 * Höhe des HTML Elements für große Streams im WebFront
	 */
	define ("IPSCAM_HEIGHT_LARGE",    800);

	/**
	 * Breite der HTML Elemente in der Mobile GUI (Auflösung des Displays)
	 */
	define ("IPSCAM_WIDTH_MOBILE",    960);

	/** @}*/
?>