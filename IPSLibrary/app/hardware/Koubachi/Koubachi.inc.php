<?

	/**@defgroup koubachi_interface Koubachi Interface
	* @ingroup koubachi
	* @{
	*
	* Interface File fuer Koubachi.
	*
	* @file Koubachi_Interface.inc.php
	* @author Dominik Zeiger
	* @version Version 0.1, 15.10.2012<br/>
	*
	*/

	/*
	TODO:
	
	mist action: 
			URL: http://my.koubachi.com/api/plants/116346/tasks?&app_key=KW0MY0XRFHC835BA1IMNR6NV
			PUT: care_action[action_type]	mist.performed
			RESPONSE: {"care_action":{"action_type":"mist.performed","time":"2012-10-17T12:19:16+00:00","plant_id":116346}}
	*/
	
	namespace domizei\koubachi;
	
	IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
	IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("Koubachi_Configuration.inc.php",            "IPSLibrary::config::hardware::Koubachi");
	
	function appendAuthToken($config, $url) {
		$url .= strpos($url, "?") === FALSE ? "?" : "&";
		
		return $url."user_credentials=".$config[USER_CREDENTIALS]."&app_key=".$config[APP_KEY];
	}
	
	function getApiData($config, $url, $xmlTag) {
		//IPSLogger_Dbg(__file__, "Getting api data from url '".$url."' and tag '".$xmlTag."'");
		$url = appendAuthToken($config, $url);
		
		$doc = new \DOMDocument();
		@$doc->load($url);
		
		$list = $doc->getElementsByTagName($xmlTag);
		return $list;
	}
	
	/**
	 * Map the given xml type to an IPS type (Default: String).
	 */
	function getIPSTypeFromXMLType($xmlType) {
		$ipsType = 3;
		
		switch($xmlType) {
			case "float":
				$ipsType = 2;
				break;
			case "integer":
			case "datetime":
				$ipsType = 1;
				break;
			case "boolean": 
				$ipsType = 0;
				break;
			default:
				$ipsType = 3;
		}

		return array(
			"IPS" => $ipsType,
			"XML" => $xmlType,
		);
	}
	
	/**
	 * Evaluate the given attribute list's "type" and "isNil" parameter to determine the target type.
	 */
	function getIPSValueType($nodeAttributes) {
		$type = false;
		$isNil = false;
		
		//IPSLogger_Dbg(__file__, "XML Node ".print_r($nodeAttributes, true));
		foreach($nodeAttributes as $attribute) {
			$name = $attribute->name;
			if($name == "type") {
				$xmlType = $attribute->value;
				$type = getIPSTypeFromXMLType($attribute->value);
			} else if($name == "nil") {
				$isNil = true;
			}
		}
		
		// get default type
		if($type === false) {
			$type = getIPSTypeFromXMLType("");
		}
		$type["isNull"] = $isNil;
		
		return $type;
	}
	
	function Koubachi_setValue($varId, $node) {
		if($varId === false || $varId == 0) {
			IPSLogger_Err(__file__, "Unable to set a koubachi value with var id 0 or false");
			return;
		}
	
		$valueType = getIPSValueType($node->attributes);
		if($valueType["isNull"] === true) {
			// get value type from existing variable and set default
			//IPSLogger_Dbg(__file__, "VariableType: ".print_r(IPS_GetVariable($varId)["VariableValue"]["ValueType"], true));
			$targetVariableType = IPS_GetVariable($varId)["VariableValue"]["ValueType"];
			$valueType["IPS"] = $targetVariableType;
			switch($targetVariableType) {
				case 0: // boolean
					$value = false;
					break;
				case 1: // integer
					$value = 0;
					break;
				case 2: // float
					$value = 0.0;
					break;
				case 3: // string
					$value = "";
					break;
			}
		} else {
			// get mapping from node value
			$value = $node->nodeValue;
			// make any necessary conversions from the koubachi to IPS value
			$value = Koubachi_convertValue($value, $valueType);
		}
		
		Koubachi_setIPSValue($varId, $value, $valueType["IPS"]);
		
		return $value;
	}
	
	function Koubachi_convertValue($value, $valueType) {
		$xmlType = $valueType["XML"];
		switch($xmlType) {	
			case "datetime":
				// convert time from XML to timestamp format
				// IPSLogger_Dbg(__file__, "DateTime raw value: ".$value);
				$timestamp = strtotime($value);
				// IPSLogger_Dbg(__file__, "DateTime parsed value: ".$timestamp);
				return $timestamp;
			case "boolean":
				return $value == "true";
			case "float":
				return (float) $value;
			case "integer":
				return (int) $value;
		}
		
		return $value;
	}
	
	function Koubachi_setIPSValue($varId, $value, $ipsType) {
		//IPSLogger_Dbg(__file__, "Setting value of $varId (IPS-Type: $ipsType): ".print_r($value, true));
		
		switch($ipsType) {	
			case 0:
				SetValueBoolean($varId, $value);
				break;
			case 1:
				SetValueInteger($varId, $value);
				break;
			case 2:
				SetValueFloat($varId, $value);
				break;
			case 3:
				SetValueString($varId, $value);
				break;
			default:
				IPSLogger_Dbg(__file__, "IPS Type not set. Unable to set the proper value.");
		}
	}
	
	function getNode($node, $childNodeName) {
		return $node->getElementsByTagName($childNodeName)->item(0);
	}
	
	function Koubachi_getVariableProfile($varName) {
		switch ($varName) {
			case "vdm-water-pending":
				return "~Alert";
				break;
		
		}
		return "";
	}
	
	function setVariableLogging($varId, $enabled = true) {
        $archiveHandlerId = IPS_GetInstanceIDByName("Archive Handler", 0);
        AC_SetLoggingStatus($archiveHandlerId, $varId, $enabled);
    }
	
	function Koubachi_CreateVariable($parentId, $node, $varName, $typeName = "") {
		$valueType = getIPSValueType($node->attributes);
		if($typeName !== "") {
			$staticValueType = getIPSTypeFromXMLType($typeName);
		}
		
		//if(isset($valueType)) IPSLogger_Dbg(__file__, "A: ".print_r($valueType, true));
		//if(isset($staticValueType)) IPSLogger_Dbg(__file__, "B: ".print_r($staticValueType, true));
		
		// check if dynamic and static value types are matching
		if(isset($valueType) && isset($staticValueType) && $staticValueType["IPS"] != $valueType["IPS"]) {
			IPSLogger_Dbg(__file__, "Value types not matching for variable '".$varName."'. (Manual type: ".$staticValueType["IPS"].", Auto type: ".$valueType["IPS"]);
		}
		
		if($valueType === false) {
			IPSLogger_Dbg(__file__, "Unable to determine value type of ".$varName);
			return false;
		}
		
		if(!isset($valueType) && isset($staticValueType)) {
			$valueType = $staticValueType;
		}
		
		$varProfile = Koubachi_getVariableProfile($varName);
		$varId = \CreateVariable($varName, $valueType["IPS"], $parentId, 0, $varProfile, 0, 0, '');
		IPS_SetVariableCustomProfile($varId, $varProfile);
		return $varId;
	}
	
	function Koubachi_CreateSetVariable($objectCategoryId, $objectNode, $xmlNodeName, $typeName, $enableLogging = false) {
		$node = getNode($objectNode, $xmlNodeName);
		// check if the variable was already created. if not, create it.
		$varId = @IPS_GetVariableIDByName($xmlNodeName, $objectCategoryId);
		if($varId === false) {
			$varId = Koubachi_CreateVariable($objectCategoryId, $node, $xmlNodeName, $typeName);
			setVariableLogging($varId, $enableLogging);
		}
		
		return Koubachi_SetValue($varId, $node);
	}
	
	function Koubachi_Update($updateWebfront = true) {
		IPSLogger_Dbg(__file__, "Running Koubachi Update");
		$config = getConfiguration();
		
		$devices = getDevices($config);
		$CategoryIdDataDevices = IPSUtil_ObjectIDByPath(PATH_DATA_DEVICES);
		foreach($devices as $device) {
			// add entry for device with most important data
			$macAddressNode = getNode($device, API_XML_DEVICE_MAC_ADDRESS);
			
			// create category for device
			$categoryId = CreateCategory($macAddressNode->nodeValue, $CategoryIdDataDevices, 10);
			//IPSLogger_Dbg(__file__, "Device category: ".$categoryId);
			$varId = Koubachi_CreateVariable($categoryId, $macAddressNode, API_XML_DEVICE_MAC_ADDRESS);
			Koubachi_SetValue($varId, $macAddressNode);
			
			$vars = getDeviceVariableTypeMapping();
			foreach($vars as $xmlNodeName => $settings) {
				$enableLogging = isset($settings[1]) && $settings[1] == true;
				Koubachi_CreateSetVariable($categoryId, $device, $xmlNodeName, $settings[0], $enableLogging);
			}
		}
		
		// make api call to get plants
		$xmlPlants = getPlants($config);
		$plants = array();
		$CategoryIdDataPlants = IPSUtil_ObjectIDByPath(PATH_DATA_PLANTS);
		foreach($xmlPlants as $xmlPlant) {
			$plant = array();
			
			// add entry for device with most important data
			$idNode = getNode($xmlPlant, API_XML_PLANT_ID);
			
			// create category for plant
			$categoryId = CreateCategory($idNode->nodeValue, $CategoryIdDataPlants, 10);
			//IPSLogger_Dbg(__file__, "plant category: ".$categoryId);
			$varId = Koubachi_CreateVariable($categoryId, $idNode, API_XML_PLANT_ID);
			$value = Koubachi_SetValue($varId, $idNode);
			$plant[API_XML_PLANT_ID] = $value;
			
			$vars = getPlantVariableTypeMapping();
			foreach($vars as $varName => $settings) {
				$enableLogging = isset($settings[1]) && $settings[1] == true;
				$value = Koubachi_CreateSetVariable($categoryId, $xmlPlant, $varName, $settings[0], $enableLogging);
				$plant[$varName] = $value;
			}
			$plants[] = $plant;
		}
		//IPSLogger_Dbg(__file__, "Plant Data: ".print_r($plants, true));
		if($updateWebfront) {
			Koubachi_UpdateWebFront($plants);
		}
	}
	
	function i18n($id, $lang = 'DE') {
		$i18n = array(
			"water"	=> "Wasser",
			"lastWater" => "<b>Zuletzt</b>",
			"nextWater" => "<b>Demnächst</b>",
			"light"	=> "Helligkeit",
			"temperature" => "Temperatur",
			"mist" => "Besprühtermin",
			"lastMisted" => "zuletzt",
			"lastUpdate" => "Letzte Aktualisierung",
			"NA" => "Keine Daten",
		);
		
		//IPSLogger_Dbg(__file__, "I18N: ".print_r($i18n, true));
		
		if(isset($i18n[$id])) {
			return $i18n[$id];
		} else {
			return $id;
		}
	}
	
	function createEntry($plant) {
		$warningTpl = "<div class='icon ipsIconWarning warning kb-tooltip' kb-tooltip='{{title}}'></div>";
		
		$str = "<div class='plant'>";
		
		if($plant[API_XML_PLANT_IS_DEVICE_ASSOCIATED]) {
			$circleColor = "green";
			$caption = "Mit Sensor verbunden";
		} else {
			$circleColor = "red";
			$caption = "Nicht mit Sensor verbunden";
		}
		
		$str .= "<div class='row label'><div title='".$caption."' class='circle ".$circleColor."'></div><div class='name' title='".$plant["location"]."'>".$plant["name"]."</div></div>";
		
		$waterLevel = $plant[API_XML_PLANT_WATER_LEVEL];
		if(!is_numeric($waterLevel)) {
			$waterLevel = i18n('NA');
		} else {
			$waterLevel = (round($waterLevel * 100, 2))." %";
		}
		if($plant[API_XML_PLANT_NEXT_WATER] > 0) {
			$nextWaterPopup = i18n('nextWater').": ".date("d.m.y" ,$plant[API_XML_PLANT_NEXT_WATER]);
		} else {
			$nextWaterPopup = i18n('nextWater').": ".i18n('NA');
		}
		if($plant[API_XML_PLANT_WATER_PENDING]) {
			$showWarning = true;
			$warning = str_replace("{{title}}", "Die Pflanze sollte gegossen werden.<br><br><b>Hinweis</b>: <br>".$plant[API_XML_PLANT_WATER_INSTRUCTION], $warningTpl);
		} else {
			$showWarning = false;
		}
		if($plant[API_XML_PLANT_LAST_WATER] > 0) {
			$lastWateredDate = date("d.m.y H:i" ,$plant[API_XML_PLANT_LAST_WATER]);
			$lastWateredPopup = i18n('lastWater').": ".$lastWateredDate;
		} else {
			$lastWateredPopup = i18n('lastWater').": ".i18n('NA');
		}
		$popup = $lastWateredPopup."<br>".$nextWaterPopup;
		$str .= "<div class='row water'>";
		$str .= "<div class='icon ipsIconDrops' title='".i18n('water')."'></div><div class='data kb-tooltip' kb-tooltip='".$popup."'>".$waterLevel."</div>".($showWarning ? $warning : "");
		$str .= "</div>";
		
		$lightLevel = $plant[API_XML_PLANT_LIGHT_LEVEL];
		if(!is_numeric($lightLevel)) {
			$lightLevel = i18n('NA');
		} else {
			$lightLevel = (round($lightLevel * 34650, 2))." Lux";
		}
		if(strlen($plant[API_XML_PLANT_LIGHT_ADVICE]) > 0 || strlen($plant[API_XML_PLANT_LIGHT_HINT]) > 0) {
			$showWarning = true;
			
			$text = "";
			if(strlen($plant[API_XML_PLANT_LIGHT_ADVICE])) {
				$text .= "<b>Ratschlag</b>: <br>".$plant[API_XML_PLANT_LIGHT_ADVICE];
			}
			if(strlen($plant[API_XML_PLANT_LIGHT_HINT])) {
				if(strlen($text) > 0) {
					$text = "<br><br>";
				}
				$text .= "<b>Hinweis</b>: <br>".$plant[API_XML_PLANT_LIGHT_HINT];
			}
			$warning = str_replace("{{title}}", $text, $warningTpl);
		} else {
			$showWarning = false;
		}
		$str .= "<div class='row light'>";
		$str .= "<div class='icon ipsIconSun' title='".i18n('light')."'></div><div class='data'>".$lightLevel."</div>".($showWarning ? $warning : "");
		$str .= "</div>";
		if($showWarning) {
			$str .= "<div class='light message'>".$text;
			$str .= "</div>";
		}
		
		$tempLevel = $plant[API_XML_PLANT_TEMPERATURE_LEVEL];
		if(!is_numeric($tempLevel)) {
			$tempLevel = i18n('NA');
		} else {
			$tempLevel = (round($tempLevel * 50, 1))." °C";
		}
		if(strlen($plant[API_XML_PLANT_TEMPERATURE_ADVICE]) > 0 || strlen($plant[API_XML_PLANT_TEMPERATURE_HINT]) > 0) {
			$showWarning = true;
			
			$text = "";
			if(strlen($plant[API_XML_PLANT_TEMPERATURE_ADVICE])) {
				$text .= "<b>Ratschlag</b>: <br>".$plant[API_XML_PLANT_TEMPERATURE_ADVICE];
			}
			if(strlen($plant[API_XML_PLANT_TEMPERATURE_HINT])) {
				if(strlen($text) > 0) {
					$text = "<br><br>";
				}
				$text .= "<b>Hinweis</b>: <br>".$plant[API_XML_PLANT_TEMPERATURE_HINT];
			}
			$warning = str_replace("{{title}}", $text, $warningTpl);
		} else {
			$showWarning = false;
		}		
		$str .= "<div class='row temperature'>";
		$str .= "<div class='icon ipsIconTemperature' title='".i18n('temperature')."'></div><div class='data'>".$tempLevel."</div>".($showWarning ? $warning : "");
		$str .= "</div>";
		
		/*$mistLevel = $plant[API_XML_PLANT_MIST_LEVEL];
		if(!is_numeric($mistLevel)) {
			$mistLevel = i18n('NA');
		} else {
			$mistLevel = (round($mistLevel * 100, 2))." %";
		}*/
		if($plant[API_XML_PLANT_NEXT_MIST] > 0) {
			$nextMistDate = date("d.m.y" ,$plant[API_XML_PLANT_NEXT_MIST]);
		} else {
			$nextMistDate = i18n('NA');
		}
		if($plant[API_XML_PLANT_MIST_PENDING]) {
			$showWarning = true;
			$warning = str_replace("{{title}}", "Die Pflanze sollte besprüht werden.<br><br><b>Hinweis:</b> ".$plant[API_XML_PLANT_MIST_INSTRUCTION], $warningTpl);
		} else {
			$showWarning = false;
		}
		if($plant[API_XML_PLANT_LAST_MIST] > 0) {
			$lastMistedDate = date("d.m.y H:i" ,$plant[API_XML_PLANT_LAST_MIST]);
			$lastMistedPopup = i18n('lastMisted').": ".$lastMistedDate;
		} else {
			$lastMistedPopup = i18n('NA');
		}
		$str .= "<div class='row light'>";
		$str .= "<div class='icon ipsIconRainfall' title='".i18n('mist')."'></div><div class='data kb-tooltip' kb-tooltip='".$lastMistedPopup."'>".$nextMistDate."</div>".($showWarning ? $warning : "");
		$str .= "</div>";
		
		$showWarning = false;
		$lastUpdate = date("d.m.y H:i" ,$plant[API_XML_PLANT_LAST_UPDATE]);;
		$str .= "<div class='row update'>";
		$str .= "<div class='icon ipsIconClock' title='".i18n('lastUpdate')."'></div><div class='data'>".$lastUpdate."</div>".($showWarning ? $warning : "");
		$str .= "</div>";
		
		$str .= "</div>";
		
		return $str;
	}
	
	function Koubachi_UpdateWebFront($plants) {
		// webfront htmlbox
		$visuPathId = IPSUtil_ObjectIDByPath(PATH_WEBFRONT);
		$overviewVarId = IPS_GetObjectIDByIdent("Overview", $visuPathId);
		//IPSLogger_Dbg(__file__, "overviewVarId: ".$overviewVarId);
		
		$csspath = "/user/Koubachi/";
		
		$str = "<link rel='stylesheet' type='text/css' href='".$csspath."Koubachi.css'>";
		$str .= "<link rel='stylesheet' type='text/css' href='".$csspath."jquery-bubble-popup-v3.css'>";

		$bootstrapScript = '
			var self = this;
			self.counter = 0;
			function bootstrap(script) {
				var outer = self;
				self.counter = self.counter + 1;
				
				console.log("bootstrapping " + script);
				var oHead = document.getElementsByTagName("HEAD").item(0);
				var oScript = document.createElement("script");
				oScript.type = "text/javascript";
				oScript.src = script;
				
				var func = function() {
					outer.counter = outer.counter - 1;
					console.log("loaded: ", this.src, " - ", outer.counter);
					if(outer.counter <= 0) {
						initKoubachi();
					}
				};
				oScript.onload = func;
				oHead.appendChild(oScript);
			}
			function initKoubachi() {
				instance = new Koubachi();
			}
			bootstrap("http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js");
			bootstrap("user/Koubachi/Koubachi.js");
		';
		
		$str .= "<div class='plants'>";

		foreach($plants as $plant) {
			$str .= createEntry($plant);
		}

		$str .= "</div><iframe src='dummy.php' width='0px' height='0px' frameborder='0' scrolling='no' onload='".$bootstrapScript."'></iframe>";
		
		SetValue($overviewVarId, $str);
	}
	
	function getDevices($config) {
		$devices = getApiData($config, API_DEVICES, API_XML_DEVICE);
		return $devices;
	}
	
	function getPlants($config) {
		$plants = getApiData($config, API_PLANTS, API_XML_PLANT);
		return $plants;
	}
?>