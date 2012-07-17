<?

    $i18n["DslMaxDataRate"] = array("de" => "Datenrate Max.");
    $i18n["DslCableCapacity"] = array("de" => "Leitungskapazität");
    $i18n["DslAtmRate"] = array("de" => "Aktuelle Datenrate");
    $i18n["DslSignalNoiseDistance"] = array("de" => "Störabstandsmarge");
    $i18n["DslLineLoss"] = array("de" => "Leitungsdämpfung");
    
    function FritzBox_getI18N($key, $lang = "de") {
        global $i18n;
        
        return utf8_decode($i18n[$key][$lang]);
    }
?>