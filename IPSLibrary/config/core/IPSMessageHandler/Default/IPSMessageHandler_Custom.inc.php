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
	 * @param integer $variable ID der auslösenden Variable
	 * @param string $value Wert der Variable
	 * @param string $component, Source Componente
	 * @param string $module Destination Module
	 * @return boolean Funktions Ergebnis, bei false wird das Event NICHT weitergereicht
	 */
	function IPSMessageHandler_BeforeHandleEvent($variable, $value, IPSComponent $component, IPSModule $module) {
		return true;
	}

	/**
	 *
	 * Callback Funktion, die nach dem behandeln eines Events aufgerufen wird
	 *
	 * @param integer $variable ID der auslösenden Variable
	 * @param string $value Wert der Variable
	 * @param string $component, Source Componente
	 * @param string $module Destination Module
	 *
	 */
	function IPSMessageHandler_AfterHandleEvent($variable, $value, IPSComponent $component, IPSModule $module) {
	}

	/**
	 *
	 * Callback Funktion, die für das Behandeln von IPSLibary Events aufgerufen wird
	 *
	 * @param string $variable ID der auslösenden Variable
	 * @param string $value Wert der Variable
	 * @param string $module Name des auslösenden Modules
	 * @param string $event Name des auslösenden Events
	 *
	 */
	function IPSMessageHandler_HandleLibraryEvent($variable, $value, $component, $module) {
	}

	/** @}*/
?>