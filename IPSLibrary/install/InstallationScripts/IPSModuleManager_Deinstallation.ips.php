<?
	 /**@addtogroup ipsmodulemanager
	 * @{
	 *
	 * Deinstallations Script für IPSModuleManager
	 *
	 * @file          IPSModuleManager_Deinstallation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 28.02.2012<br/>
	 *
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSModuleManager');
	}


	/** @}*/
?>