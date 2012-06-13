<?
    define("DEVICE_IP", "ip");
    define("DEVICE_PASSWORD", "pass");

    // ========================================================================================================================
    // Defintion of FritzBoxes
    // ========================================================================================================================
    function get_FritzBoxDevices () {
        $devices = array();
        
        $devices[] = array(
            DEVICE_IP       => "192.168.178.1",
            DEVICE_PASSWORD => "ama1Vane",
        );
        
        return $devices;
    }

?>