<?
    IPSUtils_Include ('IPSLogger.inc.php',              'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
	IPSUtils_Include ("Security_Functions.inc.php", "IPSLibrary::app::modules::Security");
    
	Security_SmokeHandlerEvent($IPS_VARIABLE, $IPS_VALUE);
	
	function Security_SmokeHandlerEvent($sourceId, $value) {
		$event = array(
			"type"			=> cat_SMOKE,
			"timestamp" 	=> time(),
			"deviceId"		=> $sourceId,
			"value"			=> $value
		);
		
		Security_handleEvent($event);
	}
?>