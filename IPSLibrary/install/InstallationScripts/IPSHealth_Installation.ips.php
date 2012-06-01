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
	$ScriptIdTimer  	= IPS_GetScriptIDByName('IPSHealth_Timer'			, $CategoryIdApp);
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

 	CreateProfile ('IPSHealth_Pro', '', '', '', true, '', ' %', 0);
 	CreateProfile ('IPSHealth_Sec', '', '', '', true, '', ' Sek.', 0);
 	CreateProfile ('IPSHealth_MB', '', '', '', true, '', ' MB', 2);
 	CreateProfile ('IPSHealth_GB', '', '', '', true, '', ' GB', 2);


	$CategoryIds	= CreateCategory(c_HealthCircles, $CategoryIdData, 300);
	$CategoryIdSYS	= CreateCategory(c_Control_SysInfo, $CategoryIdData, 310);

	$Idx  = 1;
	$configData = get_HealthConfiguration();

	foreach ($configData as $Name=>$Data) {
			$ZSUId     = CreateCategory($Name, $CategoryIds, $Idx);
			$intervall = $Data[c_HealthTimeout];
			CreateTimer_BySeconds ($Name.'-Timeout', $ScriptIdTimer, $intervall, true) ;
	}

   CreateTimer_OnceADay("SysInfo-Day", $ScriptIdTimer, 0, 0); 						// Tages Timer für Datenbankgröße
	CreateTimer_BySeconds("SysInfo-Server"	, $ScriptIdTimer, 60						, true) ;     // Timer 1 min. für Server Info
	CreateTimer_BySeconds("SysInfo-DBHealth"	, $ScriptIdTimer, c_Warn_Schwellwert	, true) ;     // Timer gemäß Para für Datenbank Überwachung.

	// Übersicht
	$UebersichtId	 = CreateVariable(c_Control_Uebersicht, 3 /*String*/,  $CategoryIdData, 10, '~HTMLBox', null, '');

	// Logging
	$CategoryIdLog	 = CreateCategory('Log', $CategoryIdData, 210);
	$ControlIdLog   = CreateVariable('LogMessages',  3 /*String*/,  $CategoryIdLog, 220, '~HTMLBox', null, '');
	$ControlIdLogId = CreateVariable('LogId',        1 /*Integer*/, $CategoryIdLog, 230, '',         null, 0);

	//System Info
	// Statistik
	$SysCategoryID 	= CreateVariable(c_Property_Categorys,      1 /*Integer*/, $CategoryIdSYS, 10, '',         null, 0);
	$SysEventsID 		= CreateVariable(c_Property_Events,         1 /*Integer*/, $CategoryIdSYS, 20, '',         null, 0);
	$SysInstancesID 	= CreateVariable(c_Property_Instances,      1 /*Integer*/, $CategoryIdSYS, 30, '',         null, 0);
	$SysLinksID 		= CreateVariable(c_Property_Links,          1 /*Integer*/, $CategoryIdSYS, 40, '',         null, 0);
	$SysModulesID 		= CreateVariable(c_Property_Modules,        1 /*Integer*/, $CategoryIdSYS, 50, '',         null, 0);
	$SysObjectsID 		= CreateVariable(c_Property_Objects,        1 /*Integer*/, $CategoryIdSYS, 60, '',         null, 0);
	$SysProfilesID 	= CreateVariable(c_Property_Profiles,       1 /*Integer*/, $CategoryIdSYS, 70, '',         null, 0);
	$SysScriptsID 		= CreateVariable(c_Property_Scripts,        1 /*Integer*/, $CategoryIdSYS, 80, '',         null, 0);
	$SysVariableID 	= CreateVariable(c_Property_Variable,       1 /*Integer*/, $CategoryIdSYS, 90, '',         null, 0);
	$SysDBGroesseID 	= CreateVariable(c_Property_DB_Groesse,     2 /*Float*/,   $CategoryIdSYS, 100, 'IPSHealth_MB',        null, 0);
	$SysDBZuwachsID 	= CreateVariable(c_Property_DB_Zuwachs,     2 /*Float*/,   $CategoryIdSYS, 110, 'IPSHealth_MB',        null, 0);

	// Datenbank Health
	$SysDBFehlerID 	= CreateVariable(c_Property_DB_Fehler,      	0 /*Boolean*/, $CategoryIdSYS, 120, 'IPSHealth_Err',       null, 0);
	$SyslastWriteID 	= CreateVariable(c_Property_lastWrite,      	1 /*Integer*/, $CategoryIdSYS, 130, 'IPSHealth_Sec',       null, 0);
	$SysLogDBGroesseID = CreateVariable(c_Property_LogDB_Groesse,  2 /*Float*/,  	$CategoryIdSYS, 140, 'IPSHealth_MB',       null, 0);

	// Server Info
	$SysServerZeit 	= CreateVariable(c_Property_ServerZeit,		3 /*String*/,  $CategoryIdSYS, 150, '',       null, 0);
	$SysServerHDD 		= CreateVariable(c_Property_ServerHDD,  		2 /*Float*/,  $CategoryIdSYS, 160, 'IPSHealth_GB',        null, 0);
	$SysServerCPU 		= CreateVariable(c_Property_ServerCPU,  		1 /*Integer*/,  $CategoryIdSYS, 170, 'IPSHealth_Pro',      null, 0);

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
		$WebFrontOverviewSYS     = CreateCategory( 'SystemInfo',     $WebFrontId,    10);
		$WebFrontOverviewMLD     = CreateCategory( 'Meldungen', 		 $WebFrontId,    20);
		$WebFrontOverview			 = CreateCategory( 'Overview',  		 $WebFrontId,    30);

		DeleteWFCItems($WFC_ConfigId, $WFC_TabPaneItem);

		// Übersicht
		CreateWFCItemTabPane   ($WFC_ConfigId, $WFC_TabPaneItem,             	$WFC_TabPaneParent, 	$WFC_TabPaneOrder, 		$WFC_TabPaneName, $WFC_TabPaneIcon);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_SysInfo',		$WFC_TabPaneItem,  		  	20, 'System Info'	, ''				, $WebFrontOverviewSYS /*BaseId*/, 'false' /*BarBottomVisible*/);
//		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_OV',       	$WFC_TabPaneItem,    		10, $WFC_TabName1	, $WFC_TabIcon1, 0 /*Horizontal*/					, 40 /*Hight*/	, 	0 /*Target=Pane1*/, 0/*UsePixel*/, 'true');
//		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_Overview', $WFC_TabPaneItem.'_OV',  	10, 'Übersicht'	, ''				, $WebFrontOverview /*BaseId*/	, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_Log',  		$WFC_TabPaneItem, 			20, 'Logging'		, ''				, $WebFrontOverviewMLD /*BaseId*/, 'false' /*BarBottomVisible*/);                             // integer $PercentageSlider

		$Dummy_SysInfoId 	= CreateInstance ('IPS Statistik',	$WebFrontOverviewSYS, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 20);
		$Dummy_DBHealthId	= CreateInstance ('IPS Datenbank', 	$WebFrontOverviewSYS, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 30);
		$Dummy_ServerId 	= CreateInstance ('Server Info', 	$WebFrontOverviewSYS, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 40);
//		$Dummy_Log_Id 		= CreateInstance ('Meldungen', 	$WebFrontOverviewMLD, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 50);

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

		CreateLink		(c_Property_DB_Fehler,				$SysDBFehlerID,  			$Dummy_DBHealthId, 10);
		CreateLink		(c_Property_lastWrite,				$SyslastWriteID,  		$Dummy_DBHealthId, 20);
		CreateLink		(c_Property_LogDB_Groesse,			$SysLogDBGroesseID,  	$Dummy_DBHealthId, 30);

		CreateLink		(c_Property_ServerZeit,				$SysServerZeit,  			$Dummy_ServerId, 10);
		CreateLink		(c_Property_ServerHDD,				$SysServerHDD,  			$Dummy_ServerId, 20);
		CreateLink		(c_Property_ServerCPU,				$SysServerCPU,  			$Dummy_ServerId, 30);

		CreateLink     (c_Control_MeldungID,		$ControlIdLogId,				$WebFrontOverviewMLD, 20);
		CreateLink     (c_Control_Meldungen,		$ControlIdLog,					$WebFrontOverviewMLD, 30);


		// Übersicht
//			$Idx = 20;
//		foreach ($ZSUConfig as $ZSUName=>$ZSUData) {
//			$CirclyId   = get_ZSUCirclyId($ZSUName, $CategoryIdZSUs);

//			CreateLink($ZSUData[c_Property_Name],    	get_ZSUControlId(c_Control_Uebersicht,   	$CirclyId),		$WebFrontOverview,	$Idx);

//			$Idx = $Idx + 10;
//		}


	}

	ReloadAllWebFronts();

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
		IPS_SetEventActive($TimerId, $Active);
		return $TimerId;
	}

	/** @}*/
?>