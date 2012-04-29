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

	/**@addtogroup IPSSchaltuhr
	 * @{
	 *
	 * @file          IPSSchaltuhr_Constants.inc.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 * Konstanten Definitionen für IPSSchaltuhr
	 *
	 */
	define ("c_ZSUCircles",					"Zeitschaltuhren");
	define ("c_ZSUCircle",					"Zeitschaltuhr_");

	define ("c_Control_ZSUName",			"ZSU_Name");
	define ("c_Control_MeldungID",		"letzte Meldungs ID");
	define ("c_Control_Meldungen",		"Meldungen");

// Globale Daten
	define ("c_Control_Name",				"Name");
	define ("c_Control_StartTag",       "StartTag");
	define ("c_Control_StopTag",       	"StopTag");
	define ("c_Control_StartStunde",		"StartStunde");
	define ("c_Control_StopStunde",		"StopStunde");
	define ("c_Control_StartMinute",		"StartMinute");
	define ("c_Control_StopMinute",		"StopMinute");
	define ("c_Control_StartAktiv",		"StartAktiv");
	define ("c_Control_StopAktiv",		"StopAktiv");
	define ("c_Control_RunAktiv",			"RunActive");
	define ("c_Control_Uebersicht",		"Uebersicht");


//	define ("c_Control_Tag",					"Tag");
//	define ("c_Control_LTag",					"LTag");
//	define ("c_Control_Global",				"WochenZSU");
//	define ("c_Control_Aktiv",				"Aktiv");
//	define ("c_Control_Feiertag",				"Feiertag");
//	define ("c_Control_Frost",					"Frost");
//	define ("c_Control_Urlaub",				"Urlaub");
//	define ("c_Control_Schlummer",			"Schlummer");
//	define ("c_Control_End",					"End");

// WochenZSU Daten
//	define ("c_Control_Uebersicht",		"Uebersicht");
	define ("c_Control_StartZeit",		"StartZeit");
	define ("c_Control_StopZeit",			"StopZeit");
	define ("c_Control_TagAktiv",			"TagAktiv");
	define ("c_Control_SollAusgang",		"SollAusgang");
	define ("c_Control_IstAusgang",		"IstAusgang");

	define ("c_Program_Montag",			"Montag");
	define ("c_Program_Dienstag",			"Dienstag");
	define ("c_Program_Mittwoch",			"Mittwoch");
	define ("c_Program_Donnerstag",		"Donnerstag");
	define ("c_Program_Freitag",			"Freitag");
	define ("c_Program_Samstag",			"Samstag");
	define ("c_Program_Sonntag",			"Sonntag");
	define ("c_Program_On", 	 			'Ein');
	define ("c_Program_Off",  				'Aus');

	define ("c_Format_StartTime",			'H:i');
	define ("c_Format_LastTime",			'H:i:s');

	define ("c_Property_Name",				'Name');
	define ("c_Property_StartSensoren", 'StartSensoren');
	define ("c_Property_StartSensor", 	'StartSensor');
	define ("c_Property_StopSensoren", 	'StopSensoren');
	define ("c_Property_StopSensor", 	'StopSensor');
	define ("c_Property_RunSensoren",   'RunSensoren');
	define ("c_Property_RunSensor",	   'RunSensor');

	define ("c_Property_Sensor",   		'Sensor');
	define ("c_Property_SensorID", 		'SensorID');
	define ("c_Property_Condition",		'Condition');
	define ("c_Property_Value",  			'Value');

//	define ("c_Property_StopName",	   'StopName');
//	define ("c_Property_StopSensor", 	'StopSensor');
//	define ("c_Property_StopSensorID", 	'StopSensorID');
//	define ("c_Property_StopCondition",	'StopCondition');
//	define ("c_Property_StopValue",  	'StopValue');

//	define ("c_Property_RunName",	   	'RunName');
//	define ("c_Property_RunSensor",   	'RunSensor');
//	define ("c_Property_RunSensorID",  	'RunSensorID');
//	define ("c_Property_RunCondition",	'RunCondition');
//	define ("c_Property_RunValue",	  	'RunValue');

//	define ("",  					'');

//Webfront
	define ("c_WFC_Name",        			'Schaltuhr Name');
	define ("c_WFC_StartTag",  			'Start Tag');
	define ("c_WFC_StopTag",  				'Stop Tag');
	define ("c_WFC_StartMinute",  		'StartMinute');
	define ("c_WFC_StopMinute",		 	'Stop Minute');
	define ("c_WFC_StartStunde",  		'Start Stunde');
	define ("c_WFC_StopStunde",  			'Stop Stunde');

	define ("c_WFC_RunAktiv",  			'In Betrieb Bedingung');
	define ("c_WFC_StartAktiv",  			'Start Bedingung');
	define ("c_WFC_StopAktiv",  			'Stop Bedingung');
	define ("c_WFC_Snooze",           	'Schlummerfunktion');
	define ("c_WFC_End",              	'Endefunktion');
	define ("c_WFC_Uebersicht",         'Übersicht');
//	define ("c_WFC_",  					'');



	/** @}*/
?>