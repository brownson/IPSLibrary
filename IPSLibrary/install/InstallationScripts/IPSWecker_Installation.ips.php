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

	 /**@defgroup IPSWecker_visualization IPSWecker Visualisierung
	 * @ingroup IPSWecker
	 * @{
	 *
	 * Visualisierungen von IPSWecker
	 *
	 * IPSWecker WebFront Visualisierung:
	 *
	 *  Übersicht über aller Wecker
	 *  @image html IPSWecker_WebFrontOverview.jpg
	 *  <BR>
	 *  Detailansicht einer Wecker Konfiguration
	 *  @image html IPSWecker_WebFrontSettings.jpg
	 *
	 *
	 * IPSWecker Mobile Visualisierung:
	 *
	 *  Übersicht über aller Wecker
	 *  @image html IPSWecker_MobileOverview.png
	 *  <BR>
	 *  Detailansicht einer Wecker Konfiguration
	 *  @image html IPSWecker_MobileSettings.png
	 *
	 *@}*/

	 /**@defgroup IPSWecker_install IPSWecker Installation
	 * @ingroup IPSWecker
	 * @{
	 *
	 * Script zur kompletten Installation der IPSWecker Steuerung.
	 *
	 * Vor der Installation muß das File IPSWecker_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * In dem File IPSWecker_Custom.inc.php werden die Wekcer Aktionen parametriert
	 *
	 * In dem File IPSWecker.ini werden die Webfront Konfigurationen angepasst.
	 *
	 * @page rquirements_IPSWecker Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 *
	 * @page install_IPSWecker Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSWecker Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSWecker_Installation.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('IPSWecker');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');
//	$moduleManager->VersionHandler()->CheckModuleVersion('IPSComponent','2.50.1');
//	$moduleManager->VersionHandler()->CheckModuleVersion('IPSMessageHandler','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",                "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSWecker_Configuration.inc.php",   "IPSLibrary::config::modules::IPSWecker");

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

	$WFC10_Enabled        = $moduleManager->GetConfigValue('Enabled', 'WFC10');
	$WFC10_ConfigId       = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', '');
	$WFC10_Path           = $moduleManager->GetConfigValue('Path', 'WFC10');
	$WFC10_TabPaneItem    = $moduleManager->GetConfigValue('TabPaneItem', 'WFC10');
	$WFC10_TabPaneParent  = $moduleManager->GetConfigValue('TabPaneParent', 'WFC10');
	$WFC10_TabPaneName    = $moduleManager->GetConfigValue('TabPaneName', 'WFC10');
	$WFC10_TabPaneIcon    = $moduleManager->GetConfigValue('TabPaneIcon', 'WFC10');
	$WFC10_TabPaneOrder   = $moduleManager->GetConfigValueInt('TabPaneOrder', 'WFC10');
	$WFC10_TabName1       = $moduleManager->GetConfigValue('TabName1', 'WFC10');
	$WFC10_TabIcon1       = $moduleManager->GetConfigValue('TabIcon1', 'WFC10');

	$Touch_Enabled        = $moduleManager->GetConfigValue('Enabled', 'Touch');
	$Touch_ConfigId       = $moduleManager->GetConfigValueIntDef('ID', 'Touch', '');
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

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');

	// Add Scripts
   $ScriptIdChangeSettings  = IPS_GetScriptIDByName('IPSWecker_ChangeSettings',  $CategoryIdApp);
   $ScriptIdTimer   			 = IPS_GetScriptIDByName('IPSWecker_Timer',    			$CategoryIdApp);
   $ScriptIdEvent   			 = IPS_GetScriptIDByName('IPSWecker_Event',    			$CategoryIdApp);

	// Create Weckers and Controls
	// ----------------------------------------------------------------------------------------------------------------------------
//	CreateProfile_Associations ('IPSWecker_Program', array(
//												c_ProgramId_Manual	 	=> c_Program_Manual,
//												c_ProgramId_EveryTag 	=> c_Program_EveryTag,
//												c_ProgramId_Every2Tag 	=> c_Program_Every2Tag,
//												c_ProgramId_Every3Tag 	=> c_Program_Every3Tag,
//												c_ProgramId_MonWedFri 	=> c_Program_MonWedFri,
//												c_ProgramId_MonTur 		=> c_Program_MonTur,
//												c_ProgramId_SunTag 		=> c_Program_SunTag));


	CreateProfile_Associations ('IPSWecker_LTag', array(
												0	=> c_Program_Montag,
												1 	=> c_Program_Dienstag,
												2 	=> c_Program_Mittwoch,
												3 	=> c_Program_Donnerstag,
												4 	=> c_Program_Freitag,
												5 	=> c_Program_Samstag,
												6 	=> c_Program_Sonntag,
												7 	=> c_Program_Werktags,
												8 	=> c_Program_Wochenende,
												9 	=> c_Program_Woche),
												'', array(
												0  =>	0x800000,
												1  =>	0x800000,
												2  =>	0x800000,
												3  =>	0x800000,
												4  =>	0x800000,
												5 	=>	0x008000,
												6 	=>	0x008000,
												7 	=>	-1,
												8 	=>	-1,
												9 	=>	-1));

	CreateProfile_Associations ('IPSWecker_LStunde', array(
												0	=> '00',
												1 	=> '01',
												2 	=> '02',
												3 	=> '03',
												4 	=> '04',
												5 	=> '05',
												6 	=> '06',
												7 	=> '07',
												8 	=> '08',
												9 	=> '09',
												10 	=> '10',
												11 	=> '11',
												12 	=> '12',
												13 	=> '13',
												14 	=> '14',
												15 	=> '15',
												16 	=> '16',
												17 	=> '17',
												18 	=> '18',
												19 	=> '19',
												20 	=> '20',
												21 	=> '21',
												22 	=> '22',
												23 	=> '23')
												);

	CreateProfile_Associations ('IPSWecker_LMinute', array(
												0	=> '00',
												1 	=> '05',
												2 	=> '10',
												3 	=> '15',
												4 	=> '20',
												5 	=> '25',
												6 	=> '30',
												7 	=> '35',
												8 	=> '40',
												9 	=> '45',
												10 => '50',
												11 => '55'));


	CreateProfile_Associations ('IPSWecker_Name', array(
												0	=> '00'));

	CreateProfile_Associations ('IPSWecker_Tag', array(
												-1		=> '-',
												0 		=> c_Program_Montag,
												100 	=> '+'),'', array(
												-1  	=>	0x00F0F0,
												0  	=>	-1,
												100 	=>	0x00F0F0));

 	CreateProfile_Associations ('IPSWecker_Stunde', array(
// 	                                 -2 	=> 'AUS',
												-1		=> '-',
												0		=> '%d',
												100	=> '+'),'', array(
//												-2 	=>	0x800000,
												-1  	=>	0x00F0F0,
												0  	=>	-1,
												100 	=>	0x00F0F0));

 	CreateProfile_Associations ('IPSWecker_Minute', array(
												-1	=> '-',
												0	=> '%d',
												100	=> '+'),'', array(
												-1  	=>	0x00F0F0,
												0  	=>	-1,
												100 	=>	0x00F0F0));

	CreateProfile_Associations ('IPSWecker_Global', array(
												0	=> c_Program_Off,
												1 	=> c_Program_On),'', array(
												0  =>	0x800000,
												1 	=>	0x008000));

	CreateProfile_Associations ('IPSWecker_Aktiv', array(
												0	=> c_Program_NoWeck,
												1 	=> c_Program_Weck),'', array(
												0  =>	0x800000,
												1 	=>	0x008000));

	CreateProfile_Associations ('IPSWecker_Frost', array(
												0	=> c_Program_NormWeck,
												1 	=> c_Program_PrevWeck),'', array(
												0  =>	0x800000,
												1 	=>	0x008000));

	CreateProfile_Associations ('IPSWecker_Schlummer', array(
												0	=> c_Program_Off,
												1 	=> c_Program_On),'', array(
												0  =>	0x800000,
												1 	=>	0x008000));

	CreateProfile_Associations ('IPSWecker_End', array(
												0	=> c_Program_Off,
												1 	=> c_Program_On),'', array(
												0  =>	0x800000,
												1 	=>	0x008000));

// Timer Event erstellen
	CreateTimer_Profile ('Timer_Event', $ScriptIdTimer, 0, 0, 0, false);

	$CategoryIdWeckers	= CreateCategory(c_WeckerCircles, $CategoryIdData, 300);
	$WeckerConfig        = get_WeckerConfiguration();
	$Ass                 = 0;
	$Idx                 = 100;
	$vpn 						= 'IPSWecker_Name';

  	SetVariableConstant ("WECKER_ID_WECKZEITEN",    $CategoryIdWeckers,   	'IPSWecker_IDs.inc.php', 'IPSLibrary::app::modules::IPSWecker');
  	SetVariableConstant ("WECKER_ID_TIMER",    		$ScriptIdTimer,   		'IPSWecker_IDs.inc.php', 'IPSLibrary::app::modules::IPSWecker');

// Weckzeiten als Association anlegen
   if(IPS_VariableProfileExists($vpn)){
        IPS_DeleteVariableProfile($vpn);
	}
	IPS_CreateVariableProfile($vpn, 1);
   IPS_SetVariableProfileValues($vpn, 0, 10, 0);

	foreach ($WeckerConfig as $WeckerName=>$WeckerData) {
    	 	IPS_SetVariableProfileAssociation($vpn, $Ass, $WeckerData[c_Property_Name],"", -1);

			$WeckerId              	= CreateCategory($WeckerName, $CategoryIdWeckers, $Idx);
			$ControlIdMontag			= CreateVariable(c_Control_Mo,			3 /*String*/,  $WeckerId, 10, '~String',   null, '00:00');
			$ControlIdDienstag		= CreateVariable(c_Control_Di,			3 /*String*/,  $WeckerId, 20, '~String',   null, '00:00');
			$ControlIdMittwoch		= CreateVariable(c_Control_Mi,			3 /*String*/,  $WeckerId, 30, '~String',   null, '00:00');
			$ControlIdDonnerstag		= CreateVariable(c_Control_Do,			3 /*String*/,  $WeckerId, 40, '~String',   null, '00:00');
			$ControlIdFreitag			= CreateVariable(c_Control_Fr,			3 /*String*/,  $WeckerId, 50, '~String',   null, '00:00');
			$ControlIdSamstag			= CreateVariable(c_Control_Sa,			3 /*String*/,  $WeckerId, 60, '~String',   null, '00:00');
			$ControlIdSonntag			= CreateVariable(c_Control_So,			3 /*String*/,  $WeckerId, 70, '~String',   null, '00:00');

//			$ControlIdMoActive    	= CreateVariable(c_Control_Mo_Active,   0 /*Boolean*/, $WeckerId, 100, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, true);
//			$ControlIdDiActive    	= CreateVariable(c_Control_Di_Active,   0 /*Boolean*/, $WeckerId, 110, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, true);
//			$ControlIdMiActive    	= CreateVariable(c_Control_Mi_Active,   0 /*Boolean*/, $WeckerId, 120, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, true);
//			$ControlIdDoActive    	= CreateVariable(c_Control_Do_Active,   0 /*Boolean*/, $WeckerId, 130, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, true);
//			$ControlIdFrActive    	= CreateVariable(c_Control_Fr_Active,   0 /*Boolean*/, $WeckerId, 140, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, true);
//			$ControlIdSaActive    	= CreateVariable(c_Control_Sa_Active,   0 /*Boolean*/, $WeckerId, 150, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, true);
//			$ControlIdSoActive    	= CreateVariable(c_Control_So_Active,   0 /*Boolean*/, $WeckerId, 160, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, true);

			$ControlIdActive			= CreateVariable(c_Control_Optionen,			3 /*String*/,  $WeckerId, 200, '~String',   	null, '1,1,1,1,1,1,1,0,0,0,1,0,0,0,0,0,0,0,0,0');
//			$ControlIdGlobal		   = CreateVariable(c_Control_Global,			  	0 /*Boolean*/, $WeckerId, 210, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, true );
//			$ControlIdFeiertag    	= CreateVariable(c_Control_Feiertag,   		0 /*Boolean*/, $WeckerId, 220, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, false);
//			$ControlIdFrost		   = CreateVariable(c_WFC_Frost,			  	0 /*Boolean*/, $WeckerId, 230, 'IPSWecker_Frost',   $ScriptIdChangeSettings, false );
//			$ControlIdUrlaub  	  	= CreateVariable(c_Control_Urlaub, 				0 /*Boolean*/, $WeckerId, 240, 'IPSWecker_Aktiv',   $ScriptIdChangeSettings, false);
			$ControlIdUrlaubszeit	= CreateVariable(c_Control_Urlaubszeit,		3 /*String*/,  $WeckerId, 250, '~TextBox',  	null, '');
			$ControlIdUebersicht		= CreateVariable(c_Control_Uebersicht,			3 /*String*/,  $WeckerId, 260, '~HTMLBox',   null, '');

			CreateTimer_Profile ($WeckerName."_0", $ScriptIdTimer, 0, 0, $Idx+1, false);
			CreateTimer_Profile ($WeckerName."_1", $ScriptIdTimer, 0, 0, $Idx+2, false);
			CreateTimer_Profile ($WeckerName."_2", $ScriptIdTimer, 0, 0, $Idx+3, false);
			CreateTimer_Profile ($WeckerName."_3", $ScriptIdTimer, 0, 0, $Idx+4, false);
			CreateTimer_Profile ($WeckerName."_4", $ScriptIdTimer, 0, 0, $Idx+5, false);
			CreateTimer_Profile ($WeckerName."_5", $ScriptIdTimer, 0, 0, $Idx+6, false);
			CreateTimer_Profile ($WeckerName."_6", $ScriptIdTimer, 0, 0, $Idx+7, false);

			if ($WeckerData[c_Property_StopSensor] <> ''){
				if (IPS_VariableExists($WeckerData[c_Property_StopSensor]) == true ){
					CreateEvent ($WeckerName, $WeckerData[c_Property_StopSensor], $ScriptIdEvent, 0);
				}
			}

			$Idx = $Idx  + 10;
			$Ass++ ;
	}
	// Logging
	$CategoryIdLog	 = CreateCategory('Log', $CategoryIdData, 210);
	$ControlIdLog   = CreateVariable('LogMessages',  3 /*String*/,  $CategoryIdLog, 220, '~HTMLBox', null, '');
	$ControlIdLogId = CreateVariable('LogId',        1 /*Integer*/, $CategoryIdLog, 230, '',         null, 0);

	$ControlIdOVWeckerName     = CreateVariable(c_Control_Name,   		1 /*Integer*/, $CategoryIdData,  10, 'IPSWecker_Name', 		$ScriptIdChangeSettings, 0, '');
	$ControlIdOVWeckerTag     	= CreateVariable(c_Control_Tag,			1 /*Integer*/, $CategoryIdData,  20, 'IPSWecker_Tag', 		$ScriptIdChangeSettings, 0, '');
	$ControlIdOVWeckerLTag    	= CreateVariable(c_Control_LTag,			1 /*Integer*/, $CategoryIdData,  20, 'IPSWecker_LTag', 		$ScriptIdChangeSettings, 0, '');
	$ControlIdOVWeckerStunde   = CreateVariable(c_Control_Stunde,  	1 /*Integer*/, $CategoryIdData,  30, 'IPSWecker_Stunde', 	$ScriptIdChangeSettings, 0, '');
	$ControlIdOVWeckerLStunde  = CreateVariable(c_Control_LStunde,  	1 /*Integer*/, $CategoryIdData,  30, 'IPSWecker_LStunde', 	$ScriptIdChangeSettings, 0, '');
	$ControlIdOVWeckerMinute   = CreateVariable(c_Control_Minute,  	1 /*Integer*/, $CategoryIdData,  40, 'IPSWecker_Minute', 	$ScriptIdChangeSettings, 0, '');
	$ControlIdOVWeckerLMinute  = CreateVariable(c_Control_LMinute,  	1 /*Integer*/, $CategoryIdData,  40, 'IPSWecker_LMinute', 	$ScriptIdChangeSettings, 0, '');

	$ControlIdOVActive			= CreateVariable(c_Control_Active,		0 /*Boolean*/, $CategoryIdData,  50, 'IPSWecker_Aktiv',  	$ScriptIdChangeSettings, true);
	$ControlIdOVGlobal		   = CreateVariable(c_Control_Global,		0 /*Boolean*/, $CategoryIdData,  60, 'IPSWecker_Global',  	$ScriptIdChangeSettings, true );
	$ControlIdOVFeiertag    	= CreateVariable(c_Control_Feiertag,   0 /*Boolean*/, $CategoryIdData,  70, 'IPSWecker_Aktiv',  	$ScriptIdChangeSettings, false);
	$ControlIdOVFrost		    	= CreateVariable(c_Control_Frost,	   0 /*Boolean*/, $CategoryIdData,  80, 'IPSWecker_Frost',  	$ScriptIdChangeSettings, false);
	$ControlIdOVSchlummer    	= CreateVariable(c_Control_Schlummer,  0 /*Boolean*/, $CategoryIdData,  80, 'IPSWecker_Schlummer', $ScriptIdChangeSettings, false);
	$ControlIdOVEnd		    	= CreateVariable(c_Control_End,		   0 /*Boolean*/, $CategoryIdData,  80, 'IPSWecker_End',  		$ScriptIdChangeSettings, false);
	$ControlIdOVUrlaub  	  		= CreateVariable(c_Control_Urlaub, 		0 /*Boolean*/, $CategoryIdData,  90, 'IPSWecker_Aktiv',  	$ScriptIdChangeSettings, false);
	$ControlIdOVUrlaubszeit		= CreateVariable(c_Control_Urlaubszeit,3 /*String*/,  $CategoryIdData, 100, '~TextBox',   			$ScriptIdChangeSettings, '');
	$ControlIdOVUebersicht		= CreateVariable(c_Control_Uebersicht,	3 /*String*/,  $CategoryIdData, 200, '~HTMLBox',   null,	'');

	CreateEvent (c_Control_LTag, 		$ControlIdOVWeckerLTag, 	$ScriptIdEvent, 0);
	CreateEvent (c_Control_LStunde, 	$ControlIdOVWeckerLStunde, $ScriptIdEvent, 0);
	CreateEvent (c_Control_LMinute, 	$ControlIdOVWeckerLMinute, $ScriptIdEvent, 0);


	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront > 19" Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC_Enabled and $WFC_ConfigId <> '') {
		$WebFrontId               = CreateCategoryPath($WFC_Path, 10);
		EmptyCategory($WebFrontId);
		$WebFrontOverviewId       = CreateCategory(    'Overview', 	$WebFrontId,    0);
		$WebFrontOverviewTopL     = CreateCategory(    'Top_L',    	$WebFrontOverviewId,    10);
		$WebFrontOverviewTopR     = CreateCategory(    'Top_R',    	$WebFrontOverviewId,    20);
		$WebFrontOverviewBottomL  = CreateCategory(    'Bottom_L',  $WebFrontOverviewId,    30);
		$WebFrontOverviewBottomR  = CreateCategory(    'Bottom_R',  $WebFrontOverviewId,    40);

		DeleteWFCItems($WFC_ConfigId, $WFC_TabPaneItem);

		// Übersicht
//		CreateWFCItemTabPane   ($WFC_ConfigId, $WFC_TabPaneItem,             	$WFC_TabPaneParent,           $WFC_TabPaneOrder, 	$WFC_TabPaneName, $WFC_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_OV',       	$WFC_TabPaneParent,          	$WFC_TabPaneOrder, 						'', 	$WFC_TabIcon1, 	0 /*Vertical*/, 50 /*Hight*/, 	0 /*Target=Pane1*/, 0/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_OVTop',    	$WFC_TabPaneItem.'_OV',       10, 						'',			 		'', 					1 /*Vertical*/, 	50 /*Width*/, 	1 /*Target=Pane2*/, 0/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_OVBottom', 	$WFC_TabPaneItem.'_OV',       20, 						'', 					'', 					1 /*Vertical*/, 	50 /*Width*/,  1 /*Target=Pane2*/, 0/*UsePixel*/, 'true');

		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OVTopL',   	$WFC_TabPaneItem.'_OVTop',    10, 'Top_1', 		'', $WebFrontOverviewTopL /*BaseId*/,		'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OVTopR',  		$WFC_TabPaneItem.'_OVTop', 	20, 'Top_1', 		'', $WebFrontOverviewTopR /*BaseId*/, 		'false' /*BarBottomVisible*/);                             // integer $PercentageSlider
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OVBottomL',   $WFC_TabPaneItem.'_OVBottom', 20, 'Bottom_1', 	'', $WebFrontOverviewBottomL /*BaseId*/, 	'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OVBottomR', 	$WFC_TabPaneItem.'_OVBottom', 20, 'Bottom_2', 	'', $WebFrontOverviewBottomR /*BaseId*/, 	'false' /*BarBottomVisible*/);

		// Top Left
		CreateLink     (c_WFC_Tag,					$ControlIdOVWeckerLTag,  		$WebFrontOverviewTopL, 10);
		CreateLink     (c_WFC_Stunde,				$ControlIdOVWeckerLStunde, 	$WebFrontOverviewTopL, 20);
		CreateLink     (c_WFC_Minute,				$ControlIdOVWeckerLMinute,		$WebFrontOverviewTopL, 30);
		CreateLink     (c_WFC_Active,				$ControlIdOVActive,				$WebFrontOverviewTopL, 40);

		// Top Right
		CreateLink		(c_WFC_AlarmName,			$ControlIdOVWeckerName,  		$WebFrontOverviewTopR, 10);
		CreateLink     (c_WFC_Global,				$ControlIdOVGlobal,				$WebFrontOverviewTopR, 20);
		CreateLink     (c_WFC_Urlaub,			$ControlIdOVUrlaub,				$WebFrontOverviewTopR, 30);
		CreateLink     (c_WFC_Feiertag,				$ControlIdOVFeiertag,			$WebFrontOverviewTopR, 40);
		CreateLink     (c_WFC_Frost,				$ControlIdOVFrost,				$WebFrontOverviewTopR, 50);
		CreateLink     (c_WFC_Snooze,				$ControlIdOVSchlummer,			$WebFrontOverviewTopR, 60);
		CreateLink     (c_WFC_End,					$ControlIdOVEnd,					$WebFrontOverviewTopR, 70);
		CreateLink     (c_WFC_Urlaubszeit,		$ControlIdOVUrlaubszeit,		$WebFrontOverviewTopR, 80);

		// BottomL
		CreateLink     (c_WFC_Uebersicht,		$ControlIdOVUebersicht,				$WebFrontOverviewBottomL, 10);

		// BottomR
		CreateLink     (c_Control_MeldungID,		$ControlIdLogId,				$WebFrontOverviewBottomR, 10);
		CreateLink     (c_Control_Meldungen,		$ControlIdLog,					$WebFrontOverviewBottomR, 20);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront 10" Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled and $WFC10_ConfigId <> '') {
		$Web10FrontId               = CreateCategoryPath($WFC10_Path, 10);
		EmptyCategory($Web10FrontId);
		$Web10FrontOverviewId       = CreateCategory(    'Overview', $Web10FrontId,    0);
		$Web10FrontOverviewTopL     = CreateCategory(    'Top_L',    $Web10FrontOverviewId,    10);
		$Web10FrontOverviewTopR     = CreateCategory(    'Top_R',    $Web10FrontOverviewId,    20);
		$Web10FrontOverviewBottomL  = CreateCategory(    'Bottom_L',  $Web10FrontOverviewId,    30);
		$Web10FrontOverviewBottomR  = CreateCategory(    'Bottom_R',  $Web10FrontOverviewId,    40);
		$Web10FrontOverviewLog		  = CreateCategory(    'LOG',  		$Web10FrontOverviewId,    50);

		DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem);

		// Übersicht
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,             	$WFC10_TabPaneParent, $WFC10_TabPaneOrder, 	$WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OV',       	$WFC10_TabPaneItem.'',          10, $WFC10_TabName1, 	$WFC10_TabIcon1, 	1 /*Vertical*/, 50 /*Hight*/, 	0 /*Target=Pane1*/, 0/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTop',    	$WFC10_TabPaneItem.'_OV',       20, '',			 		'', 					0 /*Vertical*/, 	50 /*Width*/, 	1 /*Target=Pane2*/, 0/*UsePixel*/, 'true');

		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTopL',   	$WFC10_TabPaneItem.'_OVTop',    	10, 'Column_1', '', $Web10FrontOverviewTopL /*BaseId*/,		'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVBottomL',  $WFC10_TabPaneItem.'_OVTop', 		20, 'Bottom_1', '', $Web10FrontOverviewBottomL /*BaseId*/, 'false' /*BarBottomVisible*/);                             // integer $PercentageSlider
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTopR',   	$WFC10_TabPaneItem.'_OV',  		10, 'Column_2', '', $Web10FrontOverviewTopR /*BaseId*/, 	'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_LOG',		 	$WFC10_TabPaneItem.'',  		  2000, 'LOG'	  , '', $Web10FrontOverviewLog /*BaseId*/, 'false' /*BarBottomVisible*/);

		// Top Left
		CreateLink     (c_WFC_Tag,				$ControlIdOVWeckerTag,  	$Web10FrontOverviewTopL, 10);
		CreateLink     (c_WFC_Stunde,			$ControlIdOVWeckerStunde, 	$Web10FrontOverviewTopL, 20);
		CreateLink     (c_WFC_Minute,			$ControlIdOVWeckerMinute,	$Web10FrontOverviewTopL, 30);
		CreateLink     (c_WFC_Active,			$ControlIdOVActive,			$Web10FrontOverviewTopL, 40);

		// Top Right
		CreateLink		(c_WFC_AlarmName,		$ControlIdOVWeckerName,  	$Web10FrontOverviewTopR, 10);
		CreateLink     (c_WFC_Global,			$ControlIdOVGlobal,			$Web10FrontOverviewTopR, 20);
		CreateLink     (c_WFC_Urlaub,		$ControlIdOVUrlaub,			$Web10FrontOverviewTopR, 30);
		CreateLink     (c_WFC_Feiertag,			$ControlIdOVFeiertag,		$Web10FrontOverviewTopR, 40);
		CreateLink     (c_WFC_Frost,			$ControlIdOVFrost,			$Web10FrontOverviewTopR, 50);
		CreateLink     (c_WFC_Snooze,			$ControlIdOVSchlummer,		$Web10FrontOverviewTopR, 60);
		CreateLink     (c_WFC_End,				$ControlIdOVEnd,				$Web10FrontOverviewTopR, 70);
//		CreateLink     (c_Control_Meldungen,		$ControlIdLog,					$Web10FrontOverviewTopR, 30);


		// BottomL
		CreateLink     (c_WFC_Urlaubszeit,		$ControlIdOVUrlaubszeit,	$Web10FrontOverviewBottomL, 40);

		// BottomR
//		CreateLink     (c_WFC_Uebersicht,		$ControlIdOVUebersicht,		$Web10FrontOverviewBottomL, 10);

		// LOG
		CreateLink     (c_Control_MeldungID,		$ControlIdLogId,				$Web10FrontOverviewLog, 10);
		CreateLink     (c_Control_Meldungen,		$ControlIdLog,					$Web10FrontOverviewLog, 20);

		// Web10front Detail
		$Idx = 20;
		foreach ($WeckerConfig as $WeckerId=>$WeckerData) {
			$CirclyId   = get_WeckerCirclyId($WeckerId, $CategoryIdWeckers);
			$WeckerName = $WeckerData[c_Property_Name];

			// Detailed CirclyData
			$Web10FrontDetailId  			= CreateCategory($WeckerName, $Web10FrontId, 100+$Idx);
			CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_'.$WeckerName.'_Bottom', 	$WFC10_TabPaneItem.'',  	100+$Idx,		$WeckerName, 	'', $Web10FrontDetailId /*BaseId*/, 'false' /*BarBottomVisible*/);

			CreateLink(c_WFC_Uebersicht,    	get_WeckerControlId(c_Control_Uebersicht,   	$CirclyId),		$Web10FrontDetailId,	10);

			$Idx = $Idx + 10;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Touch 7" Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Touch_Enabled and $Touch_ConfigId <> '') {
		$TouchId               = CreateCategoryPath($Touch_Path, 10);
		EmptyCategory($TouchId);
		$TouchOverviewId       = CreateCategory(    'Overview',  $TouchId,    0);
		$TouchOverviewWoche    = CreateCategory(    'Woche',     $TouchOverviewId,    10);
		$TouchOverviewOption   = CreateCategory(    'Optionen',  $TouchOverviewId,    50);
		$TouchOverviewTag		  = CreateCategory(    'Tag',  		$TouchOverviewId,    60);

		DeleteWFCItems($Touch_ConfigId, $Touch_TabPaneItem);

		// Übersicht
		CreateWFCItemTabPane   ($Touch_ConfigId, $Touch_TabPaneItem,             	$Touch_TabPaneParent,           $Touch_TabPaneOrder, 	$Touch_TabPaneName, $Touch_TabPaneIcon);

		CreateWFCItemCategory  ($Touch_ConfigId, $Touch_TabPaneItem.'_Woche',   	$Touch_TabPaneItem.'',    	10, $Touch_TabName1	, ''	, $TouchOverviewWoche /*BaseId*/,		'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($Touch_ConfigId, $Touch_TabPaneItem.'_Optionen',	$Touch_TabPaneItem.'',  	20, 'Optionen'	, ''	, $TouchOverviewOption /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($Touch_ConfigId, $Touch_TabPaneItem.'_Tag',	 		$Touch_TabPaneItem.'',  	30, 'Tag'	  	, ''	, $TouchOverviewTag /*BaseId*/, 'false' /*BarBottomVisible*/);

		// Tag
		CreateLink		(c_WFC_AlarmName,		$ControlIdOVWeckerName,  	$TouchOverviewTag, 10);
		CreateLink     (c_WFC_Tag,				$ControlIdOVWeckerTag,  	$TouchOverviewTag, 20);
		CreateLink     (c_WFC_Stunde,			$ControlIdOVWeckerStunde, 	$TouchOverviewTag, 30);
		CreateLink     (c_WFC_Minute,			$ControlIdOVWeckerMinute,	$TouchOverviewTag, 40);
		CreateLink     (c_WFC_Active,			$ControlIdOVActive,			$TouchOverviewTag, 50);

		// Woche
		CreateLink		(c_WFC_AlarmName,		$ControlIdOVWeckerName,  	$TouchOverviewWoche, 10);
		CreateLink     (c_WFC_Global,			$ControlIdOVGlobal,			$TouchOverviewWoche, 20);
		CreateLink     (c_WFC_Urlaubszeit,	$ControlIdOVUrlaubszeit,	$TouchOverviewWoche, 40);

		// Wochen Optionen
		CreateLink     (c_WFC_Urlaub,			$ControlIdOVUrlaub,			$TouchOverviewOption, 30);
		CreateLink     (c_WFC_Feiertag,		$ControlIdOVFeiertag,		$TouchOverviewOption, 40);
		CreateLink     (c_WFC_Frost,			$ControlIdOVFrost,			$TouchOverviewOption, 50);
		CreateLink     (c_WFC_Snooze,			$ControlIdOVSchlummer,		$TouchOverviewOption, 60);
		CreateLink     (c_WFC_End,				$ControlIdOVEnd,				$TouchOverviewOption, 70);

		// Touch Detail
		$Idx = 20;
		foreach ($WeckerConfig as $WeckerId=>$WeckerData) {
			$CirclyId   = get_WeckerCirclyId($WeckerId, $CategoryIdWeckers);
			$WeckerName = $WeckerData[c_Property_Name];

			// Detailed CirclyData
			$TouchDetailId  			= CreateCategory($WeckerName, $TouchId, 100+$Idx);
			CreateWFCItemCategory  ($Touch_ConfigId, $Touch_TabPaneItem.'_'.$WeckerName.'_Bottom', 	$Touch_TabPaneItem.'',  	100+$Idx,		$WeckerName, 	'', $TouchDetailId /*BaseId*/, 'false' /*BarBottomVisible*/);

			CreateLink(c_WFC_Uebersicht,    	get_WeckerControlId(c_Control_Uebersicht,   	$CirclyId),		$TouchDetailId,	10);

			$Idx = $Idx + 10;
		}

	}

	ReloadAllWebFronts();

	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled) {
		$mobileId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);

		EmptyCategory($mobileId);
		$mobileOverviewWoche    = CreateCategory(    'Wochen Einst.',  $mobileId,    10);
//		$mobileOverviewOption   = CreateCategory(    'Optionen',  $mobileId,    20);
		$mobileOverviewTag		= CreateCategory(    'Tag(e) Einst.',  $mobileId,    20);
		$mobileOverviewOVLog    = CreateCategory(    'Meldungen', 		$mobileId,    30);

		// Woche
		CreateLink		(c_WFC_AlarmName,		$ControlIdOVWeckerName,  	$mobileOverviewWoche, 10);
		CreateLink     (c_WFC_Global,			$ControlIdOVGlobal,			$mobileOverviewWoche, 20);
		CreateLink     (c_WFC_Urlaubszeit,	$ControlIdOVUrlaubszeit,	$mobileOverviewWoche, 30);
		CreateLink     (c_WFC_Urlaub,			$ControlIdOVUrlaub,			$mobileOverviewWoche, 40);
		CreateLink     (c_WFC_Feiertag,		$ControlIdOVFeiertag,		$mobileOverviewWoche, 50);
		CreateLink     (c_WFC_Frost,			$ControlIdOVFrost,			$mobileOverviewWoche, 60);
		CreateLink     (c_WFC_Snooze,			$ControlIdOVSchlummer,		$mobileOverviewWoche, 70);
		CreateLink     (c_WFC_End,				$ControlIdOVEnd,				$mobileOverviewWoche, 80);

		// Tag
		CreateLink		(c_WFC_AlarmName,		$ControlIdOVWeckerName,  	$mobileOverviewTag, 10);
		CreateLink     (c_WFC_Tag,				$ControlIdOVWeckerLTag,  	$mobileOverviewTag, 20);
		CreateLink     (c_WFC_Stunde,			$ControlIdOVWeckerLStunde, $mobileOverviewTag, 30);
		CreateLink     (c_WFC_Minute,			$ControlIdOVWeckerLMinute,	$mobileOverviewTag, 40);
		CreateLink     (c_WFC_Active,			$ControlIdOVActive,			$mobileOverviewTag, 50);

		//Log
		CreateLink     (c_Control_Meldungen, $ControlIdLog,				$mobileOverviewOVLog, 30);

		// Übersichten
		$Idx = 100;
		foreach ($WeckerConfig as $WeckerId=>$WeckerData) {
			$CirclyId   = get_WeckerCirclyId($WeckerId, $CategoryIdWeckers);
			$WeckerName = $WeckerData[c_Property_Name];

			// Detailed CirclyData
			CreateLink($WeckerName,    	get_WeckerControlId(c_Control_Uebersicht,   	$CirclyId),		$mobileId,	$Idx);

			$Idx = $Idx + 10;
		}
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	// eDIP Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($eDIP_Enabled) {
		$eDIPId  = CreateCategoryPath($eDIP_Path, $eDIP_PathOrder, $eDIP_PathIcon);

		EmptyCategory($eDIPId);
		$eDIPOverviewWoche    = CreateCategory(    'Wochen Einst.',     $eDIPId,    10);
		$eDIPOverviewOption   = CreateCategory(    'Optionen',  $eDIPId,    20);
//		$eDIPOverviewTag		= CreateCategory(     'Tag(e) Einst.',  		 $eDIPId,    30);

		// Woche
		CreateLink		(c_WFC_AlarmName,		$ControlIdOVWeckerName,  	$eDIPOverviewWoche, 10);
		CreateLink     (c_WFC_Urlaubszeit,	$ControlIdOVUrlaubszeit,	$eDIPOverviewWoche, 30);
		CreateLink     (c_WFC_Urlaub,		$ControlIdOVUrlaub,			$eDIPOverviewOption, 40);
		CreateLink     (c_WFC_Feiertag,			$ControlIdOVFeiertag,		$eDIPOverviewOption, 50);
		CreateLink     (c_WFC_Frost,			$ControlIdOVFrost,			$eDIPOverviewOption, 60);
		CreateLink     (c_WFC_Snooze,			$ControlIdOVSchlummer,		$eDIPOverviewOption, 70);
		CreateLink     (c_WFC_End,				$ControlIdOVEnd,				$eDIPOverviewOption, 80);

		// Tag
		CreateLink		(c_WFC_AlarmName,		$ControlIdOVWeckerName,  	$eDIPId, 10);
		CreateLink     (c_WFC_Global,			$ControlIdOVGlobal,			$eDIPId, 20);
		CreateLink     (c_WFC_Tag,				$ControlIdOVWeckerTag,  	$eDIPId, 30);
		CreateLink     (c_WFC_Stunde,			$ControlIdOVWeckerStunde, 	$eDIPId, 40);
		CreateLink     (c_WFC_Minute,			$ControlIdOVWeckerMinute,	$eDIPId, 50);
		CreateLink     (c_WFC_Active,			$ControlIdOVActive,			$eDIPId, 60);

	}



   // ------------------------------------------------------------------------------------------------
	function get_WeckerCirclyId($DeviceName, $ParentId) {
		$CategoryId = IPS_GetObjectIDByIdent($DeviceName, $ParentId);
		return $CategoryId;
	}

   // ------------------------------------------------------------------------------------------------
	function get_WeckerControlId($ControlName, $CirclyId) {
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

	/** Definieren "Tages" Timers
	 *
	 * Anlegen eines Timers, der einmal pro Tag zu einer bestimmten Uhrzeit ausgeführt wird
	 *
	 * @param string $Name Name des Timers
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Stunde Stunde zu der der Timer aktiviert werden soll
	 * @param integer $Minute Minute zu der der Timer aktiviert werden soll
	 * @return integer ID des Timers
	 *
	 */
	function CreateTimer_Profile ($Name, $ParentId, $Stunde=0, $Minute=0, $Position=0, $Status=true) {
		$TimerId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($TimerId === false) $TimerId = @IPS_GetEventIDByName($Name, $ParentId);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetParent($TimerId, $ParentId);
			IPS_SetName($TimerId, $Name);
			IPS_SetIdent($TimerId, Get_IdentByName($Name));
			if (!IPS_SetEventCyclic($TimerId, 2 /**Daily*/, 1,0,0,0,0)) {
				Error ("IPS_SetEventCyclic failed !!!");
			}
			if (!IPS_SetEventCyclicTimeBounds($TimerId, mktime($Stunde, $Minute, 0), 0)) {
				Error ("IPS_SetEventCyclicTimeBounds failed !!!");
			}
			IPS_SetPosition($TimerId, $Position);
			IPS_SetEventActive($TimerId, $Status);
			Debug ('Created Wecker Timer '.$Name.'='.$TimerId."");
		}
		return $TimerId;
	}

	/** @}*/
?>