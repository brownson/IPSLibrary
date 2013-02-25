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

	/**@ingroup ipslight
	 * @{
	 *
	 * @file          IPSLight_Constants.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 26.07.2012<br/>
	 *
	 * Definition der Konstanten für IPSLight
	 *
	 */

	// Confguration Property Definition
	define ('IPSLIGHT_NAME',				0);
	define ('IPSLIGHT_GROUPS',				1);
	define ('IPSLIGHT_TYPE',				2);
	define ('IPSLIGHT_COMPONENT',			3);
	define ('IPSLIGHT_POWERCIRCLE',			4);
	define ('IPSLIGHT_POWERWATT',			5);
	define ('IPSLIGHT_SIMULATION',			6);

	define ('IPSLIGHT_ACTIVATABLE',			100);

	define ('IPSLIGHT_PROGRAMON',			200);
	define ('IPSLIGHT_PROGRAMOFF',			201);
	define ('IPSLIGHT_PROGRAMLEVEL',		202);
	define ('IPSLIGHT_PROGRAMRGB',			203);

	define ('IPSLIGHT_WFCSPLITPANEL',		'WFCSplitPanel');
	define ('IPSLIGHT_WFCCATEGORY',			'WFCCategory');
	define ('IPSLIGHT_WFCGROUP',			'WFCGroup');
	define ('IPSLIGHT_WFCLINKS',			'WFCLinks');

	// Supported Device Types
	define ('IPSLIGHT_TYPE_SWITCH',			'Switch');
	define ('IPSLIGHT_TYPE_RGB',			'RGB');
	define ('IPSLIGHT_TYPE_DIMMER',			'Dimmer');

	// Device specific Properties
	define ('IPSLIGHT_DEVICE_COLOR',		'#Color');
	define ('IPSLIGHT_DEVICE_LEVEL',		'#Level');

	// Simulation Constants
	define ('IPSLIGHT_SIMULATION_DATEFMT',	'Ymd');
	define ('IPSLIGHT_SIMULATION_TIMEFMT',	'His');
	define ('IPSLIGHT_SIMULATION_VARTIME',	'LastTime');
	define ('IPSLIGHT_SIMULATION_VARDATE',	'FileDate');
	define ('IPSLIGHT_SIMULATION_VARSTATE',	'State');
	define ('IPSLIGHT_SIMULATION_VARDAYS',	'DaysBack');
	define ('IPSLIGHT_SIMULATION_VARMODE',	'DayMode');

	define ('IPSLIGHT_SIMULATION_MODEDAYS',	0);
	define ('IPSLIGHT_SIMULATION_MODEUSR1',	1);
	define ('IPSLIGHT_SIMULATION_MODEUSR2',	2);

	/** @}*/
?>