<?
    function getLang($langId) {
		$langId = strtoupper($langId);
	
		$langString["ALARM_DETECTED_MAIL_SUFFIX"] = "\n\nTrace: \n%s\n\nEreignisse: \n%s";
	
		$langString["ALARM_MOTION_DETECTED_HEADER"] = "ALARM - Bewegung erkannt";
		$langString["ALARM_MOTION_DETECTED_BODY"] = "%s: Bewegung durch '%s' am Ort '%s' erkannt.";
		$langString["ALARM_MOTION_DETECTED_BODY_HISTORY"] = $langString["ALARM_MOTION_DETECTED_BODY"].$langString["ALARM_DETECTED_MAIL_SUFFIX"];
		
		$langString["ALARM_SMOKE_DETECTED_HEADER"] = "ALARM - Rauch erkannt";
		$langString["ALARM_SMOKE_DETECTED_BODY"] = "%s: Rauch durch '%s' am Ort '%s' erkannt.";
		$langString["ALARM_SMOKE_DETECTED_BODY_HISTORY"] = $langString["ALARM_SMOKE_DETECTED_BODY"].$langString["ALARM_DETECTED_MAIL_SUFFIX"];
		
		$langString["ALARM_CLOSURE_DETECTED_HEADER"] = "ALARM - Öffnung erkannt";
		$langString["ALARM_CLOSURE_DETECTED_BODY"] = "%s: Öffnung von '%s' am Ort '%s' erkannt.";
		$langString["ALARM_CLOSURE_DETECTED_BODY_HISTORY"] = $langString["ALARM_CLOSURE_DETECTED_BODY"].$langString["ALARM_DETECTED_MAIL_SUFFIX"];
		
		if(isset($langString[$langId])) {
			return $langString[$langId];
		}
		
		IPSLogger_Wrn(__file__, "No language string for language id '".$langId."' defined.");
		return $langId;
	}
?>