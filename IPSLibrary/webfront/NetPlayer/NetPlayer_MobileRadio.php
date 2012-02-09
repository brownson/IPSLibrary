<?php 
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_MobileRadio.php
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
	IPSUtils_Include ("NetPlayer.inc.php", "IPSLibrary::app::modules::NetPlayer");

	/** @}*/
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

	<link rel="stylesheet" type="text/css" href="mNetPlayer.css" />
	<?
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
		<div id="containerControlLine0" class="containerControlLine100">
			<div  id= "rc_mp_play" class="containerControlButton">Play</div>
		</div>
		<div id="containerControlLine1" class="containerControlLine50">
			<div  id= "rc_mp_pause" class="containerControlButton" style="margin-left:-10px;">Pause</div>
		</div>
		<div id="containerControlLine2" class="containerControlLine50">
			<div  id= "rc_mp_stop" class="containerControlButton" style="margin-right:-10px;">Stop</div>
		</div>
		<div id="containerControlLine3" class="containerControlLine">
			<div  id= "rc_mp_descr" class="containerControlDescr">Radio</div>
			<div  id= "rc_mp_radiotitel" class="containerControlData"><?php echo GetValue(NP_ID_RADIONAME);?></div>
		</div>
		<div id="containerControlLine4" class="containerControlLine100">
			<div  id= "rc_mp_player" class="containerControlButton">Musik Player</div>
		</div>

		<div id="containerControlLine2" class="containerControlLineSeparator">
		</div>

		<div id="containerControlLine0" class="containerControlLine">

			<?
				$left = true;
				$idx  = 0;
				foreach (NetPlayer_GetRadioList() as $radioName=>$radioURL) {
					echo '<div id="containerControlLine2" class="containerControlLine50">';
					if ($left) {
						echo '<div  id= "rc_mp_radio'.$idx.'" class="containerControlButton" radiourl="'.$radioURL.'" style="margin-left:-10px;">'.$radioName.'</div>';
					} else {
						echo '<div  id= "rc_mp_radio'.$idx.'" class="containerControlButton" radiourl="'.$radioURL.'" style="margin-right:-10px;">'.$radioName.'</div>';
					}		
					echo '</div>';
					$idx++;
					$left = !$left;
				}
			?>
		</div>
	</div>


  </body>
</html>
