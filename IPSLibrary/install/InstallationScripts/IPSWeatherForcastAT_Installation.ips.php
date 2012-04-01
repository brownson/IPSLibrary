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


	/**@defgroup ipsweatherforcastat_visualization IPSWeatherForcastAT Visualisierung
	 * @ingroup ipsweatherforcastat
	 * @{
	 *
	 * Visualisierungen von IPSWeatherForcastAT
	 *
	 * IPSWeatherForcastAT WebFront Visualisierung:
	 *
	 *  @image html IPSWeatherForcastAT_WebFront.png
	 *
	 *
	 * IPSWeatherForcastAT Mobile Visualisierung:
	 *
	 *  @image html IPSWeatherForcastAT_Mobile.png
	 *
	 *@}*/

	/**@defgroup ipsweatherforcastat_installation IPSWeatherForcastAT Installation
	 * @ingroup ipsweatherforcastat
	 * @{
	 *
	 * Installations File für IPSWeatherForcastAT
	 *
	 * @section requirements_ipsweatherforcastat Installations Voraussetzungen IPSWeatherForcast (AT)
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 *
	 *
	 * @page install_ipsweatherforcastat Installations Schritte
	 * Folgende Schritte sind zur Installation der EDIP Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration, Installation ist auch ohne spezielle Konfiguration möglich)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSWeatherForcastAT_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 13.02.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('IPSWeatherForcastAT');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');

	IPSUtils_Include ("IPSInstaller.inc.php",                      "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSWeatherForcastAT_Configuration.inc.php", "IPSLibrary::config::modules::Weather::IPSWeatherForcastAT");

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

	$categoryId_Data     = $moduleManager->GetModuleCategoryID('data');
	$categoryId_App      = $moduleManager->GetModuleCategoryID('app');

	// Scripts
	$scriptId_Refresh  = IPS_GetScriptIDByName('IPSWeatherForcastAT_Refresh',  $categoryId_App);
	$timerId_Refresh   = CreateTimer_CyclicByMinutes ('Refresh', $scriptId_Refresh, 30) ;

	// Create Variables
	$LastRefreshDateTime     = CreateVariable("LastRefreshDateTime",    3 /*String*/,  $categoryId_Data,  10, '~String',  null, '');
	$LastRefreshTime         = CreateVariable("LastRefreshTime",        3 /*String*/,  $categoryId_Data,  20, '~String',  null, '');
	$TodaySeaLevel           = CreateVariable("SeaLevel",               1 /*Integer*/, $categoryId_Data,  30,  null,       null, 0);
	$TodayAirHumidity        = CreateVariable("AirHumidity",            3 /*String*/,  $categoryId_Data,  40,  '~String',  null, '');
	$TodayWind               = CreateVariable("Wind",                   3 /*String*/,  $categoryId_Data,  50,  '~String',  null, '');

	$TodayDayOfWeek          = CreateVariable("TodayDay",               3 /*String*/,  $categoryId_Data,  100,  '~String',  null, '');
	$TodayTempCurrent        = CreateVariable("TodayTempCurrent",       1 /*Integer*/, $categoryId_Data,  110,  null,       null, 0);
	$TodayTempMin            = CreateVariable("TodayTempMin",           1 /*Integer*/, $categoryId_Data,  120,  null,       null, 0);
	$TodayTempMax            = CreateVariable("TodayTempMax",           1 /*Integer*/, $categoryId_Data,  130,  null,       null, 0);
	$TodayIcon               = CreateVariable("TodayIcon",              3 /*String*/,  $categoryId_Data,  140,  '~String',  null, '');
	$TodayTextShort          = CreateVariable("TodayForecastLong",      3 /*String*/,  $categoryId_Data,  150,  '~String',  null, '');
	$TodayTextLong           = CreateVariable("TodayForecastShort",     3 /*String*/,  $categoryId_Data,  160, '~String',  null, '');

	$Forecast1DayOfWeek       = CreateVariable("TomorrowDay",           3 /*String*/,  $categoryId_Data,  200,  '~String',  null, '');
	$Forecast1TempMin         = CreateVariable("TomorrowTempMin",       1 /*Integer*/, $categoryId_Data,  210,  null,       null, 0);
	$Forecast1TempMax         = CreateVariable("TomorrowTempMax",       1 /*Integer*/, $categoryId_Data,  220,  null,       null, 0);
	$Forecast1TextShort       = CreateVariable("TomorrowForecastLong",  3 /*String*/,  $categoryId_Data,  230,  '~String',  null, '');
	$Forecast1TextLong        = CreateVariable("TomorrowForecastShort", 3 /*String*/,  $categoryId_Data,  240,  '~String',  null, '');
	$Forecast1Icon            = CreateVariable("TomorrowIcon",          3 /*String*/,  $categoryId_Data,  250,  '~String',  null, '');

	$Forecast2DayOfWeek       = CreateVariable("Tomorrow1Day",          3 /*String*/,  $categoryId_Data,  300,  '~String',  null, '');
	$Forecast2TempMin         = CreateVariable("Tomorrow1TempMin",      1 /*Integer*/, $categoryId_Data,  310,  null,       null, 0);
	$Forecast2TempMax         = CreateVariable("Tomorrow1TempMax",      1 /*Integer*/, $categoryId_Data,  320,  null,       null, 0);
	$Forecast2TextShort       = CreateVariable("Tomorrow1ForecastLong", 3 /*String*/,  $categoryId_Data,  330,  '~String',  null, '');
	$Forecast2TextLong        = CreateVariable("Tomorrow1ForecastShort",3 /*String*/,  $categoryId_Data,  340,  '~String',  null, '');
	$Forecast2Icon            = CreateVariable("Tomorrow1Icon",         3 /*String*/,  $categoryId_Data,  350,  '~String',  null, '');

	$Forecast3DayOfWeek       = CreateVariable("Tomorrow2Day",          3 /*String*/,  $categoryId_Data,  400,  '~String',  null, '');
	$Forecast3TempMin         = CreateVariable("Tomorrow2TempMin",      1 /*Integer*/, $categoryId_Data,  410,  null,       null, 0);
	$Forecast3TempMax         = CreateVariable("Tomorrow2TempMax",      1 /*Integer*/, $categoryId_Data,  420,  null,       null, 0);
	$Forecast3TextShort       = CreateVariable("Tomorrow2ForecastLong", 3 /*String*/,  $categoryId_Data,  430,  '~String',  null, '');
	$Forecast3TextLong        = CreateVariable("Tomorrow2ForecastShort",3 /*String*/,  $categoryId_Data,  440,  '~String',  null, '');
	$Forecast3Icon            = CreateVariable("Tomorrow2Icon",         3 /*String*/,  $categoryId_Data,  450,  '~String',  null, '');

	$iForecast                = CreateVariable("iForecast",             3 /*String*/,  $categoryId_Data,  1000, '~HTMLBox', null, '<iframe frameborder="0" width="100%" height="4000px" src="../user/Weather/Weather.php"</iframe>');

	// Webfront Installation
	if ($WFC10_Enabled) {
		DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem.$WFC10_TabItem);
		CreateWFCItemTabPane      ($WFC10_ConfigId, $WFC10_TabPaneItem, $WFC10_TabPaneParent,  $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemExternalPage ($WFC10_ConfigId, $WFC10_TabPaneItem.$WFC10_TabItem, $WFC10_TabPaneItem, $WFC10_TabOrder, $WFC10_TabName, $WFC10_TabIcon, "user\/IPSWeatherForcastAT\/Weather.php", 'false' /*BarBottomVisible*/);
		ReloadAllWebFronts();
	}

	// iPhone Installation
	if ($Mobile_Enabled) {
		$mobileId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		$mobileId  = CreateCategoryPath($Mobile_Path.'.'.$Mobile_Name, $Mobile_Order, $Mobile_Icon);

		CreateLink('Vorhersage',      $iForecast, $mobileId, 10);
	}

	// Execute Data Refresh
	IPSUtils_Include ("IPSWeatherForcastAT_Refresh.ips.php", "IPSLibrary::app::modules::Weather::IPSWeatherForcastAT");

	/** @}*/
?>