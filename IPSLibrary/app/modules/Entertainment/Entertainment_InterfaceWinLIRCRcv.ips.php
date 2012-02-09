<?
	/**@addtogroup entertainment_interface
	 * @ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_InterfaceWinLIRCRcv.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Anbindung IRTrans/WinLIRC, Script wird über Events von der WinLIRC Button Variable getriggert.
	 *
	 */

	include_once "Entertainment.inc.php";
	include_once "Entertainment_InterfaceWinLIRCSnd.inc.php";

	if($_IPS['SENDER'] == "Variable") {
	   WinLIRC_ReceiveData_Variable($_IPS['VARIABLE'], $_IPS['VALUE']);
	}
	
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function WinLIRC_ReceiveData_Variable($VariableId, $Value) {
	   if ($Value == "") {
			return;
		}

		$Button = $Value;
		$InstanceId = IPS_GetParent($VariableId);
		$ChildrenIds = IPS_GetChildrenIDs($InstanceId);
		foreach ($ChildrenIds as $Id) {
		   if ($Id <> $VariableId) {
		      $RemoteControl = GetValue($Id);
		   }
		}
     	IPSLogger_Com(__file__, "Received Data from WinLIRC-Variable, Control='$RemoteControl', Command='$Button'");
 		$MessageType = get_MessageTypeByControl($RemoteControl);
		WinLIRC_ReceiveData($RemoteControl, $Button, $MessageType);
	}

  /** @}*/
?>