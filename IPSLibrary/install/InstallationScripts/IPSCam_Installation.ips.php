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

	/**@defgroup ipscam_visualization IPSCam Visualisierung
	 * @ingroup ipscam
	 * @{
	 *
	 * Visualisierungen von IPSCam
	 *
	 * IPSCam WebFront Visualisierung:
	 *
	 *
	 *@}*/

	/**@defgroup ipscam_install IPSCam Installation
	 * @ingroup ipscam
	 * @{
	 *
	 * Script zur kompletten Installation der IPSCam Steuerung.
	 *
	 * Vor der Installation muß das File IPSCam_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page rquirements_IPSCam Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.2
	 * - IPSLogger >= 2.50.1
	 * - IPSComponent >= 2.50.1
	 *
	 * @page install_IPSCam Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSCam Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSCam_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.08.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSCam');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSComponent','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",          "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSCam.inc.php",                "IPSLibrary::app::modules::IPSCam");
	IPSUtils_Include ("IPSCam_Constants.inc.php",      "IPSLibrary::app::modules::IPSCam");
	IPSUtils_Include ("IPSCam_Configuration.inc.php",  "IPSLibrary::config::modules::IPSCam");

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
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');

	$categoryIdCommon   = CreateCategory('Common',  $CategoryIdData, 10);
	$categoryIdCams     = CreateCategory('Cams',    $CategoryIdData, 20);
	$categoryIdDisplay  = CreateCategory('Display', $CategoryIdData, 30);
	$categoryIdNavPanel = CreateCategory('NavigationPanel', $categoryIdDisplay,10);
	$categoryIdCamPanel = CreateCategory('CameraPanel',     $categoryIdDisplay,20);

	// Add Scripts
	$scriptIdActionScript   = IPS_GetScriptIDByName('IPSCam_ActionScript', $CategoryIdApp);
	$scriptIdPurgeFiles     = IPS_GetScriptIDByName('IPSCam_PurgeFiles', $CategoryIdApp);
	$scriptIdGenerateMovies = IPS_GetScriptIDByName('IPSCam_GenerateMovies', $CategoryIdApp);
	$scriptIdNavPicPrev     = IPS_GetScriptIDByName('IPSCam_NavPicPrev', $CategoryIdApp);
	$scriptIdNavPicNext     = IPS_GetScriptIDByName('IPSCam_NavPicNext', $CategoryIdApp);
	$scriptIdNavDayPrev     = IPS_GetScriptIDByName('IPSCam_NavDayPrev', $CategoryIdApp);
	$scriptIdNavDayNext     = IPS_GetScriptIDByName('IPSCam_NavDayNext', $CategoryIdApp);
	$scriptIdPictRef        = IPS_GetScriptIDByName('IPSCam_RefreshPicture', $CategoryIdApp);
	$scriptIdPictStore      = IPS_GetScriptIDByName('IPSCam_StorePicture', $CategoryIdApp);

	IPS_SetIcon($scriptIdNavPicPrev, 'HollowArrowLeft');
	IPS_SetIcon($scriptIdNavPicNext, 'HollowArrowRight');
	IPS_SetIcon($scriptIdNavDayPrev, 'HollowDoubleArrowLeft');
	IPS_SetIcon($scriptIdNavDayNext, 'HollowDoubleArrowRight');
	IPS_SetIcon($scriptIdPictRef,    'Repeat');
	IPS_SetIcon($scriptIdPictStore,  'ArrowRight');
	
	$timerId_PurgeFiles     = CreateTimer_OnceADay ('Purge', $scriptIdPurgeFiles, 0, 30) ;
	$timerId_GenerateMovies = CreateTimer_OnceADay ('Generate', $scriptIdGenerateMovies, 0, 45) ;

	// Profiles
	$associationsMode = array(IPSCAM_MODE_LIVE        => 'Live',
	                          IPSCAM_MODE_PICTURE     => 'Bild',
	                          IPSCAM_MODE_HISTORY     => 'History',
	                          IPSCAM_MODE_SETTINGS    => 'Einstellungen');

	$associationsCam = array();
	foreach (IPSCam_GetConfiguration() as $idx=>$data) {
		$associationsCam[$idx] = $data[IPSCAM_PROPERTY_NAME];
	}

	$associationsSize      = array(IPSCAM_SIZE_SMALL     => 'S',
	                               IPSCAM_SIZE_MIDDLE    => 'M',
	                               IPSCAM_SIZE_LARGE     => 'L');
 
	$associationsNavPict   = array(IPSCAM_NAV_BACK        => '<<',
	                               IPSCAM_NAV_FORWARD     => '>>');
 
	$associationsNavDays   = array(IPSCAM_DAY_BACK        => '<<',
	                               IPSCAM_DAY_FORWARD     => '>>');

	$associationsMotMode   = array(1*60                   => '1 Min',
	                               5*60                   => '5 Min',
	                               10*60                  => '10 Min',
	                               30*60                  => '30 Min',
	                               60*60                  => '1 Stunde',
	                               60*60*24               => '1 Tag',
	                               IPSCAM_VAL_DISABLED    => 'Aus');

	$associationsPictRef   = array(10                     => '10 Sek',
	                               30                     => '30 Sek',
	                               60                     => '1 Min',
	                               5*60                   => '5 Min',
	                               10*60                  => '10 Min',
	                               30*60                  => '30 Min',
	                               IPSCAM_VAL_DISABLED    => 'Aus');

	$associationsPictStore = array(30*60                  => '30 Min',
	                               60*60                  => '1 Stunde',
	                               60*60*4                => '4 Stunden',
	                               IPSCAM_VAL_DISABLED    => 'Aus');

	$associationsPictReset = array(1*60                   => '1 Min',
	                               5*60                   => '5 Min', 
	                               10*60                  => '10 Min',
	                               30*60                  => '30 Min',
	                               60*60                  => '1 Stunde',
	                               IPSCAM_VAL_DISABLED    => 'Aus');

	CreateProfile_Associations ('IPSCam_Size',       $associationsSize);
	CreateProfile_Associations ('IPSCam_Cam',        $associationsCam);
	CreateProfile_Associations ('IPSCam_Mode',       $associationsMode);
	CreateProfile_Associations ('IPSCam_NavPict',    $associationsNavPict);
	CreateProfile_Associations ('IPSCam_NavDays',    $associationsNavDays);
	CreateProfile_Associations ('IPSCam_MotMode',    $associationsMotMode);
	CreateProfile_Count        ('IPSCam_MotHist',    1, 1,   365,   null, ' Tage',   null);
	CreateProfile_Associations ('IPSCam_PictRef',    $associationsPictRef);
	CreateProfile_Associations ('IPSCam_PictStore',  $associationsPictStore);
	CreateProfile_Associations ('IPSCam_PictReset',  $associationsPictReset);
	CreateProfile_Count        ('IPSCam_PictHist',   1, 1,   365,   null, ' Tage',   null);

	// ===================================================================================================
	// Add Camera Devices
	// ===================================================================================================
	$variableIdCamSelect = CreateVariable(IPSCAM_VAR_CAMSELECT,1 /*Integer*/, $categoryIdCommon, 10, 'IPSCam_Cam',     $scriptIdActionScript, 0, 'Power');
	$variableIdCamHtml   = CreateVariable(IPSCAM_VAR_CAMHTML,  3 /*String*/,  $categoryIdCommon,100, '~HTMLBox',       $scriptIdActionScript, '<iframe frameborder="0" width="100%" height="530px"  src="../user/IPSCam/IPSCam_CameraHtml.php"</iframe>', 'Window');
	$variableIdHtml      = CreateVariable(IPSCAM_VAR_HTML,     3 /*String*/,  $categoryIdCommon,110, '~HTMLBox',       $scriptIdActionScript, '', 'Window');
	$variableIdiHtml     = CreateVariable(IPSCAM_VAR_IHTML,    3 /*String*/,  $categoryIdCommon,120, '~HTMLBox',       $scriptIdActionScript, '', 'Window');
	$variableIdCamPict   = CreateMedia (IPSCAM_VAR_CAMPICT, $categoryIdCommon, IPS_GetKernelDir().'Cams/0/Picture/Common.jpg', false, 1 /*Image*/, 'Image', 110);
	$variableIdCamHist   = CreateMedia (IPSCAM_VAR_CAMHIST, $categoryIdCommon, IPS_GetKernelDir().'Cams/0/History/20120101.jpg', false, 1 /*Image*/, 'Clock', 110); 
	$variableIdMode      = CreateVariable(IPSCAM_VAR_MODE,     1 /*Integer*/, $categoryIdCommon, 20, 'IPSCam_Mode',    $scriptIdActionScript, 0, 'Gear');
	$variableIdModeLive  = CreateVariable(IPSCAM_VAR_MODELIVE, 0 /*Boolean*/, $categoryIdCommon, 30, '~Switch',        $scriptIdActionScript, false, 'Window');
	$variableIdModePict  = CreateVariable(IPSCAM_VAR_MODEPICT, 0 /*Boolean*/, $categoryIdCommon, 40, '~Switch',        $scriptIdActionScript, false, 'Image');
	$variableIdModeHist  = CreateVariable(IPSCAM_VAR_MODEHIST, 0 /*Boolean*/, $categoryIdCommon, 50, '~Switch',        $scriptIdActionScript, false, 'Clock');
	$variableIdModeSett  = CreateVariable(IPSCAM_VAR_MODESETT, 0 /*Boolean*/, $categoryIdCommon, 60, '~Switch',        $scriptIdActionScript, false, 'Gear');
	$variableIdNavPict   = CreateVariable(IPSCAM_VAR_NAVPICT,  1 /*Integer*/, $categoryIdCommon, 70, 'IPSCam_NavPict', $scriptIdActionScript, -1, 'HollowArrowRight');
	$variableIdNavDays   = CreateVariable(IPSCAM_VAR_NAVDAYS,  1 /*Integer*/, $categoryIdCommon, 80, 'IPSCam_NavDays', $scriptIdActionScript, -1, 'HollowDoubleArrowRight');
	$variableIdNavTime   = CreateVariable(IPSCAM_VAR_NAVTIME,  3 /*String*/,  $categoryIdCommon, 90, '~String',        null,  date(IPSCAM_NAV_DATEFORMATDISP), 'Clock');

	$camConfig = IPSCam_GetConfiguration();
	foreach ($camConfig as $idx=>$data) {
		$categoryIdCamX      = CreateCategory($idx, $categoryIdCams, $idx);
		$variableIdCamPowerX = CreateVariable(IPSCAM_VAR_CAMPOWER,   0 /*Boolean*/,  $categoryIdCamX, 10, '~Switch',             $scriptIdActionScript, false, 'Power');
		$variableIdCamHtmlX  = CreateVariable(IPSCAM_VAR_CAMHTML,    3 /*String*/,   $categoryIdCamX, 20, '~HTMLBox',            $scriptIdActionScript, '<iframe frameborder="0" width="100%" height="530px"  src="../user/IPSCam/IPSCam_Camera'.$idx.'.php"</iframe>', 'Window');
		$variableIdCamPictX  = CreateMedia (IPSCAM_VAR_CAMPICT, $categoryIdCamX, IPS_GetKernelDir().'Cams/'.$idx.'/Picture/Current.jpg',  false, 1 /*Image*/, 'Image', 30); 
    	$variableIdCamHistX  = CreateMedia (IPSCAM_VAR_CAMHIST, $categoryIdCamX, IPS_GetKernelDir().'Cams/'.$idx.'/History/20120101.jpg', false, 1 /*Image*/, 'Clock', 110); 
		$componentParams     = $data[IPSCAM_PROPERTY_COMPONENT];
		$component           = IPSComponent::CreateObjectByParams($componentParams);
		$urlStream           = $component->Get_URLLiveStream();
		$variableIdCamStreamX= CreateMediaStream (IPSCAM_VAR_CAMSTREAM, $categoryIdCamX, $urlStream,'Image', 40); 

		$variableIdMotMode   = CreateVariable(IPSCAM_VAR_MOTMODE,    1 /*Integer*/,  $categoryIdCamX, 100, 'IPSCam_MotMode',     $scriptIdActionScript, IPSCAM_VAL_DISABLED, 'Motion');
		$variableIdMotTime   = CreateVariable(IPSCAM_VAR_MOTTIME,    3 /*String*/,   $categoryIdCamX, 110, '~String',            $scriptIdActionScript, '13:00', 'Clock');
		$variableIdMotHist   = CreateVariable(IPSCAM_VAR_MOTHIST,    1 /*Integer*/,  $categoryIdCamX, 120, 'IPSCam_MotHist',     $scriptIdActionScript, 7, 'Image');
		$variableIdMotSize   = CreateVariable(IPSCAM_VAR_MOTSIZE,    1 /*Integer*/,  $categoryIdCamX, 130, 'IPSCam_Size',        $scriptIdActionScript, 1, 'Distance');
		$variableIdPictRef   = CreateVariable(IPSCAM_VAR_PICTREF,    1 /*Integer*/,  $categoryIdCamX, 200, 'IPSCam_PictRef',     $scriptIdActionScript, IPSCAM_VAL_DISABLED, 'Repeat');
		$variableIdPictStore = CreateVariable(IPSCAM_VAR_PICTSTORE,  1 /*Integer*/,  $categoryIdCamX, 210, 'IPSCam_PictStore',   $scriptIdActionScript, IPSCAM_VAL_DISABLED, 'Lightning');
		$variableIdPictReset = CreateVariable(IPSCAM_VAR_PICTRESET,  1 /*Integer*/,  $categoryIdCamX, 220, 'IPSCam_PictReset',   $scriptIdActionScript, IPSCAM_VAL_DISABLED, 'Cross');
		$variableIdPictHist  = CreateVariable(IPSCAM_VAR_PICTHIST,   1 /*Integer*/,  $categoryIdCamX, 230, 'IPSCam_PictHist',    $scriptIdActionScript, 14, 'Image');
		$variableIdPictSize  = CreateVariable(IPSCAM_VAR_PICTSIZE,   1 /*Integer*/,  $categoryIdCamX, 240, 'IPSCam_Size',        $scriptIdActionScript, 1, 'Distance');
		$variableIdNavTime   = CreateVariable(IPSCAM_VAR_NAVTIME,    3 /*String*/,   $categoryIdCamX, 300, '~String',            null,                  date(IPSCAM_NAV_DATEFORMATDISP), 'Clock');
		$variableIdNavFile   = CreateVariable(IPSCAM_VAR_NAVFILE,    3 /*String*/,   $categoryIdCamX, 310, '~String',            null,                  date(IPSCAM_NAV_DATEFORMATFILE), '');
		
		IPSCAM_CreateDirectory(IPS_GetKernelDir().'Cams/'.$idx.'/History/');
		IPSCAM_CreateDirectory(IPS_GetKernelDir().'Cams/'.$idx.'/Picture/');
		IPSCAM_CreateDirectory(IPS_GetKernelDir().'Cams/'.$idx.'/MotionCapture/');
	}

	// Remove unused Variables
	$variableIdSize   = @IPS_GetObjectIDByIdent(Get_IdentByName('Size'), $categoryIdCommon);
	if ($variableIdSize !== false) {
		IPS_DeleteVariable($variableIdSize);
	}
	$variableIdNavFile   = @IPS_GetObjectIDByIdent(Get_IdentByName(IPSCAM_VAR_NAVFILE), $categoryIdCommon);
	if ($variableIdNavFile !== false) {
		IPS_DeleteVariable($variableIdNavFile);
	}


	function IPSCAM_CreateDirectory($directory) {
		if (!file_exists($directory)) {
			mkdir($directory, 0, true);
		}
	}
	
	$baseId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSCam');
	$displayCategoryId  = IPS_GetObjectIDByIdent('Display', $baseId);
	$categoryIdNavPanel = IPS_GetObjectIDByIdent('NavigationPanel', $displayCategoryId);
	$categoryIdCamPanel = IPS_GetObjectIDByIdent('CameraPanel', $displayCategoryId);
	$categoryIdCams     = IPS_GetObjectIDByIdent('Cams', $baseId);
	$categoryIdCommon   = IPS_GetObjectIDByIdent('Common', $baseId);

	CreateLink('Auswahl',  IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $categoryIdCommon), $categoryIdCamPanel, 10);
	CreateLink('Kamera',   IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMHTML,   $categoryIdCommon), $categoryIdCamPanel, 20);
	CreateLink('Bild',     IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMPICT,   $categoryIdCommon), $categoryIdCamPanel, 30);
	CreateLink('History',  IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMHIST,   $categoryIdCommon), $categoryIdCamPanel, 40);
	$instanceId = CreateDummyInstance("Einstellungen", $categoryIdCamPanel, 30);

	$instanceId = CreateDummyInstance("Modus", $categoryIdNavPanel, 10);
	CreateLink('Live',           IPS_GetObjectIDByIdent(IPSCAM_VAR_MODELIVE, $categoryIdCommon), $instanceId, 10);
	CreateLink('Bild',           IPS_GetObjectIDByIdent(IPSCAM_VAR_MODEPICT, $categoryIdCommon), $instanceId, 20);
	CreateLink('History',        IPS_GetObjectIDByIdent(IPSCAM_VAR_MODEHIST, $categoryIdCommon), $instanceId, 30);
	CreateLink('Einstellungen',  IPS_GetObjectIDByIdent(IPSCAM_VAR_MODESETT, $categoryIdCommon), $instanceId, 40);
	$instanceId = CreateDummyInstance("Navigation", $categoryIdNavPanel, 20);
	CreateLink('Bilder',         IPS_GetObjectIDByIdent(IPSCAM_VAR_NAVPICT, $categoryIdCommon), $instanceId, 10);
	CreateLink('Tage',           IPS_GetObjectIDByIdent(IPSCAM_VAR_NAVDAYS, $categoryIdCommon), $instanceId, 20);
	//CreateLink('Uhrzeit',        IPS_GetObjectIDByIdent(IPSCAM_VAR_NAVTIME, $categoryIdCommon), $instanceId, 30);
	$instanceId = CreateDummyInstance("Bild", $categoryIdNavPanel, 30);
	CreateLink('Aktualisieren',  IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_RefreshPicture'), $instanceId, 10);
	CreateLink('Speichern',      IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_StorePicture'), $instanceId, 20);
	$instanceId = CreateDummyInstance("Kamera Einstellungen", $categoryIdNavPanel, 35);
	$instanceId = CreateDummyInstance("Power", $categoryIdNavPanel, 40);
	$id = CreateLink('Power',          IPS_GetObjectIDByIdent(IPSCAM_VAR_MODESETT, $categoryIdCommon), $instanceId, 10);
	IPS_SetIcon($id, 'Power');
	$id = CreateLink('WLAN',           IPS_GetObjectIDByIdent(IPSCAM_VAR_MODESETT, $categoryIdCommon), $instanceId, 10);
	IPS_SetIcon($id, 'Intensity');

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		//$categoryId_WebFront         = CreateCategoryPath($WFC10_Path);
		//EmptyCategory($categoryId_WebFront);

		$tabItem = $WFC10_TabPaneItem.$WFC10_TabItem;
		DeleteWFCItems($WFC10_ConfigId, $tabItem);
		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem, $WFC10_TabPaneParent,  $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $tabItem,           $WFC10_TabPaneItem,    $WFC10_TabOrder,     $WFC10_TabName,     $WFC10_TabIcon, 1 /*Vertical*/, 330 /*Width*/, 0 /*Target=Pane1*/, 1/*UsePixel*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $tabItem.'_Left',   $tabItem,   10, '', '', $categoryIdNavPanel   /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $tabItem.'_Right',  $tabItem,   20, '', '', $categoryIdCamPanel   /*BaseId*/, 'false' /*BarBottomVisible*/);

		ReloadAllWebFronts();
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Mobile Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($Mobile_Enabled ) {
		$mobileId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		$mobileId  = CreateCategoryPath($Mobile_Path.'.'.$Mobile_Name, $Mobile_Order, $Mobile_Icon);
		EmptyCategory($mobileId);
		
		$camConfig = IPSCam_GetConfiguration();
		foreach ($camConfig as $cameraIdx=>$data) {
			$mobileIdCam    = CreateCategory($data[IPSCAM_PROPERTY_NAME], $mobileId, $cameraIdx, 'Image');
			$categoryIdCam      = IPS_GetCategoryIDByName($cameraIdx, $categoryIdCams);

			CreateLink('Live',                IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMHTML, $categoryIdCam),  $mobileIdCam, 10);
			CreateLink('Bild',                IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMPICT, $categoryIdCam),  $mobileIdCam, 20);
			CreateLink('Bild Aktualisieren',  IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_RefreshPicture'.($cameraIdx+1)), $mobileIdCam, 30);
			CreateLink('Bild Speichern',      IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_StorePicture'.($cameraIdx+1)),   $mobileIdCam, 40);

			if (array_key_exists(IPSCAM_PROPERTY_SWITCHPOWER, $camConfig[$cameraIdx])) { 
				$variableIdPower = $camConfig[$cameraIdx][IPSCAM_PROPERTY_SWITCHPOWER];
				if ($variableIdPower<>'') {
					$id = CreateLink('Power',  IPSUtil_ObjectIDByPath($variableIdPower), $mobileIdCam, 50);
					IPS_SetIcon($id, 'Power');
				}
			}

			if (array_key_exists(IPSCAM_PROPERTY_SWITCHWLAN, $camConfig[$cameraIdx])) { 
				$variableIdWLAN = $camConfig[$cameraIdx][IPSCAM_PROPERTY_SWITCHWLAN];
				if ($variableIdWLAN<>'') {
					$id = CreateLink('WLAN',  IPSUtil_ObjectIDByPath($variableIdWLAN), $mobileIdCam, 60);
					$id = IPS_SetIcon($id, 'Intensity');
				}
			}

			$categoryIdSettings = CreateCategory('Einstellungen', $categoryIdCam, 100);
			$instanceIdDetail  = CreateDummyInstance("Allgemein", $categoryIdSettings, 10);
			CreateLink('Bild Aktualisierung',    IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTREF, $categoryIdCam), $instanceIdDetail, 10);
			CreateLink('Aktivierung Bild Modus', IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTRESET, $categoryIdCam), $instanceIdDetail, 30);
			$instanceIdDetail  = CreateDummyInstance("Automatische Bilder", $categoryIdSettings, 10);
			CreateLink('Autom. Bild Speicherung',IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTSTORE, $categoryIdCam), $instanceIdDetail, 20);
			CreateLink('Bild Historisierung',    IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTHIST, $categoryIdCam), $instanceIdDetail, 40);
			CreateLink('Bildgröße',              IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTSIZE, $categoryIdCam), $instanceIdDetail, 50);
			$instanceIdDetail  = CreateDummyInstance("Zeitraffer", $categoryIdSettings, 10);
			CreateLink('Modus',       IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTMODE, $categoryIdCam), $instanceIdDetail, 60);
			CreateLink('Abstand',     IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTTIME, $categoryIdCam), $instanceIdDetail, 70);
			CreateLink('Zeitraum',    IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTHIST, $categoryIdCam), $instanceIdDetail, 80);
			CreateLink('Bildgröße',   IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTSIZE, $categoryIdCam), $instanceIdDetail, 90);
		}

		$instanceIdStreams = CreateDummyInstance("Live Streams", $mobileId, 100);
		$camConfig = IPSCam_GetConfiguration();
		foreach ($camConfig as $cameraIdx=>$data) {
			$categoryIdCam   = IPS_GetCategoryIDByName($cameraIdx, $categoryIdCams);
			CreateLink($data[IPSCAM_PROPERTY_NAME],  IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMHTML, $categoryIdCam),  $instanceIdStreams, 10);
		}

		$instanceIdHistory = CreateDummyInstance("History", $mobileId, 200);
		CreateLink('Auswahl',       IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $categoryIdCommon), $instanceIdHistory, 10);
		CreateLink('History Bild',  IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMHIST,   $categoryIdCommon), $instanceIdHistory, 20);
		CreateLink('Uhrzeit',       IPS_GetObjectIDByIdent(IPSCAM_VAR_NAVTIME,   $categoryIdCommon), $instanceIdHistory, 30);
		CreateLink('Voriges Bild',  IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_NavPicPrev'), $instanceIdHistory, 100);
		CreateLink('Nächstes Bild', IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_NavPicNext'), $instanceIdHistory, 110);
		CreateLink('Voriger Tag',   IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_NavDayPrev'), $instanceIdHistory, 120);
		CreateLink('Nächster Tag',  IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_NavDayNext'), $instanceIdHistory, 130);
	}

?>