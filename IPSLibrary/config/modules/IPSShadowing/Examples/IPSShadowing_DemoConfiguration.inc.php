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

	/**@defgroup ipsshadowing_configuration IPSShadowing Konfiguration
	 * @ingroup ipsshadowing
	 * @{
	 *
	 * @file          IPSShadowing_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 19.03.2012<br/>
	 *
	 * Konfigurations File für IPSShadowing
	 *
	 */

	IPSUtils_Include ("IPSShadowing_Constants.inc.php",      "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSTwilight_Configuration.inc.php", "IPSLibrary::config::modules::Weather::IPSTwilight");

	/**
	 * Definition des Innenemperatur Sensors, die Konstante muß auf eine Variable verweisen, die die aktuelle Innentemperatur
	 * als Float Value enthält.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_TempSensorIndoor",		"Program.WeatherStation.IndoorTemperature");

	/**
	 * Definition des Aussentemperatur Sensors, die Konstante muß auf eine Variable verweisen, die die aktuelle Aussentemperatur
	 * als Float Value enthält.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_TempSensorOutdoor",	"Program.WeatherStation.OutdoorTemperature");

	/**
	 * Definition des Helligkeits Sensors, die Konstante muß auf eine Variable verweisen, die die aktuelle Helligkeit
	 * als Integer oder Float Value enthält.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_BrightnessSensor",	'');

	/**
	 * Definition des Anwesenheits Flags, die Konstante muß auf eine Variable verweisen, die den aktuellen Anwesenheits Status als
	 * boolean Wert enthält (true bedeutet Anwesend).
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_PresentPath",			'');

	/**
	 * Definition des Abwesenheits Flags, die Konstante muß auf eine Variable verweisen, die den aktuellen Abwesenheits Status als
	 * boolean Wert enthält (true bedeutet Abwesend).
	 * Diese Variable kann alternativ zu dem Anwesenheits Flag gesetzt werden.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_AbsencePath",			"Program.Presence.OutOfHome");

	/**
	 * Definition des Tagesbeginn Zeitpunktes, die Konstante muß auf eine Variable verweisen, die die Zeit in der Form
	 * hh:mm enthält.
	 * Verlinkung erfolgt direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_TwilightBgnOfDayPath",			"Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values.SunriseBegin");

	/**
	 * Definition des Tagesend Zeitpunktes, die Konstante muß auf eine Variable verweisen, die die Zeit in der Form
	 * hh:mm enthält.
	 * Verlinkung erfolgt direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_TwilightEndOfDayPath",			"Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values.SunriseEnd");
	
	/**
	 * Definition des "limited" Tagesbeginn Zeitpunktes, die Konstante muß auf eine Variable verweisen, die die Zeit in der Form
	 * hh:mm enthält. Durch diese Variable ist es möglich, dass der Tagesbeginn in bestimmten Grenzen liegen muß.
	 * Verlinkung erfolgt direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_TwilightLimitedBgnOfDayPath",	"Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values.SunriseBeginLimited");

	/**
	 * Definition des "limited" Tagesend Zeitpunktes, die Konstante muß auf eine Variable verweisen, die die Zeit in der Form
	 * hh:mm enthält. Durch diese Variable ist es möglich, dass der Tagesbeginn in bestimmten Grenzen liegen muß.
	 * Verlinkung erfolgt direkt durch Angabe der ID oder durch Angabe des Pfades.
	 */
	define ("c_Setting_TwilightLimitedEndOfDayPath",	"Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values.SunriseEndLimited");

	/**
	 * Angabe des Breitengrades zur Berechnung des Sonnenstandes
	 */
	define ('IPSSHADOWING_LATITUDE', IPSTWILIGHT_LATITUDE);

	/**
	* Angabe des Längengrades zur Berechnung des Sonnenstandes
	*/
	define ('IPSSHADOWING_LONGITUDE', IPSTWILIGHT_LONGITUDE);
	

	/**
	 * Definiert die Anzahl der Meldungen, die im Applikation Logging Window angezeigt werden.
	 */
	define ("c_LogMessage_Count",				30);

	
	/**
	 *
	 * Definition der Beschattungs Elemente
	 * Die Konfiguration erfolgt in Form eines Arrays, für jedes Beschattungs Device wird ein Eintrag im Array erzeugt.
	 * 
	 * Der Eintrag "c_Property_Name" spezifiziert den Namen des Beschattungs Elements, der im WebFront und in den Log's angezeigt
	 * wird.
	 *
	 * Der Eintrag "c_Property_Component" spezifiziert die Hardware, es kann jeder "Shutter" Component String Konstruktor
	 * angegeben werden. Detailiertere Informationen kann man auch im core Modul IPSComponent finden.
	 *
	 *
	 * Beispiel:
	 * @code
        function get_ShadowingConfiguration() {
          return array(
            c_ShadowingDevice_1  =>  array(
               c_Property_ShadowingType     => c_ShadowingType_Jalousie,
               c_Property_Name              => 'Küche',
               c_Property_Component         => 'IPSComponentShutter_Dummy,12345',
               c_Property_TimeOpening       => 35,
               c_Property_TimeClosing       => 35,
               c_Property_TimeDimoutUp      => 2,
               c_Property_TimeDimoutDown    => 3,
               c_Property_TimePause         => 1,
               c_Property_TempSensorOutdoor => '',
               c_Property_TempSensorIndoor  => '',
             ));
        }
	 * @endcocde
	 *
	 * @return string Liefert Array mit Bewässerungs Kreisen
	 */
	function get_ShadowingConfiguration() {
		return array(
			c_ShadowingDevice_1 =>	array(
				c_Property_ShadowingType	=> c_ShadowingType_Jalousie,
				c_Property_Name				=> 'Küche',
				c_Property_Component		=> 'IPSComponentShutter_Dummy,12345',
				c_Property_TimeOpening		=> 35,
				c_Property_TimeClosing		=> 35,
				c_Property_TimeDimoutUp		=> 2,
				c_Property_TimeDimoutDown	=> 3,
				c_Property_TimePause		=> 1,
				c_Property_TempSensorIndoor	=> '',
				),
			c_ShadowingDevice_2 =>	array(
				c_Property_ShadowingType	=> 	c_ShadowingType_Jalousie,
				c_Property_Name				=> 'Terrasse',
				c_Property_Component		=> 'IPSComponentShutter_Dummy,12345',
				c_Property_TimeOpening		=> 	60,
				c_Property_TimeClosing		=> 	60,
				c_Property_TimeDimoutUp		=> 	2,
				c_Property_TimeDimoutDown	=> 	3,
				c_Property_TimePause		=> 	1,
				c_Property_TempSensorIndoor	=> '',
				),
			c_ShadowingDevice_3 =>	array(
				c_Property_ShadowingType	=> 	c_ShadowingType_Jalousie,
				c_Property_Name				=> 'Wohnzimmer',
				c_Property_Component		=> 'IPSComponentShutter_Dummy,12345',
				c_Property_TimeOpening		=> 	60,
				c_Property_TimeClosing		=> 	60,
				c_Property_TimeDimoutUp		=> 	2,
				c_Property_TimeDimoutDown	=> 	4,
				c_Property_TimePause		=> 	1,
				c_Property_TempSensorIndoor	=> '',
				),
			c_ShadowingDevice_4 =>	array(
				c_Property_ShadowingType	=> 	c_ShadowingType_Shutter,
				c_Property_Name				=> 'Kinderzimmer 1',
				c_Property_Component		=> 'IPSComponentShutter_Dummy,12345',
				c_Property_TimeOpening		=> 	60,
				c_Property_TimeClosing		=> 	60,
				c_Property_TimeDimoutUp		=> 	2,
				c_Property_TimeDimoutDown	=> 	3,
				c_Property_TimePause		=> 	1,
				c_Property_TempSensorIndoor	=> '',
				),
			c_ShadowingDevice_5 =>	array(
				c_Property_ShadowingType	=> 	c_ShadowingType_Shutter,
				c_Property_Name				=> 'Kinderzimmer 2',
				c_Property_Component		=> 'IPSComponentShutter_Dummy,12345',
				c_Property_TimeOpening		=> 	60,
				c_Property_TimeClosing		=> 	60,
				c_Property_TimeDimoutUp		=> 	2,
				c_Property_TimeDimoutDown	=> 	3,
				c_Property_TimePause		=> 	1,
				c_Property_TempSensorIndoor => '',
				),

			c_ShadowingDevice_6 =>	array(
				c_Property_ShadowingType	=> 	c_ShadowingType_Marquees,
				c_Property_Name				=> 'Markise Links',
				c_Property_Component		=> 'IPSComponentShutter_Dummy,12345',
				c_Property_TimeOpening		=> 	70,
				c_Property_TimeClosing		=> 	70,
				c_Property_TimePause		=> 	1,
				c_Property_TempSensorIndoor => '',
				),
			c_ShadowingDevice_7 =>	array(
				c_Property_ShadowingType	=> 	c_ShadowingType_Marquees,
				c_Property_Name				=> 'Markise Rechts',
				c_Property_Component		=> 'IPSComponentShutter_Dummy,12345',
				c_Property_TimeOpening		=> 	70,
				c_Property_TimeClosing		=> 	70,
				c_Property_TimePause		=> 	1,
				c_Property_TempSensorIndoor => '',
				),

	   );
	}
	/** @}*/
?>