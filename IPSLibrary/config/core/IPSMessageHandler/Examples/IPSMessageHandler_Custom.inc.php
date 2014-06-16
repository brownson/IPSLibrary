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
		switch($variable) {
			// Taster zur Ansteuerung der Markisen
			case 59542: /*InpS2*/
			case 37689: /*InpS6*/ 
			case 56752: /*InpS3*/ 
			case 52590: /*InpS7*/ 

			// Taster zur Steuerung der Terrassenbeleuchtung
			case 12456: /*InpS1*/ 
			case 37693: /*InpS5*/ 
			case 57359: /*InpL1*/ 
			case 55574: /*InpL5*/ 

			// Taster zur Ansteuerung von Audio Terrasse
			case 51018: /*InpS4*/   /*Volume-*/
			case 44967: /*InpS8*/   /*Volume+*/
			case 31397: /*InpL6*/   /*Source*/
			case 33116: /*InpL3*/   /*Previous Titel*/
			case 48165: /*InpL7*/   /*Next Titel*/
			case 35556: /*InpL4*/   /*PowerOff*/
			case 43222: /*InpL8*/   /*PowerOn*/
				if (GetValue(42673)==0) {
					return true; // Nur bei Anwesenheit
				} else {
					IPSLogger_Not(__file__, 'Terrassentaster bei Abwesenheit aktiviert ...!!!', 2);
					return false;
				}
				break;
			case 57607:
				HM_WriteValueBoolean(40393, 'STATE', true);
				HM_WriteValueBoolean(40393, 'STATE', false);
				break;
			case 20451:
				HM_WriteValueBoolean(33866, 'STATE', true);
				HM_WriteValueBoolean(33866, 'STATE', false);
				break;
			case 45699:
				IPSUtils_Include ("IPSLight.inc.php",          "IPSLibrary::app::modules::IPSLight");
				IPSUtils_Include ("Entertainment.inc.php",     "IPSLibrary::app::modules::Entertainment");
				if (GetValue(17535)) {
					IPSLight_SetSwitch(17535,false);
					IPSLight_SetSwitch(23068,false);
					IPSLight_SetSwitch(18489,false);
					Entertainment_SetRoomPowerByRoomId(22483,false);
				} else {
					IPSLight_SetSwitch(17535,true);
					Entertainment_SetRoomPowerByRoomId(22483,true);
				}
				return false;
			default:
				return true;
		}
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
	function IPSMessageHandler_AfterHandleLibraryEvent($variable, $value, $component, $module) {
		switch($variable) {
			// Taster zur Ansteuerung der Markisen
			case 33471: /*InpS2*/
				HM_WriteValueBoolean(15191, 'STATE', $value);
				break;
			default:
		}
	}

	/** @}*/
?>