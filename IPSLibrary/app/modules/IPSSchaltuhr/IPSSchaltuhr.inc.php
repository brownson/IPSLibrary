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


	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSSchaltuhr_ChangeName($ControlId, $Value) {
		SetValueInteger ($ControlId,$Value);

	   $parentId 		= get_CirclyIdByControlId($ControlId);
		$NameValue		= get_ControlValue(c_Control_Name,$parentId)+1;
		$CirclesId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSSchaltuhr.'.c_ZSUCircles);
		$CircleId 		= get_CirclyIdByCircleIdent(c_ZSUCircle.$NameValue, $CirclesId );

		get_CircleConf($CircleId, $Value);

	   set_ControlValue(c_Control_Uebersicht, $parentId, get_ControlValue(c_Control_Uebersicht, $CircleId));

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
		set_Overview($CircleId);
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
		set_Overview($CircleId);
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
		set_Overview($CircleId);
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
		set_Overview($CircleId);
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
		set_Overview($CircleId);
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
		set_Overview($CircleId);
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
		set_Overview($CircleId);

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
		set_Overview($CircleId);

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
		set_Overview($CircleId);

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
	function set_Overview($CircleId){

 		$property = get_ZSUConfiguration();
 		$property = $property[IPS_GetName($CircleId)];
 		
		$html ="<style type='text/css'>\n";
		$html.="		table.zeitplan { width: 100%; border-collapse: true;}\n";
		$html.="		table.zeitplan td { border: 1px solid #444455; }\n";
		$html.="</style>\n";

		$html.="<table class='zeitplan'>\n\n";
		$html.="	<tr>";
		$html.="		<td style='width: 160px;' align='center'>".c_Control_StartZeit."</td>\n";
		$html.="		<td style='width: 100px;' align='center'>".get_ControlValue(c_Control_StartZeit, $CircleId)."</td>\n";
		$html.="		<td style='width: 100px; border: 0px;' align='center'>      </td>\n";
		$html.="		<td style='width: 160px;' align='center'>".c_Control_StopZeit."</td>\n";
		$html.="		<td style='width: 100px;' align='center'>".get_ControlValue(c_Control_StopZeit, $CircleId)."</td>\n";
		$html.="		<td style='border: 0px;' align='center'>      </td>\n";
		$html.="	</tr>";
		$html.="	</table>";

		$objectIds 				= explode(',',get_ControlValue(c_Control_StartTag, $CircleId));
		$html.="<table class='zeitplan'>\n\n";
		$html.="	<tr>";
		$html.="		<td style='width: 160px;' align='center'>".c_WFC_Tage."</td>\n";
		if ($objectIds[1] == '1')  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_Program_Montag."</td>\n";

		if ($objectIds[2] == '1')  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_Program_Dienstag."</td>\n";

		if ($objectIds[3] == '1')  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_Program_Mittwoch."</td>\n";

		if ($objectIds[4] == '1')  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_Program_Donnerstag."</td>\n";

		if ($objectIds[5] == '1')  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_Program_Freitag."</td>\n";

		if ($objectIds[6] == '1')  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_Program_Samstag."</td>\n";

		if ($objectIds[7] == '1')  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_Program_Sonntag."</td>\n";
		$html.="	</tr>";
		$html.="	</table>";
		$html.="&nbsp;\n";
		$html.="&nbsp;\n";


		$objectIds 	= explode(',',get_ControlValue(c_Control_StartAktiv, $CircleId));
		$html.="<table class='zeitplan'>\n\n";
		$html.="	<tr>";
		$html.="		<td style='width: 160px;' align='center'>".c_WFC_StartSensor."</td>\n";
		foreach ($property[c_Property_StartSensoren] as $ID=>$Data) {
				$variable     = IPS_GetVariable($Data[c_Property_SensorID]);
				$cprofile      = $variable['VariableCustomProfile'];
				$profile      = $variable['VariableProfile'];
				if ($cprofile!=='') {
					$profileData  = IPS_GetVariableProfile($cprofile);
					$suffix = $profileData['Suffix'];
				} elseif ($profile!=='') {
					$profileData  = IPS_GetVariableProfile($profile);
					$suffix = $profileData['Suffix'];
				}

		 		$res = false;
				if ($Data[c_Property_Condition] == '>' and GetValue($Data[c_Property_SensorID]) > $Data[c_Property_Value]) $res = true;
				if ($Data[c_Property_Condition] == '=' and GetValue($Data[c_Property_SensorID]) == $Data[c_Property_Value]) $res = true;
				if ($Data[c_Property_Condition] == '<' and GetValue($Data[c_Property_SensorID]) < $Data[c_Property_Value]) $res = true;

				if ($objectIds[$ID] == '1' and $res == true )	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
				if ($objectIds[$ID] == '1' and $res == false )	$farbe_tag = "		<td style='background: #808000;' colspan='1' align='center'>";
				if ($objectIds[$ID] == '0')	$farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".$Data[c_Property_Name]."<br>".GetValue($Data[c_Property_SensorID])."$suffix  ".$Data[c_Property_Condition]."  ".$Data[c_Property_Value]." $suffix</td>\n";
		}
		$html.="	</tr>";


		$objectIds 	= explode(',',get_ControlValue(c_Control_RunAktiv, $CircleId));
		$html.="	<tr>";
		$html.="		<td style='width: 160px;' align='center'>".c_WFC_RunSensor."</td>\n";
		foreach ($property[c_Property_RunSensoren] as $ID=>$Data) {
				$variable     = IPS_GetVariable($Data[c_Property_SensorID]);
				$profile      = $variable['VariableCustomProfile'];
				if ($profile<>'') {
					$profileData  = IPS_GetVariableProfile($profile);
					$suffix = $profileData['Suffix'];
				}

		 		$res = false;
				if ($Data[c_Property_Condition] == '>' and GetValue($Data[c_Property_SensorID]) > $Data[c_Property_Value]) $res = true;
				if ($Data[c_Property_Condition] == '=' and GetValue($Data[c_Property_SensorID]) == $Data[c_Property_Value]) $res = true;
				if ($Data[c_Property_Condition] == '<' and GetValue($Data[c_Property_SensorID]) < $Data[c_Property_Value]) $res = true;

				if ($objectIds[$ID] == '1' and $res == true )	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
				if ($objectIds[$ID] == '1' and $res == false )	$farbe_tag = "		<td style='background: #808000;' colspan='1' align='center'>";
				if ($objectIds[$ID] == '0')	$farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".$Data[c_Property_Name]."<br>".GetValue($Data[c_Property_SensorID])."$suffix  ".$Data[c_Property_Condition]."  ".$Data[c_Property_Value]." $suffix</td>\n";
		}
		$html.="	</tr>";


		$objectIds 	= explode(',',get_ControlValue(c_Control_StopAktiv, $CircleId));
		$html.="	<tr>";
		$html.="		<td style='width: 160px;' align='center'>".c_WFC_StopSensor."</td>\n";
		foreach ($property[c_Property_StopSensoren] as $ID=>$Data) {
				$variable     = IPS_GetVariable($Data[c_Property_SensorID]);
				$profile      = $variable['VariableCustomProfile'];
				if ($profile<>'') {
					$profileData  = IPS_GetVariableProfile($profile);
					$suffix = $profileData['Suffix'];
				}

		 		$res = false;
				if ($Data[c_Property_Condition] == '>' and GetValue($Data[c_Property_SensorID]) > $Data[c_Property_Value]) $res = true;
				if ($Data[c_Property_Condition] == '=' and GetValue($Data[c_Property_SensorID]) == $Data[c_Property_Value]) $res = true;
				if ($Data[c_Property_Condition] == '<' and GetValue($Data[c_Property_SensorID]) < $Data[c_Property_Value]) $res = true;

				if ($objectIds[$ID] == '1' and $res == true )	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
				if ($objectIds[$ID] == '1' and $res == false )	$farbe_tag = "		<td style='background: #808000;' colspan='1' align='center'>";
				if ($objectIds[$ID] == '0')	$farbe_tag = "		<td style='background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".$Data[c_Property_Name]."<br>".GetValue($Data[c_Property_SensorID])."$suffix  ".$Data[c_Property_Condition]."  ".$Data[c_Property_Value]." $suffix</td>\n";
		}
		$html.="	</tr>";
		$html.="	</table>";
		$html.="&nbsp;\n";
		$html.="&nbsp;\n";
		$html.="&nbsp;\n";

		$html.="<table class='zeitplan'>\n\n";
		$html.="	<tr>";
		$html.="		<td style='width: 160px;' align='center'>".c_WFC_Ausgang."</td>\n";

		if (get_ControlValue(c_Control_SollAusgang, $CircleId))  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='width: 160px; background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_WFC_SollZustand."</td>\n";

		if (get_ControlValue(c_Control_IstAusgang, $CircleId))  	$farbe_tag = "		<td style='background: #008000;' colspan='1' align='center'>";
		else $farbe_tag = "		<td style='width: 160px; background: #800000;' colspan='1' align='center'>";
				$html.="$farbe_tag ".c_WFC_IstZustand."</td>\n";
		$html.="		<td style='border: 0px;' align='center'>      </td>\n";

		$html.="	</tr>";
		$html.="	</table>";
		$html.="&nbsp;\n";
		$html.="&nbsp;\n";

		$html.="<table class='zeitplan'>\n\n";
		$html.="	<tr>";
		$html.="		<td style='font-size:12px; width: 160px;' align='center'>".c_WFC_Legende."</td>\n";

		$html.="<td style='font-size:12px; width: 160px; background: #800000;' colspan='1' align='center'>".c_WFC_Abgeschaltet."</td>\n";
		$html.="<td style='font-size:12px; width: 160px; background: #808000;' colspan='1' align='center'>".c_WFC_EinOhneBeding."</td>\n";
		$html.="<td style='font-size:12px; width: 160px; background: #008000;' colspan='1' align='center'>".c_WFC_EinMitBeding."</td>\n";
		$html.="		<td style='border: 0px;' align='center'>      </td>\n";


		$html.="	</tr>";
		$html.="	</table>";
		$html.="&nbsp;\n";
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


	/** @}*/
?>