<?
	IPSUtils_Include ("Entertainment_InterfaceWinLIRCRcv.ips.php", "IPSLibrary::app::modules::Entertainment");

	// -------------------------------------------------------------------------------------------------
	// RemoteControl Button
	// -------------------------------------------------------------------------------------------------
	if ($_GET['rc_action']=="rc_cmd") {

		WinLIRC_ReceiveData_Webfront($_GET['rc_name'], $_GET['rc_button']);

		if ($_GET['rc_button2'] <> "") {
			WinLIRC_ReceiveData_Webfront($_GET['rc_name2'], $_GET['rc_button2']);
		}
		if ($_GET['rc_button3'] <> "") {
			WinLIRC_ReceiveData_Webfront($_GET['rc_name3'], $_GET['rc_button3']);
		}

	} else if ($_GET['rc_action']=="rc_program") {
		echo WinLIRC_ReceiveData_Program($_GET['rc_program'], $_GET['rc_devicename']);

	} else {
		IPSLogger_Wrn(__file__, "Received Unknown RemoteControl-Action '".$_GET['rc_action']."'");
	}
?>