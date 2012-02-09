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
			<table width=100%>
				<tr>
					<th id="rc_cmd_vol_minus" rc_name="YamahaReceiver" rc_button="VolumeMinus" rc_button2="VolumeMinus" rc_button3="VolumeMinus" class="rc_button">-</th>
					<th id="rc_cmd_vol_plus"  rc_name="YamahaReceiver" rc_button="VolumePlus"  rc_button2="VolumePlus"  rc_button3="VolumePlus"  class="rc_button">+</th>
					<?
						if ($iPhone) {
							echo '</tr></table><hr noshade width="90%" size="3" align="center"><table width=100%><tr>';
						} else {
							echo '<th></th>';
						}
					?>
					<th id="rc_cmd_effect"    rc_name="YamahaReceiver" rc_button="Effect"      class="rc_button">Effect On/Off</th>
					<th id="rc_cmd_prologic"  rc_name="YamahaReceiver" rc_button="Prologic"    class="rc_button">Prologic</th>
					<th id="rc_cmd_enhanced"  rc_name="YamahaReceiver" rc_button="Enhanced"    class="rc_button">Enhanced</th>
				</tr>
			</table>
		</div> 
	</body>
</html>
