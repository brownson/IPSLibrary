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
	 * @file          IPSShadowing_Logging.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 21.03.2012<br/>
	 *
	 * Application Logging von IPSShadowing
	 */

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSShadowing_GetMessageByMovement($deviceId) {
		$ToBeMoved = GetValue(IPS_GetObjectIDByIdent(c_Control_Movement, $deviceId));

		if ($ToBeMoved==c_MovementId_Opened) {
			$Message = "Öffnen";
		} elseif ($ToBeMoved==c_MovementId_Dimout) {
			$Message = "Abdunkelung";
		} elseif ($ToBeMoved==c_MovementId_Closed) {
			$Message = "Geschlossen";
		} elseif ($ToBeMoved==c_MovementId_25) {
			$Message = "25%";
		} elseif ($ToBeMoved==c_MovementId_50) {
			$Message = "50%";
		} elseif ($ToBeMoved==c_MovementId_75) {
			$Message = "75%";
		} elseif ($ToBeMoved==c_MovementId_90) {
			$Message = "90%";
		} elseif ($ToBeMoved==c_MovementId_Shadowing) {
			$Message = "Beschattung";
		} elseif ($ToBeMoved==c_MovementId_Up) {
			$Message = "Öffnen";
		} elseif ($ToBeMoved==c_MovementId_Down) {
			$Message = "Schliessen";
		} elseif ($ToBeMoved==c_MovementId_MovingOut) {
			$Message = "Ausfahren";
		} elseif ($ToBeMoved==c_MovementId_MovingIn) {
			$Message = "Einfahren";
		} elseif ($ToBeMoved==c_MovementId_MovedOut) {
			$Message = "Ausfahrt";
		} elseif ($ToBeMoved==c_MovementId_MovedIn) {
			$Message = "Einfahrt";
		} else {
			$Message = "Stop";
		}
		return $Message;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSShadowing_GetProgramName($ProgramId) {
		$ProgramList = array(
			c_ProgramId_Manual					=> c_Program_Manual,
			c_ProgramId_Opened					=> c_Program_Opened,
			c_ProgramId_MovedIn					=> c_Program_MovedIn,
			c_ProgramId_OpenedOrShadowing		=> c_Program_OpenedOrShadowing,
			c_ProgramId_OpenedAndShadowing		=> c_Program_OpenedAndShadowing,
			c_ProgramId_OpenedDay				=> c_Program_OpenedDay,
			c_ProgramId_OpenedNight	 			=> c_Program_OpenedNight,
			c_ProgramId_Closed					=> c_Program_Closed,
			c_ProgramId_90						=> c_Program_90,
			c_ProgramId_75						=> c_Program_75,
			c_ProgramId_50						=> c_Program_50,
			c_ProgramId_25						=> c_Program_25,
			c_ProgramId_MovedOut				=> c_Program_MovedOut,
			c_ProgramId_MovedOutTemp			=> c_Program_MovedOutTemp,
			c_ProgramId_Dimout					=> c_Program_Dimout,
			c_ProgramId_DimoutOrShadowing		=> c_Program_DimoutOrShadowing,
			c_ProgramId_DimoutAndShadowing		=> c_Program_DimoutAndShadowing,
			c_ProgramId_LastPosition			=> c_Program_LastPosition
		);

		return $ProgramList[$ProgramId];
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSShadowing_GetDeviceName($deviceId) {
		$deviceConfig = get_ShadowingConfiguration();
		$deviceIdent  = IPS_GetIdent($deviceId);
		$deviceName   = $deviceConfig[$deviceIdent]['Name'];
		return $deviceName;
	}
	
	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSShadowing_LogMoveByControl($DeviceId) {
		$DeviceName = IPSShadowing_GetDeviceName($DeviceId);
		$Message    = IPSShadowing_GetMessageByMovement($DeviceId)." von '$DeviceName' (Manuelle Steuerung)";
		IPSLogger_Inf(__file__, $Message);
		IPSShadowing_Log($Message);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSShadowing_LogMoveByProgram($DeviceId, $ProgramId, $LogMessage, $TriggeredByTemp) {
		$Message     = IPSShadowing_GetMessageByMovement($DeviceId);
		$DeviceName  = IPSShadowing_GetDeviceName($DeviceId);
		$ProgramName = IPSShadowing_GetProgramName($ProgramId);

		$Message .= " von '$DeviceName' durch $LogMessage, Programm='$ProgramName'";
		if ($TriggeredByTemp) {
			$Priority = GetValue(IPSShadowing_GetSettingControlId(c_Control_MsgPrioTemp, $DeviceId));
		} else {
			$Priority = GetValue(IPSShadowing_GetSettingControlId(c_Control_MsgPrioProg, $DeviceId));
		}

		IPSShadowing_Log($Message);
		IPSLogger_Not(__file__, $Message, $Priority);
	}

	function IPSShadowing_LogActivateScenario($scenarioId) {
		$scenarioName = IPS_GetName($scenarioId);
		IPSLogger_Inf(__file__,'Activation of Scenario "'.$scenarioName.'"');
		IPSShadowing_Log('Aktivierung von Szenario "'.$scenarioName.'"');
	}
	
	
	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSShadowing_LogChange($DeviceId, $Value, $ControlId) {
		$ObjectType  = IPS_GetName(IPS_GetParent($DeviceId));
		$ObjectName  = IPS_GetName($DeviceId);
		$ControlType = IPS_GetIdent($ControlId);
		if ($Value===true) {
			$Value = 'Ein';
		} elseif ($Value===false) {
			$Value = 'Aus';
		} else {
		}
		switch($ObjectType) {
			case 'Temp':
				IPSLogger_Inf(__file__,"Configuration Change for $ObjectType=$ObjectName: $ControlType=$Value");
				IPSShadowing_Log("Änderung von Temperatur Profil '$ObjectName', '$ControlType'=$Value");
				break;
			case 'Sun':
				IPSLogger_Inf(__file__,"Configuration Change for $ObjectType=$ObjectName: $ControlType=$Value");
				IPSShadowing_Log("Änderung von Sonnenstand Profil '$ObjectName', '$ControlType'=$Value");
				break;
			case 'Weather':
				IPSLogger_Inf(__file__,"Configuration Change for $ObjectType=$ObjectName: $ControlType=$Value");
				IPSShadowing_Log("Änderung von Wetter Profil '$ObjectName', '$ControlType'=$Value");
				break;
			case 'BgnOfDay':
				IPSLogger_Inf(__file__,"Configuration Change for $ObjectType=$ObjectName: $ControlType=$Value");
				IPSShadowing_Log("Änderung von Tagesbeginn Profil '$ObjectName', '$ControlType'=$Value");
				break;
			case 'EndOfDay':
				IPSLogger_Inf(__file__,"Configuration Change for $ObjectType=$ObjectName: $ControlType=$Value");
				IPSShadowing_Log("Änderung von Tagesende Profil '$ObjectName', '$ControlType'=$Value");
				break;
			case 'Scenarios':
				IPSLogger_Inf(__file__,"Configuration Change for $ObjectType=$ObjectName: $ControlType=$Value");
				IPSShadowing_Log("Änderung von Szenario '$ObjectName', '$ControlType'=$Value");
				break;
			case 'Devices':
				$deviceName   = IPSShadowing_GetDeviceName($DeviceId);
				IPSLogger_Inf(__file__,"Configuration Change for $ObjectType=$ObjectName: $ControlType=$Value");
				IPSShadowing_Log("Änderung von Beschattungs Element '$deviceName', '$ControlType'=$Value");
				break;
			default:
				IPSLogger_Inf(__file__,"Configuration Change for '$ObjectName': $ControlType=$Value");
				IPSShadowing_Log("Änderung von '$ControlType' auf $Value von '$ObjectName'");
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSShadowing_Log($Msg) {
		$logMessages = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Log.LogMessages');
		$logId       = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Log.LogId');

		IPSLogger_OutProgram($Msg, $logMessages, $logId, IPSSHADOWING_LOGMESSAGECOUNT, 12);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSShadowing_GetSettingControlId($ControlName) {
		$categoryIdSettings = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Settings');
		$VariableId = IPS_GetVariableIDByName($ControlName, $categoryIdSettings);
		if ($VariableId === false) {
			throw new Exception ("Control '$ControlName' could NOT be found in Settings");
		}

		return $VariableId;
	}

	/** @}*/
?>