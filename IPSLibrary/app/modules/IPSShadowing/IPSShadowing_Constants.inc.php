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

	/**@addtogroup ipsshadowing
	 * @{
	 *
	 * @file          IPSShadowing_Constants.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 21.03.2012<br/>
	 *
	 * Definition der Konstanten für IPSShadowing
	 *
	 */

	// Shadowing Controls
	// --------------------------------------------------------------------------
	define ("c_Control_Movement",				"Movement");
	define ("c_Movement_Stop",					"Stop");
	define ("c_Movement_Down",					"Runter");
	define ("c_Movement_Up",					"Hoch");
	define ("c_Movement_MovingIn",				"Ein");
	define ("c_Movement_MovingOut",				"Aus");
	define ("c_Movement_Space",					" ");
	define ("c_Movement_Opened",				"Offen");
	define ("c_Movement_MovedIn",				"Eingefahren");
	define ("c_Movement_25",					"25%%");
	define ("c_Movement_50",					"50%%");
	define ("c_Movement_75",					"75%%");
	define ("c_Movement_90",					"90%%");
	define ("c_Movement_Shadowing",				"Beschattung");
	define ("c_Movement_Dimout",				"Abdunkelung");
	define ("c_Movement_Closed",				"Geschlossen");
	define ("c_Movement_MovedOut",				"Ausgefahren");
	define ("c_Movement_NoAction",				"Keine Aktion");

	define ("c_MovementId_Closed",				0);
	define ("c_MovementId_Dimout",				1);
	define ("c_MovementId_Shadowing",			2);
	define ("c_MovementId_MovedOut",			3);
	define ("c_MovementId_90",					4);
	define ("c_MovementId_75",					5);
	define ("c_MovementId_50",					6);
	define ("c_MovementId_25",					7);
	define ("c_MovementId_Opened",				8);
	define ("c_MovementId_MovedIn",				9);
	define ("c_MovementId_Space",				10);
	define ("c_MovementId_Down",				11);
	define ("c_MovementId_MovingOut",			12);
	define ("c_MovementId_Stop",    			13);
	define ("c_MovementId_Up",					14);
	define ("c_MovementId_MovingIn",			15);
	define ("c_MovementId_NoAction",			16);

	define ("c_Control_ProgramNight",			"ProgramNight");
	define ("c_Control_ProgramDay",				"ProgramDay");
	define ("c_Control_ProgramTemp",			"ProgramTemp");
	define ("c_Control_ProgramPresent",			"ProgramPresent");
	define ("c_Control_ProgramWeather",			"ProgramWeather");
	define ("c_Control_Position",				"Position");
	define ("c_Control_Display",				"Display");
	define ("c_Control_StepsToDo",				"StepsToDo");
	define ("c_Control_Step",					"Step");
	define ("c_Control_StartTime",				"StartTime");
	define ("c_Control_Automatic",				"Automatic");
	define ("c_Control_ManualChange",			"ManualChange");
	define ("c_Control_TempChange",				"TemperatureChange");
	define ("c_Control_TempLastPos",			"TemperatureLastPos");
	define ("c_Control_ProfileTemp",			"ProfileTemp");
	define ("c_Control_ProfileSun",				"ProfileSun");
	define ("c_Control_ProfileWeather",			"ProfileWeather");
	define ("c_Control_ProfileBgnOfDay",		"ProfileBgnOfDay");
	define ("c_Control_ProfileEndOfDay",		"ProfileEndOfDay");
	define ("c_Control_ProfileInfo",			"ProfileInfo");

	define ("c_Control_ProfileName",			"ProfileName");
	define ("c_Control_TempLevelOutShadow",		"TempLevelOutShadow");
	define ("c_Control_TempLevelOutClose",		"TempLevelOutClose");
	define ("c_Control_TempLevelOutOpen",		"TempLevelOutOpen");
	define ("c_Control_TempLevelInShadow",		"TempLevelInShadow");
	define ("c_Control_TempLevelInClose",		"TempLevelInClose");
	define ("c_Control_TempLevelInOpen",		"TempLevelInOpen");
	define ("c_Control_Brightness",				"Brightness");
	define ("c_Control_AzimuthBgn",				"AzimuthBgn");
	define ("c_Control_AzimuthEnd",				"AzimuthEnd");
	define ("c_Control_Elevation",				"Elevation");
	define ("c_Control_Date",					"Date");
	define ("c_Control_Simulation",				"Simulation");
	define ("c_Control_Orientation",			"Orientation");
	define ("c_Control_WindLevel",				"WindLevel");
	define ("c_Control_RainCheck",				"RainCheck");

	define ("c_Control_WorkdayMode",			"WorkdayMode");
	define ("c_Control_WorkdayTime",			"WorkdayTime");
	define ("c_Control_WorkdayOffset",			"WorkdayOffset");
	define ("c_Control_WeekendMode",			"WeekendMode");
	define ("c_Control_WeekendTime",			"WeekendTime");
	define ("c_Control_WeekendOffset",			"WeekendOffset");

	define ("c_Control_ScenarioName",			"ScenarioName");
	define ("c_Control_ScenarioEdit",			"ScenarioEdit");
	define ("c_Control_ScenarioSelect",			"ScenarioSelect");
	define ("c_Control_ScenarioActivate",		"ScenarioActivate");

	define ("c_Control_ProfileTempSelect",		"ProfileTempSelect");
	define ("c_Control_ProfileSunSelect",		"ProfileSunSelect");
	define ("c_Control_ProfileWeatherSelect",	"ProfileWeatherSelect");
	define ("c_Control_ProfileBgnOfDaySelect",	"ProfileBgnOfDaySelect");
	define ("c_Control_ProfileEndOfDaySelect",	"ProfileEndOfDaySelect");

	define ("c_ModeId_Individual",				0);
	define ("c_ModeId_Twillight",				1);
	define ("c_ModeId_LimitedTwillight",		2);

	// Common Settings Controls
	define ("c_Control_MsgPrioTemp",			"MsgPrioTemp");
	define ("c_Control_MsgPrioProg",			"MsgPrioProg");

	define ("c_TempLevel_Ignore",				100);

	define ("c_Color_ProfileActive",			0x880000);

	// Programm Difinition
	// --------------------------------------------------------------------------
	define ("c_Program_Manual",					"Manuell");
	define ("c_Program_Opened",					"Offen");
	define ("c_Program_MovedIn",				"Eingefahren");
	define ("c_Program_OpenedOrShadowing",		"Offen oder Beschattung");
	define ("c_Program_OpenedAndShadowing",		"Offen und Beschattung");
	define ("c_Program_OpenedDay",				"Geöffnet Tag");
	define ("c_Program_OpenedNight",			"Geöffnet Nacht");
	define ("c_Program_Closed",					"Geschlossen");
	define ("c_Program_25",						"25%");
	define ("c_Program_50",						"50%");
	define ("c_Program_75",						"75%");
	define ("c_Program_90",						"90%");
	define ("c_Program_Dimout",					"Geschlossen");
	define ("c_Program_MovedOut",				"Ausgefahren");
	define ("c_Program_MovedOutTemp",			"Ausgefahren Temperatur");
	define ("c_Program_DimoutOrShadowing",		"Schatten oder Geschl");
	define ("c_Program_DimoutAndShadowing",		"Schatten und Geschl.");
	define ("c_Program_LastPosition",			"LastPosition");

	define ("c_ProgramId_Opened",				1);
	define ("c_ProgramId_MovedIn",				2);
	define ("c_ProgramId_OpenedOrShadowing",	3);
	define ("c_ProgramId_OpenedAndShadowing",	4);
	define ("c_ProgramId_OpenedDay",			5);
	define ("c_ProgramId_OpenedNight",			6);
	define ("c_ProgramId_25",					7);
	define ("c_ProgramId_50",					8);
	define ("c_ProgramId_75",					9);
	define ("c_ProgramId_90",					10);
	define ("c_ProgramId_Closed",				11);
	define ("c_ProgramId_Dimout",				12);
	define ("c_ProgramId_DimoutOrShadowing",	13);
	define ("c_ProgramId_DimoutAndShadowing",	14);
	define ("c_ProgramId_MovedOut",				15);
	define ("c_ProgramId_MovedOutTemp",			16);
	define ("c_ProgramId_Manual",				17);
	define ("c_ProgramId_LastPosition",			18);

	define ("c_ShadowingType_Shutter",			"Shutter");
	define ("c_ShadowingType_Jalousie",			"Jolousie");
	define ("c_ShadowingType_Marquees",			"Marquees");

	define ("c_Format_DateTime",				'Y.m.d H:i:s');

	// Device Configuration Properties
	// --------------------------------------------------------------------------
	define ("c_Property_Name",					"Name");
	define ("c_Property_ShadowingType",			"ShadowingType");
	define ("c_Property_TimeOpening",			"TimeOpening");
	define ("c_Property_TimeClosing",			"TimeClosing");
	define ("c_Property_TimeDimoutUp",			"TimeDimoutUp");
	define ("c_Property_TimeDimoutDown",		"TimeDimoutDown");
	define ("c_Property_TimePause",				"TimePauseBreak");
	define ("c_Property_Component",				"Component");
	define ("c_Property_TempSensorIndoor",		"TempSensorIndoor");

	define ("c_ShadowingDevice_1",				"Device1");
	define ("c_ShadowingDevice_2",				"Device2");
	define ("c_ShadowingDevice_3",				"Device3");
	define ("c_ShadowingDevice_4",				"Device4");
	define ("c_ShadowingDevice_5",				"Device5");
	define ("c_ShadowingDevice_6",				"Device6");
	define ("c_ShadowingDevice_7",				"Device7");
	define ("c_ShadowingDevice_8",				"Device8");
	define ("c_ShadowingDevice_9",				"Device9");
	define ("c_ShadowingDevice_10",				"Device10");
	define ("c_ShadowingDevice_11",				"Device11");
	define ("c_ShadowingDevice_12",				"Device12");
	define ("c_ShadowingDevice_13",				"Device13");
	define ("c_ShadowingDevice_14",				"Device14");
	define ("c_ShadowingDevice_15",				"Device15");
	define ("c_ShadowingDevice_16",				"Device16");
	define ("c_ShadowingDevice_17",				"Device17");
	define ("c_ShadowingDevice_18",				"Device18");
	define ("c_ShadowingDevice_19",				"Device19");
	define ("c_ShadowingDevice_20",				"Device20");
	define ("c_ShadowingDevice_21",				"Device21");
	define ("c_ShadowingDevice_22",				"Device22");
	define ("c_ShadowingDevice_23",				"Device23");
	define ("c_ShadowingDevice_24",				"Device24");
	define ("c_ShadowingDevice_25",				"Device25");
	define ("c_ShadowingDevice_26",				"Device26");
	define ("c_ShadowingDevice_27",				"Device27");
	define ("c_ShadowingDevice_28",				"Device28");
	define ("c_ShadowingDevice_29",				"Device29");
	define ("c_ShadowingDevice_30",				"Device30");
	
	/** @}*/
?>