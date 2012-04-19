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

	/**@addtogroup IPSWecker
	 * @{
	 *
	 * @file          IPSWecker_Constants.inc.php
	 * @author        Andr� Czwalina
	 * @version
	 * Version 1.00.0, 01.04.2012<br/>
	 *
	 * Konstanten Definitionen f�r IPSWecker
	 *
	 */
	define ("c_WeckerCircles",				"Weckzeiten");
	define ("c_WeckerCircle",				"Weckzeit");
	define ("c_WeckerCircle_1",			"Weckzeit_1");
	define ("c_WeckerCircle_2",			"Weckzeit_2");
	define ("c_WeckerCircle_3",			"Weckzeit_3");
	define ("c_WeckerCircle_4",			"Weckzeit_4");
	define ("c_WeckerCircle_5",			"Weckzeit_5");
	define ("c_WeckerCircle_6",			"Weckzeit_6");
	define ("c_WeckerCircle_7",			"Weckzeit_7");
	define ("c_WeckerCircle_8",			"Weckzeit_8");
	define ("c_WeckerCircle_9",			"Weckzeit_9");
	define ("c_WeckerCircle_10",			"Weckzeit_10");

//	define ("c_Control_NextTime",				"NextTime");
	define ("c_Control_Wochentag",			"Wochentag");
	define ("c_Control_MeldungID",			"letzte Meldungs ID");
	define ("c_Control_Meldungen",			"Meldungen");

// Globale Daten
	define ("c_Control_Name",					"Name");
	define ("c_Control_Tag",					"Tag");
	define ("c_Control_Stunde",				"Stunde");
	define ("c_Control_Minute",				"Minute");
	define ("c_Control_Global",				"Wochenwecker");
	define ("c_Control_Active",				"Tageswecker");
	define ("c_Control_Feiertag",				"Feiertag");
	define ("c_Control_Frost",					"Frost");
	define ("c_Control_Urlaub",				"Urlaub");
	define ("c_Control_Schlummer",			"Schlummer");
	define ("c_Control_End",					"End");

// Wochenwecker Daten
	define ("c_Control_Uebersicht",			"Uebersicht");
	define ("c_Control_Optionen",				"Optionen");
	define ("c_Control_Urlaubszeit",			"Urlaubszeit");
	define ("c_Control_Mo",						"Montag");
	define ("c_Control_Di",						"Dienstag");
	define ("c_Control_Mi",						"Mittwoch");
	define ("c_Control_Do",						"Donnerstag");
	define ("c_Control_Fr",						"Freitag");
	define ("c_Control_Sa",						"Samstag");
	define ("c_Control_So",						"Sonntag");
//	define ("c_Control_Mo_Active",			"Mo_Active");
//	define ("c_Control_Di_Active",			"Di_Active");
//	define ("c_Control_Mi_Active",			"Mi_Active");
//	define ("c_Control_Do_Active",			"Do_Active");
//	define ("c_Control_Fr_Active",			"Fr_Active");
//	define ("c_Control_Sa_Active",			"Sa_Active");
//	define ("c_Control_So_Active",			"So_Active");


	define ("c_Control_WeckerName",			"Wecker_Name");


	define ("c_Program_Montag",			"Montag");
	define ("c_Program_Dienstag",			"Dienstag");
	define ("c_Program_Mittwoch",			"Mittwoch");
	define ("c_Program_Donnerstag",		"Donnerstag");
	define ("c_Program_Freitag",			"Freitag");
	define ("c_Program_Samstag",			"Samstag");
	define ("c_Program_Sonntag",			"Sonntag");
	define ("c_Program_Werkstags",		"Werktags");
	define ("c_Program_Wochenende",		"Wochenende");
	define ("c_Program_Woche",				"Woche");

	define ("c_ProgramId_Montag",			00);
	define ("c_ProgramId_Dienstag",		01);
	define ("c_ProgramId_Mittwoch",		02);
	define ("c_ProgramId_Donnerstag",	03);
	define ("c_ProgramId_Freitag",		04);
	define ("c_ProgramId_Samstag",		05);
	define ("c_ProgramId_Sonntag",		06);
	define ("c_ProgramId_Feiertag",		07);
	define ("c_ProgramId_Urlaub",			08);
	define ("c_ProgramId_Frost",			09);
	define ("c_ProgramId_Global",			10);
	define ("c_ProgramId_Snooze",			11);
	define ("c_ProgramId_End",				12);

	define ("c_Format_StartTime",			'H:i');
//	define ("c_Format_NextDate",			'Y.m.d');
//	define ("c_Format_NextTime",			'H:i');
//	define ("c_Format_LastDate",			'Y.m.d');
	define ("c_Format_LastTime",			'H:i:s');

	define ("c_Property_Name",				'Name');
	define ("c_Property_FrostTemp",		'FrostTemperatur');
	define ("c_Property_FrostSensor",   'FrostSensor');
	define ("c_Property_FrostTime",     'FrostTime');
	define ("c_Property_SnoozeTime", 	'SnoozeTime');
	define ("c_Property_EndTime",  		'EndTime');
	define ("c_Property_StopSensor",  	'StopSensor');
	define ("c_Property_Schichtgruppe", 'Schichtgruppe');


//Webfront
	define ("c_Table_Global",  			'Wochenwecker');
	define ("c_Table_Day",  				'Tag(e)');
	define ("c_Table_Hour",  				'Stunde');
	define ("c_Table_Minute",  			'Minute');
	define ("c_Table_Feature",		 		'Globale Funktionen');
	define ("c_Table_Active",  			'Aktiv');
	define ("c_Table_HolidayTime",  		'Urlaubszeiten');
	define ("c_Table_Holiday",  			'Im Urlaub');
	define ("c_Table_Feasts",  			'An Feiertagen');
	define ("c_Table_Freeze",  			'Bei Frost');
	define ("c_Table_Snooze",           'Schlummerfunktion');
	define ("c_Table_End",              'Endefunktion');
	define ("c_Table_Overview",         '�bersicht');
	define ("c_Table_AlarmName",        'Wecker Name');
//	define ("c_Table_",  					'');


	define ("c_Asso_On", 	 				'Ein');
	define ("c_Asso_Off",  					'Aus');
	define ("c_Asso_Weck", 					'Wecken');
	define ("c_Asso_NoWeck", 				'Nicht Wecken');
	define ("c_Asso_Active", 				'Aktiv');
	define ("c_Asso_InActive",				'Inktiv');
	define ("c_Asso_PrevWeck",				'Fr�her');
	define ("c_Asso_NormWeck",				'Normal');

	/** @}*/
?>