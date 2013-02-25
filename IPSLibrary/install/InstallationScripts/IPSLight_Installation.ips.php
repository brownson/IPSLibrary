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

	/**@defgroup ipslight_visualization IPSLight Visualisierung
	 * @ingroup ipslight
	 * @{
	 *
	 * Visualisierungen von IPSLight
	 *
	 * IPSLight WebFront Visualisierung:
	 *
	 *
	 *@}*/

	/**@defgroup ipslight_install IPSLight Installation
	 * @ingroup ipslight
	 * @{
	 *
	 * Script zur kompletten Installation der IPSLight Steuerung.
	 *
	 * Vor der Installation muß das File IPSLight_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page rquirements_IPSLight Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.2
	 * - IPSLogger >= 2.50.1
	 * - IPSComponent >= 2.50.1
	 * - IPSMessageHandler >= 2.50.1
	 *
	 * @page install_IPSLight Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSLight Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSLight_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 19.03.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSLight');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSComponent','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSMessageHandler','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSLight.inc.php",                "IPSLibrary::app::modules::IPSLight");
	IPSUtils_Include ("IPSLight_Constants.inc.php",      "IPSLibrary::app::modules::IPSLight");
	IPSUtils_Include ("IPSLight_Configuration.inc.php",  "IPSLibrary::config::modules::IPSLight");

	$WFC10_Enabled        = $moduleManager->GetConfigValueBool('Enabled', 'WFC10');
	$WFC10_ConfigId       = $moduleManager->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
	$WFC10_Path           = $moduleManager->GetConfigValue('Path', 'WFC10');
	$WFC10_TabPaneItem    = $moduleManager->GetConfigValue('TabPaneItem', 'WFC10');
	$WFC10_TabPaneParent  = $moduleManager->GetConfigValue('TabPaneParent', 'WFC10');
	$WFC10_TabPaneName    = $moduleManager->GetConfigValue('TabPaneName', 'WFC10');
	$WFC10_TabPaneIcon    = $moduleManager->GetConfigValue('TabPaneIcon', 'WFC10');
	$WFC10_TabPaneOrder   = $moduleManager->GetConfigValueInt('TabPaneOrder', 'WFC10');

	$mobile_Enabled       = $moduleManager->GetConfigValue('Enabled', 'Mobile');
	$mobile_Path          = $moduleManager->GetConfigValue('Path', 'Mobile');
	$mobile_PathOrder     = $moduleManager->GetConfigValueInt('PathOrder', 'Mobile');
	$mobile_PathIcon      = $moduleManager->GetConfigValue('PathIcon', 'Mobile');

	$WFC10_Regenerate     = true;
	$mobile_Regenerate    = false;

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');

	$categoryIdSwitches   = CreateCategory('Switches',   $CategoryIdData, 10);
	$categoryIdGroups     = CreateCategory('Groups',     $CategoryIdData, 20);
	$categoryIdPrograms   = CreateCategory('Programs',   $CategoryIdData, 30);
	$categoryIdSimulation = CreateCategory('Simulation', $CategoryIdData, 40);

	// Add Scripts
	$scriptIdActionScript  = IPS_GetScriptIDByName('IPSLight_ActionScript', $CategoryIdApp);

	// Profiles
	CreateProfile_Switch ("IPSLight_Light", 'Aus', 'An', $Icon="", -1, 0x00ff00, $IconOff="BulbOff", $IconOn="BulbOn");
	CreateProfile_Count  ('IPSLight_SimulationDays', 1, 1, 31,   null, ' Tage',   null);
	CreateProfile_Associations ('IPSLight_SimulationMode', array(IPSLIGHT_SIMULATION_MODEDAYS => 'Tage zurück', 
	                                                             IPSLIGHT_SIMULATION_MODEUSR1 => 'Simulation 1',
	                                                             IPSLIGHT_SIMULATION_MODEUSR2 => 'Simulation 2'));

	$statusId = CreateVariable(IPSLIGHT_SIMULATION_VARSTATE, 0 /*Boolean*/, $categoryIdSimulation, 10, '~Switch', $scriptIdActionScript, false, 'Motion');
	$statusId = CreateVariable(IPSLIGHT_SIMULATION_VARMODE,  1 /*Integer*/, $categoryIdSimulation, 20, 'IPSLight_SimulationMode', $scriptIdActionScript, 0, 'Gear');
	$statusId = CreateVariable(IPSLIGHT_SIMULATION_VARDAYS,  1 /*Integer*/, $categoryIdSimulation, 30, 'IPSLight_SimulationDays', $scriptIdActionScript, 7, 'Clock');
	$statusId = CreateVariable(IPSLIGHT_SIMULATION_VARDATE,  3 /*String*/,  $categoryIdSimulation, 40, '~String', null, '', '');
	$statusId = CreateVariable(IPSLIGHT_SIMULATION_VARTIME,  3 /*String*/,  $categoryIdSimulation, 50, '~String', null, '', '');

	$directory = IPS_GetKernelDir().'\\Simulation';
	if (!file_exists($directory)) {
		mkdir($directory, 0, true);
	}

	// ===================================================================================================
	// Add Light Devices
	// ===================================================================================================
	$idx = 10;
	$lightConfig = IPSLight_GetLightConfiguration();
	foreach ($lightConfig as $deviceName=>$deviceData) {
		$deviceType = $deviceData[IPSLIGHT_TYPE];

		switch ($deviceType) {
			case IPSLIGHT_TYPE_SWITCH:
				$switchId = CreateVariable($deviceName,    0 /*Boolean*/, $categoryIdSwitches,  $idx, '~Switch', $scriptIdActionScript, false, 'Bulb');
				break;
			case IPSLIGHT_TYPE_DIMMER:
				$switchId = CreateVariable($deviceName,                       0 /*Boolean*/, $categoryIdSwitches,  $idx, '~Switch',        $scriptIdActionScript, false, 'Bulb');
				$levelId  = CreateVariable($deviceName.IPSLIGHT_DEVICE_LEVEL, 1 /*Integer*/, $categoryIdSwitches,  $idx, '~Intensity.100', $scriptIdActionScript, false, 'Intensity');
				break;
			case IPSLIGHT_TYPE_RGB:
				$switchId = CreateVariable($deviceName,                       0 /*Boolean*/, $categoryIdSwitches,  $idx, '~Switch',        $scriptIdActionScript, false, 'Bulb');
				$colorId  = CreateVariable($deviceName.IPSLIGHT_DEVICE_COLOR, 1 /*Integer*/, $categoryIdSwitches,  $idx, '~HexColor',      $scriptIdActionScript, false, 'HollowDoubleArrowRight');
				$levelId  = CreateVariable($deviceName.IPSLIGHT_DEVICE_LEVEL, 1 /*Integer*/, $categoryIdSwitches,  $idx, '~Intensity.100', $scriptIdActionScript, false, 'Intensity');
				break;
			default:
				trigger_error('Unknown DeviceType '.$deviceType.' found for Light '.$devicename);
		}
		$idx = $idx + 1;
	}

	// ===================================================================================================
	// Add Groups
	// ===================================================================================================
	$idx = 10;
	$groupConfig = IPSLight_GetGroupConfiguration();
	foreach ($groupConfig as $groupName=>$groupData) {
		$switchId     = CreateVariable($groupName,    0 /*Boolean*/, $categoryIdGroups,  $idx, '~Switch', $scriptIdActionScript, false, 'Bulb');
		$idx = $idx + 1;
	}

	// ===================================================================================================
	// Add Programs
	// ===================================================================================================
	$idx = 10;
	$programConfig = IPSLight_GetProgramConfiguration();
	foreach ($programConfig as $programName=>$programData) {
		$itemIdx = 0;
		$programAssociations = array();
		foreach ($programData as $programItemName=>$programItemData) {
			$programAssociations[]=$programItemName;
		}
		CreateProfile_Associations ('IPSLight_'.$programName, $programAssociations, "ArrowRight");
		$programId = CreateVariable($programName, 1 /*Integer*/, $categoryIdPrograms,  $idx,  'IPSLight_'.$programName, $scriptIdActionScript, 0);
		$idx = $idx + 1;
	}

	// Register Events for Device Synchronization
	// ------------------------------------------
	IPSUtils_Include ('IPSMessageHandler.class.php', 'IPSLibrary::app::core::IPSMessageHandler');
	$messageHandler = new IPSMessageHandler();
	$lightConfig = IPSLight_GetLightConfiguration();
	foreach ($lightConfig as $deviceName=>$deviceData) {
		$component = $deviceData[IPSLIGHT_COMPONENT];
		$componentParams = explode(',', $component);
		$componentClass = $componentParams[0];

		// Homematic
		if ($componentClass=='IPSComponentSwitch_Homematic') {
			$instanceId = IPSUtil_ObjectIDByPath($componentParams[1]);
			$variableId = @IPS_GetObjectIDByIdent('STATE', $instanceId);
			if ($variableId===false) {
				$moduleManager->LogHandler()->Log('Variable with Name STATE could NOT be found for Homematic Instance='.$instanceId);
			} else {
				$moduleManager->LogHandler()->Log('Register OnChangeEvent vor Homematic Instance='.$instanceId);
				$messageHandler->RegisterOnChangeEvent($variableId, $component, 'IPSModuleSwitch_IPSLight,');
			}
		// EIB
		} elseif ($componentClass=='IPSComponentSwitch_EIB') {
			$instanceId = IPSUtil_ObjectIDByPath($componentParams[1]);
			$variableId = @IPS_GetObjectIDByIdent('Value', $instanceId);
			if ($variableId===false) {
				$moduleManager->LogHandler()->Log('Variable with Name Value could NOT be found for EIB Instance='.$instanceId);
			} else {
				$moduleManager->LogHandler()->Log('Register OnChangeEvent vor EIB Instance='.$instanceId);
				$messageHandler->RegisterOnChangeEvent($variableId, $component, 'IPSModuleSwitch_IPSLight,');
			}
		} else {
			//$moduleManager->LogHandler()->Log('Found Component '.$componentClass);
		}
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$categoryId_WebFront                = CreateCategoryPath($WFC10_Path);
		if ($WFC10_Regenerate) {
			EmptyCategory($categoryId_WebFront);
			DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem);
			DeleteWFCItems($WFC10_ConfigId, 'Light_TP');
		}
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,  $WFC10_TabPaneParent, $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);

		$webFrontConfig = IPSLight_GetWebFrontConfiguration();
		$order = 10;
		foreach($webFrontConfig as $tabName=>$tabData) {
			$tabCategoryId	= CreateCategory($tabName, $categoryId_WebFront, $order);
			foreach($tabData as $WFCItem) {
				$order = $order + 10;
				switch($WFCItem[0]) {
					case IPSLIGHT_WFCSPLITPANEL:
						CreateWFCItemSplitPane ($WFC10_ConfigId, $WFCItem[1], $WFCItem[2]/*Parent*/,$order,$WFCItem[3],$WFCItem[4],(int)$WFCItem[5],(int)$WFCItem[6],(int)$WFCItem[7],(int)$WFCItem[8],$WFCItem[9]);
						break;
					case IPSLIGHT_WFCCATEGORY:
						$categoryId	= CreateCategory($WFCItem[1], $tabCategoryId, $order);
						CreateWFCItemCategory ($WFC10_ConfigId, $WFCItem[1], $WFCItem[2]/*Parent*/,$order, $WFCItem[3]/*Name*/,$WFCItem[4]/*Icon*/, $categoryId, 'false');
						break;
					case IPSLIGHT_WFCGROUP:
					case IPSLIGHT_WFCLINKS:
						$categoryId = IPS_GetCategoryIDByName($WFCItem[2], $tabCategoryId);
						if ($WFCItem[0]==IPSLIGHT_WFCGROUP) {
							$categoryId = CreateDummyInstance ($WFCItem[1], $categoryId, $order);
						}
						$links      = explode(',', $WFCItem[3]);
						$names      = $links;
						if (array_key_exists(4, $WFCItem)) {
							$names = explode(',', $WFCItem[4]);
						}
						foreach ($links as $idx=>$link) {
							$order = $order + 1;
							CreateLinkByDestination($names[$idx], get_VariableId($link,$categoryIdSwitches,$categoryIdGroups,$categoryIdPrograms), $categoryId, $order);
						}
						break;
					default:
						trigger_error('Unknown WFCItem='.$WFCItem[0]);
			   }
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($mobile_Enabled ) {
		$mobileId  = CreateCategoryPath($mobile_Path, $mobile_PathOrder, $mobile_PathIcon);
		if ($mobile_Regenerate) {
			EmptyCategory($mobileId);
		}
		$order = 10;
		foreach (IPSLight_GetMobileConfiguration() as $roomName=>$roomData) {
			if (is_array($roomData)) {
				$roomId	= CreateCategory($roomName, $mobileId, $order);
				foreach($roomData as $roomItem) {
					$order = $order + 10;
					switch($roomItem[0]) {
						case IPSLIGHT_WFCGROUP:
						case IPSLIGHT_WFCLINKS:
							$instanceId = $roomId;
							if ($roomItem[0]==IPSLIGHT_WFCGROUP) {
								$instanceId = CreateDummyInstance ($roomItem[1], $roomId, $order);
							}
							$links      = explode(',', $roomItem[2]);
							$names      = $links;
							if (array_key_exists(3, $roomItem)) {
								$names = explode(',', $roomItem[3]);
							}
							foreach ($links as $idx=>$link) {
								$order = $order + 1;
								CreateLinkByDestination($names[$idx], get_VariableId($link,$categoryIdSwitches,$categoryIdGroups,$categoryIdPrograms), $instanceId, $order);
							}
							break;
						 
						default:
							trigger_error('Unknown RoomItem='.$roomItem[0]);
				   }
				}
			} else {
				$links = explode(',', $roomData);
				foreach ($links as $link) {
					CreateLink($link, get_VariableId($link,$categoryIdSwitches,$categoryIdGroups,$categoryIdPrograms), $mobileId, $order);
					$order = $order + 10;
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_VariableId($name, $switchCategoryId, $groupCategoryId, $categoryIdPrograms) {
		$childrenIds = IPS_GetChildrenIDs($switchCategoryId);
		foreach ($childrenIds as $childId) {
			if (IPS_GetName($childId)==$name) {
				return $childId;
			}
		}
		$childrenIds = IPS_GetChildrenIDs($groupCategoryId);
		foreach ($childrenIds as $childId) {
			if (IPS_GetName($childId)==$name) {
				return $childId;
			}
		}
		$childrenIds = IPS_GetChildrenIDs($categoryIdPrograms);
		foreach ($childrenIds as $childId) {
			if (IPS_GetName($childId)==$name) {
				return $childId;
			}
		}
		trigger_error("$name could NOT be found in 'Switches' and 'Groups'");
	}

?>