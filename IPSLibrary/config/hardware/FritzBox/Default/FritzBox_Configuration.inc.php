<?
    // constants
    define("FRITZBOX_DATA_BASE_PATH", "Program.IPSLibrary.data.hardware.FritzBox");
    
    define("c_FritzBox_1", "FB1921681781");
    
    define("DEVICE_IP", "ip");
    define("DEVICE_PASSWORD", "pass");

    // ========================================================================================================================
    // Definition of FritzBoxes
    // ========================================================================================================================
    function get_FritzBoxDevices () {
        return array(
            c_FritzBox_1 => array(
                DEVICE_IP       => "192.168.178.1",
                DEVICE_PASSWORD => "ama1Vane",
            )
        );
    }
    
    // ========================================================================================================================
    // Definition of Settings
    // ========================================================================================================================
    function get_FritzBoxSettings() {
        $settings = array(
            "LOG"                   => array(
                "MINUTES_BETWEEN_LOGS"          => 30,
                "dB"        => array(
                    // log when the value has changed at least by the given amount
                    //"MIN_CHANGE"                => 3,
                    // log when the value has changed at least by the given amount
                    "MIN_CHANGE_PERCENT"         => 35,
                    // force log when the value has changed at least by the given amount
                    "MIN_CHANGE_PERCENT_FORCE"   => 70,
                    // force log every x minutes
                    "MINUTES_BETWEEN_LOGS"       => 5,
                    // log when the value has changed at least by the given amount
                    "MINUTES_BETWEEN_LOGS_MIN_CHANGE"         => 20,
                ),
            ),
        );
        
        return $settings;
    }
    
?>