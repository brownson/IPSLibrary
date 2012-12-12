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
     *  Version 0.0.001, 31.01.2012<br/>
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
    
    // Get Scripts Ids
    $ID_ScriptFritzBoxStatus  = IPS_GetScriptIDByName('FritzBox_Status',  $CategoryIdApp);
    CreateTimer_CyclicBySeconds ('FritzBox_GetStatus', $ID_ScriptFritzBoxStatus, 45);
    
    $ID_ScriptFritzBoxCallMonitor  = IPS_GetScriptIDByName('FritzBox_CallMonitor', $CategoryIdApp);
    
    // get configuration and create a category for each box
    $devices = get_FritzBoxDevices();
    foreach($devices as $deviceName => &$deviceConfig) {
        //echo "Creating device ".$device[DEVICE_IP]."in $CategoryIdData \n";
        $CategoryIdDevice = CreateCategory($deviceName, $CategoryIdData, 20);
        CreateVariable('SID',  3 /*String*/, $CategoryIdDevice, 10);
        $CategoryIdState = CreateCategory('State', $CategoryIdDevice, 50);
        
        $CategoryIdDect = CreateCategory('Dect', $CategoryIdState, 50);
        $deviceConfig["DECT_ID"] = $CategoryIdDect;
        
        $receiveInstanceId = CreateDummyInstance("Receive", $CategoryIdState, 0);
        $sendInstanceId = CreateDummyInstance("Send", $CategoryIdState, 10);
        $deviceConfig["STATE_ID"] = $CategoryIdState;
        $deviceConfig["RECEIVE_ID"] = $receiveInstanceId;
        $deviceConfig["SEND_ID"] = $sendInstanceId;
        
        // ----------------------------------------------------------------------------------------------------------------------------
        // Create Socket
        // ----------------------------------------------------------------------------------------------------------------------------
        $socketId = @IPS_GetInstanceIDByName($deviceName, 0);
        if($socketId === false) {
            echo "Socket Instance with name '$deviceName' not found. Creating...\n";
            $socketId = IPS_CreateInstance(GetModuleId("Client Socket"));
            IPS_SetName($socketId, $deviceName);
            CSCK_SetHost($socketId, $deviceConfig[DEVICE_IP]);
            CSCK_SetPort($socketId, 1012);
            CSCK_SetOpen($socketId, true);
            IPS_ApplyChanges($socketId);
        } else echo "Socket Instance with name '$deviceName' found @ $socketId\n";
        // ----------------------------------------------------------------------------------------------------------------------------
        // Create Register Variable
        // ----------------------------------------------------------------------------------------------------------------------------
        $regVarId = @IPS_GetInstanceIDByName("Call Monitor", $CategoryIdDevice);
        if($regVarId === false) {
            echo "RegVar Instance with name 'Call Monitor' not found. Creating...\n";
            $regVarId = IPS_CreateInstance(GetModuleId("Register Variable"));
            IPS_SetParent($regVarId, $CategoryIdDevice);
            IPS_SetName($regVarId, "Call Monitor");
        } else echo "RegVar Instance with name 'Call Monitor' found @ $regVarId\n";
        
        RegVar_SetRXObjectID($regVarId, $ID_ScriptFritzBoxCallMonitor);
        IPS_ConnectInstance($regVarId, $socketId);
        IPS_ApplyChanges($regVarId);
    }
    
    function GetModuleId($moduleName) {
        foreach (IPS_GetModuleList() as $moduleId) {
            $module = IPS_GetModule($moduleId);
            if ($module['ModuleName'] == $moduleName) {
                return $moduleId;
            }
        }
        return '';
    }
    
    // ----------------------------------------------------------------------------------------------------------------------------
    // Webfront Installation
    // ----------------------------------------------------------------------------------------------------------------------------
    if ($WFC10_Enabled) {
        $ID_CategoryWebFront        = CreateCategoryPath($WFC10_Path);
        EmptyCategory($ID_CategoryWebFront);
        $ID_CategoryOutput          = CreateCategory('FritzBox', $ID_CategoryWebFront, 10);
        $ID_CategoryLeft            = CreateCategory('Left',     $ID_CategoryOutput, 10);
        $ID_CategoryRight           = CreateCategory('Right',    $ID_CategoryOutput, 20);

        $UniqueId = date('Hi');
        $baseName = $WFC10_TabPaneItem.'_'.$WFC10_TabPaneName;
        DeleteWFCItems($WFC10_ConfigId, $baseName);
        DeleteWFCItems($WFC10_ConfigId, $baseName.'_OvSP');
        
        CreateWFCItemTabPane   ($WFC10_ConfigId, $baseName,                          $WFC10_TabPaneItem,         $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
        CreateWFCItemSplitPane ($WFC10_ConfigId, $baseName.'_OvSP',                  $baseName, 0, $WFC10_TabName1, $WFC10_TabIcon1, 1 /*Vertical*/, 50 /*Width*/, 0 /*Target=Pane1*/, 0 /*Percent*/, 'true');
        CreateWFCItemCategory  ($WFC10_ConfigId, $baseName.'_OvCatLeft'.$UniqueId,   $baseName.'_OvSP', $WFC10_TabOrder1, $WFC10_TabName1, $WFC10_TabIcon1, $ID_CategoryLeft /*BaseId*/, 'false' /*BarBottomVisible*/);
        CreateWFCItemCategory  ($WFC10_ConfigId, $baseName.'_OvCatRight'.$UniqueId, $baseName.'_OvSP', $WFC10_TabOrder1, $WFC10_TabName1, $WFC10_TabIcon1, $ID_CategoryRight /*BaseId*/, 'false' /*BarBottomVisible*/);
        
        $count = count($devices);
        if($count == 1) {
            foreach($devices as $device) {
                CreateLink($device[DEVICE_IP]." - Receive", $device["RECEIVE_ID"], $ID_CategoryLeft, 10);
                CreateLink($device[DEVICE_IP]." - Send", $device["SEND_ID"], $ID_CategoryLeft, 10);
            }
            // Dect Status
            $dectChildren = IPS_GetChildrenIDs($device["DECT_ID"]);
            $i = 0;
            foreach($dectChildren as $dectChild) {
                $dectName = GetValueString(IPS_GetObjectIDByName("Name", $dectChild));
                CreateLink($device[DEVICE_IP]." - ".$dectName, $dectChild, $ID_CategoryRight, $i++);
            }
        } else {
            // TODO: create categories?
            foreach($devices as $device) {
                CreateLink($device[DEVICE_IP]." - Receive", $device["RECEIVE_ID"], $ID_CategoryLeft, 10);
                CreateLink($device[DEVICE_IP]." - Send", $device["SEND_ID"], $ID_CategoryLeft, 10);
            }
            //CreateLink($device[DEVICE_IP]." - DECT Status", $device["DECT_ID"], $ID_CategoryRight, 50);
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
    }
    
    if(class_exists('FritzBox')) {
        echo 'Register FritzBox for MessageHandler';
        //$instanceIdAudioMax = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.FritzBox.AudioMax_Server');
        //$instanceIdVariable = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.FritzBox.AudioMax_Server.LAST_COMMAND');
        //IPSMessageHandler::RegisterOnChangeEvent($instanceIdVariable/*Var*/, 'IPSComponentAVControl_AudioMax,'.$instanceIdAudioMax, 'IPSModuleAVControl_Entertainment');
    }
    
    CreateProfile_DectStatus();

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
    
    function CreateProfile_DectStatus() {
        $Name = "FritzBox_DectStatus";
        @IPS_DeleteVariableProfile($Name);
        IPS_CreateVariableProfile($Name, 1);
        IPS_SetVariableProfileText($Name, "", "");
        IPS_SetVariableProfileValues($Name, 0, 0, 0);
        IPS_SetVariableProfileDigits($Name, 0);
        IPS_SetVariableProfileIcon($Name, "");
        IPS_SetVariableProfileAssociation($Name, 0, "Getrennt", "", 0xaaaaaa);
        IPS_SetVariableProfileAssociation($Name, 1, "Paging", "", 0x0000CD);
        IPS_SetVariableProfileAssociation($Name, 2, "Verbunden", "", 0x32CD32);
        IPS_SetVariableProfileAssociation($Name, 3, "Verbunden", "", 0x32CD32);
        IPS_SetVariableProfileAssociation($Name, 4, "Verbindungsaufbau", "", 0xFFFF00);
    }

    /** @}*/
?>