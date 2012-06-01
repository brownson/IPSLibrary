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

	/**@defgroup IPSHealth IPSHealth
	 * @ingroup modules
	 * @{
	 *
	 * IPSHealth ist ein IPS Modul, das
	 *
	 * @file          IPSHealth.inc.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                  	"IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSInstaller.inc.php",               	"IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSComponent.class.php",             	"IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSHealth_Constants.inc.php",      	"IPSLibrary::app::modules::IPSHealth");
	IPSUtils_Include ("IPSHealth_Configuration.inc.php",  	"IPSLibrary::config::modules::IPSHealth");
	IPSUtils_Include ("IPSHealth_Custom.inc.php",         	"IPSLibrary::config::modules::IPSHealth");
	IPSUtils_Include ("IPSHealth_Logging.inc.php",        	"IPSLibrary::app::modules::IPSHealth");

	// ----------------------------------------------------------------------------------------------------------------------------
	function Check_VarTO($CircleName) {
			$Properts   = get_HealthConfiguration()[$CircleName];

			if (function_exists($CircleName)) {
				IPSLogger_Dbg(__file__, 'Health CallBack Funktion '.$CircleName.' Existiert in IPSHealth_Custom.');
				IPSHealth_Log($CircleName.' HealthCheck gestartet');

				$i=0;
				$r=0;
				$timeout = $Properts[c_HealthTimeout];
				foreach ($Properts[c_HealthVariables] as $ObjectID) {
					$Object = IPS_GetVariable($ObjectID);
					$lasttime = $Object['VariableUpdated'];
					$diff = (int)round(time() - $lasttime);
					$mld = 'OK';
					if ($diff > $timeout) {
						$mld = "Zeit ($diff Sek.) überschritten";
						IPSHealth_Log($CircleName." Variable: ".IPS_GetName($ObjectID)."($ObjectID),  Ergebnis: $mld");
						IPSLogger_Err(__file__, $CircleName.",  Variable: ".IPS_GetName($ObjectID)."($ObjectID),  Zeit: $diff,  Ergebnis: $mld");
						$r++;
					}
					$i++;
				}
				if ($r == 0 and $i > 0) IPSHealth_Log($CircleName.' HealthCheck Fehlerfrei beendet');
				if ($i == 0) IPSHealth_Log($CircleName.' Keine Variablen zur Überwachung!');

			} else {

					IPSLogger_Err(__file__, "HealthCheck CallBack Funktion $CircleName in IPSHealth_Custom existiert nicht. Health: ".$Name);
			}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_SysInfo_Server($CircleId) {
				$ips_serverzeit_id	= get_ControlId(c_Property_ServerZeit, $CircleId);
				$ips_servercpu_id		= get_ControlId(c_Property_ServerCPU, $CircleId);
				$ips_serverhdd_id		= get_ControlId(c_Property_ServerHDD, $CircleId);

				$arr = Sys_GetCPUInfo();
				SetValue($ips_servercpu_id ,$arr['CPU_AVG']);

				$hdd = Sys_GetHardDiskInfo();
				SetValue($ips_serverhdd_id ,$hdd[c_SYS_HDD]['FREE']/1024/1024/1024);

				SetValueString($ips_serverzeit_id , formatTag(Date('D'))." ".Date('H:i'));

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_SysInfo_DBHealth($CircleId) {
			   $DB_Timestamp        = get_ControlId(c_Property_lastWrite, $CircleId);
				$DB_Size					= get_ControlId(c_Property_LogDB_Groesse, $CircleId);
			   $Warnstatus          = get_ControlId(c_Property_DB_Fehler, $CircleId);
				$DB_FileName			= IPS_GetKernelDir()."db\logging.db";          //Pfad der DB

				$write_date_unix =  filemtime($DB_FileName);
				$delta_unix = time() - $write_date_unix;
				$write_date = date("r", $write_date_unix);
				$delta = date("r", $delta_unix);

				// "Alter letzter Schreibvorgang" schreiben
				SetValue($DB_Timestamp, $delta_unix);


				// Datenbankgröße schreiben
				SetValueFloat($DB_Size, get_DB_Groesse());

				// Warrnstatus ermitteln und Aktionen auslösen
				if ($delta_unix > c_Warn_Schwellwert) {
					echo c_Log_Content." $delta_unix sec\n";
					SMTP_SendMail( c_Mail_Instanz, c_Mail_Subject, c_Mail_Content." \nletzte Aktualisierung vor: $delta_unix sec,\num: $write_date Uhr!");
					IPSLogger_Err(__file__, c_Mail_Content." \nletzte Aktualisierung vor: $delta_unix sec,\num: $write_date Uhr!");
					setValueBoolean($Warnstatus , true);
				} elseif ($delta_unix <60) {
					setValueBoolean($Warnstatus , false);
				}

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_SysInfo_Statistik($CircleId) {
				$ips_db_groesse_id	= get_ControlId(c_Property_DB_Groesse, $CircleId);
				$ips_db_zuwachs_id 	= get_ControlId(c_Property_DB_Zuwachs, $CircleId);
				$ips_objects_id 	 	= get_ControlId(c_Property_Objects, $CircleId);
				$ips_profiles_id 	 	= get_ControlId(c_Property_Profiles, $CircleId);
				$ips_scripts_id 	 	= get_ControlId(c_Property_Scripts, $CircleId);
				$ips_variables_id  	= get_ControlId(c_Property_Variable, $CircleId);
				$ips_instances_id  	= get_ControlId(c_Property_Instances, $CircleId);
				$ips_categories_id 	= get_ControlId(c_Property_Categorys, $CircleId);
				$ips_links_id 		 	= get_ControlId(c_Property_Links, $CircleId);
				$ips_modules_id 	 	= get_ControlId(c_Property_Modules, $CircleId);
				$ips_events_id	  	 	= get_ControlId(c_Property_Events, $CircleId);

				// DB-Werte in Var schreiben
				setValueFloat($ips_db_zuwachs_id, get_DB_Groesse()-getValueFloat($ips_db_groesse_id));
				setValueFloat($ips_db_groesse_id, get_DB_Groesse());

				// Anzahl IPS Objekte ermitteln
				$array = IPS_GetObjectList();
				$count = count($array);
				setValueInteger($ips_objects_id, $count);
				$array =array();

				// Anzahl IPS Profile ermitteln
				$array = IPS_GetVariableProfileList();
				$count = count($array);
				setValueInteger($ips_profiles_id, $count);
				$array =array();

				// Anzahl IPS Sripte ermitteln
				$array = IPS_GetScriptList();
				$count = count($array);
				setValueInteger($ips_scripts_id, $count);
				$array =array();

				// Anzahl IPS Variablen ermitteln
				$array = IPS_GetVariableList();
				$count = count($array);
				setValueInteger($ips_variables_id, $count);
				$array =array();

				// Anzahl IPS Instanzen ermitteln
				$array = IPS_GetInstanceList();
				$count = count($array);
				setValueInteger($ips_instances_id, $count);
				$array =array();

				// Anzahl IPS Kategorien ermitteln
				$array = IPS_GetCategoryList();
				$count = count($array);
				setValueInteger($ips_categories_id, $count);
				$array =array();

				// Anzahl IPS Links ermitteln
				$array = IPS_GetLinkList();
				$count = count($array);
				setValueInteger($ips_links_id, $count);
				$array =array();

				// Anzahl IPS Module ermitteln
				$array = IPS_GetModuleList();
				$count = count($array);
				setValueInteger($ips_modules_id, $count);
				$array =array();

				// Anzahl IPS Events ermitteln
				$array = IPS_GetEventList();
				$count = count($array);
				setValueInteger($ips_events_id, $count);
				$array =array();

	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function get_DB_Groesse() {
				$DB_FileName = IPS_GetKernelDir()."db\logging.db";          //Pfad der DB

				// DB-Grösse herausfinden
				$groesse = filesize("$DB_FileName");
				$groesse = $groesse/1024/1024;
				$groesse = round($groesse, 2);

		return $groesse;
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
			$parentId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
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
	function formatTag($DateItem) {
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
		);
		return $Translation[$DateItem];
	}


	/** @}*/
?>