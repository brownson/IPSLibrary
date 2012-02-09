<?
	/**@ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_Device.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Steuerung der Geräte
	 *
	 */

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_MaxValueByControlId($ControlId) {
		$VariableObject = IPS_GetVariable($ControlId);
		$ProfileName = $VariableObject['VariableCustomProfile'];
		$ProfileObject = IPS_GetVariableProfile($ProfileName);
		$MaxValue = Count($ProfileObject['Associations']);
		return $MaxValue;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetProgramPrev($Id, $MessageType=c_MessageType_Action) {
	   $Value = GetValue($Id) - 1;
	   $MaxValue = get_MaxValueByControlId($Id);
	   if ($Value < 0) {
	      $Value = $MaxValue-1;
	   }
	   Entertainment_SetProgram($Id, $Value, $MessageType);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetProgramNext($Id, $MessageType=c_MessageType_Action) {
	   $Value = GetValue($Id) + 1;
	   $MaxValue = get_MaxValueByControlId($Id);
	   if ($Value > $MaxValue-1) {
	      $Value = 0;
	   }
	   Entertainment_SetProgram($Id, $Value, $MessageType);
	}


	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetProgram($Id, $Value, $MessageType=c_MessageType_Action) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
		  return;
		}
		if (GetValue($Id) <> $Value) {
			IPSLogger_Inf(__file__, "Set Program '$Value' for Device '$DeviceName' (MessageType=$MessageType)");
			SetValue($Id, $Value);
		  	Entertainment_SendDataByDeviceName($DeviceName, c_Control_Program,
			  												array(c_Property_CommPrg), $MessageType);
			Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
			//Entertainment_RefreshRemoteControlByDeviceName($DeviceName);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetMode($Id, $Value, $MessageType=c_MessageType_Action) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
		  return;
		}
		if (GetValue($Id) <> $Value) {
			IPSLogger_Inf(__file__, "Set Mode '$Value' for Device '$DeviceName' (MessageType=$MessageType)");
			SetValue($Id, $Value);
		  	Entertainment_SendDataByDeviceName($DeviceName, c_Control_Mode,
		  												  array(c_Property_CommMode), $MessageType);
			Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetVolume($Id, $Value, $MessageType=c_MessageType_Action) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
		  return;
		}
		IPSLogger_Inf(__file__, 'Set Volume "'.$Value.'" for Device "'.$DeviceName.'"');
	   $Limit = get_DeviceControlConfigValue($DeviceName, c_Control_Volume, c_Property_Limit);
	   if ($Limit!==false and $Value>$Limit) {
			IPSLogger_Dbg(__file__, "Limit Volume $Value-->$Limit for Device '$DeviceName'");
	      $Value = $Limit;
	   }
		if (GetValue($Id) <> $Value) {
			SetValue($Id, $Value);
		  	Entertainment_SendDataByDeviceName($DeviceName, c_Control_Volume, array(c_Property_CommVol), $MessageType);
			Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetBalance($Id, $Value, $MessageType=c_MessageType_Action) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
		  return;
		}
		IPSLogger_Inf(__file__, 'Set Balance "'.$Value.'" for Device "'.$DeviceName.'"');
		if (GetValue($Id) <> $Value) {
			SetValue($Id, $Value);
		  	Entertainment_SendDataByDeviceName($DeviceName, c_Control_Balance, array(c_Property_CommBal), $MessageType);
			Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetTreble($Id, $Value, $MessageType=c_MessageType_Action) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
		  return;
		}
		IPSLogger_Inf(__file__, 'Set Treble "'.$Value.'" for Device "'.$DeviceName.'"');
		if (GetValue($Id) <> $Value) {
			SetValue($Id, $Value);
		  	Entertainment_SendDataByDeviceName($DeviceName, c_Control_Treble, array(c_Property_CommTre), $MessageType);
			Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
		}
	}
	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetMiddle($Id, $Value, $MessageType=c_MessageType_Action) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
		  return;
		}
		IPSLogger_Inf(__file__, 'Set Middle "'.$Value.'" for Device "'.$DeviceName.'"');
		if (GetValue($Id) <> $Value) {
			SetValue($Id, $Value);
		  	Entertainment_SendDataByDeviceName($DeviceName, c_Control_Middle, array(c_Property_CommMid), $MessageType);
			Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
		}
	}
	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetBass($Id, $Value, $MessageType=c_MessageType_Action) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
		  return;
		}
		IPSLogger_Inf(__file__, 'Set Bass "'.$Value.'" for Device "'.$DeviceName.'"');
		if (GetValue($Id) <> $Value) {
			SetValue($Id, $Value);
		  	Entertainment_SendDataByDeviceName($DeviceName, c_Control_Bass, array(c_Property_CommBas), $MessageType);
			Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
		}
	}
	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetMuting($Id, $Value, $MessageType=c_MessageType_Action) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
			return;
		}
	   if (!is_bool($Value)) { 												/*Toggle Power Value*/
	      $Value = !GetValue($Id);
		}
		if (GetValue($Id) <> $Value) {
			IPSLogger_Inf(__file__, 'Set Muting "'.bool2OnOff($Value).'" for Device "'.$DeviceName.'"');
			SetValue($Id, $Value);
			if ($Value) {
		  		Entertainment_SendDataByDeviceName($DeviceName, c_Control_Muting,
				  			array(c_Property_CommMuteOn, c_Property_CommMute), $MessageType);
			} else {
		  		Entertainment_SendDataByDeviceName($DeviceName, c_Control_Muting,
				  			array(c_Property_CommMuteOff, c_Property_CommMute), $MessageType);
			}
			Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetDeviceControl($DeviceControlId, $Value) {
		$ControlType = get_ControlType($DeviceControlId);
		switch ($ControlType) {
		   case c_Control_Volume:
		      Entertainment_SetVolume($DeviceControlId, $Value);
		      break;
		   case c_Control_Balance:
		      Entertainment_SetBalance($DeviceControlId, $Value);
		      break;
		   case c_Control_Treble:
		      Entertainment_SetTreble($DeviceControlId, $Value);
		      break;
		   case c_Control_Middle:
		      Entertainment_SetMiddle($DeviceControlId, $Value);
		      break;
		   case c_Control_Bass:
		      Entertainment_SetBass($DeviceControlId, $Value);
		      break;
		   case c_Control_Muting:
		      Entertainment_SetMuting($DeviceControlId, $Value);
		      break;
		   case c_Control_Mode:
		      Entertainment_SetMode($DeviceControlId, $Value);
		      break;
		   case c_Control_Program:
		      Entertainment_SetProgram($DeviceControlId, $Value);
		      break;
		   case c_Control_Source:
		   case c_Control_RoomPower:
		   case c_Control_DevicePower:
				IPSLogger_Err(__file__, 'Controls of Type "'.$ControlType.'" cannot be handled with this function, ID='.$DeviceControlId.' !');
		      break;
			default:
				IPSLogger_Err(__file__, 'Unknown DeviceControl with ID='.$DeviceControlId.' !');
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetControl ($ControlId, $Value) {
		if (isDeviceControl($ControlId)) {
		   Entertainment_SetDeviceControl($ControlId, $Value);
		} else if (isRoomControl($ControlId)) {
		   Entertainment_SetDeviceControlByRoomControlId($ControlId, $Value);
		} else {
		   IPSLogger_Err(__file__, 'Unknown Control, ID='.$ControlId.', Name="'.IPS_GetName($ControlId).'"');
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetRoomControlByDeviceControlId($DeviceControlId, $Value) {
		$RoomControlIds = get_RoomControlIdsByDeviceControlId($DeviceControlId);
		foreach ($RoomControlIds as $RoomControlId) {
			IPSLogger_Dbg(__file__, 'Set Control "'.IPS_GetName($RoomControlId).'" "'.$Value.'" in Room "'.IPS_GetName(IPS_GetParent($RoomControlId)).'"');
			SetValue($RoomControlId, $Value);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetDeviceControlByRoomControlId($RoomControlId, $Value) {
		$DeviceControlId = get_DeviceControlIdByRoomControlId($RoomControlId);
		if ($DeviceControlId !== false) {
		   Entertainment_SetDeviceControl($DeviceControlId, $Value);
		} else {
		   IPSLogger_Err(__file__, 'No DeviceControl found for RoomControlId='.$RoomControlId.', Name="'.IPS_GetName($RoomControlId).'"');
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetDeviceControlByRoomId($RoomId, $ControlType, $Value) {
		$RoomControlId = get_ControlIdByRoomId($RoomId, $ControlType);
		if ($RoomControlId !== false) {
		   Entertainment_SetDeviceControlByRoomControlId($RoomControlId, $Value);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetDeviceControlByDeviceName($DeviceName, $ControlType, $Value) {
		$DeviceControlId = get_ControlIdByDeviceName($DeviceName, $ControlType, false);
		if ($DeviceControlId !== false) {
			Entertainment_SetDeviceControl($DeviceControlId, $Value);
		}
	}

  /** @}*/
?>