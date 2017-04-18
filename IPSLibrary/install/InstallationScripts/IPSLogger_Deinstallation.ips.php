<?
	/**@addtogroup ipslogger
	 * @{
	 *
	 * Deinstallations File für den IPSLogger
	 *
	 * @file          IPSLogger_Deinstallation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 25.02.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('IPSLogger');
	}

	IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");

	$WFC10_ConfigId = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
	DeleteWFCItems ($WFC10_ConfigId, 'IPSLogger_Widget');

	Unregister_PhpErrorHandler($moduleManager);

	// ------------------------------------------------------------------------------------------------
	function Unregister_PhpErrorHandler($moduleManager) {
		$file = IPS_GetKernelDir().'scripts/__autoload.php';

		if (!file_exists($file)) {
			return;
		}
		$FileContent = file_get_contents($file);
		$includeCommand = 'IPSUtils_Include("IPSLogger_PhpErrorHandler.inc.php", "IPSLibrary::app::core::IPSLogger");';
		$FileContent = str_replace($includeCommand, '', $FileContent);
		$moduleManager->LogHandler()->Log('Unregister Php ErrorHandler of IPSLogger in File __autoload.php');
		file_put_contents($file, $FileContent);
	}
	/** @}*/
?>