<?
	/*
	 * This file is part of the IPSLibrary.
	 *
	 * The IPSLibrary is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published
	 * by the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * The IPSLibrary is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
	 */

	 /**@defgroup IPSHealth_visualization IPSHealth Visualisierung
	 * @ingroup IPSHealth
	 * @{
	 *
	 * Visualisierungen von IPSHealth
	 *
	 * IPSHealth WebFront Visualisierung:
	 *
	 *  Übersicht über aller Alarm
	 *  @image html IPSHealth_WebFrontOverview.jpg
	 *  <BR>
	 *  Detailansicht einer Alarm Konfiguration
	 *  @image html IPSHealth_WebFrontSettings.jpg
	 *
	 *
	 * IPSHealth Mobile Visualisierung:
	 *
	 *  Übersicht über aller Alarm
	 *  @image html IPSHealth_MobileOverview.png
	 *  <BR>
	 *  Detailansicht einer Alarm Konfiguration
	 *  @image html IPSHealth_MobileSettings.png
	 *
	 *@}*/

	 /**@defgroup IPSHealth_install IPSHealth Installation
	 * @ingroup IPSHealth
	 * @{
	 *
	 * Script zur kompletten Installation der IPSHealth Steuerung.
	 *
	 * Vor der Installation muß das File IPSHealth_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * In dem File IPSHealth_Custom.inc.php werden die Wekcer Aktionen parametriert
	 *
	 * In dem File IPSHealth.ini werden die Webfront Konfigurationen angepasst.
	 *
	 * @page rquirements_IPSHealth Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 *
	 * @page install_IPSHealth Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSHealth Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSHealth_Installation.ips.php
	 * @author        André Czwalina
	 * @version
	 *  Version 1.00.0, 01.04.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('IPSHealth');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php"				, "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSHealth_Constants.inc.php"		, "IPSLibrary::app::modules::IPSHealth");
	IPSUtils_Include ("IPSHealth_Configuration.inc.php", "IPSLibrary::config::modules::IPSHealth");
//	IPSUtils_Include ("IPSHealth_SystemInfo.ips.php"	, "IPSLibrary::config::modules::IPSHealth");

	$WFC_Enabled        = $moduleManager->GetConfigValue('Enabled', 'WFC');
	$WFC_ConfigId       = $moduleManager->GetConfigValueIntDef('ID', 'WFC', GetWFCIdDefault());
	$WFC_Path           = $moduleManager->GetConfigValue('Path', 'WFC');
	$WFC_TabPaneItem    = $moduleManager->GetConfigValue('TabPaneItem', 'WFC');
	$WFC_TabPaneParent  = $moduleManager->GetConfigValue('TabPaneParent', 'WFC');
	$WFC_TabPaneName    = $moduleManager->GetConfigValue('TabPaneName', 'WFC');
	$WFC_TabPaneIcon    = $moduleManager->GetConfigValue('TabPaneIcon', 'WFC');
	$WFC_TabPaneOrder   = $moduleManager->GetConfigValueInt('TabPaneOrder', 'WFC');
	$WFC_TabName1       = $moduleManager->GetConfigValue('TabName1', 'WFC');
	$WFC_TabIcon1       = $moduleManager->GetConfigValue('TabIcon1', 'WFC');
/*
	$WFC10_Enabled        = $moduleManager->GetConfigValue('Enabled', 'WFC10');
	$WFC10_ConfigId       = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
	$WFC10_Path           = $moduleManager->GetConfigValue('Path', 'WFC10');
	$WFC10_TabPaneItem    = $moduleManager->GetConfigValue('TabPaneItem', 'WFC10');
	$WFC10_TabPaneParent  = $moduleManager->GetConfigValue('TabPaneParent', 'WFC10');
	$WFC10_TabPaneName    = $moduleManager->GetConfigValue('TabPaneName', 'WFC10');
	$WFC10_TabPaneIcon    = $moduleManager->GetConfigValue('TabPaneIcon', 'WFC10');
	$WFC10_TabPaneOrder   = $moduleManager->GetConfigValueInt('TabPaneOrder', 'WFC10');
	$WFC10_TabName1       = $moduleManager->GetConfigValue('TabName1', 'WFC10');
	$WFC10_TabIcon1       = $moduleManager->GetConfigValue('TabIcon1', 'WFC10');

	$Touch_Enabled        = $moduleManager->GetConfigValue('Enabled', 'Touch');
	$Touch_ConfigId       = $moduleManager->GetConfigValueIntDef('ID', 'Touch', GetWFCIdDefault());
	$Touch_Path           = $moduleManager->GetConfigValue('Path', 'Touch');
	$Touch_TabPaneItem    = $moduleManager->GetConfigValue('TabPaneItem', 'Touch');
	$Touch_TabPaneParent  = $moduleManager->GetConfigValue('TabPaneParent', 'Touch');
	$Touch_TabPaneName    = $moduleManager->GetConfigValue('TabPaneName', 'Touch');
	$Touch_TabPaneIcon    = $moduleManager->GetConfigValue('TabPaneIcon', 'Touch');
	$Touch_TabPaneOrder   = $moduleManager->GetConfigValueInt('TabPaneOrder', 'Touch');
	$Touch_TabName1       = $moduleManager->GetConfigValue('TabName1', 'Touch');
	$Touch_TabIcon1       = $moduleManager->GetConfigValue('TabIcon1', 'Touch');

	$Mobile_Enabled       = $moduleManager->GetConfigValue('Enabled', 'Mobile');
	$Mobile_Path          = $moduleManager->GetConfigValue('Path', 'Mobile');
	$Mobile_PathOrder     = $moduleManager->GetConfigValueInt('PathOrder', 'Mobile');
	$Mobile_PathIcon      = $moduleManager->GetConfigValue('PathIcon', 'Mobile');

	$eDIP_Enabled       = $moduleManager->GetConfigValue('Enabled', 'eDIP');
	$eDIP_Path          = $moduleManager->GetConfigValue('Path', 'eDIP');
	$eDIP_PathOrder     = $moduleManager->GetConfigValueInt('PathOrder', 'eDIP');
	$eDIP_PathIcon      = $moduleManager->GetConfigValue('PathIcon', 'eDIP');
*/
	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$AppPath        = "Program.IPSLibrary.app.modules.IPSHealth";
	$DataPath       = "Program.IPSLibrary.data.modules.IPSHealth";
	$ConfigPath     = "Program.IPSLibrary.config.modules.IPSHealth";

	$CategoryIdData = CreateCategoryPath($DataPath);
	$CategoryIdApp  = CreateCategoryPath($AppPath);

	// Add Scripts
	$ScriptIdTimer  	= IPS_GetScriptIDByName('IPSHealth_Timer'				, $CategoryIdApp);
	$ScriptIdCS  		= IPS_GetScriptIDByName('IPSHealth_ChangeSettings'	, $CategoryIdApp);
	$ScriptIdhc  		= IPS_GetScriptIDByName('IPSHealth_HighChart_Queue'	, $CategoryIdApp);
//   $ScriptIdSysInfo 	= IPS_GetScriptIDByName('IPSHealth_SystemInfo'	, $CategoryIdApp);

	// Archiv Handler
	$archiveHandlerID = IPS_GetInstanceIDByName("Archive Handler", 0);


	// Create Associations
	// ----------------------------------------------------------------------------------------------------------------------------

 	CreateProfile_Associations ('IPSHealth_Err', array(
												0	=> 'Kein Fehler',
												1	=> 'Fehler',
												),'',

											array(
												0  	=>	0x00FF00,
												1  	=>	0xFF0000,
												));

 	CreateProfile_Associations ('IPSHealth_NA', array(
												0	=> 'Aus',
												1	=> 'läuft ..',
												),'',

											array(
												0  	=>	0x00FF00,
												1  	=>	0xFF0000,
												));

 	CreateProfile_Associations ('IPSHealth_Select', array(
												1	=> '---',
												),'',

											array(
												1  	=>	0x0066CC,
												));


 	CreateProfile ('IPSHealth_Pro', '', '', '', true, '', ' %', 0);
 	CreateProfile ('IPSHealth_Sec', '', '', '', true, '', ' Sek.', 0);
 	CreateProfile ('IPSHealth_MB', '', '', '', true, '', ' MB', 2);
 	CreateProfile ('IPSHealth_GB', '', '', '', true, '', ' GB', 2);


	$CategoryIds		= CreateCategory(c_HealthCircles			, $CategoryIdData, 300);
	$CategoryIdSATI	= CreateCategory(c_Control_Statistik	, $CategoryIdData, 300);
	$CategoryIdDBW		= CreateCategory(c_Control_DBWartung	, $CategoryIdData, 300);
	$CategoryIdDBM		= CreateCategory(c_Control_DBMonitor	, $CategoryIdData, 300);
	$CategoryIdSVR		= CreateCategory(c_Control_Server		, $CategoryIdData, 300);
	$CategoryIdHC		= CreateCategory(c_Control_HightChart	, $CategoryIdData, 300);
	$CategoryIdIFS    = CreateCategory(c_Control_Interfaces , $CategoryIdData, 300);
//	$CategoryIdCTRL	= CreateCategory(c_Control_CTRL			, $CategoryIdData, 300);

	$Idx  = 1;
	$configData = get_HealthConfiguration();

	foreach ($configData as $Name=>$Data) {
			$CircleId     			= CreateCategory($Name, $CategoryIds, 10+$Idx);
			$CircleUebersichtId	= CreateVariable(c_Control_Uebersicht	, 3 /*String*/		, $CircleId, 10, '~HTMLBox', null, '');
			$CricleSWId			 	= CreateVariable(c_Control_Select		, 1 /*Integer*/	, $CircleId, 20, 'IPSHealth_Select'	, $ScriptIdCS, 0);
			$CricleErrId		 	= CreateVariable(c_Control_Error			, 0 /*Boolean*/	, $CircleId, 30, 'IPSHealth_Err'	, null, 0);
			$intervall 				= $Data[c_CircleIntervall];
			$TimerId					= CreateTimer_BySeconds ($Name.'-Timeout', $ScriptIdTimer, $intervall, true);
			
	}

   CreateTimer_OnceADay("SysInfo-Day"			, $ScriptIdTimer	, 0						, 0); 		// Tages Timer für Datenbankgröße
	CreateTimer_BySeconds("SysInfo-Server"		, $ScriptIdTimer	, 60						, true);		// Timer 1 min. für Server Info
	CreateTimer_BySeconds("SysInfo-DBHealth"	, $ScriptIdTimer	, c_Warn_Schwellwert	, true);		// Timer gemäß Para für Datenbank Überwachung.
	CreateTimer_BySeconds("High"		, $ScriptIdhc		, 3553					, true);    // Timer für Hight Chart
	
   IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');
	$moduleManager = new IPSModuleManager('IPSHealth');
	$version = $moduleManager->VersionHandler()->GetModuleVersion();
	$version = str_replace("..Installing", "", $version);

	// Übersicht
	$UebersichtId	 = CreateVariable(c_Control_Uebersicht			, 3 /*String*/,  $CategoryIdData, 10, '~HTMLBox'		, null, '');
	$CricleErrId	 = CreateVariable(c_Control_Error				, 0 /*Boolean*/, $CategoryIdData, 20, 'IPSHealth_Err'	, null, 0);
	$ModulSYSId		 = CreateVariable(c_Control_System				, 0 /*Boolean*/, $CategoryIdData, 10, 'IPSHealth_Select'	, $ScriptIdCS, 0);
	$ModulIFSId		 = CreateVariable(c_Control_IOInterfaces			, 0 /*Boolean*/, $CategoryIdData, 20, 'IPSHealth_Select'	, $ScriptIdCS, 0);
	$ModulUpdateId	 = CreateVariable(c_Control_Modul				, 0 /*Boolean*/, $CategoryIdData, 10, 'IPSHealth_Select'	, $ScriptIdCS, 0);
	$ModulVersionId = CreateVariable(c_Control_Version				, 0 /*Boolean*/, $CategoryIdData, 20, 'IPSHealth_Select'	, $ScriptIdCS, 0);
//	$Uebersicht3Id	 = CreateVariable(c_Control_UebersichtCircle	, 3 /*String*/,  $CategoryIdData, 30, '~HTMLBox', null, '');

	//Interfaces
	$UebersichtIFSId	 = CreateVariable(c_Control_Uebersicht		, 3 /*String*/,  $CategoryIdIFS, 1, '~HTMLBox'		, null, '');


	// Logging
	$CategoryIdLog	 = CreateCategory('Log', $CategoryIdData, 210);
	$ControlIdLog   = CreateVariable('LogMessages',  3 /*String*/,  $CategoryIdLog, 220, '~HTMLBox', null, '');
	$ControlIdLogId = CreateVariable('LogId',        1 /*Integer*/, $CategoryIdLog, 230, '',         null, 0);

	//System Info
	// Statistik
	$SysCategoryID 	= CreateVariable(c_Property_Categorys,      	1 /*Integer*/	, $CategoryIdSATI, 10, '',         null, 0);
	$SysEventsID 		= CreateVariable(c_Property_Events,         	1 /*Integer*/	, $CategoryIdSATI, 20, '',         null, 0);
	$SysInstancesID 	= CreateVariable(c_Property_Instances,      	1 /*Integer*/	, $CategoryIdSATI, 30, '',         null, 0);
	$SysLinksID 		= CreateVariable(c_Property_Links,          	1 /*Integer*/	, $CategoryIdSATI, 40, '',         null, 0);
	$SysModulesID 		= CreateVariable(c_Property_Modules,        	1 /*Integer*/	, $CategoryIdSATI, 50, '',         null, 0);
	$SysObjectsID 		= CreateVariable(c_Property_Objects,        	1 /*Integer*/	, $CategoryIdSATI, 60, '',         null, 0);
	$SysProfilesID 	= CreateVariable(c_Property_Profiles,       	1 /*Integer*/	, $CategoryIdSATI, 70, '',         null, 0);
	$SysScriptsID 		= CreateVariable(c_Property_Scripts,        	1 /*Integer*/	, $CategoryIdSATI, 80, '',         null, 0);
	$SysVariableID 	= CreateVariable(c_Property_Variable,       	1 /*Integer*/	, $CategoryIdSATI, 90, '',         null, 0);
	$SysDBGroesseID 	= CreateVariable(c_Property_DB_Groesse,     	2 /*Float*/		, $CategoryIdSATI, 100, 'IPSHealth_MB',        null, 0);
	$SysDBZuwachsID 	= CreateVariable(c_Property_DB_Zuwachs,     	2 /*Float*/		, $CategoryIdSATI, 110, 'IPSHealth_MB',        null, 0);
	$SysUptimeID 		= CreateVariable(c_Property_Uptime,     	  	1 /*Integer*/	, $CategoryIdSATI, 120, '',        null, 0);
//	$SysUptimeHumanID = CreateVariable(c_Property_UptimeHuman,    	3 /*String*/	, $CategoryIdSATI, 130, '',        null, '');
	$SysBetriebStdIID = CreateVariable(c_Property_BetriebStdI,    	1 /*Integer*/	, $CategoryIdSATI, 140, ''					, null, 0);
//	$SysBetriebStdSID = CreateVariable(c_Property_BetriebStdS,    	3 /*String*/	, $CategoryIdSATI, 150, ''					, null, '');

	// Datenbank Health
	$SysDBFehlerID 	= CreateVariable(c_Property_DB_Fehler,      	0 /*Boolean*/	, $CategoryIdDBM, 10, 'IPSHealth_Err'	, null, 0);
	$SyslastWriteID 	= CreateVariable(c_Property_lastWrite,      	1 /*Integer*/	, $CategoryIdDBM, 20, 'IPSHealth_Sec'	, null, 0);
	$SysLogDBGroesseID= CreateVariable(c_Property_LogDB_Groesse,  	2 /*Float*/		, $CategoryIdDBM, 30, 'IPSHealth_MB'	, null, 0);

	// Datenbank Wartung
	$SysDBHistory  	= CreateVariable(c_Property_DBHistory,    	3 /*String*/	, $CategoryIdDBW, 10, '',        null, '');
	$SysDBNeuagg  		= CreateVariable(c_Property_DBNeuagg,    		0 /*Boolean*/	, $CategoryIdDBW, 20, 'IPSHealth_NA', $ScriptIdCS, 0);
	$SysDBVarGes  		= CreateVariable(c_Property_DBVarGes,    		1 /*Integer*/	, $CategoryIdDBW, 30, '',        null, 0);
	$SysDBSteps  		= CreateVariable(c_Property_DBSteps,    		2 /*Float*/		, $CategoryIdDBW, 40, 'IPSHealth_Pro',        null, 100);
	$SysDBVarReady		= CreateVariable(c_Property_DBVarReady,    	1 /*Integer*/	, $CategoryIdDBW, 50, '',        null, 0);
	$SysDBaktVar		= CreateVariable(c_Property_DBaktVar,	    	3 /*String*/	, $CategoryIdDBW, 60, '',        null, '');
	$SysDBStart			= CreateVariable(c_Property_DBStart,    		3 /*String*/	, $CategoryIdDBW, 70, '',        null, '');
	$SysDBReady			= CreateVariable(c_Property_DBReady,    		3 /*String*/	, $CategoryIdDBW, 80, '',        null, '');

	// Server Info
	$SysServerZeit 	= CreateVariable(c_Property_ServerZeit,		3 /*String*/	, $CategoryIdSVR, 10, '',       null, '');
	$SysServerHDD 		= CreateVariable(c_Property_ServerHDD,  		2 /*Float*/		, $CategoryIdSVR, 20, 'IPSHealth_GB',        null, 0);
	$SysServerCPU 		= CreateVariable(c_Property_ServerCPU,  		1 /*Integer*/	, $CategoryIdSVR, 30, 'IPSHealth_Pro',      null, 0);

	// HightChart
	$SysHCQueue 		= CreateVariable(c_Control_HCQueue,		3 /*String*/	, $CategoryIdHC, 10, '~HTMLBox',       null, '');

	// Datenbank Logging einschalten
	AC_SetLoggingStatus($archiveHandlerID, $SysCategoryID		, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysEventsID		, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysInstancesID	, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysLinksID			, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysModulesID		, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysObjectsID		, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysProfilesID		, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysScriptsID		, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysVariableID		, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysDBGroesseID	, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysDBZuwachsID	, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysLogDBGroesseID, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysServerHDD		, c_SYS_Logging);
	AC_SetLoggingStatus($archiveHandlerID, $SysServerCPU		, c_SYS_Logging);

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront > 19" Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC_Enabled and $WFC_ConfigId <> '') {
		$WebFrontId               = CreateCategoryPath($WFC_Path, 10);
		EmptyCategory($WebFrontId);
		$WebFrontOverview1		 = CreateCategory( 'Overview_1'		, $WebFrontId,    10);
		$WebFrontOverview2		 = CreateCategory( 'Overview_2'		, $WebFrontId,    20);
		$WebFrontOverview3		 = CreateCategory( 'Overview_3'		, $WebFrontId,    30);
		$WebFrontOverview4		 = CreateCategory( 'Overview_4'		, $WebFrontId,    40);
		$WebFrontOverviewSYSL    = CreateCategory( 'Statistik'		, $WebFrontId,    50);
		$WebFrontOverviewSYSR    = CreateCategory( 'System Info'		, $WebFrontId,    60);
		$WebFrontOverviewMLD     = CreateCategory( 'Meldungen'		, $WebFrontId,    70);

		DeleteWFCItems($WFC_ConfigId, $WFC_TabPaneItem);

		// Übersicht
		CreateWFCItemTabPane   ($WFC_ConfigId, $WFC_TabPaneItem,             	$WFC_TabPaneParent, 	$WFC_TabPaneOrder, 		$WFC_TabPaneName, $WFC_TabPaneIcon);
//		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_Overview'	, $WFC_TabPaneItem					, 	10, 'Übersicht'	, '', $WebFrontOverview /*BaseId*/	, 'false' /*BarBottomVisible*/);
		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_Overview'	, $WFC_TabPaneItem					,	10, 'Übersicht'	, '', 0 /*Horizontal*/	, 50 /*Width*/	, 	0 /*Target=Pane1*/, 0/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_1'			, $WFC_TabPaneItem.'_OV_Overview',	10, ''	, '', 1 /*Vertical*/	, 66 /*Width*/	, 	0 /*Target=Pane1*/, 0/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_2'			, $WFC_TabPaneItem.'_OV_1'			,	10, ''	, '', 1 /*Vertical*/	, 50 /*Width*/	, 	0 /*Target=Pane1*/, 0/*UsePixel*/, 'true');

		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV4'				, $WFC_TabPaneItem.'_OV_Overview', 	20, 'Queue'		, '', $WebFrontOverview4 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV3'				, $WFC_TabPaneItem.'_OV_1'			, 	20, 'Circle'	, '', $WebFrontOverview3 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV2'				, $WFC_TabPaneItem.'_OV_2'			, 	20, 'Circle'	, '', $WebFrontOverview2 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV1'				, $WFC_TabPaneItem.'_OV_2'			, 	10, 'Circle'	, '', $WebFrontOverview1 /*BaseId*/, 'false' /*BarBottomVisible*/);

		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_SysInfo_SP', 	$WFC_TabPaneItem					,	20, 'System Info'	, '', 1 /*Vertikal*/	, 50 /*Width*/	, 	0 /*Target=Pane1*/, 0/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_SysInfoL',		$WFC_TabPaneItem.'_SysInfo_SP', 	10, 'Statistik'	, '', $WebFrontOverviewSYSL /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_SysInfoR',		$WFC_TabPaneItem.'_SysInfo_SP', 	20, 'System Info'	, '', $WebFrontOverviewSYSR /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_Log',  		$WFC_TabPaneItem					, 	30, 'Logging'		, '', $WebFrontOverviewMLD /*BaseId*/, 'false' /*BarBottomVisible*/);                             // integer $PercentageSlider

		$Dummy_SysInfoId 	= CreateInstance ('IPS Statistik'		, $WebFrontOverviewSYSL, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 10);
		$Dummy_DBHealthId	= CreateInstance ('IPS DB-Monitoring'	, $WebFrontOverviewSYSR, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 20);
		$Dummy_DBWartungId= CreateInstance ('IPS DB-Wartung'		, $WebFrontOverviewSYSR, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 30);
		$Dummy_ServerId 	= CreateInstance ('Server Info'			, $WebFrontOverviewSYSR, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 40);
//		$Dummy_Log_Id 		= CreateInstance ('Meldungen', 	$WebFrontOverviewMLD, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 50);

		// Statistik
		CreateLink		(c_Property_Categorys,				$SysCategoryID,  			$Dummy_SysInfoId, 10);
		CreateLink		(c_Property_Events,					$SysEventsID,  			$Dummy_SysInfoId, 20);
		CreateLink		(c_Property_Instances,				$SysInstancesID,  		$Dummy_SysInfoId, 30);
		CreateLink		(c_Property_Links,					$SysLinksID,  				$Dummy_SysInfoId, 40);
		CreateLink		(c_Property_Modules,					$SysModulesID,  			$Dummy_SysInfoId, 50);
		CreateLink		(c_Property_Objects,					$SysObjectsID,  			$Dummy_SysInfoId, 60);
		CreateLink		(c_Property_Profiles,				$SysProfilesID,  			$Dummy_SysInfoId, 70);
		CreateLink		(c_Property_Scripts,					$SysScriptsID,  			$Dummy_SysInfoId, 80);
		CreateLink		(c_Property_Variable,				$SysVariableID,  			$Dummy_SysInfoId, 90);
		CreateLink		(c_Property_DB_Groesse,				$SysDBGroesseID,  		$Dummy_SysInfoId, 100);
		CreateLink		(c_Property_DB_Zuwachs,				$SysDBZuwachsID,  		$Dummy_SysInfoId, 110);
		CreateLink		(c_Property_Uptime,					$SysUptimeID,  			$Dummy_SysInfoId, 120);
		CreateLink		(c_Property_BetriebStdI,			$SysBetriebStdIID,  		$Dummy_SysInfoId, 130);

		// DB Monitoring
		CreateLink		(c_Property_DB_Fehler,				$SysDBFehlerID,  			$Dummy_DBHealthId, 10);
		CreateLink		(c_Property_lastWrite,				$SyslastWriteID,  		$Dummy_DBHealthId, 20);
		CreateLink		(c_Property_LogDB_Groesse,			$SysLogDBGroesseID,  	$Dummy_DBHealthId, 30);

		// DB Wartung
		CreateLink		(c_Property_DBNeuagg,				$SysDBNeuagg,  			$Dummy_DBWartungId, 10);
		CreateLink		(c_Property_DBVarGes,				$SysDBVarGes,  			$Dummy_DBWartungId, 20);
		CreateLink		(c_Property_DBVarReady,				$SysDBVarReady,  			$Dummy_DBWartungId, 30);
		CreateLink		(c_Property_DBaktVar,				$SysDBaktVar,  			$Dummy_DBWartungId, 40);
		CreateLink		(c_Property_DBSteps,					$SysDBSteps,  				$Dummy_DBWartungId, 50);
		CreateLink		(c_Property_DBStart,					$SysDBStart,  				$Dummy_DBWartungId, 60);
		CreateLink		(c_Property_DBReady,					$SysDBReady,  				$Dummy_DBWartungId, 70);
//		CreateLink		(c_Property_DBHistory,				$SysDBHistory,  			$Dummy_DBWartungId, 10);

		// Server Info
		CreateLink		(c_Property_ServerZeit,				$SysServerZeit,  			$Dummy_ServerId, 10);
		CreateLink		(c_Property_ServerHDD,				$SysServerHDD,  			$Dummy_ServerId, 20);
		CreateLink		(c_Property_ServerCPU,				$SysServerCPU,  			$Dummy_ServerId, 30);

		// Log Meldungen
		CreateLink     (c_Control_MeldungID,				$ControlIdLogId,			$WebFrontOverviewMLD, 20);
		CreateLink     (c_Control_Meldungen,				$ControlIdLog,				$WebFrontOverviewMLD, 30);

		// Unten
		CreateLink     (c_Control_HCQueue,					$SysHCQueue,				$WebFrontOverview4, 10);

		// Oben Links
		CreateLink		(c_Control_System,					$ModulSYSId,  				$WebFrontOverview1, 1);
		CreateLink		(c_Control_IOInterfaces,			$ModulIFSId,  				$WebFrontOverview1, 1);
//		CreateLink		(c_Property_UptimeHuman,			$SysUptimeHumanID,  		$WebFrontOverview1, 100);
//		CreateLink     (c_Control_BetriebStd,				$SysBetriebStdSID,		$WebFrontOverview1, 110);

		// Oben Mitte
		CreateLink		(c_Control_Info,						$UebersichtId,  			$WebFrontOverview2, 100);

		// Oben Rechts
		IPS_SetHidden(CreateLink		(c_Control_Version,					$ModulVersionId,  		$WebFrontOverview3, 1), true);
		IPS_SetHidden(CreateLink		(c_Control_Modul,						$ModulUpdateId,  			$WebFrontOverview3, 2), true) ;
		IPS_SetHidden(CreateLink		(c_Control_IOInterfaces,			$UebersichtIFSId, 		$WebFrontOverview3, 3), true);

		$Idx = 10;
		foreach ($configData as $Name=>$Data) {
			$CirclyId   	= get_CirclyIdi($Name, $CategoryIds);

			$ControlId 		= get_ControlId(c_Control_Uebersicht,$CirclyId);
			IPS_SetHidden(CreateLink($Data[c_CircleName],		$ControlId,	$WebFrontOverview3,	$Idx),true);

			$ControlId 		= get_ControlId(c_Control_Select,$CirclyId);
			CreateLink($Data[c_CircleName],		$ControlId,	$WebFrontOverview1,	$Idx);

			$Idx = $Idx + 10;
		}
	}

	ReloadAllWebFronts();

   // ------------------------------------------------------------------------------------------------
	function get_CirclyIdi($DeviceName, $ParentId) {
		$CategoryId = IPS_GetObjectIDByIdent($DeviceName, $ParentId);
		return $CategoryId;
	}

   // ------------------------------------------------------------------------------------------------
	function get_ControlId($ControlName, $CirclyId) {
	   $VariableId = IPS_GetObjectIDByIdent($ControlName, $CirclyId);
	   return $VariableId;
	}
	
	
	/** Anlegen eines Profils mit Associations
	 *
	 * der Befehl legt ein Profile an und erzeugt für die übergebenen Werte Assoziationen
	 *
	 * @param string $Name Name des Profiles
	 * @param string $Associations[] Array mit Wert und Namens Zuordnungen
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @param integer $Color[] Array mit Farbwerten im HTML Farbcode (z.b. 0x0000FF für Blau). Sonderfall: -1 für Transparent
	 * @param boolean $DeleteProfile Profile löschen und neu generieren
	 *
	 */
	function CreateProfile ($Name, $Associations, $Icon="", $Color=-1, $DeleteProfile=true, $Prefix="", $Suffix="", $Digits=0) {
		if ($DeleteProfile) {
			@IPS_DeleteVariableProfile($Name);
		}
		@IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
		IPS_SetVariableProfileValues($Name, 0, 0, 0);
		IPS_SetVariableProfileDigits($Name, $Digits);
		IPS_SetVariableProfileIcon($Name, $Icon);
		if ($Associations <> ""){
			foreach($Associations as $Idx => $IdxName) {
				if ($IdxName == "") {
				  // Ignore
				} elseif (is_array($Color)) {
					IPS_SetVariableProfileAssociation($Name, $Idx, $IdxName, "", $Color[$Idx]);
			   } else {
					IPS_SetVariableProfileAssociation($Name, $Idx, $IdxName, "", $Color);
				}
			}
		}
	}

	/** Definieren "Sekunden" Timers
	 *
	 * Anlegen eines Timers, der alle $Seconds Sekunden ausgeführt wird
	 *
	 * @param string $Name Name des Timers
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Seconds Intervall in Sekunden
	 * @param boolean $Active Timer aktiv setzen
	 * @return integer ID des Timers
	 *
	 */
	function CreateTimer_BySeconds ($Name, $ParentId, $Seconds, $Active=true) {
		$TimerId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($TimerId === false) $TimerId = @IPS_GetEventIDByName($Name, $ParentId);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetParent($TimerId, $ParentId);
			IPS_SetName($TimerId, $Name);
			IPS_SetIdent($TimerId, Get_IdentByName($Name));
			Debug ('Created Timer '.$Name.'='.$TimerId."");
		}
		if (!IPS_SetEventCyclic($TimerId, 2 /*Daily*/, 1 /*Int*/,0 /*Days*/,0/*DayInt*/,1/*TimeType Sec*/,$Seconds/*Sec*/)) {
			Error ("IPS_SetEventCyclic failed !!!");
		}
		IPS_SetEventCyclicTimeBounds($TimerId,mktime(0, 0, date('s')),0);
		IPS_SetEventActive($TimerId, $Active);
		return $TimerId;
	}
	/** @}*/
?>