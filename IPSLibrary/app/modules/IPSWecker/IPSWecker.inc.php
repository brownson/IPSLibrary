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
	* Version 1.00.3, 22.04.2012<br/>
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


//$wecker = AddConfiguration(21764  );
//print_r($wecker);


 	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWeckerChangeAktivCircle(){
		$WeckerConfig  = get_WeckerConfiguration();

		$Ass=0;
		foreach ($WeckerConfig as $WeckerName=>$WeckerData) {
			$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.($Ass+1), WECKER_ID_WECKZEITEN);
			$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);
			$objectIds 		= explode(',',GetValue($ConfId));

			if ($WeckerData[c_Property_Schichtgruppe] <> ''){

				if (in_array((int)date("W"), $WeckerData[c_Property_Schichtzyklus])){
					$objectIds[10] = "1";
				} else {
					$objectIds[10] = "0";
				}
				SetValue($ConfId, implode(",", $objectIds));
			}

			if ($objectIds[10]) {
			  IPS_SetVariableProfileAssociation('IPSWecker_Name', $Ass, $WeckerData[c_Property_Name],"", 0x00FF00);
			} else {
			  IPS_SetVariableProfileAssociation('IPSWecker_Name', $Ass, $WeckerData[c_Property_Name],"", 0xFF0000);
			}

			$parentId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker');
			if (get_ControlValue(c_Control_Name, $parentId) == $Ass){
				set_Overview($parentId, $CircleId);
				$wecker	=	AddConfiguration($CircleId);
				set_Control($wecker);
			}
			$Ass++;

		}
	}

 	// ----------------------------------------------------------------------------------------------------------------------------
	function AddConfiguration($CircleId, $object=array()){

	$object['Circle'] 		= AddActiveConfiguration($CircleId);
	$object['Property'] 		= AddPropertyConfiguration($CircleId);
	$object['Control'] 		= AddActiveControl();

	$object['ActiveTime'] 	= $object['Circle']['Time_'.get_DateTranslation(Date('l'))];
	$object['Global'] 		= $object['Circle'][c_Control_Global];
	$object['Active'] 		= $object['Circle']['Active_'.get_DateTranslation(Date('l'))];
	$object['CircleTime'] 	= mktime(substr($object['ActiveTime'],0,2), substr($object['ActiveTime'],3,2), 0);
	$object['RetFeiertag'] 	= get_Feiertag($object['Circle'][c_Control_Feiertag]);
	$object['RetUrlaub'] 	= get_Urlaub($object['Circle'][c_Control_Urlaub], $object['Circle'][c_Control_Urlaubszeit]);

	return $object;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function AddActiveControl(){
		$parentId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker');

		$object = array();
		$object['ControlId']             = $parentId;
		$object[c_Control_Name] 			= get_ControlValue(c_Control_Name, $parentId);
		$object[c_Control_LTag] 			= GetValueFormatted(get_ControlId(c_Control_LTag, $parentId));
		$object[c_Control_LStunde] 		= GetValueFormatted(get_ControlId(c_Control_LStunde, $parentId));
		$object[c_Control_LMinute] 		= GetValueFormatted(get_ControlId(c_Control_LMinute, $parentId));
		$object[c_Control_Global] 			= get_ControlValue(c_Control_Global, $parentId);
		$object[c_Control_Active] 			= get_ControlValue(c_Control_Active, $parentId);
		$object[c_Control_Feiertag] 		= get_ControlValue(c_Control_Feiertag, $parentId);

		$object[c_Control_Frost] 			= get_ControlValue(c_Control_Frost, $parentId);
		$object[c_Control_Urlaub] 			= get_ControlValue(c_Control_Urlaub, $parentId);
		$object[c_Control_Schlummer] 		= get_ControlValue(c_Control_Schlummer, $parentId);
		$object[c_Control_End] 				= get_ControlValue(c_Control_End, $parentId);
//		$object[c_Control_Uebersicht] 	= get_ControlValue(c_Control_Uebersicht, $parentId);
		$object[c_Control_Urlaubszeit] 	= get_ControlValue(c_Control_Urlaubszeit, $parentId);

		return $object;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function AddActiveConfiguration($CircleId){

		$Conf = get_ControlValue(c_Control_Optionen, $CircleId);

		$object = array();
		for ($i = 0; $i < 12; $i++){
			$objectIds[$i] = '0';
		}
		$objectIds = explode(',', $Conf);

		$object['CircleId']     			= $CircleId;
		$object[c_Control_Name] 			= get_ControlType($CircleId);

		$object['Time_'.c_Control_Mo] 	= get_ControlValue(c_Control_Mo, $CircleId);
		$object['Time_'.c_Control_Di] 	= get_ControlValue(c_Control_Di, $CircleId);
		$object['Time_'.c_Control_Mi] 	= get_ControlValue(c_Control_Mi, $CircleId);
		$object['Time_'.c_Control_Do] 	= get_ControlValue(c_Control_Do, $CircleId);
		$object['Time_'.c_Control_Fr] 	= get_ControlValue(c_Control_Fr, $CircleId);
		$object['Time_'.c_Control_Sa] 	= get_ControlValue(c_Control_Sa, $CircleId);
		$object['Time_'.c_Control_So]	 	= get_ControlValue(c_Control_So, $CircleId);

//		for ($i = 0; $i < 12; $i++){
//			if ( $objectIds[$i] == '') $objectIds[$i] = '0';
//		}


		$object['Active_'.c_Control_Mo] 	= $objectIds[0];
		$object['Active_'.c_Control_Di] 	= $objectIds[1];
		$object['Active_'.c_Control_Mi] 	= $objectIds[2];
		$object['Active_'.c_Control_Do] 	= $objectIds[3];
		$object['Active_'.c_Control_Fr] 	= $objectIds[4];
		$object['Active_'.c_Control_Sa] 	= $objectIds[5];
		$object['Active_'.c_Control_So] 	= $objectIds[6];
		$object[c_Control_Feiertag] 	  	= $objectIds[7];
		$object[c_Control_Urlaub]		  	= $objectIds[8];
		$object[c_Control_Frost] 			= $objectIds[9];
		$object[c_Control_Global] 			= $objectIds[10];
		$object[c_Control_Schlummer] 		= $objectIds[11];
		$object[c_Control_End] 				= $objectIds[12];

		$object[c_Control_Urlaubszeit] 	= get_ControlValue(c_Control_Urlaubszeit, $CircleId);
		$object['Uebersicht'] = get_ControlValue(c_Control_Uebersicht, $CircleId);

		return $object;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function AddPropertyConfiguration($CircleId){
		$WeckerConfig      = get_WeckerConfiguration();

		$CircleName = get_ControlType($CircleId);

		$object	= array();
		$object[c_Property_Name]	 			= $WeckerConfig[$CircleName][c_Property_Name];
		$object[c_Property_StopSensor]		= $WeckerConfig[$CircleName][c_Property_StopSensor];
		$object[c_Property_FrostTemp]	 		= $WeckerConfig[$CircleName][c_Property_FrostTemp];
		$object[c_Property_FrostSensor]		= $WeckerConfig[$CircleName][c_Property_FrostSensor];
		$object[c_Property_FrostTime]	 		= $WeckerConfig[$CircleName][c_Property_FrostTime];
		$object[c_Property_SnoozeTime]		= $WeckerConfig[$CircleName][c_Property_SnoozeTime];
		$object[c_Property_EndTime]	 		= $WeckerConfig[$CircleName][c_Property_EndTime];
		$object[c_Property_Schichtgruppe]	= $WeckerConfig[$CircleName][c_Property_Schichtgruppe];

		return $object;
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_TimerEvents($parentId, $CircleId){
		$ConfId 				= get_ControlId(c_Control_Optionen, $CircleId);
		$WeckerName			= IPS_GetName($CircleId);
		$WeckerConfig      = get_WeckerConfiguration();

		$ConfId 					= get_ControlId(c_Control_Optionen,	$CircleId);
		$objectIds 				= explode(',',GetValue($ConfId));
		$CircleIdent      	= IPS_GetName($CircleId);
		$ParamsFrostTime 	 = $WeckerConfig[$CircleIdent][c_Property_FrostTime];

		$wecker_feiertag 		= $objectIds[7];
		$wecker_urlaub 		= $objectIds[8];
		$wecker_frost 			= $objectIds[9];
		$wecker_aktiv_all 	= $objectIds[10];
		$wecker_snooze 		= $objectIds[11];
		$wecker_end 			= $objectIds[12];
		$wecker_urlaubszeit 	= get_ControlValue(c_Control_Urlaubszeit,	$CircleId);
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
	function set_NextTimerEvent($WeckerName, $wecker_zeit, $FrostTime){			//Aufruf von IPSWecker_Timer

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

		$DayId = get_ControlId(c_Control_LTag, $parentId);
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
			case c_Program_Werktags:
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
/*
	function get_Config($parentId, $CircleId){
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);
		$ControlType = get_ControlType($CircleId);
//		IPSWecker_Log('Config lesen für Wecker: '.$ControlType);

		$DayId = get_ControlId(c_Control_LTag, $parentId);
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
			case c_Program_Werktags:
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
//		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);

	}

*/
	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeWecker($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);
		$ControlType 	= get_ControlType($ControlId);
      $ControlValue 	= GetValue($ControlId);

		$tagId   = get_ControlId(c_Control_LTag, $parentId);
		$tagl 	= GetValueFormatted($tagId);
		if (($tagl==c_Program_Werktags) or ($tagl==c_Program_Woche) or ($tagl==c_Program_Wochenende)){
			  SetValue($tagId, 0);
			  IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, c_Program_Montag,"", -1);
		}

      IPSWecker_ChangeSchicht($parentId, $CircleId, $Value);
		$wecker	=	AddConfiguration($CircleId);
		set_Control($wecker);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeSchicht($parentId, $CircleId, $Value) {
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$WeckerConfig  = get_WeckerConfiguration();

		$CircleItem    = IPS_GetName($CircleId);
		$Propertyschicht 		= $WeckerConfig[$CircleItem][c_Property_Schichtgruppe];

		$Ass=0;
		foreach ($WeckerConfig as $WeckerName=>$WeckerData) {
			$WeckerCf 		= get_CirclyIdByCircleIdent(c_WeckerCircle.($Ass+1), WECKER_ID_WECKZEITEN);
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
					set_Overview($parentId, $WeckerCf);
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
			$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$Ass, WECKER_ID_WECKZEITEN);
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
/*	function get_AlarmClock($parentId, $CircleId) {
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);


		$tagl 	= GetValueFormatted(get_ControlId(c_Control_LTag, $parentId));
		if (($tagl !== c_Program_Werktags) and ($tagl !== c_Program_Woche) and ($tagl !== c_Program_Wochenende)){
				$wecker_tag 	= GetValueFormatted(get_ControlId(c_Control_LTag, $parentId));
				$wecker_zeit	= get_ControlValue($wecker_tag, 	$CircleId);
				$Hour = substr($wecker_zeit,0,2);
				$Minute =substr($wecker_zeit,3,2);
				set_ControlValue(c_Control_Stunde, 	$parentId, $Hour);
	 			set_ControlValue(c_Control_Minute, 	$parentId, $Minute);
		}
	}
*/
	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeDay($ControlId, $Value) {
		$parentId 		= get_CirclyIdByControlId($ControlId);
		$DayId  			= get_ControlId(c_Control_LTag, $parentId);
		$DayValue		= GetValue($DayId);

		switch($Value){
		case -1:
				$DayValue--;
				if ($DayValue <0) $DayValue = 9;
			break;
		case 100:
				$DayValue++;
				if ($DayValue >9) $DayValue = 0;
			break;
		}

		IPSWecker_ChangeLDay($DayId, $DayValue);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeLDay($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

		$parentId 		= get_CirclyIdByControlId($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);

	   IPS_SetVariableProfileAssociation('IPSWecker_Tag', 0, GetValueFormatted ($ControlId),"", -1);

		$wecker	=	AddConfiguration($CircleId);
		set_Control($wecker);
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_Control($Config) {
		$parentId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker');

		if (($Config['Control'][c_Control_LTag]<>c_Program_Werktags) and ($Config['Control'][c_Control_LTag]<>c_Program_Woche) and ($Config['Control'][c_Control_LTag]<>c_Program_Wochenende)){
				$Time = $Config['Circle']['Time_'.$Config['Control'][c_Control_LTag]];
				$Active = $Config['Circle']['Active_'.$Config['Control'][c_Control_LTag]];
				SetValue(get_ControlId(c_Control_LStunde, $parentId),substr($Time,0,2) );
				SetValue(get_ControlId(c_Control_LMinute, $parentId),(substr($Time,3,2)/5) );
				SetValue(get_ControlId(c_Control_Active, $parentId), $Active);
		}


		SetValue(get_ControlId(c_Control_Global, $parentId), $Config['Circle'][c_Control_Global]);
		SetValue(get_ControlId(c_Control_Feiertag, $parentId), $Config['Circle'][c_Control_Feiertag]);
		SetValue(get_ControlId(c_Control_Frost, $parentId), $Config['Circle'][c_Control_Frost]);
		SetValue(get_ControlId(c_Control_Urlaub, $parentId), $Config['Circle'][c_Control_Urlaub]);
		SetValue(get_ControlId(c_Control_Schlummer, $parentId), $Config['Circle'][c_Control_Schlummer]);
		SetValue(get_ControlId(c_Control_End, $parentId), $Config['Circle'][c_Control_End]);

		SetValue(get_ControlId(c_Control_Urlaubszeit, $parentId), $Config['Circle'][c_Control_Urlaubszeit]);
		SetValue(get_ControlId(c_Control_Uebersicht, $parentId), $Config['Circle'][c_Control_Uebersicht]);
	}



	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeStunde($ControlId, $Value) {
		$parentId 		= get_CirclyIdByControlId($ControlId);
		$StundeId  		= get_ControlId(c_Control_LStunde, $parentId);
		$StundeValue	= GetValue($StundeId);

		switch($Value){
		case -1:
				$StundeValue--;
				if ($StundeValue <0) $StundeValue = 23;
			break;
		case 100:
				$StundeValue++;
				if ($StundeValue >23) $StundeValue = 0;
			break;
		}
		IPSWecker_ChangeLStunde($StundeId, $StundeValue);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeLStunde($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

		$parentId 		= get_CirclyIdByControlId($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);

	   IPS_SetVariableProfileAssociation('IPSWecker_Stunde', 0, GetValueFormatted ($ControlId),"", -1);

		$wecker	=	AddConfiguration($CircleId);
		set_ConfigTime($wecker);

		$UeberValue	= set_Overview($parentId, $CircleId);
		set_ControlValue(c_Control_Uebersicht, $parentId, $UeberValue);
		set_TimerEvents($parentId, $CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeMinute($ControlId, $Value) {
		$parentId 		= get_CirclyIdByControlId($ControlId);
		$MinuteId  		= get_ControlId(c_Control_LMinute, $parentId);
		$MinuteValue	= GetValue($MinuteId);

		switch($Value){
		case -1:
				$MinuteValue--;
				if ($MinuteValue <0) $MinuteValue = 11;
			break;
		case 100:
				$MinuteValue++;
				if ($MinuteValue >11) $MinuteValue = 0;
			break;
		}
		IPSWecker_ChangeLMinute($MinuteId, $MinuteValue);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeLMinute($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

		$parentId 		= get_CirclyIdByControlId($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);

	   IPS_SetVariableProfileAssociation('IPSWecker_Minute', 0, GetValueFormatted ($ControlId),"", -1);

		$wecker	=	AddConfiguration($CircleId);
		set_ConfigTime($wecker);

		$UeberValue	= set_Overview($parentId, $CircleId);
		set_ControlValue(c_Control_Uebersicht, $parentId, $UeberValue);
		set_TimerEvents($parentId, $CircleId);
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_ConfigTime($Config){

		$CircleId = $Config['Circle']['CircleId'];
		$Name = $Config['Circle']['Name'];
		$tagl 	= $Config['Control']['LTag'];

		if ($tagl==c_Program_Werktags){
				SetValue(get_ControlId(c_Control_Mo,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Di,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Mi,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Do,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Fr,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);

		} elseif ($tagl==c_Program_Wochenende){
				SetValue(get_ControlId(c_Control_Sa,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_So,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);

		} elseif ($tagl==c_Program_Woche) {
				SetValue(get_ControlId(c_Control_Mo,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Di,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Mi,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Do,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Fr,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_Sa,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
				SetValue(get_ControlId(c_Control_So,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);

		} else {
				SetValue(get_ControlId($tagl,$CircleId),$Config['Control']['LStunde'].':'.$Config['Control']['LMinute']);
		}
//      $null = IPSWecker_Log('Neue Weckzeit gespeichert');
      return;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_Configuration($Config) {
		$CircleId = $Config['Circle']['CircleId'];
		$Name = $Config['Circle']['Name'];
		$tagl 	= $Config['Control']['LTag'];

		$objectIds  	=  array();
		$objectIds[c_ProgramId_Montag] 		= ((bool)$Config['Circle']['Active_'.c_Control_Mo]?		"1": "0");
		$objectIds[c_ProgramId_Dienstag] 	= ((bool)$Config['Circle']['Active_'.c_Control_Di]?		"1": "0");
		$objectIds[c_ProgramId_Mittwoch] 	= ((bool)$Config['Circle']['Active_'.c_Control_Mi]?		"1": "0");
		$objectIds[c_ProgramId_Donnerstag] 	= ((bool)$Config['Circle']['Active_'.c_Control_Do]?		"1": "0");
		$objectIds[c_ProgramId_Freitag] 		= ((bool)$Config['Circle']['Active_'.c_Control_Fr]?		"1": "0");
		$objectIds[c_ProgramId_Samstag] 		= ((bool)$Config['Circle']['Active_'.c_Control_Sa]?		"1": "0");
		$objectIds[c_ProgramId_Sonntag] 		= ((bool)$Config['Circle']['Active_'.c_Control_So]?		"1": "0");
		$objectIds[c_ProgramId_Feiertag]	 	= ((bool)$Config['Control'][c_Control_Feiertag]?			"1": "0");
		$objectIds[c_ProgramId_Urlaub] 		= ((bool)$Config['Control'][c_Control_Urlaub]?		 		"1": "0");
		$objectIds[c_ProgramId_Frost] 		= ((bool)$Config['Control'][c_Control_Frost]? 				"1": "0");
		$objectIds[c_ProgramId_Global] 		= ((bool)$Config['Control'][c_Control_Global]? 				"1": "0");
		$objectIds[c_ProgramId_Snooze] 		= ((bool)$Config['Control'][c_Control_Schlummer]? 			"1": "0");
		$objectIds[c_ProgramId_End] 			= ((bool)$Config['Control'][c_Control_End]? 					"1": "0");

		if ($tagl==c_Program_Werktags){
				$objectIds[c_ProgramId_Montag] 		= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Dienstag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Mittwoch] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Donnerstag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Freitag] 		= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Program_Wochenende){
				$objectIds[c_ProgramId_Samstag] 		= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Sonntag] 		= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Program_Woche) {
				$objectIds[c_ProgramId_Montag] 		= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Dienstag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Mittwoch] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Donnerstag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Freitag] 		= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Samstag] 		= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
				$objectIds[c_ProgramId_Sonntag] 		= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Control_Mo) {
				$objectIds[c_ProgramId_Montag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Control_Di) {
				$objectIds[c_ProgramId_Dienstag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Control_Mi) {
				$objectIds[c_ProgramId_Mittwoch] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Control_Do) {
				$objectIds[c_ProgramId_Donnerstag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Control_Fr) {
				$objectIds[c_ProgramId_Freitag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Control_Sa) {
				$objectIds[c_ProgramId_Samstag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");

		} elseif ($tagl==c_Control_So) {
				$objectIds[c_ProgramId_Sonntag] 	= ((bool)$Config['Control'][c_Control_Active]?		"1": "0");
		}
		SetValue(get_ControlId(c_Control_Optionen, $CircleId), implode(",", $objectIds));
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWecker_ChangeActive($ControlId, $Value) {
		SetValueBoolean ($ControlId,$Value);

		$parentId = get_CirclyIdByControlId($ControlId);
		$NameId			= get_ControlId(c_Control_Name,$parentId);
		$NameValue		= GetValueInteger($NameId)+1;
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);
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
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);
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
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);
		$ConfId 			= get_ControlId(c_Control_Optionen, $CircleId);


		set_ConfigGlobal($parentId, $CircleId);
		set_TimerEvents($parentId, $CircleId);

		$UeberValue	= get_ControlValue(c_Control_Uebersicht, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);
		set_WeckerAssociation($parentId, $CircleId);

//		$wecker	=	AddConfiguration($CircleId);
//		set_Configuration($wecker);
//		set_Control($wecker);

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
		$CircleId 		= get_CirclyIdByCircleIdent(c_WeckerCircle.$NameValue, WECKER_ID_WECKZEITEN);


		set_ControlValue(c_Control_Urlaubszeit, $CircleId, $Value);
		set_Overview($parentId, $CircleId);
		$UeberValue	= get_ControlValue(c_Control_Uebersicht, $CircleId);
		set_ControlValue(c_Control_Uebersicht, 	$parentId, $UeberValue);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_ControlType($ControlId) {
		$ControlName = IPS_GetName($ControlId);
		return $ControlName;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyIdByCircleIdent($CircleIdent, $parentId=null) {
		if ($parentId==null) {
			$parentId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWecker');
		}
		$CirclyId = IPS_GetCategoryIDByName($CircleIdent, $parentId);
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
	function	getAviableSensor($sensor){
		$ret = 0;
		if ($sensor !== '') $ret = 1;
		if (IPS_VariableExists($sensor)) $ret = 2;

//IPS_LogMessage('DEBUG',"AviableSensor ($sensor): ".$ret);

		return $ret;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_Overview($parentId, $CircleId){

		$html ="<style type='text/css'>\n";
		$html.="		table.stundenplan { width: 100%; border-collapse: true;}\n";
		$html.="		table.stundenplan td { border: 1px solid #444455; }\n";
		$html.="</style>\n";

		$html.="<table class='stundenplan'>\n\n";
		$html.="	<tr>";
		$html.="		<td style='width: 50px;' align='center'>".c_WFC_Tag."</td>\n";
		$html.="		<td style='width: 90px;' align='center'>".c_WFC_Stunde."</td>\n";
		$html.="		<td style='width: 200px;' align='center'>".c_WFC_Active."</td>\n";
		$html.="		<td style='width: 100px; border: 0px;' align='center'>      </td>\n";
		$html.="		<td style='width: 200px;' align='center'>".c_WFC_Feature."</td>\n";
		$html.="		<td style='width: 200px;' align='center'>".c_WFC_Active."</td>\n";
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
			$wecker_global = c_Program_Off;
			$farbe_global = "		<td style='background: #880000;' colspan='1' align='center'>";
		}
		else {
			$wecker_global = c_Program_On;
			$farbe_global = "		<td style='background: #008000;' colspan='1' align='center'>";
		}

		if($wecker_feiertag  == 0)	{
			$wecker_feiertag = c_Program_NoWeck;
			if($wecker_aktiv_all) 	$farbe_feiertag = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_feiertag = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else	{
			$wecker_feiertag = c_Program_Weck;
			if($wecker_aktiv_all) 	$farbe_feiertag = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_feiertag = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($wecker_urlaub == 0)	{
			$wecker_urlaub = c_Program_NoWeck;
			if($wecker_aktiv_all) 	$farbe_urlaub = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_urlaub = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$wecker_urlaub = c_Program_Weck;
			if($wecker_aktiv_all) 	$farbe_urlaub = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all) 	$farbe_urlaub = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($wecker_frost == 0)	{
			$wecker_frost = c_Program_NormWeck;
			if($wecker_aktiv_all) 	$farbe_frost = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_frost = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$wecker_frost = c_Program_PrevWeck;
			if($wecker_aktiv_all) 	$farbe_frost = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_frost = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($wecker_snooze == 0){
			$wecker_snooze = c_Program_Off;
			if($wecker_aktiv_all) 	$farbe_schlummer = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_schlummer = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$wecker_snooze = c_Program_On;
			if($wecker_aktiv_all) 	$farbe_schlummer = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_schlummer = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($wecker_end == 0)	{
			$wecker_end = c_Program_Off;
			if($wecker_aktiv_all) 	$farbe_end = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$wecker_aktiv_all)  $farbe_end = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$wecker_end = c_Program_On;
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
				$wecker_aktiv = c_Program_NoWeck;
				if($wecker_aktiv_all) 	$farbe_aktiv = "		<td style='background: #880000;' colspan='1' align='center'>";
				if(!$wecker_aktiv_all)  $farbe_aktiv = "		<td style='background: #400000;' colspan='1' align='center'>";
			}
			else {
				$wecker_aktiv = c_Program_Weck;
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
					$html.="		<td align='center'>".c_WFC_Global."</td>\n";
					$html.="$farbe_global $wecker_global</td>\n";
				break;
			case 1:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Urlaub."</td>\n";
					$html.="$farbe_urlaub $wecker_urlaub</td>\n";
				break;
			case 2:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Feiertag."</td>\n";
					$html.="$farbe_feiertag $wecker_feiertag</td>\n";
				break;
			case 3:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Frost."</td>\n";
					$html.="$farbe_frost $wecker_frost</td>\n";
				break;
			case 4:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Snooze."</td>\n";
					$html.="$farbe_schlummer $wecker_snooze</td>\n";
				break;
			case 5:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_End."</td>\n";
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
		$html.="		<td>".c_WFC_Urlaubszeit."</td>\n";
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

//		$tag = new Feiertag();
		$Fdays = getHolidays(Date('Y'));
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
  		function getEasterSundayTime($year)	{
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

	// ----------------------------------------------------------------------------------------------------------------------------
  		function getHolidays($year) {
  		
			$bland				= c_Property_Bundesland;
  		
    		$time = getEasterSundayTime($year);
			$days[""] 									= 0;
		 	$days["Neujahr"] 							= mktime(0, 0, 0, 1, 1, $year);
    		$days["Karfreitag"] 						= $time-(86400*2);
    		$days["Ostersonntag"] 					= $time;
    		$days["Ostermontag"] 					= $time+(86400);
    		$days["Tag der Arbeit"]        		= mktime(0, 0, 0, 5, 1, $year);
    		$days["Christi Himmelfahrt"] 			= $time+(86400*39);
    		$days["Pfingstsonntag"] 				= $time+(86400*49);
    		$days["Pfingstmontag"] 					= $time+(86400*50);
    		$days["Tag der deutschen Einheit"] 	= mktime(0, 0, 0, 10, 3, $year);
    		$days["Buß- und Bettag"] 				= mktime(0, 0, 0, 11, 26+(7-date('w', mktime(0, 0, 0, 11, 26, $year)))-11, $year); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    		$days["1. Weihnachtsfeiertag"] 		= mktime(0, 0, 0, 12, 25, $year);
    		$days["2. Weihnachtsfeiertag"] 		= mktime(0, 0, 0, 12, 26, $year);

			//*******************************
			// Fester $Feiertag in BW, BY, ST
			//*******************************
			if (($bland == "BW") or ($bland == "BY") or ($bland == "ST")) {
		    		$days["Heilige 3 Könige"] 			= mktime(0, 0, 0, 1, 6, $year); //!!!!!!!!!!!!!!!!!!!!!!!
			}

			//***************************************
			// Fester $Feiertag in BB, MV, SA, ST, TH
			//***************************************
			if (($bland == "BB") or ($bland == "MV") or ($bland == "SA") or ($bland == "ST") or ($bland == "TH")) {
		    		$days["Reformationstag"] 			   = mktime(0, 0, 0, 10, 31, $year); //!!!!!!!!!!!!!!!!!!!!!
			}

			//***************************************
			// Fester $Feiertag in BW, BY, NW, RP, SL
			//***************************************
			if (($bland == "BW") or ($bland == "BY") or ($bland == "NW") or ($bland == "RP") or ($bland == "SL")) {
		    		$days["Allerheiligen"] 					= mktime(0, 0, 0, 11, 1, $year);
			}

			//*******************************************
			// Fester $Feiertag in BY (nicht überall), SL
			//*******************************************
			if (($bland == "BY") or ($bland == "SL")) {
		    		$days["Maria Himmelfahrt"] 			= mktime(0, 0, 0, 08, 15, $year); //!!!!!!!!!!!!!!!!!!!!!
			}

			//**********************************************************************
			// Bewegliche Feiertage BW, BY, HE, NW, RP, SL, (SA, TH nicht überall)
			//**********************************************************************
			if (($bland == "BW") or ($bland == "BY") or ($bland == "HE") or ($bland == "NW") or ($bland == "RP") or ($bland == "SL") or ($bland == "SA") or ($bland == "TH")) {
			    		$days["Fronleichnam"] 				= $time+(86400*60); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			}

    		return $days;
		}



	/** @}*/
?>