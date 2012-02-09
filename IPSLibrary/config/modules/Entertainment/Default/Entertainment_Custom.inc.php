<?
	/**@addtogroup entertainment
	 * @{
	 *
	 * @file          Entertainment_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Callback Methoden der Entertainment Steuerung
	*/

	IPSUtils_Include ("Entertainment_InterfaceWinLIRCSnd.inc.php", "IPSLibrary::app::modules::Entertainment");
	IPSUtils_Include ("Entertainment.inc.php",                     "IPSLibrary::app::modules::Entertainment");

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_Before_SendData($Parameters) {
		return true;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_After_SendData($Parameters) {
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_Before_ReceiveData($Parameters, $MessageType) {
		return true;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_After_ReceiveData($Parameters, $MessageType) {
	}

	/** @}*/
?>