<?
    IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
    
    function getMotionDevices() {
        $ret = array();
        
        // HM-IPS Instance ID, Location
        $ret[] = array(37263, "Hallway");
        
        return $ret;
    }
    
?>