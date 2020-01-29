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

	 /**@defgroup ipshomematic_installation ipshomematic Installation
	 * @ingroup ipshomematic
	 * @{
	 *
	 * Installations File für den ipshomematic
	 *
	 * @section requirements_ipshomematic Installations Voraussetzungen ipshomematic
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 *
	 * @section ipshomematic_visu Visualisierungen für ipshomematic
	 * - WebFront 10Zoll
	 * - Mobile
	 *
	 * @page install_ipshomematic_install Installations Schritte
	 * Folgende Schritte sind zur Installation der Homematic Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSHomematic_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 02.07.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('ipshomematic');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.2');

	IPSUtils_Include ("IPSInstaller.inc.php",               "IPSLibrary::install::IPSInstaller");

	$WFC10_Enabled          = $moduleManager->GetConfigValue('Enabled', 'WFC10');
	$WFC10_ConfigId         = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
	$WFC10_Path             = $moduleManager->GetConfigValue('Path', 'WFC10');
	$WFC10_TabPaneItem      = $moduleManager->GetConfigValue('TabPaneItem', 'WFC10');
	$WFC10_TabPaneParent    = $moduleManager->GetConfigValue('TabPaneParent', 'WFC10');
	$WFC10_TabPaneName      = $moduleManager->GetConfigValue('TabPaneName', 'WFC10');
	$WFC10_TabPaneOrder     = $moduleManager->GetConfigValueInt('TabPaneOrder', 'WFC10');
	$WFC10_TabPaneIcon      = $moduleManager->GetConfigValue('TabPaneIcon', 'WFC10');
	$WFC10_TabPaneExclusive = $moduleManager->GetConfigValueBoolDef('TabPaneExclusive', 'WFC10', false);
	
	$WFC10_TabItem1         = $moduleManager->GetConfigValue('TabItem1', 'WFC10');
	$WFC10_TabName1         = $moduleManager->GetConfigValue('TabName1', 'WFC10');
	$WFC10_TabIcon1         = $moduleManager->GetConfigValue('TabIcon1', 'WFC10');
	$WFC10_TabOrder1        = $moduleManager->GetConfigValueInt('TabOrder1', 'WFC10');

	$Mobile_Enabled         = $moduleManager->GetConfigValue('Enabled', 'Mobile');
	$Mobile_Path            = $moduleManager->GetConfigValue('Path', 'Mobile');
	$Mobile_PathOrder       = $moduleManager->GetConfigValueInt('PathOrder', 'Mobile');
	$Mobile_PathIcon        = $moduleManager->GetConfigValue('PathIcon', 'Mobile');
	$Mobile_Name1           = $moduleManager->GetConfigValue('Name1', 'Mobile');
	$Mobile_Order1          = $moduleManager->GetConfigValueInt('Order1', 'Mobile');
	$Mobile_Icon1           = $moduleManager->GetConfigValue('Icon1', 'Mobile');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');
	$CategoryIdHardware = CreateCategoryPath('Hardware.Homematic');

	IPSUtils_Include ('IPSHomematic_Constants.inc.php',      'IPSLibrary::app::hardware::IPSHomematic');
	IPSUtils_Include ('IPSHomematic_Configuration.inc.php',  'IPSLibrary::config::hardware::IPSHomematic');

	// Scripts
	$scriptIdTimerRefreshServiceMessages = IPS_GetScriptIDByName('IPSHomematic_TimerRefreshServiceMessages', $CategoryIdApp);
	$scriptIdTimerRefreshRSSI            = IPS_GetScriptIDByName('IPSHomematic_TimerRefreshRSSI',            $CategoryIdApp);
	$scriptIdRefreshRSSI                 = IPS_GetScriptIDByName('IPSHomematic_RefreshRSSI',                 $CategoryIdApp);
	$scriptIdRefreshServiceMessages      = IPS_GetScriptIDByName('IPSHomematic_RefreshServiceMessages',      $CategoryIdApp);
	$scriptIdRefreshStatusVariables      = IPS_GetScriptIDByName('IPSHomematic_RefreshStatusVariables',      $CategoryIdApp);
	$scriptIdResetServiceMessage         = IPS_GetScriptIDByName('IPSHomematic_ResetServiceMessages',        $CategoryIdApp);
	$scriptIdChangeSettings              = IPS_GetScriptIDByName('IPSHomematic_ChangeSettings',              $CategoryIdApp);
	$scriptIdSmokeDetector               = IPS_GetScriptIDByName('IPSHomematic_SmokeDetector',               $CategoryIdApp);

	// Timer
	CreateTimer_CyclicByMinutes ('Check', $scriptIdTimerRefreshServiceMessages, 5);
	CreateTimer_OnceADay ('Refresh_03', $scriptIdTimerRefreshRSSI,  3, 0);
	CreateTimer_OnceADay ('Refresh_14', $scriptIdTimerRefreshRSSI, 14, 0);
	
	// Profiles
	CreateProfile_Count        ('IPSHomematic_Priority',   1, 1,   10,    null, "",    null);

	// Variables
	$categoryIdStatus    = CreateCategory('StatusMessages',  $CategoryIdData, 100);
	$controlIdRSSI       = CreateVariable(HM_CONTROL_RSSI,       3 /*String*/, $categoryIdStatus, 10, '~HTMLBox', null, '', 'Intensity');
	$controlIdRSSIDevice = CreateVariable(HM_CONTROL_RSSIDEVICE, 3 /*String*/, $categoryIdStatus, 20, '~HTMLBox', null, '', 'Intensity');
	$controlIdRSSIPeer   = CreateVariable(HM_CONTROL_RSSIPEER,   3 /*String*/, $categoryIdStatus, 30, '~HTMLBox', null, '', 'Intensity');
	$controlIdMessages   = CreateVariable(HM_CONTROL_MESSAGES,   3 /*String*/, $categoryIdStatus, 40, '~HTMLBox', null, '', 'Warning');

	$categoryIdSettings  = CreateCategory('Settings',  $CategoryIdData, 200);
	$controlIdPriority   = CreateVariable(HM_CONTROL_PRIORITY,   1 /*Integer*/, $categoryIdSettings, 10, 'IPSHomematic_Priority', $scriptIdChangeSettings, 2, 'Warning');

	$moduleManager->LogHandler()->Log("Check Homematic Instances");

	// Homematic Instances
	foreach (get_HomematicConfiguration() as $component=>$componentData) {
		$propertyAddress  = $componentData[0];
		$propertyChannel  = $componentData[1];
		$propertyProtocol = $componentData[2];
		$propertyType     = $componentData[3];
		$propertyName     = $component;
		
		$DeviceId = CreateHomematicInstance($moduleManager,
                                            $propertyAddress,
                                            $propertyChannel,
                                            $propertyName,
                                            $CategoryIdHardware,
                                            $propertyProtocol);

		$SystemId = CreateHomematicInstance($moduleManager,
                                            $propertyAddress,
                                            0,
                                            $propertyName.'#',
                                            $CategoryIdHardware,
                                            $propertyProtocol);

		if ($propertyType==HM_TYPE_SMOKEDETECTOR) {
			$variableId = IPS_GetVariableIDByName('STATE', $DeviceId);

			CreateEvent ($propertyName, $variableId, $scriptIdSmokeDetector);
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$moduleManager->LogHandler()->Log('Generate WebFront Interface');

		$categoryIdWebFront         = CreateCategoryPath($WFC10_Path);
		EmptyCategory($categoryIdWebFront);
		$categoryIdWebFrontLeft          = CreateCategory('Left',  $categoryIdWebFront, 10);
		$categoryIdWebFrontRight         = CreateCategory('Right', $categoryIdWebFront, 20);
		$categoryIdWebFrontRightDetail1  = CreateCategory('RSSI Sender',    $categoryIdWebFrontRight, 20);
		$categoryIdWebFrontRightDetail2  = CreateCategory('RSSI Empfänger', $categoryIdWebFrontRight, 30);
		CreateLink('Service Meldungen', $controlIdMessages,    $categoryIdWebFrontRight, 10);
		CreateLink('Empfangsstärken',   $controlIdRSSI,        $categoryIdWebFrontRight, 20);
		CreateLink('RSSI Sender',       $controlIdRSSIDevice,  $categoryIdWebFrontRightDetail1, 10);
		CreateLink('RSSI Empfänger',    $controlIdRSSIPeer,    $categoryIdWebFrontRightDetail2, 10);

		$instanceId = CreateDummyInstance('Service Meldungen', $categoryIdWebFrontLeft, 10);
		CreateLink('Meldungen laden',         $scriptIdRefreshServiceMessages, $instanceId, 10);
		CreateLink('Meldungen bestätigen',    $scriptIdResetServiceMessage,    $instanceId, 20);
		CreateLink('Priorität Notifizierung', $controlIdPriority,              $instanceId, 30);
		CreateLink('Refresh Empfangsstärken', $scriptIdRefreshRSSI,            $categoryIdWebFrontLeft, 20);
		CreateLink('Refresh Statusvariablen', $scriptIdRefreshStatusVariables, $categoryIdWebFrontLeft, 30);

		$WFC10Tab = $WFC10_TabPaneItem.$WFC10_TabItem1;
		DeleteWFCItems($WFC10_ConfigId, $WFC10Tab);
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,  $WFC10_TabPaneParent, $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10Tab.'', $WFC10_TabPaneItem.'',   $WFC10_TabOrder1, $WFC10_TabName1, $WFC10_TabIcon1, 1 /*Vertical*/, 40 /*Widht*/, 0 /*Target=Pane1*/, 0/*UsePercent*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10Tab.'_Left', $WFC10Tab.'', 10, '', '', $categoryIdWebFrontLeft  /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10Tab.'_Right',$WFC10Tab.'', 20, '', '', $categoryIdWebFrontRight /*BaseId*/, 'true'  /*BarBottomVisible*/);

		ReloadAllWebFronts();
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled) {
		$moduleManager->LogHandler()->Log('Generate Mobile Interface');

		$categoryIdMobile    = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		$categoryIdMobile    = CreateCategory($Mobile_Name1,  $categoryIdMobile, $Mobile_Order1, $Mobile_Icon1);

		$instanceId = CreateDummyInstance('Empfangsstärken', $categoryIdMobile, 10);
		CreateLink('RSSI Sender',       $controlIdRSSIDevice,  $instanceId, 10);
		CreateLink('RSSI Empfänger',    $controlIdRSSIPeer,    $instanceId, 20);
		CreateLink('Refresh',           $scriptIdRefreshRSSI,  $instanceId, 30);

		$instanceId = CreateDummyInstance('Service Meldungen', $categoryIdMobile, 20);
		CreateLink('Aktuelle Meldungen',      $controlIdMessages,              $instanceId, 10);
		CreateLink('Priorität Notifizierung', $controlIdPriority,              $instanceId, 20);
		CreateLink('Meldungen laden',         $scriptIdRefreshServiceMessages, $instanceId, 30);
		CreateLink('Meldungen bestätigen',    $scriptIdResetServiceMessage,    $instanceId, 40);

		$instanceId = CreateDummyInstance('Status Variablen', $categoryIdMobile, 30);
		CreateLink('Refresh', $scriptIdRefreshStatusVariables, $instanceId, 10);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Local Functions
	// ----------------------------------------------------------------------------------------------------------------------------
	function CreateHomematicInstance($moduleManager, $Address, $Channel, $Name, $ParentId, $Protocol='BidCos-RF') {
		foreach (IPS_GetInstanceListByModuleID("{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}") as $HomematicModuleId ) {
			$HMAddress = IPS_GetProperty($HomematicModuleId,'Address');
			if ($HMAddress=="$Address:$Channel") {
				$moduleManager->LogHandler()->Log("Found existing HomaticModule '$Name' Address=$Address, Channel=$Channel, Protocol=$Protocol");
				return $HomematicModuleId;
			}
		}

		$moduleManager->LogHandler()->Log("Create HomaticModule '$Name' Address=$Address, Channel=$Channel, Protocol=$Protocol");
		$HomematicModuleId = IPS_CreateInstance("{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");
		IPS_SetParent($HomematicModuleId, $ParentId);
		IPS_SetName($HomematicModuleId, $Name);
		IPS_SetProperty($HomematicModuleId,'Address',$Address.':'.$Channel);
		if ($Protocol == 'BidCos-RF') {
			$Protocol = 0;
		} else {
			$Protocol = 1;
		}
		IPS_SetProperty($HomematicModuleId, 'Protocol', $Protocol);
		IPS_SetProperty($HomematicModuleId, 'EmulateStatus', true);
		// Apply Changes
		IPS_ApplyChanges($HomematicModuleId);

		return $HomematicModuleId;
	}

	/** @}*/
?>