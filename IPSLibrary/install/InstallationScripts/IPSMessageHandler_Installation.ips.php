<?
	/**@defgroup ipsmessagehandler_installation IPSMessageHandler Installation
	 * @ingroup ipsmessagehandler
	 * @{
	 *
	 * Installations Script für IPSMessageHandler
	 *
	 * @file          IPSMessageHandler_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 31.01.2012<br/>
	 *
	 * @section requirements_messagehandler Installations Voraussetzungen IPSMessageHandler
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 *
	 * @page install_messagehandler Installations Schritte
	 * Folgende Schritte sind zur Installation von IPSMessageHandler nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Installation (siehe IPSModuleManager)
	 */

	return; 

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSMessageHandler');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.1');


	/** @}*/
?>