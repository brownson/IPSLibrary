<?php 
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_Mobile.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * File kann in das Mobil Frontend eingebunden werden (zB per iFrame) und ermöglicht das Steuern 
	 * des Netplayers.
	 *
	 */

	 IPSUtils_Include ("NetPlayer.inc.php", "IPSLibrary::app::modules::NetPlayer2");

	IPSLogger_Inf(__file__,'Load Mobile GUI ...');
   
	$RemoteControlType = GetValue(NP_ID_CONTROLTYPE);
	
	switch($RemoteControlType) {
		case 0:
			include "NetPlayer_MobilePlayer.php";
			break;
		case 1:
			include "NetPlayer_MobileSelection.php";
			break;
		case 2:
			include "NetPlayer_MobileRadio.php";
			break;
		default:
			echo "Unknown RemoteControlType $RemoteControlType";
	}


	 /** @}*/
?>
