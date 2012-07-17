<?
    IPSUtils_Include ('NetIO230B.inc.php',      'IPSLibrary::app::hardware::NetIO230B');
    
    if(isset($IPS_SENDER)) {
        $acquiredSemaphore = false;
        if(isset($IPS_VARIABLE) && is_numeric($IPS_VARIABLE)) {
            $semaphoreName = "SemaphoreNetIO-$IPS_VARIABLE";
            if(!IPS_SemaphoreEnter($semaphoreName, 1)) {
                IPSLogger_Inf(__file__, "Unable to acquire semaphore $semaphoreName.");
                return;
            }
            $acquiredSemaphore = true;
        }
        
        if($IPS_SENDER == "RunScript" && isset($action)) {
            //IPSLogger_Dbg(__file__, "RunScript - Action: $action");
            switch ($action) {
                case "poweroff":
                    NetIO230B::getInstanceFromPortIdAndSetStatus($IPS_VARIABLE, false);
                    break;
                case "poweron":
                    NetIO230B::getInstanceFromPortIdAndSetStatus($IPS_VARIABLE, false);
                    break;
                case "getStatus":
                    if(!isset($source)) {
                        throw new Exception("Action 'getStatus' requires 'source' parameter");
                    }
                    $regVarId = IPS_GetParent($source);
                    $netIO = new NetIO230B($regVarId);
                    $netIO->updateStatus();
                    break;
                default;
                   // toggle
                    IPSLogger_Inf(__file__, "Unknown action ".$action.". Toggleing power.");
                    NetIO230B::getInstanceFromPortIdAndSetStatus($IPS_VARIABLE, !GetValue($IPS_VARIABLE));
            }
        } else if($IPS_SENDER == "RegisterVariable") {
            $regVarInstance = $IPS_INSTANCE;
            $netIO = new NetIO230B($regVarInstance);
            $netIO->handleResponse($IPS_VALUE);
        } else if($IPS_SENDER == "WebFront") {
            NetIO230B::getInstanceFromPortIdAndSetStatus($IPS_VARIABLE, $IPS_VALUE);
        } else if($IPS_SENDER == "Execute") {
            
        } else if($IPS_SENDER == "Variable" || $IPS_SENDER == "TimerEvent") {
            // probably just doing an include somewhere
        } else {
            IPSLogger_Wrn(__file__, "Unhandled IPS_SENDER: ".$IPS_SENDER);
        }
        if($acquiredSemaphore) {
            IPS_SemaphoreLeave($semaphoreName);
        }
    }
?>