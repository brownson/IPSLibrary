<?
	/**@ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_Communication.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Funktionen zur Kommunikation mit den diversen Interfaces
	 *
	 */

	// ---------------------------------------------------------------------------------------------------------------------------
	function ExtractCommProperties($PropertyData, $CommParams, $ControlData=null) {
      if (!is_array($PropertyData) || !is_array($CommParams)) {
          return false;
	   }
	   if (count($PropertyData) <> count($CommParams)) {
			return false;
		}
	   $CommProperties = array();
	   foreach ($PropertyData as $Idx=>$Property) {
	      $CommParam = $CommParams[$Idx];
	      
	      if ($Property === $CommParams[$Idx]) {
	         //Ok, Continue
	      } else if ($Property === c_Template_Value) {
		      $CommProperties[c_Template_Value] = $CommParams[$Idx];
	      } else if ($Property === c_Template_Code and
			           array_key_exists(c_Property_Codes,$ControlData) and
						  array_key_exists($CommParam, array_flip($ControlData[c_Property_Codes])) ) {
		      $CommProperties[c_Template_Code] = $CommParams[$Idx];
		      $Codes = array_flip($ControlData[c_Property_Codes]);
		      $CommProperties[c_Template_Value] = $Codes[$CommParams[$Idx]];
	      } else if ($Property === c_Template_Code2 and
			           array_key_exists(c_Property_Codes2,$ControlData) and
						  array_key_exists($CommParam, array_flip($ControlData[c_Property_Codes2])) ) {
		      $CommProperties[c_Template_Code2] = $CommParams[$Idx];
		      $Codes = array_flip($ControlData[c_Property_Codes2]);
		      $CommProperties[c_Template_Value] = $Codes[$CommParams[$Idx]];
			} else {
			   return false;
			}
	   }
	   return $CommProperties;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_CommPropertiesSource($CommParams) {
	   $SourceConf = get_SourceConfiguration();
	   foreach ($SourceConf as $RoomName => $RoomSourceData) {
	   	foreach ($RoomSourceData as $SourceIdx => $SourceData) {
		   	foreach ($SourceData as $SourcePropertyName => $SourcePropertyData) {
	   	   	if (is_array($SourcePropertyData)) {
			   		foreach ($SourcePropertyData as $DevicePropertyName => $DevicePropertyData) {
							$CommProperties = ExtractCommProperties($DevicePropertyData, $CommParams);
							if ($CommProperties !== false) {
								$CommProperties[c_Property_Comm]     = $DevicePropertyName;
								$CommProperties[c_Property_Device] 	 = $SourceConf[$RoomName][$SourceIdx][$SourcePropertyName][c_Property_Device];
								$CommProperties[c_Property_Control]  = c_Control_Source;
								$CommProperties[c_Property_Name]		 = $SourcePropertyName;
								$CommProperties[c_Property_SourceIdx]= $SourceIdx;
								$CommProperties[c_Property_Room]     = $RoomName;
							return $CommProperties;
						}
			   		}
		   		}
	   		}
	   	}
	   }
		return false;
	}


	// ---------------------------------------------------------------------------------------------------------------------------
	function get_CommPropertiesDevice($CommParams) {
	   $DeviceConf = get_DeviceConfiguration();
	   foreach ($DeviceConf as $DeviceName => $DeviceData) {
	   	foreach ($DeviceData as $ControlType => $ControlData) {
	   	   if (is_array($ControlData)) {
		   		foreach ($ControlData as $PropertyName => $PropertyData) {
						$CommProperties = ExtractCommProperties($PropertyData, $CommParams, $ControlData);
						if ($CommProperties !== false) {
		   		      $CommProperties[c_Property_Comm]    = $PropertyName;
							$CommProperties[c_Property_Device] 	= $DeviceName;
							$CommProperties[c_Property_Control] = $ControlType;
							$CommProperties[c_Property_Name]		= $PropertyName;
		   		      return $CommProperties;
		   		   }
		   		}
		   	}
	   	}
	   }
		return false;
	}
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function get_CommProperties($CommParams) {
		$CommProperties = get_CommPropertiesDevice($CommParams);
		if  ($CommProperties !== false) {
			return $CommProperties;
		}
		$CommProperties = get_CommPropertiesSource($CommParams);
		if  ($CommProperties !== false) {
			return $CommProperties;
		}
		return false;
	}



	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_ReceiveData($CommParams, $MessageType=c_MessageType_Action) {
		if (!Entertainment_Before_ReceiveData($CommParams, $MessageType)) {
			return true;
		}
	
		$CommProperties = get_CommProperties($CommParams);
		if ($CommProperties !== false) {
			$CommType   = $CommProperties[c_Property_Comm];
			$DeviceName = $CommProperties[c_Property_Device];
			IPSLogger_Trc(__File__, 'Received Data "'.implode($CommParams, '.').'" for Device '.$DeviceName);
			switch ($CommType) {
				case c_Property_CommPower:
				case c_Property_CommPower2:
					Entertainment_SetDevicePower(get_ControlIdByDeviceName($DeviceName, c_Control_DevicePower), c_Value_Toggle, $MessageType);
					break;
				case c_Property_CommPowerOn:
				case c_Property_CommPowerOn2:
					Entertainment_SetDevicePower(get_ControlIdByDeviceName($DeviceName, c_Control_DevicePower), true, $MessageType);
					break;
				case c_Property_CommPowerOff:
				case c_Property_CommPowerOff2:
					Entertainment_SetDevicePower(get_ControlIdByDeviceName($DeviceName, c_Control_DevicePower), false, $MessageType);
					break;
				case c_Property_CommMute:
				case c_Property_CommMute2:
					Entertainment_SetMuting(get_ControlIdByDeviceName($DeviceName, c_Control_Muting), c_Value_Toggle, $MessageType);
					break;
				case c_Property_CommMuteOn:
				case c_Property_CommMuteOn2:
					Entertainment_SetMuting(get_ControlIdByDeviceName($DeviceName, c_Control_Muting), true, $MessageType);
					break;
				case c_Property_CommMuteOff:
				case c_Property_CommMuteOff2:
					Entertainment_SetMuting(get_ControlIdByDeviceName($DeviceName, c_Control_Muting), false, $MessageType);
					break;
				case c_Property_CommVol:
				case c_Property_CommVol2:
					Entertainment_SetVolume(get_ControlIdByDeviceName($DeviceName, c_Control_Volume),
					                        (int)$CommProperties[c_Template_Value], $MessageType);
					break;
				case c_Property_CommVolPlus:
					Entertainment_SetVolumeDiff(get_ControlIdByDeviceName($DeviceName, c_Control_Volume),
					                            5, $MessageType);
					break;
				case c_Property_CommVolMinus:
					Entertainment_SetVolumeDiff(get_ControlIdByDeviceName($DeviceName, c_Control_Volume),
					                            -5, $MessageType);
					break;
				case c_Property_CommBal:
				case c_Property_CommBal2:
					Entertainment_SetBalance(get_ControlIdByDeviceName($DeviceName, c_Control_Balance),
					                        (int)$CommProperties[c_Template_Value], $MessageType);
					break;
				case c_Property_CommTre:
				case c_Property_CommTre2:
					Entertainment_SetTreble(get_ControlIdByDeviceName($DeviceName, c_Control_Treble),
					                        (int)$CommProperties[c_Template_Value], $MessageType);
					break;
				case c_Property_CommMid:
				case c_Property_CommMid2:
					Entertainment_SetMiddle(get_ControlIdByDeviceName($DeviceName, c_Control_Middle),
					                        (int)$CommProperties[c_Template_Value], $MessageType);
					break;
				case c_Property_CommBas:
				case c_Property_CommBas2:
					Entertainment_SetBass(get_ControlIdByDeviceName($DeviceName, c_Control_Bass),
					                        (int)$CommProperties[c_Template_Value], $MessageType);
					break;
				case c_Property_CommMode:
				case c_Property_CommMode2:
					Entertainment_SetMode(get_ControlIdByDeviceName($DeviceName, c_Control_Mode),
					                      $CommProperties[c_Template_Value], $MessageType);
					break;
				case c_Property_CommRemSrc:
					$ControlId = get_ControlIdByDeviceName($DeviceName, c_Control_RemoteSourceType);
					$Value = $CommProperties[c_Template_Value];
					Entertainment_SetRemoteControlType($ControlId,
					                                   $Value,
					                                   c_Control_RemoteSource);
					break;
				case c_Property_CommRemVol:
					Entertainment_SetRemoteControlType(get_ControlIdByDeviceName($DeviceName, c_Control_RemoteVolumeType),
					                                   $CommProperties[c_Template_Value],
																  c_Control_RemoteVolume);
					break;
				case c_Property_CommSrc:
				case c_Property_CommSrc2:
					$SourceIdx  = $CommProperties[c_Property_SourceIdx];
					$RoomName   = $CommProperties[c_Property_Room];
					IPSLogger_Inf(__file__, "Switch Source for $RoomName to $SourceIdx");
					Entertainment_SetSource(get_ControlIdByRoomId(get_RoomId($RoomName), c_Control_Source), $SourceIdx, $MessageType);
					break;
				case c_Property_CommSrcNext:
					$RoomName   = $CommProperties[c_Property_Room];
					IPSLogger_Inf(__file__, "Switch Next Source for $RoomName");
					Entertainment_SetSourceNext(get_ControlIdByRoomId(get_RoomId($RoomName), c_Control_Source), $MessageType);
					break;
				case c_Property_CommPrg:
				case c_Property_CommPrg2:
					Entertainment_SetProgram(get_ControlIdByDeviceName($DeviceName, c_Control_Program),
					                         $CommProperties[c_Template_Value], $MessageType);
					break;
				case c_Property_CommPrgPrev:
				case c_Property_CommPrgPrev2:
					Entertainment_SetProgramPrev(get_ControlIdByDeviceName($DeviceName, c_Control_Program), $MessageType);
					break;
				case c_Property_CommPrgNext:
				case c_Property_CommPrgNext2:
					Entertainment_SetProgramNext(get_ControlIdByDeviceName($DeviceName, c_Control_Program), $MessageType);
					break;
				default:
					IPSLogger_Err(__File__, 'Found unknown CommunicationType "'.$CommType.'"');
			}
			Entertainment_After_ReceiveData($CommParams, $MessageType);
			return true;
		}
		Entertainment_After_ReceiveData($CommParams, $MessageType);
		return false;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SendData($DeviceName, $ControlType, $CommParams, $CommType) {
		$CommConfig     = get_CommunicationConfiguration();
		$CommInterface  = $CommParams[0];
		$FunctionName   = $CommConfig[$CommInterface][c_Property_FunctionSnd];
		$FunctionScript = $CommConfig[$CommInterface][c_Property_ScriptSnd];
		$FunctionParameters = array();
		foreach ($CommParams as $CommIdx => $CommParam) {
		   if ($CommParam === c_Template_Value) {
			   $FunctionParameters[] = GetValue(get_ControlIdByDeviceName($DeviceName, $ControlType));
		   } else if ($CommParam === c_Template_Code) {
		      $DeviceConfig = get_DeviceConfiguration();
		      $Value = GetValue(get_ControlIdByDeviceName($DeviceName, $ControlType));
			   $FunctionParameters[] = $DeviceConfig[$DeviceName][$ControlType][c_Property_Codes][$Value];
			} else {
				$FunctionParameters[] = $CommParam;
			}
		}
		if (!Entertainment_Before_SendData($FunctionParameters)) {
			return;
		}
	   IPSLogger_Trc(__file__, 'SendData '.$CommInterface.'.'.$FunctionName.'('.print_r($FunctionParameters, true).')');
		try {
			include_once $FunctionScript;
			$Function = new ReflectionFunction($FunctionName);
			$Function->invoke($FunctionParameters);
		} catch (Exception $e) {
			IPSLogger_Err(__file__, 'Error Executing Function '.$FunctionName.':'.$e->getMessage());
		}
  		Entertainment_After_SendData($FunctionParameters);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SendDataByDeviceName($DeviceName, $ControlType, $CommTypeList, $MessageType) {
	   if ($MessageType <> c_MessageType_Action) {
	      return;
		}
	   $DeviceData = get_DeviceConfiguration();
	   $Control = $DeviceData[$DeviceName][$ControlType];
	   foreach ($CommTypeList as $CommType) {
	      if (array_key_exists($CommType, $Control)) {
	         Entertainment_SendData($DeviceName, $ControlType, $Control[$CommType], $CommType);
				break;
	      }
	   }
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SendDataBySourceIdx($RoomId, $SourceIdx, $MessageType) {
	   if ($MessageType <> c_MessageType_Action) {
	      return;
		}
	   $RoomName    = IPS_GetName($RoomId);
        $DeviceTypes = get_SourceDeviceTypes($RoomId, $SourceIdx);
        $SourceConf  = get_SourceConfiguration();
        foreach ($DeviceTypes as $DeviceType=>$DeviceName) {
            $SourcesData  = $SourceConf[$RoomName][$SourceIdx][$DeviceType];
            
            // wrap older/non array configuration in an array for downward compatibility
            if(isset($SourcesData[c_Property_Device])) {
                $SourcesData = array($SourcesData);
            }
            
			foreach($SourcesData as $SourceData) {
				$DeviceName = $SourceData[c_Property_Device];
				if (array_key_exists(c_Property_CommSrc, $SourceData)) {
					Entertainment_SendData($DeviceName, c_Control_Source, $SourceData[c_Property_CommSrc], c_Property_CommSrc);
				}
			}
      }
	}

  /** @}*/

?>