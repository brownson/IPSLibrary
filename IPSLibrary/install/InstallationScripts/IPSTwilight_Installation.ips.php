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

	/**@defgroup ipstwilight_visualization IPSTwilight Visualisierung
	 * @ingroup ipstwilight
	 * @{
	 *
	 * Visualisierungen von IPSTwilight
	 *
	 * IPSTwilight WebFront Visualisierung:
	 *
	 *  @image html IPSTwilight_WebFront.png
	 *
	 *
	 * IPSTwilight Mobile Visualisierung:
	 *
	 *  @image html IPSTwilight_Mobile.png
	 *
	 *@}*/

	/**@defgroup ipstwilight_installation IPSTwilight Installation
	 * @ingroup ipstwilight
	 * @{
	 *
	 * Installations File für IPSTwilight
	 *
	 * @section requirements_ipstwilight Installations Voraussetzungen IPSTwilight
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 *
	 * @page install_ipstwilight Installations Schritte
	 * Folgende Schritte sind zur Installation von IPSTwilight nötig
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSTwilight_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 13.02.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('IPSTwilight');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",              "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSTwilight_Configuration.inc.php", "IPSLibrary::config::modules::Weather::IPSTwilight");

	$WFC10_Enabled        = $moduleManager->GetConfigValue('Enabled', 'WFC10');
	$WFC10_ConfigId       = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
	$WFC10_Path           = $moduleManager->GetConfigValue('Path', 'WFC10');
	$WFC10_TabPaneItem    = $moduleManager->GetConfigValue('TabPaneItem', 'WFC10');
	$WFC10_TabPaneParent  = $moduleManager->GetConfigValue('TabPaneParent', 'WFC10');
	$WFC10_TabPaneName    = $moduleManager->GetConfigValue('TabPaneName', 'WFC10');
	$WFC10_TabPaneIcon    = $moduleManager->GetConfigValue('TabPaneIcon', 'WFC10');
	$WFC10_TabPaneOrder   = $moduleManager->GetConfigValueInt('TabPaneOrder', 'WFC10');
	$WFC10_TabItem        = $moduleManager->GetConfigValue('TabItem', 'WFC10');
	$WFC10_TabName        = $moduleManager->GetConfigValue('TabName', 'WFC10');
	$WFC10_TabIcon        = $moduleManager->GetConfigValue('TabIcon', 'WFC10');
	$WFC10_TabOrder       = $moduleManager->GetConfigValueInt('TabOrder', 'WFC10');

	$Mobile_Enabled       = $moduleManager->GetConfigValue('Enabled', 'Mobile');
	$Mobile_Path          = $moduleManager->GetConfigValue('Path', 'Mobile');
	$Mobile_PathOrder     = $moduleManager->GetConfigValueInt('PathOrder', 'Mobile');
	$Mobile_PathIcon      = $moduleManager->GetConfigValue('PathOrder', 'Mobile');
	$Mobile_Name          = $moduleManager->GetConfigValue('Name', 'Mobile');
	$Mobile_Order         = $moduleManager->GetConfigValueInt('Order', 'Mobile');
	$Mobile_Icon          = $moduleManager->GetConfigValue('Icon', 'Mobile');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$Location = IPSTWILIGHT_LOCATION;

	$categoryId_Data     = $moduleManager->GetModuleCategoryID('data');
	$categoryId_App      = $moduleManager->GetModuleCategoryID('app');
	$categoryId_DataGraphics  = CreateCategory('Graphics', $categoryId_Data, 10);
	$categoryId_DataValues    = CreateCategory('Values',   $categoryId_Data, 20);

	// Scripts
	$scriptId_Refresh  = IPS_GetScriptIDByName('IPSTwilight',  $categoryId_App);
	$timerId_Refresh   = CreateTimer_OnceADay ('Refresh', $scriptId_Refresh, 0, 15) ;

	// Graphics
	$YearMediaId          = CreateMedia ('IPSTwilight_Year',          $categoryId_DataGraphics, IPS_GetKernelDir().'media/IPSTwilight_Year.gif',          false,1,'Sun');
	$YearLimitedMediaId   = CreateMedia ('IPSTwilight_YearLimited',   $categoryId_DataGraphics, IPS_GetKernelDir().'media/IPSTwilight_YearLimited.gif',   false,1,'Sun');
	$YearUnlimitedMediaId = CreateMedia ('IPSTwilight_YearUnlimited', $categoryId_DataGraphics, IPS_GetKernelDir().'media/IPSTwilight_YearUnlimited.gif', false,1,'Sun');
	$DayMediaId           = CreateMedia ('IPSTwilight_Day',           $categoryId_DataGraphics, IPS_GetKernelDir().'media/IPSTwilight_Day.gif',           false,1,'Sun');
	$DayLimitedMediaId    = CreateMedia ('IPSTwilight_DayLimited',    $categoryId_DataGraphics, IPS_GetKernelDir().'media/IPSTwilight_DayLimited.gif',    false,1,'Sun');
	$DayUnlimitedMediaId  = CreateMedia ('IPSTwilight_DayUnlimited',  $categoryId_DataGraphics, IPS_GetKernelDir().'media/IPSTwilight_DayUnlimited.gif',  false,1,'Sun');

	//Data
	$DisplaySwitchId   = CreateVariable('Display' ,     0 /*Boolean*/, $categoryId_DataValues, 10, '~Switch',$scriptId_Refresh, false,   'Information');

	$SunriseBeg        = CreateVariable("SunriseBegin",           3 /*String*/,  $categoryId_DataValues,  110, '~String', $scriptId_Refresh, '');
	$SunriseEnd        = CreateVariable("SunriseEnd",             3 /*String*/,  $categoryId_DataValues,  120, '~String', $scriptId_Refresh, '');
	$SunriseBegLim     = CreateVariable("SunriseBeginLimited",    3 /*String*/,  $categoryId_DataValues,  130, '~String', $scriptId_Refresh, '');
	$SunriseEndLim     = CreateVariable("SunriseEndLimited",      3 /*String*/,  $categoryId_DataValues,  140, '~String', $scriptId_Refresh, '');
	$SunriseDisplay    = CreateVariable("SunriseDisplay",         3 /*String*/,  $categoryId_DataValues,  150, '~String', null,              '00:00 - 00:00');
	$SunriseLimits     = CreateVariable("SunriseLimits",          3 /*String*/,  $categoryId_DataValues,  160, '~String', $scriptId_Refresh, '05:30-07:30/18:30-20:30');

	$CivilBeg          = CreateVariable("CivilBegin",             3 /*String*/,  $categoryId_DataValues,  210, '~String', $scriptId_Refresh, '');
	$CivilEnd          = CreateVariable("CivilEnd",               3 /*String*/,  $categoryId_DataValues,  220, '~String', $scriptId_Refresh, '');
	$CivilBegLim       = CreateVariable("CivilBeginLimited",      3 /*String*/,  $categoryId_DataValues,  230, '~String', $scriptId_Refresh, '');
	$CivilEndLim       = CreateVariable("CivilEndLimited",        3 /*String*/,  $categoryId_DataValues,  240, '~String', $scriptId_Refresh, '');
	$CivilDisplay      = CreateVariable("CivilDisplay",           3 /*String*/,  $categoryId_DataValues,  250, '~String', null,              '00:00 - 00:00');
	$CivilLimits       = CreateVariable("CivilLimits",            3 /*String*/,  $categoryId_DataValues,  260, '~String', $scriptId_Refresh, '05:00-07:00/19:00-21:00');

	$NauticBeg         = CreateVariable("NauticBegin",            3 /*String*/,  $categoryId_DataValues,  310, '~String', $scriptId_Refresh, '');
	$NauticEnd         = CreateVariable("NauticEnd",              3 /*String*/,  $categoryId_DataValues,  320, '~String', $scriptId_Refresh, '');
	$NauticBegLim      = CreateVariable("NauticBeginLimited",     3 /*String*/,  $categoryId_DataValues,  330, '~String', $scriptId_Refresh, '');
	$NauticEndLim      = CreateVariable("NauticEndLimited",       3 /*String*/,  $categoryId_DataValues,  340, '~String', $scriptId_Refresh, '');
	$NauticDisplay     = CreateVariable("NauticDisplay",          3 /*String*/,  $categoryId_DataValues,  350, '~String', null,              '00:00 - 00:00');
	$NauticLimits      = CreateVariable("NauticLimits",           3 /*String*/,  $categoryId_DataValues,  360, '~String', $scriptId_Refresh, '04:30-06:30/19:30-21:30');

	$AstronomicBeg     = CreateVariable("AstronomicBegin",        3 /*String*/,  $categoryId_DataValues,  410, '~String', $scriptId_Refresh, '');
	$AstronomicEnd     = CreateVariable("AstronomicEnd",          3 /*String*/,  $categoryId_DataValues,  420, '~String', $scriptId_Refresh, '');
	$AstronomicBegLim  = CreateVariable("AstronomicBeginLimited", 3 /*String*/,  $categoryId_DataValues,  430, '~String', $scriptId_Refresh, '');
	$AstronomicEndLim  = CreateVariable("AstronomicEndLimited",   3 /*String*/,  $categoryId_DataValues,  440, '~String', $scriptId_Refresh, '');
	$AstronomicDisplay = CreateVariable("AstronomicDisplay",      3 /*String*/,  $categoryId_DataValues,  450, '~String', null,              '00:00 - 00:00');
	$AstronomicLimits  = CreateVariable("AstronomicLimits",       3 /*String*/,  $categoryId_DataValues,  460, '~String', $scriptId_Refresh, '04:00-06:00/20:00-22:00');

	IPSUtils_Include ("IPSTwilight.ips.php", "IPSLibrary::app::modules::Weather::IPSTwilight");

	// ----------------------------------------------------------------------------------------------------------------------------
	// WebFront Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$categoryId_WebFront         = CreateCategoryPath($WFC10_Path);
		$categoryId_WebFrontTopLeft  = CreateCategory(  'TopLeft',  $categoryId_WebFront, 10);
		$categoryId_WebFrontTopRight = CreateCategory(  'TopRight', $categoryId_WebFront, 20);
		$categoryId_WebFrontBottom   = CreateCategory(  'Bottom',   $categoryId_WebFront, 30);
		$categoryId_WebFrontRight    = CreateCategory(  'Right',    $categoryId_WebFront, 40);

		CreateLinkByDestination('Sonnen-Aufgang/Untergang', $SunriseDisplay,    $categoryId_WebFrontTopLeft,  10);
		CreateLinkByDestination('Limits',                   $SunriseLimits,     $categoryId_WebFrontTopRight, 10);
		CreateLinkByDestination('Zivile Dämmerung',         $CivilDisplay,      $categoryId_WebFrontTopLeft,  20);
		CreateLinkByDestination('Limits',                   $CivilLimits,       $categoryId_WebFrontTopRight, 20);
		CreateLinkByDestination('Nautische Dämmerung',      $NauticDisplay,     $categoryId_WebFrontTopLeft,  30);
		CreateLinkByDestination('Limits',                   $NauticLimits,      $categoryId_WebFrontTopRight, 30);
		CreateLinkByDestination('Astronomische Dämmerung',  $AstronomicDisplay, $categoryId_WebFrontTopLeft,  40);
		CreateLinkByDestination('Limits',                   $AstronomicLimits,  $categoryId_WebFrontTopRight, 40);

		CreateLinkByDestination("Tag- und Nachtstunden in $Location",  $YearMediaId,      $categoryId_WebFrontBottom, 10);
		CreateLinkByDestination('Show Limited',                        $DisplaySwitchId,  $categoryId_WebFrontRight,  10);
		CreateLinkByDestination('Aktueller Tag',                       $DayMediaId,       $categoryId_WebFrontRight,  20);

		$UId = date('Hi');
		$tabItem = $WFC10_TabPaneItem.$WFC10_TabItem;
		DeleteWFCItems($WFC10_ConfigId, 'WeatherTPSunrise');
		DeleteWFCItems($WFC10_ConfigId, $tabItem);
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem, $WFC10_TabPaneParent,  $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $tabItem,           $WFC10_TabPaneItem,    $WFC10_TabOrder,     $WFC10_TabName,     $WFC10_TabIcon, 1 /*Vertical*/, 310 /*Width*/, 1 /*Target=Pane2*/, 1/*UsePixel*/, 'true');
		CreateWFCItemSplitPane ($WFC10_ConfigId,   $tabItem.'_Left',              $tabItem,         10, '', '', 0 /*Horicontal*/, 205 /*Height*/, 0 /*Target=Pane1*/, 1 /*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId,   $tabItem.'_Right'.$UId,        $tabItem,         20, '', '', $categoryId_WebFrontRight    /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemSplitPane ($WFC10_ConfigId,     $tabItem.'_Top',             $tabItem.'_Left', 10, '', '', 1 /*Vertical*/, 50 /*Width*/, 0 /*Target=Pane1*/, 0 /*UsePercentage*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId,     $tabItem.'_Bottom'.$UId,     $tabItem.'_Left', 20, '', '', $categoryId_WebFrontBottom   /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId,       $tabItem.'_TopLeft'.$UId,  $tabItem.'_Top',  10, '', '', $categoryId_WebFrontTopLeft  /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId,       $tabItem.'_TopRight'.$UId, $tabItem.'_Top',  20, '', '', $categoryId_WebFrontTopRight /*BaseId*/, 'false' /*BarBottomVisible*/);

		ReloadAllWebFronts();
	}
	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled) {
		$mobileId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		$mobileId  = CreateCategoryPath($Mobile_Path.'.'.$Mobile_Name, $Mobile_Order, $Mobile_Icon);
		
		$InstanceId    = CreateDummyInstance("Sonnen-Aufgang/Untergang",  $mobileId, 10);
		CreateLink('Begin/Ende',   $SunriseDisplay, $InstanceId, 10);
		CreateLink('Limits',       $SunriseLimits,  $InstanceId, 20);
		$InstanceId    = CreateDummyInstance("Zivile Dämmerung",  $mobileId, 20);
		CreateLink('Begin/Ende',   $CivilDisplay, $InstanceId, 10);
		CreateLink('Limits',       $CivilLimits,  $InstanceId, 20);
		$InstanceId    = CreateDummyInstance("Nautische Dämmerung",  $mobileId, 30);
		CreateLink('Begin/Ende',   $NauticDisplay, $InstanceId, 10);
		CreateLink('Limits',       $NauticLimits,  $InstanceId, 20);
		$InstanceId    = CreateDummyInstance("Astronomische Dämmerung",  $mobileId, 40);
		CreateLink('Begin/Ende',   $AstronomicDisplay, $InstanceId, 10);
		CreateLink('Limits',       $AstronomicLimits,  $InstanceId, 20);

		CreateLinkByDestination("Jahres Grafik",            $YearUnlimitedMediaId,   $mobileId, 100);
		CreateLinkByDestination('Tages Grafik',             $DayUnlimitedMediaId,    $mobileId, 110);
		CreateLinkByDestination("Jahres Grafik (limited)",  $YearLimitedMediaId,     $mobileId, 120);
		CreateLinkByDestination('Tages Grafik (limited)',   $DayLimitedMediaId,      $mobileId, 130);
	}
	/** @}*/
?>