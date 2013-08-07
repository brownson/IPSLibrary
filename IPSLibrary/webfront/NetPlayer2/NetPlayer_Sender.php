<?php 
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_Sender.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Java Script Funktionen (JQuery) zum Senden und Empfangen. 
	 *
	 * Dieses Script File wird von den diversen HTML Seiten per INCLUDE eingebunden und ermöglicht 
	 * das Senden und Empfangen von Daten ohne die Seite neu Laden zu müssen.
	 * 
	 * Auf der IPS Seite werden die Requests vom File NetPlayer_Receiver.php verarbeitet.
	 *
	 */
	IPSUtils_Include ("NetPlayer.inc.php", "IPSLibrary::app::modules::NetPlayer2");

	$agent  = $_SERVER['HTTP_USER_AGENT'];
	$mobile = (preg_match("@ipod@i", $agent) || preg_match("@iphone@i",$agent));

	?>

	<style type="text/css">
		<?
			$agent = $_SERVER['HTTP_USER_AGENT'];
			if (strpos($agent, 'Macintosh') > 0) {
				$background = 'background:#e4e4e4;';
				$foreground = 'color:#000000;';
			} else if (strpos($agent, 'iPad') > 0 or strpos($agent, 'iPhone') > 0) {
				$background = 'background:#252525;';
				$foreground = 'color:#ffffff;';
			} else {
				$background = 'background:#252525;';
				$foreground = 'color:#ffffff;';
			}
			echo 'html, body { '.$background.$foreground.' }';
		?>
		
	</style>

	<script type="text/javascript" src="jquery.min.js"></script>

	<script type="text/javascript">
		function trigger_button() {
			var serverAddr;
			if (window.location.protocol == "https:") { serverAddr = "https://<?echo $_SERVER["HTTP_HOST"];?>"; }
			else{ serverAddr = "http://<?echo $_SERVER["HTTP_HOST"];?>"; }
			var obj_id = $(this).attr("id");
			$('#'+obj_id).addClass("containerControlButtonSelected");

		   // -----------------------------------------------------------------------------------------------------
		   // MediaPlayer
		   // -----------------------------------------------------------------------------------------------------
		   // Select Mediaplayer CD
		   if ($('#'+obj_id).attr('cd_name') !== undefined) {
			  var cd_name = $('#'+obj_id).attr('cd_name');
			  $.get(serverAddr+'/user/NetPlayer2/NetPlayer_Receiver.php?rc_action=rc_netcd&cd_path='+encodeURIComponent(cd_name)+"&id="+obj_id, function(data) { <?if ($mobile) echo 'location.reload();'; ?>});
					 
		   // Select Category
		   } else if ($('#'+obj_id).attr('cd_cat') !== undefined) {
			  var cd_cat = $('#'+obj_id).attr('cd_cat');
			  $.get(serverAddr+'/user/NetPlayer2/NetPlayer_Receiver.php?rc_action=rc_netcat&cd_cat='+encodeURIComponent(cd_cat)+"&id="+obj_id, function(data) { <?if ($mobile) echo 'location.reload();'; ?>});

		   // Select Track
		   } else if ($('#'+obj_id).attr('track') !== undefined) {
			  var track = $('#'+obj_id).attr('track');
			  $.get(serverAddr+'/user/NetPlayer2/NetPlayer_Receiver.php?rc_action=rc_netcat&track='+encodeURIComponent(track)+"&id="+obj_id, function(data) { <?if ($mobile) echo 'location.reload();'; ?>});

		   // MediaPlayer WebRadio
		   } else if ($('#'+obj_id).attr('radiourl') !== undefined) {
			  var radiotitel = $('#'+obj_id).html();
			  var radiourl = $('#'+obj_id).attr('radiourl');
			  $.get(serverAddr+'/user/NetPlayer2/NetPlayer_Receiver.php?rc_action=rc_netradio&id='+obj_id+'&radiotitel='+radiotitel+'&radiourl='+radiourl, function(data) { <?if ($mobile) echo 'location.reload();'; ?>});
			  $('#rc_mp_radiotitel').html(radiotitel);

			} else if (obj_id=='rc_mp_radio' || obj_id=='rc_mp_select' || obj_id=='rc_mp_player') {
				$.get(serverAddr+'/user/NetPlayer2/NetPlayer_Receiver.php?rc_action=rc_other&id='+obj_id, function(data) { <?if ($mobile) echo 'location.reload();'; ?>});
		   				
			} else if (obj_id=='rc_mp_cdselectprev' || obj_id=='rc_mp_cdselectnext' || obj_id=='rc_mp_cdselectback'  || obj_id=='rc_mp_cdselectroot' ) {
				$.get(serverAddr+'/user/NetPlayer2/NetPlayer_Receiver.php?rc_action=rc_other&id='+obj_id, function(data) { <?if ($mobile) echo 'location.reload();'; ?>});
		   				
		   // -----------------------------------------------------------------------------------------------------
		   // Other Buttons
		   // -----------------------------------------------------------------------------------------------------
		   } else {
			  $.ajax({ type: "POST",
					   url: serverAddr+"/user/NetPlayer2/NetPlayer_Receiver.php",
					   data: "rc_action=rc_other&id="+obj_id });
		   }

		   setTimeout(function(){
			  $('#'+obj_id).removeClass("containerControlButtonSelected");}, 200);
		} 

		$(function(){$(".containerControlButton").click(trigger_button);});
	</script>
<?php 
   /** @}*/
?>
