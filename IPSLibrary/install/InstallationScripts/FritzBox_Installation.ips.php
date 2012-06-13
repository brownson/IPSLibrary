<?
    /**@defgroup fritzbox_installation FritzBox Installation
     * @ingroup fritzbox
     * @{
     *
     * Installations File für den FritzBox
     *
     * @section requirements_fritzbox Installations Voraussetzungen FritzBox
     * - IPS Kernel >= 2.50
     * - IPSModuleManager >= 2.50.1
     *
     * @section visu_fritzbox Visualisierungen für FritzBox
     * - WebFront 10Zoll
     * - Mobile
     *
     * @page install_fritzbox Installations Schritte
     * Folgende Schritte sind zur Installation der EDIP Ansteuerung nötig:
     * - Laden des Modules (siehe IPSModuleManager)
     * - Konfiguration (Details siehe Konfiguration, Installation ist auch ohne spezielle Konfiguration möglich)
     * - Installation (siehe IPSModuleManager)
     *
     * @file          FritzBox_Installation.ips.php
     * @author        Dominik Zeiger
     * @version
     *  Version 2.50.1, 31.01.2012<br/>
     *
     */

    if (!isset($moduleManager)) {
        IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

        echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
        $moduleManager = new IPSModuleManager('FritzBox');
    }

    $moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
    $moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');

    IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
    IPSUtils_Include ("FritzBox_Configuration.inc.php", "IPSLibrary::config::hardware::FritzBox");

    $WFC10_Enabled          = $moduleManager->GetConfigValue('Enabled', 'WFC10');
    $WFC10_ConfigId         = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
    $WFC10_Path             = $moduleManager->GetConfigValue('Path', 'WFC10');
    $WFC10_TabPaneItem      = $moduleManager->GetConfigValue('TabPaneItem', 'WFC10');
    $WFC10_TabPaneParent    = $moduleManager->GetConfigValue('TabPaneParent', 'WFC10');
    $WFC10_TabPaneName      = $moduleManager->GetConfigValue('TabPaneName', 'WFC10');
    $WFC10_TabPaneOrder     = $moduleManager->GetConfigValueInt('TabPaneOrder', 'WFC10');
    $WFC10_TabPaneIcon      = $moduleManager->GetConfigValue('TabPaneIcon', 'WFC10');
    $WFC10_TabPaneExclusive = $moduleManager->GetConfigValueBoolDef('TabPaneExclusive', 'WFC10', false);
    
    $WFC10_TabItem1         = $moduleManager->GetConfigValue('TabItem1', 'WFC10');
    $WFC10_TabName1         = $moduleManager->GetConfigValue('TabName1', 'WFC10');
    $WFC10_TabIcon1         = $moduleManager->GetConfigValue('TabIcon1', 'WFC10');
    $WFC10_TabOrder1        = $moduleManager->GetConfigValueInt('TabOrder1', 'WFC10');

    $Mobile_Enabled         = $moduleManager->GetConfigValue('Enabled', 'Mobile');
    $Mobile_Path            = $moduleManager->GetConfigValue('Path', 'Mobile');
    $Mobile_PathOrder       = $moduleManager->GetConfigValueInt('PathOrder', 'Mobile');
    $Mobile_PathIcon        = $moduleManager->GetConfigValue('PathIcon', 'Mobile');
    $Mobile_Name1           = $moduleManager->GetConfigValue('Name1', 'Mobile');
    $Mobile_Order1          = $moduleManager->GetConfigValueInt('Order1', 'Mobile');
    $Mobile_Icon1           = $moduleManager->GetConfigValue('Icon1', 'Mobile');

    // ----------------------------------------------------------------------------------------------------------------------------
    // Program Installation
    // ----------------------------------------------------------------------------------------------------------------------------

    $CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');
    $CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
    $CategoryIdConfig   = $moduleManager->GetModuleCategoryID('config');
    
    // get configuration and create a category for each box
    $devices = get_FritzBoxDevices();
    foreach($devices as &$device) {
        //echo "Creating device ".$device[DEVICE_IP]."in $CategoryIdData \n";
        createModule($device, $CategoryIdData);
    }
    
    function createModule(&$device, $categoryId) {
        $ip = str_replace(".", "-", $device[DEVICE_IP]);
        $CategoryIdDevice        = CreateCategory("IP$ip",     $categoryId, 20);
        CreateVariable('SID',  3 /*String*/, $CategoryIdDevice, 10);
        $CategoryIdState        = CreateCategory('State',     $CategoryIdDevice, 50);
        
        $receiveInstanceId = createDslInformationNode("Receive", $CategoryIdState, 0);
        $sendInstanceId = createDslInformationNode("Send", $CategoryIdState, 10);
        $device["STATE_ID"] = $CategoryIdState;
        $device["RECEIVE_ID"] = $receiveInstanceId;
        $device["SEND_ID"] = $sendInstanceId;
    }
    
    function createDslInformationNode($name, $categoryId, $position) {
        $instanceId      = CreateDummyInstance($name, $categoryId, $position);
        return $instanceId;
    }
    
    // Get Scripts Ids
    $ID_ScriptFritzBoxStatus  = IPS_GetScriptIDByName('FritzBox_Status',  $CategoryIdApp);
    CreateTimer_CyclicBySeconds ('FritzBox_GetStatus', $ID_ScriptFritzBoxStatus, 45, false);

    // ----------------------------------------------------------------------------------------------------------------------------
    // Webfront Installation
    // ----------------------------------------------------------------------------------------------------------------------------
    if ($WFC10_Enabled) {
        $ID_CategoryWebFront        = CreateCategoryPath($WFC10_Path);
        $ID_CategoryOutput          = CreateCategory('FritzBox',    $ID_CategoryWebFront, 10);
        $ID_CategoryLeft            = CreateCategory('Left',      $ID_CategoryOutput,         100);
        $ID_CategoryRight           = CreateCategory('Right',        $ID_CategoryOutput,  10);

        $UniqueId = date('Hi');
        DeleteWFCItems($WFC10_ConfigId, 'SystemTP_FritzBox');
        DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvSPLeft');
        DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvSPRight');
        //CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,  $WFC10_TabPaneParent, $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
        //CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.$WFC10_TabItem1.$UniqueId, $WFC10_TabPaneItem, $WFC10_TabOrder1, $WFC10_TabName1, $WFC10_TabIcon1, $ID_CategoryOutput /*BaseId*/, 'false' /*BarBottomVisible*/);
        
        CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,                          $WFC10_TabPaneParent,         $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
        CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvSP',              $WFC10_TabPaneItem,              0, $WFC10_TabName1, $WFC10_TabIcon1, 1 /*Vertical*/, 50 /*Width*/, 0 /*Target=Pane1*/, 0 /*Percent*/, 'true');
        CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvCatLeft'.$UniqueId,   $WFC10_TabPaneItem.'_OvSP',  $WFC10_TabOrder1, $WFC10_TabName1, $WFC10_TabIcon1, $ID_CategoryLeft /*BaseId*/, 'false' /*BarBottomVisible*/);
        //CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvSPRight',             $WFC10_TabPaneItem,  0, '', '', 0 /*Horizontal*/, 50 /*Width*/, 0 /*Target=Pane1*/, 0 /*Percent*/, 'true');
        CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvCatRight.'.$UniqueId,   $WFC10_TabPaneItem.'_OvSP', $WFC10_TabOrder1, $WFC10_TabName1, $WFC10_TabIcon1, $ID_CategoryRight /*BaseId*/, 'false' /*BarBottomVisible*/);
        foreach($devices as $device) {
            CreateLink($device[DEVICE_IP]." - Receive", $device["RECEIVE_ID"],    $ID_CategoryLeft, 10);
            CreateLink($device[DEVICE_IP]." - Send", $device["SEND_ID"],    $ID_CategoryRight, 10);
        }

        ReloadAllWebFronts();
    }

    // ----------------------------------------------------------------------------------------------------------------------------
    // iPhone Installation
    // ----------------------------------------------------------------------------------------------------------------------------
    if ($Mobile_Enabled) {
        $ID_CategoryiPhone    = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
        
        foreach($devices as $device) {
            CreateLink("FritzBox@".$device[DEVICE_IP], $device["STATE_ID"],    $ID_CategoryiPhone, 10);
        }
        /*$ID_Output = CreateDummyInstance("Widget", $ID_CategoryiPhone, 100);
        CreateLink('Receive',   $ID_SingleOutEnabled,             $ID_Output,   10);
        CreateLink('Send',      $ID_SingleOutLevel,               $ID_Output,   20);*/
    }

    Register_PhpErrorHandler($moduleManager);

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