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
	 * @file          IPSWecker_Logging.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 11.03.2012<br/>
	 *
	 * Application Logging von IPSWecker
	 *
	 */

	 // ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_LogActivate($CircleId, $Value, $Mode) {
		$CircleName = get_CirclyNameByID($CircleId);
		IPSLogger_Inf(__file__, bool2Activation($Value)." Wecker ($Mode) for Circle '$CircleName'");
		if ($Value) {
			IPSWecker_Log("Aktiviere Beregnung ($Mode) für '$CircleName'");
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_LogNoActivationByRainfall($CircleId, $SensorLimit, $Rainfall) {
		$CircleName = get_CirclyNameByID($CircleId);
		IPSLogger_Dbg(__file__, "No Wecker for Circle '$CircleName' during Rainfall of $Rainfall (Limit $SensorLimit)");
		IPSWecker_Log("Keine Bewässerung von $CircleName durch Regen (Sensor: $Rainfall)");
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_LogChange($CircleId, $Value, $ControlId) {
		$CircleName  = get_CirclyNameByID($CircleId);
		$ControlType = get_ControlType($ControlId);
		IPSLogger_Dbg(__file__,"Configuration Change for Circle '$CircleName': $ControlType=$Value");
		if ($Value) {
			IPSWecker_Log("Änderung Konfiguration ('$CircleName'): $ControlType=$Value");
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_Log($Msg) {
		$id_LogMessages = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker.Log.LogMessages');
		$id_LogId       = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker.Log.LogId');

		$Msg          = htmlentities($Msg, ENT_COMPAT, 'ISO-8859-1');
		$Msg          = str_replace("\n", "<BR>", $Msg);

		$MsgList      = GetValue($id_LogMessages);
		$TablePrefix  = '<table width="100%" style="font-family:courier; font-size:12px;">';
		$CurrentMsgId = GetValue($id_LogId)+1;
		SetValue($id_LogId, $CurrentMsgId);

	      //2010-12-03 22:09 Msg ...
		   $Out =  '<tr id="'.$CurrentMsgId.'" style="color:#FFFFFF;">';
		   $Out .=    '<td>'.date('Y-m-d H:i').'</td>';
		   $Out .=    '<td>'.$Msg.'</td>';
		   $Out .= '</tr>';


			//<table><tr><td>....</tr></table>
			if (strpos($MsgList, '</table>') === false) {
			   $MsgList = "";
			} else {
			   $StrTmp      = '<tr id="'.($CurrentMsgId-c_LogMessage_Count+1).'"';
				if (strpos($MsgList, $StrTmp)===false) {
				   $StrPos = strlen($TablePrefix);
			   } else {
					$StrPos      = strpos($MsgList, $StrTmp);
			   }
				$StrLen      = strlen($MsgList) - strlen('</table>') - $StrPos;
				$MsgList     = substr($MsgList, $StrPos, $StrLen);
			}
			SetValue($id_LogMessages, $TablePrefix.$MsgList.$Out.'</table>');
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function bool2Activation($bool) {
	   if ($bool) {
			return "Activate";
	   } else if (!$bool) {
			return "Deactivate";
	   } else {
			return "NULL";
	   }
	}

	/** @}*/
?>