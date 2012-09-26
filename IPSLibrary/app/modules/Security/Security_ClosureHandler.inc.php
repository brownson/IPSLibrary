<?
    IPSUtils_Include ('IPSLogger.inc.php',              'IPSLibrary::app::core::IPSLogger');
    //IPSUtils_Include ("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
	IPSUtils_Include ("Security_Functions.inc.php", "IPSLibrary::app::modules::Security");
    
	Security_ClosureHandlerEvent($IPS_VARIABLE, $IPS_VALUE);
	
	function Security_ClosureHandlerEvent($sourceId, $value) {
		$deviceConfig = Security_getClosureConfigById($sourceId);
		IPSLogger_Trc(__file__, "Security_ClosureHandlerEvent Source: ".$deviceConfig[c_Name]."@".$deviceConfig[c_Location]."(".$sourceId.")");
		
		$event = array(
			"type"			=> cat_CLOSURE,
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