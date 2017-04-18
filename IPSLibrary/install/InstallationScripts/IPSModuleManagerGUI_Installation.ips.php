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

	/**@defgroup ipsmodulemanagergui_visualization IPSModuleManagerGUI Visualisierung
	 * @ingroup ipsmodulemanagergui
	 * @{
	 *
	 * Visualisierungen von IPSModuleManagerGUI
	 *
	 * IPSModuleManagerGUI WebFront Visualisierung:
	 *
	 *
	 *@}*/

	/**@defgroup ipsmodulemanagergui_install IPSModuleManagerGUI Installation
	 * @ingroup ipsmodulemanagergui
	 * @{
	 *
	 * Script zur kompletten Installation der IPSModuleManagerGUI Steuerung.
	 *
	 * Vor der Installation muß das File IPSModuleManagerGUI_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page rquirements_IPSModuleManagerGUI Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.3
	 * - IPSLogger >= 2.50.1
	 *
	 * @page install_IPSModuleManagerGUI Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSModuleManagerGUI Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSModuleManagerGUI_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.10.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSModuleManagerGUI');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.3');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.2');

	IPSUtils_Include ("IPSInstaller.inc.php",                       "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSModuleManagerGUI.inc.php",                "IPSLibrary::app::modules::IPSModuleManagerGUI");
	IPSUtils_Include ("IPSModuleManagerGUI_Constants.inc.php",      "IPSLibrary::app::modules::IPSModuleManagerGUI");

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

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');

	// Add Scripts
	$scriptIdSearchUpdates   = IPS_GetScriptIDByName('IPSModuleManagerGUI_SearchUpdates', $CategoryIdApp);

	// Add Update Scripts
	$timerId_SearchUpdates   = CreateTimer_OnceADay ('SearchUpdates', $scriptIdSearchUpdates, rand(0,4), rand(0,59)) ;


	// ===================================================================================================
	// Add Module Details
	// ===================================================================================================
	$variableIdStatus        = CreateVariable(IPSMMG_VAR_ACTION,      3 /*String*/,  $CategoryIdData, 10, '~String',  null,   'Overview', '');
	$variableIdModule        = CreateVariable(IPSMMG_VAR_MODULE,      3 /*String*/,  $CategoryIdData, 20, '~String',  null,   '', '');
	$variableIdInfo          = CreateVariable(IPSMMG_VAR_INFO,        3 /*String*/,  $CategoryIdData, 30, '~String',  null,   '', '');
	$variableIdHTML          = CreateVariable(IPSMMG_VAR_HTML,        3 /*String*/,  $CategoryIdData, 40, '~HTMLBox', null,   '<iframe frameborder="0" width="100%" height="600px"  src="../user/IPSModuleManagerGUI/IPSModuleManagerGUI.php"</iframe>', 'Information');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$categoryId_WebFront         = CreateCategoryPath($WFC10_Path);
		EmptyCategory($categoryId_WebFront);
		CreateLink('IPSLibrary',  $variableIdHTML,  $categoryId_WebFront, 10);

		// System Tabpane
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem, $WFC10_TabPaneParent,  $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);

		// IPSLibrary Tabpane
		$tabItem = $WFC10_TabPaneItem.$WFC10_TabItem;
		DeleteWFCItems($WFC10_ConfigId, $tabItem);
		//CreateWFCItemExternalPage ($WFC10_ConfigId, $tabItem, $WFC10_TabPaneItem, $WFC10_TabOrder, $WFC10_TabName, $WFC10_TabIcon, "user\/IPSModuleManagerGUI\/IPSModuleManagerGUI.php", 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory ($WFC10_ConfigId, $tabItem, $WFC10_TabPaneItem, $WFC10_TabOrder, $WFC10_TabName, $WFC10_TabIcon, $categoryId_WebFront, 'false' /*BarBottomVisible*/);

		ReloadAllWebFronts();
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	// Not Supported

?>