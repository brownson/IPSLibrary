<?
	include_once "IPSHealth.inc.php";

// --- Konfig: Variablen & Parameter deklarieren -------------------------------


   # Variablen
	$CircleId     			= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_SysInfo);
   $DB_Timestamp        = get_ControlId(c_Property_lastWrite, $CircleId);
	$DB_Size					= get_ControlId(c_Property_LogDB_Groesse, $CircleId);
   $Warnstatus          = get_ControlId(c_Property_DB_Fehler, $CircleId);
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

	# Datenbank
	$DB_FileName  			= IPS_GetKernelDir()."db\logging.db";          //Pfad der DB



// ------------------------ Konfig  Ende ---------------------------------------

	switch ($_IPS['SENDER']) {
		case 'TimerEvent':
			$eventId 	=  $_IPS['EVENT'];
			$strpos  	= strrpos(IPS_GetName($eventId), '-', 0);
			$CircleName = substr(IPS_GetName($eventId),0, $strpos);
			$EventMode 	= substr(IPS_GetName($eventId), $strpos+1, strlen(IPS_GetName($eventId))-$strpos-1);
//			$Properts   = get_HealthConfiguration()[$CircleName];

		// ------- Script --------------------------------------------------------------
			if ($EventMode = "Info"){
				$write_date_unix =  filemtime($DB_FileName);
				$delta_unix = time() - $write_date_unix;
				$write_date = date("r", $write_date_unix);
				$delta = date("r", $delta_unix);

				// "Alter letzter Schreibvorgang" schreiben
				SetValue($DB_Timestamp, $delta_unix);

				// DB-Grösse herausfinden
				$groesse = filesize("$DB_FileName");
				$groesse = $groesse/1024/1024;
				$groesse = round($groesse, 2);

				// Datenbankgröße schreiben
				SetValueFloat($DB_Size, $groesse);

				// Warrnstatus ermitteln und Aktionen auslösen
				if ($delta_unix > c_Warn_Schwellwert) {
					echo c_Log_Content." $delta_unix sec\n";
					SMTP_SendMail( c_Mail_Instanz, c_Mail_Subject, c_Mail_Content." \nletzte Aktualisierung vor: $delta_unix sec,\num: $write_date Uhr!");
					setValueBoolean($Warnstatus , true);
				} elseif ($delta_unix <60) {
					setValueBoolean($Warnstatus , false);
				}
			}


			if ($EventMode = "Day"){

				// DB-Grösse herausfinden
				$groesse = filesize("$DB_FileName");
				$groesse = $groesse/1024/1024;
				$groesse = round($groesse, 2);

				// DB-Werte in Var schreiben
				setValueFloat($ips_db_zuwachs_id, $groesse-getValueFloat($ips_db_groesse_id));
				setValueFloat($ips_db_groesse_id, $groesse);
				//echo "Die IPS-Datenbank ist $groesse MB gross.";

				// Anzahl IPS Objekte ermitteln
				$array = IPS_GetObjectList();
				$count = count($array);
				setValueInteger($ips_objects_id, $count);
				//echo "Anzahl Objekte = $count - ";
				$array =array();

				// Anzahl IPS Profile ermitteln
				$array = IPS_GetVariableProfileList();
				$count = count($array);
				setValueInteger($ips_profiles_id, $count);
				//echo "Anzahl Profile = $count - ";
				$array =array();

				// Anzahl IPS Sripte ermitteln
				$array = IPS_GetScriptList();
				$count = count($array);
				setValueInteger($ips_scripts_id, $count);
				//echo "Anzahl Scripte = $count - ";
				$array =array();

				// Anzahl IPS Variablen ermitteln
				$array = IPS_GetVariableList();
				$count = count($array);
				setValueInteger($ips_variables_id, $count);
				//echo "Anzahl Variablen = $count - ";
				$array =array();

				// Anzahl IPS Instanzen ermitteln
				$array = IPS_GetInstanceList();
				$count = count($array);
				setValueInteger($ips_instances_id, $count);
				//echo "Anzahl Instanzen = $count - ";
				$array =array();

				// Anzahl IPS Kategorien ermitteln
				$array = IPS_GetCategoryList();
				$count = count($array);
				setValueInteger($ips_categories_id, $count);
				//echo "Anzahl Kategorieen = $count - ";
				$array =array();

				// Anzahl IPS Links ermitteln
				$array = IPS_GetLinkList();
				$count = count($array);
				setValueInteger($ips_links_id, $count);
				//echo "Anzahl Links = $count - ";
				$array =array();

				// Anzahl IPS Module ermitteln
				$array = IPS_GetModuleList();
				$count = count($array);
				setValueInteger($ips_modules_id, $count);
				//echo "Anzahl Module = $count - ";
				$array =array();

				// Anzahl IPS Events ermitteln
				$array = IPS_GetEventList();
				$count = count($array);
				setValueInteger($ips_events_id, $count);
				//echo "Anzahl Events = $count - ";
				$array =array();
			}

			break;
		case 'WebFront':
			break;
		case 'Execute':
			break;
		case 'RunScript':
			break;
		default:
			IPSLogger_Err(__file__, 'Unknown Sender '.$_IPS['SENDER']);
			break;
	}
















	function DBFilesize($groesse) {
			$mb = 1024;                                //kBits pro MB
			$einheit = explode(' ','B KB MB GB');
			for ($i = 0; $groesse > $mb; $i++) {
				$groesse /= $mb;                         //Groesse runterrechnen
      	}
			return round($groesse, 2) . ' ' . $einheit[$i]; //brauchbar runden
	}
	
	

	// --- Script ------------------------------------------------------------------




?>