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
	 * @file          IPSHealth_ChangeSettings.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 *
	 * Script wird für das WebFront um Änderungen an den Variablen vorzunehmen
	 *
	 */

	include_once "IPSHealth.inc.php";
	$AppPath        = "Program.IPSLibrary.app.modules.IPSHealth";
	$IdApp     = get_ObjectIDByPath($AppPath);

	if ($_IPS['SENDER']=='WebFront') {
		$instanceId   = $_IPS['VARIABLE'];
		$ControlId   = get_CirclyIdByControlId($instanceId);
		$ControlType = get_ControlType($instanceId);

		switch($ControlType) {
			case c_Control_Select:
			   CircleSelect($ControlId, $instanceId, $_IPS['VALUE']);
				break;

			case c_Property_DBNeuagg:
			   DB_Reaggregieren($ControlId, $instanceId, $_IPS['VALUE']);
			   break;

			case c_Control_Modul:

//					$Circle0Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
					$ips_uebersicht_id	= get_ControlId(c_Control_Uebersicht, $ControlId);

					$html1 = "";
					$html1 = $html1 . "<table border='0' bgcolor=#ff6611 width='100%' height='300' cellspacing='0'  >";

					$html1 = $html1 . "<tr>";
					$html1 = $html1 . "<td style='text-align:left;'>";
					$html1 = $html1 . "<span style='font-family:arial;color:white;font-size:15px;'><br></span>";
					$html1 = $html1 . "<span style='font-family:arial;color:white;font-size:15px;'></span></td>";
					$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:white;font-size:50px;'>Update</span></td>";
					$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:20px;'></span></td>";
					$html1 = $html1 . "</tr>";

					$html1 = $html1 . "<tr>";
					$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px'></span></td>";
					$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:yellow;font-size:50px'>IPSHealth</span></td>";
					$html1 = $html1 . "</tr>";

					$html1 = $html1 . "<tr>";
					$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px;'></span></td>";
					$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:yellow;font-size:50px;'>wurde gestartet</span></td>";
					$html1 = $html1 . "</tr>";

					$html1 = $html1 . "</table>";

					SetValueString($ips_uebersicht_id,$html1);

				IPS_RunScript(IPS_GetScriptIDByName("IPSHealth_ModulUpdate",$IdApp));
				break;

			case c_Control_Version:
			   get_ModulVersion($ControlId, $instanceId, $_IPS['VALUE']);
			   break;

			default:
				IPSLogger_Err(__file__, "Error Unknown ControlType");// $ControlType");
				break;
	   }

	}

	/** @}*/
?>