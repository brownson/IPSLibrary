<?
    /**@defgroup koubachi_installation Koubachi Installation
     * @ingroup koubachi
     * @{
     *
     * Installations File für den Koubachi
     *
     * @section requirements_koubachi Installations Voraussetzungen Koubachi
     * - IPS Kernel >= 2.50
     * - IPSModuleManager >= 2.50.1
     *
     * @section visu_koubachi Visualisierungen für Koubachi
     * - WebFront 10Zoll
     * - Mobile
     *
     * @page install_koubachi Installations Schritte
     * Folgende Schritte sind zur Installation nötig:
     * - Laden des Modules (siehe IPSModuleManager)
     * - Konfiguration (Details siehe Konfiguration, Installation ist auch ohne spezielle Konfiguration möglich)
     * - Installation (siehe IPSModuleManager)
     *
     * @file          Koubachi_Installation.ips.php
     * @author        Dominik Zeiger
     * @version
     *  Version 1.0.008, 15.10.2012<br/>
     *
     */
	 
	namespace domizei\koubachi;

    if (!isset($moduleManager)) {
        IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

        echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
        $moduleManager = new \IPSModuleManager('Koubachi');
    }

    $moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
    $moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');

	Register_PhpErrorHandler($moduleManager);
	
    IPSUtils_Include ("IPSInstaller.inc.php", "IPSLibrary::install::IPSInstaller");
    IPSUtils_Include ('Koubachi.inc.php', 'IPSLibrary::app::hardware::Koubachi');
    IPSUtils_Include ("Koubachi_Configuration.inc.php", "IPSLibrary::config::hardware::Koubachi");
	
    // ----------------------------------------------------------------------------------------------------------------------------
    // Program Installation
    // ----------------------------------------------------------------------------------------------------------------------------
    $CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');
    $CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
    $CategoryIdConfig   = $moduleManager->GetModuleCategoryID('config');
	
	$CategoryIdDataDevices = CreateCategory("Devices", $CategoryIdData, 10);
	$CategoryIdDataPlants = CreateCategory("Plants", $CategoryIdData, 20);
    
    // Get Scripts Ids
    $ID_ScriptKoubachi = IPS_GetScriptIDByName('Koubachi', $CategoryIdApp);
	$ID_ScriptKoubachiUpdate = IPS_GetScriptIDByName('Koubachi_Update', $CategoryIdApp);
	
	// WebFront
	$WFC10_Enabled          = $moduleManager->GetConfigValue('Enabled', 'WFC10');
    $WFC10_ConfigId         = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
    $WFC10_Path             = $moduleManager->GetConfigValue('Path', 'WFC10');
    $WFC10_TabPaneItem      = $moduleManager->GetConfigValue('TabItem', 'WFC10');
    $WFC10_TabPaneParent    = $moduleManager->GetConfigValue('TabParent', 'WFC10');
    $WFC10_TabPaneName      = $moduleManager->GetConfigValue('TabName', 'WFC10');
    $WFC10_TabPaneOrder     = $moduleManager->GetConfigValueInt('TabOrder', 'WFC10');
    $WFC10_TabPaneIcon      = $moduleManager->GetConfigValue('TabIcon', 'WFC10');
    $WFC10_TabPaneExclusive = $moduleManager->GetConfigValueBoolDef('TabExclusive', 'WFC10', false);
	
	if ($WFC10_Enabled) {
		echo "--- Creating WebFront Interface ----------------------------------------------------------\n";
        $ID_CategoryWebFront        = CreateCategoryPath($WFC10_Path);
		EmptyCategory($ID_CategoryWebFront);
		
		$overviewVarId = CreateVariable("Overview", 3, $ID_CategoryWebFront, 0, '~HTMLBox', 0, '', '');
		IPS_SetName($overviewVarId, "Übersicht");
		
		// create devices and plants in IPS
		Koubachi_Update();
		
        $UniqueId = date('Hi');
        $baseName = $WFC10_TabPaneItem;
        DeleteWFCItems($WFC10_ConfigId, $baseName);
        
		//$ID_CategoryDevices = CreateCategory('Devices', $ID_CategoryWebFront, 10);
		//$ID_CategoryPlants = CreateCategory('Plants', $ID_CategoryWebFront, 10);
        /*CreateWFCItemTabPane($WFC10_ConfigId, $baseName,                 $WFC10_TabPaneParent, $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemCategory($WFC10_ConfigId, $baseName.'_CatDevices'.$UniqueId, $baseName, 0, 'Devices', $WFC10_TabPaneIcon, $ID_CategoryDevices, 'false');
        $deviceDataCategory = get_ObjectIDByPath(PATH_DATA_DEVICES);
		$childrenIds = IPS_GetChildrenIDs($deviceDataCategory);
		foreach($childrenIds as $childId) {
			$macAddress = GetValue(IPS_GetVariableIDByName (API_XML_DEVICE_MAC_ADDRESS, $childId));
			CreateLink($macAddress, $childId, $ID_CategoryDevices, 10);
		}*/
		
		//CreateWFCItemCategory  ($WFC10_ConfigId, $baseName.'_CatPlants'.$UniqueId, $baseName, 10, 'Plants', $WFC10_TabPaneIcon, $ID_CategoryPlants /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $baseName, $WFC10_TabPaneParent, 30, $WFC10_TabPaneName, $WFC10_TabPaneIcon, $ID_CategoryWebFront /*BaseId*/, 'false' /*BarBottomVisible*/);
        /*$plantDataCategory = get_ObjectIDByPath(PATH_DATA_PLANTS);
		$childrenIds = IPS_GetChildrenIDs($plantDataCategory);
		foreach($childrenIds as $childId) {
			$plantName = GetValue(IPS_GetVariableIDByName (API_XML_PLANT_NAME, $childId));
			CreateLink($plantName, $childId, $ID_CategoryPlants, 10);
			//$plantLocation = GetValue(IPS_GetVariableIDByName (API_XML_PLANT_LOCATION, $childId));
		}*/
		
        ReloadAllWebFronts();
    } else {
		Koubachi_Update(false);
	}
	
	// create timer for data update
	$TimerID = CreateTimer_CyclicByMinutes('Refresh', $ID_ScriptKoubachiUpdate, 240, true);
	
    // ------------------------------------------------------------------------------------------------
    function Register_PhpErrorHandler($moduleManager) {
        $file = IPS_GetKernelDir().'scripts\\__autoload.php';

        if (!file_exists($file)) {
            throw new Exception($file.' could NOT be found!', E_USER_ERROR);
        }
        $FileContent = file_get_contents($file);

        $pos = strpos($FileContent, 'IPSLogger_PhpErrorHandler.inc.php');

        if ($pos === false) {
            $includeCommand = '    IPSUtils_Include("IPSLogger_PhpErrorHandler.inc.php", "IPSLibrary::app::core::IPSLogger");';
            $FileContent = str_replace('?>', $includeCommand.PHP_EOL.'?>', $FileContent);
            $moduleManager->LogHandler()->Log('Register Php ErrorHandler of IPSLogger in File __autoload.php');
            file_put_contents($file, $FileContent);
        }
    }
    
    /** @}*/
?>