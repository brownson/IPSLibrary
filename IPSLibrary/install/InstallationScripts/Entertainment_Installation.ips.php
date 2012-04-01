<?
	/**@defgroup entertainment_install Entertainment Installation
	 * @ingroup entertainment
	 * @{
	 *
	 * Script zur kompletten Installation der Entertainment Steuerung.
	 *
	 * Vor der Installation muß das File Entertainment_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page rquirements_entertainment Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 * - abhängig von der Konfiguration kann es noch weitere Abhängigkeiten geben.
	 *
	 * @page install_entertainment Installations Schritte
	 * Folgende Schritte sind zur Installation der Entertainment Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          Entertainment_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 31.01.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('Entertainment');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",                "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("Entertainment_Configuration.inc.php", "IPSLibrary::config::modules::Entertainment");

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
	$Mobile_Name          = $moduleManager->GetConfigValue('Name', 'Mobile');
	$Mobile_Order         = $moduleManager->GetConfigValueInt('Order', 'Mobile');
	$Mobile_Icon          = $moduleManager->GetConfigValue('Icon', 'Mobile');

	$WFC10_Recreate = true;
	$Mobile_Recreate= true;

	$ProgramDeleteExistingProfiles = true;
	
	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	echo "--- Entertainment Installation -------------------------------------------------------------------\n";
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');

	$CategoryIdDevices       = CreateCategory('Devices',    $CategoryIdData, 10);
	$CategoryIdRoomes        = CreateCategory('Roomes',     $CategoryIdData, 20);

	// Add Scripts
	
	echo "--- Add Scripts ------------------------------------------------------------------------ \n";
   $ScriptIdAllOff         = IPS_GetScriptIDByName('Entertainment_AllRoomesOff',     $CategoryIdApp);
   $ScriptIdConnASyn       = IPS_GetScriptIDByName('Entertainment_ConnectAsynchron', $CategoryIdApp);
   $ScriptIdPostInstall    = IPS_GetScriptIDByName('Entertainment_PostInstallation', $CategoryIdApp);
   $ScriptIdInterface      = IPS_GetScriptIDByName('Entertainment_Interface',        $CategoryIdApp);

	// Delete existing Entertainment Profiles
	if ($ProgramDeleteExistingProfiles) {
		$Profiles = IPS_GetVariableProfileList();
		foreach ($Profiles as $Profile) {
		   if (strpos( $Profile, 'Entertainment_')===0) {
		      echo "Delete Profile '$Profile'\n";
		      IPS_DeleteVariableProfile($Profile);
		   }
		}
	}

	// Generate Events for Interfaces
	echo "--- Interfaces ------------------------------------------------------\n";
   $CommConfig  = get_CommunicationConfiguration();
   $idx         = 100;
   foreach ($CommConfig as $CommName => $CommProperties) {
      if (array_key_exists(c_Property_ScriptSnd, $CommProperties)) {
         $scriptFile  = $CommProperties[c_Property_ScriptSnd];
         $scriptName  = str_replace('.inc', '', str_replace('.php', '', str_replace('.ips', '',$scriptFile)));
		   $id_ScriptSnd   = IPS_GetScriptIDByName($scriptName, $CategoryIdApp);
		}
      if (array_key_exists(c_Property_ScriptRcv, $CommProperties)) {
         $scriptFile  = $CommProperties[c_Property_ScriptRcv];
         $scriptName  = str_replace('.inc', '', str_replace('.php', '', str_replace('.ips', '',$scriptFile)));
		   $id_ScriptRcv   = IPS_GetScriptIDByName($scriptName, $CategoryIdApp);
		}
      if (array_key_exists(c_Property_Variables, $CommProperties)) {
         $variables   = $CommProperties[c_Property_Variables];
         $instance    = $CommProperties[c_Property_Instance];
         $id_Instance = get_ObjectIDByPath($instance);
         if ($id_ScriptRcv ===null) {
				echo "No Receive Script found for Communication $CommName\n";
				exit;
			}
			foreach(explode(',',$variables) as $variable) {
				$id_Variable = IPS_GetVariableIDByName($variable, $id_Instance);
				$id_Event    = CreateEvent ($CommName.'_'.$variable, $id_Variable, $id_ScriptRcv, $TriggerType=0/*ByRefresh*/);
			}
      }
		$idx = $idx + 10;
   }

	// Generate Roomes and Controls
	echo "--- Create Roomes and Controls ---------------------------------------------------------\n";
	$RoomData          = get_RoomConfiguration();
	if (count($RoomData)==0) {
		throw new Exception('No Roomes defined, see Demo Configuration File for an Example!!!');
	}
	$RoomOrder         = 100;
	foreach($RoomData as $RoomName => $RoomProperties) {
		$RoomId       = CreateCategory($RoomName, $CategoryIdRoomes, $RoomOrder);
		$ControlOrder = 10;
		foreach($RoomProperties as $ControlType => $ControlData) {
			if (!is_array($ControlData)) continue 1;
         $ControlId = CreateControl ($ControlType, $ControlData, $RoomId, $ScriptIdInterface, false, $ControlOrder);
         $ControlOrder = $ControlOrder + 10;
		}
      $RoomOrder = $RoomOrder + 100;
	}

	// Generate Devices and Controls
	echo "--- Create Devices and Controls --------------------------------------------------------\n";
	$DeviceData          = get_DeviceConfiguration();
	if (count($DeviceData)==0) {
		throw new Exception('No Devices defined, see Demo Configuration File for an Example!!!');
	}
	$DeviceOrder         = 100;
	foreach($DeviceData as $DeviceName => $DeviceProperties) {
		$DeviceId     = CreateCategory($DeviceName, $CategoryIdDevices, $DeviceOrder);
		$ControlOrder = 10;
		foreach($DeviceProperties as $ControlType => $ControlData) {
			if (!is_array($ControlData)) continue 1;
         CreateControl ($ControlType, $ControlData, $DeviceId, $ScriptIdInterface, $ControlOrder);
         $ControlOrder = $ControlOrder + 10;
		}
      $DeviceOrder = $DeviceOrder + 100;
      // Process Installation Script of Device
      if (array_key_exists(c_Property_Installation, $DeviceProperties)) {
         $InstallScript = $DeviceProperties[c_Property_Installation];
			try {
			   echo 'EXECUTE Device specific Installation Procedure: '.$InstallScript."\n";
				include_once $InstallScript;
				$Function       = new ReflectionFunction('Installation');
				$Function->invoke($DeviceId);
			} catch (Exception $e) {
		     	echo 'Error Executing Function '.$FunctionName.':'.$e->getMessage()."\n";
		     	exit;
			}
      }
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	// Webfront Definition
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($WFC10_Enabled) {
		echo "--- Create WebFront Interface ----------------------------------------------------------\n";
		$WebFrontId               = CreateCategoryPath($WFC10_Path);
		if ($WFC10_Recreate) {
			EmptyCategory($WebFrontId);
			DeleteWFCItems($WFC10_ConfigId, $WFC10_TabPaneItem);
		}

		$UniqueId                 = date('Hi');
		$ID_CategoryWebFrontOverview            = CreateCategory(  'Overview',      $WebFrontId,         100);
		$ID_CategoryWebFrontOverviewLeft        = CreateCategory(    'Left',        $ID_CategoryWebFrontOverview,  10);
		$ID_CategoryWebFrontOverviewRightTop    = CreateCategory(    'RightTop',    $ID_CategoryWebFrontOverview,  10);
		$ID_CategoryWebFrontOverviewRightBottom = CreateCategory(    'RightBottom', $ID_CategoryWebFrontOverview,  20);

		CreateWFCItemTabPane   ($WFC10_ConfigId, $WFC10_TabPaneItem,                          $WFC10_TabPaneParent,         $WFC10_TabPaneOrder, $WFC10_TabPaneName, $WFC10_TabPaneIcon);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvSPLeft',              $WFC10_TabPaneItem,               0, $WFC10_TabName1, $WFC10_TabIcon1, 1 /*Vertical*/, 40 /*Width*/, 0 /*Target=Pane1*/, 0/*Percent*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvCatLeft'.$UniqueId,   $WFC10_TabPaneItem.'_OvSPLeft',  10, '', '', $ID_CategoryWebFrontOverviewLeft /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemSplitPane ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvSPRight',             $WFC10_TabPaneItem.'_OvSPLeft',  20, '', '', 0 /*Horizontal*/, 50 /*Width*/, 0 /*Target=Pane1*/, 0/*Percent*/, 'true');
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvCatTop.'.$UniqueId,   $WFC10_TabPaneItem.'_OvSPRight', 10, '', '', $ID_CategoryWebFrontOverviewRightTop /*BaseId*/, 'false' /*BarBottomVisible*/);
		CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_OvCatBottom'.$UniqueId, $WFC10_TabPaneItem.'_OvSPRight', 20, '', '', $ID_CategoryWebFrontOverviewRightBottom /*BaseId*/, 'false' /*BarBottomVisible*/);

		CreateLink('Alle Räume Ausschalten',  $ScriptIdAllOff,  $ID_CategoryWebFrontOverviewRightTop, 1000);
	}


	if ($Mobile_Enabled) {
		$iPhoneId  = CreateCategoryPath($Mobile_Path, $Mobile_PathOrder, $Mobile_PathIcon);
		if ($Mobile_Recreate) {
		   EmptyCategory($iPhoneId);
		}
		CreateLink('Alle Räume Ausschalten',  $ScriptIdAllOff,  $iPhoneId,                            1000);
	}

	// Link to Roomes and Room Controls
	$RoomOrder = 1;
	foreach($RoomData as $RoomItem => $RoomProperties) {
	   $RoomId          = IPS_GetCategoryIDByName($RoomItem, $CategoryIdRoomes);

	   // Create Link to Room
	   $RoomName = $RoomProperties[c_Property_Name];
	   if ($RoomName=="") continue;
		if ($Mobile_Enabled) $ID_RoomiPhone   = CreateCategory($RoomName, $iPhoneId,    $RoomOrder);
		if ($WFC10_Enabled)  $ID_RoomWebfront = CreateCategory($RoomName, $WebFrontId,  $RoomOrder);
		$RoomOrder       = $RoomOrder + 1;
		$DeviceOrder     = 10;
		if ($WFC10_Enabled) CreateWFCItemCategory  ($WFC10_ConfigId, $WFC10_TabPaneItem.'_'.$RoomOrder.'_'.$UniqueId,$WFC10_TabPaneItem, $RoomOrder,$RoomName, '', $ID_RoomWebfront /*BaseId*/, 'false' /*BarBottomVisible*/);
		foreach($RoomProperties as $ControlType => $ControlData) {
		   if (!is_array($ControlData)) continue 1;
		   $ControlName = $ControlData[c_Property_Name];
			$SwitchId = IPS_GetVariableIDByName($ControlName, $RoomId);

			// Create Link to RoomPower Switch
		   if ($ControlType == c_Control_RoomPower) {
				if ($WFC10_Enabled)  CreateLink($RoomName,     $SwitchId,  $ID_CategoryWebFrontOverviewRightTop, $RoomOrder);
				if ($WFC10_Enabled)  CreateLink($ControlName,  $SwitchId,  $ID_RoomWebfront,                     $DeviceOrder);
				if ($Mobile_Enabled) CreateLink($RoomName,     $SwitchId,  $iPhoneId,                            $RoomOrder);
				if ($Mobile_Enabled) CreateLink($ControlName,  $SwitchId,  $ID_RoomiPhone,                       $DeviceOrder);

			// Create Link to RoomSource Switch
			} else if ($ControlType == c_Control_Source) {
				if ($WFC10_Enabled)  CreateLink($RoomName,  $SwitchId,  $ID_CategoryWebFrontOverviewRightBottom, $RoomOrder);
				if ($WFC10_Enabled)  CreateLink($ControlName,  $SwitchId,  $ID_RoomWebfront,                     $DeviceOrder);
				if ($Mobile_Enabled) CreateLink($ControlName,  $SwitchId,  $ID_RoomiPhone,                       $DeviceOrder);

			} else if ($ControlType==c_Control_RemoteSource or $ControlType==c_Control_RemoteVolume) {
				if ($WFC10_Enabled) CreateLink($ControlName,  $SwitchId,  $ID_RoomWebfront,                     $DeviceOrder);

			} else if ($ControlType==c_Control_iRemoteSource or $ControlType==c_Control_iRemoteVolume) {
				if ($Mobile_Enabled) CreateLink($ControlName,  $SwitchId,  $ID_RoomiPhone,                       $DeviceOrder);

			} else {
			   if ($WFC10_Enabled) {
					if (array_key_exists(c_Property_Group, $ControlData)) {
						$GroupSwitchId = IPS_GetVariableIDByName($ControlData[c_Property_Group], $RoomId);
						CreateLink($ControlData[c_Property_Group],  $GroupSwitchId,  $ID_RoomWebfront,   $DeviceOrder);
						$GroupId = CreateDummyInstance($ControlData[c_Property_Group], $ID_RoomWebfront, $DeviceOrder+1);
						CreateLink($ControlName,  $SwitchId,  $GroupId,          $DeviceOrder);
					} else {
					   CreateLink($ControlName,  $SwitchId,  $ID_RoomWebfront,  $DeviceOrder);
					}
				}
			   if ($Mobile_Enabled) {
					if ($ControlType==c_Control_Group) {
					} else if (array_key_exists(c_Property_Group, $ControlData)) {
						$GroupId = CreateCategory($ControlData[c_Property_Group], $ID_RoomiPhone, $DeviceOrder);
						CreateLink($ControlName,  $SwitchId,  $GroupId,          $DeviceOrder);
					} else {
					   CreateLink($ControlName,  $SwitchId,  $ID_RoomiPhone,  $DeviceOrder);
					}
				}
			}
			$DeviceOrder = $DeviceOrder + 10;
		}
	}

	// Link to Devices and Device Controls
	$Order = 100;
	if ($Mobile_Enabled) $ID_iPhoneDevices = CreateDummyInstance("Geräte", $iPhoneId, 2000);
	foreach($DeviceData as $DeviceItem => $DeviceProperties) {
		$DeviceId        = IPS_GetCategoryIDByName($DeviceItem, $CategoryIdDevices);

	   $DeviceName = $DeviceProperties[c_Property_Name];
	   if ($DeviceName=="") continue;

		foreach($DeviceProperties as $ControlType => $ControlData) {
		   if ($ControlType == c_Control_DevicePower) {
				$SwitchId = IPS_GetVariableIDByName($ControlData[c_Property_Name], $DeviceId);
				// Create Link to DevicePower Switch
				if ($WFC10_Enabled)  CreateLink($DeviceName,  $SwitchId,  $ID_CategoryWebFrontOverviewLeft,    $Order);
				if ($Mobile_Enabled) CreateLink($DeviceName,  $SwitchId,  $ID_iPhoneDevices,                   $Order);
				$Order = $Order + 10;
			}
		}
	}

	// Register Variables
	// -------------------
	echo "--- Register Variable Constants --------------------------------------------------------\n";
	SetVariableConstant ("c_ID_Devices",                 $CategoryIdDevices,  'Entertainment_IDs.inc.php', "IPSLibrary::app::modules::Entertainment");
	SetVariableConstant ("c_ID_Roomes",                  $CategoryIdRoomes,   'Entertainment_IDs.inc.php', "IPSLibrary::app::modules::Entertainment");
	SetVariableConstant ("c_ID_ConnectAsynchronScript",  $ScriptIdConnASyn,   'Entertainment_IDs.inc.php', "IPSLibrary::app::modules::Entertainment");
   if ($WFC10_Enabled)  SetVariableConstant ("c_ID_WebFrontRoomes",  $WebFrontId,      'Entertainment_IDs.inc.php', "IPSLibrary::app::modules::Entertainment");
   if ($Mobile_Enabled) SetVariableConstant ("c_ID_MobileRoomes",    $ID_RoomiPhone,   'Entertainment_IDs.inc.php', "IPSLibrary::app::modules::Entertainment");

	if ($WFC10_Enabled) {
		ReloadAllWebFronts();
	}
	
	// Post Installation	return;
	// -----------------
	IPS_RunScript($ScriptIdPostInstall);
	echo "--- Installation successfully finised !!! ----------------------------------------------\n";



   // ------------------------------------------------------------------------------------------------
	function get_DevicePropertybyParent($ParentId, $ControlType, $Property) {
	   $Data = false;
	   $DeviceConfig = get_DeviceConfiguration();
	   $Name = IPS_GetName($ParentId);
	   if (IPS_GetName(IPS_GetParent($ParentId)) == 'Devices') {
	      $Data = $DeviceConfig[$Name][$ControlType][$Property];
	   } else {
	      $SourceConfig = get_SourceConfiguration();
	      foreach ($SourceConfig as $RoomName=>$RoomData) {
	   		foreach ($RoomData as $SourceIdx => $SourceIdxData) {
	   	   	if (is_array($SourceIdxData)) {
			   		foreach ($SourceIdxData as $SourceType => $SourceTypeData) {
			   		   if ($SourceType==c_Property_Input or $SourceType==c_Property_Switch or $SourceType==c_Property_Output) {
			   		      $DeviceName = $SourceTypeData[c_Property_Device];
			   		      $DeviceControls = $DeviceConfig[$DeviceName];
			   		      if (array_key_exists($ControlType, $DeviceControls)) {
			   		         $Data = $DeviceConfig[$DeviceName][$ControlType][$Property];
								}
			   		   }
			   		}
			   	}
				}
	      }
	   }
	   if ($Data===false) {
	      if ($ControlType==c_Control_iRemoteVolume) {
            return get_DevicePropertybyParent($ParentId, c_Control_RemoteVolume, $Property);
			}
	      if ($ControlType==c_Control_iRemoteSource) {
            return get_DevicePropertybyParent($ParentId, c_Control_RemoteSource, $Property);
			}
	      echo $Name.'.'.$ControlType.'.'.$Property." could NOT be found !!!/n";
	      exit;
	   }
		return $Data;
	}

   // ------------------------------------------------------------------------------------------------
	function CreateControl ($ControlType, $ControlData, $ParentId, $ActionScriptId, $Order) {
		$Name  = $ControlData[c_Property_Name];
	   switch ($ControlType) {
	      case c_Control_RoomPower:
				$ControlId  = CreateVariable($Name,  0 /*Boolean*/, $ParentId, $Order, '~Switch', $ActionScriptId, false, 'Power');
				break;
	      case c_Control_DevicePower:
				$ControlId  = CreateVariable($Name,  0 /*Boolean*/, $ParentId, $Order, '~Switch', $ActionScriptId, false, 'Power');
				break;
	      case c_Control_Muting:
				$ControlId  = CreateVariable($Name,  0 /*Boolean*/, $ParentId, $Order, '~Switch', $ActionScriptId, false, 'Speaker');
				break;
	      case c_Control_Source:
	         $Profile = 'Entertainment_Source'.$ParentId;
	         CreateProfile_Source($Profile, $ParentId);
				$ControlId  = CreateVariable($Name,  1 /*Integer*/, $ParentId, $Order, $Profile, $ActionScriptId, 0, 'Information');
				break;
	      case c_Control_Volume:
	         $Profile = 'Entertainment_Volume'.$ParentId;
	         $MinValue = get_DevicePropertybyParent($ParentId, $ControlType, c_Property_MinValue);
	         $MaxValue = get_DevicePropertybyParent($ParentId, $ControlType, c_Property_MaxValue);
            CreateProfile_Volume($Profile, $MinValue, $MaxValue);
				$ControlId  = CreateVariable($Name,  1 /*Integer*/, $ParentId, $Order, $Profile, $ActionScriptId,  $MinValue, 'Intensity');
				break;
	      case c_Control_Group:
				$ControlId  = CreateVariable($Name,  0 /*Boolean*/, $ParentId, $Order, '~Switch', $ActionScriptId, false, $ControlData[c_Property_Icon]);
				break;
	      case c_Control_Balance:
	      case c_Control_Treble:
	      case c_Control_Middle:
	      case c_Control_Bass:
	         $Profile = 'Entertainment_'.$ControlType.$ParentId;
	         $MinValue = get_DevicePropertybyParent($ParentId, $ControlType, c_Property_MinValue);
	         $MaxValue = get_DevicePropertybyParent($ParentId, $ControlType, c_Property_MaxValue);
            CreateProfile_Tone($Profile, $MinValue, $MaxValue);
				$ControlId  = CreateVariable($Name,  1 /*Integer*/, $ParentId, $Order, $Profile, $ActionScriptId, $MinValue, '');
				break;
	      case c_Control_Mode:
	         $Profile = 'Entertainment_Mode'.$ParentId;
	         $Names   = get_DevicePropertybyParent($ParentId, $ControlType, c_Property_Names);
            CreateProfile_Names($Profile, $Names);
				$ControlId  = CreateVariable($Name,  1 /*Integer*/, $ParentId, $Order, $Profile, $ActionScriptId, 0, 'Gear');
				break;
	      case c_Control_Program:
	         $Profile = 'Entertainment_Program'.$ParentId;
	         $Names   = get_DevicePropertybyParent($ParentId, $ControlType, c_Property_Names);
            CreateProfile_Names($Profile, $Names);
				$ControlId  = CreateVariable($Name,  1 /*Integer*/, $ParentId, $Order, $Profile, $ActionScriptId, 0, 'Image');
				break;
	      case c_Control_RemoteVolumeType:
	      case c_Control_RemoteSourceType:
				$ControlId  = CreateVariable($Name,  1 /*Integer*/, $ParentId, $Order, '', $ActionScriptId, 0);
				break;
	      case c_Control_iRemoteVolume:
	      case c_Control_RemoteVolume:
				$ControlId  = CreateVariable($Name, 3 /*String*/,  $ParentId, $Order,   '~HTMLBox', null, null, 'Intensity');
	         $Names   = get_DevicePropertybyParent($ParentId, $ControlType, c_Property_Names);
				SetValue($ControlId, c_RemoteControlHtmlPrefix.$Names[0].c_RemoteControlHtmlSuffix);
				break;
	      case c_Control_iRemoteSource:
	      case c_Control_RemoteSource:
				$ControlId  = CreateVariable($Name, 3 /*String*/,  $ParentId, $Order,   '~HTMLBox', null, null, 'Notebook');
	         $Names   = get_DevicePropertybyParent($ParentId, $ControlType, c_Property_Names);
				SetValue($ControlId, c_RemoteControlHtmlPrefix.$Names[0].c_RemoteControlHtmlSuffix);
				break;
			default;
			   $ControlId = false;
				break;
	   }
	   return $ControlId;
	}

   // ------------------------------------------------------------------------------------------------
	function CreateProfile_Volume ($Name, $MinValue, $MaxValue) {
	   @IPS_DeleteVariableProfile($Name);
		IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, "", "%");
		IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, 1);
		IPS_SetVariableProfileDigits($Name, 0);
		IPS_SetVariableProfileIcon($Name, "");
	}

   // ------------------------------------------------------------------------------------------------
	function CreateProfile_Tone ($Name, $MinValue, $MaxValue) {
	   @IPS_DeleteVariableProfile($Name);
		IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, "", "%");
		IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, 1);
		IPS_SetVariableProfileDigits($Name, 0);
		IPS_SetVariableProfileIcon($Name, "");
	}

   // ------------------------------------------------------------------------------------------------
	function CreateProfile_Names ($Name, $Names) {
	   @IPS_DeleteVariableProfile($Name);
		IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, "", "");
		IPS_SetVariableProfileValues($Name, 0, 0, 0);
		IPS_SetVariableProfileDigits($Name, 0);
		IPS_SetVariableProfileIcon($Name, "");
		foreach($Names as $Idx => $IdxName) {
			IPS_SetVariableProfileAssociation($Name, $Idx, $IdxName, "", 0xaaaaaa);
		}
	}

   // ------------------------------------------------------------------------------------------------
	function CreateProfile_Source ($Name, $RoomId) {
	   @IPS_DeleteVariableProfile($Name);
		IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, "", "");
		IPS_SetVariableProfileValues($Name, 0, 0, 0);
		IPS_SetVariableProfileDigits($Name, 0);
		IPS_SetVariableProfileIcon($Name, "");
	   $SourceData = get_SourceConfiguration();
		$SourceItems = $SourceData[IPS_GetName($RoomId)];
		foreach($SourceItems as $SourceId => $SourceData) {
		   if ($SourceData[c_Property_Name]=="") continue;
			IPS_SetVariableProfileAssociation($Name, $SourceId, $SourceData[c_Property_Name], "", 0xaaaaaa);
		}
	}

	/** @}*/

?>