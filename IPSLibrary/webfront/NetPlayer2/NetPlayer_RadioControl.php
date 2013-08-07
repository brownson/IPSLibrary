<?php 
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_RadioControl.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * File kann in das WebFront eingebunden werden (zB. per iFrame) und ermöglicht das Abspielen diverser 
	 * Web Radios.
	 *
	 * Die Definition der Radio Sender mit zugehöriger URL wird im File NetPlayer_Configuration.ips.php vorgenommen.
	 *
	 */
	/** @}*/
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

	<link rel="stylesheet" type="text/css" href="NetPlayer.css" />
	<?
		IPSUtils_Include ("NetPlayer.inc.php", "IPSLibrary::app::modules::NetPlayer2");
		include_once "NetPlayer_Sender.php";
		include_once "NetPlayer_Utils.php";
	?>
	<script>
		$(function(){$(".containerControlButton").click(refresh_radio);});
		$(function() {refresh_radio();});	  
	</script>
  </head>

  <body>
	<div id="containerRadioSelect" class="containerRadioSelect">
		<div id="containerControlLine0" class="containerControlLine">
			<?
				$left = true;
				$idx  = 0;
				foreach (NetPlayer_GetRadioList() as $radioName=>$radioURL) {
					echo '<div  id= "rc_mp_radio'.$idx.'" class="containerControlButton" style="width:200px;" radiourl="'.$radioURL.'">'.$radioName.'</div>';
					$idx++;
					$left = !$left;
				}
			?>
		</div>
	</div>

	<div id="containerRadioInfo" class="containerRadioInfo">
		<div id="containerControlLine1" class="containerControlLine">
			<div  id= "rc_mp_play" class="containerControlButton" style="width:126px;">Play</div>
			<div  id= "rc_mp_stop" class="containerControlButton" style="width:126px;">Stop</div>
		</div>
		<div id="containerControlLine2" class="containerControlLine">
			<div  id= "rc_mp_pause" class="containerControlButton" style="width:126px;">Pause</div>
		</div>
		<div id="containerControlLine3" class="containerControlLine">
			<div  id= "rc_mp_radiotitel" class="containerControlData" style="overflow:auto;height:20px;font:normal 20px verdana; text-align:center; padding:10px; height:40px;"><?php echo GetValue(NP_ID_RADIONAME);?></div>
		</div>
		<div id="containerControlLine8" class="containerControlLineBottom">
			<div  id= "rc_mp_player" class="containerControlButton" style="width:296px;">Musik Player</div>
		</div>
	</div>


  </body>
</html>
