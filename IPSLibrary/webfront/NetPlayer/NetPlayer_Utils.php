<?php 
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_Utils.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Diverse Funktionen um die Daten von MP3 Player und Radio zu aktualisieren
	 *
	 */
	IPSUtils_Include ("NetPlayer.inc.php", "IPSLibrary::app::modules::NetPlayer");
?>
	<script type="text/javascript">
		var l_sec_total = 0;
		var l_sec_current = 0;
		var l_lasttracktitel;
		var l_status = -1;

		function lpad(number, length) {
			var str = '' + number;
			while (str.length < length) {
				str = '0' + str;
			}
			return str;
		}

		function sectotime(sec) {
		 var time = "" + Math.floor(sec / 60) + ":" + lpad(sec % 60, 2);
		return time;
		}

		function timetosec(time) {
			var sec = 0;
			if (time != "") {
				if (time.indexOf(":") >= 0) {
					var l_sec_min = parseInt(time.substr(0,time.indexOf(":")));
					var l_sec_sec = parseInt(time.substr(time.indexOf(":")+1,2));
					sec = l_sec_min*60+l_sec_sec;
				} else {
					sec = parseInt(time);
				}
			}
			return sec;
		}
	  
		function refresh_tracklist() {
			$(".containerControlTrack").each( function() 
			{
				$(this).removeClass("containerControlTrackActive");
				var l_titel = $("#rc_mp_titel").html();
				var l_tracklistitem = $(this).html();
				if (l_titel != "" && l_titel != " " && l_tracklistitem != undefined && l_tracklistitem.indexOf(l_titel) >= 0 ) {
					$(this).addClass("containerControlTrackActive");
				}
			})
		}  

		function refresh_tracktime() {
			if (l_status != 0) {
				l_sec_current = 0;
			} else {
				l_sec_current = l_sec_current + 1;
				$("#rc_mp_current").html(sectotime(l_sec_current));
				if (l_sec_current >= l_sec_total) {
					refresh();
				}
			}
		}

		function refresh_playerstatus(data) {
			l_status = data;
			$(".containerControlButton").each( function() {
				$(this).removeClass("containerControlButtonActive");
			})
			if (l_status == 0) {
				$("#rc_mp_play").addClass("containerControlButtonActive");
			} else if (l_status == 1) {
				$("#rc_mp_pause").addClass("containerControlButtonActive");
			} else if (l_status == 2){
				$("#rc_mp_stop").addClass("containerControlButtonActive");
			}
		}

		function refresh_totaltime(data) {
			l_sec_total = timetosec(data);
			$("#rc_mp_length").html(data);
		}

		function refresh_currenttime(data) {
			l_sec_current = timetosec(data);
			$("#rc_mp_current").html(data);
		}

		 function refresh_trackdata() {
			var serverAddr = "<?echo $_SERVER["HTTP_HOST"];?>";
			$.get("http://"+serverAddr+"/user/NetPlayer/NetPlayer_Receiver.php?rc_action=rc_other&id=rc_mp_length", refresh_totaltime);
			$.get("http://"+serverAddr+"/user/NetPlayer/NetPlayer_Receiver.php?rc_action=rc_other&id=rc_mp_current", refresh_currenttime);
			$("#rc_mp_interpret").load("http://"+serverAddr+"/user/NetPlayer/NetPlayer_Receiver.php",{id: "rc_mp_interpret", rc_action: "rc_other"});
			$("#rc_mp_album").load("http://"+serverAddr+"/user/NetPlayer/NetPlayer_Receiver.php",{id: "rc_mp_album", rc_action: "rc_other"});
			$("#rc_mp_titel").load("http://"+serverAddr+"/user/NetPlayer/NetPlayer_Receiver.php",{id: "rc_mp_titel", rc_action: "rc_other"});
			$.get("http://"+serverAddr+"/user/NetPlayer/NetPlayer_Receiver.php?rc_action=rc_other&id=rc_mp_status", refresh_playerstatus);
		}

		function refresh_player() {
			setTimeout(function(){
				refresh_trackdata();
				l_sec_total = timetosec($("#rc_mp_length").html());
				l_sec_current = timetosec($("#rc_mp_current").html());
				setTimeout(function(){
					refresh_tracklist();
					//$("#rc_mp_interpret").html("Total="+l_sec_total+", Current="+l_sec_current);
				}, 500);
			}, 500);
		}

		function refresh_radiostatus(data) {
			l_status = data;
			$(".containerControlButton").each( function() {
				$(this).removeClass("containerControlButtonActive");
			})
			if (l_status == 0) {
				$("#rc_mp_play").addClass("containerControlButtonActive");
			} else if (l_status == 1) {
				$("#rc_mp_pause").addClass("containerControlButtonActive");
			} else if (l_status == 2){
				$("#rc_mp_stop").addClass("containerControlButtonActive");
			}
		}
 
		function refresh_radiodata() {
			var serverAddr = "<?echo $_SERVER["HTTP_HOST"];?>";
			$.get("http://"+serverAddr+"/user/NetPlayer/NetPlayer_Receiver.php?rc_action=other&id=rc_mp_status", refresh_radiostatus);
		}

		function refresh_radio() {
			setTimeout(function(){
				refresh_radiodata();
			}, 1000);
			setTimeout(function(){
				refresh_radiodata();
			}, 1000);
		}

	</script>
<?php 
   /** @}*/
?>
