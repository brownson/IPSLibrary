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

	/**@ingroup ipspowercontrol
	 * @{
	 *
	 * @file          IPSPowerControl_Constants.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.09.2012<br/>
	 *
	 * Definition der Konstanten für IPSPowerControl
	 *
	 */

	// Confguration Property Definition
	define ('IPSPC_PROPERTY_NAME',				'Name');
	define ('IPSPC_PROPERTY_VARWATT',			'VariableWatt');
	define ('IPSPC_PROPERTY_VARKWH',			'VariableKWH');
	define ('IPSPC_PROPERTY_DISPLAY',			'Display');
	define ('IPSPC_PROPERTY_VALUETYPE',			'ValueType');

	define ('IPSPC_VALUETYPE_TOTAL',			'Total');
	define ('IPSPC_VALUETYPE_DETAIL',			'Detail');
	define ('IPSPC_VALUETYPE_OTHER',			'Other');

	// Storage of calculated Values
	define ('IPSPC_VAR_VALUEKWH',				'ValueKWH_');
	define ('IPSPC_VAR_VALUEWATT',				'ValueWatt_');
	// Selection
	define ('IPSPC_VAR_SELECTVALUE',			'SelectValue');
	define ('IPSPC_VAR_PERIODCOUNT',			'PeriodAndCount');
	define ('IPSPC_VAR_TYPEOFFSET',				'TypeAndOffset');
	define ('IPSPC_VAR_TIMEOFFSET',				'TimeOffset');
	define ('IPSPC_VAR_TIMECOUNT',				'TimeCount');
	// Visualization
	define ('IPSPC_VAR_CHARTHTML',				'ChartHTML');


	define ('IPSPC_PERIOD_HOUR',				10);
	define ('IPSPC_PERIOD_DAY',					11);
	define ('IPSPC_PERIOD_WEEK',				12);
	define ('IPSPC_PERIOD_MONTH',				13);
	define ('IPSPC_PERIOD_YEAR',				14);

	define ('IPSPC_COUNT_SEPARATOR',			10000);
	define ('IPSPC_COUNT_MINUS',				20001);
	define ('IPSPC_COUNT_VALUE',				20002);
	define ('IPSPC_COUNT_PLUS',					20003);

	define ('IPSPC_TYPE_KWH',					10);
	define ('IPSPC_TYPE_WATT',					11);
	define ('IPSPC_TYPE_EURO',					12);
	define ('IPSPC_TYPE_STACK',					13);
	define ('IPSPC_TYPE_STACK2',				14);
	define ('IPSPC_TYPE_PIE',					15);
	define ('IPSPC_TYPE_OFF',					16);

	define ('IPSPC_OFFSET_SEPARATOR',			10000);
	define ('IPSPC_OFFSET_PREV',				30000);
	define ('IPSPC_OFFSET_VALUE',				30001);
	define ('IPSPC_OFFSET_NEXT',				30002);



	/** @}*/
?>