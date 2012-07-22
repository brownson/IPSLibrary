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
			default:
				return true;
		}
		return true;
	}

	/*
	 *
	 *
	 */
	 
	/** @}*/
?>