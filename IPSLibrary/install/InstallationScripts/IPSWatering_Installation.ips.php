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

	 /**@defgroup ipswatering_visualization IPSWatering Visualisierung
	 * @ingroup ipswatering
	 * @{
	 *
	 * Visualisierungen von IPSWatering
	 *
	 * IPSWatering WebFront Visualisierung:
	 *
	 *  Übersicht über alle Bewässerungs Kreise
	 *  @image html IPSWatering_WebFrontOverview.jpg
	 *  <BR>
	 *  Detailansicht eines Bewässerungs Kreises
	 *  @image html IPSWatering_WebFrontSettings.jpg
	 *
	 *
	 * IPSWatering Mobile Visualisierung:
	 *
	 *  Übersicht über alle Bewässerungs Kreise
	 *  @image html IPSWatering_MobileOverview.png
	 *  <BR>
	 *  Detailansicht eines Bewässerungs Kreises
	 *  @image html IPSWatering_MobileSettings.png
	 *
	 *@}*/

	 /**@defgroup ipswatering_install IPSWatering Installation
	 * @ingroup ipswatering
	 * @{
	 *
	 * Script zur kompletten Installation der IPSWatering Steuerung.
	 *
	 * Vor der Installation muß das File IPSWatering_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page rquirements_ipswatering Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 * - IPSComponent >= 2.50.1
	 * - IPSMessageHandler >= 2.50.1
	 *
	 * @page install_ipswatering Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSWatering Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSWatering_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 10.03.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSWatering');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSComponent','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSMessageHandler','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",                "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSWatering_Configuration.inc.php",   "IPSLibrary::config::modules::IPSWatering");

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

	$Mobile_Enabled       = $moduleManager->GetConfigValue('Enabled', 'Mobile');
	$Mobile_Path          = $moduleManager->GetConfigValue('Path', 'Mobile');
	$Mobile_PathOrder     = $moduleManager->GetConfigValueInt('PathOrder', 'Mobile');
	$Mobile_PathIcon      = $moduleManager->GetConfigValue('PathIcon', 'Mobile');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');

	// Add Scripts
   $ScriptIdChangeSettings  = IPS_GetScriptIDByName('IPSWatering_ChangeSettings',  $CategoryIdApp);
   $ScriptIdRefreshTimer    = IPS_GetScriptIDByName('IPSWatering_RefreshTimer',    $CategoryIdApp);
   $ScriptIdActivationTimer = IPS_GetScriptIDByName('IPSWatering_ActivationTimer', $CategoryIdApp);
   $ScriptIdAutomaticOn     = IPS_GetScriptIDByName('IPSWatering_AutomaticOn',     $CategoryIdApp);
   $ScriptIdAutomaticOff    = IPS_GetScriptIDByName('IPSWatering_AutomaticOff',    $CategoryIdApp);

	// Create Circles and Controls
	// ----------------------------------------------------------------------------------------------------------------------------
	CreateProfile_Associations ('IPSWatering_Program', array(
												c_ProgramId_Manual	 	=> c_Program_Manual,
												c_ProgramId_EveryDay 	=> c_Program_EveryDay,
												c_ProgramId_Every2Day 	=> c_Program_Every2Day,
												c_ProgramId_Every3Day 	=> c_Program_Every3Day,
												c_ProgramId_MonWedFri 	=> c_Program_MonWedFri,
												c_ProgramId_MonTur 		=> c_Program_MonTur,
												c_ProgramId_Sunday 		=> c_Program_Sunday,
												c_ProgramId_Monday 		=> c_Program_Monday,
												c_ProgramId_Tuesday 	=> c_Program_Tuesday,
												c_ProgramId_Wednesday 	=> c_Program_Wednesday,
												c_ProgramId_Thursday 	=> c_Program_Thursday,
												c_ProgramId_Friday 		=> c_Program_Friday,
												c_ProgramId_Saturday 	=> c_Program_Saturday
												));
	CreateProfile_Associations ('IPSWatering_Sensor', array(
												0	=> 'Aus',
												1 	=> '1 mm',
												2 	=> '2 mm',
												3 	=> '3 mm',
												4 	=> '4 mm',
												5 	=> '5 mm'));
	CreateProfile_Duration ('IPSWatering_Duration', 5, 5, 120);
	CreateProfile_Switch ('IPSWatering_Active', 'Regner Aus', 'Regner An', '', 0x606060);

	$CategoryIdCircles	= CreateCategory('WaterCircles', $CategoryIdData, 20);
	$WaterConfig         = get_WateringConfiguration();
	$Idx                 = 10;
	foreach ($WaterConfig as $CircleName=>$CircleData) {
		$CircleId              = CreateCategory($CircleName, $CategoryIdCircles, $Idx);
		$ControlIdActive       = CreateVariable(c_Control_Active,       0 /*Boolean*/, $CircleId,  10, 'IPSWatering_Active',   $ScriptIdChangeSettings, false, 'Drops');
		$ControlIdAutomatic    = CreateVariable(c_Control_Automatic,    0 /*Boolean*/, $CircleId,  20, '~Switch',              $ScriptIdChangeSettings, false, 'Power');
		$ControlIdStartTime    = CreateVariable(c_Control_StartTime,    3 /*String*/,  $CircleId,  30, '~String',              $ScriptIdChangeSettings, '07:00', 'Clock');
		$ControlIdDuration     = CreateVariable(c_Control_Duration,     1 /*Integer*/, $CircleId,  40, 'IPSWatering_Duration', $ScriptIdChangeSettings, 45, 'Intensity');
		$ControlIdProgram      = CreateVariable(c_Control_Program,      1 /*Integer*/, $CircleId,  50, 'IPSWatering_Program',  $ScriptIdChangeSettings, c_ProgramId_EveryDay, 'Calendar');
		$ControlIdSensor       = CreateVariable(c_Control_Sensor,       1 /*Integer*/, $CircleId,  60, 'IPSWatering_Sensor',   $ScriptIdChangeSettings, 0, 'Rainfall');
		$ControlIdLastDate     = CreateVariable(c_Control_LastDate,     3 /*String*/,  $CircleId, 100, '~String',              null, date(c_Format_LastDate));
		$ControlIdLastTime     = CreateVariable(c_Control_LastTime,     3 /*String*/,  $CircleId, 110, '~String',              null, date(c_Format_LastTime));
		$ControlIdNextDate     = CreateVariable(c_Control_NextDate,     3 /*String*/,  $CircleId, 120, '~String',              null, date(c_Format_NextDate));
		$ControlIdNextTime     = CreateVariable(c_Control_NextTime,     3 /*String*/,  $CircleId, 130, '~String',              null, date(c_Format_NextTime));
		$ControlIdNextDisplay  = CreateVariable(c_Control_NextDisplay,  3 /*String*/,  $CircleId, 140, '~String',              null, '');
		$ControlIdToBeDone     = CreateVariable(c_Control_ToBeDone,     3 /*String*/,  $CircleId, 150, '~String',              null, 'Automatik Aus');
		$Idx = $Idx  + 10;
	}
	// Logging
	$CategoryIdLog	 = CreateCategory('Log', $CategoryIdData, 30);
	$ControlIdLog   = CreateVariable('LogMessages',  3 /*String*/,  $CategoryIdLog, 40, '~HTMLBox', null, '');
	$ControlIdLogId = CreateVariable('LogId',        1 /*Integer*/, $CategoryIdLog, 50, '',         null, 0);

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$WebFrontId               = CreateCategoryPath($WFC10_Path, 10);
		EmptyCategory($WebFrontId);
		$WebFrontOverviewId       = CreateCategory(    'Overview', $WebFrontId,    0);
		$WebFrontOverviewTop1     = CreateCategory(    'Top_1',    $WebFrontOverviewId,    10);
		$WebFrontOverviewTop2     = CreateCategory(    'Top_2',    $WebFrontOverviewId,    20);
		$WebFrontOverviewBottom1  = CreateCategory(    'Bottom_1', $WebFrontOverviewId,    30);
		$WebFrontOverviewBottom2  = CreateCategory(    'Bottom_2', $WebFrontOverviewId,    40);

		DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem);
		$UId = date('Hi');
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,             $WFC10_TabPaneParent,           $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OV',       $WFC10_TabPaneItem.'',          10, $WFC10_TabName1, $WFC10_TabIcon1, 0 /*Horizontal*/, 300 /*Hight*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTop',    $WFC10_TabPaneItem.'_OV',       10, '', '', 1 /*Vertical*/, 430 /*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVBottom', $WFC10_TabPaneItem.'_OV',       20, '', '', 1 /*Vertical*/, 430 /*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTop1'.$UId,   $WFC10_TabPaneItem.'_OVTop',    10, 'Column_1', '', $WebFrontOverviewTop1 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTop2'.$UId,   $WFC10_TabPaneItem.'_OVTop',    10, 'Column_2', '', $WebFrontOverviewTop2 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVBottom1'.$UId,$WFC10_TabPaneItem.'_OVBottom', 20, 'Column_1', '', $WebFrontOverviewBottom1 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVBottom2'.$UId,$WFC10_TabPaneItem.'_OVBottom', 20, 'Column_2', '', $WebFrontOverviewBottom2 /*BaseId*/, 'false' /*BarBottomVisible*/);

		// Webfront Overview
		$Idx = 10;
		foreach ($WaterConfig as $CircleId=>$CircleData) {
			$CirclyId   = get_WateringCirclyId($CircleId, $CategoryIdCircles);
			$CircleName = $CircleData[c_Property_Name];
		   // Overview Data
			CreateLink              ($CircleName, get_WateringControlId(c_Control_Active,   $CirclyId),  $WebFrontOverviewTop1, $Idx);
			CreateLinkByDestination ('Status',    get_WateringControlId(c_Control_ToBeDone, $CirclyId),  $WebFrontOverviewTop2, $Idx);

			// Detailed CirclyData
			$WebFrontDetailId  = CreateCategory($CircleId, $WebFrontId, 100+$Idx);
			CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_'.$Idx.$UId,$WFC10_TabPaneItem.'', 100+$Idx, $CircleName, '', $WebFrontDetailId /*BaseId*/, 'false' /*BarBottomVisible*/);
			CreateLink('Status',             get_WateringControlId(c_Control_Active,     $CirclyId),  $WebFrontDetailId, 10);
			CreateLink('Automatik',          get_WateringControlId(c_Control_Automatic,  $CirclyId),  $WebFrontDetailId, 20);
			CreateLink('Programm',           get_WateringControlId(c_Control_Program,    $CirclyId),  $WebFrontDetailId, 30);
			CreateLink('Regen Sensor',       get_WateringControlId(c_Control_Sensor,     $CirclyId),  $WebFrontDetailId, 35);
			CreateLink('Beregnungs Dauer',   get_WateringControlId(c_Control_Duration,   $CirclyId),  $WebFrontDetailId, 40);
			CreateLink('Start Zeit',         get_WateringControlId(c_Control_StartTime,  $CirclyId),  $WebFrontDetailId, 50);

			$Idx = $Idx + 10;
		}
		// Bottom Left
		CreateLink('Meldungen', $ControlIdLog,  $WebFrontOverviewBottom1, 10);

		// Bottom Right
		CreateLink('Automatic Ein', $ScriptIdAutomaticOn,  $WebFrontOverviewBottom2, 10);
		CreateLink('Automatic Aus', $ScriptIdAutomaticOff, $WebFrontOverviewBottom2, 20);

		ReloadAllWebFronts();
	}
	
	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled) {
		$mobileId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		$Idx               = 10;
		foreach ($WaterConfig as $CircleId=>$CircleData) {
			$CirclyId   = get_WateringCirclyId($CircleId, $CategoryIdCircles);
			$CircleName = $CircleData[c_Property_Name];

		   // iPhone Overview
			CreateLink($CircleName,  get_WateringControlId(c_Control_Active,     $CirclyId),  $mobileId,      $Idx);

			// Detailed CirclyData
			$WebFrontiPhoneDetailId  = CreateCategory($CircleName, $mobileId, $Idx*10);
			$iPhoneControl = CreateDummyInstance("Steuerung", $WebFrontiPhoneDetailId, 10);
			CreateLink('Status',             get_WateringControlId(c_Control_Active,     $CirclyId),  $iPhoneControl, 10);
			CreateLink('Automatik',          get_WateringControlId(c_Control_Automatic,  $CirclyId),  $iPhoneControl, 20);

			$iPhoneProgram = CreateDummyInstance("Programm", $WebFrontiPhoneDetailId, 20);
			CreateLink('Programm',           get_WateringControlId(c_Control_Program,    $CirclyId),  $iPhoneProgram, 30);
			CreateLink('Sensor',             get_WateringControlId(c_Control_Sensor,     $CirclyId),  $iPhoneProgram, 35);
			CreateLink('Beregnungs Dauer',   get_WateringControlId(c_Control_Duration,   $CirclyId),  $iPhoneProgram, 40);
			CreateLink('Start Zeit',         get_WateringControlId(c_Control_StartTime,  $CirclyId),  $iPhoneProgram, 50);

			$iPhoneInfos = CreateDummyInstance("Infos", $WebFrontiPhoneDetailId, 30);
			CreateLink('Nächste Dauer',      get_WateringControlId(c_Control_ToBeDone,   $CirclyId),  $iPhoneInfos, 60);
			CreateLink('Nächste Beregnung',  get_WateringControlId(c_Control_NextDisplay,$CirclyId),  $iPhoneInfos, 70);
			CreateLink('Nächstes Datum',     get_WateringControlId(c_Control_NextDate,   $CirclyId),  $iPhoneInfos, 80);
			CreateLink('Letztes Datum',      get_WateringControlId(c_Control_LastDate,   $CirclyId),  $iPhoneInfos, 90);
			CreateLink('Letzte Zeit',        get_WateringControlId(c_Control_LastTime,   $CirclyId),  $iPhoneInfos, 100);

			$Idx = $Idx + 10;
		}
	}

	// ------------------------------------------------------------------------------------------------
	function get_WateringCirclyId($DeviceName, $ParentId) {
		$CategoryId = IPS_GetObjectIDByIdent($DeviceName, $ParentId);
		return $CategoryId;
	}

   // ------------------------------------------------------------------------------------------------
	function get_WateringControlId($ControlName, $CirclyId) {
	   $VariableId = IPS_GetObjectIDByIdent($ControlName, $CirclyId);
	   return $VariableId;
	}

   // ------------------------------------------------------------------------------------------------
	function CreateProfile_Duration ($Name, $Start, $Step, $Stop, $Prefix=" Min", $Icon="") {
	   @IPS_DeleteVariableProfile($Name);
		IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, "", $Prefix);
		IPS_SetVariableProfileValues($Name, $Start, $Stop, $Step);
		IPS_SetVariableProfileDigits($Name, 0);
		IPS_SetVariableProfileIcon($Name, $Icon);
	}

	/** @}*/
?>