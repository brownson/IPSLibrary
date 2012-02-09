<?
	/**@addtogroup entertainment
	 * @{
	 *
	 * @file          Entertainment_YamahaCustom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Callback Methoden der Entertainment Steuerung
	*/

	IPSUtils_Include ("Entertainment_InterfaceWinLIRCSnd.inc.php", "IPSLibrary::app::modules::Entertainment");
	IPSUtils_Include ("Entertainment.inc.php",                     "IPSLibrary::app::modules::Entertainment");

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_Before_SendData($Parameters) {
		if ($Parameters[0]==c_Comm_WinLIRC and $Parameters[1]=='yamahareceiver') {
			$Button      = $Parameters[2];

			// Special Handling for Muting of YamahaReceiver: Use InputSelector Phone for Muting,
			// Switch back to current Input when Muting Off
			// ==================================================================================
			if ($Button == "muteon") {
				IPSLogger_Dbg(__file__, "Set Muting 'On' for Yamaha Receiver (Switch to Phone Input)");
            WinLIRC_SendData(array(c_Comm_WinLIRC, 'yamahareceiver', 'phone'));
				return false; // Abort current Processing

			} else if ($Button=='muteoff') {
				$RoomId = get_RoomId(c_Room_LivingRoom);
				$SourceIdx = get_SourceIdxByRoomId($RoomId);
		   	Entertainment_SendDataBySourceIdx($RoomId, $SourceIdx, c_MessageType_Action);
				return false; // Abort current Processing

		   // Special Handling for Yamaha Tuner, YamahaReceiver supports only Previous/Next Station for Tuner
		   // --> Simulate Buttons for Station "1" - "8" by Previous and Next.
	   	// ===============================================================================================
			} else if ($Button == "0" or $Button == "1" or $Button == "2" or $Button == "3" or $Button == "4" or
			           $Button == "5" or $Button == "6" or $Button == "7") {
				$ControlId   = get_ControlIdByDeviceName(c_Device_YamahaTuner, c_Control_Program);
				$StationNew  = GetValue($ControlId);
				$StationObj  = IPS_GetObject($ControlId);
				$StationCurr = $StationObj["ObjectInfo"];
				IPSLogger_Trc(__file__, "Switch YamahaTuner from StationCurrent=".$StationCurr." to  StationNew=".$StationNew);

				if ($StationNew-$StationCurr <= 4 and $StationNew-$StationCurr >= 0) {
				   $IRButton = 'presetnext';
					$Count = $StationNew-$StationCurr;
				} else if (($StationCurr-$StationNew) < 0) {
				   $IRButton = 'presetlast';
					$Count = 8-($StationNew-$StationCurr);
				} else if (($StationCurr-$StationNew) <= 4) {
				   $IRButton = 'presetlast';
					$Count = $StationCurr-$StationNew;
				} else {
				   $IRButton = 'presetnext';
					$Count = 8-$StationCurr+$StationNew;
				}

				IPS_SetInfo ($ControlId, $StationNew);
				IPSLogger_Dbg(__file__, "Switch Yamaha TunerStation from ".$StationCurr." to ".$StationNew." ==> ".$Count."x ".$IRButton);
				for ($idx=1; $idx<=$Count; $idx++) {
					include_once "Entertainment_InterfaceWinLIRC.ips.php";
               WinLIRC_SendData(array(c_Comm_WinLIRC, 'yamahareceiver', $IRButton));
					sleep(0.2);
				}
				return false; // Abort current Processing
			}
		}
		return true;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_After_SendData($Parameters) {
		//include_once "Presence.ips.php";
	   //Presence_RegisterAction(null, null);

	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_Before_ReceiveData($Parameters, $MessageType) {
		return true;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_After_ReceiveData($Parameters, $MessageType) {
		//include_once "Presence.ips.php";
	   //Presence_RegisterAction(null, null);
	}

	/** @}*/
?>