<?
	/**@ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_Source.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Funktionen um die Sourcen (Eingangswahlschalter) der verschiedenen Räume steueren zu
	 * können.
	 *
	 */

	// Show/Hide Group
	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetGroupControlVisibility($GroupSwitchId, $Value) {
		$RoomConfig   = get_RoomConfiguration();
		$RoomName     = IPS_GetName(IPS_GetParent($GroupSwitchId));
		$ControlName  = IPS_GetName($GroupSwitchId);
		$WFRoomName   = $RoomConfig[$RoomName][c_Property_Name];
		$WFRoomId     = IPS_GetCategoryIDByName($WFRoomName, c_ID_WebFrontRoomes);
		$WFGroupId    = IPS_GetInstanceIDByName($ControlName, $WFRoomId);
		IPS_SetHidden($WFGroupId, !$Value);
		SetValue($GroupSwitchId, $Value);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetRoomControlVisibility($RoomControlId, $Value) {
		$RoomName     = IPS_GetName(IPS_GetParent($RoomControlId));
		$RoomConfig   = get_RoomConfiguration();
		$ControlName  = IPS_GetName($RoomControlId);
		$WFRoomName   = $RoomConfig[$RoomName][c_Property_Name];
		if ($WFRoomName=="") return;
		$WFRoomId     = IPS_GetCategoryIDByName($WFRoomName, c_ID_WebFrontRoomes);

		$WFControlId = false;
		$ChildrenIds = IPS_GetChildrenIDs($WFRoomId);
		foreach($ChildrenIds as $ChildrenIdx => $ChildrenId) {
			if (IPS_LinkExists($ChildrenId)) {
				if (IPS_GetName($ChildrenId)==$ControlName) {
					$WFControlId = $ChildrenId;
				}
			} else {
				$WFControlId = @IPS_GetLinkIDByName($ControlName, $WFRoomId);
			}
		}

		if ($WFControlId!==false) {
			$WFControl=IPS_GetObject($WFControlId);
			if ($WFControl['ObjectIsHidden']<> !$Value) {
				IPS_SetHidden($WFControlId, !$Value);
			}
		}
	}


	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SyncAllRoomControls() {
		$RoomIds = IPS_GetChildrenIDs(c_ID_Roomes);
		foreach ($RoomIds as $RoomId) {
			Entertainment_SyncRoomControls($RoomId);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SyncRoomControls($RoomId) {
		$RoomName     = IPS_GetName($RoomId);
		$RoomPower    = GetValue(get_ControlIdByRoomId($RoomId, c_Control_RoomPower));
		$RoomConfig   = get_RoomConfiguration();
		$ControlTypes = $RoomConfig[$RoomName];
		foreach ($ControlTypes as $ControlType=>$ControlData) {
			if ($ControlType==c_Control_Muting or
				 $ControlType==c_Control_Volume or
				 $ControlType==c_Control_Balance or
				 $ControlType==c_Control_Treble or
				 $ControlType==c_Control_Middle or
				 $ControlType==c_Control_Bass or
				 $ControlType==c_Control_Program or
				 $ControlType==c_Control_RemoteVolume or
				 $ControlType==c_Control_iRemoteVolume or
				 $ControlType==c_Control_RemoteSource or
				 $ControlType==c_Control_iRemoteSource or
				 $ControlType==c_Control_Mode) {
				 $RoomControlId   = get_ControlIdByRoomId($RoomId, $ControlType);
				 $DeviceControlId = get_DeviceControlIdByRoomControlId($RoomControlId);

				if ($DeviceControlId===false and $ControlType==c_Control_iRemoteVolume) {
					$DeviceControlId = get_DeviceControlIdByRoomControlId($RoomControlId, c_Control_RemoteVolume);
				} else if ($DeviceControlId===false and $ControlType==c_Control_iRemoteSource) {
					$DeviceControlId = get_DeviceControlIdByRoomControlId($RoomControlId, c_Control_RemoteSource);
				} else {
				  //
				}

				IPSLogger_Trc(__file__,'Sync Room="'.$RoomName.'", Control="'.$ControlType.'", DeviceControlId='.$DeviceControlId);
				if ($DeviceControlId!==false) {
					SetValue($RoomControlId, GetValue($DeviceControlId));
					Entertainment_SetRoomControlVisibility($RoomControlId, $RoomPower);
				} else {
					Entertainment_SetRoomControlVisibility($RoomControlId, false);
				}
			}
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetSourceNext($SourceId, $MessageType=c_MessageType_Action) {
		$MaxValue = get_MaxValueByControlId($SourceId);
		$Value    = GetValue($SourceId) + 1;
		if ($Value >= $MaxValue) {
			$Value = 0;
		}
		Entertainment_SetSource($SourceId, $Value, $MessageType);
	}
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetSource($SourceId, $Value, $MessageType=c_MessageType_Action) {
		$RoomId = IPS_GetParent($SourceId);
		$IsRoomPoweredOn = IsRoomPoweredOn($RoomId);
		if (GetValue($SourceId) <> $Value || !$IsRoomPoweredOn) {
			$RoomId = IPS_GetParent($SourceId);
			$SourceName = get_SourceName($RoomId, $Value);
			IPSLogger_Inf(__file__, 'Set Source "'.$SourceName.'" of Room '.IPS_GetName($RoomId));
			SetValue($SourceId, $Value);
			if (!$IsRoomPoweredOn) {
				Entertainment_SetRoomPowerByRoomId($RoomId, true, false);
			}
			Entertainment_SetDeviceControlByRoomId($RoomId, c_Control_Muting, false);
			Entertainment_SetDevicePowerByRoomId($RoomId, true);
			Entertainment_SendDataBySourceIdx($RoomId, $Value, $MessageType);
			Entertainment_SyncRoomControls($RoomId);
			Entertainment_PowerOffUnusedDevices();
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetSourceByRoomId($RoomId, $SourceIdx) {
		$SourceId = get_ControlIdByRoomId($RoomId, c_Control_Source);
		Entertainment_SetSource($SourceId, $SourceIdx);
	}

  /** @}*/
?>