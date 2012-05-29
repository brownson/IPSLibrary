<?
	/**@addtogroup entertainment
	 * @{
	 *
	 * @file          Entertainment_Control.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Diverse Funktionen um Werte der Entertainment Struktur abzufragen
	*/


	// ---------------------------------------------------------------------------------------------------------------------------
	function get_SourceName($RoomId, $SourceIdx) {
	   $SourceConf = get_SourceConfiguration();
	   $RoomName   = IPS_GetName($RoomId);
	   return $SourceConf[$RoomName][$SourceIdx][c_Property_Name];
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_DeviceConfigValue($DeviceName, $Property) {
	   $DeviceConfig = get_DeviceConfiguration();
		if (array_key_exists($Property, $DeviceConfig[$DeviceName])) {
		   return $DeviceConfig[$DeviceName][$Property];
		} else {
		   return false;
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_DeviceControlConfigValue($DeviceName, $ControlType, $Property) {
	   $DeviceConfig = get_DeviceConfiguration();
		if (array_key_exists($Property, $DeviceConfig[$DeviceName][$ControlType])) {
		   return $DeviceConfig[$DeviceName][$ControlType][$Property];
		} else {
		   return false;
		}

	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_DeviceControlValue($DeviceName, $ControlType) {
	   $ControlId = get_ControlIdByDeviceName($DeviceName, $ControlType);
	   if ($ControlId !== false) {
	      return GetValue($ControlId);
	   } else {
	      IPSLogger_Err(__file__, "ControlType '$ControlType' could NOT be found for Device '$Device'!");
	   }
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_CommConfigValue($Name, $Property) {
	   $CommConfig = get_CommunicationConfiguration();
	   $CommData = $CommConfig[$Name];
		if (array_key_exists($Property, $CommData)) {
		   return $CommData[$Property];
		} else {
		   return false;
		}
	   IPSLogger_Err(__file__, "CommunicationDevice with Name '$Name' could NOT be found in CommunicationConfiguration");
	   return false;
	}


	// ---------------------------------------------------------------------------------------------------------------------------
	function isDevicePoweredOnByDeviceName ($DeviceName) {
		$PowerControlId = get_ControlIdByDeviceName($DeviceName, c_Control_DevicePower, false);
		if ($PowerControlId!==false) { 
			return GetValue($PowerControlId);
		}
		return true;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_RoomControlIdsByDeviceControlId($DeviceControlId) {
	   $DeviceId       = IPS_GetParent($DeviceControlId);
	   $DeviceName     = IPS_GetName($DeviceId);
   	$RoomIds        = IPS_GetChildrenIDs(c_ID_Roomes);
   	$RoomControlIds = array();
		foreach ($RoomIds as $RoomId) {
			$RoomDeviceNames = get_DeviceNamesByRoomId($RoomId);
			foreach ($RoomDeviceNames as $RoomDeviceName) {
			   if ($RoomDeviceName==$DeviceName) {
					$ControlId = get_ControlIdByRoomId($RoomId,get_ControlType($DeviceControlId));
					if ($ControlId !== false) {
						$RoomControlIds[] = $ControlId;
					}
			   }
			}
		}
		return $RoomControlIds;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_DeviceControlIdByRoomControlId($RoomControlId, $ControlType=null) {
	   $RoomId = IPS_GetParent($RoomControlId);
		$DeviceNames = get_DeviceNamesByRoomId($RoomId);
		
		if ($ControlType==null) {
			$ControlType = get_ControlType($RoomControlId);
		}
		if ($ControlType == c_Control_Volume or $ControlType == c_Control_Muting or $ControlType == c_Control_RemoteVolume) {
		   $DeviceNames = array_reverse($DeviceNames);
		}
		foreach($DeviceNames as $DeviceName) {
			$DeviceControlId = get_ControlIdByDeviceName($DeviceName, $ControlType, false);
			if ($DeviceControlId !== false) {
			   return $DeviceControlId;
			}
		}
		return false;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function isDeviceControl($ControlId) {
	   return (IPS_GetParent(IPS_GetParent($ControlId)) == c_ID_Devices);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function isRoomControl($ControlId) {
	   return (IPS_GetParent(IPS_GetParent($ControlId)) == c_ID_Roomes);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function bool2OnOff($bool) {
	   if ($bool) {
			return "On";
	   } else if (!$bool) {
			return "Off";
	   } else {
			return "???";
	   }
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_ControlType($ControlId) {
		$Name = IPS_GetName($ControlId);
		$ParentName = IPS_GetName(IPS_GetParent($ControlId));
		$CategoryId = IPS_GetParent(IPS_GetParent($ControlId));

		if ($CategoryId == c_ID_Roomes) {
			$RoomData = get_RoomConfiguration();
			foreach($RoomData[$ParentName] as $ControlType => $ControlProperties) {
				if (!is_array($ControlProperties)) continue;
			   if ($ControlProperties[c_Property_Name] == $Name) {
					return $ControlType;
				}
			}
			return "";
		} else if ($CategoryId == c_ID_Devices) {
			$DeviceData = get_DeviceConfiguration();
			foreach($DeviceData[$ParentName] as $ControlType => $ControlProperties) {
				if (!is_array($ControlProperties)) continue;
			   if ($ControlProperties[c_Property_Name] == $Name) {
					return $ControlType;
				}
			}
			return "";
		} else {
			return "";
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_SourceIdxByRoomId($RoomId) {
		$RoomName = IPS_GetName($RoomId);
	   $RoomData = get_RoomConfiguration();
		$SourceName = $RoomData[$RoomName][c_Control_Source][c_Property_Name];

	   $ChildrenIds = IPS_GetChildrenIDs($RoomId);
		foreach($ChildrenIds as $ChildrenIdx => $ChildrenId) {
		   if (IPS_GetName($ChildrenId) == $SourceName) {
				return GetValue($ChildrenId);
			}
		}
		return false;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_ControlNameByDeviceName($DeviceName, $ControlType, $ErrorOnNotFound=true) {
		$DeviceData = get_DeviceConfiguration();
		if (!array_key_exists($DeviceName, $DeviceData)) {
		   IPSLogger_Wrn(__file__, 'Unknown DeviceName "'.$DeviceName.'"');
		   return false;
		}
		$Device = $DeviceData[$DeviceName];
		if (array_key_exists($ControlType, $Device)) {
		   return $DeviceData[$DeviceName][$ControlType][c_Property_Name];
		}
		if ($ErrorOnNotFound) {
			IPSLogger_Err(__file__, "ControlName could NOT be found for Device='$DeviceName' and ControlType='$ControlType'");
			exit;
		}
		return false;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_ControlIdByDeviceName($DeviceName, $ControlType, $ErrorOnNotFound=true) {

		$ControlName = get_ControlNameByDeviceName($DeviceName, $ControlType, $ErrorOnNotFound);

		if ($ControlName !== false) { // Device has Control
			$Ids = IPS_GetChildrenIDs(c_ID_Devices);
			$DeviceId=false;
			foreach($Ids as $Idx => $Id) {
		   	if ($DeviceName == IPS_GetName($Id)) {
		   	   $DeviceId = $Id;
			   }
			}
			if ($DeviceId !== false) { // Found Device
				$Ids = IPS_GetChildrenIDs($DeviceId);
				foreach($Ids as $Idx => $Id) {
		   		if ($ControlName == IPS_GetName($Id)) {
		   		   return $Id;
				   }
				}
			}
		}
		if ($ErrorOnNotFound) {
			IPSLogger_Err(__file__, "ControlId could Not be found for Device='$DeviceName' and ControlType='$ControlType'");
			exit;
  		}
		return false;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_ControlNameByRoomName($RoomName, $ControlType) {
		$RoomData = get_RoomConfiguration();
		$Room = $RoomData[$RoomName];
		if (array_key_exists($ControlType, $Room)) {
		   return $RoomData[$RoomName][$ControlType][c_Property_Name];
		}
		return false;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_ControlIdByRoomId($RoomId, $ControlType) {
		$ControlName = get_ControlNameByRoomName(IPS_GetName($RoomId), $ControlType);
		if ($ControlName !== false) { // Room has Control
			$Ids = IPS_GetChildrenIDs($RoomId);
			foreach($Ids as $Idx => $Id) {
	   		if ($ControlName == IPS_GetName($Id)) {
	   		   return $Id;
			   }
			}
		}
		return false;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_ActiveRoomIds () {
   	$RoomIds = IPS_GetChildrenIDs(c_ID_Roomes);
   	$ActiveRoomIds = array();
		foreach ($RoomIds as $RoomId) {
			$PowerId = get_ControlIdByRoomId($RoomId, c_Control_RoomPower);
			if (GetValue($PowerId)) {
				$ActiveRoomIds[] = $RoomId;
			}
		}
		return $ActiveRoomIds;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_DeviceNamesByRoomId($RoomId, $SourceIdx=-1, $SourceDeviceTypes=array(c_Property_Input, c_Property_Switch, c_Property_Output)) {
        $SourceConf     = get_SourceConfiguration();
        if ($SourceIdx==-1) {
			$SourceId       = get_ControlIdByRoomId($RoomId, c_Control_Source);
			$SourceIdx      = GetValue($SourceId);
		}
		$SourceConfRoom = $SourceConf[IPS_GetName($RoomId)][$SourceIdx];
		$DeviceNames    = array();
		foreach ($SourceDeviceTypes as $SourceDeviceType) {
			if (array_key_exists($SourceDeviceType, $SourceConfRoom)) {
				$SourceDevices = $SourceConfRoom[$SourceDeviceType];
                // wrap older/non array configuration in an array for downward compatibility
                if(isset($SourceDevices[c_Property_Device])) {
                    $SourceDevices = array($SourceDevices);
                }
                
				foreach($SourceDevices as $SourceDevice) {
					$DeviceName = $SourceDevice[c_Property_Device];
					$DeviceNames[$DeviceName] = $DeviceName;
				}
			}
		}
		return $DeviceNames;
	}
    
	// ---------------------------------------------------------------------------------------------------------------------------
	function get_SourceDeviceTypeArray($DeviceList) {
		$DeviceTypes = array();
		if(isset($DeviceList[c_Property_Device])) {
			$DeviceList = array($DeviceList);
		}
		foreach($DeviceList as $Device) {
			array_push($DeviceTypes, $Device[c_Property_Device]);
		}
		return $DeviceTypes;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_SourceDeviceTypes($RoomId, $SourceIdx) {
   	$SourceConf = get_SourceConfiguration();
		$SourceConfRoom = $SourceConf[IPS_GetName($RoomId)][$SourceIdx];
		$DeviceTypes = array();
		if (array_key_exists(c_Property_Input, $SourceConfRoom)) {
			$DeviceTypes[c_Property_Input] = get_SourceDeviceTypeArray($SourceConfRoom[c_Property_Input]);
		}
		if (array_key_exists(c_Property_Switch, $SourceConfRoom)) {
			$DeviceTypes[c_Property_Switch] = get_SourceDeviceTypeArray($SourceConfRoom[c_Property_Switch]);
		}
		if (array_key_exists(c_Property_Output, $SourceConfRoom)) {
			$DeviceTypes[c_Property_Output] = get_SourceDeviceTypeArray($SourceConfRoom[c_Property_Output]);
		}
		return $DeviceTypes;
	}

	// Function returns all active DeviceNames (active Roomes with current Devices)
	// ---------------------------------------------------------------------------------------------------------------------------
	function get_ActiveDeviceNames() {
		$ActiveRoomIds     = get_ActiveRoomIds();
		$ActiveDeviceNames = array();
		foreach ($ActiveRoomIds as $RoomId) {
			$DeviceNames = get_DeviceNamesByRoomId($RoomId);
			foreach ($DeviceNames as $DeviceName) {
				$ActiveDeviceNames[$DeviceName] = $DeviceName;
			}
		}
		return $ActiveDeviceNames;
	}


	// ---------------------------------------------------------------------------------------------------------------------------
	function get_RoomId($RoomName) {
	   $RoomIds = IPS_GetChildrenIDs(c_ID_Roomes);
	   foreach ($RoomIds as $RoomId) {
	      if (IPS_GetName($RoomId)==$RoomName) {
	         return $RoomId;
	      }
	   }
	   return false;
	}
	// Function returns RoomId for Device where Room is active and current Output is equal to specified DeviceName
	// ---------------------------------------------------------------------------------------------------------------------------
	function get_RoomIdByOutputDevice($DeviceName) {
        $SourceConf = get_SourceConfiguration();
        foreach ($SourceConf as $RoomName=>$RoomSources) {
            $RoomId     = get_RoomId($RoomName);
            $SourceIdx  = get_SourceIdxByRoomId($RoomId);
            $RoomSource = $RoomSources[$SourceIdx];
			if (array_key_exists(c_Property_Output, $RoomSource)) {
				$OutputDevices = $RoomSource[c_Property_Output];
                // wrap older/non array configuration in an array for downward compatibility
                if(isset($OutputDevices[c_Property_Device])) {
                    $OutputDevices = array($OutputDevices);
                }
                
				$OutputName = $OutputDevices[0][c_Property_Device];
				if ($OutputName == $DeviceName) {
					return $RoomId;
				}
			}
        }
        return false;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_SourceListByDeviceName($DeviceName) {
	   $SourceConfig = get_SourceConfiguration();
	   $SourceList   = array();
	   foreach ($SourceConfig as $RoomName=>$RoomSources) {
	      $RoomId = get_RoomId($RoomName);
	      $CurrentIdx  = get_SourceIdxByRoomId($RoomId);
	      foreach ($RoomSources as $SourceIdx=>$SourceData) {
	   		$DeviceNames = get_DeviceNamesByRoomId($RoomId, $SourceIdx, array(c_Property_Output));
				if (in_array($DeviceName, $DeviceNames)) {
                    if (!array_key_exists($RoomId, $SourceList)) {
                        $SourceList[$RoomId] = $SourceIdx;
                    }
					if ($CurrentIdx==$SourceIdx) {
                        $SourceList[$RoomId] = $SourceIdx;
                        break;
					}
				}
	      }
	   }
	   return $SourceList;
	}

  /** @}*/
?>