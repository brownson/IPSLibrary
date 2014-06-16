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
			48380 => array('OnChange','IPSComponentSwitch_Homematic,32626','IPSModuleSwitch_IPSLight,',),
			39067 => array('OnChange','IPSComponentSwitch_Homematic,48611','IPSModuleSwitch_IPSLight,',),
			58479 => array('OnChange','IPSComponentSwitch_Homematic,17605','IPSModuleSwitch_IPSLight,',),
			43656 => array('OnChange','IPSComponentSwitch_Homematic,27179','IPSModuleSwitch_IPSLight,',),
			31355 => array('OnChange','IPSComponentSwitch_Homematic,24592','IPSModuleSwitch_IPSLight,',),
			13313 => array('OnChange','IPSComponentSwitch_Homematic,11022','IPSModuleSwitch_IPSLight,',),
			20923 => array('OnChange','IPSComponentSwitch_Homematic,21180','IPSModuleSwitch_IPSLight,',),
			34099 => array('OnChange','IPSComponentSwitch_Homematic,13545','IPSModuleSwitch_IPSLight,',),
			21829 => array('OnChange','IPSComponentSwitch_Homematic,51577','IPSModuleSwitch_IPSLight,',),
			24251 => array('OnChange','IPSComponentSwitch_Homematic,32432','IPSModuleSwitch_IPSLight,',),
			59698 => array('OnChange','IPSComponentSwitch_Homematic,52619','IPSModuleSwitch_IPSLight,',),
			44813 => array('OnChange','IPSComponentSwitch_Homematic,45579','IPSModuleSwitch_IPSLight,',),
			59385 => array('OnChange','IPSComponentSwitch_Homematic,18743','IPSModuleSwitch_IPSLight,',),
			23938 => array('OnChange','IPSComponentSwitch_Homematic,59085','IPSModuleSwitch_IPSLight,',),
			27759 => array('OnChange','IPSComponentSwitch_Homematic,19661','IPSModuleSwitch_IPSLight,',),
			40701 => array('OnChange','IPSComponentSwitch_Homematic,21631','IPSModuleSwitch_IPSLight,',),
			38623 => array('OnChange','IPSComponentSwitch_Homematic,40611','IPSModuleSwitch_IPSLight,',),
			38770 => array('OnChange','IPSComponentSwitch_Homematic,39307','IPSModuleSwitch_IPSLight,',),
			28061 => array('OnChange','IPSComponentSwitch_Homematic,43774','IPSModuleSwitch_IPSLight,',),
			45125 => array('OnChange','IPSComponentSwitch_Homematic,21540','IPSModuleSwitch_IPSLight,',),
			44303 => array('OnChange','IPSComponentSwitch_Homematic,28131','IPSModuleSwitch_IPSLight,',),
			47363 => array('OnChange','IPSComponentSwitch_Homematic,34233','IPSModuleSwitch_IPSLight,',),
			28929 => array('OnChange','IPSComponentSwitch_Homematic,35891','IPSModuleSwitch_IPSLight,',),
			45624 => array('OnChange','IPSComponentSwitch_Homematic,34391','IPSModuleSwitch_IPSLight,',),
			43699 => array('OnChange','IPSComponentSwitch_Homematic,58524','IPSModuleSwitch_IPSLight,',),
			45045 => array('OnChange','IPSComponentSwitch_Homematic,50936','IPSModuleSwitch_IPSLight,',),
			10699 => array('OnChange','IPSComponentSwitch_Homematic,50862','IPSModuleSwitch_IPSLight,',),
			53528 => array('OnChange','IPSComponentSwitch_Homematic,41458','IPSModuleSwitch_IPSLight,',),
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
			45699 /*InpL1*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,IPSLight_ToggleSwitchByName,Werkstatt'),  /*AllOff*/

			57990 /*InpS3*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,22483,false'),  /*PowerOff*/
			29675 /*InpS7*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,22483,true'),   /*PowerOn*/
			57327 /*InpS4*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,SetVolumeMinus,2'), /*Volume -*/
			17306 /*InpS8*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,SetVolumePlus,2'),  /*Volume +*/
			33883 /*InpS5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Prev'),                /*Prev Titel*/
			43223 /*InpS9*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Next'),                /*Next Titel*/
			33457 /*InpS6*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,ToggleMute,2'),     /*Muting*/
			55566 /*InpS10*/=> array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Netplayer,NetPlayer_Switch'),              /*Source*/

			29769 /*InpS1*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,Werkstatt2'),
			23427 /*InpS2*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,Werkstatt3'),
			17048 /*InpS3*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,22483,false'),  /*PowerOff*/
			25690 /*InpS4*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,22483,true'),  /*PowerOn*/
			57607 /*InpS5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,SetVolumeMinus,2'), /*Volume -*/
			20451 /*InpS6*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,AudioMax,SetVolumePlus,2'),  /*Volume +*/

			// Taster zur Ansteuerung der Terrassenbeleuchtung
			12456 /*InpS1*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleGroupByName,Aussen'),
			37693 /*InpS5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_SetProgramNextByName,TerrasseProgram'),
			57359 /*InpL1*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,TerrassePergola'),
			55574 /*InpL5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,TerrasseGarten'),

			// Taster zur Ansteuerung der Wellnessbeleuchtung
			10261 /*InpS1*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,WellnessSauna'),
			16855 /*InpS3*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,WellnessAmbiente'),
			40346 /*InpS4*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,WellnessDusche'),
			15719 /*InpS5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_DimRelativByName,WellnessDecke,-20'),
			37995 /*InpS6*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_DimRelativByName,WellnessDecke,+20'),
			17936 /*InpL5*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,WellnessDecke'),
			59657 /*InpL6*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,WellnessDecke'),
			47480 /*InpS7*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_DimRelativByName,WellnessWand,-20'),
			45476 /*InpS8*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_DimRelativByName,WellnessWand,+20'),
			34866 /*InpL7*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,WellnessWand'),
			45476 /*InpL8*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,WellnessWand'),
			26967 /*InpS9*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleGroupByName,Wellness'),
			21469 /*InpS13*/=> array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleGroupByName,Wellness'),
			22671 /*InpS14*/=> array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_SetProgramNextByName,WellnessProgram'),
			49275 /*InpL13*/=> array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,20231,true'),  /*PowerOn*/
			29320 /*InpL14*/=> array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetRoomPowerByRoomId,20231,false'),  /*PowerOff*/

			//59426 /*InpS2*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,IPSLight_ToggleSwitchByName,WellnessSauna'),
			//21493 /*InpS10*/ => array('OnUpdate','IPSComponentSensor_Button','IPSModuleSensor_IPSLight,ToggleSwitchByName,???'),
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
			'sauna.power'                => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetSourceByRoomId,40058,1'),
			//'sauna.mute'                 => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_ToggleMuting,23064'),
			'sauna.volumeplus'           => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetVolumeDiff,28925,5'),
			'sauna.volumeminus'          => array('IPSComponentSensor_Button','IPSModuleSensor_Entertainment,Entertainment_SetVolumeDiff,28925,-5'),
			);
		return $config;
	}


/** @}*/
?>