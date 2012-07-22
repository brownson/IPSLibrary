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
	 *   Version 2.50.1, 31.01.2012<br/>
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
			// Synchronisierung MediaPlayer
			39520 => array('OnChange','IPSComponentPlayer_MediaPlayer,24582','IPSModulePlayer_NetPlayer',),

			// Synchronisierung AudioMax
			28071 => array('OnChange','IPSComponentAVControl_AudioMax,25694','IPSModuleAVControl_Entertainment',),

			// Synchronization Shutter Position
			12757 => array('OnChange','IPSComponentShutter_Homematic,50248','IPSModuleShutter_IPSShadowing,',),
			50835 => array('OnChange','IPSComponentShutter_Homematic,24528','IPSModuleShutter_IPSShadowing,',),
			26628 => array('OnChange','IPSComponentShutter_Homematic,56299','IPSModuleShutter_IPSShadowing,',),
			24799 => array('OnChange','IPSComponentShutter_Homematic,15951','IPSModuleShutter_IPSShadowing,',),
			22395 => array('OnChange','IPSComponentShutter_Homematic,20041','IPSModuleShutter_IPSShadowing,',),
			25978 => array('OnChange','IPSComponentShutter_Homematic,15228','IPSModuleShutter_IPSShadowing,',),
			21031 => array('OnChange','IPSComponentShutter_Homematic,35354','IPSModuleShutter_IPSShadowing,',),

			// Taster zur Ansteuerung der Markisen
			59542 /*InpS2*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSShadowing,29095,3',), /*Markise1 ausfahren*/
			37689 /*InpS6*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSShadowing,29095,9',), /*Markise1 einfahren*/
			56752 /*InpS3*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSShadowing,44927,3',), /*Markise2 ausfahren*/
			52590 /*InpS7*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSShadowing,44927,9',), /*Markise2 einfahren*/

			// Taster zur Ansteuerung der Terrassenbeleuchtung
			12456 /*InpS1*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,ToggleGroupByName,Aussen'), 
			37693 /*InpS5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,SetProgramNext,TerrasseProgram'), 
			57359 /*InpL1*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,ToggleSwitchByName,TerrassePergola'), 
			55574 /*InpL5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,ToggleSwitchByName,TerrasseGarten'), 

			// Taster zur Ansteuerung von Audio Terrasse
			56247 /*InpL2*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,ToggleMute,0'),     /*Muting*/
			31397 /*InpL6*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Switch'),              /*Source*/
			33116 /*InpL3*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Prev'),                /*Prev Titel*/
			48165 /*InpL7*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Next'),                /*Next Titel*/
			51018 /*InpS4*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,SetVolumeMinus,0'), /*Volume -*/
			44967 /*InpS8*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,SetVolumePlus,0'),  /*Volume +*/
			35556 /*InpL4*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,25854,false'),  /*PowerOff*/
			43222 /*InpL8*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,25854,true'),   /*PowerOn*/

			// Taster zur Ansteuerung von Audio Werkstatt
			57990 /*InpS3*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,22483,false'),  /*PowerOff*/
			29675 /*InpS7*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,22483,true'),   /*PowerOn*/
			57327 /*InpS4*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,SetVolumeMinus,2'), /*Volume -*/
			17306 /*InpS8*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,SetVolumePlus,2'),  /*Volume +*/
			33883 /*InpS5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Prev'),                /*Prev Titel*/
			43223 /*InpS9*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Next'),                /*Next Titel*/
			33457 /*InpS6*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,ToggleMute,2'),     /*Muting*/
			55566 /*InpS10*/=> array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Switch'),              /*Source*/
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
			'yamahareceiver.power'       => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetSourceByRoomId,46541,2'),
			'yamahareceiver.volumeplus'  => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetVolumeDiff,16243,5'),
			'yamahareceiver.volumeminus' => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetVolumeDiff,16243,-5'),
			'yamahareceiver.presetprev'  => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,','IPSModuleSensor_Netplayer,NetPlayer_Prev'),
			'yamahareceiver.presetnext'  => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,','IPSModuleSensor_Netplayer,NetPlayer_Next'),
			);
		return $config;
	}


/** @}*/
?>