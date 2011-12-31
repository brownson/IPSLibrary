<?
	/**@defgroup ipslogger_installation IPSLogger Installation
	 * @ingroup ipslogger
	 * @{
	 *
	 * Installations File für den IPSLogger
	 *
	 * @section requirements_ipslogger Installations Voraussetzungen IPSLogger
	 * - keine
	 *
	 * @section visu_ipslogger Visualisierungen für IPSLogger
	 * - WebFront 10Zoll
	 * - Mobile
	 *
	 * @file          IPSLogger_Installation.ips.php
	 * @author        Andreas Brauneis
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.ips.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSModuleManager');
	}


	IPSUtils_Include ("IPSInstaller.ips.php",            "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSLogger_Configuration.ips.php", "IPSLibrary::config::core::IPSLogger");

	$libraryPath = $moduleManager->GetConfigValue(IPSConfigurationHandler::LIBRARY);
	$AppPath        = $libraryPath.".IPSLibrary.app.core.IPSLogger";
	$DataPath       = $libraryPath.".IPSLibrary.data.core.IPSLogger";
	$ConfigPath     = $libraryPath.".IPSLibrary.config.core.IPSLogger";

	$WFC10_Enabled  = $moduleManager->GetConfigValue('Enabled', 'WFC10');
	$WFC10_ConfigId = $moduleManager->GetConfigValue('ID', 'WFC10');
	$WFC10_Path     = $moduleManager->GetConfigValue('Path', 'WFC10');
	$WFC10_Parent   = $moduleManager->GetConfigValue('Root', 'WFC10');
	$WFC10_TabName  = $moduleManager->GetConfigValue('TabName', 'WFC10');
	$WFC10_TabIcon  = $moduleManager->GetConfigValue('TabIcon', 'WFC10');
	$WFC10_TabOrder = $moduleManager->GetConfigValue('TabOrder', 'WFC10');

	$Mobile_Enabled = $moduleManager->GetConfigValue('Enabled', 'Mobile');
	$Mobile_Path    = $moduleManager->GetConfigValue('Path', 'Mobile');
	$Mobile_Order   = $moduleManager->GetConfigValue('Order', 'Mobile');
	$Mobile_Icon    = $moduleManager->GetConfigValue('Icon', 'Mobile');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------

	$CategoryIdData = CreateCategoryPath($DataPath);
	$CategoryIdApp  = CreateCategoryPath($AppPath);
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

	SetVariableConstant ("c_ID_SingleOutEnabled",   $ID_SingleOutEnabled, $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_SingleOutLevel",     $ID_SingleOutLevel,   $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_SingleOutMsg",       $ID_SingleOutMsg,     $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_HtmlOutEnabled",     $ID_HtmlOutEnabled,   $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_HtmlOutLevel",       $ID_HtmlOutLevel,     $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_HtmlOutMsgCount",    $ID_HtmlOutMsgCount,  $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_HtmlOutMsgId",       $ID_HtmlOutMsgId,     $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_HtmlOutMsgList",     $ID_HtmlOutMsgList,   $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_IPSOutEnabled",      $ID_IPSOutEnabled,    $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_IPSOutLevel",        $ID_IPSOutLevel,      $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_EMailOutEnabled",    $ID_EMailOutEnabled,  $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_EMailOutLevel",      $ID_EMailOutLevel,    $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_EMailOutPriority",   $ID_EMailOutPriority, $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_EMailOutDelay",      $ID_EMailOutSendDelay,$AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_EMailOutMsgList",    $ID_EMailOutMsgList,  $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_FileOutEnabled",     $ID_FileOutEnabled,   $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_FileOutLevel",       $ID_FileOutLevel,     $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_FileOutDays",        $ID_FileOutDays,      $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_Log4IPSOutEnabled",  $ID_Log4IPSOutEnabled,$AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_Log4IPSOutLevel",    $ID_Log4IPSOutLevel,  $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_Log4IPSOutDays",     $ID_Log4IPSOutDays,   $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_ScriptSendMail",     $ID_ScriptIPSLoggerSendMail, $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_ScriptPurgeLogFiles",$ID_ScriptIPSLoggerPurgeLogFiles, $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_EchoOutEnabled",     $ID_EchoOutEnabled,   $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_EchoOutLevel",       $ID_EchoOutLevel,     $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_ProwlOutEnabled",    $ID_ProwlOutEnabled,  $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_ProwlOutLevel",      $ID_ProwlOutLevel,    $AppPath.'IPSLogger_IDs.ips.php');
	SetVariableConstant ("c_ID_ProwlOutPriority",   $ID_ProwlOutPriority, $AppPath.'IPSLogger_IDs.ips.php');

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
		CreateWFCItemTabPane   ($WFC10_ConfigId, 'SystemTP',                       $WFC10_Parent, $WFC10_TabOrder, $WFC10_TabName, $WFC10_TabIcon);
		CreateWFCItemCategory  ($WFC10_ConfigId, 'SystemTP_LogWindow'.$UniqueId,   'SystemTP',  10, 'Logging', 'Window', $ID_CategoryOutput /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, 'SystemTP_LogSettings'.$UniqueId, 'SystemTP',  20, 'Log Settings','Gear',   $ID_CategorySettings /*BaseId*/, 'true' /*BarBottomVisible*/);

		// Output Window
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
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// iPhone Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled) {
		$ID_CategoryiPhone    = CreateCategoryPath($Mobile_Path, $Mobile_Order, $Mobile_Icon);
		CreateLink('Logging Window',   $ID_HtmlOutMsgList,    $ID_CategoryiPhone, 10);
		$ID_CategoryiPhone    = CreateCategoryPath($Mobile_Path.'.Logging Settings', 20, 'Gear');

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

	// ----------------------------------------------------------------------------------------------------------------------------
	// Some Tests
	// ----------------------------------------------------------------------------------------------------------------------------
	IPSUtils_Include ("IPSLogger.ips.php", "IPSLibrary::app::core::IPSLogger");

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