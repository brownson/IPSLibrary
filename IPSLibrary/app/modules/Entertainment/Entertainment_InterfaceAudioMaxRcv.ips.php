<?
	/**@addtogroup entertainment_interface
	 * @ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_InterfaceAudioMaxRcv.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * AudioMax Multiroom Anbindung
	 *
	 */

	include_once "Entertainment.inc.php";
	include_once "Entertainment_InterfaceAudioMaxRcv.ips.php";
	include_once "AudioMax.inc.php";


	$variableId = IPS_VARIABLE;
	$value      = IPS_VALUE;
	
	if ($value == "") return;

	$parameters    = explode(AM_COM_SEPARATOR, $value);

	$command  = $parameters[1];
	$roomId   = $parameters[2];
	$function = $parameters[3];
	$value    = $parameters[4];

	if ($command==AM_CMD_ROOM) $roomId=$roomId-1;
	if ($command==AM_FNC_INPUTSELECT) $value=$value-1;

	if ($function==AM_FNC_VOLUME) {
		$muting = Entertainment_AudioMax_GetValue(array(c_Comm_AudioMax, $command, $roomId, $function, $value), c_Control_Muting);
		if ($value==AM_VAL_VOLUME_MAX and $muting) {
			return;
		} elseif ($muting) {
			Entertainment_ReceiveData(array(c_Comm_AudioMax, AM_COM_ROO, $roomId, 'mut', AM_FNC_BOOLEAN_FALSE), c_MessageType_Info);
		} else {
		}
		$value = AM_FNC_VOLUME_MAX - $value;

   } elseif ($function==AM_FNC_BALANCELEFT) {
      $function = 'bal';
      $value    = round(AM_FNC_BALANCERIGHT/2 - $value/2);

   } elseif ($function==AM_FNC_BALANCERIGHT) {
      $function = 'bal';
      $value    = round(AM_FNC_BALANCERIGHT/2 + $value/2);

   }

   $parameters = array(c_Comm_AudioMax, $command, $roomId, $function, $value);

   IPSLogger_Dbg(__file__, "Received Data to AudioMax: Command='$command', RoomId='$roomId', Function='$function' Value=$value");
 	Entertainment_ReceiveData($parameters, c_MessageType_Info);

  /** @}*/
?>