<?
	/**@ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_Connect.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Funktionen zum Herstellen einer Verbindung.
	 *
	 */

	include_once "Entertainment.inc.php";

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_Connect($DeviceName, $Value, $Asynchron=false) {
	   if ($Asynchron) {
			IPS_RunScriptEx(c_ID_ConnectAsynchronScript, Array("DeviceName" => $DeviceName, "Value" => $Value));

	   } else {
		   $DeviceConfig = get_DeviceConfiguration();
		   $PowerControl = $DeviceConfig[$DeviceName][c_Control_DevicePower];
		   if (array_key_exists(c_Property_ConnectSocket, $PowerControl)) {
		      $CommInterface = $PowerControl[c_Property_ConnectSocket];
		      Entertainment_Connect_Socket($CommInterface, $DeviceName, $Value);
		   }
	   }
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_Connect_Socket($CommInterface, $DeviceName, $Value) {
	   $CommConfig = get_CommunicationConfiguration();
	   $ModuleId          = $CommConfig[$CommInterface][c_Property_Instance];
	   $IPAddress         = $CommConfig[$CommInterface][c_Property_IPAddress];
      $ConnectionTimeout = $CommConfig[$CommInterface][c_Property_Timeout];

      if ($IPAddress=="" or $ModuleId=="") {
			IPSLogger_Wrn(__file__, "IPAddress/ModuleId NOT defined, Connection to Device '$DeviceName' NOT possible");
			return;
		}
		
		if ($Value) {
			Entertainment_Connect_WaitForSocketOpen($CommInterface);
		} else {
		   Entertainment_Connect_SocketClose($CommInterface);
		}
	}

  	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_Connect_SocketClose($CommInterface) {
	   $CommConfig        = get_CommunicationConfiguration();
	   $IPAddress         = $CommConfig[$CommInterface][c_Property_IPAddress];
	   $ModuleId          = $CommConfig[$CommInterface][c_Property_Instance];

		$AllSocketDevicesOff = true;
	   $DeviceConfig        = get_DeviceConfiguration();
		foreach ($DeviceConfig as $DeviceName=>$DeviceData) {
		   if (array_key_exists(c_Control_DevicePower, $DeviceData)) {
			   $PowerControl = $DeviceConfig[$DeviceName][c_Control_DevicePower];
			   if (array_key_exists(c_Property_ConnectSocket, $PowerControl)) {
			      if ($PowerControl[c_Property_ConnectSocket]==$CommInterface
					    and isDevicePoweredOnByDeviceName ($DeviceName)) {
						$AllSocketDevicesOff = false;
			      }
			   }
		   }
		}

		$Status = CSCK_GetOpen($ModuleId);
		IPSLogger_Dbg(__file__, "Check Connection for Interface '$CommInterface', SocketOpen=".boolString($Status).
										" and AllDevicesOff=".boolString($AllSocketDevicesOff));
		if ($Status and $AllSocketDevicesOff) {
			IPSLogger_Inf(__file__, "Close Socket Connection to Interface '$CommInterface' (IP=$IPAddress)");
			CSCK_SetOpen($ModuleId , false);
			IPS_ApplyChanges($ModuleId);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_Connect_WaitForSocketOpen($CommInterface) {
	   $CommConfig        = get_CommunicationConfiguration();
	   $ModuleId          = $CommConfig[$CommInterface][c_Property_Instance];
	   $IPAddress         = $CommConfig[$CommInterface][c_Property_IPAddress];
      $ConnectionTimeout = $CommConfig[$CommInterface][c_Property_Timeout];

      if ($IPAddress=="" or $ModuleId=="") {
			IPSLogger_Wrn(__file__, "IPAddress/ModuleId NOT defined, Connection to Interface '$CommInterface' NOT possible");
			return false;
		} else if (CSCK_GetOpen($ModuleId)) {
		   return $ModuleId;
		} else {
			$RetryCount = 0;
			while ($RetryCount < $ConnectionTimeout) {
				$RetryCount = $RetryCount + 1;
				$Status = CSCK_GetOpen($ModuleId);
				IPSLogger_Dbg(__file__, "Check Connection for Interface '$CommInterface', GetOpen=>>".boolString($Status)."<<");
				if (!$Status) {
					IPSLogger_Trc(__file__, "Ping Module '$CommInterface' (Retry=".$RetryCount.")");

					if (Sys_Ping($IPAddress, 100)) {
						IPSLogger_Inf(__file__, "Open Socket Connection to Module '$CommInterface' (IP=$IPAddress)");
						CSCK_SetOpen($ModuleId , true);
						IPS_ApplyChanges($ModuleId);
						$RetryCount = $ConnectionTimeout;
						return $ModuleId;
					} else {
					   sleep(1);
					}
				} else {
					return $ModuleId;
				}
			}
		}
	}




	// ---------------------------------------------------------------------------------------------------------------------------
	function boolString($bool) {
	   if ($bool) {
			return "true";
	   } else if (!$bool) {
			return "false";
	   } else {
			return "???";
	   }
	}

  /** @}*/
?>