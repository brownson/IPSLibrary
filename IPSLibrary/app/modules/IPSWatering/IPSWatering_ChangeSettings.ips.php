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

	/**@addtogroup ipswatering
	 * @{
	 *
	 * @file          IPSWatering_ChangeSettings.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 11.03.2012<br/>
	 *
	 * Script wird für das WebFront um Änderungen an den Variablen vorzunehmen
	 *
	 */

	include_once "IPSWatering.inc.php";

	if ($_IPS['SENDER']=='WebFront' || $_IPS['SENDER']=='Action') {
		$ControlId   = $_IPS['VARIABLE'];
		$CircleId    = get_CirclyIdByControlId($ControlId);
		$ControlType = get_ControlType($ControlId);
	
		switch($ControlType) {
			case c_Control_Active:
				IPSWatering_SetActive($ControlId, $_IPS['VALUE'], c_Mode_StartManual);
				break;
			case c_Control_StartTime:
			case c_Control_Duration:
			case c_Control_Program:
			case c_Control_Sensor:
			case c_Control_Automatic:
				IPSWatering_SetValue($ControlId, $_IPS['VALUE']);
				break;
			default:
				IPSLogger_Err(__file__, "Error Unknown ControlType $ControlType");
				Exit;
	   }
	}

	/** @}*/
?>