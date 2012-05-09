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

	 /**@defgroup IPSSchaltuhr_visualization IPSSchaltuhr Visualisierung
	 * @ingroup IPSSchaltuhr
	 * @{
	 *
	 * Visualisierungen von IPSSchaltuhr
	 *
	 * IPSSchaltuhr WebFront Visualisierung:
	 *
	 *  Übersicht über aller ZSU
	 *  @image html IPSSchaltuhr_WebFrontOverview.jpg
	 *  <BR>
	 *  Detailansicht einer ZSU Konfiguration
	 *  @image html IPSSchaltuhr_WebFrontSettings.jpg
	 *
	 *
	 * IPSSchaltuhr Mobile Visualisierung:
	 *
	 *  Übersicht über aller ZSU
	 *  @image html IPSSchaltuhr_MobileOverview.png
	 *  <BR>
	 *  Detailansicht einer ZSU Konfiguration
	 *  @image html IPSSchaltuhr_MobileSettings.png
	 *
	 *@}*/

	 /**@defgroup IPSSchaltuhr_install IPSSchaltuhr Installation
	 * @ingroup IPSSchaltuhr
	 * @{
	 *
	 * Script zur kompletten Installation der IPSSchaltuhr Steuerung.
	 *
	 * Vor der Installation muß das File IPSSchaltuhr_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * In dem File IPSSchaltuhr_Custom.inc.php werden die Wekcer Aktionen parametriert
	 *
	 * In dem File IPSSchaltuhr.ini werden die Webfront Konfigurationen angepasst.
	 *
	 * @page rquirements_IPSSchaltuhr Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 *
	 * @page install_IPSSchaltuhr Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSSchaltuhr Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSSchaltuhr_Installation.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('IPSSchaltuhr');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');
//	$moduleManager->VersionHandler()->CheckModuleVersion('IPSComponent','2.50.1');
//	$moduleManager->VersionHandler()->CheckModuleVersion('IPSMessageHandler','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",                "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSSchaltuhr_Configuration.inc.php",   "IPSLibrary::config::modules::IPSSchaltuhr");

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

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');

	// Add Scripts
   $ScriptIdChangeSettings  = IPS_GetScriptIDByName('IPSSchaltuhr_ChangeSettings',  $CategoryIdApp);
   $ScriptIdTimer   			 = IPS_GetScriptIDByName('IPSSchaltuhr_Timer',    			$CategoryIdApp);
   $ScriptIdEvent   			 = IPS_GetScriptIDByName('IPSSchaltuhr_Event',    			$CategoryIdApp);

	// Create ZSUs and Controls
	// ----------------------------------------------------------------------------------------------------------------------------

	CreateProfile_Associations ('IPSSchaltuhr_StartTag', array(
												0	=> ' ',
												1	=> c_Program_Montag,
												2 	=> c_Program_Dienstag,
												3 	=> c_Program_Mittwoch,
												4 	=> c_Program_Donnerstag,
												5 	=> c_Program_Freitag,
												6 	=> c_Program_Samstag,
												7 	=> c_Program_Sonntag,
												),'', array(
												0  =>	-1,
												1  =>	-1,
												2  =>	-1,
												3  =>	-1,
												4  =>	-1,
												5 	=>	-1,
												6 	=>	-1,
												7 	=>	-1,
												));

	CreateProfile_Associations ('IPSSchaltuhr_StopTag', array(
												0	=> ' ',
												1	=> c_Program_Montag,
												2 	=> c_Program_Dienstag,
												3 	=> c_Program_Mittwoch,
												4 	=> c_Program_Donnerstag,
												5 	=> c_Program_Freitag,
												6 	=> c_Program_Samstag,
												7 	=> c_Program_Sonntag,
												),'', array(
												0  =>	-1,
												1  =>	-1,
												2  =>	-1,
												3  =>	-1,
												4  =>	-1,
												5 	=>	-1,
												6 	=>	-1,
												7 	=>	-1,
												));

	CreateProfile_Associations ('IPSSchaltuhr_LStunde', array(
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

	CreateProfile_Associations ('IPSSchaltuhr_LMinute', array(
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


	CreateProfile_Associations ('IPSSchaltuhr_Name', array(
												0	=> '00'));

	CreateProfile_Associations ('IPSSchaltuhr_Tag', array(
												-1		=> '-',
												0 		=> c_Program_Montag,
												100 	=> '+'),'', array(
												-1  	=>	0x00F0F0,
												0  	=>	-1,
												100 	=>	0x00F0F0));

 	CreateProfile_Associations ('IPSSchaltuhr_Stunde', array(
// 	                                 -2 	=> 'AUS',
												-1		=> '-',
												0		=> '%d',
												100	=> '+'),'', array(
//												-2 	=>	0x800000,
												-1  	=>	0x00F0F0,
												0  	=>	-1,
												100 	=>	0x00F0F0));

 	CreateProfile_Associations ('IPSSchaltuhr_Minute', array(
												-1	=> '-',
												0	=> '%d',
												100	=> '+'),'', array(
												-1  	=>	0x00F0F0,
												0  	=>	-1,
												100 	=>	0x00F0F0));

	CreateProfile_Associations ('IPSSchaltuhr_Aktiv', array(
												0	=> c_Program_Off,
												1 	=> c_Program_On),'', array(
												0  =>	0x800000,
												1 	=>	0x008000));

	CreateProfile_Associations ('IPSSchaltuhr_StartSensor', array(
												0	=> ' ',
												1	=> 'Sensor_1',
												2 	=> 'Sensor_2',
												3 	=> 'Sensor_3',
												4 	=> 'Sensor_4',
												5 	=> 'Sensor_5',
												6 	=> 'Sensor_6',
												7 	=> 'Sensor_7',
												8 	=> 'Sensor_8',
												9 	=> 'Sensor_9',
												10	=> 'Sensor_10',
												),'', array(
												0  =>	-1,
												1 	=>	-1,
												2 	=>	-1,
												3 	=>	-1,
												4 	=>	-1,
												5 	=>	-1,
												6 	=>	-1,
												7 	=>	-1,
												8 	=>	-1,
												9 	=>	-1,
												10	=>	-1,
												));

	CreateProfile_Associations ('IPSSchaltuhr_StopSensor', array(
												0	=> ' ',
												1	=> 'Sensor_1',
												2 	=> 'Sensor_2',
												3 	=> 'Sensor_3',
												4 	=> 'Sensor_4',
												5 	=> 'Sensor_5',
												6 	=> 'Sensor_6',
												7 	=> 'Sensor_7',
												8 	=> 'Sensor_8',
												9 	=> 'Sensor_9',
												10	=> 'Sensor_10',
												),'', array(
												0  =>	-1,
												1 	=>	-1,
												2 	=>	-1,
												3 	=>	-1,
												4 	=>	-1,
												5 	=>	-1,
												6 	=>	-1,
												7 	=>	-1,
												8 	=>	-1,
												9 	=>	-1,
												10	=>	-1,
												));

	CreateProfile_Associations ('IPSSchaltuhr_RunSensor', array(
												0	=> ' ',
												1	=> 'Sensor_1',
												2 	=> 'Sensor_2',
												3 	=> 'Sensor_3',
												4 	=> 'Sensor_4',
												5 	=> 'Sensor_5',
												6 	=> 'Sensor_6',
												7 	=> 'Sensor_7',
												8 	=> 'Sensor_8',
												9 	=> 'Sensor_9',
												10	=> 'Sensor_10',
												),'', array(
												0  =>	-1,
												1 	=>	-1,
												2 	=>	-1,
												3 	=>	-1,
												4 	=>	-1,
												5 	=>	-1,
												6 	=>	-1,
												7 	=>	-1,
												8 	=>	-1,
												9 	=>	-1,
												10	=>	-1,
												));



	$CategoryIdZSUs	= CreateCategory(c_ZSUCircles, $CategoryIdData, 300);
  	SetVariableConstant ("ZSU_ID_ZSUZEITEN",    $CategoryIdZSUs,   	'IPSSchaltuhr_IDs.inc.php', 'IPSLibrary::app::modules::IPSSchaltuhr');
  	SetVariableConstant ("ZSU_ID_TIMER",    		$ScriptIdTimer,   		'IPSSchaltuhr_IDs.inc.php', 'IPSLibrary::app::modules::IPSSchaltuhr');

	$ZSUConfig        = get_ZSUConfiguration();
	$Ass                 = 0;
	$Idx                 = 100;
	$vpn 						= 'IPSSchaltuhr_Name';

// ZSUzeiten als Association anlegen
   if(IPS_VariableProfileExists($vpn)){
        IPS_DeleteVariableProfile($vpn);
	}

	IPS_CreateVariableProfile($vpn, 1);
   	IPS_SetVariableProfileValues($vpn, 0, 10, 0);

	foreach ($ZSUConfig as $ZSUName=>$ZSUData) {
    	 	IPS_SetVariableProfileAssociation($vpn, $Ass, $ZSUData[c_Property_Name],"", -1);

			$ZSUId              		= CreateCategory($ZSUName, $CategoryIdZSUs, $Idx);
			$ControlIdStartzeit		= CreateVariable(c_Control_StartZeit,		3 /*String*/,  $ZSUId, 10, '~String',   null, '09:00');
			$ControlIdStopzeit		= CreateVariable(c_Control_StopZeit,		3 /*String*/,  $ZSUId, 20, '~String',   null, '20:00');
			$ControlIdStartTag		= CreateVariable(c_Control_StartTag,		3 /*String*/,  $ZSUId, 30, '~String',   null, '0,1,1,1,1,1,1,1,');
			$ControlIdStopTag			= CreateVariable(c_Control_StopTag,			3 /*String*/,  $ZSUId, 30, '~String',   null, '0,1,1,1,1,1,1,1,');
			$ControlIdStartAktiv		= CreateVariable(c_Control_StartAktiv,		3 /*String*/,  $ZSUId, 40, '~String',   null, '0,0,0,0,0,0,0,0,0,0,0,');
			$ControlIdStopAktiv		= CreateVariable(c_Control_StopAktiv,		3 /*String*/,  $ZSUId, 50, '~String',   null, '0,0,0,0,0,0,0,0,0,0,0,');
			$ControlIdRunAktiv		= CreateVariable(c_Control_RunAktiv,		3 /*String*/,  $ZSUId, 60, '~String',   null, '0,0,0,0,0,0,0,0,0,0,0,');
			$ControlIdUebersicht		= CreateVariable(c_Control_Uebersicht,		3 /*String*/,  $ZSUId, 70, '~HTMLBox',  null, '');
			$ControlIdSAusgang    	= CreateVariable(c_Control_SollAusgang,   0 /*Boolean*/, $ZSUId, 100, '',   null, false);
			$ControlIdIAusgang    	= CreateVariable(c_Control_IstAusgang,    0 /*Boolean*/, $ZSUId, 110, '',   null, false);

			$i=0;
			foreach ($ZSUData[c_Property_RunSensoren] as $ZSURunName=>$ZSURunData) {
				if ($ZSURunName <> ''){
					CreateEvent ($ZSUName.'-'.$i, 	$ZSURunData[c_Property_SensorID], $ScriptIdEvent, 0);//$Idx+$i);
				}
			$i++;
			}

// Timer Event erstellen
			CreateTimerWeek ($ZSUName.'-Start', $ScriptIdTimer, 127,  9, 0, $Idx+1, true);
			CreateTimerWeek ($ZSUName.'-Stop', 	$ScriptIdTimer, 127, 20, 0, $Idx+2, true);

			$Idx = $Idx  + 20;
			$Ass++ ;
	}
	// Logging
	$CategoryIdLog	 = CreateCategory('Log', $CategoryIdData, 210);
	$ControlIdLog   = CreateVariable('LogMessages',  3 /*String*/,  $CategoryIdLog, 220, '~HTMLBox', null, '');
	$ControlIdLogId = CreateVariable('LogId',        1 /*Integer*/, $CategoryIdLog, 230, '',         null, 0);

	$ControlIdOVZSUName     	= CreateVariable(c_Control_Name,   			1 /*Integer*/, $CategoryIdData,  10, 'IPSSchaltuhr_Name', 		$ScriptIdChangeSettings, 0, 'Title');
	$ControlIdOVZSUStartTag   	= CreateVariable(c_Control_StartTag,		1 /*Integer*/, $CategoryIdData,  20, 'IPSSchaltuhr_StartTag',	$ScriptIdChangeSettings, 0, 'Calendar');
	$ControlIdOVZSUStopTag    	= CreateVariable(c_Control_StopTag,			1 /*Integer*/, $CategoryIdData,  25, 'IPSSchaltuhr_StopTag', 	$ScriptIdChangeSettings, 0, 'Calendar');
	$ControlIdOVZSUStartStunde = CreateVariable(c_Control_StartStunde,  	1 /*Integer*/, $CategoryIdData,  30, 'IPSSchaltuhr_LStunde', 	$ScriptIdChangeSettings, 0, 'Clock');
	$ControlIdOVZSUStopStunde  = CreateVariable(c_Control_StopStunde,  	1 /*Integer*/, $CategoryIdData,  35, 'IPSSchaltuhr_LStunde', 	$ScriptIdChangeSettings, 0, 'Clock');
	$ControlIdOVZSUStartMinute = CreateVariable(c_Control_StartMinute,  	1 /*Integer*/, $CategoryIdData,  40, 'IPSSchaltuhr_LMinute', 	$ScriptIdChangeSettings, 0, 'Clock');
	$ControlIdOVZSUStopMinute  = CreateVariable(c_Control_StopMinute,  	1 /*Integer*/, $CategoryIdData,  45, 'IPSSchaltuhr_LMinute', 	$ScriptIdChangeSettings, 0, 'Clock');

	$ControlIdOVStartAktiv		= CreateVariable(c_Control_StartAktiv,		1 /*Integer*/, $CategoryIdData,  60, 'IPSSchaltuhr_StartSensor', 	$ScriptIdChangeSettings, 0, 'Intensity');
	$ControlIdOVStopAktiv		= CreateVariable(c_Control_StopAktiv,		1 /*Integer*/, $CategoryIdData,  70, 'IPSSchaltuhr_StopSensor',  	$ScriptIdChangeSettings, 0, 'Intensity');
	$ControlIdOVRunAktiv			= CreateVariable(c_Control_RunAktiv,		1 /*Integer*/, $CategoryIdData,  80, 'IPSSchaltuhr_RunSensor',  	$ScriptIdChangeSettings, 0, 'Intensity');
	$ControlIdOVUebersicht		= CreateVariable(c_Control_Uebersicht,		3 /*String*/,  $CategoryIdData, 200, '~HTMLBox',   null,	'');


	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront > 19" Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC_Enabled and $WFC_ConfigId <> '') {
		$WebFrontId               = CreateCategoryPath($WFC_Path, 10);
		EmptyCategory($WebFrontId);
		$WebFrontOverviewCFG     = CreateCategory(    'Konfiguration',   $WebFrontId,    10);
		$WebFrontOverviewMLD     = CreateCategory(     'Meldungen', 		$WebFrontId,    20);
		$WebFrontOverview			  = CreateCategory(    'Overview',  		$WebFrontId,    30);

		DeleteWFCItems($WFC_ConfigId, $WFC_TabPaneItem);

		// Übersicht
		CreateWFCItemTabPane   ($WFC_ConfigId, $WFC_TabPaneItem,             	$WFC_TabPaneParent, 	$WFC_TabPaneOrder, 		$WFC_TabPaneName, $WFC_TabPaneIcon);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_Konfig',		$WFC_TabPaneItem,  		  	20, 		'Konfig', 			'', $WebFrontOverviewCFG /*BaseId*/,		'false' /*BarBottomVisible*/);
		CreateWFCItemSplitPane ($WFC_ConfigId, $WFC_TabPaneItem.'_OV',       	$WFC_TabPaneItem,    		10, 		$WFC_TabName1, 	$WFC_TabIcon1, 	0 /*Horizontal*/, 40 /*Hight*/, 	0 /*Target=Pane1*/, 0/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_Overview', $WFC_TabPaneItem.'_OV',  	10, 'Übersicht', 		'', $WebFrontOverview /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC_ConfigId, $WFC_TabPaneItem.'_OV_Log',  		$WFC_TabPaneItem.'_OV', 	20, 'Loging', 			'', $WebFrontOverviewMLD /*BaseId*/, 		'false' /*BarBottomVisible*/);                             // integer $PercentageSlider

		$Dummy_Start_Id 	= CreateInstance ('Start',			$WebFrontOverviewCFG, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 20);
		$Dummy_Run_Id 		= CreateInstance ('Laufzeit', 	$WebFrontOverviewCFG, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 30);
		$Dummy_Stop_Id 	= CreateInstance ('Stop', 			$WebFrontOverviewCFG, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 40);
//		$Dummy_Log_Id 		= CreateInstance ('Meldungen', 	$WebFrontOverviewMLD, '{485D0419-BE97-4548-AA9C-C083EB82E61E}', 50);

		// Top Left
		CreateLink		(c_WFC_Name,				$ControlIdOVZSUName,  			$WebFrontOverviewCFG, 10);
		CreateLink     (c_WFC_StartTag,			$ControlIdOVZSUStartTag,		$Dummy_Start_Id, 20);
		CreateLink     (c_WFC_StartStunde,		$ControlIdOVZSUStartStunde,	$Dummy_Start_Id, 30);
		CreateLink     (c_WFC_StartMinute,		$ControlIdOVZSUStartMinute,	$Dummy_Start_Id, 40);
		CreateLink     (c_WFC_StartAktiv,		$ControlIdOVStartAktiv,			$Dummy_Start_Id, 50);

		// Top Right
		CreateLink     (c_WFC_RunAktiv,			$ControlIdOVRunAktiv,			$Dummy_Run_Id, 10);
		CreateLink     (c_WFC_StopTag,			$ControlIdOVZSUStopTag,			$Dummy_Stop_Id, 20);
		CreateLink     (c_WFC_StopStunde,		$ControlIdOVZSUStopStunde,		$Dummy_Stop_Id, 30);
		CreateLink     (c_WFC_StopMinute,		$ControlIdOVZSUStopMinute,		$Dummy_Stop_Id, 40);
		CreateLink     (c_WFC_StopAktiv,			$ControlIdOVStopAktiv,			$Dummy_Stop_Id, 50);

		// BottomL
//		CreateLink     (c_WFC_Uebersicht,		$ControlIdOVUebersicht,				$WebFrontOverviewMLD, 10);

		// BottomR
		CreateLink     (c_Control_MeldungID,		$ControlIdLogId,				$WebFrontOverviewMLD, 20);
		CreateLink     (c_Control_Meldungen,		$ControlIdLog,					$WebFrontOverviewMLD, 30);


		// Übersicht
		$Idx = 20;
		foreach ($ZSUConfig as $ZSUName=>$ZSUData) {
			$CirclyId   = get_ZSUCirclyId($ZSUName, $CategoryIdZSUs);

			CreateLink($ZSUData[c_Property_Name],    	get_ZSUControlId(c_Control_Uebersicht,   	$CirclyId),		$WebFrontOverview,	$Idx);

			$Idx = $Idx + 10;
		}


	}

	ReloadAllWebFronts();




   // ------------------------------------------------------------------------------------------------
	function get_ZSUCirclyId($DeviceName, $ParentId) {
		$CategoryId = IPS_GetObjectIDByIdent($DeviceName, $ParentId);
		return $CategoryId;
	}

   // ------------------------------------------------------------------------------------------------
	function get_ZSUControlId($ControlName, $CirclyId) {
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
			Debug ('Created ZSU Timer '.$Name.'='.$TimerId."");
		}
		return $TimerId;
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
	function CreateTimerWeek ($Name, $ParentId, $Datumstage=0, $Stunde=0, $Minute=0, $Position=0, $Status=true) {
		$TimerId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($TimerId === false) $TimerId = @IPS_GetEventIDByName($Name, $ParentId);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetParent($TimerId, $ParentId);
			IPS_SetName($TimerId, $Name);
			IPS_SetIdent($TimerId, Get_IdentByName($Name));
			if (!IPS_SetEventCyclic($TimerId, 3 /**Week*/, 1 /**Datumsintervall**/,$Datumstage /**Datumstage**/,0 /**Datemstageintervall**/,0 /**Zeittyp**/,0 /**Zeitintervall**/)) {
				Error ("IPS_SetEventCyclic failed !!!");
			}
			if (!IPS_SetEventCyclicTimeBounds($TimerId, mktime($Stunde, $Minute, 0), 0)) {
				Error ("IPS_SetEventCyclicTimeBounds failed !!!");
			}
			IPS_SetPosition($TimerId, $Position);
			IPS_SetEventActive($TimerId, $Status);
			Debug ('Created ZSU Timer '.$Name.'='.$TimerId."");
		}
		return $TimerId;
	}

	/** @}*/
?>