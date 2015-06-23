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

	//IPSLogger_Inf(__file__, "Post Parameters: ");
	//foreach ($_POST as $key=>$value) {
	//	IPSLogger_Inf(__file__, "Post $key = $value");
	//}
	
	$id       = $_POST['id'];
	$action   = $_POST['action'];
	$module   = $_POST['module'];
	$info     = $_POST['info'];

	$moduleManager = new IPSModuleManager('', '', sys_get_temp_dir(), true);
	$repository = '';
	if ($module<>'') {
		$moduleInfos = $moduleManager->GetModuleInfos($module);
		$repository  = $moduleInfos['Repository'];
	}
	
	switch ($action) {
		case IPSMMG_ACTION_STOREANDINSTALL:
			IPSModuleManagerGUI_StoreParameters($module, $_POST);
			if (IPSModuleManagerGUI_GetLock($action, true)) {
				IPSLogger_Inf(__file__, 'IPSModuleManagerGUI - Installation of Module '.$module);
				$moduleManager = new IPSModuleManager($module);
				$moduleManager->InstallModule();
			}
			IPSModuleManagerGUI_SetPage(IPSMMG_ACTION_MODULE, $module);
			break;
		case IPSMMG_ACTION_STORE:
			IPSModuleManagerGUI_StoreParameters($module, $_POST);
			IPSModuleManagerGUI_SetPage(IPSMMG_ACTION_MODULE, $module);
			break;
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
			IPSModuleManagerGUI_SetPage(IPSMMG_ACTION_MODULE, $module);
			break;
		case 'Delete':
			if (IPSModuleManagerGUI_GetLock($action, true)) {
				IPSLogger_Inf(__file__, 'IPSModuleManagerGUI - Delete of Module '.$module);
				$moduleManager = new IPSModuleManager($module);
				$moduleManager->DeleteModule();
			}
			IPSModuleManagerGUI_SetPage(IPSMMG_ACTION_OVERVIEW, $module);
			break;
		default:
			IPSModuleManagerGUI_SetPage($action, $module, $info);
	}
	IPSModuleManagerGUI_Refresh();


	/** @}*/
?>