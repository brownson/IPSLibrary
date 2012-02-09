<?
	/**@addtogroup entertainment_interface
	 * @ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_InterfaceWinLIRCSnd.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Anbindung von IRTrans oder WinLIRC (IR Schnittstelle)
	 *
	 */

	include_once "Entertainment.inc.php";

	// ---------------------------------------------------------------------------------------------------------------------------
	function get_MessageTypeByControl($Control) {
	   $CommConfig = get_CommunicationConfiguration();
	   $MessageTypes = $CommConfig[c_Comm_WinLIRC][c_Property_MessageTypes];
	   if (array_key_exists($Control, $MessageTypes)) {
	      return $MessageTypes[$Control];
	   } else {
	      return c_MessageType_Action;
	   }
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_SendData_doTranslation($Control, $Button, $TranslationListItems) {
		foreach ($TranslationListItems as $Item) {
	      $Data = explode('.', $Item);
     		if ($Data[0]=='*') { $ControlNew=$Control;} else { $ControlNew = $Data[0];}
     		if ($Data[1]=='*') { $ButtonNew=$Button;} else { $ButtonNew = $Data[1];}
     		IPSLogger_Trc(__file__, "Translate RemoteMessage $Control.$Button -> $ControlNew.$ButtonNew");
		   WinLIRC_doSendData($ControlNew,$ButtonNew);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_SendData_Translation($Control, $Button) {
	   $CommConfig = get_CommunicationConfiguration();
	   $TranslationList = $CommConfig[c_Comm_WinLIRC][c_Property_OutTranslationList];
	   if (array_key_exists($Control.'.'.$Button, $TranslationList)) {
	      $TranslationListItems = $TranslationList[$Control.'.'.$Button];
	      WinLIRC_SendData_doTranslation($Control, $Button, $TranslationListItems);
	      return true;
	   } else if (array_key_exists($Control, $TranslationList)) {
	      $TranslationListItems = $TranslationList[$Control];
	      WinLIRC_SendData_doTranslation($Control, $Button, $TranslationListItems);
	      return true;
		} else {
			return false;
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_doSendData($Control, $Button) {
     	$ModuleId = get_CommConfigValue(c_Comm_WinLIRC, c_Property_Instance);
     	IPSLogger_Com(__file__, "Send Data to WinLIRC, Control='$Control', Command='$Button' (Module=$ModuleId)");
     	if ($ModuleId!==false and $ModuleId<>"") {
      	if (@IRT_SendOnce($ModuleId, $Control, $Button)==false) {
		     	IPSLogger_Wrn(__file__, "Unknown IRTrans Message, Control='$Control', Command='$Button' (Module=$ModuleId)");
			};
      }
	}
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_SendData($Parameters) {
	   $Control = $Parameters[1];
		$Button  = $Parameters[2];

		if (!WinLIRC_SendData_Translation($Control, $Button)) {
		   WinLIRC_doSendData($Control, $Button);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_ReceiveData_Translation(&$Control, &$Button) {
	   $CommConfig = get_CommunicationConfiguration();
	   $TranslationList = $CommConfig[c_Comm_WinLIRC][c_Property_InpTranslationList];
	   if (array_key_exists($Control.'.'.$Button, $TranslationList)) {
	      $Data = explode('.', $TranslationList[$Control.'.'.$Button]);
     		IPSLogger_Trc(__file__, "Translate RemoteMessage $Control.$Button -> $Data[0].$Data[1]");
	      $Control = $Data[0];
	      $Button  = $Data[1];
	   } else if (array_key_exists($Control, $TranslationList)) {
	      $ControlNew = $TranslationList[$Control];
     		IPSLogger_Trc(__file__, "Translate RemoteMessage $Control.$Button -> $ControlNew.$Button");
	      $Control = $TranslationList[$Control];
		} else {
		  // Nothing to Translate ...
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_ReceiveData($RemoteControl, $Button, $MessageType) {
		WinLIRC_ReceiveData_Translation($RemoteControl, $Button);
     	$Parameters = array(c_Comm_WinLIRC, $RemoteControl, $Button);
      if (!Entertainment_ReceiveData($Parameters, $MessageType)) {
         if ($MessageType==c_MessageType_Action) {
         	WinLIRC_SendData($Parameters);
         }
      }
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_ReceiveData_Webfront($RemoteControl, $Button) {
     	IPSLogger_Com(__file__, "Received Data from WinLIRC-Webfront, Control='$RemoteControl', Command='$Button'");
      WinLIRC_ReceiveData($RemoteControl, $Button, c_MessageType_Action);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_ReceiveData_Program($Program, $DeviceName) {
     	IPSLogger_Com(__file__, "Received Program '$Program' from WinLIRC-Webfront, Device='$DeviceName'");
     	$ControlId = get_ControlIdByDeviceName($DeviceName, c_Control_Program);
     	if ($Program=='next') {
	     	Entertainment_SetProgramNext($ControlId);
     	} else if ($Program=='prev') {
	     	Entertainment_SetProgramPrev($ControlId);
     	} else {
	     	Entertainment_SetProgram($ControlId, $Program);
     	}
     	return GetValue($ControlId);
	}

  /** @}*/
?>