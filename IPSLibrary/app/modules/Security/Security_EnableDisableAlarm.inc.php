<?
	
	IPSUtils_Include ('IPSLogger.inc.php',              'IPSLibrary::app::core::IPSLogger');
	IPSUtils_Include ("Security_Functions.inc.php", "IPSLibrary::app::modules::Security");

	Security_enabledDisableAlarm($IPS_VALUE);
?>