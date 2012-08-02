<?
    IPSUtils_Include ('IPSLogger.inc.php',              'IPSLibrary::app::core::IPSLogger');
    IPSUtils_Include ("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
    
    $varId = $IPS_VARIABLE;
    $value = $IPS_VALUE;
    
    if($value !== True) {
        return;
    }
    
    $dataPath = "Program.IPSLibrary.data.modules.Security.".$IPS_VARIABLE;
    $idDataPath = get_ObjectIDByPath($dataPath);
    
    $lastMotionId = IPS_GetVariableIDByName(v_LAST_MOTION, $idDataPath);
    $value = substr(GetValueString($lastMotionId), 0, 5000);
    $value = date("Y-m-d H:i:s")."<br>".$value;
    SetValueString($lastMotionId, $value);
    
    // TODO: read "alarm active" setting and sent mail
?>