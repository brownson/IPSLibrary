<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="Content-Style-Type" content="text/css">
		<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">

		<link rel="stylesheet" type="text/css" href="Remote.css" />
		<?include "Remote_Sender.php"?>
	</head>

  <body>
    <div id="containerLeft">
	<table  width=100%>
		  <tr>
			<th id="rc_cmd_menu" rc_name="TopfieldSat" rc_button="Menu" class="rc_button33">Menu</th>
			<th id="rc_cmd_up" rc_name="TopfieldSat" rc_button="ProgramNext"  class="rc_button33_blue" >Programm +</th>
			<th id="rc_cmd_guide" rc_name="TopfieldSat" rc_button="Guide"  class="rc_button33" >Guide</th>
		  </tr>
		  <tr>
			<th id="rc_cmd_navleft" rc_name="TopfieldSat" rc_button="VolumeMinus"  class="rc_button33_blue"><<</th>
			<th id="rc_cmd_ok" rc_name="TopfieldSat" rc_button="OK"  class="rc_button33_blue">OK</th>
			<th id="rc_cmd_navright" rc_name="TopfieldSat" rc_button="VolumePlus"  class="rc_button33_blue">>></th>
		  </tr>
		  <tr>
			<th id="rc_cmd_exit" rc_name="TopfieldSat" rc_button="Exit"  class="rc_button33" >Exit</th>
			<th id="rc_cmd_ProgrammPlus" rc_name="TopfieldSat" rc_button="ProgramLast"  class="rc_button33_blue">Programm -</th>
			<th id="rc_cmd_fav" rc_name="TopfieldSat" rc_button="Fav"  class="rc_button33" >FAV</th>
		  </tr>
		  <tr>
			<th id="rc_cmd_Simple" rc_name="topfieldsattype" rc_button="0" class="rc_button33" >Simple</th>
		  </tr>
		  <tr>
			<th id="rc_cmd_teletext" rc_name="TopfieldSat" rc_button="Teletext"  class="rc_button33">Teletext</th>
			<th id="rc_cmd_audio" rc_name="TopfieldSat" rc_button="Audio"  class="rc_button33">Audio</th>
			<th id="rc_cmd_format" rc_name="TopfieldSat" rc_button="Format"  class="rc_button33">Format</th>
		  </tr>
		</table>
	 </div> 
	<div id="containerRight">
		<?
			IPSUtils_Include ("Entertainment.inc.php", "IPSLibrary::app::modules::Entertainment");
			$Program = get_DeviceControlValue(c_Device_TopfieldSat, c_Control_Program);
			$Names   = get_DeviceControlConfigValue(c_Device_TopfieldSat, c_Control_Program, c_Property_Names);
			$Codes   = get_DeviceControlConfigValue(c_Device_TopfieldSat, c_Control_Program, c_Property_Codes);
			$Device  = c_Device_TopfieldSat;
		?>
		<table width=100%>
		  <tr>
			<th id="rc_cmd_1" rc_program="0" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==0) {echo "rc_button_active";}?>"><?php echo $Names[0];?></th>
			<th id="rc_cmd_2" rc_program="1" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==1) {echo "rc_button_active";}?>"><?php echo $Names[1];?></th>
			<th id="rc_cmd_3" rc_program="2" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==2) {echo "rc_button_active";}?>"><?php echo $Names[2];?></th>
		  </tr>
		  <tr>
			<th id="rc_cmd_4" rc_program="3" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==3) {echo "rc_button_active";}?>"><?php echo $Names[3];?></th>
			<th id="rc_cmd_5" rc_program="4" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==4) {echo "rc_button_active";}?>"><?php echo $Names[4];?></th>
			<th id="rc_cmd_6" rc_program="5" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==5) {echo "rc_button_active";}?>"><?php echo $Names[5];?></th>
		  </tr>
		  <tr>
			<th id="rc_cmd_7" rc_program="6" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==6) {echo "rc_button_active";}?>"><?php echo $Names[6];?></th>
			<th id="rc_cmd_8" rc_program="7" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==7) {echo "rc_button_active";}?>"><?php echo $Names[7];?></th>
			<th id="rc_cmd_9" rc_program="8" rc_devicename="<?php echo $Device;?>"  class="rc_button33 <?php if ($Program==8) {echo "rc_button_active";}?>"><?php echo $Names[8];?></th>
		  </tr>
		  <tr>
			<th id="rc_cmd_10" rc_program="9"  rc_devicename="<?php echo $Device;?>" class="rc_button33 <?php if ($Program==9) {echo "rc_button_active";}?>"><?php echo $Names[9];?></th>
			<th id="rc_cmd_11" rc_program="10" rc_devicename="<?php echo $Device;?>" class="rc_button33 <?php if ($Program==10) {echo "rc_button_active";}?>"><?php echo $Names[10];?></th>
			<th id="rc_cmd_12" rc_program="11" rc_devicename="<?php echo $Device;?>" class="rc_button33 <?php if ($Program==11) {echo "rc_button_active";}?>"><?php echo $Names[11];?></th>
		  </tr>
		</table>
	</div> 
  </body>
</html>
