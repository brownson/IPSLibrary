<?
    /**@defgroup security_installation Security Installation
     * @ingroup security
     * @{
     *
     * Installations File für den Security
     *
     * @section requirements_security Installations Voraussetzungen Security
     * - IPS Kernel >= 2.50
     * - IPSModuleManager >= 2.50.1
     *
     * @section visu_security Visualisierungen für Security
     * - WebFront 10Zoll
     * - Mobile
     *
     * @page install_security Installations Schritte
     * Folgende Schritte sind zur Installation der EDIP Ansteuerung nötig:
     * - Laden des Modules (siehe IPSModuleManager)
     * - Konfiguration (Details siehe Konfiguration, Installation ist auch ohne spezielle Konfiguration möglich)
     * - Installation (siehe IPSModuleManager)
     *
     * @file          Security_Installation.ips.php
     * @author        Dominik Zeiger
     * @version
     *  Version 2.50.1, 31.01.2012<br/>
     *
     */

    if (!isset($moduleManager)) {
        IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

        echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
        $moduleManager = new IPSModuleManager('Security');
    }

    $moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
    $moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');

    IPSUtils_Include ("IPSInstaller.inc.php",           "IPSLibrary::install::IPSInstaller");
    IPSUtils_Include ("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
    
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
    $ID_ScriptSecurity = IPS_GetScriptIDByName('Security', $CategoryIdApp);
    $ID_ScriptSecurityMotionHandler = IPS_GetScriptIDByName('Security_MotionHandler', $CategoryIdApp);
	$ID_ScriptSecuritySmokeHandler = IPS_GetScriptIDByName('Security_SmokeHandler', $CategoryIdApp);
	$ID_ScriptSecurityClosureHandler = IPS_GetScriptIDByName('Security_ClosureHandler', $CategoryIdApp);
    $ID_ScriptSecurityEnableDisableAlarm = IPS_GetScriptIDByName('Security_EnableDisableAlarm', $CategoryIdApp);
    
    // TODO: enable logging
	CreateVariable(v_ALARM_ACTIVE, 0 /*Boolean*/, $CategoryIdData, 0, "~Switch", $ID_ScriptSecurityEnableDisableAlarm, false);
	CreateVariable(cat_MOTION."Log", 3 /* String */, $CategoryIdData, 0, "~HTMLBox", false, false);
	CreateVariable(cat_SMOKE."Log", 3 /* String */, $CategoryIdData, 0, "~HTMLBox", false, false);
	CreateVariable(cat_CLOSURE."Log", 3 /* String */, $CategoryIdData, 0, "~HTMLBox", false, false);
	
    /*$devices = getMotionDevices();
    $CategoryIdDataMotion = CreateCategory(cat_MOTION, $CategoryIdData, 50);
    foreach($devices as $deviceNumber => &$deviceConfig) {
        if(!isset($deviceConfig[c_Variable_ID]) || !IPS_ObjectExists($deviceConfig[c_Variable_ID])) {
            IPSLogger_Err(__file__, "No device variable defined.");
            throw new Exception("No device variable defined.");
        }
        $deviceId = $deviceConfig[c_Variable_ID];
    
        echo "Creating device ".$deviceConfig[c_Name]." (Location: ".$deviceConfig[c_Location].") in $CategoryIdDataMotion for $deviceId \n";
        $CategoryIdDevice = CreateCategory($deviceId, $CategoryIdDataMotion, 50);
        CreateVariable("Last".cat_MOTION, 3, $CategoryIdDevice, 10, "~HTMLBox");
        
        // TODO
        $motionEventId = CreateEvent($deviceId." - On Motion", $deviceId, $ID_ScriptSecurityMotionHandler);
    }
	
	$devices = getSmokeDevices();
    $CategoryIdDataSmoke = CreateCategory(cat_SMOKE, $CategoryIdData, 50);
    foreach($devices as $deviceNumber => &$deviceConfig) {
        $deviceId = $deviceConfig[c_Variable_ID];
    
        echo "Creating device ".$deviceConfig[c_Name]." (Location: ".$deviceConfig[c_Location].") in $CategoryIdDataSmoke for $deviceId \n";
        $CategoryIdDevice = CreateCategory($deviceId, $CategoryIdDataSmoke, 50);
        CreateVariable("Last".cat_SMOKE, 3, $CategoryIdDevice, 10, "~HTMLBox");
        
        // TODO
        $smokeEventId = CreateEvent($deviceId." - On Smoke", $deviceId, $ID_ScriptSecuritySmokeHandler);
    }*/
	
	createCategoryAndDevices($CategoryIdData, cat_MOTION, getMotionDevices(), $ID_ScriptSecurityMotionHandler);
	createCategoryAndDevices($CategoryIdData, cat_SMOKE, getSmokeDevices(), $ID_ScriptSecuritySmokeHandler);
	createCategoryAndDevices($CategoryIdData, cat_CLOSURE, getClosureDevices(), $ID_ScriptSecurityClosureHandler);
	
	function createCategoryAndDevices($parentCategory, $type, $devices, $handlerScriptId) {
		$typeCategoryId = CreateCategory($type, $parentCategory, 50);
		foreach($devices as $deviceNumber => &$deviceConfig) {
			$deviceId = $deviceConfig[c_Variable_ID];
		
			echo "Creating device ".$deviceConfig[c_Name]." (Location: ".$deviceConfig[c_Location].") in $typeCategoryId for $deviceId \n";
			$CategoryIdDevice = CreateCategory($deviceId, $typeCategoryId, 50);
			CreateVariable("Last".$type, 3 /*String*/, $CategoryIdDevice, 10, "~HTMLBox");
			
			// TODO
			$eventId = CreateEvent($deviceId." - On ".$type, $deviceId, $handlerScriptId);
		}
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
    
    return;
    
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
        $ID_CategoryOutput          = CreateCategory('Security', $ID_CategoryWebFront, 10);
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
            CreateLink("Security@".$device[DEVICE_IP], $device["STATE_ID"],    $ID_CategoryiPhone, 10);
        }
    }
    
    if(class_exists('Security')) {
        echo 'Register Security for MessageHandler';
        //$instanceIdAudioMax = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.Security.AudioMax_Server');
        //$instanceIdVariable = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.Security.AudioMax_Server.LAST_COMMAND');
        //IPSMessageHandler::RegisterOnChangeEvent($instanceIdVariable/*Var*/, 'IPSComponentAVControl_AudioMax,'.$instanceIdAudioMax, 'IPSModuleAVControl_Entertainment');
    }
    
    CreateProfile_DectStatus();
    
    function CreateProfile_DectStatus() {
        $Name = "Security_DectStatus";
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