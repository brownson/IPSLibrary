<?
	/**@ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_Room.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Funktionen zur Steuerung der Räume
	 *
	 */

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetRoomVisible($PowerId, $Value) {
	   $RoomConfig   = get_RoomConfiguration();
	   $RoomId        = IPS_GetParent($PowerId);
	   $RoomName      = IPS_GetName($RoomId);
	   $WFRoomName   = $RoomConfig[$RoomName][c_Property_Name];
	   if ($WFRoomName=="") return;
	   $WFRoomId     = IPS_GetCategoryIDByName($WFRoomName, c_ID_WebFrontRoomes);

		$ChildrenIds = IPS_GetChildrenIDs($WFRoomId);
		foreach($ChildrenIds as $ChildrenIdx => $ChildrenId) {
		   if (IPS_LinkExists($ChildrenId)) {
			   $LinkData = IPS_GetLink($ChildrenId);
			   $LinkedChildId = $LinkData["LinkChildID"];
		  		if ($LinkedChildId <> $PowerId) {
		    		IPSLogger_Trc(__file__, 'Set Control "'.IPS_GetName($ChildrenId).'" of Room "'.IPS_GetName($RoomId).'" Visible='.bool2OnOff($Value));
		  			IPS_SetHidden($ChildrenId, !$Value);
		  		}
			} else {
		  	   $GroupSwitchId = get_ControlIdByRoomId($RoomId, c_Control_Group);
		 		IPS_SetHidden($ChildrenId, !GetValue($GroupSwitchId) or !$Value);
			}
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetRoomPower($PowerId, $Value, $PowerOnDevices=true) {
	   if (GetValue($PowerId) <> $Value) {
	      IPSLogger_Inf(__file__, 'Set Power for Room "'.IPS_GetName(IPS_GetParent($PowerId)).'" '.bool2OnOff($Value));
			SetValue($PowerId, $Value);
	      Entertainment_SetRoomVisible($PowerId, $Value);
			if ($PowerOnDevices) {
	      	Entertainment_SetDevicePowerByRoomId(IPS_GetParent($PowerId), $Value);
			}
	      Entertainment_SyncRoomControls(IPS_GetParent($PowerId));
	   }
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetRoomPowerByRoomId($RoomId, $Value, $PowerOnDevices=true) {
	   $PowerId = get_ControlIdByRoomId($RoomId, c_Control_RoomPower);
		Entertainment_SetRoomPower($PowerId, $Value, $PowerOnDevices);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IsRoomPoweredOn($RoomId) {
	   $PowerId = get_ControlIdByRoomId($RoomId, c_Control_RoomPower);
	   return GetValue($PowerId);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_PowerOffUnusedRoomes() {
	   IPSLogger_Dbg(__file__, 'PowerOff unused Roomes ...');
		$RoomIds =  get_ActiveRoomIds();
		foreach ($RoomIds as $RoomId) {
		   $RoomActice = false;
		   $DeviceNames = get_DeviceNamesByRoomId($RoomId);
			foreach ($DeviceNames as $DeviceName) {
				$RoomActive = isDevicePoweredOnByDeviceName($DeviceName) or $RoomActice;
			}
			if (!$RoomActive) {
			   Entertainment_SetRoomPowerByRoomId($RoomId, false);
			}
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetRoomPowerByDeviceName($DeviceName, $Value) {
	   if ($Value) {
	      $SourceList = get_SourceListByDeviceName($DeviceName);
			if (count($SourceList)==1) {
				$RoomKeys  = array_keys($SourceList);
				$RoomId    = $RoomKeys[0];
				if (!IsRoomPoweredOn($RoomId)) {
                    $SourceIdx = $SourceList[$RoomId];
					Entertainment_SetRoomPowerByRoomId($RoomId, true, false);
					Entertainment_SetSourceByRoomId($RoomId, $SourceIdx);
				}
			}
		} else {
			$RoomId = get_RoomIdByOutputDevice($DeviceName);
			if ($RoomId!==false and IsRoomPoweredOn($RoomId)) {
				Entertainment_SetRoomPowerByRoomId($RoomId, false);
			}
		}
	}




  /** @}*/
?>