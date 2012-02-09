<?php 
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_MobilePlayer.php
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

	<link rel="stylesheet" type="text/css" href="mNetPlayer.css" />
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
	<div id="containerControl" class="containerControl">
		<div id="containerControlLine0" class="containerControlLine100">
			<div  id= "rc_mp_play" class="containerControlButton">Play</div>
		</div>
		<div id="containerControlLine11" class="containerControlLine50">
			<div  id= "rc_mp_last" class="containerControlButton" style="margin-left:-10px;">Last</div>
		</div>
		<div id="containerControlLine12" class="containerControlLine50">
			<div  id= "rc_mp_next" class="containerControlButton" style="margin-right:-10px;">Next</div>
		</div>
		<div id="containerControlLine2" class="containerControlLine50">
			<div  id= "rc_mp_pause" class="containerControlButton" style="margin-left:-10px;">Pause</div>
		</div>
		<div id="containerControlLine2" class="containerControlLine50">
			<div  id= "rc_mp_stop" class="containerControlButton" style="margin-right:-10px;">Stop</div>
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
		
		<div id="containerControlLine2" class="containerControlLineSeparator"></div>
		
		<div id="containerControlLine2" class="containerControlLine50">
			<table width=100% style=" font-size:32px; overflow:hidden;"><?php echo GetValue(NP_ID_CDTRACKLISTHTML);?></table>
		</div>
		<div id="containerControlLine2" class="containerControlLine50">
			<table width=100%><tr><th id="mp_cover"><img style="width:400px; height:400px; margin-bottom:20px; vertical-align:middle" src="NetPlayer_Cover.jpg" alt=""></th></tr></table>
		</div>
		<div id="containerControlLine2" class="containerControlLine50" style="left:0px;">
			<div  id= "rc_mp_select" class="containerControlButton">Musik Auswahl</div>
		</div>
		<div id="containerControlLine2" class="containerControlLine50" style="left:0px;">
			<div  id= "rc_mp_radio" class="containerControlButton">Internet Radio</div>
		</div>
	</div>
	<div id="containerCover"  class="containerCover">
	</div>
	<div id="containerPlayList" class="containerPlayList">
	</div>
	
  </body>
</html>
