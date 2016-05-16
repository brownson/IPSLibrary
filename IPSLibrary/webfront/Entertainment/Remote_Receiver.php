<?
	IPSUtils_Include ("Entertainment_InterfaceWinLIRCRcv.ips.php", "IPSLibrary::app::modules::Entertainment");

	// -------------------------------------------------------------------------------------------------
	// RemoteControl Button
	// -------------------------------------------------------------------------------------------------
	if ($_POST['rc_action']=="rc_cmd") {

		WinLIRC_ReceiveData_Webfront($_POST['rc_name'], $_POST['rc_button']);

		if ($_POST['rc_button2'] <> "") {
			WinLIRC_ReceiveData_Webfront($_POST['rc_name2'], $_POST['rc_button2']);
		}
		if ($_POST['rc_button3'] <> "") {
			WinLIRC_ReceiveData_Webfront($_POST['rc_name3'], $_POST['rc_button3']);
		}

	} else if ($_POST['rc_action']=="rc_program") {
		echo WinLIRC_ReceiveData_Program($_POST['rc_program'], $_POST['rc_devicename']);

	} else {
		IPSLogger_Wrn(__file__, "Received Unknown RemoteControl-Action '".$_POST['rc_action']."'");
	}
?>