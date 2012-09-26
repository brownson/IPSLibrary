<?
    IPSUtils_Include ('IPSLogger.inc.php',              'IPSLibrary::app::core::IPSLogger');
    //IPSUtils_Include ("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
	IPSUtils_Include ("Security_Functions.inc.php", "IPSLibrary::app::modules::Security");
    
	Security_SmokeHandlerEvent($IPS_VARIABLE, $IPS_VALUE);
	
	function Security_SmokeHandlerEvent($sourceId, $value) {
		$deviceConfig = Security_getSmokeConfigById($sourceId);
		IPSLogger_Trc(__file__, "SmokeEvent Source: ".$deviceConfig[c_Name]."@".$deviceConfig[c_Location]."(".$sourceId.")");
		
		$event = array(
			"type"			=> cat_SMOKE,
			"timestamp" 	=> time(),
			"deviceId"		=> $sourceId,
			"device"		=> $deviceConfig,
			"enabled"		=> $value
		);
		
		Security_logEvent($event);
		
		if($value !== True) {
			return;
		}
		
		Security_raiseAlarm($event);
	}
?>