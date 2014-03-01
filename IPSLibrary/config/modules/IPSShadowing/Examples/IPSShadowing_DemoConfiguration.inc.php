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

	/** Temperatursensor Innen
	 *
	 * Definition des Innentemperatur Sensors, die Konstante muß auf eine Variable verweisen, die die aktuelle Innentemperatur
	 * als Float Value enthält.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_TEMPSENSORINDOOR",		"");

	/** Temperatursensor Aussen
	 *
	 * Definition des Aussentemperatur Sensors, die Konstante muß auf eine Variable verweisen, die die aktuelle Aussentemperatur
	 * als Float Value enthält.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_TEMPSENSOROUTDOOR",	"");

	/** Helligkeitssensor
	 *
	 * Definition des Helligkeits Sensors, die Konstante muß auf eine Variable verweisen, die die aktuelle Helligkeit
	 * als Integer oder Float Value enthält.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_BRIGHTNESSSENSOR",	'');

	/** Regensensor
	 *
	 * Definition des Regen Sensors, die Konstante muß auf eine Variable verweisen, die den Wert des Sensors als 
	 * boolschen Wert enthält.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_RAINSENSOR",		"");

	/** Windsensor
	 *
	 * Definition des Wind Sensors, die Konstante muß auf eine Variable verweisen, die den Wert des Sensors als 
	 * Float Wert mit Angabe in "kmh" enthält, 
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_WINDSENSOR",		"");

	/** Profil Wetterdefinition / Klassifiktation
	 *
	 * Definition/Masseinheit des Windlevels.
	 * Einstellung:   false       Vergleich der Windgeschwindigkeit mit dem Windlevel in km/h
	 *                true        Vergleich der Windgeschwindigkeit mit dem Windlevel in Beaufort
	 *
	 * Dieser Parameter kann jederzeit geändert werden.
	 * Für die Übernahme der Änderung ist eine erneute Installation über den ModuleManager oder ModuleManagerGUI notwendig.
	 */
	define ("IPSSHADOWING_WINDLEVEL_CLASSIFICATION",		false);

	/** Anwesenheits Flag
	 *
	 * Definition des Anwesenheits Flags, die Konstante muß auf eine Variable verweisen, die den aktuellen Anwesenheits Status als
	 * boolean Wert enthält (true bedeutet Anwesend).
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_PRESENT",			'');

	/** Abwesenheits Flag
	 *
	 * Definition des Abwesenheits Flags, die Konstante muß auf eine Variable verweisen, die den aktuellen Abwesenheits Status als
	 * boolean Wert enthält (true bedeutet Abwesend).
	 * Diese Variable kann alternativ zu dem Anwesenheits Flag gesetzt werden.
	 * Die Verlinkung der Variable erfolgt entweder direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_ABSENCE",			"");

	/** Zeitpunkt Sonnenaufgang
	 *
	 * Definition des Tagesbeginn Zeitpunktes, die Konstante muß auf eine Variable verweisen, die die Zeit in der Form
	 * hh:mm enthält.
	 * Verlinkung erfolgt direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_TWILIGHTSUNRISE",			"Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values.SunriseBegin");

	/** Zeitpunkt Sonnenuntergang
	 *
	 * Definition des Tagesend Zeitpunktes, die Konstante muß auf eine Variable verweisen, die die Zeit in der Form
	 * hh:mm enthält.
	 * Verlinkung erfolgt direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_TWILIGHTSUNSET",			"Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values.SunriseEnd");
	
	/** Zeitpunkt limited Sonnenaufgang
	 * 
	 * Definition des "limited" Tagesbeginn Zeitpunktes, die Konstante muß auf eine Variable verweisen, die die Zeit in der Form
	 * hh:mm enthält. Durch diese Variable ist es möglich, dass der Tagesbeginn in bestimmten Grenzen liegen muß.
	 * Verlinkung erfolgt direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_TWILIGHTSUNRISELIMITED",	"Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values.SunriseBeginLimited");

	/** Zeitpunkt limited Sonnenuntergang
	 *
	 * Definition des "limited" Tagesend Zeitpunktes, die Konstante muß auf eine Variable verweisen, die die Zeit in der Form
	 * hh:mm enthält. Durch diese Variable ist es möglich, dass der Tagesbeginn in bestimmten Grenzen liegen muß.
	 * Verlinkung erfolgt direkt durch Angabe der ID oder durch Angabe des Pfades.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_TWILIGHTSUNSETLIMITED",	"Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values.SunriseEndLimited");

	/**
	 * Angabe des Breitengrades zur Berechnung des Sonnenstandes
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ('IPSSHADOWING_LATITUDE', IPSTWILIGHT_LATITUDE);

	/**
	* Angabe des Längengrades zur Berechnung des Sonnenstandes
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	*/
	define ('IPSSHADOWING_LONGITUDE', IPSTWILIGHT_LONGITUDE);

	/**
	 * Ausrichtung des Gebäudes
	 * 
	 * Dieser Wert spezifiziert die Abweichung von der Ausrichtung des Gebäudes Richtung Süden in Grad.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ('IPSSHADOWING_BUILDINGORIENTATION',   -15);
	
	/**
	 * Verhältnis der Gebäudewände
	 *
	 * Mit diesem Parameter kann das Verhältnis der Länge der Gebäudewände in der Grafik verändert werden. 
	 *
	 * 0 bedeutet dass alle Gebäudewände gleich lang sind, bei positiven Werten wird die südliche Seite des Gebäudes
	 * breiter und umgekehrt.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ('IPSSHADOWING_BUILDINGRELATION',      10);
	

	/**
	 * Definiert die Anzahl der Meldungen, die im Applikation Logging Window angezeigt werden.
	 *
	 * Dieser Parameter kann jederzeit geändert werden, keine Installation erforderlich.
	 */
	define ("IPSSHADOWING_LOGMESSAGECOUNT",				30);


	
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