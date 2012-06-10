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

	/**@addtogroup IPSHealth
	 * @{
	 *
	 * @file          IPSHealth_Constants.inc.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 * Konstanten Definitionen für IPSHealth
	 *
	 */

	define ("c_HealthCircles",				"Circles");
	define ("c_HealthCircle",				"Circle_");
	define ("c_HealthTimer",				"Timer");
	define ("c_HealthTimeout",				"Timeout");
	define ("c_HealthVariables",			"Variable");
	define ("c_CircleName",             "Name");
	
	define ("c_Control_SysInfo",			"System Info");
	define ("c_Control_Statistik",      "Statistik");
	define ("c_Control_DBMonitor",      "DB-Monitoring");
	define ("c_Control_DBWartung",      "DB-Wartung");
	define ("c_Control_Server",         "Server");
	define ("c_Control_HightChart",     "HightChart");
	define ("c_Control_MeldungID",		"letzte Meldungs ID");
	define ("c_Control_Meldungen",		"Meldungen");

	define ("c_Control_Uebersicht",		"Uebersicht");
	define ("c_Control_UebersichtCircle",		"Uebersicht_Circle");
	define ("c_Control_HCQueue",        "HighChart Queue");
	define ("c_Control_BetriebStd",     "Betriebsstunden");
	define ("c_Control_Info",           "Information");
	define ("c_Control_Select",         "Select");
	define ("c_Control_Error",          "Fehler");
	define ("c_Control_Modul",          "IPSHealth Updaten");
	define ("c_Control_Version",        "IPSHealth Version");

	define ("c_Property_Timeout",			'Timeout');
	define ("c_Property_Variables", 		'Variablen');

	define ("c_Property_Categorys",  	'IPS Categorys');
	define ("c_Property_Events",  		'IPS Events');
	define ("c_Property_Instances",  	'IPS Instances');
	define ("c_Property_Links",  			'IPS Links');
	define ("c_Property_Modules",  		'IPS Modules');
	define ("c_Property_Objects",  		'IPS Objects');
	define ("c_Property_Profiles",  		'IPS Profiles');
	define ("c_Property_Scripts",  		'IPS Scripts');
	define ("c_Property_Variable",  		'IPS Variables');
	define ("c_Property_DB_Groesse",  	'IPS-DB Größe');
	define ("c_Property_DB_Zuwachs",  	'IPS-DB Zuwachs');
	define ("c_Property_DB_Fehler",  	'DB-Fehler');
	define ("c_Property_lastWrite",  	'letzter Schreibvorgang vor');
	define ("c_Property_LogDB_Groesse",	'aktuelle DB Größe');
	define ("c_Property_Uptime",        'Laufzeit');
	define ("c_Property_BetriebStdI",   'Betriebszeit');

	define ("c_Property_ServerZeit",    'Zeit');
	define ("c_Property_ServerHDD",  	'freie HDD Kapazität');
	define ("c_Property_ServerCPU",  	'CPU Auslastung');
	

	define ("c_Property_DBHistory",  	'History');
	define ("c_Property_DBNeuagg",  		'DB Neuaggregation');
	define ("c_Property_DBVarGes",  		'Variablen gesamt');
	define ("c_Property_DBaktVar",  		'in Arbeit');
	define ("c_Property_DBVarReady", 	'Variablen Fertig');
	define ("c_Property_DBSteps",  		'Fortschritt');
	define ("c_Property_DBReady", 		'Beendet');
	define ("c_Property_DBStart", 		'Gestartet');
//	define ("", 			'');
//	define ("", 			'');
//	define ("", 			'');

//Webfront
	define ("c_WFC_Name",        			'Health Name');
	define ("c_WFC_StartTag",  			'Start Tag');
	define ("c_WFC_StopTag",  				'Stop Tag');
	define ("c_WFC_StartMinute",  		'Start Minute');
	define ("c_WFC_StopMinute",		 	'Stop Minute');
	define ("c_WFC_StartStunde",  		'Start Stunde');
	define ("c_WFC_StopStunde",  			'Stop Stunde');

	define ("c_WFC_RunAktiv",  			'Laufzeit Bedingung');
	define ("c_WFC_StartAktiv",  			'Start Bedingung');
	define ("c_WFC_StopAktiv",  			'Stop Bedingung');
	define ("c_WFC_Uebersicht",         'Übersicht');

	define ("c_WFC_Zeit",         		'Zeit');
	define ("c_WFC_Tage",         		'Tag ');
	define ("c_WFC_StartSensor", 	 		'Start Sensor(en)');
	define ("c_WFC_StopSensor", 	 		'Stop Sensor(en)');
	define ("c_WFC_RunSensor",  			'Laufzeit Sensor(en)');
	define ("c_WFC_Ausgang",    			'Ausgangsfunktion');
	define ("c_WFC_SollZustand",        'Soll Zustand');
	define ("c_WFC_IstZustand",         'Ist Zustand');
	define ("c_WFC_EinOhneBeding",      'Aktiv (Bedingung nicht erfüllt)');
	define ("c_WFC_EinMitBeding",       'Aktiv (Bedingung erfüllt)');
	define ("c_WFC_Abgeschaltet",       'Abgeschaltet');
	define ("c_WFC_Legende", 	  	    	'Legende Farben');


	/** @}*/
?>