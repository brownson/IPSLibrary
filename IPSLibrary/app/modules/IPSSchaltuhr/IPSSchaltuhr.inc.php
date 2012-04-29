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

	/**@defgroup IPSSchaltuhr IPSSchaltuhr
	 * @ingroup modules
	 * @{
	 *
	 * IPSSchaltuhr ist ein IPS Modul, das
	 *
	 * @file          IPSSchaltuhr.inc.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                  	"IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSInstaller.inc.php",               	"IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSComponent.class.php",             	"IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSSchaltuhr_Constants.inc.php",      	"IPSLibrary::app::modules::IPSSchaltuhr");
	IPSUtils_Include ("IPSSchaltuhr_Configuration.inc.php",  	"IPSLibrary::config::modules::IPSSchaltuhr");
	IPSUtils_Include ("IPSSchaltuhr_Custom.inc.php",         	"IPSLibrary::config::modules::IPSSchaltuhr");
	IPSUtils_Include ("IPSSchaltuhr_Logging.inc.php",        	"IPSLibrary::app::modules::IPSSchaltuhr");
	IPSUtils_Include ("IPSSchaltuhr_IDs.inc.php",            	"IPSLibrary::app::modules::IPSSchaltuhr");


//print_r(IPS_GetVariableProfile('IPSSchaltuhr_StartTag'));


	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeName($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		get_CircleConf($CircleId, $Value);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CircleConf($CircleId, $Value) {
		$Value++;
		$ControlId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr');

		$Property   = get_ZSUConfiguration();

		$StartZeit 	=  get_ControlValue(c_Control_StartZeit, $CircleId);
		$StopZeit 	=  get_ControlValue(c_Control_StopZeit, $CircleId);
		$StartTag 	=  get_ControlValue(c_Control_StartTag, $CircleId);
		$StopTag 	=  get_ControlValue(c_Control_StopTag, $CircleId);
		$StartAktiv =  get_ControlValue(c_Control_StartAktiv, $CircleId);
		$StopAktiv 	=  get_ControlValue(c_Control_StopAktiv, $CircleId);
		$RunAktiv 	=  get_ControlValue(c_Control_RunAktiv, $CircleId);

		set_ControlValue(c_Control_StartStunde, $ControlId, (int)substr($StartZeit,0,2));
		set_ControlValue(c_Control_StartMinute, $ControlId, (int)substr($StartZeit,3,2)/5);
		set_ControlValue(c_Control_StopStunde, $ControlId, (int)substr($StopZeit,0,2));
		set_ControlValue(c_Control_StopMinute, $ControlId, (int)substr($StopZeit,3,2)/5);

		set_AssoInteger('IPSSchaltuhr_StartTag', IPS_GetVariableProfile('IPSSchaltuhr_StartTag')['Associations'], $StartTag);
		set_AssoInteger('IPSSchaltuhr_StopTag',  IPS_GetVariableProfile('IPSSchaltuhr_StopTag')['Associations'], $StopTag);
		set_AssoInteger('IPSSchaltuhr_StartSensor', $Property[c_ZSUCircle.$Value][c_Property_StartSensoren], $StartAktiv);
		set_AssoInteger('IPSSchaltuhr_StopSensor', $Property[c_ZSUCircle.$Value][c_Property_StopSensoren],$StopAktiv);
		set_AssoInteger('IPSSchaltuhr_RunSensor', $Property[c_ZSUCircle.$Value][c_Property_RunSensoren],$RunAktiv);

	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_AssoInteger($Association, $Profile, $Value) {
		$objects = explode(',',$Value);
		$i=1;
		for ($i = 1; $i < count($objects)-1; $i++){
			if ($i > count($Profile)){
			  IPS_SetVariableProfileAssociation($Association, $i, ' ',"", -1);
			} else {
				set_AssoValue($Association, $i, $Profile, $objects[$i]);
			}
  		}

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_AssoValue($Association, $Position, $Profile, $Value) {


			if ($Value == 1) {
			  IPS_SetVariableProfileAssociation($Association, $Position, $Profile[$Position]['Name'],"", 0x00FF00);
			} elseif ($Value == 0) {
			  IPS_SetVariableProfileAssociation($Association, $Position, $Profile[$Position]['Name'],"", 0xFF0000);
			}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeStartTag($ControlId, $Value) {
		SetValueInteger ($ControlId,0);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		if ($Value>0)	tgl_AssoValue('IPSSchaltuhr_StartTag', $Value);

		$ConfValue = get_AssoValue('IPSSchaltuhr_StartTag');
		set_ControlValue(c_Control_StartTag, $CircleId, $ConfValue );

		set_TimerEvents($CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeStopTag($ControlId, $Value) {
		SetValueInteger ($ControlId,0);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		if ($Value>0)	tgl_AssoValue('IPSSchaltuhr_StopTag', $Value);

		$ConfValue = get_AssoValue('IPSSchaltuhr_StopTag');
		set_ControlValue(c_Control_StopTag, $CircleId, $ConfValue );

		set_TimerEvents($CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeStartStunde($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		$zeit = GetValueFormatted(get_ControlId(c_Control_StartStunde, $parentId)).":".GetValueFormatted(get_ControlId(c_Control_StartMinute, $parentId));
		set_ControlValue(c_Control_StartZeit, $CircleId, $zeit);

		set_TimerEvents($CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeStopStunde($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		$zeit = GetValueFormatted(get_ControlId(c_Control_StopStunde, $parentId)).":".GetValueFormatted(get_ControlId(c_Control_StopMinute, $parentId));
		set_ControlValue(c_Control_StopZeit, $CircleId, $zeit);

		set_TimerEvents($CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeStartMinute($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		$zeit = GetValueFormatted(get_ControlId(c_Control_StartStunde, $parentId)).":".GetValueFormatted(get_ControlId(c_Control_StartMinute, $parentId));
		set_ControlValue(c_Control_StartZeit, $CircleId, $zeit);

		set_TimerEvents($CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeStopMinute($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		$zeit = GetValueFormatted(get_ControlId(c_Control_StopStunde, $parentId)).":".GetValueFormatted(get_ControlId(c_Control_StopMinute, $parentId));
		set_ControlValue(c_Control_StopZeit, $CircleId, $zeit);

		set_TimerEvents($CircleId);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeStartAktiv($ControlId, $Value) {
		SetValueInteger ($ControlId,0);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		if ($Value>0)	tgl_AssoValue('IPSSchaltuhr_StartSensor', $Value);

		$ConfValue = get_AssoValue('IPSSchaltuhr_StartSensor');
		set_ControlValue(c_Control_StartAktiv, $CircleId, $ConfValue );

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeStopAktiv($ControlId, $Value) {
		SetValueInteger ($ControlId,0);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		if ($Value>0)	tgl_AssoValue('IPSSchaltuhr_StopSensor', $Value);

		$ConfValue = get_AssoValue('IPSSchaltuhr_StopSensor');
		set_ControlValue(c_Control_StopAktiv, $CircleId, $ConfValue );

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeRunAktiv($ControlId, $Value) {
		SetValueInteger ($ControlId,0);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		if ($Value>0)	tgl_AssoValue('IPSSchaltuhr_RunSensor', $Value);

		$ConfValue = get_AssoValue('IPSSchaltuhr_RunSensor');
		set_ControlValue(c_Control_RunAktiv, $CircleId, $ConfValue );

	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeInteger($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $CircleId = get_CirclyIdByControlId($ControlId);
      $ControlValue = GetValue($ControlId);
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function set_TimerEvents($CircleId) {
		$StartTimeId  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSSchaltuhr.IPSSchaltuhr_Timer.'.get_ControlType($CircleId).'-Start');
		$StopTimeId   = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSSchaltuhr.IPSSchaltuhr_Timer.'.get_ControlType($CircleId).'-Stop');

		$StartZeit 	= get_ControlValue(c_Control_StartZeit, $CircleId);
		$StartTag 	= get_ControlValue(c_Control_StartTag, $CircleId);
		$StopZeit 	= get_ControlValue(c_Control_StopZeit, $CircleId);
		$StopTag 	= get_ControlValue(c_Control_StopTag, $CircleId);

		$zeit = mktime(substr($StartZeit,0,2), substr($StartZeit,3,2), 0);
		if (!IPS_SetEventCyclicTimeBounds($StartTimeId, $zeit, 0)) {
			Error ("IPS_SetEventCyclicTimeBounds $TimerId $zeit failed !!!");
		}

		$zeit = mktime(substr($StopZeit,0,2), substr($StopZeit,3,2), 0);
		if (!IPS_SetEventCyclicTimeBounds($StopTimeId, $zeit, 0)) {
			Error ("IPS_SetEventCyclicTimeBounds $TimerId $zeit failed !!!");
		}

		$objectIds 		= explode(',',$StartTag);
		$days = $objectIds[1]*1;
		$days = $days + ($objectIds[2]*2);
		$days = $days + ($objectIds[3]*4);
		$days = $days + ($objectIds[4]*8);
		$days = $days + ($objectIds[5]*16);
		$days = $days + ($objectIds[6]*32);
		$days = $days + ($objectIds[7]*64);

		if (!IPS_SetEventCyclic($StartTimeId, 3 /**Week*/, 1 /**Datumsintervall**/,$days /**Datumstage**/,0 /**Datemstageintervall**/,0 /**Zeittyp**/,0 /**Zeitintervall**/)) {
			Error ("IPS_SetEventCyclic failed !!!");
		}

		$objectIds 		= explode(',',$StopTag);
		$days = $objectIds[1]*1;
		$days = $days + ($objectIds[2]*2);
		$days = $days + ($objectIds[3]*4);
		$days = $days + ($objectIds[4]*8);
		$days = $days + ($objectIds[5]*16);
		$days = $days + ($objectIds[6]*32);
		$days = $days + ($objectIds[7]*64);

		if (!IPS_SetEventCyclic($StopTimeId, 3 /**Week*/, 1 /**Datumsintervall**/,$days /**Datumstage**/,0 /**Datemstageintervall**/,0 /**Zeittyp**/,0 /**Zeitintervall**/)) {
			Error ("IPS_SetEventCyclic failed !!!");
		}

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_AssoValue($Association) {

		$Profile = IPS_GetVariableProfile($Association)['Associations'];

		$ret ='';
		foreach ($Profile as $AssoName=>$AssoData) {
			if ($AssoData['Color'] == -1 or $AssoData['Color'] == 0xFF0000) {
				$ret .= '0,';
			} else {
				$ret .= '1,';
			}
  		}
	return $ret;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function tgl_AssoValue($Association, $Value) {

			$Profile = IPS_GetVariableProfile($Association)['Associations'];

//			if ($Profile[$Value]['Color'] == -1 or $Profile[$Value]['Color'] == 0xFF0000) {
			if ($Profile[$Value]['Color'] == 0xFF0000) {
			  IPS_SetVariableProfileAssociation($Association, $Value, $Profile[$Value]['Name'],"", 0x00FF00);
			} else {
			  IPS_SetVariableProfileAssociation($Association, $Value, $Profile[$Value]['Name'],"", 0xFF0000);
			}
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function get_ControlType($ControlId) {
		$ControlName = IPS_GetName($ControlId);
		return $ControlName;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyIdByCircleIdent($CircleIdent, $parentId=null) {
		if ($parentId==null) {
			$parentId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr');
		}
		$CirclyId = IPS_GetCategoryIDByName($CircleIdent, $parentId);
		return $CirclyId;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyNameByID($CircleId) {
	   $CircleItem       = IPS_GetName($CircleId);
		$ZSUConfig      = get_ZSUConfiguration();
		return $ZSUConfig[$CircleItem][c_Property_Name];
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
	function	get_AviableSensor($sensor){
		$ret = 0;
		if ($sensor !== '') $ret = 1;
		if (IPS_VariableExists($sensor)) $ret = 2;
		return $ret;
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
	function set_Overview($parentId, $CircleId){
/*
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

		$ZSU_feiertag 		= $objectIds[7];
		$ZSU_urlaub 		= $objectIds[8];
		$ZSU_frost	 		= $objectIds[9];
		$ZSU_aktiv_all 	= $objectIds[10];
		$ZSU_snooze 	   = $objectIds[11];
		$ZSU_end 			= $objectIds[12];
		$ZSU_name 			= get_CirclyNameByID($CircleId);
		$ZSU_urlaubszeit 	= get_ControlValue(c_Control_Urlaubszeit,	$CircleId);

		if($ZSU_aktiv_all == 0)	{
			$ZSU_global = c_Program_Off;
			$farbe_global = "		<td style='background: #880000;' colspan='1' align='center'>";
		}
		else {
			$ZSU_global = c_Program_On;
			$farbe_global = "		<td style='background: #008000;' colspan='1' align='center'>";
		}

		if($ZSU_feiertag  == 0)	{
			$ZSU_feiertag = c_Program_NoZSU;
			if($ZSU_aktiv_all) 	$farbe_feiertag = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_feiertag = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else	{
			$ZSU_feiertag = c_Program_ZSU;
			if($ZSU_aktiv_all) 	$farbe_feiertag = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_feiertag = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($ZSU_urlaub == 0)	{
			$ZSU_urlaub = c_Program_NoZSU;
			if($ZSU_aktiv_all) 	$farbe_urlaub = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_urlaub = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$ZSU_urlaub = c_Program_ZSU;
			if($ZSU_aktiv_all) 	$farbe_urlaub = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all) 	$farbe_urlaub = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($ZSU_frost == 0)	{
			$ZSU_frost = c_Program_NormZSU;
			if($ZSU_aktiv_all) 	$farbe_frost = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_frost = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$ZSU_frost = c_Program_PrevZSU;
			if($ZSU_aktiv_all) 	$farbe_frost = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_frost = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($ZSU_snooze == 0){
			$ZSU_snooze = c_Program_Off;
			if($ZSU_aktiv_all) 	$farbe_schlummer = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_schlummer = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$ZSU_snooze = c_Program_On;
			if($ZSU_aktiv_all) 	$farbe_schlummer = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_schlummer = "		<td style='background: #002000;' colspan='1' align='center'>";
		}

		if($ZSU_end == 0)	{
			$ZSU_end = c_Program_Off;
			if($ZSU_aktiv_all) 	$farbe_end = "		<td style='background: #880000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_end = "		<td style='background: #400000;' colspan='1' align='center'>";
		}
		else {
			$ZSU_end = c_Program_On;
			if($ZSU_aktiv_all) 	$farbe_end = "		<td style='background: #008000;' colspan='1' align='center'>";
			if(!$ZSU_aktiv_all)  $farbe_end = "		<td style='background: #002000;' colspan='1' align='center'>";
		}



		for ($tag = 0; $tag < 7; $tag++){
			if ($tag == 0) $ZSU_tag = c_Control_Mo;
			if ($tag == 1) $ZSU_tag = c_Control_Di;
			if ($tag == 2) $ZSU_tag = c_Control_Mi;
			if ($tag == 3) $ZSU_tag = c_Control_Do;
			if ($tag == 4) $ZSU_tag = c_Control_Fr;
			if ($tag == 5) $ZSU_tag = c_Control_Sa;
			if ($tag == 6) $ZSU_tag = c_Control_So;


			$ZSU_zeit	= get_ControlValue($ZSU_tag, 		$CircleId);
			$ZSU_aktiv 		= $objectIds[$tag];


			if($ZSU_aktiv 		== 0) {
				$ZSU_aktiv = c_Program_NoZSU;
				if($ZSU_aktiv_all) 	$farbe_aktiv = "		<td style='background: #880000;' colspan='1' align='center'>";
				if(!$ZSU_aktiv_all)  $farbe_aktiv = "		<td style='background: #400000;' colspan='1' align='center'>";
			}
			else {
				$ZSU_aktiv = c_Program_ZSU;
				if($ZSU_aktiv_all) 	$farbe_aktiv = "		<td style='background: #008000;' colspan='1' align='center'>";
				if(!$ZSU_aktiv_all)  $farbe_aktiv = "		<td style='background: #002000;' colspan='1' align='center'>";
			}


			$html.="	<tr>\n";
			$html.="		<td align='center'> $ZSU_tag</td>\n";
			$html.="		<td align='center'> $ZSU_zeit</td>\n";
			$html.="$farbe_aktiv $ZSU_aktiv</td>\n";

			switch ($tag){
			case 0:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Global."</td>\n";
					$html.="$farbe_global $ZSU_global</td>\n";
				break;
			case 1:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Urlaub."</td>\n";
					$html.="$farbe_urlaub $ZSU_urlaub</td>\n";
				break;
			case 2:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Feiertag."</td>\n";
					$html.="$farbe_feiertag $ZSU_feiertag</td>\n";
				break;
			case 3:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Frost."</td>\n";
					$html.="$farbe_frost $ZSU_frost</td>\n";
				break;
			case 4:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_Snooze."</td>\n";
					$html.="$farbe_schlummer $ZSU_snooze</td>\n";
				break;
			case 5:
					$html.="		<td style='width: 100px; border: 0px;' align='center'></td>\n";
					$html.="		<td align='center'>".c_WFC_End."</td>\n";
					$html.="$farbe_end $ZSU_end</td>\n";
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
		$html.="		<td>: ".$ZSU_urlaubszeit."</td>\n";
		$html.="	</tr>\n";
		$html.="</table>\n";
		set_ControlValue(c_Control_Uebersicht, $CircleId, $html);
*/
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


	/** @}*/
?>