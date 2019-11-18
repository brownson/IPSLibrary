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

	/**@defgroup ipspowercontrol_visualization IPSPowerControl Visualisierung
	 * @ingroup ipspowercontrol
	 * @{
	 *
	 * Visualisierungen von IPSPowerControl
	 *
	 * IPSPowerControl WebFront Visualisierung:
	 *
	 *
	 *@}*/

	/**@defgroup ipspowercontrol_install IPSPowerControl Installation
	 * @ingroup ipspowercontrol
	 * @{
	 *
	 * Script zur kompletten Installation von IPSPowerControl.
	 *
	 * Vor der Installation muß das File IPSPowerControl_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page rquirements_IPSPowerControl Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.2
	 * - IPSLogger >= 2.50.1
	 * - IPSComponent >= 2.50.1
	 *
	 * @page install_IPSPowerControl Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSPowerControl Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSPowerControl_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.09.2012<br/>
	 *
	 */
	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSPowerControl');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSComponent','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",                   "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSPowerControl.inc.php",                "IPSLibrary::app::modules::IPSPowerControl");
	IPSUtils_Include ("IPSPowerControl_Constants.inc.php",      "IPSLibrary::app::modules::IPSPowerControl");
	IPSUtils_Include ("IPSPowerControl_Configuration.inc.php",  "IPSLibrary::config::modules::IPSPowerControl");

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
	$Mobile_PathIcon      = $moduleManager->GetConfigValue('PathIcon', 'Mobile');
	$Mobile_Name          = $moduleManager->GetConfigValue('Name', 'Mobile');
	$Mobile_Order         = $moduleManager->GetConfigValueInt('Order', 'Mobile');
	$Mobile_Icon          = $moduleManager->GetConfigValue('Icon', 'Mobile');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');

	$categoryIdCommon   = CreateCategory('Common',  $CategoryIdData, 10);
	$categoryIdValues   = CreateCategory('Values',  $CategoryIdData, 20);
	$categoryIdCustom   = CreateCategory('Custom',  $CategoryIdData, 30);

	// Add Scripts
	$scriptIdActionScript   = IPS_GetScriptIDByName('IPSPowerControl_ActionScript', $CategoryIdApp);
	$scriptIdNavPrev        = IPS_GetScriptIDByName('IPSPowerControl_NavigatePrev', $CategoryIdApp);
	$scriptIdNavNext        = IPS_GetScriptIDByName('IPSPowerControl_NavigateNext', $CategoryIdApp);
	IPS_SetIcon($scriptIdNavPrev, 'HollowArrowLeft');
	IPS_SetIcon($scriptIdNavNext, 'HollowArrowRight');
	$scriptIdCountPlus      = IPS_GetScriptIDByName('IPSPowerControl_NavigatePlus', $CategoryIdApp);
	$scriptIdCountMinus     = IPS_GetScriptIDByName('IPSPowerControl_NavigateMinus', $CategoryIdApp);
	IPS_SetIcon($scriptIdCountPlus,  'HollowArrowUp');
	IPS_SetIcon($scriptIdCountMinus, 'HollowArrowDown');
	
	$timerId_Refresh     = CreateTimer_CyclicBySeconds ('CalculateWattValues', $scriptIdActionScript, IPSPC_REFRESHINTERVAL_WATT) ;
	$timerId_Refresh     = CreateTimer_CyclicByMinutes ('CalculateKWHValues',  $scriptIdActionScript, IPSPC_REFRESHINTERVAL_KWH) ;

	$associationsTypeAndOffset   = array(
	                               IPSPC_TYPE_WATT        => 'Watt',
	                               IPSPC_TYPE_KWH         => 'kWh',
	                               IPSPC_TYPE_EURO        => 'Euro',
	                               IPSPC_TYPE_STACK       => 'Details',
	                               IPSPC_TYPE_STACK2      => 'Total',
	                               IPSPC_TYPE_PIE         => 'Pie',
	                               IPSPC_TYPE_OFF         => 'Off',
	                               IPSPC_OFFSET_SEPARATOR => ' ',
	                               IPSPC_OFFSET_PREV      => '<<',
	                               IPSPC_OFFSET_VALUE     => '0',
	                               IPSPC_OFFSET_NEXT      => '>>'
	                               );
	CreateProfile_Associations ('IPSPowerControl_TypeAndOffset',   $associationsTypeAndOffset);

	$associationsPeriodAndCount  = array(
	                              //IPSPC_PERIOD_HOUR     => 'Stunde',
	                              IPSPC_PERIOD_DAY      => 'Tag',
	                              IPSPC_PERIOD_WEEK     => 'Woche',
	                              IPSPC_PERIOD_MONTH    => 'Monat',
	                              IPSPC_PERIOD_YEAR     => 'Jahr',
	                              IPSPC_COUNT_SEPARATOR => ' ',
	                              IPSPC_COUNT_MINUS     => '-',
	                              IPSPC_COUNT_VALUE     => '1',
	                              IPSPC_COUNT_PLUS      => '+',
	                              );
	CreateProfile_Associations ('IPSPowerControl_PeriodAndCount',   $associationsPeriodAndCount);

	$associationsValues = array();
	foreach (IPSPowerControl_GetValueConfiguration() as $idx=>$data) {
		$associationsValues[$idx] = $data[IPSPC_PROPERTY_NAME];
	}
	CreateProfile_Associations ('IPSPowerControl_SelectValues',     $associationsValues);

	
	// ===================================================================================================
	// Add Variables
	// ===================================================================================================
	$variableIdTypeOffset  = CreateVariable(IPSPC_VAR_TYPEOFFSET,  1 /*Integer*/, $categoryIdCommon,  10, 'IPSPowerControl_TypeAndOffset',   $scriptIdActionScript,  IPSPC_TYPE_KWH, 'Clock');
	$variableIdPeriodCount = CreateVariable(IPSPC_VAR_PERIODCOUNT, 1 /*Integer*/, $categoryIdCommon,  20, 'IPSPowerControl_PeriodAndCount',  $scriptIdActionScript,  IPSPC_PERIOD_DAY, 'Clock');
	$variableIdTimeOffset  = CreateVariable(IPSPC_VAR_TIMEOFFSET,  1 /*Integer*/, $categoryIdCommon,  40, '',                                null,                   0, '');
	$variableIdTimeCount   = CreateVariable(IPSPC_VAR_TIMECOUNT,   1 /*Integer*/, $categoryIdCommon,  50, '',                                null,                   1, '');
	$variableIdChartHtml   = CreateVariable(IPSPC_VAR_CHARTHTML,   3 /*String*/,  $categoryIdCommon, 100, '~HTMLBox',                        $scriptIdActionScript, '<iframe frameborder="0" width="100%" height="530px"  src="../user/Highcharts/IPS_Template.php"</iframe>', 'Graph');

	foreach (IPSPowerControl_GetValueConfiguration() as $idx=>$data) {
		$valueType = $data[IPSPC_PROPERTY_VALUETYPE];
		switch($valueType) {
			case IPSPC_VALUETYPE_GAS:
				$variableIdValueM3     = CreateVariable(IPSPC_VAR_VALUEM3.$idx,    2 /*float*/,   $categoryIdValues,  100+$idx, '~Gas',    null,          0, 'Lightning');
				break;
			case IPSPC_VALUETYPE_WATER:
				$variableIdValueM3     = CreateVariable(IPSPC_VAR_VALUEM3.$idx,    2 /*float*/,   $categoryIdValues,  100+$idx, '~Water',    null,          0, 'Lightning');
				break;
			default:
				$variableIdValueKWH     = CreateVariable(IPSPC_VAR_VALUEKWH.$idx,    2 /*float*/,   $categoryIdValues,  100+$idx, '~Electricity',    null,          0, 'Lightning');
				$variableIdValueWatt    = CreateVariable(IPSPC_VAR_VALUEWATT.$idx,   2 /*float*/,   $categoryIdValues,  200+$idx, '~Watt.14490',     null,          0, 'Lightning');
		}
		$variableIdSelectValue  = CreateVariable(IPSPC_VAR_SELECTVALUE.$idx, 0 /*Boolean*/, $categoryIdCommon,  100+$idx, '~Switch', $scriptIdActionScript, 0, 'Lightning');
	}

	// ===================================================================================================
	// Activate Variable Logging
	// ===================================================================================================
	$archiveHandlerList = IPS_GetInstanceListByModuleID ('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
	$archiveHandlerId = $archiveHandlerList[0];
	foreach (IPSPowerControl_GetValueConfiguration() as $idx=>$data) {
		$variableId = @IPS_GetObjectIDByIdent(IPSPC_VAR_VALUEKWH.$idx, $categoryIdValues);
		if ($variableId!==false and !AC_GetLoggingStatus($archiveHandlerId, $variableId)) {
			AC_SetLoggingStatus($archiveHandlerId, $variableId, true);
			AC_SetAggregationType($archiveHandlerId, $variableId, 1);
		}
		$variableId = @IPS_GetObjectIDByIdent(IPSPC_VAR_VALUEWATT.$idx, $categoryIdValues);
		if ($variableId!==false and !AC_GetLoggingStatus($archiveHandlerId, $variableId)) {
			AC_SetLoggingStatus($archiveHandlerId, $variableId, true);
			//AC_SetAggregationType($archiveHandlerId, $variableId, 1);
		}
		$variableId = @IPS_GetObjectIDByIdent(IPSPC_VAR_VALUEM3.$idx, $categoryIdValues);
		if ($variableId!==false and !AC_GetLoggingStatus($archiveHandlerId, $variableId)) {
			AC_SetLoggingStatus($archiveHandlerId, $variableId, true);
			AC_SetAggregationType($archiveHandlerId, $variableId, 1);
		}
	}
	IPS_ApplyChanges($archiveHandlerId);

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		$categoryId_WebFront         = CreateCategoryPath($WFC10_Path);
		EmptyCategory($categoryId_WebFront);
		$categoryIdLeft  = CreateCategory('Left',  $categoryId_WebFront, 10);
		$categoryIdRight = CreateCategory('Right', $categoryId_WebFront, 20);

		$tabItem = $WFC10_TabPaneItem.$WFC10_TabItem;
		DeleteWFCItems($WFC10_ConfigId, $tabItem);
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem, $WFC10_TabPaneParent,  $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $tabItem,           $WFC10_TabPaneItem,    $WFC10_TabOrder,     $WFC10_TabName,     $WFC10_TabIcon, 1 /*Vertical*/, 360 /*Width*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $tabItem.'_Left',   $tabItem,   10, '', '', $categoryIdLeft   /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $tabItem.'_Right',  $tabItem,   20, '', '', $categoryIdRight  /*BaseId*/, 'false' /*BarBottomVisible*/);

		// Left Panel
		$instanceId = CreateDummyInstance("Stromkreise", $categoryIdLeft, 30);
		foreach (IPSPowerControl_GetValueConfiguration() as $idx=>$data) {
			if ($data[IPSPC_PROPERTY_DISPLAY]) {
				$variableIdSelectValue  = IPS_GetObjectIDByIdent(IPSPC_VAR_SELECTVALUE.$idx, $categoryIdCommon);
				$valueType = $data[IPSPC_PROPERTY_VALUETYPE];
				switch($valueType) {
					case IPSPC_VALUETYPE_GAS:
						CreateLink($data[IPSPC_PROPERTY_NAME], $variableIdSelectValue, $categoryIdLeft, $idx);
						break;
					case IPSPC_VALUETYPE_WATER:
						CreateLink($data[IPSPC_PROPERTY_NAME], $variableIdSelectValue, $categoryIdLeft, $idx);
						break;
					default:
						CreateLink($data[IPSPC_PROPERTY_NAME], $variableIdSelectValue, $instanceId, $idx);
				}
			}
		}

		// Right Panel
		CreateLink('Type/Offset', $variableIdTypeOffset,  $categoryIdRight, 10);
		CreateLink('Zeitraum',    $variableIdPeriodCount, $categoryIdRight, 20);
		CreateLink('Chart',       $variableIdChartHtml,   $categoryIdRight, 40);

		ReloadAllWebFronts();
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled ) {
		$mobileId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		$mobileId  = CreateCategory($Mobile_Name, $mobileId, $Mobile_Order, $Mobile_Icon);
		EmptyCategory($mobileId);
		
		CreateLink('Chart',         $variableIdChartHtml,   $mobileId, 10);

		$instanceIdChart  = CreateDummyInstance("Chart Auswahl", $mobileId, 20);
		CreateLink('Statistic', $variableIdTypeOffset,  $instanceIdChart, 10);

		$instanceIdChart  = CreateDummyInstance("Zeitraum", $mobileId, 30);
		CreateLink('Zeitraum',      $variableIdPeriodCount, $instanceIdChart, 50);
		CreateLink('Anzahl',        $variableIdTimeCount,   $instanceIdChart, 60);
		CreateLink('Anzahl -',      $scriptIdCountMinus,    $instanceIdChart, 70);
		CreateLink('Anzahl +',      $scriptIdCountPlus,     $instanceIdChart, 80);
		CreateLink('Zeit Offset',   $variableIdTimeOffset,  $instanceIdChart, 20);
		CreateLink('Zeit Zurück',   $scriptIdNavPrev,       $instanceIdChart, 30);
		CreateLink('Zeit Vorwärts', $scriptIdNavNext,       $instanceIdChart, 40);

		$instanceIdChart = CreateDummyInstance("Auswahl Verbraucher", $mobileId, 40);
		foreach (IPSPowerControl_GetValueConfiguration() as $idx=>$data) {
			if ($data[IPSPC_PROPERTY_DISPLAY]) {
				$variableIdSelectValue  = IPS_GetObjectIDByIdent(IPSPC_VAR_SELECTVALUE.$idx, $categoryIdCommon);
				$valueType = $data[IPSPC_PROPERTY_VALUETYPE];
				switch($valueType) {
					case IPSPC_VALUETYPE_GAS:
						CreateLink($data[IPSPC_PROPERTY_NAME], $variableIdSelectValue, $instanceIdChart, $idx);
						break;
					case IPSPC_VALUETYPE_WATER:
						CreateLink($data[IPSPC_PROPERTY_NAME], $variableIdSelectValue, $instanceIdChart, $idx);
						break;
					default:
						CreateLink($data[IPSPC_PROPERTY_NAME], $variableIdSelectValue, $instanceIdChart, $idx);
				}
			}
		}
	}

?>