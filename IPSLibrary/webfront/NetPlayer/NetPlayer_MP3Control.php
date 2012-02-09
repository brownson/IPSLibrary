<?php 
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_MP3Control.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * File kann in das WebFront eingebunden werden (zB per iFrame) und ermöglicht das Steuern 
	 * des MP3 Players.
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
		IPSUtils_Include ("NetPlayer.inc.php", "IPSLibrary::app::modules::NetPlayer");
		include_once "NetPlayer_Sender.php";
		include_once "NetPlayer_Utils.php";
	?>
	<script>
		$(function(){$(".containerControlTrack").click(trigger_button);});
		$(function(){$(".containerControlButton").click(refresh_player);});
		$(function() {refresh_player();});	  
		setInterval(refresh_tracktime, 1000);
		setInterval(refresh_player, 10000);
	</script>
	</head>
  
  <body>
	<div id="containerPlayList" class="containerPlayList">
		<table class="rc_mp_tracklisttab"><?php echo GetValue(NP_ID_CDTRACKLISTHTML);?></table>
	</div>
	<div id="containerCover"  class="containerCover">
		<table width=100%><tr><th id="mp_cover" style="height:230px" ><img style="height:150; width:150px; vertical-align:middle" src="NetPlayer_Cover.jpg" alt=""></th></tr></table>
	</div>
	<div id="containerControl" class="containerControl">
		<div id="containerControlLine1" class="containerControlLine">
			<div  id= "rc_mp_last" class="containerControlButton" style="width:109px;">Last</div>
			<div  id= "rc_mp_play" class="containerControlButton" style="width:109px;">Play</div>
			<div  id= "rc_mp_next" class="containerControlButton" style="width:109px;">Next</div>
		</div>
		<div id="containerControlLine2" class="containerControlLine">
			<div  id= "rc_mp_pause" class="containerControlButton" style="width:109px;">Pause</div>
			<div  id= "rc_mp_stop" class="containerControlButton" style="width:109px;">Stop</div>
		</div>
		<div id="containerControlLine3" class="containerControlLine">
			<div  id= "rc_mp_descr" class="containerControlDescr">Interpret</div>
			<div  id= "rc_mp_interpret" class="containerControlData"></div>
		</div>
		<div id="containerControlLine4" class="containerControlLine">
			<div  id= "rc_mp_descr" class="containerControlDescr">Album</div>
			<div  id= "rc_mp_album" class="containerControlData"></div>
		</div>
		<div id="containerControlLine5" class="containerControlLine">
			<div  id= "rc_mp_descr" class="containerControlDescr">Titel</div>
			<div  id= "rc_mp_titel" class="containerControlData"></div>
		</div>
		<div id="containerControlLine6" class="containerControlLine">
			<div  id= "rc_mp_descr" class="containerControlDescr">Titellänge</div>
			<div  id= "rc_mp_length" class="containerControlData"></div>
		</div>
		<div id="containerControlLine7" class="containerControlLine">
			<div  id= "rc_mp_descr" class="containerControlDescr">Aktuell</div>
			<div  id= "rc_mp_current" class="containerControlData"></div>
		</div>
		<div id="containerControlLine8" class="containerControlLineBottom">
			<div  id= "rc_mp_select" class="containerControlButton" style="width:176px;">Musik Auswahl</div>
			<div  id= "rc_mp_radio" class="containerControlButton" style="width:176px;">Internet Radio</div>
		</div>
	</div>
	
  </body>
</html>
