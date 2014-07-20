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

	/**@defgroup ipsshadowing_visualization IPSShadowing Visualisierung
	 * @ingroup ipsshadowing
	 * @{
	 *
	 * Visualisierungen von IPSShadowing
	 *
	 * IPSShadowing WebFront Visualisierung:
	 *
	 *  Übersicht über alle Beschattungselemente
	 *  @image html IPSShadowing_WFOverview.png
	 *  <BR>
	 *  Detailansicht eines eines Beschattungselementes
	 *  @image html IPSShadowing_WFDevice.png
	 *
	 *@}*/

	 /**@defgroup ipsshadowing_install IPSShadowing Installation
	 * @ingroup ipsshadowing
	 * @{
	 *
	 * Script zur kompletten Installation der IPSShadowing Steuerung.
	 *
	 * Vor der Installation muß das File IPSShadowing_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page rquirements_ipsshadowing Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.2
	 * - IPSLogger >= 2.50.1
	 * - IPSComponent >= 2.50.1
	 *
	 * @page install_ipsshadowing Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSShadowing Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSShadowing_Installation.ips.php
	 * @author        Andreas Brauneis
	 *
	 * @version
	 *  Version 2.50.1, 19.03.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSShadowing');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSComponent','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSTwilight','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",                "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSShadowing.inc.php",                "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Constants.inc.php",      "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Configuration.inc.php",  "IPSLibrary::config::modules::IPSShadowing");

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
	$ScriptIdChangeSettings         = IPS_GetScriptIDByName('IPSShadowing_ChangeSettings',         $CategoryIdApp);
	$ScriptIdAutomaticOn            = IPS_GetScriptIDByName('IPSShadowing_AutomaticOn',            $CategoryIdApp);
	$ScriptIdAutomaticOff           = IPS_GetScriptIDByName('IPSShadowing_AutomaticOff',           $CategoryIdApp);
	$ScriptIdAutomaticReset         = IPS_GetScriptIDByName('IPSShadowing_AutomaticReset',         $CategoryIdApp);
	$ScriptIdRefreshTimer           = IPS_GetScriptIDByName('IPSShadowing_RefreshTimer',           $CategoryIdApp);
	$ScriptIdProgramTimer           = IPS_GetScriptIDByName('IPSShadowing_ProgramTimer',           $CategoryIdApp);
	$ScriptIdResetTimer             = IPS_GetScriptIDByName('IPSShadowing_ResetTimer',             $CategoryIdApp);
	$ScriptIdScenarioCreate         = IPS_GetScriptIDByName('IPSShadowing_ScenarioCreate',         $CategoryIdApp);
	$ScriptIdScenarioDelete         = IPS_GetScriptIDByName('IPSShadowing_ScenarioDelete',         $CategoryIdApp);
	$ScriptIdProfileTempCreate      = IPS_GetScriptIDByName('IPSShadowing_ProfileTempCreate',      $CategoryIdApp);
	$ScriptIdProfileTempDelete      = IPS_GetScriptIDByName('IPSShadowing_ProfileTempDelete',      $CategoryIdApp);
	$ScriptIdProfileSunCreate       = IPS_GetScriptIDByName('IPSShadowing_ProfileSunCreate',       $CategoryIdApp);
	$ScriptIdProfileSunDelete       = IPS_GetScriptIDByName('IPSShadowing_ProfileSunDelete',       $CategoryIdApp);
	$ScriptIdProfileWeatherCreate   = IPS_GetScriptIDByName('IPSShadowing_ProfileWeatherCreate',   $CategoryIdApp);
	$ScriptIdProfileWeatherDelete   = IPS_GetScriptIDByName('IPSShadowing_ProfileWeatherDelete',   $CategoryIdApp);
	$ScriptIdProfileBgnOfDayCreate  = IPS_GetScriptIDByName('IPSShadowing_ProfileBgnOfDayCreate',  $CategoryIdApp);
	$ScriptIdProfileBgnOfDayDelete  = IPS_GetScriptIDByName('IPSShadowing_ProfileBgnOfDayDelete',  $CategoryIdApp);
	$ScriptIdProfileEndOfDayCreate  = IPS_GetScriptIDByName('IPSShadowing_ProfileEndOfDayCreate',  $CategoryIdApp);
	$ScriptIdProfileEndOfDayDelete  = IPS_GetScriptIDByName('IPSShadowing_ProfileEndOfDayDelete',  $CategoryIdApp);

	// Create Circles and Controls
	// ----------------------------------------------------------------------------------------------------------------------------
	$IPSShadowing_ProgNigJal = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_Opened      	=> c_Program_Opened,
		c_ProgramId_Dimout      	=> c_Program_Dimout);
	$IPSShadowing_ProgDayJal = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_Opened      	=> c_Program_Opened,
		c_ProgramId_OpenedOrShadowing	=> c_Program_OpenedOrShadowing,
		c_ProgramId_Dimout      	=> c_Program_Dimout);
	$IPSShadowing_ProgTmpJal = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_DimoutOrShadowing	=> c_Program_DimoutOrShadowing,
		c_ProgramId_DimoutAndShadowing	=> c_Program_DimoutAndShadowing,
		c_ProgramId_Dimout      	=> c_Program_Dimout);
	$IPSShadowing_ProgPreJal = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_Opened      	=> c_Program_Opened,
		c_ProgramId_OpenedDay		=> c_Program_OpenedDay,
		c_ProgramId_OpenedNight    => c_Program_OpenedNight);

	$IPSShadowing_ProgNigSht = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_Opened      	=> c_Program_Opened,
		c_ProgramId_25				=> c_Program_25,
		c_ProgramId_50				=> c_Program_50,
		c_ProgramId_75				=> c_Program_75,
		c_ProgramId_90				=> c_Program_90,
		c_ProgramId_Closed			=> c_Program_Closed);
	$IPSShadowing_ProgDaySht = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_Opened      	=> c_Program_Opened,
		c_ProgramId_25				=> c_Program_25,
		c_ProgramId_50				=> c_Program_50,
		c_ProgramId_75				=> c_Program_75,
		c_ProgramId_90				=> c_Program_90,
		c_ProgramId_Closed      	=> c_Program_Closed);
	$IPSShadowing_ProgTmpSht = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_25				=> c_Program_25,
		c_ProgramId_50				=> c_Program_50,
		c_ProgramId_75				=> c_Program_75,
		c_ProgramId_90				=> c_Program_90,
		c_ProgramId_Closed      	=> c_Program_Closed);
	$IPSShadowing_ProgPreSht = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_Opened      	=> c_Program_Opened,
		c_ProgramId_OpenedDay		=> c_Program_OpenedDay,
		c_ProgramId_OpenedNight    => c_Program_OpenedNight);

	$IPSShadowing_ProgNigMar = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_MovedIn      	=> c_Program_MovedIn,
		c_ProgramId_MovedOut      	=> c_Program_MovedOut);
	$IPSShadowing_ProgDayMar = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_MovedIn      	=> c_Program_MovedIn,
		c_ProgramId_50				=> c_Program_50,
		c_ProgramId_75				=> c_Program_75,
		c_ProgramId_MovedOut      	=> c_Program_MovedOut);
	$IPSShadowing_ProgTmpMar = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_50				=> c_Program_50,
		c_ProgramId_75				=> c_Program_75,
		c_ProgramId_MovedOut      	=> c_Program_MovedOut);
	$IPSShadowing_ProgPreMar = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_50				=> c_Program_50,
		c_ProgramId_75				=> c_Program_75,
		c_ProgramId_MovedOut      	=> c_Program_MovedOut,
		c_ProgramId_MovedOutTemp   => c_Program_MovedOutTemp);
	$IPSShadowing_ProgWeaMar = array(
		c_ProgramId_Manual      	=> c_Program_Manual,
		c_ProgramId_MovedIn			=> c_Program_MovedIn);

	$IPSShadowing_Movement = array(
		c_MovementId_Opened      	=> c_Movement_Opened,
		c_MovementId_Space      	=> c_Movement_Space,
		c_MovementId_Down      		=> c_Movement_Down,
		c_MovementId_Stop      		=> c_Movement_Stop,
		c_MovementId_Up      		=> c_Movement_Up,
	);
	$IPSShadowing_MovementJal = $IPSShadowing_Movement;
	$IPSShadowing_MovementJal[c_MovementId_Shadowing] = c_Movement_Shadowing;
	$IPSShadowing_MovementJal[c_MovementId_Dimout]    = c_Movement_Dimout;

	$IPSShadowing_MovementSht = $IPSShadowing_Movement;
	$IPSShadowing_MovementSht[c_MovementId_Closed] = c_Movement_Closed;
	$IPSShadowing_MovementSht[c_MovementId_90]     = c_Movement_90;
	$IPSShadowing_MovementSht[c_MovementId_75]     = c_Movement_75;
	$IPSShadowing_MovementSht[c_MovementId_50]     = c_Movement_50;

	$IPSShadowing_MovementMar = array(
		c_MovementId_MovedOut      => c_Movement_MovedOut,
		c_MovementId_75      		=> c_Movement_75,
		c_MovementId_50      		=> c_Movement_50,
		c_MovementId_MovedIn      	=> c_Movement_MovedIn,
		c_MovementId_Space      	=> c_Movement_Space,
		c_MovementId_MovingOut     => c_Movement_MovingOut,
		c_MovementId_Stop      		=> c_Movement_Stop,
		c_MovementId_MovingIn      => c_Movement_MovingIn,
	);
	$IPSShadowing_MovementCol = array(
		c_MovementId_Stop      		=> -1,
		c_MovementId_Down      		=> 0x00FF00,
		c_MovementId_Up      		=> 0x00FF00,
		c_MovementId_MovingIn      => 0x00FF00,
		c_MovementId_MovingOut 		=> 0x00FF00,
		c_MovementId_Space      	=> 0,
		c_MovementId_NoAction     	=> 0xAAAAAA,
		c_MovementId_Opened      	=> 0xAAAAAA,
		c_MovementId_MovedIn      	=> 0xAAAAAA,
		c_MovementId_25    			=> 0xAAAAAA,
		c_MovementId_50    			=> 0xAAAAAA,
		c_MovementId_75    			=> 0xAAAAAA,
		c_MovementId_90    			=> 0xAAAAAA,
		c_MovementId_Shadowing    	=> 0xAAAAAA,
		c_MovementId_Dimout	    	=> 0xAAAAAA,
		c_MovementId_Closed      	=> 0xAAAAAA,
		c_MovementId_MovedOut      => 0xAAAAAA,
	);

	$IPSShadowing_MovementJal[c_MovementId_Shadowing] = c_Movement_Shadowing;

	CreateProfile_Associations ('IPSShadowing_ProgNigSht', $IPSShadowing_ProgNigSht);
	CreateProfile_Associations ('IPSShadowing_ProgDaySht', $IPSShadowing_ProgDaySht);
	CreateProfile_Associations ('IPSShadowing_ProgTmpSht', $IPSShadowing_ProgTmpSht);
	CreateProfile_Associations ('IPSShadowing_ProgPreSht', $IPSShadowing_ProgPreSht);
	CreateProfile_Associations ('IPSShadowing_ProgNigJal', $IPSShadowing_ProgNigJal);
	CreateProfile_Associations ('IPSShadowing_ProgDayJal', $IPSShadowing_ProgDayJal);
	CreateProfile_Associations ('IPSShadowing_ProgTmpJal', $IPSShadowing_ProgTmpJal);
	CreateProfile_Associations ('IPSShadowing_ProgPreJal', $IPSShadowing_ProgPreJal);
	CreateProfile_Associations ('IPSShadowing_ProgNigMar', $IPSShadowing_ProgNigMar);
	CreateProfile_Associations ('IPSShadowing_ProgDayMar', $IPSShadowing_ProgDayMar);
	CreateProfile_Associations ('IPSShadowing_ProgTmpMar', $IPSShadowing_ProgTmpMar);
	CreateProfile_Associations ('IPSShadowing_ProgPreMar', $IPSShadowing_ProgPreMar);
	CreateProfile_Associations ('IPSShadowing_ProgWeaMar', $IPSShadowing_ProgWeaMar);

	CreateProfile_Associations ('IPSShadowing_MovementJal', $IPSShadowing_MovementJal, "", $IPSShadowing_MovementCol, true);
	CreateProfile_Associations ('IPSShadowing_MovementSht', $IPSShadowing_MovementSht, "", $IPSShadowing_MovementCol, true);
	CreateProfile_Associations ('IPSShadowing_MovementMar', $IPSShadowing_MovementMar, "", $IPSShadowing_MovementCol, true);

	CreateProfile_Count        ('IPSShadowing_Step',       1, 1,   4,     null, "",    null);
	CreateProfile_Count        ('IPSShadowing_Priority',   1, 1,   10,    null, "",    null);
	CreateProfile_Count        ('IPSShadowing_TempDelta',  1, 1,   5,     null, " °C", null);
	CreateProfile_Count        ('IPSShadowing_Position',   0, 1,   100,   null, "%",   null);

	CreateProfile_Associations ('IPSShadowing_TempLevelOutShadow', array(22=>'Aussen >= 22°C', 23=>'>= 23°C', 24=>'>= 24°C', 25=>'>= 25°C', 26=>'>= 26°C', c_TempLevel_Ignore=>'Ignorieren'));
	CreateProfile_Associations ('IPSShadowing_TempLevelInShadow',  array(22=>'Innen >= 22°C',  23=>'>= 23°C', 24=>'>= 24°C', 25=>'>= 25°C', 26=>'>= 26°C', c_TempLevel_Ignore=>'Ignorieren'));
	CreateProfile_Associations ('IPSShadowing_TempLevelOutClose',  array(22=>'Aussen >= 22°C', 23=>'>= 23°C', 24=>'>= 24°C', 25=>'>= 25°C', 26=>'>= 26°C', c_TempLevel_Ignore=>'Ignorieren'));
	CreateProfile_Associations ('IPSShadowing_TempLevelInClose',   array(22=>'Innen >= 22°C',  23=>'>= 23°C', 24=>'>= 24°C', 25=>'>= 25°C', 26=>'>= 26°C', c_TempLevel_Ignore=>'Ignorieren'));
	CreateProfile_Associations ('IPSShadowing_TempLevelOutOpen',   array(22=>'Aussen <= 22°C', 23=>'<= 23°C', 24=>'<= 24°C', 25=>'<= 25°C', 26=>'<= 26°C', c_TempLevel_Ignore=>'Ignorieren'));
	CreateProfile_Associations ('IPSShadowing_TempLevelInOpen',    array(22=>'Innen <= 22°C',  23=>'<= 23°C', 24=>'<= 24°C', 25=>'<= 25°C', 26=>'<= 26°C', c_TempLevel_Ignore=>'Ignorieren'));
	CreateProfile_Count        ('IPSShadowing_Brightness', 0, 2000, 100000, null, " Lux",    null);

	CreateProfile_Count        ('IPSShadowing_AzimuthBgn', 0, 5,   360,   null, " °",  null);
	CreateProfile_Count        ('IPSShadowing_AzimuthEnd', 0, 5,   360,   null, " °",  null);
	CreateProfile_Count        ('IPSShadowing_Elevation',  0, 1,   90,    null, " °",  null);
	CreateProfile_Count        ('IPSShadowing_Date',       1, 1,   12,    null, "%",   null);

	CreateProfile_Associations ('IPSShadowing_TimeMode',   array('individuelle Zeit', 'Dämmerung', 'Dämmerung (begrenzt)'));
	CreateProfile_Count        ('IPSShadowing_TimeOffset', -120, 5, 120, null, " Min",  null);

	CreateProfile_Count        ('IPSShadowing_Wind', 0, 10, 100, null, " kmh",    null);

	CreateProfile_Associations ('IPSShadowing_ProfileBgnOfDay',   array());
	CreateProfile_Associations ('IPSShadowing_ProfileEndOfDay',   array());
	CreateProfile_Associations ('IPSShadowing_ProfileTemp',       array());
	CreateProfile_Associations ('IPSShadowing_ProfileWeather',    array());
	CreateProfile_Associations ('IPSShadowing_ProfileSun',        array());

	CreateProfile_Associations ('IPSShadowing_ScenarioSelect',    array());
	CreateProfile_Associations ('IPSShadowing_ScenarioActivate',  array(0=>'Inaktiv'));
	CreateProfile_Associations ('IPSShadowing_ScenarioShutter',   array(
													c_MovementId_NoAction		=> c_Movement_NoAction,
													c_MovementId_Closed			=> c_Movement_Closed,
													c_MovementId_90				=> c_Movement_90,
													c_MovementId_75				=> c_Movement_75,
													c_MovementId_50				=> c_Movement_50,
													c_MovementId_Opened			=> c_Movement_Opened,
													c_MovementId_Stop				=> c_Movement_Stop),"", $IPSShadowing_MovementCol, true);
	CreateProfile_Associations ('IPSShadowing_ScenarioJalousie',  array(
													c_MovementId_NoAction		=> c_Movement_NoAction,
													c_MovementId_Shadowing		=> c_Movement_Shadowing,
													c_MovementId_Dimout			=> c_Movement_Dimout,
													c_MovementId_Opened			=> c_Movement_Opened,
													c_MovementId_Stop				=> c_Movement_Stop),"", $IPSShadowing_MovementCol, true);
	CreateProfile_Associations ('IPSShadowing_ScenarioMarquees',  array(
													c_MovementId_NoAction		=> c_Movement_NoAction,
													c_MovementId_MovedOut		=> c_Movement_MovedOut,
													c_MovementId_75				=> c_Movement_75,
													c_MovementId_50				=> c_Movement_50,
													c_MovementId_MovedIn			=> c_Movement_MovedIn,
													c_MovementId_Stop				=> c_Movement_Stop),"", $IPSShadowing_MovementCol, true);

	$CategoryIdDevices	= CreateCategory('Devices', $CategoryIdData, 20);



	// Profile Manager
	// ====================================================================================================================================
	$CategoryIdProfiles         = CreateCategory('Profiles',   $CategoryIdData, 30);
	$CategoryIdProfilesTemp     = CreateCategory(  'Temp',       $CategoryIdProfiles, 10);
	$CategoryIdProfilesSun      = CreateCategory(  'Sun',        $CategoryIdProfiles, 20);
	$CategoryIdProfilesWeather  = CreateCategory(  'Weather',    $CategoryIdProfiles, 30);
	$CategoryIdProfilesBgnOfDay = CreateCategory(  'BgnOfDay',   $CategoryIdProfiles, 40);
	$CategoryIdProfilesEndOfDay = CreateCategory(  'EndOfDay',   $CategoryIdProfiles, 50);

	$CategoryIdProfileManager   = CreateCategory('ProfileManager', $CategoryIdData, 35);
	$ControlIdProfileTempSelect       = CreateVariable(c_Control_ProfileTempSelect,     1 /*Integer*/, $CategoryIdProfileManager, 10, 'IPSShadowing_ProfileTemp',    $ScriptIdChangeSettings, 0, 'Temperature');
	$ControlIdProfileSunSelect        = CreateVariable(c_Control_ProfileSunSelect,      1 /*Integer*/, $CategoryIdProfileManager, 20, 'IPSShadowing_ProfileSun',     $ScriptIdChangeSettings, 0, 'Sun');
	$ControlIdProfileWeatherSelect    = CreateVariable(c_Control_ProfileWeatherSelect,  1 /*Integer*/, $CategoryIdProfileManager, 30, 'IPSShadowing_ProfileWeather', $ScriptIdChangeSettings, 0, 'Drops');
	$ControlIdProfileBgnOfDaySelect   = CreateVariable(c_Control_ProfileBgnOfDaySelect, 1 /*Integer*/, $CategoryIdProfileManager, 40, 'IPSShadowing_ProfileBgnOfDay', $ScriptIdChangeSettings, 0, 'Clock');
	$ControlIdProfileEndOfDaySelect   = CreateVariable(c_Control_ProfileEndOfDaySelect, 1 /*Integer*/, $CategoryIdProfileManager, 50, 'IPSShadowing_ProfileEndOfDay', $ScriptIdChangeSettings, 0, 'Clock');
	$CategoryIdProfileTempDisplay     = CreateCategory('DisplayTemp',     $CategoryIdProfileManager, 100);
	$CategoryIdProfileSunDisplay      = CreateCategory('DisplaySun',      $CategoryIdProfileManager, 110);
	$CategoryIdProfileWeatherDisplay  = CreateCategory('DisplayWeather',  $CategoryIdProfileManager, 120);
	$CategoryIdProfileBgnOfDayDisplay = CreateCategory('DisplayBgnOfDay', $CategoryIdProfileManager, 130);
	$CategoryIdProfileEndOfDayDisplay = CreateCategory('DisplayEndOfDay', $CategoryIdProfileManager, 140);
	$CategoryIdProfileSunGraphs       = CreateCategory('GraphsSun', $CategoryIdProfileManager, 200);
	$MediaIdAzimuth                   = CreateMedia ('Sonnenstand', $CategoryIdProfileSunGraphs, IPS_GetKernelDir().'media\\IPSShadowing_Azimuth.gif', false, 1, 'Sun');

	//++Migration v2.50.2 --> 2.50.3
	$categoryIdTempProfiles      = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.Temp');
	$categoryIdListTempProfiles  = IPS_GetChildrenIDs($categoryIdTempProfiles);
	foreach ($categoryIdListTempProfiles as $categoryIdTempProfile) {
		CreateVariable(c_Control_TempLevelOutShadow, 1 /*Integer*/,  $categoryIdTempProfile, 10, 'IPSShadowing_TempLevelOutShadow', $ScriptIdChangeSettings, c_TempLevel_Ignore, 'Temperature');
		CreateVariable(c_Control_TempLevelInShadow,  1 /*Integer*/,  $categoryIdTempProfile, 20, 'IPSShadowing_TempLevelInShadow',  $ScriptIdChangeSettings, c_TempLevel_Ignore, 'Temperature');
		CreateVariable(c_Control_TempLevelOutClose,  1 /*Integer*/,  $categoryIdTempProfile, 30, 'IPSShadowing_TempLevelOutClose',  $ScriptIdChangeSettings, c_TempLevel_Ignore, 'Temperature');
		CreateVariable(c_Control_TempLevelInClose,   1 /*Integer*/,  $categoryIdTempProfile, 40, 'IPSShadowing_TempLevelInClose',   $ScriptIdChangeSettings, c_TempLevel_Ignore, 'Temperature');
		CreateVariable(c_Control_TempLevelOutOpen,   1 /*Integer*/,  $categoryIdTempProfile, 50, 'IPSShadowing_TempLevelOutOpen',   $ScriptIdChangeSettings, c_TempLevel_Ignore, 'Temperature');
		CreateVariable(c_Control_TempLevelInOpen,    1 /*Integer*/,  $categoryIdTempProfile, 60, 'IPSShadowing_TempLevelInOpen',    $ScriptIdChangeSettings, c_TempLevel_Ignore, 'Temperature');
		$variableId = @IPS_GetObjectIDByName('TempDiffClosing',   $categoryIdTempProfile);  if ($variableId!==false) { IPS_DeleteVariable($variableId); }
		$variableId = @IPS_GetObjectIDByName('TempDiffShadowing', $categoryIdTempProfile);  if ($variableId!==false) { IPS_DeleteVariable($variableId); }
		$variableId = @IPS_GetObjectIDByName('TempDiffOpening',   $categoryIdTempProfile);  if ($variableId!==false) { IPS_DeleteVariable($variableId); }

		//++Migration v2.50.15 --> 2.50.16
		CreateVariable(c_Control_BrightnessLow,      1 /*Integer*/,  $categoryIdTempProfile, 65, 'IPSShadowing_Brightness',         $ScriptIdChangeSettings, 0,         'Sun');
	}
	$linkId = @IPS_GetObjectIDByName('Differenz Beschattung', $CategoryIdProfileTempDisplay); if ($linkId!==false) { IPS_DeleteLink($linkId); }
	$linkId = @IPS_GetObjectIDByName('Differenz Abdunkelung', $CategoryIdProfileTempDisplay); if ($linkId!==false) { IPS_DeleteLink($linkId); }
	$linkId = @IPS_GetObjectIDByName('Differenz Öffnen',      $CategoryIdProfileTempDisplay); if ($linkId!==false) { IPS_DeleteLink($linkId); }
	@IPS_DeleteVariableProfile('IPSShadowing_TempDiffShadowing');
	@IPS_DeleteVariableProfile('IPSShadowing_TempDiffClosing');
	@IPS_DeleteVariableProfile('IPSShadowing_TempDiffOpening');

	//++Migration v2.50.15 --> 2.50.16
	$id = @IPS_GetObjectIdByName("Helligkeits Grenze", $CategoryIdProfileTempDisplay);
	if ($id!==false) {
		EmptyCategory($id);
		IPS_DeleteInstance($id);
	}
	$profileId = GetValue($ControlIdProfileTempSelect);
	if ($profileId<>0) {
		$profile   = new IPSShadowing_ProfileTemp($profileId);
		$profile->Display($CategoryIdProfileTempDisplay);
	}

	CreateProfile_Associations ('IPSShadowing_WindBeaufort',   array(
						0 => 'Windstille (0 km/h)',
						1 => 'leiser Zug (2 km/h)',
						2 => 'leichte Brise (6 km/h)',
						3 => 'schwache Brise (12 km/h)',
						4 => 'mäßige Brise (20 km/h)',
						5 => 'frischer Wind (29 km/h)',
						6 => 'starker Wind (39 km/h)',
						7 => 'steifer Wind (50 km/h)',
						8 => 'stürmischer Wind (62 km/h)',
						9 => 'Sturm (75 km/h)',
						10 => 'schwerer Sturm (89 km/h)',
						11 => 'orkanartiger Sturm (103 km/h)',
						12 => 'Orkan (117 km/h)'
						 ));

	$profiles=IPS_GetChildrenIDs($CategoryIdProfilesWeather);
	foreach ($profiles as $profileId) {
		$windlevelID=IPS_GetObjectIDByIdent("WindLevel", $profileId);
		$variableinfo=IPS_GetVariable($windlevelID);
		$customprofile=$variableinfo['VariableCustomProfile'];
		$value=GetValue($windlevelID);
		if (defined('IPSSHADOWING_WINDLEVEL_CLASSIFICATION') and IPSSHADOWING_WINDLEVEL_CLASSIFICATION) {
			if ($customprofile=="IPSShadowing_Wind") {
				IPS_SetVariableCustomProfile ($windlevelID,'IPSShadowing_WindBeaufort');
				SetValue($windlevelID,intval($value/3.6));
			}
		} else {
			if ($customprofile=="IPSShadowing_WindBeaufort") {
				IPS_SetVariableCustomProfile ($windlevelID,'IPSShadowing_Wind');
				SetValue($windlevelID,round($value*3.6,-1));
			}
		}
	}

	$profileManager = new IPSShadowing_ProfileManager();
	$profileManager->AssignAllProfileAssociations();

	$Profiles = IPS_GetChildrenIDs($CategoryIdProfilesTemp);
	if (count($Profiles)==0) {
		$profileManager->CreateTemp('Standard');
	}
	$Profiles = IPS_GetChildrenIDs($CategoryIdProfilesSun);
	if (count($Profiles)==0) {
		$profileManager->CreateSun('Süden');
	}
	$Profiles = IPS_GetChildrenIDs($CategoryIdProfilesWeather);
	if (count($Profiles)==0) {
		$profileManager->CreateWeather('Standard');
	} else {
	}
	$Profiles = IPS_GetChildrenIDs($CategoryIdProfilesBgnOfDay);
	if (count($Profiles)==0) {
		$profileManager->CreateBgnOfDay('Dämmerung');
	}
	$Profiles = IPS_GetChildrenIDs($CategoryIdProfilesEndOfDay);
	if (count($Profiles)==0) {
		$profileManager->CreateEndOfDay('Dämmerung');
	}
	// Scenario Manager
	// ====================================================================================================================================
	$CategoryIdScenarios = CreateCategory('Scenarios', $CategoryIdData, 40);
	$CategoryIdScenarioManager = CreateCategory('ScenarioManager', $CategoryIdData, 45);
	$ControlIdScenarioSelect   = CreateVariable(c_Control_ScenarioSelect,   1 /*Integer*/, $CategoryIdScenarioManager, 10, 'IPSShadowing_ScenarioSelect',  $ScriptIdChangeSettings, 0, 'Shutter');
	$ControlIdScenarioActivate = CreateVariable(c_Control_ScenarioActivate, 1 /*Integer*/, $CategoryIdScenarioManager, 20, 'IPSShadowing_ScenarioActivate', $ScriptIdChangeSettings, 0, 'Shutter');
	$CategoryIdScenarioDisplay = CreateCategory('Display', $CategoryIdScenarioManager, 20);

	$scenarioManager = new IPSShadowing_ScenarioManager();
	$scenarioManager->AssignAllScenarioAssociations();

	$ScenarioId = @IPS_GetObjectIDByName('Alle Schliessen', $CategoryIdScenarios);
	if ($ScenarioId===false) {
		echo 'Create Scenario "Alle Schliessen"'.PHP_EOL;
		$scenarioId = $scenarioManager->Create('Alle Schliessen', c_MovementId_Closed);
		$scenario = new IPSShadowing_Scenario($scenarioId);
		$scenario->ResetEditMode();

	}
	$ScenarioId = @IPS_GetObjectIDByName('Alle Öffnen', $CategoryIdScenarios);
	if ($ScenarioId===false) {
		echo 'Create Scenario "Alle Öffnen"'.PHP_EOL;
		$scenarioManager->Create('Alle Öffnen', c_MovementId_Opened);
		$scenario = new IPSShadowing_Scenario($scenarioId);
		$scenario->ResetEditMode();
	}

	// Settings
	// ====================================================================================================================================
	$CategoryIdSettings	   = CreateCategory('Settings', $CategoryIdData, 30);
	$ControlIdMsgPrioTemp  = CreateVariable(c_Control_MsgPrioTemp,  1 /*Integer*/, $CategoryIdSettings,  10, 'IPSShadowing_Priority', $ScriptIdChangeSettings, 2);
	$ControlIdMsgPrioProg  = CreateVariable(c_Control_MsgPrioProg,  1 /*Integer*/, $CategoryIdSettings,  20, 'IPSShadowing_Priority', $ScriptIdChangeSettings, 4);

	// Logging
	// ====================================================================================================================================
	$CategoryIdLog	       = CreateCategory('Log', $CategoryIdData, 40);
	$ControlIdLog          = CreateVariable('LogMessages',  3 /*String*/,  $CategoryIdLog, 10, '~HTMLBox', null, "");
	$ControlIdLogId        = CreateVariable('LogId',        1 /*Integer*/, $CategoryIdLog, 20, '',         null, 0);

	// Shadowing Devices
	// ====================================================================================================================================
	$DeviceConfig        = get_ShadowingConfiguration();
	$Idx                 = 10;
	foreach ($DeviceConfig as $DeviceName=>$DeviceData) {
		$ShadowingType = $DeviceConfig[$DeviceName][c_Property_ShadowingType];
		$DeviceId      = CreateCategory($DeviceName, $CategoryIdDevices, $Idx);

		$ControlIdDisplay          = CreateVariable(c_Control_Display,        3 /*String*/,  $DeviceId,  50, '~String',                      null,                    'Manuell', 'Information');
		$ControlIdStepsToDo        = CreateVariable(c_Control_StepsToDo,      3 /*String*/,  $DeviceId,  60, '~String',                      null,                    '');
		$ControlIdStep             = CreateVariable(c_Control_Step,           1 /*Integer*/, $DeviceId,  70, 'IPSShadowing_Step',            null,                    -1);
		$ControlIdStartTime        = CreateVariable(c_Control_StartTime,      1 /*Integer*/, $DeviceId,  80, '~UnixTimestamp',               null,                    -1);
		$ControlIdProgramTime      = CreateVariable(c_Control_ProgramTime,    1 /*Integer*/, $DeviceId,  85, '~UnixTimestamp',               null,                    -1);
		$ControlIdPosition         = CreateVariable(c_Control_Position,       1 /*Integer*/, $DeviceId,  90, 'IPSShadowing_Position',        $ScriptIdChangeSettings, 0,       'Intensity');
		$ControlIdManualChange     = CreateVariable(c_Control_ManualChange,   0 /*Boolean*/, $DeviceId, 110, '~Switch',                      $ScriptIdChangeSettings, false,   'Warning');
		$ControlIdTempChange       = CreateVariable(c_Control_TempChange,     0 /*Boolean*/, $DeviceId, 120, '~Switch',                      null                   , false,   'Warning');
		$ControlIdTempLastPos      = CreateVariable(c_Control_TempLastPos,    1 /*Integer*/, $DeviceId, 125, '',                             null                   , false,   'Information');
		$ControlIdAutomatic        = CreateVariable(c_Control_Automatic ,     0 /*Boolean*/, $DeviceId, 130, '~Switch',                      $ScriptIdChangeSettings, false,   'Power');

		$ControlIdTempProfile      = CreateVariable(c_Control_ProfileTemp,    1 /*Integer*/, $DeviceId, 330, 'IPSShadowing_ProfileTemp',     $ScriptIdChangeSettings, 0,       'Temperature');
		$ControlIdSunProfile       = CreateVariable(c_Control_ProfileSun,     1 /*Integer*/, $DeviceId, 340, 'IPSShadowing_ProfileSun',      $ScriptIdChangeSettings, 0,       'Sun');
		$ControlIdBgnOfDayProfile  = CreateVariable(c_Control_ProfileBgnOfDay,1 /*Integer*/, $DeviceId, 350, 'IPSShadowing_ProfileBgnOfDay', $ScriptIdChangeSettings, 0,       'Clock');
		$ControlIdEndOfDayProfile  = CreateVariable(c_Control_ProfileEndOfDay,1 /*Integer*/, $DeviceId, 360, 'IPSShadowing_ProfileEndOfDay', $ScriptIdChangeSettings, 0,       'Clock');
		$ControlIdProfileInfo      = CreateVariable(c_Control_ProfileInfo,    3 /*String*/,  $DeviceId, 380, '~String',                      null,                    '',      'Information');

		if ($ShadowingType==c_ShadowingType_Shutter) {
			$ControlIdMovement         = CreateVariable(c_Control_Movement,       1 /*Integer*/, $DeviceId,  10, 'IPSShadowing_MovementSht', $ScriptIdChangeSettings, c_MovementId_Stop,  'Shutter');
			$ControlIdProgramNight     = CreateVariable(c_Control_ProgramNight,   1 /*Integer*/, $DeviceId, 200, 'IPSShadowing_ProgNigSht',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Bed');
			$ControlIdProgramDay       = CreateVariable(c_Control_ProgramDay,     1 /*Integer*/, $DeviceId, 210, 'IPSShadowing_ProgDaySht',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Sun');
			$ControlIdProgramTemp      = CreateVariable(c_Control_ProgramTemp,    1 /*Integer*/, $DeviceId, 220, 'IPSShadowing_ProgTmpSht',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Temperature');
			$ControlIdProgramPresent   = CreateVariable(c_Control_ProgramPresent, 1 /*Integer*/, $DeviceId, 230, 'IPSShadowing_ProgPreSht',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Motion');
		} elseif ($ShadowingType==c_ShadowingType_Jalousie) {
			$ControlIdMovement         = CreateVariable(c_Control_Movement,       1 /*Integer*/, $DeviceId,  10, 'IPSShadowing_MovementJal', $ScriptIdChangeSettings, c_MovementId_Stop,  'Shutter');
			$ControlIdProgramNight     = CreateVariable(c_Control_ProgramNight,   1 /*Integer*/, $DeviceId, 200, 'IPSShadowing_ProgNigJal',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Bed');
			$ControlIdProgramDay       = CreateVariable(c_Control_ProgramDay,     1 /*Integer*/, $DeviceId, 210, 'IPSShadowing_ProgDayJal',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Sun');
			$ControlIdProgramTemp      = CreateVariable(c_Control_ProgramTemp,    1 /*Integer*/, $DeviceId, 220, 'IPSShadowing_ProgTmpJal',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Temperature');
			$ControlIdProgramPresent   = CreateVariable(c_Control_ProgramPresent, 1 /*Integer*/, $DeviceId, 230, 'IPSShadowing_ProgPreJal',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Motion');
		} elseif ($ShadowingType==c_ShadowingType_Marquees) {
			$ControlIdMovement         = CreateVariable(c_Control_Movement,       1 /*Integer*/, $DeviceId,  10, 'IPSShadowing_MovementMar', $ScriptIdChangeSettings, c_MovementId_Stop,  'Shutter');
			$ControlIdProgramNight     = CreateVariable(c_Control_ProgramNight,   1 /*Integer*/, $DeviceId, 200, 'IPSShadowing_ProgNigMar',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Bed');
			$ControlIdProgramDay       = CreateVariable(c_Control_ProgramDay,     1 /*Integer*/, $DeviceId, 210, 'IPSShadowing_ProgDayMar',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Sun');
			$ControlIdProgramTemp      = CreateVariable(c_Control_ProgramTemp,    1 /*Integer*/, $DeviceId, 220, 'IPSShadowing_ProgTmpMar',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Temperature');
			$ControlIdProgramWeather   = CreateVariable(c_Control_ProgramWeather, 1 /*Integer*/, $DeviceId, 220, 'IPSShadowing_ProgWeaMar',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Drops');
			$ControlIdProgramPresent   = CreateVariable(c_Control_ProgramPresent, 1 /*Integer*/, $DeviceId, 230, 'IPSShadowing_ProgPreMar',  $ScriptIdChangeSettings, c_ProgramId_Manual, 'Motion');
			$ControlIdWeatherProfile   = CreateVariable(c_Control_ProfileWeather, 1 /*Integer*/, $DeviceId, 370, 'IPSShadowing_ProfileWeather', $ScriptIdChangeSettings, 0,       'Drops');
		} else {
		   throw new Exception('Unknown ShadowingType '.$ShadowingType);
		}
		$Idx = $Idx  + 10;
	}

	// Deletion of old Devices
	// -----------------------
	$deviceConfig        = get_ShadowingConfiguration();
	$categoryIdDevices   = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Devices');
	$deviceIds           = IPS_GetChildrenIDs($categoryIdDevices);
	foreach ($deviceIds as $deviceIdx=>$deviceId) {
	   $deviceName = IPS_GetName($deviceId);
	   if (!array_key_exists($deviceName, $deviceConfig)) {
	      echo 'Remove DeviceData of unknown Device='.$deviceName.PHP_EOL;
			EmptyCategory($deviceId);
			DeleteCategory($deviceId);
	   }
	}

	// Type Correction of Scenarios
	// ----------------------------
	$MovementIds = array(
        c_MovementId_Space          => c_Movement_Space,
        c_MovementId_NoAction        => c_Movement_NoAction,
        c_MovementId_Up              => c_Movement_Up,
        c_MovementId_Down              => c_Movement_Down,
        c_MovementId_Stop              => c_Movement_Stop,
        c_MovementId_90              => c_Movement_90,
        c_MovementId_75              => c_Movement_75,
        c_MovementId_50              => c_Movement_50,
        c_MovementId_Closed          => c_Movement_Closed,
        c_MovementId_Opened          => c_Movement_Opened,
        c_MovementId_Dimout          => c_Movement_Dimout,
        c_MovementId_Shadowing          => c_Movement_Shadowing,
        c_MovementId_MovedOut      => c_Movement_MovedOut,
        c_MovementId_MovedIn          => c_Movement_MovedIn,
        c_MovementId_MovingOut     => c_Movement_MovingOut,
        c_MovementId_MovingIn      => c_Movement_MovingIn,
		);
	$categoryIdDevices   = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Devices');
	$categoryIdScenarios = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Scenarios');
	$scenarios           = IPS_GetChildrenIDs($categoryIdScenarios);
	$deviceConfig        = get_ShadowingConfiguration();
	foreach ($scenarios as $scenarioId) {
		echo 'Found Scenario='.IPS_GetName($scenarioId).PHP_EOL;
		foreach ($deviceConfig as $deviceIdent=>$deviceData) {
			$controlId = @IPS_GetObjectIDByIdent($deviceIdent, $scenarioId);
			if ($controlId!==false) {
				$movementId = GetValue($controlId);
				$invalid = false;
				switch ($deviceData[c_Property_ShadowingType]) {
					case c_ShadowingType_Jalousie:
						if ($movementId<>c_MovementId_NoAction and $movementId<>c_MovementId_Shadowing and $movementId<>c_MovementId_Dimout
							and $movementId<>c_MovementId_Opened and $movementId<>c_MovementId_Stop) {
                            $invalid = true;
						}
						break;
					case c_ShadowingType_Shutter:
						if ($movementId<>c_MovementId_NoAction and $movementId<>c_MovementId_Closed and $movementId<>c_MovementId_90
							and $movementId<>c_MovementId_50 and $movementId<>c_MovementId_50
							and $movementId<>c_MovementId_Opened and $movementId<>c_MovementId_Stop) {
                            $invalid = true;
						}
						break;
					case c_ShadowingType_Marquees:
						if ($movementId<>c_MovementId_NoAction and $movementId<>c_MovementId_MovedOut and $movementId<>c_MovementId_MovedIn
							and $movementId<>c_MovementId_75 and $movementId<>c_MovementId_50 and $movementId<>c_MovementId_Stop) {
							$invalid = true;
						}
						break;
				}
				if ($invalid) {
					echo '   INVALID --> '.IPS_GetName($controlId).'='.$MovementIds[$movementId].PHP_EOL;
					echo '   --> Repair, Set "NoAction"'.PHP_EOL;
					SetValue($controlId, c_MovementId_NoAction);
				} else {
					echo '   OK --> '.IPS_GetName($controlId).'='.$MovementIds[$movementId].PHP_EOL;
				}
			}
		}
	}

	// Correction of Profiles
	// ----------------------
	$profileManager = new IPSShadowing_ProfileManager();
	$profileManager->AssignAllProfileAssociations();
	$profileManager->CorrectDeletedDeviceProfiles();

	// Register Events for Device Synchronization
	// ------------------------------------------
	IPSUtils_Include ('IPSMessageHandler.class.php', 'IPSLibrary::app::core::IPSMessageHandler');
	$messageHandler = new IPSMessageHandler();
	foreach ($DeviceConfig as $DeviceName=>$DeviceData) {
		$component = $DeviceConfig[$DeviceName][c_Property_Component];
		$componentParams = explode(',', $component);
		$componentClass = $componentParams[0];

		// Homematic
		if ($componentClass=='IPSComponentShutter_Homematic') {
			$instanceId = IPSUtil_ObjectIDByPath($componentParams[1]);
			$variableId = @IPS_GetObjectIDByName('LEVEL', $instanceId);
			if ($variableId===false) {
				$moduleManager->LogHandler()->Log('Variable with Name LEVEL could NOT be found for Homematic Instance='.$instanceId);
			} else {
				$moduleManager->LogHandler()->Log('Register OnChangeEvent vor Homematic Instance='.$instanceId);
				$messageHandler->RegisterOnChangeEvent($variableId, $component, 'IPSModuleShutter_IPSShadowing,');
			}
		} else {
			$moduleManager->LogHandler()->Log('Found Component '.$componentClass);
		}
	}

	CreateTimer_OnceADay ('Reset', $ScriptIdResetTimer, 0, 5);
	CreateTimer_CyclicBySeconds ('Refresh', $ScriptIdRefreshTimer, 1, false);
	CreateTimer_CyclicByMinutes ('Program', $ScriptIdProgramTimer, 5);

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$categoryId_WebFront                = CreateCategoryPath($WFC10_Path);
		EmptyCategory($categoryId_WebFront);
		DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem);
		DeleteWFCItems($WFC10_ConfigId, 'ShadowTP');
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,  $WFC10_TabPaneParent, $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);

		// WebFront Overview
		$WebFrontOverviewId        = CreateCategory('Overview',  $categoryId_WebFront,    10);
		$WebFrontOverviewTop1      = CreateCategory(  'Top1',    $WebFrontOverviewId,    10);
		$WebFrontOverviewTop2      = CreateCategory(  'Top2',    $WebFrontOverviewId,    20);
		$WebFrontOverviewBottom1   = CreateCategory(  'Bottom1', $WebFrontOverviewId,    30);
		$WebFrontOverviewBottom2   = CreateCategory(  'Bottom2', $WebFrontOverviewId,    40);
		$UniqueId                  = date('H:i');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OV',                 $WFC10_TabPaneItem.'',          10, $WFC10_TabName1, $WFC10_TabIcon1, 0 /*Horizontal*/, 160 /*Height*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTop',              $WFC10_TabPaneItem.'_OV',       10, '', '', 1 /*Vertical*/, 300 /*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVBottom',           $WFC10_TabPaneItem.'_OV',       20, '', '', 1 /*Vertical*/, 430 /*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTop1'.$UniqueId,   $WFC10_TabPaneItem.'_OVTop',    10, 'Top1', '', $WebFrontOverviewTop1 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVTop2'.$UniqueId,   $WFC10_TabPaneItem.'_OVTop',    20, 'Top2', '', $WebFrontOverviewTop2 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVBottom1'.$UniqueId,$WFC10_TabPaneItem.'_OVBottom', 10, 'Bottom1', '', $WebFrontOverviewBottom1 /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OVBottom2'.$UniqueId,$WFC10_TabPaneItem.'_OVBottom', 20, 'Bottom2', '', $WebFrontOverviewBottom2 /*BaseId*/, 'false' /*BarBottomVisible*/);
		$Idx = 10;
		foreach ($DeviceConfig as $DeviceIdent=>$DeviceData) {
			$DeviceId  = IPS_GetObjectIDByIdent($DeviceIdent, $CategoryIdDevices);
			$DeviceName = $DeviceData[c_Property_Name];
			CreateLink($DeviceName,           IPS_GetObjectIDByIdent(c_Control_Movement, $DeviceId), $WebFrontOverviewTop1, $Idx);
			CreateLinkByDestination('Status', IPS_GetObjectIDByIdent(c_Control_Display,  $DeviceId), $WebFrontOverviewTop2, $Idx);
			$Idx = $Idx + 10;
		}
		CreateLink('Szenarien',       $ControlIdScenarioActivate, $WebFrontOverviewBottom1, 10);
		CreateLink('Automatic Ein',   $ScriptIdAutomaticOn,       $WebFrontOverviewBottom2, 10);
		CreateLink('Automatic Aus',   $ScriptIdAutomaticOff,      $WebFrontOverviewBottom2, 20);
		CreateLink('Automatic Reset', $ScriptIdAutomaticReset,    $WebFrontOverviewBottom2, 30);

		// Shadowing Devices
		CreateWFCItemTabPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_Devices', $WFC10_TabPaneItem, 20, 'Beschattungs Elemente', 'Information');
		$Idx = 10;
		foreach ($DeviceConfig as $DeviceIdent=>$DeviceData) {
			$DeviceId  = IPS_GetObjectIDByIdent($DeviceIdent, $CategoryIdDevices);
			$DeviceName = $DeviceData[c_Property_Name];

			$WebFrontDetailId             = CreateCategory($DeviceName, $categoryId_WebFront, 100+$Idx);
			$WebFrontDetailTopId          = CreateCategory("Top",         $WebFrontDetailId, 100+$Idx);
			$WebFrontDetailBottomLeftId   = CreateCategory("BottomLeft",  $WebFrontDetailId, 100+$Idx);
			$WebFrontDetailBottomRightId  = CreateCategory("BottomRight", $WebFrontDetailId, 100+$Idx);
			CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Devices$DeviceName",                        $WFC10_TabPaneItem.'_Devices', 100+$Idx, $DeviceName, '', 0 /*Horizontal*/, 315 /*Hight*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
			CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Devices$DeviceName"."Top".$UniqueId,        $WFC10_TabPaneItem."_Devices$DeviceName", 10, '', '', $WebFrontDetailTopId /*BaseId*/, 'false' /*BarBottomVisible*/);
			CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Devices$DeviceName"."Bottom",               $WFC10_TabPaneItem."_Devices$DeviceName", 20, '', '', 1 /*Vertical*/, 65 /*Width*/, 0 /*Target=Pane1*/, 0/*UsePercentage*/, 'true');
			CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Devices$DeviceName"."BottomLeft".$UniqueId, $WFC10_TabPaneItem."_Devices$DeviceName"."Bottom", 10, '', '', $WebFrontDetailBottomLeftId  /*BaseId*/, 'false' /*BarBottomVisible*/);
			CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Devices$DeviceName"."BottomRight".$UniqueId,$WFC10_TabPaneItem."_Devices$DeviceName"."Bottom", 10, '', '', $WebFrontDetailBottomRightId /*BaseId*/, 'false' /*BarBottomVisible*/);

			CreateLink('Steuerung',           IPS_GetObjectIDByIdent(c_Control_Movement,        $DeviceId),  $WebFrontDetailTopId, 10);
			$ProgramDeviceId = CreateDummyInstance("Programme", $WebFrontDetailTopId, 20);
			CreateLink('Programm Nacht',      IPS_GetObjectIDByIdent(c_Control_ProgramNight,    $DeviceId),  $ProgramDeviceId, 10);
			CreateLink('Programm Tag',        IPS_GetObjectIDByIdent(c_Control_ProgramDay,      $DeviceId),  $ProgramDeviceId, 20);
			CreateLink('Programm Temperatur', IPS_GetObjectIDByIdent(c_Control_ProgramTemp,     $DeviceId),  $ProgramDeviceId, 30);
			CreateLink('Programm Anwesenend', IPS_GetObjectIDByIdent(c_Control_ProgramPresent,  $DeviceId),  $ProgramDeviceId, 40);
			$controlIdProgramWeather = @IPS_GetObjectIDByIdent(c_Control_ProgramWeather, $DeviceId);
			if ($controlIdProgramWeather!==false) {
				CreateLink('Programm Wetter', $controlIdProgramWeather,  $ProgramDeviceId, 35);
			}

			CreateLink('Tagesbeginn Profil',  IPS_GetObjectIDByIdent(c_Control_ProfileBgnOfDay, $DeviceId),  $WebFrontDetailBottomLeftId, 10);
			CreateLink('Tagesende Profil',    IPS_GetObjectIDByIdent(c_Control_ProfileEndOfDay, $DeviceId),  $WebFrontDetailBottomLeftId, 20);
			CreateLink('Temperatur Profil',   IPS_GetObjectIDByIdent(c_Control_ProfileTemp,     $DeviceId),  $WebFrontDetailBottomLeftId, 30);
			CreateLink('Sonnenstand Profil',  IPS_GetObjectIDByIdent(c_Control_ProfileSun,      $DeviceId),  $WebFrontDetailBottomLeftId, 40);
			if ($controlIdProgramWeather!==false) {
				CreateLink('Wetter Profil',       IPS_GetObjectIDByIdent(c_Control_ProfileWeather,  $DeviceId),  $WebFrontDetailBottomLeftId, 50);
			}
			CreateLink('Profil Information',  IPS_GetObjectIDByIdent(c_Control_ProfileInfo,     $DeviceId),  $WebFrontDetailBottomLeftId, 60);

			CreateLink('Automatik',           IPS_GetObjectIDByIdent(c_Control_Automatic,       $DeviceId),  $WebFrontDetailBottomRightId, 10);
			CreateLink('Manueller Modus',     IPS_GetObjectIDByIdent(c_Control_ManualChange,    $DeviceId),  $WebFrontDetailBottomRightId, 20);
			CreateLink('Temperatur Modus',    IPS_GetObjectIDByIdent(c_Control_TempChange,      $DeviceId),  $WebFrontDetailBottomRightId, 25);
			CreateLink('Status',              IPS_GetObjectIDByIdent(c_Control_Display,         $DeviceId),  $WebFrontDetailBottomRightId, 30);
			CreateLink('Position',            IPS_GetObjectIDByIdent(c_Control_Position,        $DeviceId),  $WebFrontDetailBottomRightId, 40);

			$Idx = $Idx + 10;
		}

		// Scenarien
		$WebFrontScenariosId      = CreateCategory('Scenarien', $categoryId_WebFront, 30);
		$WebFrontScenariosIdTopL  = CreateCategory(  'TopLeft',  $WebFrontScenariosId, 10);
		$WebFrontScenariosIdTopR  = CreateCategory(  'TopRight', $WebFrontScenariosId, 20);
		$WebFrontScenariosIdBot   = CreateCategory(  'Bottom',   $WebFrontScenariosId, 30);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Scenarios",    $WFC10_TabPaneItem, 30, 'Szenarien', 'Script', 0 /*Horizontal*/, 108 /*Hight*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_ScenariosTop", $WFC10_TabPaneItem."_Scenarios",    10, '', '', 1 /*Vertical*/, 300/*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_ScenariosBot", $WFC10_TabPaneItem."_Scenarios",    20, '', '', $CategoryIdScenarioDisplay /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_ScenariosTopL",$WFC10_TabPaneItem."_ScenariosTop", 10, '', '', $WebFrontScenariosIdTopL /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_ScenariosTopR",$WFC10_TabPaneItem."_ScenariosTop", 20, '', '', $WebFrontScenariosIdTopR /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateLink('Szenario Auswahl',  $ControlIdScenarioSelect,  $WebFrontScenariosIdTopL, 10);
		CreateLink('Neues Szenario',    $ScriptIdScenarioCreate,   $WebFrontScenariosIdTopR, 10);
		CreateLink('Szenario löschen',  $ScriptIdScenarioDelete,   $WebFrontScenariosIdTopR, 20);

		// Profiles
		$WebFrontProfilesId       = CreateCategory('Profiles', $categoryId_WebFront, 40);
		$WebFrontProfilesId1      = CreateCategory(  'ProfilesTemp', $WebFrontProfilesId, 10);
		$WebFrontProfilesId1TopL  = CreateCategory(    'TopLeft',  $WebFrontProfilesId1, 10);
		$WebFrontProfilesId1TopR  = CreateCategory(    'TopRight', $WebFrontProfilesId1, 20);
		$WebFrontProfilesId1BotR  = CreateCategory(    'BottomRight',   $WebFrontProfilesId1, 30);
		$WebFrontProfilesId2      = CreateCategory(  'ProfilesSun', $WebFrontProfilesId, 20);
		$WebFrontProfilesId2TopL  = CreateCategory(    'TopLeft',  $WebFrontProfilesId2, 10);
		$WebFrontProfilesId2TopR  = CreateCategory(    'TopRight', $WebFrontProfilesId2, 20);
		$WebFrontProfilesId2Bot   = CreateCategory(    'Bottom',   $WebFrontProfilesId2, 30);
		$WebFrontProfilesId3      = CreateCategory(  'ProfilesWeather', $WebFrontProfilesId, 30);
		$WebFrontProfilesId3TopL  = CreateCategory(    'TopLeft',  $WebFrontProfilesId3, 10);
		$WebFrontProfilesId3TopR  = CreateCategory(    'TopRight', $WebFrontProfilesId3, 20);
		$WebFrontProfilesId3Bot   = CreateCategory(    'Bottom',   $WebFrontProfilesId3, 30);
		$WebFrontProfilesId4      = CreateCategory(  'ProfilesBgnOfDay',     $WebFrontProfilesId, 40);
		$WebFrontProfilesId4TopL  = CreateCategory(    'TopLeft',  $WebFrontProfilesId4, 10);
		$WebFrontProfilesId4TopR  = CreateCategory(    'TopRight', $WebFrontProfilesId4, 20);
		$WebFrontProfilesId4BotR  = CreateCategory(    'BottomRight',   $WebFrontProfilesId4, 30);
		$WebFrontProfilesId5      = CreateCategory(  'ProfilesEndOfDay', $WebFrontProfilesId, 50);
		$WebFrontProfilesId5TopL  = CreateCategory(    'TopLeft',  $WebFrontProfilesId5, 10);
		$WebFrontProfilesId5TopR  = CreateCategory(    'TopRight', $WebFrontProfilesId5, 20);
		$WebFrontProfilesId5BotR  = CreateCategory(    'BottomRight',   $WebFrontProfilesId5, 30);
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem.'_Profiles', $WFC10_TabPaneItem, 40, 'Profile', 'Clock');

		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles1",    $WFC10_TabPaneItem.'_Profiles',     10, 'Temperatur', 'Temperature', 0 /*Horizontal*/, 108 /*Hight*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles1Top", $WFC10_TabPaneItem."_Profiles1",    10, '', '', 1 /*Vertical*/, 300/*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles1Bot", $WFC10_TabPaneItem."_Profiles1",    20, '', '', $CategoryIdProfileTempDisplay /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles1TopL",$WFC10_TabPaneItem."_Profiles1Top", 10, '', '', $WebFrontProfilesId1TopL /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles1TopR",$WFC10_TabPaneItem."_Profiles1Top", 20, '', '', $WebFrontProfilesId1TopR /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles2",    $WFC10_TabPaneItem.'_Profiles',     20, 'Sonnenstand', 'Sun', 0 /*Horizontal*/, 108 /*Hight*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles2Top", $WFC10_TabPaneItem."_Profiles2",    10, '', '', 1 /*Vertical*/, 300/*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles2Bot", $WFC10_TabPaneItem."_Profiles2",    20, '', '', 1 /*Vertical*/, 420/*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles2BotL",$WFC10_TabPaneItem."_Profiles2Bot", 10, '', '', $CategoryIdProfileSunDisplay /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles2BotR",$WFC10_TabPaneItem."_Profiles2Bot", 20, '', '', $CategoryIdProfileSunGraphs /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles2TopL",$WFC10_TabPaneItem."_Profiles2Top", 10, '', '', $WebFrontProfilesId2TopL /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles2TopR",$WFC10_TabPaneItem."_Profiles2Top", 20, '', '', $WebFrontProfilesId2TopR /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles3",    $WFC10_TabPaneItem.'_Profiles',     20, 'Wetter', 'Drops', 0 /*Horizontal*/, 108 /*Hight*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles3Top", $WFC10_TabPaneItem."_Profiles3",    10, '', '', 1 /*Vertical*/, 300/*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles3Bot", $WFC10_TabPaneItem."_Profiles3",    20, '', '', $CategoryIdProfileWeatherDisplay /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles3TopL",$WFC10_TabPaneItem."_Profiles3Top", 10, '', '', $WebFrontProfilesId3TopL /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles3TopR",$WFC10_TabPaneItem."_Profiles3Top", 20, '', '', $WebFrontProfilesId3TopR /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles4",    $WFC10_TabPaneItem.'_Profiles',     20, 'Tagesbeginn', 'Clock', 0 /*Horizontal*/, 108 /*Hight*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles4Top", $WFC10_TabPaneItem."_Profiles4",    10, '', '', 1 /*Vertical*/, 300/*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles4Bot", $WFC10_TabPaneItem."_Profiles4",    20, '', '', $CategoryIdProfileBgnOfDayDisplay /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles4TopL",$WFC10_TabPaneItem."_Profiles4Top", 10, '', '', $WebFrontProfilesId4TopL /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles4TopR",$WFC10_TabPaneItem."_Profiles4Top", 20, '', '', $WebFrontProfilesId4TopR /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles5",    $WFC10_TabPaneItem.'_Profiles',     30, 'Tagesende', 'Clock', 0 /*Horizontal*/, 108 /*Hight*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles5Top", $WFC10_TabPaneItem."_Profiles5",    10, '', '', 1 /*Vertical*/, 300/*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles5Bot", $WFC10_TabPaneItem."_Profiles5",    20, '', '', $CategoryIdProfileEndOfDayDisplay /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles5TopL",$WFC10_TabPaneItem."_Profiles5Top", 10, '', '', $WebFrontProfilesId5TopL /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Profiles5TopR",$WFC10_TabPaneItem."_Profiles5Top", 20, '', '', $WebFrontProfilesId5TopR /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateLink('Profil Auswahl',   $ControlIdProfileTempSelect,      $WebFrontProfilesId1TopL, 10);
		CreateLink('Neues Profil',     $ScriptIdProfileTempCreate,       $WebFrontProfilesId1TopR, 10);
		CreateLink('Profil löschen',   $ScriptIdProfileTempDelete,       $WebFrontProfilesId1TopR, 20);
		CreateLink('Profil Auswahl',   $ControlIdProfileSunSelect,       $WebFrontProfilesId2TopL, 10);
		CreateLink('Neues Profil',     $ScriptIdProfileSunCreate,        $WebFrontProfilesId2TopR, 10);
		CreateLink('Profil löschen',   $ScriptIdProfileSunDelete,        $WebFrontProfilesId2TopR, 20);
		CreateLink('Profil Auswahl',   $ControlIdProfileWeatherSelect,   $WebFrontProfilesId3TopL, 10);
		CreateLink('Neues Profil',     $ScriptIdProfileWeatherCreate,    $WebFrontProfilesId3TopR, 10);
		CreateLink('Profil löschen',   $ScriptIdProfileWeatherDelete,    $WebFrontProfilesId3TopR, 20);
		CreateLink('Profile Auswahl',  $ControlIdProfileBgnOfDaySelect,  $WebFrontProfilesId4TopL, 10);
		CreateLink('Neues Profil',     $ScriptIdProfileBgnOfDayCreate,   $WebFrontProfilesId4TopR, 10);
		CreateLink('Profil löschen',   $ScriptIdProfileBgnOfDayDelete,   $WebFrontProfilesId4TopR, 20);
		CreateLink('Profile Auswahl',  $ControlIdProfileEndOfDaySelect,  $WebFrontProfilesId5TopL, 10);
		CreateLink('Neues Profil',     $ScriptIdProfileEndOfDayCreate,   $WebFrontProfilesId5TopR, 10);
		CreateLink('Profil löschen',   $ScriptIdProfileEndOfDayDelete,   $WebFrontProfilesId5TopR, 20);

		// Common Settings
		$WebFrontSettingId  = CreateCategory('Settings', $categoryId_WebFront, 50);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_Settings', $WFC10_TabPaneItem, 50, 'Einstellungen', 'Gear', $WebFrontSettingId /*BaseId*/, 'true' /*BarBottomVisible*/);
		CreateLink('Msg Prio. Temparatur',   IPS_GetObjectIDByIdent(c_Control_MsgPrioTemp,     $CategoryIdSettings),  $WebFrontSettingId, 60);
		CreateLink('Msg Prio. Programm',     IPS_GetObjectIDByIdent(c_Control_MsgPrioProg,     $CategoryIdSettings),  $WebFrontSettingId, 70);

		// Application Logging
		$WebFrontLoggingId = CreateCategory('Logging',         $categoryId_WebFront, 60);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem."_Logging", $WFC10_TabPaneItem, 60, 'Meldungen', 'Window', $WebFrontLoggingId /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateLink('Meldungen', $ControlIdLog,  $WebFrontLoggingId, 10);

		ReloadAllWebFronts();
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled ) {
		$MobileId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		EmptyCategory($MobileId);

		$MobileDevicesId  = CreateCategory('Beschattungselemente', $MobileId, 10, 'Information');
		$MobileSettingsId = CreateCategory('Einstellungen',        $MobileId, 40, 'Gear');
		$MobileLogId      = CreateCategory('Meldungen',            $MobileId, 50, 'Window');

		$MobileScenariosId = CreateDummyInstance("Szenarien", $MobileId, 5);
		CreateLink('Auswahl',       $ControlIdScenarioActivate, $MobileScenariosId, 10);
		$Idx               = 10;
		foreach ($DeviceConfig as $DeviceIdent=>$DeviceData) {
			$DeviceId  = IPS_GetObjectIDByIdent($DeviceIdent, $CategoryIdDevices);
			$DeviceName = $DeviceData[c_Property_Name];

			// Mobile Overview
			CreateLink($DeviceName,  IPS_GetObjectIDByIdent(c_Control_Movement, $DeviceId), $MobileId, $Idx);

			// Detailed DeviceData
			$MobileDeviceDetailId  = CreateCategory($DeviceName, $MobileDevicesId, $Idx);
			CreateLink('Bewegung',            IPS_GetObjectIDByIdent(c_Control_Movement,        $DeviceId),  $MobileDeviceDetailId, 10);
			CreateLink('Status',              IPS_GetObjectIDByIdent(c_Control_Display,         $DeviceId),  $MobileDeviceDetailId, 20);
			CreateLink('Position',            IPS_GetObjectIDByIdent(c_Control_Position,        $DeviceId),  $MobileDeviceDetailId, 30);
			$ProgramDeviceId = CreateDummyInstance("Programme", $MobileDeviceDetailId, 1000);
			CreateLink('Nacht',      IPS_GetObjectIDByIdent(c_Control_ProgramNight,    $DeviceId),  $ProgramDeviceId, 10);
			CreateLink('Tag',        IPS_GetObjectIDByIdent(c_Control_ProgramDay,      $DeviceId),  $ProgramDeviceId, 20);
			CreateLink('Temperatur', IPS_GetObjectIDByIdent(c_Control_ProgramTemp,     $DeviceId),  $ProgramDeviceId, 30);
			CreateLink('Anwesenend', IPS_GetObjectIDByIdent(c_Control_ProgramPresent,  $DeviceId),  $ProgramDeviceId, 40);
			$controlIdProgramWeather = @IPS_GetObjectIDByIdent(c_Control_ProgramWeather, $DeviceId);
			if ($controlIdProgramWeather!==false) {
				CreateLink('Programm Wetter', $controlIdProgramWeather,  $ProgramDeviceId, 35);
			}
			$ProfileDeviceId = CreateDummyInstance("Profile", $MobileDeviceDetailId, 1100);
			CreateLink('Temperatur Profil',   IPS_GetObjectIDByIdent(c_Control_ProfileTemp,     $DeviceId),  $ProfileDeviceId, 10);
			CreateLink('Sonnenstand Profil',   IPS_GetObjectIDByIdent(c_Control_ProfileSun,     $DeviceId),  $ProfileDeviceId, 20);
			if ($controlIdProgramWeather!==false) {
				CreateLink('Wetter Profil',   IPS_GetObjectIDByIdent(c_Control_ProfileWeather,     $DeviceId),  $ProfileDeviceId, 30);
			}
			CreateLink('Tagesbeginn Profil',  IPS_GetObjectIDByIdent(c_Control_ProfileBgnOfDay, $DeviceId),  $ProfileDeviceId, 40);
			CreateLink('Tagesende Profil',    IPS_GetObjectIDByIdent(c_Control_ProfileEndOfDay, $DeviceId),  $ProfileDeviceId, 50);
			CreateLink('Profil Information',  IPS_GetObjectIDByIdent(c_Control_ProfileInfo,     $DeviceId),  $ProfileDeviceId, 60);
			$SettingsDeviceId = CreateDummyInstance("Einstellungen", $MobileDeviceDetailId, 1200);
			CreateLink('Automatik',           IPS_GetObjectIDByIdent(c_Control_Automatic,       $DeviceId),  $SettingsDeviceId, 10);
			CreateLink('Manueller Modus',     IPS_GetObjectIDByIdent(c_Control_ManualChange,    $DeviceId),  $SettingsDeviceId, 20);
			CreateLink('Temperatur Modus',    IPS_GetObjectIDByIdent(c_Control_TempChange,      $DeviceId),  $SettingsDeviceId, 25);
			CreateLink('Status',              IPS_GetObjectIDByIdent(c_Control_Display,         $DeviceId),  $SettingsDeviceId, 30);
			CreateLink('Position',            IPS_GetObjectIDByIdent(c_Control_Position,        $DeviceId),  $SettingsDeviceId, 40);

			$Idx = $Idx + 10;
		}

		CreateLink('Automatic Ein',        $ScriptIdAutomaticOn,       $MobileSettingsId, 10);
		CreateLink('Automatic Aus',        $ScriptIdAutomaticOff,      $MobileSettingsId, 20);
		CreateLink('Automatic Reset',      $ScriptIdAutomaticReset,    $MobileSettingsId, 30);
		CreateLink('Msg Prio. Temparatur', $ControlIdMsgPrioTemp,      $MobileSettingsId, 100);
		CreateLink('Msg Prio. Programm',   $ControlIdMsgPrioTemp,      $MobileSettingsId, 110);

		$MobileSettingScenarioId  = CreateCategory('Szenarien', $MobileSettingsId, 10, 'Script');
		CreateLink('Szenario Auswahl',     $ControlIdScenarioSelect,         $MobileSettingScenarioId, 10);
		CreateLink('Szenario Einstellungen', $CategoryIdScenarioDisplay,     $MobileSettingScenarioId, 10);
		CreateLink('Neues Szenario',       $ScriptIdScenarioCreate,          $MobileSettingScenarioId, 20);
		CreateLink('Szenario löschen',     $ScriptIdScenarioDelete,          $MobileSettingScenarioId, 30);

		$MobileSettingProfileId = CreateCategory('Temperatur Profile', $MobileSettingsId, 10, 'Temperature');
		CreateLink('Profil Auswahl',       $ControlIdProfileTempSelect,      $MobileSettingProfileId, 10);
		CreateLink('Profil Einstellungen', $CategoryIdProfileTempDisplay,    $MobileSettingProfileId,10);
		CreateLink('Neues Profil',         $ScriptIdProfileTempCreate,       $MobileSettingProfileId, 30);
		CreateLink('Profil löschen',       $ScriptIdProfileTempDelete,       $MobileSettingProfileId, 40);
		$MobileSettingProfileId = CreateCategory('Sonnenstand Profile', $MobileSettingsId, 20, 'Sun');
		CreateLink('Profil Auswahl',       $ControlIdProfileSunSelect,      $MobileSettingProfileId, 10);
		CreateLink('Profil Einstellungen', $CategoryIdProfileSunDisplay,    $MobileSettingProfileId,10);
		//CreateLink('Profil Graph',         $CategoryIdProfileSunGraphs,     $MobileSettingProfileId,20);
		CreateLink('Profil Graph',         $MediaIdAzimuth,                 $MobileSettingProfileId,20);

		CreateLink('Neues Profil',         $ScriptIdProfileSunCreate,       $MobileSettingProfileId, 30);
		CreateLink('Profil löschen',       $ScriptIdProfileSunDelete,       $MobileSettingProfileId, 40);
		$MobileSettingProfileId = CreateCategory('Wetter Profile', $MobileSettingsId, 30, 'Drops');
		CreateLink('Profil Auswahl',       $ControlIdProfileWeatherSelect,      $MobileSettingProfileId, 10);
		CreateLink('Profil Einstellungen', $CategoryIdProfileWeatherDisplay,    $MobileSettingProfileId,10);
		CreateLink('Neues Profil',         $ScriptIdProfileWeatherCreate,       $MobileSettingProfileId, 30);
		CreateLink('Profil löschen',       $ScriptIdProfileWeatherDelete,       $MobileSettingProfileId, 40);
		$MobileSettingProfileId = CreateCategory('Tagesbeginn Profile',$MobileSettingsId, 40, 'Clock');
		CreateLink('Profile Auswahl',      $ControlIdProfileBgnOfDaySelect,  $MobileSettingProfileId, 10);
		CreateLink('Profil Einstellungen', $CategoryIdProfileBgnOfDayDisplay,$MobileSettingProfileId,10);
		CreateLink('Neues Profil',         $ScriptIdProfileBgnOfDayCreate,   $MobileSettingProfileId, 20);
		CreateLink('Profil löschen',       $ScriptIdProfileBgnOfDayDelete,   $MobileSettingProfileId, 30);
		$MobileSettingProfileId = CreateCategory('Tagesende Profile',  $MobileSettingsId, 50, 'Clock');
		CreateLink('Profile Auswahl',      $ControlIdProfileEndOfDaySelect,  $MobileSettingProfileId, 10);
		CreateLink('Profil Einstellungen', $CategoryIdProfileEndOfDayDisplay,$MobileSettingProfileId,10);
		CreateLink('Neues Profil',         $ScriptIdProfileEndOfDayCreate,   $MobileSettingProfileId, 20);
		CreateLink('Profil löschen',       $ScriptIdProfileEndOfDayDelete,   $MobileSettingProfileId, 30);

		CreateLink('Meldungen', $ControlIdLog,  $MobileLogId, 100);
	}



	/** @}*/
?>