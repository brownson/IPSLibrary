<?
	/**@addtogroup entertainment_interface
	 * @ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_InterfaceAudioMaxSnd.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * AudioMax Multiroom Anbindung
	 *
	 */

	include_once "Entertainment.inc.php";
	include_once "AudioMax.inc.php";

	function Entertainment_AudioMax_GetValue($Parameters, $ControlType) {
		$CommProperties = get_CommProperties($Parameters);
	   $CommType   = $CommProperties[c_Property_Comm];
	   $DeviceName = $CommProperties[c_Property_Device];
		$VolumeControlId   = get_ControlIdByDeviceName($DeviceName, $ControlType);
		return GetValue($VolumeControlId);
	}


	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_AudioMax_SendData($parameters) {
	   return;
     	$command  = $parameters[1];
     	$roomId   = $parameters[2];
     	$function = $parameters[3];
     	$value    = $parameters[4];

		if ($command==AM_CMD_ROOM) $roomId=$roomId-1;
		if ($command==AM_FNC_INPUTSELECT) $value=$value-1;

     	if ($function==AM_FNC_VOLUME) {
     	   $value = AM_VAL_VOLUME_MAX - $value;
     	} elseif ($function==AM_FNC_POWER and $command==AM_CMD_ROOM) {
     	   if ((AM_CONFIG_ROOMAMPLIFIER1 and $room==0) or
			    (AM_CONFIG_ROOMAMPLIFIER2 and $room==1) or
				 (AM_CONFIG_ROOMAMPLIFIER3 and $room==2) or
				 (AM_CONFIG_ROOMAMPLIFIER4 and $room==3)) return true;
     	} elseif ($function=='mut') {
     	   $function = AM_FNC_VOLUME;
     	   if ($value==AM_VAL_BOOLEAN_TRUE) {
	     	   $value = AM_VAL_VOLUME_MAX;
     	   } else {
	     	   $value = Entertainment_AudioMax_GetValue($parameters, c_Control_Volume);
     	   }
     	} elseif ($function=='bal') { // 78 Mute, 0 volle Lautstärke
     	   if ($value<=AM_VAL_BALANCELEFT_MAX/2) {
	     	   $function = AM_FNC_BALANCELEFT;
     	   	$value    = round(AM_VAL_BALANCELEFT_MAX - $value*2);
			} else {
     	      $function = AM_FNC_BALANCERIGHT;
     	   	$value    = round(AM_VAL_BALANCELEFT_MAX - (AM_VAL_BALANCELEFT_MAX-$value)*2);
			} 
		}
     	IPSLogger_Dbg(__file__, "Send Data to AudioMax: Command='$command', RoomId='$roomId', Function='$function' Value=$value");
     	$server = new AudioMaxServer(AM_CONFIG_INSTANCE);
		return $server->SendData(AM_TYP_SET, $command, $roomId, $function, $value);

	}

  /** @}*/
?>