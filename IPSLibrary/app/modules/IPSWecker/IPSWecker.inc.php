<?
	/*
	 * This file is part of the IPSLibrary.
	 *
	 * The IPSLibrary is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published
	 * by the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * The IPSLibrary is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
	 */

	/**@defgroup IPSWecker IPSWecker
	 * @ingroup modules
	 * @{
	 *
	 * IPSWecker ist ein IPS Modul, das
	 *
	 * @file          IPSWecker.inc.php
	 * @author        André Czwalina
	 * @version
	 * Version 1.00.0, 01.04.2012<br/>
	 *
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                  	"IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSInstaller.inc.php",               	"IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSComponent.class.php",             	"IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSWecker_Constants.inc.php",      	"IPSLibrary::app::modules::IPSWecker");
	IPSUtils_Include ("IPSWecker_Configuration.inc.php",  	"IPSLibrary::config::modules::IPSWecker");
	IPSUtils_Include ("IPSWecker_Custom.inc.php",         	"IPSLibrary::config::modules::IPSWecker");
	IPSUtils_Include ("IPSWecker_Logging.inc.php",        	"IPSLibrary::app::modules::IPSWecker");
	IPSUtils_Include ("IPSWecker_IDs.inc.php",            	"IPSLibrary::app::modules::IPSWecker");


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_TimerEvents($parentId, $CircleId){
		$ConfId 				= get_ControlId(c_Control_Optionen, $CircleId);
		$WeckerName			= IPS_GetName($CircleId);
		$WeckerConfig      = get_WeckerConfiguration();

		$ConfId 					= get_ControlId(c_Control_Optionen, 		$CircleId);
		$objectIds 				= explode(',',GetValue($ConfId));
		$CircleIdent      	= IPS_GetName($CircleId);
		$ParamsFrostTime 	 = $WeckerConfig[$CircleIdent][c_Property_FrostTime];

		$wecker_feiertag 		= $objectIds[7];
		$wecker_urlaub 		= $objectIds[8];
		$wecker_frost 			= $objectIds[9];
		$wecker_aktiv_all 	= $objectIds[10];
		$wecker_snooze 		= $objectIds[11];
		$wecker_end 			= $objectIds[12];
		$wecker_urlaubszeit 	= get_ControlValue(c_Control_Urlaubszeit,	$parentId);
		$VorTag = "--:--";
		$FrostTime = 0;

		deaktivate_AllTimerEvents($WeckerName);

		for ($tag = 0; $tag < 7; $tag++){
			if ($tag == 0) $wecker_tag = c_Control_Mo;
			if ($tag == 1) $wecker_tag = c_Control_Di;
			if ($tag == 2) $wecker_tag = c_Control_Mi;
			if ($tag == 3) $wecker_tag = c_Control_Do;
			if ($tag == 4) $wecker_tag = c_Control_Fr;
			if ($tag == 5) $wecker_tag = c_Control_Sa;
			if ($tag == 6) $wecker_tag = c_Control_So;

			$wecker_aktiv 		= $objectIds[$tag];
			$wecker_zeit	= get_ControlValue($wecker_tag, 	$CircleId);

			if ((($wecker_aktiv) or ($wecker_urlaub) or ($wecker_feiertag))  and ($wecker_aktiv_all)){
					if (($wecker_frost) and ($WeckerConfig[$CircleIdent][c_Property_FrostSensor] !=='')) $FrostTime = $ParamsFrostTime;
					set_NextTimerEvent($WeckerName, $wecker_zeit, $FrostTime);
			}
		}
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_NextTimerEvent($WeckerName, $wecker_zeit, $FrostTime){

			$Toleranz =2;        //Toleranzzeit in Sekunden. Timerevent auslesen hat zum Teil schwankun von 1 Sekunden. Rundungsfehler?
			$Hour = substr($wecker_zeit,0,2);
			$Minute =substr($wecker_zeit,3,2);
			$zeit = mktime($Hour, $Minute, 0)-($FrostTime*60);

		for ($i = 0; $i < 7; $i++){
			$TimerId = IPS_GetEventIDByName($WeckerName."_".$i, WECKER_ID_TIMER);
			$eventtime=IPS_GetEvent($TimerId)['CyclicTimeFrom'];
			$eventaktiv=IPS_GetEvent($TimerId)['EventActive'];

			if ((($zeit-$Toleranz) < $eventtime) and (($zeit+$Toleranz) > $eventtime) and  ($eventaktiv == true )){
				break;
			}
			elseif ($eventaktiv == false ){
				if (!IPS_SetEventCyclicTimeBounds($TimerId, $zeit, 0)) {
					Error ("IPS_SetEventCyclicTimeBounds $TimerId $zeit failed !!!");
				}
				if (!IPS_SetEventActive($TimerId, true)){
					Error ("IPS_SetEventActive $TimerId true failed !!!");
				}
				break;
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function deaktivate_AllTimerEvents($WeckerName){

		for ($i = 0; $i < 7; $i++){
			$TimerId = IPS_GetEventIDByName($WeckerName."_".$i, WECKER_ID_TIMER);
			if (!IPS_SetEventActive($TimerId, false)){
				Error ("IPS_SetEventActive $TimerId false failed !!!");
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_ConfigGlobal($parentId, $CircleId){
		$ConfId 				= get_ControlId(c_Control_Optionen, $CircleId);
		$GlobalValue 		= get_ControlValue(c_Control_Global, 		$parentId);
		$FeiertagValue 	= get_ControlValue(c_Control_Feiertag, 	$parentId);
		$FrostValue 		= get_ControlValue(c_Control_Frost, 		$parentId);
		$UrlaubValue 		= get_ControlValue(c_Control_Urlaub, 		$parentId);
		$SchlummerValue 	= get_ControlValue(c_Control_Schlummer, 	$parentId);
		$EndValue 			= get_ControlValue(c_Control_End, 			$parentId);

		$objectIds = explode(',',GetValue($ConfId));

		$objectIds[7] = ((bool)$FeiertagValue? "1": "0");
		$objectIds[8] = ((bool)$UrlaubValue? "1": "0");
		$objectIds[9] = ((bool)$FrostValue? "1": "0");
		$objectIds[10] = ((bool)$GlobalValue? "1": "0");
		$objectIds[11] = ((bool)$SchlummerValue? "1": "0");
		$objectIds[12] = ((bool)$EndValue? "1": "0");

		SetValue($ConfId, implode(",", $objectIds));
		set_Overview($parentId, $CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_ConfigActive($parentId, $CircleId){
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);
		$ActiveValue 		= get_ControlValue(c_Control_Active,		$parentId);

		$DayId = get_ControlId(c_Control_Tag, $parentId);
		$objectIds = explode(',',GetValue($ConfId));

		   switch (GetValueFormatted ($DayId)){
			case c_Program_Montag:
					$objectIds[0] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Dienstag:
					$objectIds[1] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Mittwoch:
					$objectIds[2] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Donnerstag:
					$objectIds[3] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Freitag:
					$objectIds[4] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Samstag:
					$objectIds[5] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Sonntag:
					$objectIds[6] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Werkstags:
					$objectIds[0] = ((bool)$ActiveValue? "1": "0");
					$objectIds[1] = ((bool)$ActiveValue? "1": "0");
					$objectIds[2] = ((bool)$ActiveValue? "1": "0");
					$objectIds[3] = ((bool)$ActiveValue? "1": "0");
					$objectIds[4] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Wochenende:
					$objectIds[5] = ((bool)$ActiveValue? "1": "0");
					$objectIds[6] = ((bool)$ActiveValue? "1": "0");
			  break;
			case c_Program_Woche:
					$objectIds[0] = ((bool)$ActiveValue? "1": "0");
					$objectIds[1] = ((bool)$ActiveValue? "1": "0");
					$objectIds[2] = ((bool)$ActiveValue? "1": "0");
					$objectIds[3] = ((bool)$ActiveValue? "1": "0");
					$objectIds[4] = ((bool)$ActiveValue? "1": "0");
					$objectIds[5] = ((bool)$ActiveValue? "1": "0");
					$objectIds[6] = ((bool)$ActiveValue? "1": "0");
			  break;
		default:
			Print "FEHLER (".GetValueFormatted ($ControlId).") ";
			break;
    	 	}

		SetValue($ConfId, implode(",", $objectIds));
		set_Overview($parentId, $CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_Config($parentId, $CircleId){
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);
		$ControlType = get_ControlType($CircleId);
//		IPSWecker_Log('Config lesen für Wecker: '.$ControlType);

		$DayId = get_ControlId(c_Control_Tag, $parentId);
		$objectIds = explode(',',GetValue($ConfId));
		   switch (GetValueFormatted ($DayId)){
			case c_Program_Montag:
					set_ControlValue(c_Control_Active, 	$parentId, $objectIds[0]);
			  break;
			case c_Program_Dienstag:
					set_ControlValue(c_Control_Active, 	$parentId, $objectIds[1]);
			  break;
			case c_Program_Mittwoch:
					set_ControlValue(c_Control_Active, 	$parentId, $objectIds[2]);
			  break;
			case c_Program_Donnerstag:
					set_ControlValue(c_Control_Active, 	$parentId, $objectIds[3]);
			  break;
			case c_Program_Freitag:
					set_ControlValue(c_Control_Active, 	$parentId, $objectIds[4]);
			  break;
			case c_Program_Samstag:
					set_ControlValue(c_Control_Active, 	$parentId, $objectIds[5]);
			  break;
			case c_Program_Sonntag:
					set_ControlValue(c_Control_Active, 	$parentId, $objectIds[6]);
			  break;
			case c_Program_Werkstags:
			  break;
			case c_Program_Wochenende:
			  break;
			case c_Program_Woche:
			  break;
		default:
			Print "FEHLER (".GetValueFormatted ($ControlId).") ";
			break;
    	 	}

		set_ControlValue(c_Control_Feiertag, 	$parentId, $objectIds[7]);
		set_ControlValue(c_Control_Urlaub, 		$parentId, $objectIds[8]);
		set_ControlValue(c_Control_Frost, 		$parentId, $objectIds[9]);
		set_ControlValue(c_Control_Global, 		$parentId, $objectIds[10]);
		set_ControlValue(c_Control_Schlummer, 	$parentId, $objectIds[11]);
		set_ControlValue(c_Control_End, 			$parentId, $objectIds[12]);

		$UeberValue	= get_ControlValue(c_Control_Uebersicht, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);

	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeWecker($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);
		$ControlType 	= get_ControlType($ControlId);
      $ControlValue 	= GetValue($ControlId);

		$tagl 	= GetValueFormatted(get_ControlId(c_Control_Tag, $parentId));
		if (($tagl==c_Program_Werkstags) or ($tagl==c_Program_Woche) or ($tagl==c_Program_Wochenende)){
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Montag,"", -1);
		}
      set_SchichtAssociation($parentId, $CircleId, $Value);
		get_Config($parentId, $CircleId);
		get_AlarmClock($parentId, $CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_SchichtAssociation($parentId, $CircleId, $Value) {
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$WeckerConfig  = get_WeckerConfiguration();

		$CircleItem    = IPS_GetName($CircleId);
		$Propertyschicht 		= $WeckerConfig[$CircleItem][c_Property_Schichtgruppe];

		$Ass=0;
		foreach ($WeckerConfig as $WeckerName=>$WeckerData) {
			$WeckerCf 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.($Ass+1), WECKER_ID_WECKZEITEN);
			$ConfId 			= get_ControlId(c_Control_Optionen, $WeckerCf);
			$objectIds 		= explode(',',GetValue($ConfId));

			if ($Propertyschicht <> ''){
				if ($Propertyschicht == $WeckerData[c_Property_Schichtgruppe]) {
					if ($Value == $Ass) {
						$objectIds[10] = "1";
					} else {
						$objectIds[10] = "0";
					}
					SetValue($ConfId, implode(",", $objectIds));
					$UeberValue = set_Overview($parentId, $WeckerCf);
					set_ControlValue(c_Control_Uebersicht, $parentId, $UeberValue);
				}
			}

			if ($objectIds[10]) {
			  IPS_SetVariableProfileAssociation('IPSWecker_Name', $Ass, $WeckerData[c_Property_Name],"", 0x00FF00);
			} else {
			  IPS_SetVariableProfileAssociation('IPSWecker_Name', $Ass, $WeckerData[c_Property_Name],"", 0xFF0000);
			}
			$Ass++;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_WeckerAssociation($parentId, $CircleId) {
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$WeckerConfig  = get_WeckerConfiguration();

		$Ass=1;
		foreach ($WeckerConfig as $WeckerName=>$WeckerData) {
			$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$Ass, WECKER_ID_WECKZEITEN);
			$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);
			$objectIds 		= explode(',',GetValue($ConfId));
			if ($objectIds[10]) {
			  IPS_SetVariableProfileAssociation('IPSWecker_Name', $Ass-1, $WeckerData[c_Property_Name],"", 0x00FF00);
			} else {
			  IPS_SetVariableProfileAssociation('IPSWecker_Name', $Ass-1, $WeckerData[c_Property_Name],"", 0xFF0000);
			}
			$Ass++;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_AlarmClock($parentId, $CircleId) {
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);


		$tagl 	= GetValueFormatted(get_ControlId(c_Control_Tag, $parentId));
		if (($tagl !== c_Program_Werkstags) and ($tagl !== c_Program_Woche) and ($tagl !== c_Program_Wochenende)){
				$wecker_tag 	= GetValueFormatted(get_ControlId(c_Control_Tag, $parentId));
				$wecker_zeit	= get_ControlValue($wecker_tag, 	$CircleId);
				$Hour = substr($wecker_zeit,0,2);
				$Minute =substr($wecker_zeit,3,2);
				set_ControlValue(c_Control_Stunde, 	$parentId, $Hour);
				set_ControlValue(c_Control_Minute, 	$parentId, $Minute);
		}
	}
	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeDay($ControlId, $Value) {
		$parentId 		= get_CirclyIdByControlId($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);
		$ControlValue = GetValue($ControlId);

		switch($Value){
		case -1:
		   switch (GetValueFormatted ($ControlId)){
			case c_Program_Montag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Woche,"", -1);
			  break;
			case c_Program_Dienstag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Montag,"", -1);
			  break;
			case c_Program_Mittwoch:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Dienstag,"", -1);
			  break;
			case c_Program_Donnerstag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Mittwoch,"", -1);
			  break;
			case c_Program_Freitag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Donnerstag,"", -1);
			  break;
			case c_Program_Samstag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Freitag,"", -1);
			  break;
			case c_Program_Sonntag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Samstag,"", -1);
			  break;
			case c_Program_Werkstags:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Sonntag,"", -1);
			  break;
			case c_Program_Wochenende:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Werkstags,"", -1);
			  break;
			case c_Program_Woche:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Wochenende,"", -1);
			  break;
			default:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Montag,"", -1);
			   break;
    	 	}
			break;
		case 100:
		   switch (GetValueFormatted($ControlId)){
			case c_Program_Montag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Dienstag,"", -1);
			  break;
			case c_Program_Dienstag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Mittwoch,"", -1);
			  break;
			case c_Program_Mittwoch:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Donnerstag,"", -1);
			  break;
			case c_Program_Donnerstag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Freitag,"", -1);
			  break;
			case c_Program_Freitag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Samstag,"", -1);
			  break;
			case c_Program_Samstag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Sonntag,"", -1);
			  break;
			case c_Program_Sonntag:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Werkstags,"", -1);
			  break;
			case c_Program_Werkstags:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Wochenende,"", -1);
			  break;
			case c_Program_Wochenende:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Woche,"", -1);
			  break;
			case c_Program_Woche:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Montag,"", -1);
			  break;
			default:
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Montag,"", -1);
			   break;
    	 	}
			break;
		}
		get_Config($parentId, $CircleId);
		get_AlarmClock($parentId, $CircleId);
//      $null = IPSWecker_Log('Tag gewechselt '.GetValueFormatted($ControlId));
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeStunde($ControlId, $Value) {
	   $parentId 		= get_CirclyIdByControlId($ControlId);
//		$CircleIdent   = IPS_GetName($parentId);
      $ControlValue 	= GetValueInteger($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);

		switch($Value){
		case -2:
			SetValueInteger ($ControlId,-2);
			break;

		case -1:
			$ControlValue = $ControlValue -1;
			if ($ControlValue <0) $ControlValue = 23;
			SetValueInteger ($ControlId,$ControlValue);
			break;

		case 100:
			$ControlValue = $ControlValue +1;
			if ($ControlValue <0) $ControlValue = 0;
			if ($ControlValue >23) $ControlValue = 0;
			SetValueInteger ($ControlId,$ControlValue);
			break;
		}


		set_NewTimeConfig($parentId, $CircleId);
		$UeberValue	= set_Overview($parentId, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);
		set_TimerEvents($parentId, $CircleId);


	}
	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeMinute($ControlId, $Value) {
	   $parentId 		= get_CirclyIdByControlId($ControlId);
//		$CircleIdent   = IPS_GetName($parentId);
      $ControlValue 	= GetValueInteger($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);

		switch($Value){
		case -1:
			$ControlValue = $ControlValue -5;
			if ($ControlValue <0) $ControlValue = 55;
			SetValueInteger ($ControlId,$ControlValue);
			break;
		case 100:
			$ControlValue = $ControlValue +5;
			if ($ControlValue >55) $ControlValue = 0;
			SetValueInteger ($ControlId,$ControlValue);
			break;
		}
		set_NewTimeConfig($parentId, $CircleId);
		$UeberValue	= set_Overview($parentId, $CircleId);
//		$UeberValue	= get_ControlValue(c_Control_Uebersicht, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);
		set_TimerEvents($parentId, $CircleId);

	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_NewTimeConfig($ControlId, $CircleId){
		$Name = get_CirclyNameByID($CircleId);

		$tagl 	= GetValueFormatted(get_ControlId(c_Control_Tag, $ControlId));
		if ($tagl==c_Program_Werkstags){
	      $zeitId 	= get_ControlId(c_Control_Mo,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Di,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Mi,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Do,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Fr,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
		}
		elseif ($tagl==c_Program_Wochenende){
	      $zeitId 	= get_ControlId(c_Control_Sa,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_So,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
		}
		elseif ($tagl==c_Program_Woche) {
	      $zeitId 	= get_ControlId(c_Control_Mo,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Di,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Mi,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Do,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Fr,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_Sa,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
	      $zeitId 	= get_ControlId(c_Control_So,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
		}
		else{
	      $zeitId 	= get_ControlId($tagl,$CircleId);
			set_TimeStamp($zeitId, $ControlId);
		}
//      $null = IPSWecker_Log('Neue Weckzeit gespeichert');
      return true;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_TimeStamp($zeitId, $ControlId){
      $stunde  = Getvalue(get_ControlId(c_Control_Stunde, $ControlId));
		$minute  = Getvalue(get_ControlId(c_Control_Minute, $ControlId));
      SetValue($zeitId,sprintf("%02s", $stunde).":".sprintf("%02s", $minute));
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeActive($ControlId, $Value) {
		SetValueBoolean ($ControlId,$Value);

		$parentId = get_CirclyIdByControlId($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);


		set_ConfigActive($parentId, $CircleId);
		set_TimerEvents($parentId, $CircleId);

		$UeberValue	= get_ControlValue(c_Control_Uebersicht, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeBoolean($ControlId, $Value) {
		SetValueBoolean ($ControlId,$Value);

		$parentId = get_CirclyIdByControlId($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);


		set_ConfigGlobal($parentId, $CircleId);
		set_TimerEvents($parentId, $CircleId);

		$UeberValue	= get_ControlValue(c_Control_Uebersicht, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeGlobal($ControlId, $Value) {
		SetValueBoolean ($ControlId,$Value);

		$parentId = get_CirclyIdByControlId($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);


		set_ConfigGlobal($parentId, $CircleId);
		set_TimerEvents($parentId, $CircleId);

		$UeberValue	= get_ControlValue(c_Control_Uebersicht, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);
		set_WeckerAssociation($parentId, $CircleId);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeInteger($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $CircleId = get_CirclyIdByControlId($ControlId);
      $ControlValue = GetValue($ControlId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeUrlaubszeit($ControlId, $Value) {
		SetValue ($ControlId,$Value);

		$parentId      = get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.'_'.$NameValue, WECKER_ID_WECKZEITEN);


		set_ControlValue(c_Control_Urlaubszeit, $CircleId, $Value);
		set_Overview($parentId, $CircleId);
		$UeberValue	= get_ControlValue(c_Control_Uebersicht, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);

	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ActivateWecker($CycleId, $Value, $Mode) {
		$WeckerConfig      = get_WeckerConfiguration();
		$CircleIdent      = IPS_GetName($CycleId);
		$ComponentParams  = $WeckerConfig[$CircleIdent][c_Property_Component];

		if (!IPSWecker_BeforeActivateWecker($CycleId, $Value, $Mode)) {
			return false;
		}

		$component = IPSComponent::CreateObjectByParams($ComponentParams);
		$component->SetState($Value);

		IPSWecker_AfterActivateWecker($CycleId, $Value, $Mode);

		return true;
	}



	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_SetActive($ControlId, $Value, $Mode) {
	   $CircleId = get_CirclyIdByControlId($ControlId);
	   if (GetValue($ControlId) <> $Value) {
			if (IPSWecker_ActivateWecker($CircleId, $Value, $Mode)) {
				SetValue($ControlId, $Value);
				if ($Value) {
					IPSWecker_SetMode($CircleId, $Mode);
					SetValue(get_ControlId(c_Control_LastDate, $CircleId), date(c_Format_LastDate));
					SetValue(get_ControlId(c_Control_LastTime, $CircleId), date(c_Format_LastTime));
				} else {
				   IPSWecker_CalcNextScheduleDateTime($CircleId);
				}
				IPSWecker_LogActivate($CircleId, $Value, $Mode);
			} else {
			   IPSWecker_CalcNextScheduleDateTime($CircleId);
			}
			IPSWecker_ActivateRefreshTimer($Value);
		}
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_CalcNextScheduleDateTime($CircleId) {
		$Automatic = GetValue(get_ControlId(c_Control_Automatic, $CircleId));
		SetValue(get_ControlId(c_Control_NextTime, $CircleId), GetValue(get_ControlId(c_Control_StartTime, $CircleId)));
		SetValue(get_ControlId(c_Control_NextDate, $CircleId), get_NextScheduledDate($CircleId));

		if (get_NextScheduledDate($CircleId)===false) {
			IPSWecker_SetMode($CircleId, c_Mode_AutomaticManual);
			IPSWecker_ActivateStartTimer($CircleId, false);
		} else if ($Automatic) {
			IPSWecker_SetMode($CircleId, c_Mode_AutomaticEnabled);
			IPSWecker_ActivateStartTimer($CircleId, true);
		} else {
			IPSWecker_SetMode($CircleId, c_Mode_AutomaticDisabled);
			IPSWecker_ActivateStartTimer($CircleId, false);
		}
		//IPSWecker_ActivateStartTimer($CircleId, $Automatic);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function	IPSWecker_ActivateStartTimer($CircleId, $Value=true) {
		$Name           = IPS_GetName($CircleId);
		$scriptId_Timer = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSWecker.IPSWecker_ActivationTimer');
		$TimerId        = @IPS_GetEventIDByName($Name, $scriptId_Timer);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetName($TimerId, $Name);
			IPS_SetParent($TimerId, $scriptId_Timer);
			if (!IPS_SetEventCyclic($TimerId, 1 /*Once*/, 1,0,0,0,0)) {
				IPSLogger_Err(__file__, "IPS_SetEventCyclic failed for WeckerCircle '$Name' Timer !!!");
				exit;
			}
		}
		if ($Value) {
			if (!IPS_SetEventCyclicDateBounds ($TimerId, get_DateTime(get_ControlId(c_Control_NextDate, $CircleId), c_Format_NextDate),0)) {
				IPSLogger_Err(__file__, "IPS_SetEventCyclicTimeBounds failed for WeckerCircle '$Name' Timer !!!");
				exit;
			}
			$Time = GetValue(get_ControlId(c_Control_NextTime, $CircleId));
			if (!IPS_SetEventCyclicTimeBounds($TimerId, mktime(substr($Time,0,2), substr($Time,3,2), 0), 0)) {
				IPSLogger_Err(__file__, "IPS_SetEventCyclicTimeBounds failed for WeckerCircle '$Name' Timer !!!");
				exit;
			}
		}
		IPS_SetEventActive($TimerId, $Value);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function	IPSWecker_ActivateRefreshTimer($Value) {
		$Name    = 'Refresh';
		$scriptId_Timer = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSWecker.IPSWecker_RefreshTimer');
		$TimerId = @IPS_GetEventIDByName($Name, $scriptId_Timer);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetName($TimerId, $Name);
			IPS_SetParent($TimerId, $scriptId_Timer);
			if (!IPS_SetEventCyclic($TimerId, 2 /*Daily*/, 1 /*Int*/,0 /*Days*/,0/*DayInt*/,1/*TimeType Sec*/,1/*Sec*/)) {
				IPSLogger_Err(__file__, "IPS_SetEventCyclic failed for Refresh Timer!!!");
				exit;
			}
		}

		if ($Value) {
			IPS_SetEventActive($TimerId, true);
		} else {
			$OneOrMoreCirclesActive = false;
			$categoryId_Circles = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker.WeckerCircles');
			$CircleIds          = IPS_GetChildrenIds($categoryId_Circles);
				foreach($CircleIds as $CircleId) {
				$OneOrMoreCirclesActive = ($OneOrMoreCirclesActive or GetValue(get_ControlId(c_Control_Optionen, $CircleId)));
			}
			if (!$OneOrMoreCirclesActive) {
				IPS_SetEventActive($TimerId, false);
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_SetMode($CircleId, $Mode) {
	   $ControlIdNextDisplay = get_ControlId(c_Control_NextDisplay, $CircleId);
	   $ControlIdDuration    = get_ControlId(c_Control_Duration, $CircleId);
	   $ControlIdToBeDone    = get_ControlId(c_Control_ToBeDone, $CircleId);

		switch ($Mode) {
		   case c_Mode_StartAutomatic:
		      SetValue($ControlIdNextDisplay, $Mode);
				SetValue($ControlIdToBeDone, $Mode.', '.GetValue($ControlIdDuration).' Minuten');
		      break;
		   case c_Mode_StartManual:
		      SetValue($ControlIdNextDisplay, $Mode);
				SetValue($ControlIdToBeDone, $Mode.', '.GetValue($ControlIdDuration).' Minuten');
		      break;
		   case c_Mode_AutomaticManual:
		   case c_Mode_AutomaticDisabled:
		      SetValue($ControlIdNextDisplay, $Mode);
				SetValue($ControlIdToBeDone, $Mode);
		      break;
		   case c_Mode_AutomaticEnabled:
		      $Date = get_DateTime(get_ControlId(c_Control_NextDate, $CircleId), c_Format_NextDate);
		      $Time = GetValue(get_ControlId(c_Control_NextTime, $CircleId));
		      SetValue($ControlIdNextDisplay, get_DateTranslation(date('l', $Date)).', '.$Time);
				SetValue($ControlIdToBeDone, get_DateTranslation(date('l', $Date)).', '.$Time.', '.GetValue($ControlIdDuration).' Minuten');
		      break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
   function IPSWecker_SetAutomaticForAllCircles($Value) {
		$categoryId_Circles = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker.WeckerCircles');
		$CircleIds          = IPS_GetChildrenIds($categoryId_Circles);
		foreach($CircleIds as $CircleId) {
			IPSWecker_SetValue(get_ControlId(c_Control_Automatic, $CircleId), $Value);
		}
   }

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_SetValue($ControlId, $Value) {
		$CircleId = get_CirclyIdByControlId($ControlId);
		if (GetValue($ControlId)<>$Value) {
			IPSWecker_SetActive(get_ControlId(c_Control_Optionen, $CircleId), false, c_Mode_StartManual);

			SetValue($ControlId, $Value);
			IPSWecker_CalcNextScheduleDateTime($CircleId);
			IPSWecker_LogChange($CircleId, $Value, $ControlId);
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_Refresh() {
		$categoryId_Circles = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker.WeckerCircles');
		$CircleIds          = IPS_GetChildrenIds($categoryId_Circles);
		foreach($CircleIds as $CircleId) {
			if (GetValue(get_ControlId(c_Control_Active, $CircleId))) {
				$Duration        = GetValue(get_ControlId(c_Control_Duration, $CircleId));
				$TimeCurrent     = time();
				$TimeStart       = get_DateTime(get_ControlId(c_Control_LastDate, $CircleId), c_Format_LastDate,
				                                get_ControlId(c_Control_LastTime, $CircleId), c_Format_LastTime);
				$TimeDiff        = $TimeCurrent-$TimeStart;
				$TimeDiffMinutes = floor($TimeDiff/60);
				$TimeDiffSeconds = $TimeDiff % 60;

				SetValue(get_ControlId(c_Control_ToBeDone, $CircleId),
				         GetValue(get_ControlId(c_Control_NextDisplay, $CircleId)).", $TimeDiffMinutes von $Duration Min ($TimeDiffSeconds Sek)");

				if ($TimeDiffMinutes >= $Duration) {
					IPSWecker_SetActive(get_ControlId(c_Control_Active, $CircleId),
												 false,
												 c_Mode_StartAutomatic);
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_DateTime($DateControlId, $DateFormat='Y.m.d', $TimeControlId=null, $TimeFormat='H:i') {
		$Value  = GetValue($DateControlId);
		$Format = $DateFormat;
		if ($TimeControlId<>null) {
			$Value  .= GetValue($TimeControlId);
			$Format .= $TimeFormat;
			}

		try {
			$datetime = DateTime::createFromFormat($Format, $Value);
			if ($datetime <> null) {
				return $datetime->getTimestamp();
			} else {
				IPSLogger_Err(__file__, "'$Value' could NOT be converted to DateTime using '$Format'");
				Exit;
			}
		} catch (Exception $exception) {
			IPSLogger_Err(__file__, "'$exception': '$Value' could NOT be converted to DateTime using '$Format'");
			Exit;
		}
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function get_ControlType($ControlId) {
		$ControlName = IPS_GetName($ControlId);
		return $ControlName;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyIdByCircleIdent($CircleIdent, $ParentId=null) {
		if ($ParentId==null) {
			$ParentId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker');
		}
		$CirclyId = IPS_GetCategoryIDByName($CircleIdent, $ParentId);
		return $CirclyId;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyNameByID($CircleId) {
	   $CircleItem       = IPS_GetName($CircleId);
		$WeckerConfig      = get_WeckerConfiguration();
		return $WeckerConfig[$CircleItem][c_Property_Name];
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyIdByControlId($ControlId) {
		$CirclyId = IPS_GetParent($ControlId);
		if ($CirclyId === false) {
			IPSLogger_Err(__file__, "CircleId could NOT be found for ControlId=$ControlId");
			exit;
		}
		return $CirclyId;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_ControlId($ControlName, $CircleId) {
		$VariableId = IPS_GetVariableIDByName($ControlName, $CircleId);
		if ($VariableId === false) {
			IPSLogger_Err(__file__, "Control '$ControlName' could NOT be found for CircleId=$CircleId");
			exit;
		}
		return $VariableId;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_ControlValue($ControlName, $CircleId) {
		$VariableId = IPS_GetVariableIDByName($ControlName, $CircleId);
		if ($VariableId === false) {
			IPSLogger_Err(__file__, "Control '$ControlName' could NOT be found for CircleId=$CircleId");
			exit;
		}
		$VariableValue = GetValue ($VariableId);

		return $VariableValue;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function	set_ControlValue($ControlName, $CircleId, $Value){
		$VariableId = IPS_GetVariableIDByName($ControlName, $CircleId);
		if ($VariableId === false) {
			IPSLogger_Err(__file__, "Control '$ControlName' could NOT be found for CircleId=$CircleId");
			exit;
		}
		SetValue ($VariableId, $Value);
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_Overview($parentId, $CircleId){

		$html ="<style type='text/css'>\n";
		$html.="		table.stundenplan { width: 100%; border-collapse: true;}\n";
		$html.="		table.stundenplan td { border: 1px solid #444455; }\n";
		$html.="</style>\n";

		$html.="<table class='stundenplan'>\n\n";
		$html.="	<tr>";
		$html.="		<td style='width: 50px;' align='center'>".c_Table_Day."</td>\n";
		$html.="		<td style='width: 90px;' align='center'>".c_Table_Hour."</td>\n";
		$html.="		<td style='width: 200px;' align='center'>".c_Table_Active."</td>\n";
		$html.="		<td style='width: 100px; border: 0px;' align='center'>      </td>\n";
		$html.="		<td style='width: 200px;' align='center'>".c_Table_Feature."</td>\n";
		$html.="		<td style='width: 200px;' align='center'>".c_Table_Active."</td>\n";
		$html.="		<td style='border: 0px;' align='center'></td>\n";

		$ConfId 					= get_ControlId(c_Control_Optionen, $CircleId);
		$objectIds 				= explode(',',GetValue($ConfId));

		$wecker_feiertag 		= $objectIds[7];
		$wecker_urlaub 		= $objectIds[8];
		$wecker_frost	 		= $objectIds[9];
		$wecker_aktiv_all 	= $objectIds[10];
		$wecker_snooze 	   = $objectIds[11];
		$wecker_end 			= $objectIds[12];
		$wecker_name 			= get_CirclyNameByID($CircleId);
		$wecker_urlaubszeit 	= get_ControlValue(c_Control_Urlaubszeit,	$CircleId);

		if($wecker_aktiv_all == 0)	{
			$wecker_global = c_Asso_Off;
			$farbe_global = "		<td style='background: #880000;' colspan='1' align='center'>";
		}
		else {
			$wecker_global = c_Asso_On;
			$farbe_global = "		<td style='background: #008000;' colspan='1' align='center'>";
		}

		if($wecker_feiertag  == 0)	{
			$wecker_feiertag = c_Asso_NoWeck;
			if($wecker_aktiv_all) 	$farbe_feiertag = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_feiertag = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else	{
			$wecker_feiertag = c_Asso_Weck;
			if($wecker_aktiv_all) 	$farbe_feiertag = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_feiertag = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($wecker_urlaub == 0)	{
			$wecker_urlaub = c_Asso_NoWeck;
			if($wecker_aktiv_all) 	$farbe_urlaub = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_urlaub = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$wecker_urlaub = c_Asso_Weck;
			if($wecker_aktiv_all) 	$farbe_urlaub = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all) 	$farbe_urlaub = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($wecker_frost == 0)	{
			$wecker_frost = c_Asso_NormWeck;
			if($wecker_aktiv_all) 	$farbe_frost = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_frost = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$wecker_frost = c_Asso_PrevWeck;
			if($wecker_aktiv_all) 	$farbe_frost = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_frost = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($wecker_snooze == 0){
			$wecker_snooze = c_Asso_Off;
			if($wecker_aktiv_all) 	$farbe_schlummer = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_schlummer = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$wecker_snooze = c_Asso_On;
			if($wecker_aktiv_all) 	$farbe_schlummer = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_schlummer = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($wecker_end == 0)	{
			$wecker_end = c_Asso_Off;
			if($wecker_aktiv_all) 	$farbe_end = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_end = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$wecker_end = c_Asso_On;
			if($wecker_aktiv_all) 	$farbe_end = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_end = "		<td style='background: #002000;' colspan='1' align='center'>";
		}



		for ($tag = 0; $tag < 7; $tag++){
			if ($tag == 0) $wecker_tag = c_Control_Mo;
			if ($tag == 1) $wecker_tag = c_Control_Di;
			if ($tag == 2) $wecker_tag = c_Control_Mi;
			if ($tag == 3) $wecker_tag = c_Control_Do;
			if ($tag == 4) $wecker_tag = c_Control_Fr;
			if ($tag == 5) $wecker_tag = c_Control_Sa;
			if ($tag == 6) $wecker_tag = c_Control_So;


			$wecker_zeit	= get_ControlValue($wecker_tag, 		$CircleId);
			$wecker_aktiv 		= $objectIds[$tag];


			if($wecker_aktiv 		== 0) {
				$wecker_aktiv = c_Asso_NoWeck;
				if($wecker_aktiv_all) 	$farbe_aktiv = "		<td style='background: #880000;' colspan='1' align='center'>";
				if(!$wecker_aktiv_all)  $farbe_aktiv = "		<td style='background: #400000;' colspan='1' align='center'>";
			}
			else {
				$wecker_aktiv = c_Asso_Weck;
				if($wecker_aktiv_all) 	$farbe_aktiv = "		<td style='background: #008000;' colspan='1' align='center'>";
				if(!$wecker_aktiv_all)  $farbe_aktiv = "		<td style='background: #002000;' colspan='1' align='center'>";
			}


			$html.="	<tr>\n";
			$html.="		<td align='center'> $wecker_tag</td>\n";
			$html.="		<td align='center'> $wecker_zeit</td>\n";
			$html.="$farbe_aktiv $wecker_aktiv</td>\n";

			switch ($tag){
			case 0:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_Table_Global."</td>\n";
					$html.="$farbe_global $wecker_global</td>\n";
				break;
			case 1:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_Table_Holiday."</td>\n";
					$html.="$farbe_urlaub $wecker_urlaub</td>\n";
				break;
			case 2:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_Table_Feasts."</td>\n";
					$html.="$farbe_feiertag $wecker_feiertag</td>\n";
				break;
			case 3:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_Table_Freeze."</td>\n";
					$html.="$farbe_frost $wecker_frost</td>\n";
				break;
			case 4:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_Table_Snooze."</td>\n";
					$html.="$farbe_schlummer $wecker_snooze</td>\n";
				break;
			case 5:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_Table_End."</td>\n";
					$html.="$farbe_end $wecker_end</td>\n";
				break;
			case 6:
				break;
			case 7:
				break;
			}
			$html.="	</tr>\n";
		}
		$html.="</table>\n";

		$html.="&nbsp;\n";

		$html.="<table>\n";
		$html.="	<tr>\n";
		$html.="		<td>".c_Table_HolidayTime."</td>\n";
		$html.="		<td>: ".$wecker_urlaubszeit."</td>\n";
		$html.="	</tr>\n";
		$html.="</table>\n";
		set_ControlValue(c_Control_Uebersicht, $CircleId, $html);

		return $html;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_DateTranslation($DateItem) {
		$Translation = array(
		    'Monday'    => 'Montag',
		    'Tuesday'   => 'Dienstag',
		    'Wednesday' => 'Mittwoch',
		    'Thursday'  => 'Donnerstag',
		    'Friday'    => 'Freitag',
		    'Saturday'  => 'Samstag',
		    'Sunday'    => 'Sonntag',
		    'Mon'       => 'Mo',
		    'Tue'       => 'Di',
		    'Wed'       => 'Mi',
		    'Thu'       => 'Do',
		    'Fri'       => 'Fr',
		    'Sat'       => 'Sa',
		    'Sun'       => 'So',
		    'January'   => 'Januar',
		    'February'  => 'Februar',
		    'March'     => 'März',
		    'May'       => 'Mai',
		    'June'      => 'Juni',
		    'July'      => 'Juli',
		    'October'   => 'Oktober',
		    'December'  => 'Dezember',
		    'Montag'		=> 'Mo',
		    'Dienstag'		=> 'Di',
		    'Mittwoch'		=> 'Mi',
		    'Donnerstag'	=> 'Do',
		    'Freitag'		=> 'Fr',
		    'Samstag'		=> 'Sa',
		    'Sonntag'		=> 'So',
		);
		return $Translation[$DateItem];
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_TimeToleranz($eventTime, $eventCircleTime){
		$ret = false;
		if ((($eventTime+2) > $eventCircleTime)
		and (($eventTime-2) < $eventCircleTime))
			$ret = true;

	return $ret;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_Urlaub($active, $urlaub){
		$urlaub = str_replace(" ","",$urlaub);
		$urlaub = str_replace("\n","",$urlaub);
		$frei = false;
		$nichtwecken = false;
		if ($urlaub<>""){
		   $i=0;
			$urlaubszeiten = explode(",", $urlaub);
			foreach($urlaubszeiten as $urlaubszeit){
				$datums = explode("-", $urlaubszeit);
				if (count($datums) == 1) $datums[1] = $datums[0];
				$year = date('Y');
				$day1=substr($datums[0],0,2);
				$month1=substr($datums[0],3,2);
				$day2=substr($datums[1],0,2);
				$month2=substr($datums[1],3,2);

				if (mktime(0, 0, 0, $month1, $day1, $year) < time()
				and mktime(23, 59, 59, $month2, $day2, $year) > time()) $frei = true;
			}
		}
		if ($frei == true and $active == false) $nichtwecken = true;
		return $nichtwecken;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_Feiertag($active){

		$tag = new Feiertag();
		$Fdays = $tag->getHolidays(Date('Y'));
		$nichtwecken = false;

		foreach($Fdays as $value) {
			list($key, $value) = each($Fdays);
			//Timestamp von heute
			$heute      = mktime (0,0,0,date("m"),date("d"),date("y"));
			//schauen ob Termin auf heute($erg = 0) oder morgen fällt ($erg = -1)
			$diff       = $heute-$value;
			$erg        = $diff/((60*60)*24);
			//Feiertag gefunden
			if ($erg == 0 and $active == false) $nichtwecken = true;
		}
		return $nichtwecken;
	}
	// ----------------------------------------------------------------------------------------------------------------------------
	//Feiertage
 	class Feiertag
	{
  		function getEasterSundayTime($year)
		{
   		$p = floor($year/100);
   		$r = floor($year/400);
   		$o = floor(($p*8+13)/25)-2;
   		$w = (19*($year%19)+(13+$p-$r-$o)%30)%30;
   		$e = ($w==29?28:$w);
   		if ($w==28&&($year%19)>10) $e=27;
   		$day = (2*($year%4)+4*($year%7)+6*$e+(4+$p-$r)%7)%7+22+$e;
   		$month = ($day>31?4:3);
   		if ($day>31) $day-=31;
   		return mktime(0, 0, 0, $month, $day, $year);
  		}
  		function getHolidays($year)
		{
    		$time = $this->getEasterSundayTime($year);
			$days[""] 									= 0;
		 	$days["Neujahr"] 							= mktime(0, 0, 0, 1, 1, $year);
    		//$days["Heilige 3 Könige"] 			= mktime(0, 0, 0, 1, 6, $year); //!!!!!!!!!!!!!!!!!!!!!!!
    		$days["Karfreitag"] 						= $time-(86400*2);
    		$days["Ostersonntag"] 					= $time;
    		$days["Ostermontag"] 					= $time+(86400);
    		$days["Tag der Arbeit"]        		= mktime(0, 0, 0, 5, 1, $year);
    		$days["Christi Himmelfahrt"] 			= $time+(86400*39);
    		$days["Pfingstsonntag"] 				= $time+(86400*49);
    		$days["Pfingstmontag"] 					= $time+(86400*50);
    		//$days["Fronleichnam"] 				= $time+(86400*60); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    		//$days["Maria Himmelfahrt"] 			= mktime(0, 0, 0, 08, 15, $year); //!!!!!!!!!!!!!!!!!!!!!
    		$days["Tag der deutschen Einheit"] 	= mktime(0, 0, 0, 10, 3, $year);
    		$days["Reformationstag"] 			   = mktime(0, 0, 0, 10, 31, $year); //!!!!!!!!!!!!!!!!!!!!!
    		//$days["Allerheiligen"] 					= mktime(0, 0, 0, 11, 1, $year);
    		$days["Buß- und Bettag"] 				= mktime(0, 0, 0, 11, 26+(7-date('w', mktime(0, 0, 0, 11, 26, $year)))-11, $year); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    		$days["1. Weihnachtsfeiertag"] 		= mktime(0, 0, 0, 12, 25, $year);
    		$days["2. Weihnachtsfeiertag"] 		= mktime(0, 0, 0, 12, 26, $year);
    		return $days;
		}
	}



	/** @}*/
?>