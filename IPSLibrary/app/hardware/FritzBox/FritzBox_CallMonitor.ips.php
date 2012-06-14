<?
    /**@addtogroup hardware
     * @{
     *
     * @file          FritzBox_CallMonitor.ips.php
     * @author        Dominik Zeiger
     * @version
     * Version 2.50.1, 30.05.2012<br/>
     *
     * Handles responses from FritzBox call monitor register variable
     */
    
    IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ('FritzBox_Configuration.inc.php',      'IPSLibrary::config::hardware::FritzBox');
    IPSUtils_Include ('FritzBox_Status.inc.php',      'IPSLibrary::app::hardware::FritzBox');
    
    if(isset($IPS_SENDER) && $IPS_SENDER === "RegisterVariable") {
        // get the triggering device
        $regVarInstance = $IPS_INSTANCE;
        $deviceCategoryId = IPS_GetParent($regVarInstance);
        $deviceName = IPS_GetName($deviceCategoryId);
        $devices = get_FritzBoxDevices();
        if(!isset($devices[$deviceName])) {
            throw new Exception("Device '".$deviceName."' is not part of the configuration.");
        }
        $deviceConfig = $devices[$deviceName];
        $fritzBox = new FritzBox($deviceName, $deviceConfig[DEVICE_IP], $deviceConfig[DEVICE_PASSWORD]);
        
        // TODO: is this realiable or should we rather only set the device state based on the $action?
        FritzBox_ReadDectMonitorData($fritzBox);
        
        $parts = explode(";", $IPS_VALUE);
        $dateTime = $parts[0];
        $action = $parts[1];
        $connectionId = $parts[2];
        
        /*
        Ausgehende Anrufe: datum;CALL;ConnectionID;Nebenstelle;GenutzteNummer;AngerufeneNummer;
        Eingehende Anrufe: datum;RING;ConnectionID;Anrufer-Nr;Angerufene-Nummer;
        Zustandegekommene Verbindung: datum;CONNECT;ConnectionID;Nebenstelle;Nummer;
        Ende der Verbindung: datum;DISCONNECT;ConnectionID;dauerInSekunden;
        */
        switch ($action) {
            case "RING":
                $callerNumber = $parts[3];
                $calledNumber = $parts[4];
                IPSLogger_Dbg(__file__, $connectionId." - ".$dateTime.": Ring from '$callerNumber' to '$calledNumber'");
                break;
            case "CALL":
                $sourcePhone = $parts[3];
                $outboundNumber = $parts[4];
                $calledNumber = $parts[5];
                IPSLogger_Dbg(__file__, $connectionId." - ".$dateTime.": Call to '$calledNumber' from '$sourcePhone'@'$outboundNumber'");
                break;
            case "DISCONNECT":
                $duration = $parts[3];
                IPSLogger_Dbg(__file__, $connectionId." - ".$dateTime.": Disconnect after $duration");
                break;
            case "CONNECT":
                $sourcePhone = $parts[3];
                $number = $parts[4];
                IPSLogger_Dbg(__file__, $connectionId." - ".$dateTime.": Connection from $sourcePhone@$number");
                break;
            default:
                IPSLogger_Wrn(__file__, "Unknown call action ".$action);
        }
    }
?>