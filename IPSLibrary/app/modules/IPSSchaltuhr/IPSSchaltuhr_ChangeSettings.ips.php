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
	 * @file          IPSSchaltuhr_ChangeSettings.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 *
	 * Script wird für das WebFront um Änderungen an den Variablen vorzunehmen
	 *
	 */

	include_once "IPSSchaltuhr.inc.php";

	if ($_IPS['SENDER']=='WebFront') {
		$instanceId   = $_IPS['VARIABLE'];
//		$ControlId   = get_CirclyIdByinstanceId($instanceId);
		$ControlType = get_ControlType($instanceId);

		switch($ControlType) {
//			   IPSSchaltuhr_ChangeUrlaubszeit($instanceId, $_IPS['VALUE']);

			case c_Control_Name:
				IPSSchaltuhr_ChangeName($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_StartTag:
				IPSSchaltuhr_ChangeStartTag($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_StopTag:
				IPSSchaltuhr_ChangeStopTag($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_StartStunde:
				IPSSchaltuhr_ChangeStartStunde($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_StopStunde:
				IPSSchaltuhr_ChangeStopStunde($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_StartMinute:
				IPSSchaltuhr_ChangeStartMinute($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_StopMinute:
				IPSSchaltuhr_ChangeStopMinute($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_StartAktiv:
				IPSSchaltuhr_ChangeStartAktiv($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_StopAktiv:
				IPSSchaltuhr_ChangeStopAktiv($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_RunAktiv:
				IPSSchaltuhr_ChangeRunAktiv($instanceId, $_IPS['VALUE']);
			   break;

			default:
				IPSLogger_Err(__file__, "Error Unknown ControlType $ControlType");
				break;
	   }

	}

	/** @}*/
?>