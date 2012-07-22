<?
	/**@defgroup ipsmessagehandler_configuration IPSMessageHandler Konfiguration
	 * @ingroup ipsmessagehandler
	 * @{
	 *
	 * Callback Methoden des IPSMessageHandlers.
	 *
	 * @file          IPSMessageHandler_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 12.06.2012<br/>
	 *
	 */

	/**
	 *
	 * Callback Funktion, die vor dem behandeln eines Events aufgerufen wird
	 *
	 * @return boolean Funktions Ergebnis, bei false wird das Event NICHT weitergereicht
	 */
	function IPSMessageHandler_BeforeHandleEvent($variable, $value, IPSComponent $component, IPSModule $module) {
		return true;
	}

	/*
	 *
	 *
	 */
	 
	/** @}*/
?>