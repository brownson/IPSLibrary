<?php 
	/**
	 * This file is part of the IPSLibrary.
	 *
	 * The IPSLibrary is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published
	 * by the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * The IPSLibrary is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
	 */

	/**@addtogroup ipscam
	 * @{
	 *
	 * @file          IPSCam_CameraUtils.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 10.09.2012<br/>
	 *
	 * Utility Funktionen für die IPSCam HTML Ansicht
	 *
	 */

	/** @}*/

	$agent  = $_SERVER['HTTP_USER_AGENT'];
	$mobileGUI = preg_match("@ipod@i", $agent) || preg_match("@ipad@i", $agent) || preg_match("@iphone@i",$agent);
	if ($mobileGUI) {
		echo '<link rel="stylesheet" type="text/css" href="/user/IPSCam/IPSCam_CameraMobile.css" />'.PHP_EOL.PHP_EOL;
	} else {
		echo '<link rel="stylesheet" type="text/css" href="/user/IPSCam/IPSCam_CameraWebFront.css" />'.PHP_EOL.PHP_EOL;
	}

	IPSUtils_Include ("IPSCam.inc.php",         "IPSLibrary::app::modules::IPSCam");
	$camManager = new IPSCam_Manager();
?>

		<script type="text/javascript" src="jquery.min.js"></script>

		<script type="text/javascript">
			function trigger_button() {
				var serverAddr = "<?echo $_SERVER["HTTP_HOST"];?>";
				var id         = $(this).attr("id");
				var cameraidx  = $(this).attr("cameraidx");
				$('#'+id).addClass("camButtonSelected");

				$.ajax({type: "POST",
						url: "http://"+serverAddr+"/user/IPSCam/IPSCam_CameraReceiver.php",
						data: "id="+id+"&cameraidx="+cameraidx });

				 setTimeout(function(){
					$('#'+id).removeClass("camButtonSelected");}, 200);
			}

			$(function(){$(".camButton").click(trigger_button);});
		</script>


