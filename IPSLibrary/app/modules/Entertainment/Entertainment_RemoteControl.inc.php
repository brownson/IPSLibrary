<?
	/**@ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_RemoteControl.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * RemoteControls der Geräte setzen
	 *
	 */

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetRemoteControlType($Id, $Value, $ControlType) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		IPSLogger_Inf(__file__, 'Set RemoteControlType "'.$Value.'" for Device "'.$DeviceName.'"');

		if (GetValue($Id) <> $Value) {
			SetValue($Id, $Value);
			
			$DeviceConfig = get_DeviceConfiguration();
			$Remote = c_RemoteControlHtmlPrefix.$DeviceConfig[$DeviceName][$ControlType][c_Property_Names][$Value].c_RemoteControlHtmlSuffix;
			Entertainment_SetRemoteControl(get_ControlIdByDeviceName($DeviceName, $ControlType), $Remote);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_SetRemoteControl($Id, $Value) {
	   $DeviceName = IPS_GetName(IPS_GetParent($Id));
		if (!isDevicePoweredOnByDeviceName($DeviceName)) {
		  return;
		}
		IPSLogger_Dbg(__file__, 'Set RemoteControl for Device "'.$DeviceName.'": '.$Value);
		SetValue($Id, $Value);
		Entertainment_SetRoomControlByDeviceControlId($Id, $Value);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function	Entertainment_RefreshRemoteControlByDeviceName($DeviceName, $ControlType=c_Control_RemoteSource) {
		$RemoteControlId = get_ControlIdByDeviceName($DeviceName, $ControlType, false);
		if ($RemoteControlId !== false) {
		   Entertainment_SetRemoteControl($RemoteControlId, GetValue($RemoteControlId));
		}
	}

  /** @}*/
?>