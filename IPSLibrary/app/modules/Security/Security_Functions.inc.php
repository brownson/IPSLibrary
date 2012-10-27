<?
	IPSUtils_Include ("IPSLogger.inc.php", "IPSLibrary::app::core::IPSLogger");
    IPSUtils_Include ("IPSInstaller.inc.php", "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("Security_Language.inc.php", "IPSLibrary::app::modules::Security");
	
	define ("c_Name", "Name");
    define ("c_Location", "Location");
	define ("c_Variable_ID", "VariableID");
	
	define ("cat_MOTION", "Motion");
	define ("cat_SMOKE", "Smoke");
	define ("cat_CLOSURE", "Closure");
	define ("cat_ALL", "All");
	
    define ("v_MOTION", "MOTION");
	define ("v_ALARM_MODE", "AlarmMode");
	
	define ("v_LOG_LENGTH_LIMIT", 3000);
	
	define ("v_ALARM_MODE_NAME", "alarmModeName");
	define ("v_ALARM_MODE_COLOR", "alarmModeColor");
	
	function Security_getMotionDevices() {
		IPSUtils_Include("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
		$devices = getMotionDevices();
		return $devices;
	}
	
	function Security_getSmokeDevices() {
		IPSUtils_Include("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
		$devices = getSmokeDevices();
		return $devices;
	}
	
	function Security_getClosureDevices() {
		IPSUtils_Include("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
		$devices = getClosureDevices();
		return $devices;
	}
	
    function Security_getConfigById($variableId, $type = cat_ALL) {
		$devices = array();
		switch ($type) {
			case cat_SMOKE:
				$devices = Security_getSmokeDevices();
				break;
			case cat_MOTION:
				$devices = Security_getMotionDevices();
				break;
			case cat_CLOSURE:
				$devices = Security_getClosureDevices();
				break;
			case cat_ALL:
				$devices = Security_getSmokeDevices();
				$devices = array_merge($devices, Security_getMotionDevices());
				$devices = array_merge($devices, Security_getClosureDevices());
				break;
			default:
				throw new Exception("Unsupported event type '".$type."'");
		}
		
		foreach($devices as $device) {
			if($device[c_Variable_ID] == $variableId) {
				return $device;
			}
		}
		return null;
	}
	
	function Security_resolveDevice(&$event) {
		$event["device"] = Security_getConfigById($event["deviceId"], $event["type"]);
		// IPSLogger_Dbg(__file__, "Found device: ".print_r($event["device"], true));
	}
	
	function Security_handleEvent($event) {
		Security_resolveDevice($event);
		
		$type = $event["type"];
		$value = $event["value"];
		
		IPSLogger_Trc(__file__, $type."Event - Source: ".$event["device"][c_Name]."@".$event["device"][c_Location]."(".$event["device"][c_Variable_ID]."/".$value.")");
		
		// <test>
		$variableOverrides[$event["device"][c_Variable_ID]] = $value;
		$result = Security_evaluateAlarmCondition($variableOverrides);
		$raiseAlarm = $result[0];
		$log = $result[1];
		//IPSLogger_Dbg(__file__, $log);
		
		Security_logEvent($event);
		
		if($raiseAlarm) {
			Security_raiseAlarm($event, $log);
		}
	}
	
	/**
	 * Evaluate if an alarm needs to be raised. This should be executed when ever a Motion or Closure event was detected.
	 */
	function Security_evaluateAlarmCondition($variableOverrides) {
		$executor = new Executor(getAlarmConditions(), $variableOverrides);
		$result = $executor->run();
		return array($result, $executor->log);
	}
	
	function Security_logEvent($event) {
		$type = $event["type"];
		$device = $event["device"];
		
		// update device motion log with plain time stamp
		$dataPath = "Program.IPSLibrary.data.modules.Security.".$type.".".$device[c_Variable_ID];
		$idDeviceData = get_ObjectIDByPath($dataPath);
		
		$formattedDate = date("Y-m-d H:i:s", $event["timestamp"]);
		
		$lastEventId = IPS_GetVariableIDByName("Last".$type, $idDeviceData);
		
		$oldValue = substr(GetValueString($lastEventId), 0, v_LOG_LENGTH_LIMIT);
		SetValueString($lastEventId, $formattedDate."<br>".$oldValue);
		
		// update global motion log with text
		$dataPath = "Program.IPSLibrary.data.modules.Security.".$type."Log";
		$idGlobalLog = get_ObjectIDByPath($dataPath);
		//IPSLogger_Trc(__file__, "Event Log Id ".$idGlobalLog);
		
		$value = substr(GetValueString($idGlobalLog), 0, v_LOG_LENGTH_LIMIT);
		$newValue = sprintf(getLang("ALARM_".$type."_DETECTED_BODY"), $formattedDate, $device[c_Name], $device[c_Location]);
		
		SetValueString($idGlobalLog, $newValue."<br>".$value);
	}
	
	function Security_getAlarmModeId() {
		$dataPath = "Program.IPSLibrary.data.modules.Security.".v_ALARM_MODE;
		$varId = get_ObjectIDByPath($dataPath);
		
		if($varId === FALSE) {
			throw new Exception("Variable ".v_ALARM_MODE." missing");
		}
		
		return $varId;
	}
	
	function Security_setAlarmMode($mode) {
		IPSUtils_Include("Security_Configuration.inc.php", "IPSLibrary::config::modules::Security");
		
		IPSLogger_Inf(__file__, "Setting Alarm Mode: ".getAlarmModes()[v_ALARM_MODE_NAME][$mode]);
	
		$varId = Security_getAlarmModeId();
		SetValueInteger($varId, $mode);
	}
	
	function Security_getAlarmMode() {
		$value = GetValueInteger(Security_getAlarmModeId());
		return $value;
	}
	
	function Security_raiseAlarm($event, $log = "") {
		$type = $event["type"];
		$device = $event["device"];
		$formattedDate = date("Y-m-d H:i:s", $event["timestamp"]);
		
		IPSLogger_Dbg(__file__, "Sending $type ALARM notification via prowl");
		$message = sprintf(getLang("ALARM_".$type."_DETECTED_BODY"), $formattedDate, $device[c_Name], $device[c_Location]);
		IPSLogger_SendProwlMessage('Alarm', $message, 2);
		
		IPSLogger_Dbg(__file__, "Sending $type ALARM notification via mail");
		// device log
		$dataPath = "Program.IPSLibrary.data.modules.Security.".$type.".".$device[c_Variable_ID];
		$idDeviceData = get_ObjectIDByPath($dataPath);
		$lastEventId = IPS_GetVariableIDByName("Last".$type, $idDeviceData);
		$timeEntries = str_replace("<br>", "\n", GetValueString($lastEventId));
		$message = sprintf(getLang("ALARM_".$type."_DETECTED_BODY_HISTORY"), $formattedDate, $device[c_Name], $device[c_Location], $log, $timeEntries);
		SMTP_SendMail(SMPT_MailId, getLang("ALARM_".$type."_DETECTED_HEADER"), $message);
	}
	
	function b2s($bool) {
		return $bool ? "True" : "False";
	}
	
	function getFuncArgs($args) {
		$ret = array();
		foreach($args as $arg) {
			if(is_array($arg)) {
				$ret = array_merge($ret, $arg);
			} else {
				$ret[] = $arg;
			}
		}
		return $ret;
	}
	
	class cDefinition {
		function cDefinition($name, cCondition $condition) {
			$this->name = $name;
			$this->condition = $condition;
		}
	}
	
	class Executor {
		var $definitions;
		var $variables;
		var $log = "", $cLog = "";
		function Executor($definitions, $variableOverrides) {
			$this->definitions = $definitions;
			$this->variables = $variableOverrides;
		}
		function run() {
			$i = 0;
			$shouldRaiseAlarm = false;
			foreach($this->definitions as $definition) {
				$this->logMsg("Evaluating Definition ".$i++.": '".$definition->name."'");
				
				$condition = $definition->condition;
				if($condition->matches($this)) {
					if($this->log != "") $this->log .= "\n";
					
					// append condition log to global log
					$this->log .= $this->cLog;
					
					$shouldRaiseAlarm = true;
				}
				// reset condition log
				$this->cLog = "";
			}
			return $shouldRaiseAlarm;
		}
		
		function GetValue($id) {
			//IPSLogger_Dbg(__file__, "Using Executor Interface for id '".$id."': ".print_r($this->variables, true));
			if(isset($this->variables[$id])) {
				return $this->variables[$id];
			}
			return GetValue($id);
		}
		
		function printLog($output) {
			if($this->cLog != "") {
				$this->cLog .= "\n";
			}
			$this->cLog .= $output;
			IPSLogger_Trc(__file__, $output);
		}
		
		function logPrefix($callers) {
			$size = sizeof($callers) - 5;
			if($size <= 0) return "";
			
			return str_repeat("-|-", $size);
		}
		
		function logClass() {
			$callers = debug_backtrace();
			
			$caller = $callers[1];
			
			$output = $this->logPrefix($callers).$caller["class"].".".$caller['function'];
			$this->printLog($output);
		}
		
		function logMsg($msg) {
			$callers = debug_backtrace();
			
			$prefix = $this->logPrefix($callers);
			$prefix .= "->>";
			
			$output = $prefix.$msg;
			$this->printLog($output);
		}
	}
	
	class cVariable {
		var $id;
		var $lastEvent;
		
		function cVariable($id) {
			$this->id = $id;
			$this->data = IPS_GetVariable($id);
		}
		function changed() {
			//IPSLogger_Dbg(__file__, "Data: ".print_r($this->data, true));
			return $this->data["VariableChanged"];
		}
		function updated() {
			return $this->data["VariableUpdated"];
		}
	}
	
	class cCondition {
		function cCondition() {}
		function matches(Executor $executor) {throw new Exception("Matches not implemented for ".__CLASS__);}
	}
	
	class cAlarmType extends cCondition {
		var $names;
		function cAlarmType() {
			$this->names = getFuncArgs(func_get_args());
		}
		function matches(Executor $executor) {
			$executor->logClass();
			
			$alarmMode = Security_getAlarmMode();
			$alarmModes = getAlarmModes();
			$alarmModeName = $alarmModes[v_ALARM_MODE_NAME][$alarmMode];
			//IPSLogger_Dbg(__file__, "Active Alarm Mode ".$alarmModeName);
			
			foreach($this->names as $name) {
				if($alarmModeName == $name) {
					$executor->logMsg("True. Matched active alarm mode ".$name);
					return true;
				}
			}
			//IPSLogger_Dbg(__file__, "False. No match for alarm mode.");
			return false;
		}
	}
	
	class cAnd extends cCondition{
		var $conditions;
		function cAnd() {
			$this->conditions = getFuncArgs(func_get_args());
		}
		function matches(Executor $executor) {
			$executor->logClass();
			
			foreach($this->conditions as $condition) {
				if(!$condition->matches($executor)) {
					$executor->logMsg("False");
					return false;
				}
			}
			
			$executor->logMsg("True");
			return true;
		}
	}
	
	class cOr extends cCondition{
		var $conditions;
		function cOr() {
			$this->conditions = getFuncArgs(func_get_args());
		}
		function matches(Executor $executor) {
			$executor->logClass();
			
			$matched = false;
			foreach($this->conditions as $condition) {
				if($condition->matches($executor)) {
					$executor->logMsg(b2s($matched));
					$matched = true;
				}
			}
			return $matched;
		}
	}
	
	class cValue extends cCondition {
		var $variable;
		var $value;
		function cValue($variable, $value) {
			$this->variable = $variable;
			$this->value = $value;
		}
		function matches(Executor $executor) {
			$executor->logClass();
			
			$value = $executor->GetValue($this->variable->data["VariableID"]);
			
			$result = $value == $this->value;
			$config = Security_getConfigById($this->variable->id);
			$executor->logMsg(b2s($result).". Value of '".$config[c_Location]."->".$config[c_Name]."'/Required: . ".b2s($value)."/".b2s($this->value));
			
			return $result;
		}
	}
	
	class cOrder extends cCondition {
		var $variables;
		function cOrder() {
			$this->variables = func_get_args();
		}
		/**
		 * Go from left to right and check if the timestamps are increasing.
		 */
		function matches(Executor $executor) {
			$executor->logClass();
			
			$text = "";
			foreach($this->variables as $variable) {
				if($text <> "") {
					$text .= "-->";
				}
				$config = Security_getConfigById($variable->id);
				$text .= "'".$config[c_Name]."'";
			}
			
			$prevConditionTime = -1;
			foreach($this->variables as $variable) {
				if($prevConditionTime == -1) {
					// get inital time
					$prevConditionTime = $variable->changed();
				} else {
					$curConditionTime = $variable->changed();
					if($curConditionTime > $prevConditionTime) {
						$prevConditionTime = $curConditionTime;
						continue;
					} else {
						$executor->logMsg("False. Wrong Order of events. Need: ".$text);
						// ." Current/Previous: ".$prevConditionTime."/".$curConditionTime
						return false;
					}
				}
			}
			$executor->logMsg("True. Event order correct. ".$text);
			return true;
		}
	}
    
?>