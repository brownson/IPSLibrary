<?
	/**@defgroup entertainment_interface Entertainment Interfaces
	 * @ingroup entertainment
	 * @{
	 *
	 * Interface zu externen Komponenten
	 *
	 * Dieses Script ist als Aktions Script für alle Variablen hinterlegt, die über das WebFront
	 * verändert werden können.
	 *
	 * Zusätzlich wird es auch von Register Variablen bzw. anderen Variablen per Event getriggert.
	 *
	 * @file          Entertainment_Interface.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 */

	include_once "Entertainment.inc.php";

	// ---------------------------------------------------------------------------------------------------------------------------
	// WebFront
	// ---------------------------------------------------------------------------------------------------------------------------
	if ($_IPS['SENDER'] == 'WebFront' || $_IPS['SENDER'] == 'Action') {
		$ControlType = get_ControlType($_IPS['VARIABLE']);
		switch ($ControlType) {
		   case c_Control_RoomPower:
		      Entertainment_SetRoomPower($_IPS['VARIABLE'], $_IPS['VALUE']);
		      break;
		   case c_Control_DevicePower:
		      Entertainment_SetDevicePower($_IPS['VARIABLE'], $_IPS['VALUE']);
		      break;
		   case c_Control_Source:
		      Entertainment_SetSource($_IPS['VARIABLE'], $_IPS['VALUE']);
		      break;
		   case c_Control_Group:
		      Entertainment_SetGroupControlVisibility($_IPS['VARIABLE'], $_IPS['VALUE']);
		      break;
		   case c_Control_Muting:
		   case c_Control_Volume:
		   case c_Control_Balance:
		   case c_Control_Treble:
		   case c_Control_Middle:
		   case c_Control_Bass:
		   case c_Control_Mode:
		   case c_Control_Program:
		      Entertainment_SetControl($_IPS['VARIABLE'], $_IPS['VALUE']);
		      break;
			default:
				IPSLogger_Err(__file__, 'Unknown Control with ID='.$_IPS['VARIABLE'].' !');
		}
	}

  /** @}*/
?>