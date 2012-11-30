<?
	/**@addtogroup ipscam
	 * @{
	 *
	 * @file          IPSCam_Receiver.php
	 * @author        Andreas Brauneis
	 * @version
	 *    Version 2.50.1, 23.09.2012<br/>
	 *
	 * Empfangs Script um Requests (JQuery) der HTML Seiten zu bearbeiten.
	 *
	 */

	$id         = $_GET['id'];
	$cameraIdx  = $_GET['cameraidx'];
	IPSUtils_Include ("IPSCam.inc.php", "IPSLibrary::app::modules::IPSCam");

	$camManager = new IPSCam_Manager();

	IPSLogger_Dbg(__file__, 'Process IPSCam Command='.$id.' for Camera='.$cameraIdx);

	switch ($id) {
		// Predefined Commands
		// ---------------------------------------------------------------------------------------
		case 'camButtonCommand1':
			$camManager->ProcessCommand($cameraIdx, IPSCAM_PROPERTY_ACTION1);
			break;
		case 'camButtonCommand2':
			$camManager->ProcessCommand($cameraIdx, IPSCAM_PROPERTY_ACTION2);
			break;
		case 'camButtonCommand3':
			$camManager->ProcessCommand($cameraIdx, IPSCAM_PROPERTY_ACTION3);
			break;
		case 'camButtonCommand4':
			$camManager->ProcessCommand($cameraIdx, IPSCAM_PROPERTY_ACTION4);
			break;

		// Navigation
		// ---------------------------------------------------------------------------------------
		case 'camButtonNavLeft':
			$camManager->Move($cameraIdx, IPSCAM_URL_MOVELEFT);
			break;
		case 'camButtonNavRight':
			$camManager->Move($cameraIdx, IPSCAM_URL_MOVERIGHT);
			break;
		case 'camButtonNavUp':
			$camManager->Move($cameraIdx, IPSCAM_URL_MOVEUP);
			break;
		case 'camButtonNavDown':
			$camManager->Move($cameraIdx, IPSCAM_URL_MOVEDOWN);
			break;
			
		// Predefined Positions
		// ---------------------------------------------------------------------------------------
		case 'camButtonPreDef1':
			$camManager->Move($cameraIdx, IPSCAM_URL_PREDEFPOS1);
			break;
		case 'camButtonPreDef2':
			$camManager->Move($cameraIdx, IPSCAM_URL_PREDEFPOS2);
			break;
		case 'camButtonPreDef3':
			$camManager->Move($cameraIdx, IPSCAM_URL_PREDEFPOS3);
			break;
		case 'camButtonPreDef4':
			$camManager->Move($cameraIdx, IPSCAM_URL_PREDEFPOS4);
			break;
			
		default:
			IPSLogger_Err(__file__, "Received Unknown IPSCam Command=".$id);
	}

	/** @}*/
?>