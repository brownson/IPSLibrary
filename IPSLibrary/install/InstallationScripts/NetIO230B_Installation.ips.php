<?
    /**@defgroup netio230b_installation NetIO230B Installation
     * @ingroup netio230b
     * @{
     *
     * Installations File für den NetIO230B
     *
     * @section requirements_netio230b Installations Voraussetzungen NetIO230B
     * - IPS Kernel >= 2.50
     * - IPSModuleManager >= 2.50.1
     *
     * @section visu_netio230b Visualisierungen für NetIO230B
     * - WebFront 10Zoll
     * - Mobile
     *
     * @page install_netio230b Installations Schritte
     * Folgende Schritte sind zur Installation der EDIP Ansteuerung nötig:
     * - Laden des Modules (siehe IPSModuleManager)
     * - Konfiguration (Details siehe Konfiguration, Installation ist auch ohne spezielle Konfiguration möglich)
     * - Installation (siehe IPSModuleManager)
     *
     * @file          NetIO230B_Installation.ips.php
     * @author        Dominik Zeiger
     * @version
     *  Version 2.50.1, 17.07.2012<br/>
     *
     */

    if (!isset($moduleManager)) {
        IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

        echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
        $moduleManager = new IPSModuleManager('NetIO230B');
    }

    $moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
    $moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');

    IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
    IPSUtils_Include ('NetIO230B.inc.php',      'IPSLibrary::app::hardware::NetIO230B');
    IPSUtils_Include ("NetIO230B_Configuration.inc.php", "IPSLibrary::config::hardware::NetIO230B");
    
    // ----------------------------------------------------------------------------------------------------------------------------
    // Program Installation
    // ----------------------------------------------------------------------------------------------------------------------------
    $CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');
    $CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
    $CategoryIdConfig   = $moduleManager->GetModuleCategoryID('config');
    
    // Get Scripts Ids
    $ID_ScriptNetIO230BInterface  = IPS_GetScriptIDByName('NetIO230B_Interface', $CategoryIdApp);
    
    $configurations = get_NetIO230BDevices();
    foreach($configurations as $configuration) {
        $username = $configuration[v_USERNAME];
        $password = $configuration[v_PASSWORD];
        $ip = $configuration[v_IP];
        $ipClean = str_replace(".", "", $ip);
        $deviceName = "NetIO230B".$ipClean;
        
        // client socket instanz erstellen
        $socketId = IPS_CreateInstance("{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}");
        IPS_SetName($socketId, $deviceName);
        CSCK_SetHost($socketId, $ip);
        CSCK_SetPort($socketId, 1234);
        IPS_ApplyChanges($socketId);
        
        $regVarId = CreateRegisterVariable($deviceName, $CategoryIdData, $ID_ScriptNetIO230BInterface);
        IPS_ApplyChanges($regVarId);
        if($socketId) {
            IPS_ConnectInstance($regVarId, $socketId);
        }
        
        // create settings
        $settingsID = CreateDummyInstance(v_NETIO_SETTINGS, $regVarId, 1);
        $Order = 10;
        CreateVariable(v_USERNAME, 3 /*String*/, $settingsID, $Order++, "");
        CreateVariable(v_PASSWORD, 3 /*String*/, $settingsID, $Order++, "");
        CreateVariable(v_IP, 3 /*String*/, $settingsID, $Order++, "");
        
        // Setup regular status updates
        //CreateTimer_CyclicBySeconds ('NetIO230B_GetStatus', $ID_ScriptNetIO230BInterface, 45);
        
        // create "get state" event
        $TimerID = CreateTimer_CyclicBySeconds('Refresh State', $settingsID, 30);
        IPS_SetEventScript($TimerID, "IPS_RunScriptEx(".$ID_ScriptNetIO230BInterface.", array(\"action\" => \"getStatus\", \"source\" => ".$settingsID."));");
        IPS_SetPosition($TimerID, $Order++);
        
        $Order = 10;
        CreateVariable(v_NETIO_LAST_ACTION, 1 /*Integer*/, $regVarId, $Order, "");
        
        // create the port variables
        $Order = 20;
        for($i = 1; $i <= 4; $i++) {
            $Name = "Port".$i;
            $ControlId = CreateVariable($Name,  0 /*Boolean*/, $regVarId, $Order, '~Switch', $ID_ScriptNetIO230BInterface, null, 'Power');
            $Order += 10;
        }
    }
    
    Register_PhpErrorHandler($moduleManager);

    // ------------------------------------------------------------------------------------------------
    function Register_PhpErrorHandler($moduleManager) {
        $file = IPS_GetKernelDir().'scripts\\__autoload.php';

        if (!file_exists($file)) {
            throw new Exception($file.' could NOT be found!', E_USER_ERROR);
        }
        $FileContent = file_get_contents($file);

        $pos = strpos($FileContent, 'IPSLogger_PhpErrorHandler.inc.php');

        if ($pos === false) {
            $includeCommand = '    IPSUtils_Include("IPSLogger_PhpErrorHandler.inc.php", "IPSLibrary::app::core::IPSLogger");';
            $FileContent = str_replace('?>', $includeCommand.PHP_EOL.'?>', $FileContent);
            $moduleManager->LogHandler()->Log('Register Php ErrorHandler of IPSLogger in File __autoload.php');
            file_put_contents($file, $FileContent);
        }
    }
    
    /** @}*/
?>