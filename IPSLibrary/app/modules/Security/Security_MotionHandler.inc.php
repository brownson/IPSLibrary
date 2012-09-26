<?
    IPSUtils_Include ('IPSLogger.inc.php',              'IPSLibrary::app::core::IPSLogger');
    //IPSUtils_Include ("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
	IPSUtils_Include ("Security_Functions.inc.php", "IPSLibrary::app::modules::Security");
    
	Security_MotionHandlerEvent($IPS_VARIABLE, $IPS_VALUE);
	
	function Security_MotionHandlerEvent($sourceId, $value) {
		$deviceConfig = Security_getMotionConfigById($sourceId);
		IPSLogger_Trc(__file__, "MotionEvent Source: ".$deviceConfig[c_Name]."@".$deviceConfig[c_Location]."(".$sourceId.")");
		
		$motionEvent = array(
			"type"			=> cat_MOTION,
			"timestamp" 	=> time(),
			"deviceId"		=> $sourceId,
			"device"		=> $deviceConfig,
			"enabled"		=> $value
		);
		
		Security_logEvent($motionEvent);
		
		if($value !== True) {
			return;
		}
		
		if(Security_isAlarmEnabled()) {
			Security_raiseAlarm($motionEvent);
		}
	}
?>