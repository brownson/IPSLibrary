<?
    IPSUtils_Include ('IPSLogger.inc.php',              'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
	IPSUtils_Include ("Security_Functions.inc.php", "IPSLibrary::app::modules::Security");
    
	Security_ClosureHandlerEvent($IPS_VARIABLE, $IPS_VALUE);
	
	function Security_ClosureHandlerEvent($sourceId, $value) {
		$event = array(
			"type"			=> cat_CLOSURE,
			"timestamp" 	=> time(),
			"deviceId"		=> $sourceId,
			"value"			=> $value
		);
		
		Security_handleEvent($event);
	}
?>