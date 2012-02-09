<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

	<?
		$agent  = $_SERVER['HTTP_USER_AGENT'];
		$iPhone = preg_match("@ipod@i", $agent) || preg_match("@iphone@i",$agent);
		if ($iPhone) {
			echo '<link rel="stylesheet" type="text/css" href="iRemote.css" />';
		} else {
			echo '<link rel="stylesheet" type="text/css" href="Remote.css" />';
		}
	?>
	<?include "Remote_Sender.php"?>
  </head>

  <body>
	<div id="containerVolume">
 		<?
			if ($iPhone) {
				echo '<table width=100%><tr>';
				echo    '<th id="rc_cmd_VolumeMinus" rc_name="PhilipsTV" rc_button="VolumeMinus" class="rc_button33">-</th>';
				echo    '<th id="rc_cmd_VolumePlus" rc_name="PhilipsTV" rc_button="VolumePlus" class="rc_button33">+</th>';
				echo    '<th id="rc_cmd_Display" rc_name="PhilipsTV" rc_button="Display" class="rc_button33" >Display</th>';
				echo '</tr></table>';
				echo '<hr noshade width="90%" size="3" align="center">';
				echo '<table width=100%><tr>';
				echo    '<th id="rc_cmd_stripeon"  rc_name="ledstripe" rc_button="poweron"  class="rc_button">LED On</th>';
				echo    '<th id="rc_cmd_stripeoff" rc_name="ledstripe" rc_button="poweroff" class="rc_button">LED Off</th>';
				echo '</tr></table>';
				echo '<hr noshade width="90%" size="3" align="center">';
				echo '<table width=100%><tr>';
				echo    '<th id="rc_cmd_dimmdown" rc_name="ledstripe" rc_button="dimmdown" class="rc_button">Dimm Down</th>';
				echo    '<th id="rc_cmd_dimmup"   rc_name="ledstripe" rc_button="dimmup"   class="rc_button">Dimm Up</th>';
				echo '</tr></table>';
				echo '<hr noshade width="90%" size="3" align="center">';
				echo '<table width=100%>';
				echo    '<tr><th id="rc_cmd_white"  rc_name="ledstripe" rc_button="white"  class="rc_button">White</th>';
				echo    '    <th id="rc_cmd_red"    rc_name="ledstripe" rc_button="red"    class="rc_button">Red</th></tr>';
				echo    '<tr><th id="rc_cmd_green"  rc_name="ledstripe" rc_button="green"  class="rc_button">Green</th>';
				echo    '    <th id="rc_cmd_blue"   rc_name="ledstripe" rc_button="blue"   class="rc_button">Blue</th></tr>';
				echo    '<tr><th id="rc_cmd_yellow" rc_name="ledstripe" rc_button="yellow" class="rc_button">Yellow</th>';
				echo    '    <th id="rc_cmd_orange" rc_name="ledstripe" rc_button="orange" class="rc_button">Orange</th></tr>';
				echo    '<tr><th id="rc_cmd_lgreen" rc_name="ledstripe" rc_button="lightgreen"  class="rc_button">LightGreen</th>';
				echo    '    <th id="rc_cmd_lblue"  rc_name="ledstripe" rc_button="lightblue"   class="rc_button">LightBlue</th></tr>';
				echo '</table>';
				echo '<hr noshade width="90%" size="3" align="center">';
			} else {
				echo '<table width=100%><tr>';
				echo    '<th id="rc_cmd_VolumeMinus" rc_name="PhilipsTV" rc_button="VolumeMinus" class="rc_button">-</th>';
				echo    '<th id="rc_cmd_VolumePlus" rc_name="PhilipsTV" rc_button="VolumePlus" class="rc_button">+</th>';
				echo    '<th class="rc_button_template"></th>';
				echo    '<th class="rc_button_template"></th>';
				echo    '<th class="rc_button_template"></th>';
				echo    '<th class="rc_button_template"></th>';
				echo    '<th id="rc_cmd_Display" rc_name="PhilipsTV" rc_button="Display" class="rc_button" >Display</th>';
				echo '</tr></table>';
			}
		?>
	</div>  
  </body>
</html>
