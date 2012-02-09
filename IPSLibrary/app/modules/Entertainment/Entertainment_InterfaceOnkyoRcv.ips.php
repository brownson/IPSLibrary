<?
	/**@addtogroup entertainment_interface
	 * @ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_InterfaceOnkyoRcv.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Empfangs Script zur Anbindung eines Onkyo Receivers
	 *
	 */

	include_once "Entertainment.inc.php";


	if($IPS_SENDER == "RegisterVariable") {
   	Onkyo_ReceiveData_Register($IPS_INSTANCE, $IPS_VALUE);
	}
	
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function Onkyo_ReceiveData_Register($RegisterId, $Value) {
		$Data    = substr($Value,18);
		$Command = substr($Data,0,3);
		$Param   = substr($Data,3,2);

		IPSLogger_Com(__file__, 'Received Message from Onkyo: '.$Data.' (Command='.$Command.', Param='.$Param.')');

		if ($Command=='MVL' or $Command=='ZVL') {
		   $Param = hexdec($Param);
		}

      Entertainment_ReceiveData(array(c_Comm_Onkyo, $Command, $Param), c_MessageType_Info);
	}

  /** @}*/
?>