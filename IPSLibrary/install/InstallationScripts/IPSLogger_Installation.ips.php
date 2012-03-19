<?
	/**@defgroup ipslogger_installation IPSLogger Installation
	 * @ingroup ipslogger
	 * @{
	 *
	 * Installations File für den IPSLogger
	 *
	 * @section requirements_ipslogger Installations Voraussetzungen IPSLogger
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 *
	 * @section visu_ipslogger Visualisierungen für IPSLogger
	 * - WebFront 10Zoll
	 * - Mobile
	 *
	 * @page install_ipslogger Installations Schritte
	 * Folgende Schritte sind zur Installation der EDIP Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration, Installation ist auch ohne spezielle Konfiguration möglich)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSLogger_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 31.01.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('IPSLogger');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSLogger_Configuration.inc.php", "IPSLibrary::config::core::IPSLogger");

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
	$WFC10_TabItem2         = $moduleManager->GetConfigValue('TabItem2', 'WFC10');
	$WFC10_TabName2         = $moduleManager->GetConfigValue('TabName2', 'WFC10');
	$WFC10_TabIcon2         = $moduleManager->GetConfigValue('TabIcon2', 'WFC10');
	$WFC10_TabOrder2        = $moduleManager->GetConfigValueInt('TabOrder2', 'WFC10');

	$Mobile_Enabled         = $moduleManager->GetConfigValue('Enabled', 'Mobile');
	$Mobile_Path            = $moduleManager->GetConfigValue('Path', 'Mobile');
	$Mobile_PathOrder       = $moduleManager->GetConfigValueInt('PathOrder', 'Mobile');
	$Mobile_PathIcon        = $moduleManager->GetConfigValue('PathIcon', 'Mobile');
	$Mobile_Name1           = $moduleManager->GetConfigValue('Name1', 'Mobile');
	$Mobile_Order1          = $moduleManager->GetConfigValueInt('Order1', 'Mobile');
	$Mobile_Icon1           = $moduleManager->GetConfigValue('Icon1', 'Mobile');
	$Mobile_Name2           = $moduleManager->GetConfigValue('Name2', 'Mobile');
	$Mobile_Order2          = $moduleManager->GetConfigValueInt('Order2', 'Mobile');
	$Mobile_Icon2           = $moduleManager->GetConfigValue('Icon2', 'Mobile');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------

	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');
	$InstanceId     = CreateDummyInstance("IPSLogger", $CategoryIdData, 0);

	CreateProfiles();

	// Get Scripts Ids
	$ID_ScriptIPSLoggerChangeSettings  = IPS_GetScriptIDByName('IPSLogger_ChangeSettings',  $CategoryIdApp);
	$ID_ScriptIPSLoggerSendMail        = IPS_GetScriptIDByName('IPSLogger_SendMail',        $CategoryIdApp);
	$ID_ScriptIPSLoggerClearSingleOut  = IPS_GetScriptIDByName('IPSLogger_ClearSingleOut',  $CategoryIdApp);
	$ID_ScriptIPSLoggerClearHtmlOut    = IPS_GetScriptIDByName('IPSLogger_ClearHtmlOut',    $CategoryIdApp);
	$ID_ScriptIPSLoggerPurgeLogFiles   = IPS_GetScriptIDByName('IPSLogger_PurgeLogFiles',   $CategoryIdApp);
	CreateTimer ('IPSLogger_PurgeLogFilesTimer', $ID_ScriptIPSLoggerPurgeLogFiles, 8);

	// Add IPSLogger Variables
	$ID_SingleOutEnabled  = CreateVariable('SingleOut_Enabled',  0 /*Boolean*/, $InstanceId, 100, '~Switch',            $ID_ScriptIPSLoggerChangeSettings, true,        'Power');
	$ID_SingleOutLevel    = CreateVariable('SingleOut_Level',    1 /*Integer*/, $InstanceId, 110, 'IPSLogger_Level',    $ID_ScriptIPSLoggerChangeSettings, 1 /*Error*/, 'Intensity');
	$ID_SingleOutMsg      = CreateVariable('SingleOut_Msg',      3 /*String*/,  $InstanceId, 120, '~HTMLBox',           null, ""  ,                                     'Window');
	$ID_HtmlOutEnabled    = CreateVariable('HtmlOut_Enabled',    0 /*Boolean*/, $InstanceId, 200, '~Switch',            $ID_ScriptIPSLoggerChangeSettings, true,        'Power');
	$ID_HtmlOutLevel      = CreateVariable('HtmlOut_Level',      1 /*Integer*/, $InstanceId, 210, 'IPSLogger_Level',    $ID_ScriptIPSLoggerChangeSettings, 9 /*All*/,   'Intensity');
	$ID_HtmlOutMsgCount   = CreateVariable('HtmlOut_MsgCount',   1 /*Integer*/, $InstanceId, 220, 'IPSLogger_MsgCount', $ID_ScriptIPSLoggerChangeSettings, 20,          'Distance');
	$ID_HtmlOutMsgId      = CreateVariable('HtmlOut_MsgId',      1 /*Integer*/, $InstanceId, 230, 'IPSLogger_MsgId',    null, 0,                                        'Repeat');
	$ID_HtmlOutMsgList    = CreateVariable('HtmlOut_MsgList',    3 /*String*/,  $InstanceId, 240, '~HTMLBox',           null,  "",                                      'Window');
	$ID_IPSOutEnabled     = CreateVariable('IPSOut_Enabled',     0 /*Boolean*/, $InstanceId, 300, '~Switch',            $ID_ScriptIPSLoggerChangeSettings, true,        'Power');
	$ID_IPSOutLevel       = CreateVariable('IPSOut_Level',       1 /*Integer*/, $InstanceId, 310, 'IPSLogger_Level',    $ID_ScriptIPSLoggerChangeSettings, 5 /*Debug*/, 'Intensity');
	$ID_FileOutEnabled    = CreateVariable('FileOut_Enabled',    0 /*Boolean*/, $InstanceId, 400, '~Switch',            $ID_ScriptIPSLoggerChangeSettings, false,       'Power');
	$ID_FileOutLevel      = CreateVariable('FileOut_Level',      1 /*Integer*/, $InstanceId, 410, 'IPSLogger_Level',    $ID_ScriptIPSLoggerChangeSettings, 5 /*Debug*/, 'Intensity');
	$ID_FileOutDays       = CreateVariable('FileOut_Days',       1 /*Integer*/, $InstanceId, 420, 'IPSLogger_Days',     $ID_ScriptIPSLoggerChangeSettings, 1,           'Repeat');
	$ID_Log4IPSOutEnabled = CreateVariable('Log4IPSOut_Enabled', 0 /*Boolean*/, $InstanceId, 500, '~Switch',            $ID_ScriptIPSLoggerChangeSettings, true,        'Power');
	$ID_Log4IPSOutLevel   = CreateVariable('Log4IPSOut_Level',   1 /*Integer*/, $InstanceId, 510, 'IPSLogger_Level',    $ID_ScriptIPSLoggerChangeSettings, 5 /*Debug*/, 'Intensity');
	$ID_Log4IPSOutDays    = CreateVariable('Log4IPSOut_Days',    1 /*Integer*/, $InstanceId, 520, 'IPSLogger_Days',     $ID_ScriptIPSLoggerChangeSettings, 1,           'Repeat');
	$ID_EMailOutEnabled   = CreateVariable('EMailOut_Enabled',   0 /*Boolean*/, $InstanceId, 600, '~Switch',            $ID_ScriptIPSLoggerChangeSettings, false,       'Power');
	$ID_EMailOutLevel     = CreateVariable('EMailOut_Level',     1 /*Integer*/, $InstanceId, 610, 'IPSLogger_Level',    $ID_ScriptIPSLoggerChangeSettings, 5 /*Debug*/, 'Intensity');
	$ID_EMailOutPriority  = CreateVariable('EMailOut_Priority',  1 /*Integer*/, $InstanceId, 620, 'IPSLogger_Priority', $ID_ScriptIPSLoggerChangeSettings, 0,           'Return');
	$ID_EMailOutSendDelay = CreateVariable('EMailOut_SendDelay', 1 /*Integer*/, $InstanceId, 630, 'IPSLogger_Delay',    $ID_ScriptIPSLoggerChangeSettings, 5,           'LockClosed');
	$ID_EMailOutMsgList   = CreateVariable('EMailOut_MsgList',   3 /*String*/,  $InstanceId, 640, '~TextBox',           null, "",                                       'Mail');
	$ID_EchoOutEnabled    = CreateVariable('EchoSOut_Enabled',   0 /*Boolean*/, $InstanceId, 700, '~Switch',            $ID_ScriptIPSLoggerChangeSettings, false,       'Power');
	$ID_EchoOutLevel      = CreateVariable('EchoOut_Level',      1 /*Integer*/, $InstanceId, 710, 'IPSLogger_Level',    $ID_ScriptIPSLoggerChangeSettings, 9 /*All*/,   'Intensity');
	$ID_ProwlOutEnabled   = CreateVariable('ProwlOut_Enabled',   0 /*Boolean*/, $InstanceId, 800, '~Switch',            $ID_ScriptIPSLoggerChangeSettings, false,       'Power');
	$ID_ProwlOutLevel     = CreateVariable('ProwlOut_Level',     1 /*Integer*/, $InstanceId, 810, 'IPSLogger_Level',    $ID_ScriptIPSLoggerChangeSettings, 1 /*Error*/, 'Intensity');
	$ID_ProwlOutPriority  = CreateVariable('ProwlOut_Priority',  1 /*Integer*/, $InstanceId, 820, 'IPSLogger_Priority', $ID_ScriptIPSLoggerChangeSettings, 0,           'Return');

	SetVariableConstant ("c_ID_SingleOutEnabled",   $ID_SingleOutEnabled, 'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_SingleOutLevel",     $ID_SingleOutLevel,   'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_SingleOutMsg",       $ID_SingleOutMsg,     'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_HtmlOutEnabled",     $ID_HtmlOutEnabled,   'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_HtmlOutLevel",       $ID_HtmlOutLevel,     'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_HtmlOutMsgCount",    $ID_HtmlOutMsgCount,  'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_HtmlOutMsgId",       $ID_HtmlOutMsgId,     'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_HtmlOutMsgList",     $ID_HtmlOutMsgList,   'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_IPSOutEnabled",      $ID_IPSOutEnabled,    'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_IPSOutLevel",        $ID_IPSOutLevel,      'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_EMailOutEnabled",    $ID_EMailOutEnabled,  'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_EMailOutLevel",      $ID_EMailOutLevel,    'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_EMailOutPriority",   $ID_EMailOutPriority, 'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_EMailOutDelay",      $ID_EMailOutSendDelay,'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_EMailOutMsgList",    $ID_EMailOutMsgList,  'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_FileOutEnabled",     $ID_FileOutEnabled,   'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_FileOutLevel",       $ID_FileOutLevel,     'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_FileOutDays",        $ID_FileOutDays,      'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_Log4IPSOutEnabled",  $ID_Log4IPSOutEnabled,'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_Log4IPSOutLevel",    $ID_Log4IPSOutLevel,  'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_Log4IPSOutDays",     $ID_Log4IPSOutDays,   'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_ScriptSendMail",     $ID_ScriptIPSLoggerSendMail, 'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_ScriptPurgeLogFiles",$ID_ScriptIPSLoggerPurgeLogFiles, 'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_EchoOutEnabled",     $ID_EchoOutEnabled,   'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_EchoOutLevel",       $ID_EchoOutLevel,     'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_ProwlOutEnabled",    $ID_ProwlOutEnabled,  'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_ProwlOutLevel",      $ID_ProwlOutLevel,    'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');
	SetVariableConstant ("c_ID_ProwlOutPriority",   $ID_ProwlOutPriority, 'IPSLogger_IDs.inc.php', 'IPSLibrary::app::core::IPSLogger');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$ID_CategoryWebFront         = CreateCategoryPath($WFC10_Path);
		$ID_CategoryOutput           = CreateCategory('Output',    $ID_CategoryWebFront, 10);
		$ID_CategorySettings         = CreateCategory('Settings',  $ID_CategoryWebFront, 20);
		$ID_CategorySettingsWidget   = CreateCategory(  'Widget',  $ID_CategorySettings, 200);
		$ID_CategorySettingsWebFront = CreateCategory(  'WebFront',$ID_CategorySettings, 300);
		$ID_CategorySettingsIPS      = CreateCategory(  'IPS',     $ID_CategorySettings, 400);
		$ID_CategorySettingsFile     = CreateCategory(  'File',    $ID_CategorySettings, 500);
		$ID_CategorySettingsLog4IPS  = CreateCategory(  'Log4IPS', $ID_CategorySettings, 600);
		$ID_CategorySettingsEMail    = CreateCategory(  'EMail',   $ID_CategorySettings, 700);
		$ID_CategorySettingsProwl    = CreateCategory(  'Prowl',   $ID_CategorySettings, 800);

		$UniqueId = date('Hi');
		DeleteWFCItems($WFC10_ConfigId, 'SystemTP_LogWindow');
		DeleteWFCItems($WFC10_ConfigId, 'SystemTP_LogSettings');
		DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem.$WFC10_TabItem1);
		DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem.$WFC10_TabItem2);
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,  $WFC10_TabPaneParent, $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.$WFC10_TabItem1.$UniqueId, $WFC10_TabPaneItem, $WFC10_TabOrder1, $WFC10_TabName1, $WFC10_TabIcon1, $ID_CategoryOutput /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.$WFC10_TabItem2.$UniqueId, $WFC10_TabPaneItem, $WFC10_TabOrder2, $WFC10_TabName2, $WFC10_TabIcon2, $ID_CategorySettings /*BaseId*/, 'true' /*BarBottomVisible*/);

		CreateLink('Logging Window',   $ID_HtmlOutMsgList,    $ID_CategoryOutput, 10);

		// Output Overview
		CreateLink('Output Widget',    $ID_SingleOutEnabled,  $ID_CategorySettings, 10);
		CreateLink('Output WebFront',  $ID_HtmlOutEnabled,    $ID_CategorySettings, 20);
		CreateLink('Output IPS',       $ID_IPSOutEnabled,     $ID_CategorySettings, 30);
		CreateLink('Output File',      $ID_FileOutEnabled,    $ID_CategorySettings, 40);
		CreateLink('Output Log4IPS',   $ID_Log4IPSOutEnabled, $ID_CategorySettings, 50);
		CreateLink('Output EMail',     $ID_EMailOutEnabled,   $ID_CategorySettings, 60);
		CreateLink('Output Echo',      $ID_EchoOutEnabled,    $ID_CategorySettings, 70);
		CreateLink('Output Prowl',     $ID_ProwlOutEnabled,   $ID_CategorySettings, 80);

		// Output Detail
		CreateLink('Output Enabled',   $ID_SingleOutEnabled,             $ID_CategorySettingsWidget,   10);
		CreateLink('Logging Level',    $ID_SingleOutLevel,               $ID_CategorySettingsWidget,   20);
		CreateLink('Last Message',     $ID_SingleOutMsg,                 $ID_CategorySettingsWidget,   30);
		CreateLink('Clear Message',    $ID_ScriptIPSLoggerClearSingleOut,$ID_CategorySettingsWidget,   40);
		CreateLink('Output Enabled',   $ID_HtmlOutEnabled,               $ID_CategorySettingsWebFront, 10);
		CreateLink('Logging Level',    $ID_HtmlOutLevel,                 $ID_CategorySettingsWebFront, 20);
		CreateLink('Message Count',    $ID_HtmlOutMsgCount,              $ID_CategorySettingsWebFront, 30);
		CreateLink('Last MessageID',   $ID_HtmlOutMsgId,                 $ID_CategorySettingsWebFront, 40);
		CreateLink('Clear Output',     $ID_ScriptIPSLoggerClearHtmlOut,  $ID_CategorySettingsWebFront, 50);
		CreateLink('Message List',     $ID_HtmlOutMsgList,               $ID_CategorySettingsWebFront, 60);
		CreateLink('Output Enabled',   $ID_IPSOutEnabled,                $ID_CategorySettingsIPS,      10);
		CreateLink('Logging Level',    $ID_IPSOutLevel,                  $ID_CategorySettingsIPS,      20);
		CreateLink('Output Enabled',   $ID_FileOutEnabled,               $ID_CategorySettingsFile,     10);
		CreateLink('Logging Level',    $ID_FileOutLevel,                 $ID_CategorySettingsFile,     20);
		CreateLink('Purge Files after',$ID_FileOutDays,                  $ID_CategorySettingsFile,     30);
		CreateLink('Execute Purge',    $ID_ScriptIPSLoggerPurgeLogFiles, $ID_CategorySettingsFile,     40);
		CreateLink('Output Enabled',   $ID_Log4IPSOutEnabled,            $ID_CategorySettingsLog4IPS,  10);
		CreateLink('Logging Level',    $ID_Log4IPSOutLevel,              $ID_CategorySettingsLog4IPS,  20);
		CreateLink('Purge Files after',$ID_Log4IPSOutDays,               $ID_CategorySettingsLog4IPS,  30);
		CreateLink('Execute Purge',    $ID_ScriptIPSLoggerPurgeLogFiles, $ID_CategorySettingsLog4IPS,  40);
		CreateLink('Output Enabled',   $ID_EMailOutEnabled,              $ID_CategorySettingsEMail,    10);
		CreateLink('Logging Level',    $ID_EMailOutLevel,                $ID_CategorySettingsEMail,    20);
		CreateLink('Priority',         $ID_EMailOutPriority,             $ID_CategorySettingsEMail,    30);
		CreateLink('Send Delay',       $ID_EMailOutSendDelay,            $ID_CategorySettingsEMail,    40);
		CreateLink('Message List',     $ID_EMailOutMsgList,              $ID_CategorySettingsEMail,    50);
		CreateLink('Output Enabled',   $ID_ProwlOutEnabled,              $ID_CategorySettingsProwl,    10);
		CreateLink('Logging Level',    $ID_ProwlOutLevel,                $ID_CategorySettingsProwl,    20);
		CreateLink('Priority',         $ID_ProwlOutPriority,             $ID_CategorySettingsProwl,    30);

		// Installation of Info Widget
		$wfcItems=WFC_GetItems($WFC10_ConfigId);
		$widget=false;
		foreach ($wfcItems as $item) {
			if ($item['ClassName']=='InfoWidget' and strpos($item['Configuration'], (string)$ID_SingleOutMsg) > 0) {
				echo 'InfoWidget already installed.'.PHP_EOL;
				$widget = true;
			}
		}
		if (!$widget) {
			CreateWFCItemWidget ($WFC10_ConfigId, 'IPSLogger_Widget', 'roottp', 90, $ID_SingleOutMsg, $ID_ScriptIPSLoggerClearSingleOut);
	 	}

		ReloadAllWebFronts();
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// iPhone Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled) {
		$ID_CategoryiPhone    = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		CreateLink($Mobile_Name1,   $ID_HtmlOutMsgList,    $ID_CategoryiPhone, $Mobile_Order1);
		$ID_CategoryiPhone    = CreateCategoryPath($Mobile_Path.'.'.$Mobile_Name2, $Mobile_Order2, $Mobile_Icon2);

		$ID_Output = CreateDummyInstance("Widget", $ID_CategoryiPhone, 100);
		CreateLink('Output Enabled',   $ID_SingleOutEnabled,             $ID_Output,   10);
		CreateLink('Logging Level',    $ID_SingleOutLevel,               $ID_Output,   20);
		CreateLink('Last Message',     $ID_SingleOutMsg,                 $ID_Output,   30);
		CreateLink('Clear Message',    $ID_ScriptIPSLoggerClearSingleOut,$ID_Output,   40);

		$ID_Output = CreateDummyInstance("WebFront", $ID_CategoryiPhone, 200);
		CreateLink('Output Enabled',   $ID_HtmlOutEnabled,               $ID_Output,   10);
		CreateLink('Logging Level',    $ID_HtmlOutLevel,                 $ID_Output,   20);
		CreateLink('Message Count',    $ID_HtmlOutMsgCount,              $ID_Output,   30);
		CreateLink('Clear Output',     $ID_ScriptIPSLoggerClearHtmlOut,  $ID_Output,   50);

		$ID_Output = CreateDummyInstance("IPS", $ID_CategoryiPhone, 300);
		CreateLink('Output Enabled',   $ID_IPSOutEnabled,                $ID_Output,   10);
		CreateLink('Logging Level',    $ID_IPSOutLevel,                  $ID_Output,   20);

		$ID_Output = CreateDummyInstance("LogFile", $ID_CategoryiPhone, 400);
		CreateLink('Output Enabled',   $ID_FileOutEnabled,               $ID_Output,   10);
		CreateLink('Logging Level',    $ID_FileOutLevel,                 $ID_Output,   20);
		CreateLink('Purge Files after',$ID_FileOutDays,                  $ID_Output,   30);
		CreateLink('Execute Purge',    $ID_ScriptIPSLoggerPurgeLogFiles, $ID_Output,   40);

		$ID_Output = CreateDummyInstance("Log4IPS", $ID_CategoryiPhone, 500);
		CreateLink('Output Enabled',   $ID_Log4IPSOutEnabled,            $ID_Output,   10);
		CreateLink('Logging Level',    $ID_Log4IPSOutLevel,              $ID_Output,   20);
		CreateLink('Purge Files after',$ID_Log4IPSOutDays,               $ID_Output,   30);
		CreateLink('Execute Purge',    $ID_ScriptIPSLoggerPurgeLogFiles, $ID_Output,   40);

		$ID_Output = CreateDummyInstance("EMail", $ID_CategoryiPhone, 600);
		CreateLink('Output Enabled',   $ID_EMailOutEnabled,              $ID_Output,   10);
		CreateLink('Logging Level',    $ID_EMailOutLevel,                $ID_Output,   20);
		CreateLink('Priority',         $ID_EMailOutPriority,             $ID_Output,   30);
		CreateLink('Send Delay',       $ID_EMailOutSendDelay,            $ID_Output,   40);
		CreateLink('Message List',     $ID_EMailOutMsgList,              $ID_Output,   50);

		$ID_Output = CreateDummyInstance("Prowl", $ID_CategoryiPhone, 700);
		CreateLink('Output Enabled',   $ID_ProwlOutEnabled,              $ID_Output,   10);
		CreateLink('Logging Level',    $ID_ProwlOutLevel,                $ID_Output,   20);
		CreateLink('Priority',         $ID_ProwlOutPriority,             $ID_Output,   30);
	}

	Register_PhpErrorHandler($moduleManager);

	
	// ----------------------------------------------------------------------------------------------------------------------------
	// Some Tests
	// ----------------------------------------------------------------------------------------------------------------------------
	IPSUtils_Include ("IPSLogger.inc.php", "IPSLibrary::app::core::IPSLogger");

	//  Some Test Messages
	IPSLogger_Fat(__file__, 'Test for a Fatal Error');
	IPSLogger_Err(__file__, 'Test for a Error ...');
	IPSLogger_Wrn(__file__, 'Test for a Warning');
	IPSLogger_Not(__file__, 'Test for a Notification with Priority 0 (High)');
	IPSLogger_Not(__file__, 'Test for a Notification with Priority 10 (Low)');
 	IPSLogger_Inf(__file__, 'Test for a Information Message ...');
 	IPSLogger_Dbg(__file__, 'Test for a Debug Message ...');
 	IPSLogger_Com(__file__, 'Test for a Communication Message ...');
 	IPSLogger_Trc(__file__, 'Test for a Trace Message ...');
 	IPSLogger_Tst(__file__, 'Test for a Test Message ...');

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

	// ------------------------------------------------------------------------------------------------
	function CreateTimer ($Name, $Parent, $Hour) {
	   $TimerId = @IPS_GetEventIDByName($Name, $Parent);
	   if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
   		IPS_SetName($TimerId, $Name);
   		IPS_SetParent($TimerId, $Parent);
			if (!IPS_SetEventCyclic($TimerId, 2 /**Daily*/, 1,0,0,0,0)) {
				echo "IPS_SetEventCyclic failed !!!\n";
			}
			if (!IPS_SetEventCyclicTimeBounds($TimerId, mktime($Hour, 0, 0), 0)) {
				echo "IPS_SetEventCyclicTimeBounds failed !!!\n";
			}
   		IPS_SetEventActive($TimerId, true);
			echo 'Created Timer '.$Name.'='.$TimerId."\n";
		}
		return $TimerId;
	}

   // ------------------------------------------------------------------------------------------------
	function CreateProfiles() {
		@IPS_CreateVariableProfile("IPSLogger_Level", 1);
		IPS_SetVariableProfileText("IPSLogger_Level", "", "");
		IPS_SetVariableProfileValues("IPSLogger_Level", 0, 0, 0);
		IPS_SetVariableProfileDigits("IPSLogger_Level", 0);
		IPS_SetVariableProfileIcon("IPSLogger_Level", "");
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 0, "Fatal",         "", 0xaaaaaa);
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 1, "Error",         "", 0xaaaaaa);
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 2, "Warning",       "", 0xaaaaaa);
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 3, "Notification",  "", 0xaaaaaa);
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 4, "Info",          "", 0xaaaaaa);
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 5, "Debug",         "", 0xaaaaaa);
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 6, "Communication", "", 0xaaaaaa);
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 7, "Trace",         "", 0xaaaaaa);
		IPS_SetVariableProfileAssociation("IPSLogger_Level", 9, "All",           "", 0xaaaaaa);

		@IPS_CreateVariableProfile("IPSLogger_MsgCount", 1);
		IPS_SetVariableProfileText("IPSLogger_MsgCount", "", " Msg's");
		IPS_SetVariableProfileValues("IPSLogger_MsgCount", 5, 100, 5);
		IPS_SetVariableProfileDigits("IPSLogger_MsgCount", 0);
		IPS_SetVariableProfileIcon("IPSLogger_MsgCount", "");

		@IPS_CreateVariableProfile("IPSLogger_MsgId", 1);
		IPS_SetVariableProfileText("IPSLogger_MsgId", "", "");
		IPS_SetVariableProfileValues("IPSLogger_MsgId", 0, 0, 0);
		IPS_SetVariableProfileDigits("IPSLogger_MsgId", 0);
		IPS_SetVariableProfileIcon("IPSLogger_MsgId", "");

		@IPS_CreateVariableProfile("IPSLogger_Delay", 1);
		IPS_SetVariableProfileText("IPSLogger_Delay", "", " Seconds");
		IPS_SetVariableProfileValues("IPSLogger_Delay", 0, 600, 30);
		IPS_SetVariableProfileDigits("IPSLogger_Delay", 0);
		IPS_SetVariableProfileIcon("IPSLogger_Delay", "");

		@IPS_CreateVariableProfile("IPSLogger_Days", 1);
		IPS_SetVariableProfileText("IPSLogger_Days", "", " Tage");
		IPS_SetVariableProfileValues("IPSLogger_Days", 0, 100, 5);
		IPS_SetVariableProfileDigits("IPSLogger_Days", 0);
		IPS_SetVariableProfileIcon("IPSLogger_Days", "");

		@IPS_CreateVariableProfile("IPSLogger_Priority", 1);
		IPS_SetVariableProfileText("IPSLogger_Priority", "", "");
		IPS_SetVariableProfileValues("IPSLogger_Priority", 0, 10, 1);
		IPS_SetVariableProfileDigits("IPSLogger_Priority", 0);
		IPS_SetVariableProfileIcon("IPSLogger_Priority", "");
	}
	/** @}*/
?>