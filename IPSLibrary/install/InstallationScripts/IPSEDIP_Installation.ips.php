<?
	/**@defgroup ipsedip_install EDIP Installation
	 * @ingroup ipsedip
	 * @{
	 *
	 * EDIP Installations File
	 *
	 * @file          IPSEDIP_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.2, 16.04.2012<br/>
	 *
	 * Script zur kompletten Installation der IPS EDIP Steuerung.
	 *
	 * @page rquirements_edip Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.1
	 * - IPSLogger >= 2.50.1
	 *
	 * @page install_edip Installations Schritte
	 * Folgende Schritte sind zur Installation der EDIP Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 * - ID des EDIP Empfangs Scriptes IPSEDIP_Receive.ips.php als Action Script der Register Variable(n) definieren.
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'."\n";
		$moduleManager = new IPSModuleManager('IPSEDIP');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSEDIP_Constants.inc.php",       "IPSLibrary::app::hardware::IPSEDIP");
	IPSUtils_Include ("IPSEDIP_Configuration.inc.php",   "IPSLibrary::config::hardware::IPSEDIP");

	$AppPath        = "Program.IPSLibrary.app.hardware.IPSEDIP";
	$DataPath       = "Program.IPSLibrary.data.hardware.IPSEDIP";
	$ConfigPath     = "Program.IPSLibrary.config.hardware.IPSEDIP";
	$HardwarePath   = "Hardware.EDIP";

	$IgnoreIOPortInstanceError    = $moduleManager->GetConfigValueBoolDef('IgnoreIOPortInstanceError', '', true);

	/* ---------------------------------------------------------------------- */
	/* EDIP Program Installation                                              */
	/* ---------------------------------------------------------------------- */

	$CategoryIdData = CreateCategoryPath($DataPath);
	$CategoryIdApp  = CreateCategoryPath($AppPath);
	$CategoryIdHW   = CreateCategoryPath($HardwarePath);

	$id_ScriptTimer   			= IPS_GetScriptIDByName('IPSEDIP_Timer',   			$CategoryIdApp);
	$id_ScriptEvent  		 		= IPS_GetScriptIDByName('IPSEDIP_Event',   			$CategoryIdApp);
	$id_ScriptReceive 			= IPS_GetScriptIDByName('IPSEDIP_Receive', 			$CategoryIdApp);
   $id_ScriptChangeSettings  	= IPS_GetScriptIDByName('IPSEDIP_ChangeSettings',  $CategoryIdApp);


	foreach (IPSEDIP_GetConfiguration() as $configItem=>$configData) {
	   if ($configData[EDIP_CONFIG_ROOT]<>"") {
			$id_Instance   = CreateDummyInstance($configItem, $CategoryIdData, 10);
			$id_Register   = CreateVariable(EDIP_VAR_REGISTER,     1 /*Integer*/, $id_Instance,  10, '', null, 0);
			$id_Root       = CreateVariable(EDIP_VAR_ROOT,         1 /*Integer*/, $id_Instance,  20, '', null, 0);
			$id_Current    = CreateVariable(EDIP_VAR_CURRENT,      1 /*Integer*/, $id_Instance,  40, '', null, 0);
			$id_Categories = CreateVariable(EDIP_VAR_OBJECTIDS,    3 /*String*/,  $id_Instance,  50, '', null, '');
			$id_Variables  = CreateVariable(EDIP_VAR_OBJECTCMDS,   3 /*String*/,  $id_Instance,  60, '', null, '');
			$id_Variables  = CreateVariable(EDIP_VAR_OBJECTVALUES, 3 /*String*/,  $id_Instance,  60, '', null, '');
			$id_value      = CreateVariable(EDIP_VAR_OBJECTEDIT,   1 /*Integer*/, $id_Instance,  70, '', null, 0);
			$id_backlight  = CreateVariable(EDIP_VAR_BACKLIGHT,    1 /*Integer*/, $id_Instance,  80, '~Intensity.100', $id_ScriptChangeSettings, 50);
			$id_Notify     = CreateVariable(EDIP_VAR_NOTIFY,       3 /*String*/,  $id_Instance,  90, '', $id_ScriptChangeSettings, '');

			// Create Serial Port
         $id_IOComPort = null;
			if ($configData[EDIP_CONFIG_COMPORT] <> '') {
				$id_IOComPort = CreateSerialPort('EDIP_'.$configData[EDIP_CONFIG_NAME], $configData[EDIP_CONFIG_COMPORT], 115200, 1, 8, 'None',0, $IgnoreIOPortInstanceError);
			}
			// Create Register Variable
			$registerIdConfig = $configData[EDIP_CONFIG_REGISTER];
			if ($registerIdConfig==null) {
			   if ($id_IOComPort==null) {
			      throw new IPSConfigHandlerException('Register Variable and ComPort not defined !!!');
			   }
				$registerIdConfig = CreateRegisterVariable($configData[EDIP_CONFIG_NAME].'_Register', $CategoryIdHW, $id_ScriptReceive, $id_IOComPort);
			}
			// Create Root Category
			if (!is_numeric($configData[EDIP_CONFIG_ROOT])) {
			   CreateCategoryPath($configData[EDIP_CONFIG_ROOT]);
			}

			SetValue($id_Register, IPSUtil_ObjectIDByPath($registerIdConfig));
			SetValue($id_Root,     IPSUtil_ObjectIDByPath($configData[EDIP_CONFIG_ROOT]));
			SetValue($id_Current,  IPSUtil_ObjectIDByPath($configData[EDIP_CONFIG_ROOT]));
			SetValue($id_value,    0);
			CreateTimer_OnceADay ($configData[EDIP_CONFIG_NAME].'_Backlight_Timer', $id_ScriptTimer, date('H'), date('i',time() +($configData[EDIP_CONFIG_BACKLIGHT_TIMER]*60)));
			CreateTimer_OnceADay ($configData[EDIP_CONFIG_NAME].'_Root_Timer', 		$id_ScriptTimer, date('H'), date('i',time() +($configData[EDIP_CONFIG_ROOT_TIMER]*60)));

//			CreateCategory

		} else {
		   echo "Register/Root Ids NOT assigned... \n";
		}
	}

	CreateTimer_CyclicBySeconds ('Timer', $id_ScriptTimer, EDIP_CONFIG_REFRESHTIMER);

	SetVariableConstant ("EDIP_ID_PROGRAM",    $CategoryIdData,   'IPSEDIP_IDs.inc.php', 'IPSLibrary::app::hardware::IPSEDIP');
	SetVariableConstant ("EDIP_ID_EVENTSCRIPT",$id_ScriptEvent,   'IPSEDIP_IDs.inc.php', 'IPSLibrary::app::hardware::IPSEDIP');
	SetVariableConstant ("EDIP_ID_TIMERSCRIPT",$id_ScriptTimer,   'IPSEDIP_IDs.inc.php', 'IPSLibrary::app::hardware::IPSEDIP');

	/** @}*/
?>