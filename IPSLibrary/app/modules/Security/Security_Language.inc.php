<?
    function getLang($langId) {
		$langId = strtoupper($langId);
	
		$langString["ALARM_MOTION_DETECTED_HEADER"] = "ALARM - Bewegung erkannt";
		$langString["ALARM_MOTION_DETECTED_BODY"] = "%s: Bewegung durch '%s' am Ort '%s' erkannt.";
		$langString["ALARM_MOTION_DETECTED_BODY_HISTORY"] = $langString["ALARM_MOTION_DETECTED_BODY"]."\n\nVerlauf: \n%s";
		
		$langString["ALARM_SMOKE_DETECTED_HEADER"] = "ALARM - Rauch erkannt";
		$langString["ALARM_SMOKE_DETECTED_BODY"] = "%s: Rauch durch '%s' am Ort '%s' erkannt.";
		$langString["ALARM_SMOKE_DETECTED_BODY_HISTORY"] = $langString["ALARM_SMOKE_DETECTED_BODY"]."\n\nVerlauf: \n%s";
		
		if(isset($langString[$langId])) {
			return $langString[$langId];
		}
		
		IPSLogger_Wrn(__file__, "No language string for language id '".$langId."' defined.");
		return $langId;
	}
?>