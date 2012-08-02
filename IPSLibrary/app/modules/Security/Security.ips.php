<?
    IPSUtils_Include ('IPSLogger.inc.php',              'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ("IPSInstaller.inc.php",           "IPSLibrary::install::IPSInstaller");
    IPSUtils_Include ("Security_Configuration.inc.php", "IPSLibrary::app::modules::Security");
    
    switch ($_IPS['SENDER']) {
        case "RunScript"        : break;
        case "Execute"          : break;
        case "TimerEvent"       : break;
        case "Variable"         : break;
        case "WebFront"         : break;
        case "RegisterVariable" : break;
        default                 : break;
    }
    
?>