<?
	/**@addtogroup ipscam
	 * @{
	 *
	 * @file          IPSCam_Receiver.php
	 * @author        Andreas Brauneis
	 * @version
	 *    Version 2.50.1, 23.09.2012<br/>
	 *
	 * Empfangs Script um Requests (JQuery) der HTML Seiten zu bearbeiten.
	 *
	 */

	IPSUtils_Include ("IPSModuleManagerGUI.inc.php", "IPSLibrary::app::modules::IPSModuleManagerGUI");
	IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");

	//IPSLogger_Err(__file__, 'action='.$action);
	 
	$id       = $_GET['id'];
	$action   = $_GET['action'];
	$module   = $_GET['module'];
	$info     = $_GET['info'];

	$moduleManager = new IPSModuleManager('', '', sys_get_temp_dir(), true);
	$repository = '';
	if ($module<>'') {
		$moduleInfos = $moduleManager->GetModuleInfos();
		$repository  = $moduleInfos['Repository'];
	}
	
	switch ($action) {
		case 'Refresh':
			break;
		case 'SearchUpdates':
			if (IPSModuleManagerGUI_GetLock($action, true)) {
				$moduleManager = new IPSModuleManager('', '', sys_get_temp_dir(), true);
				$moduleManager->VersionHandler()->BuildKnownModules();
			}
			break;
		case 'UpdateAll':
			if (IPSModuleManagerGUI_GetLock($action, true)) {
				IPSLogger_Inf(__file__, 'IPSModuleManagerGUI - Update of all Modules');
				$moduleManager = new IPSModuleManager();
				$moduleManager->UpdateAllModules();
			}
			break;
		case 'Update':
			if (IPSModuleManagerGUI_GetLock($action, true)) {
				IPSLogger_Inf(__file__, 'IPSModuleManagerGUI - Update of Module '.$module.' from Repository "'.$repository.'"');
				$moduleManager = new IPSModuleManager($module, $repository);
				$moduleManager->UpdateModule();
			}
			break;
		case 'Install':
			if (IPSModuleManagerGUI_GetLock($action, true)) {
				IPSLogger_Inf(__file__, 'IPSModuleManagerGUI - Installation of Module '.$module);
				$moduleManager = new IPSModuleManager($module);
				$moduleManager->InstallModule();
			}
			break;
		case 'Load':
			if (IPSModuleManagerGUI_GetLock($action, true)) {
				IPSLogger_Inf(__file__, 'IPSModuleManagerGUI - Load Files of Module '.$module.' from Repository "'.$repository.'"');
				$moduleManager = new IPSModuleManager($module, $repository);
				$moduleManager->LoadModule();
			}
			break;
		case 'Delete':
			if (IPSModuleManagerGUI_GetLock($action, true)) {
				IPSLogger_Inf(__file__, 'IPSModuleManagerGUI - Delete of Module '.$module);
				$moduleManager = new IPSModuleManager($module);
				$moduleManager->DeleteModule();
			}
			break;
		default:
			IPSModuleManagerGUI_SetPage($action, $module, $info);
	}
	IPSModuleManagerGUI_Refresh();


	/** @}*/
?>