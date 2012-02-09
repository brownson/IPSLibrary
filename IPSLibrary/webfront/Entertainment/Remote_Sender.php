	<script type="text/javascript" src="jquery.min.js"></script>
	<script type="text/javascript">

		function refresh_program(data, obj_id, program_selected) {
			$(".rc_button33").each( 
				function() 
				{
					if ($(this).attr('rc_program') !== undefined) {
						$(this).removeClass("rc_button_active");
						if ($(this).attr('rc_program') == data) {
							$(this).addClass("rc_button_active");
						}
					}
				}
			)
		}

		function trigger_button() {
			var serverAddr = "<?echo $_SERVER["HTTP_HOST"];?>";
			var obj_id = $(this).attr("id");
			$('#'+obj_id).addClass("rc_button_pressed");

			// -----------------------------------------------------------------------------------------------------
			// RemoteControl Button Command
			// -----------------------------------------------------------------------------------------------------
			if ($('#'+obj_id).attr('rc_name') !== undefined) {
				var rc_name     = "";
				var rc_button   = "";
				var rc_name2    = "";
				var rc_button2  = "";
				var rc_name3    = "";
				var rc_button3  = "";

				rc_name = $('#'+obj_id).attr('rc_name');
				rc_button = $('#'+obj_id).attr('rc_button');

				if ($('#'+obj_id).attr('rc_button2') !== undefined) {
					rc_button2 = $('#'+obj_id).attr('rc_button2');
					rc_name2   = rc_name;
					if ($('#'+obj_id).attr('rc_name2') !== undefined) {
						rc_name2 = $('#'+obj_id).attr('rc_name2');
					}
				}
				if ($('#'+obj_id).attr('rc_button3') !== undefined) {
					rc_button3 = $('#'+obj_id).attr('rc_button3');
					rc_name3   = rc_name;
					if ($('#'+obj_id).attr('rc_name3') !== undefined) {
						rc_name3 = $('#'+obj_id).attr('rc_name3');
					}
				}

				$.ajax({ type: "POST",
						url: "http://"+serverAddr+"/user/Entertainment/Remote_Receiver.php",
						data: "rc_action=rc_cmd&id="+obj_id+"&rc_name="+rc_name+"&rc_button="+rc_button
														   +"&rc_name2="+rc_name2+"&rc_button2="+rc_button2
														   +"&rc_name3="+rc_name3+"&rc_button3="+rc_button3
						});

			// -----------------------------------------------------------------------------------------------------
			// Program Button Command
			// -----------------------------------------------------------------------------------------------------
			} else if ($('#'+obj_id).attr('rc_program') !== undefined) {
				rc_program    = $('#'+obj_id).attr('rc_program');
				rc_devicename = $('#'+obj_id).attr('rc_devicename');
				$.ajax({ type: "POST",
						url: "http://"+serverAddr+"/user/Entertainment/Remote_Receiver.php",
						data: "rc_action=rc_program&id="+obj_id
										+"&rc_program="+rc_program+"&rc_devicename="+rc_devicename
										+"&rc_name="+rc_name+"&rc_button="+rc_button
										+"&rc_name2="+rc_name2+"&rc_button2="+rc_button2
										+"&rc_name3="+rc_name3+"&rc_button3="+rc_button3,
						success: function(data) {refresh_program(data, obj_id, rc_program);}
						});

			// -----------------------------------------------------------------------------------------------------
			// Other Buttons
			// -----------------------------------------------------------------------------------------------------
			} else {
			  $.ajax({ type: "POST",
					   url: "http://"+serverAddr+"/user/Entertainment/Remote_Receiver.php",
					  data: "rc_action=rc_other&id="+obj_id });
		   }

		   //Reset Button
		   setTimeout(function(){
			  $('#'+obj_id).removeClass("rc_button_pressed");}, 200);
		} 

		$(function(){$(".rc_button").click(trigger_button);});
		$(function(){$(".rc_button14").click(trigger_button);});
		$(function(){$(".rc_button33").click(trigger_button);});
		$(function(){$(".rc_button33_blue").click(trigger_button);});
	</script>
