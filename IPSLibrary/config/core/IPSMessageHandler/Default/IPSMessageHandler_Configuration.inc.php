<?
	/**@defgroup ipsmessagehandler_configuration IPSMessageHandler Konfiguration
	 * @ingroup ipsmessagehandler
	 * @{
	 *
	 * Konfigurations Einstellungen des IPSMessageHandlers.
	 *
	 * @file          IPSMessageHandler_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 */

	/**
	 *
	 * Liefert Liste mit Event Variablen, die vom MessageHandler bearbeitet werden. Diese Liste wurde von
	 * den Installations Prozeduren befüllt.
	 *
	 * @return string[] Key-Value Array mit Event Variablen und der dazugehörigen Parametern
	 */
	function IPSMessageHandler_GetEventConfiguration() {
		$eventConfiguration = array(
			);

		return $eventConfiguration;
	}

	/**
	 *
	 * Liefert Liste mit Event Variablen, die vom MessageHandler bearbeitet werden. Diese Liste kann vom Anwender 
	 * frei definiert werden
	 *
	 * @return string[] Key-Value Array mit Event Variablen und der dazugehörigen Parametern
	 */
	function IPSMessageHandler_GetEventConfigurationCust() {
		$config = array(
			);

		return $config;
	}
	 
	/**
	 *
	 * Liefert Liste mit IR Befehlen, die vom MessageHandler bearbeitet werden.
	 *
	 * @return string[] Key-Value Array mit Event Variablen und der dazugehörigen Parametern
	 */
	function IPSMessageHandler_GetEventConfigurationIR() {
		$config = array(
			);
		return $config;
	}


/** @}*/
?>