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
		<?
			IPSUtils_Include ("Entertainment.inc.php", "IPSLibrary::app::modules::Entertainment");
			$Program = get_DeviceControlValue(c_Device_YamahaTuner, c_Control_Program);
			$Names   = get_DeviceControlConfigValue(c_Device_YamahaTuner, c_Control_Program, c_Property_Names);
			$Codes   = get_DeviceControlConfigValue(c_Device_YamahaTuner, c_Control_Program, c_Property_Codes);
			$Device  = c_Device_YamahaTuner;
		?>
		<table  width=100%>
		  <tr>
			<th id="rc_cmd_next" rc_program="next" rc_devicename="<?php echo $Device;?>"  class="rc_button33" colspan="2" rowspan="1">Program +</th>
			<th class="rc_button_template" ></th>
		  </tr>
		  <tr>
			<th id="rc_cmd_prev" rc_program="prev" rc_devicename="<?php echo $Device;?>"  class="rc_button33" colspan="2" rowspan="1">Program -</th>
			<th class="rc_button_template" ></th>
		  </tr>
		  <tr>
			<th class="rc_button_template" ></th>
			<th class="rc_button_template" ></th>
			<th class="rc_button_template" ></th>
		  </tr>
		</table>
	 </div> 
	<div id="containerRight">
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
		  </tr>
		</table>
	</div> 
  </body>
</html>
