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
	 * @file          IPSWecker_ChangeSettings.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 *
	 * Script wird für das WebFront um Änderungen an den Variablen vorzunehmen
	 *
	 */

	include_once "IPSWecker.inc.php";

	if ($_IPS['SENDER']=='WebFront') {
		$instanceId   = $_IPS['VARIABLE'];
//		$ControlId   = get_CirclyIdByinstanceId($instanceId);
		$ControlType = get_ControlType($instanceId);

		switch($ControlType) {
			case c_Control_Urlaubszeit:
			   IPSWecker_ChangeUrlaubszeit($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Global:
			   IPSWecker_ChangeGlobal($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Active:
			   IPSWecker_ChangeActive($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Feiertag:
			   IPSWecker_ChangeBoolean($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Frost:
			   IPSWecker_ChangeBoolean($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Urlaub:
			   $temp = IPSWecker_ChangeBoolean($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Schlummer:
			   IPSWecker_ChangeBoolean($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_End:
			   IPSWecker_ChangeBoolean($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Name:
				IPSWecker_ChangeWecker($instanceId, $_IPS['VALUE']);
				break;

			case c_Control_Tag:
			   IPSWecker_ChangeDay($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_LTag:
			   IPSWecker_ChangeLDay($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Stunde:
			   IPSWecker_ChangeStunde($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_LStunde:
			   IPSWecker_ChangeLStunde($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Minute:
			   IPSWecker_ChangeMinute($instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_LMinute:
			   IPSWecker_ChangeLMinute($instanceId, $_IPS['VALUE']);
			   break;

			default:
				IPSLogger_Err(__file__, "Error Unknown ControlType $ControlType");
				break;
	   }

	}

	/** @}*/
?>