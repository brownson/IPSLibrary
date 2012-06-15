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
	function CheckIOInterfaces($instanceId, $name, $status) {
				$Control0Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
				$ControlId     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_Interfaces);
				$CallBack      = c_Control_Interfaces;

			// Prüfen ob Callback existiert
			if (function_exists($CallBack)) {
					IPSLogger_Dbg(__file__, 'Health CallBack Funktion '.$name.' Existiert in IPSHealth_Custom.');
					$VariableId = @IPS_GetVariableIDByName($name, $ControlId);
					if ($VariableId === false) {
                        $VariableId		 = CreateVariable($name	, 0 /*Boolean*/, $ControlId, 20, 'IPSHealth_Err', null, 0);
								IPS_SetInfo($VariableId, $instanceId);
								IPSHealth_Log("Interface Variable nicht vorhanden. Variable angelegt.");
					}
					if ($status == 'Active'){
								setvalue($VariableId, false);
								IPSHealth_Log("Interface Meldung! Interface: $name Status: $status");
					} else {
								setvalue($VariableId, true);
								IPSHealth_Log("Interface Meldung! Interface: $name Status: $status");
					}

					$html1 = "";
					$html1 = $html1 . "<table border='0' bgcolor=#006600 width='100%' height='300' cellspacing='0'  >";

					$variablesIDs = IPS_GetObject($ControlId);
					$variablesIDs = $variablesIDs['ChildrenIDs'];
					$err = false;

					foreach ($variablesIDs as $variableID) {
							if (IPS_GetName($variableID) <> c_Control_Uebersicht) {
										$ObjectName = IPS_GetName($variableID);
										if (getvalue($variableID) == true) {
												$backgroundcolor =  "#FF0000";
												$zustand = "Ausgefallen";
												$object     = IPS_GetObject($variableID);
												$objectInfo = $object['ObjectInfo'];
												$err = true;
										} else {
												$backgroundcolor =  "#008800";
												$zustand = "in Betrieb";
												$object     = IPS_GetObject($variableID);
												$objectInfo = $object['ObjectInfo'];
										}

										$html1 = $html1 . "<tr>";
										$html1 = $html1 . "<td style='text-align:left;background-color:$backgroundcolor;'>";
										$html1 = $html1 . "<span style='font-family:arial;color:black;font-size:16px;'>$ObjectName ($objectInfo):<br></span>";
										$html1 = $html1 . "<td style='text-align:center;background-color:$backgroundcolor;>";
										$html1 = $html1 . "<span style='font-family:arial;color:black;font-size:20px;font-weight:bold;'></span></td>";
										$html1 = $html1 . "<td style='text-align:left;background-color:$backgroundcolor;'>";
										$html1 = $html1 . "<span style='font-family:arial;color:black;font-size:16px;'>$zustand</span></td>";
										$html1 = $html1 . "</tr>";
							}
					}
					$html1 = $html1 . "</table>";
					set_ControlValue(c_Control_Error, $Control0Id ,$err);
					set_ControlValue(c_Control_Uebersicht, $ControlId, $html1);

					$CallBack($instanceId, $name, $status);  //Callback
			} else {
					IPSLogger_Err(__file__, "HealthCheck CallBack Funktion $CallBack in IPSHealth_Custom existiert nicht. Health: Interfaces");
			}
	}
	
	
	// ----------------------------------------------------------------------------------------------------------------------------
	function get_ModulVersion($ControlId, $instanceId, $Value) {

				$Circle0Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
				$ips_uebersicht_id	= get_ControlId(c_Control_Uebersicht, $Circle0Id);

			   IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');
				$moduleManager = new IPSModuleManager('IPSHealth');
				$version = $moduleManager->VersionHandler()->GetModuleVersion();

				$html1 = "";
				$html1 = $html1 . "<table border='0' bgcolor=#006600 width='100%' height='300' cellspacing='0'  >";

				$html1 = $html1 . "<tr>";
				$html1 = $html1 . "<td style='text-align:left;'>";
				$html1 = $html1 . "<span style='font-family:arial;color:white;font-size:15px;'><br></span>";
				$html1 = $html1 . "<span style='font-family:arial;color:white;font-size:15px;'></span></td>";
				$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:white;font-size:50px;'>IPSHealth</span></td>";
				$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:20px;'></span></td>";
				$html1 = $html1 . "</tr>";

				$html1 = $html1 . "<tr>";
				$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px'></span></td>";
				$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:yellow;font-size:50px'>Version</span></td>";
				$html1 = $html1 . "</tr>";

				$html1 = $html1 . "<tr>";
				$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px;'></span></td>";
				$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:yellow;font-size:50px;'>" .$version ."</span></td>";
				$html1 = $html1 . "</tr>";

				$html1 = $html1 . "</table>";
			   SetValueString($ips_uebersicht_id,$html1);

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function DB_Reaggregieren($ControlId, $instanceId, $Value) {
		
		$timestamp           = date("d.m.Y,", time())." ".date("H:i", time())." Uhr";
		$Fortschritt 			= get_ControlValue(c_Property_DBSteps, $ControlId);
		$archiveHandlerID    = IPS_GetInstanceIDByName("Archive Handler", 0);

		if (($Value == true) && ($Fortschritt >= 100)) {
			 set_ControlValue(c_Property_DBStart, $ControlId,$timestamp);
			 SetValueBoolean($instanceId, $Value);
          // Anzahl geloggte Variablen ermitteln
          $AggeratedVars = AC_GetAggregationVariables($archiveHandlerID, false);
          $AggeratedVars = count($AggeratedVars);
          set_ControlValue(c_Property_DBVarGes, $ControlId, $AggeratedVars);
          set_ControlValue(c_Property_DBHistory, $ControlId, "");
          set_ControlValue(c_Property_DBSteps, $ControlId, 0);
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function DB_Wartung() {
		$CircleId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_DBWartung);

		// Quelle: http://www.ip-symcon.de/service/dokumentation/modulreferenz/archive-control/datenbankwiederherstellung/

		/*****
		*
		* Automatische Reaggregation aller geloggten Variablen
		*
		* Dieses Skript reaggregiert automatisch alle geloggten Variablen nacheinander
		* automatishert bei Ausführung. Nach Abschluss des Vorgangs wird der Skript-Timer
		* gestoppt. Zur erneuten kompletten Reaggregation ist der Inhalt der automatisch
		* unterhalb des Skripts angelegten Variable 'History' zu löschen.
		*
		*****/
		$archiveHandlerID          = IPS_GetInstanceIDByName("Archive Handler", 0);

		$uhrzeit                   = date("H:i.s");
		$datum                     = date("d.m.Y");
		$timestamp                 = date("d.m.Y,", time())." ".date("H:i", time())." Uhr";

		//
		$Fortschritt               = get_ControlValue(c_Property_DBSteps, $CircleId);
		$geloggte_variablen    		= get_ControlValue(c_Property_DBVarGes, $CircleId);
		$Neuaggegation_val     		= get_ControlValue(c_Property_DBNeuagg, $CircleId);
		$finished              		= true;
		$history               		= explode(',', get_ControlValue(c_Property_DBHistory, $CircleId));
		$variableIDs           		= IPS_GetVariableList();

		if ($Neuaggegation_val == true) {
          foreach ($variableIDs as $variableID) {
	            if (AC_GetLoggingStatus($archiveHandlerID, $variableID) && !in_array($variableID, $history)) {
							$finished = false;
		              	if (@AC_ReAggregateVariable($archiveHandlerID, $variableID)) {
					          $AggeratedVars = AC_GetAggregationVariables($archiveHandlerID, false);
					          $AggeratedVars = count($AggeratedVars);
					          set_ControlValue(c_Property_DBVarGes, $CircleId, $AggeratedVars);
			                $history[] = $variableID;
								 $id = ips_getobject($variableID)['ParentID'];
								 $txt = IPS_GetName($id)."/".IPS_GetName($variableID);
			                set_ControlValue(c_Property_DBaktVar, $CircleId, $txt);
			                set_ControlValue(c_Property_DBHistory, $CircleId, implode(',', $history));
		              	}
		              	break;
	            }
          }
		}

		$history_count         = count($history);
		$Fortschritt_val       = ($history_count / $geloggte_variablen) *100;
		set_ControlValue(c_Property_DBSteps, $CircleId, round($Fortschritt_val, 1));
		set_ControlValue(c_Property_DBVarReady, $CircleId, $history_count);

		// Text für Abschlußmail
		$mailsubject           = "IPS: Reaggregation ist abgeschlossen";
		$mailcontend           = "IPS meldet: die Reaggregation ist am $datum um $uhrzeit Uhr abgeschlossen worden. Es wurden $history_count Variablen reaggregiert";
		$mailsubject_nr        = "IPS: Reaggregation wurde vorzeitig beendet";
		$mailcontend_nr        = "IPS meldet: die Reaggregation ist am $datum um $uhrzeit Uhr abgebrochen worden. Es wurden $history_count von $geloggte_variablen Variablen (= $Fortschritt %) reaggregiert";

		if (($finished) && ($Neuaggegation_val == true))
		{
				Send_EMail($mailsubject, $mailcontend);
				set_ControlValue(c_Property_DBReady, $CircleId,$timestamp);
				IPS_LogMessage('Reaggregation', 'Reaggregation completed!');
				get_ControlValue(c_Property_DBNeuagg, $CircleId, false);
            set_ControlValue(c_Property_DBaktVar, $CircleId, "");
		}
		elseif (($finished == true) && ($Neuaggegation_val == false) && ($history_count < $geloggte_variablen))
		{
				Send_EMail($mailsubject_nr, $mailcontend_nr);
				set_ControlValue(c_Property_DBReady, $CircleId,$timestamp);
				set_ControlValue(c_Property_DBVarReady, $CircleId, $history_count);
		}
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function Check_VarTimeout() {
			$Properts   	= get_HealthConfiguration();//[$CircleName];
			$CategoryIds   = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_HealthCircles);



			foreach ($Properts as $CircleName => $Data){
					$CirclyId   	= get_CirclyId($CircleName, $CategoryIds);
					$CircleId 		= get_ControlId(c_Control_Error,$CirclyId);

					if (function_exists($CircleName)) {
						IPSLogger_Dbg(__file__, 'Health CallBack Funktion '.$CircleName.' Existiert in IPSHealth_Custom.');

						$hintergrundfarbe = "#000000";
						$html1 = "";
						$html1 = $html1 . "<table border='0' bgcolor=$hintergrundfarbe width='100%' height='300' cellspacing='0'  >";
						$i=0;
						$r=0;
						$timeout = $Data[c_CircleTimeout];
						foreach ($Data[c_CircleVariables] as $ObjectID) {
							$Object = IPS_GetVariable($ObjectID);
							$ObjectName = IPS_GetName($ObjectID);
							$lasttime = $Object['VariableUpdated'];
							$diff = (int)round(time() - $lasttime);
							$Prozent = floor(($diff/$timeout) * 100);
							$mld = 'OK';
IPS_LogMessage('DEBUG',"$ObjectName $ObjectID  $diff>$timeout=$Prozent%");

							if ($diff > $timeout) {
								$mld = "Zeit ($diff Sek.) überschritten";
								if (GetValue($CircleId) == false){
										IPSHealth_Log($CircleName." Variable: $ObjectName ($ObjectID),  Ergebnis: $mld");
										$CircleName($CirclyId, $ObjectID, $ObjectName, "Error");
								}
								$r++;
							}

							if ($Prozent < 51){
								$backgroundcolor = "#008800";
							} else if (($Prozent > 50) and ($Prozent < 76)){
								$backgroundcolor = "#668800";
							} else if (($Prozent > 75) and ($Prozent < 101)){
								$backgroundcolor = "#AA8800";
							} else if ($Prozent > 100){
								$backgroundcolor = "#FF0000";
							}

							$html1 = $html1 . "<tr>";
							$html1 = $html1 . "<td style='text-align:left;background-color:$backgroundcolor;'>";
							$html1 = $html1 . "<span style='font-family:arial;color:black;font-size:16px;'>$ObjectName ($ObjectID):<br></span>";
							$html1 = $html1 . "<td style='text-align:center;background-color:$backgroundcolor;>";
							$html1 = $html1 . "<span style='font-family:arial;color:black;font-size:20px;font-weight:bold;'>$diff ($Prozent%)</span></td>";
							$html1 = $html1 . "<td style='text-align:left;background-color:$backgroundcolor;'>";
							$html1 = $html1 . "<span style='font-family:arial;color:black;font-size:16px;'>Sekunden</span></td>";
							$html1 = $html1 . "</tr>";

							$i++;
						}
						
						$html1 = $html1 . "</table>";
IPS_LogMessage('DEBUG',"$ObjectName $r=$i $CirclyId");

						Set_ControlValue(c_Control_Uebersicht, $CirclyId, $html1);

						if ($r == 0 and $i > 0) {
								if (GetValue($CircleId) == true){
										$CircleName($CircleId, $ObjectID, $ObjectName, "Good");
										IPSHealth_Log($CircleName.' HealthCheck Fehlerfrei beendet');
								}
								SetValue($CircleId,false);
						}

						if ($i == 0){
								IPSHealth_Log($CircleName.' Keine Variablen zur Überwachung!');
								SetValue($CircleId,false);
						}

						if ($r > 0 and $i > 0){
								if (GetValue($CircleId) == false){
										IPSHealth_Log($CircleName.' Summen Fehler gesetzt!');
								}
								SetValue($CircleId,true);
						}

					} else {

							IPSLogger_Err(__file__, "HealthCheck CallBack Funktion $CircleName in IPSHealth_Custom existiert nicht. Health: ".$Name);

					}
			}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_SysInfo_Server() {
				$Circle0Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
				$Circle1Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_Server);
				$Circle2Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_Statistik);
				$Circle3Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_DBWartung);
				$Circle4Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_HealthCircles);
				$Circle5Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_DBMonitor);
				$configData 			= get_HealthConfiguration();

				$ips_uebersicht_id	= get_ControlId(c_Control_Uebersicht	, $Circle0Id);
				$ips_serverzeit_id	= get_ControlId(c_Property_ServerZeit	, $Circle1Id);
				$ips_servercpu_id		= get_ControlId(c_Property_ServerCPU	, $Circle1Id);
				$ips_serverhdd_id		= get_ControlId(c_Property_ServerHDD	, $Circle1Id);
				$ips_laufzeit_id     = get_ControlId(c_Property_Uptime		, $Circle2Id);
				$ips_betriebszeiti_id= get_ControlId(c_Property_BetriebStdI	, $Circle2Id);

				// CCU Variable setzen/triggern
				if ((c_CCU_Control == true) and (c_CCU_IPSID <> "")){
            		fopen('http://'.c_CCU_IP.'/config/xmlapi/statechange.cgi?ise_id='.c_CCU_IPSID.'&new_value=0', 'r');
				}

				// CPU Auslastung
				$arr = Sys_GetCPUInfo();
				SetValue($ips_servercpu_id ,$arr['CPU_AVG']);

				// Freie HDD Kapazität
				$hdd = Sys_GetHardDiskInfo();
				SetValue($ips_serverhdd_id ,$hdd[c_SYS_HDD]['FREE']/1024/1024/1024);

				// Server Zeit
				SetValueString($ips_serverzeit_id , formatTag(Date('D'))." ".Date('H:i'));

				// Laufzeit ermitteln
				$Laufzeit            = time() - IPS_GetUptime();
				setValueInteger($ips_laufzeit_id, $Laufzeit);
				$LZ	= 	DurationToUhrzeit(time() - IPS_GetUptime());

				$Betriebszeit = getValueInteger($ips_betriebszeiti_id);
				$Betriebszeit = $Betriebszeit + 60 ;
				setValueInteger($ips_betriebszeiti_id, $Betriebszeit);
				$BZ	= 	DurationToUhrzeit($Betriebszeit);

				if (get_ControlValue(c_Property_DBNeuagg, $Circle3Id) == true) DB_Wartung($Circle3Id);


				// Datenbank Neuuaggregierung Fortschritt
				$DBWartung_Fertig = get_ControlValue(c_Property_DBSteps,$Circle3Id);

				// Datenbank Variable Fortschritt
				$DBVariable = get_ControlValue(c_Property_DBaktVar,$Circle3Id);

				// Suche Cirlce Fehler
				$err = 0;
				foreach ($configData as $Name=>$Data) {
						$CirclyId   = get_CirclyId($Name, $Circle4Id);
						$CircleId 	= get_ControlId(c_Control_Error,$CirclyId);
					   if (getValue($CircleId) == true) $err++;
				}

				// Datenbank Fehler?
			   if (get_ControlValue(c_Property_DB_Fehler,$Circle5Id) == true) $err++;

				// Sonstiger Fehler?
			   if (get_ControlValue(c_Control_Error,$Circle0Id) == true) $err++;


				// Generiere HTML Übersicht
				if ($err > 0){
							$hintergrundfarbe = "#661100";
							$systemstatus  = "Fehler im System";
							$systemcolor = "gray";
				} else {
							$hintergrundfarbe = "#003366";
							$systemstatus  = "Alles gut";
							$systemcolor = "green";
				}


				$html1 = "";
				$html1 = $html1 . "<table border='0' bgcolor=$hintergrundfarbe width='100%' height='300' cellspacing='0'  >";

				$html1 = $html1 . "<tr>";
				$html1 = $html1 . "<td style='text-align:left;'>";
				$html1 = $html1 . "<span style='font-family:arial;color:white;font-size:15px;'>Aktuell<br></span>";
				$html1 = $html1 . "<span style='font-family:arial;color:white;font-size:15px;'></span></td>";
				$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:white;font-size:24px;'>".formatTag(Date('D'))." ".Date('H:i')."</span></td>";
				$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:20px;'>Uhr</span></td>";
				$html1 = $html1 . "</tr>";

				$html1 = $html1 . "<tr>";
				$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px'>IPS-Uptime</span></td>";
				$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:yellow;font-size:20px'>$LZ</span></td>";
				$html1 = $html1 . "</tr>";

				$html1 = $html1 . "<tr>";
				$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px;'>IPS-Betriebszeit</span></td>";
				$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:yellow;font-size:20px;'>$BZ</span></td>";
				$html1 = $html1 . "</tr>";

		if (get_ControlValue(c_Property_DBNeuagg,$Circle3Id) == true){
				$html1 = $html1 . "<tr>";
				$html1 = $html1 . "<td style='text-align:left;font-family:arial;color:white;font-size:15px;'>Neuaggregierung</td>";
				$html1 = $html1 . "<td style='text-align:center;font-family:arial;font-weight:bold;color:yellow;font-size:20px;'>$DBWartung_Fertig %</td>";
				$html1 = $html1 . "</tr>";

				$html1 = $html1 . "<tr>";
				$html1 = $html1 . "<td style='text-align:left;font-family:arial;color:white;font-size:15px;'>Aggregiere</span></td>";
				$html1 = $html1 . "<td style='text-align:center;font-family:arial;color:yellow;font-size:14px;'>$DBVariable</span></td>";
				$html1 = $html1 . "</tr>";
		}

		$html1 = $html1 . "<tr>";
		$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px;'>System Status</span></td>";
		$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:$systemcolor;font-size:40px;'>$systemstatus</span></td>";
		$html1 = $html1 . "</tr>";

		$html1 = $html1 . "</table>";

	   SetValueString($ips_uebersicht_id,$html1);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function set_SysInfo_DBHealth() {
				$CircleId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_DBMonitor);

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
					Send_EMail(c_Mail_Subject, c_Mail_Content." \nletzte Aktualisierung vor: $delta_unix sec,\num: $write_date Uhr!");
					IPSLogger_Err(__file__, c_Mail_Content." \nletzte Aktualisierung vor: $delta_unix sec,\num: $write_date Uhr!");
					setValueBoolean($Warnstatus , true);
				} elseif ($delta_unix < c_Warn_Schwellwert) {
					setValueBoolean($Warnstatus , false);
				}

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function Send_EMail($Subject, $Text) {
				$ret = SMTP_SendMail( c_Mail_Instanz, $Subject, $Text );
				if ($ret === false){
					IPSLogger_Err(__file__, "EMail Versand fehlgeschlagen Instanz(".c_Mail_Instanz.") Beschreibung: $Subject");
				}
	}
	// ----------------------------------------------------------------------------------------------------------------------------
	function set_SysInfo_Statistik() {
				$CircleId     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_Control_Statistik);
				$ips_db_groesse_id		= get_ControlId(c_Property_DB_Groesse, $CircleId);
				$ips_db_zuwachs_id 		= get_ControlId(c_Property_DB_Zuwachs, $CircleId);
				$ips_objects_id 	 		= get_ControlId(c_Property_Objects, $CircleId);
				$ips_profiles_id 	 		= get_ControlId(c_Property_Profiles, $CircleId);
				$ips_scripts_id 	 		= get_ControlId(c_Property_Scripts, $CircleId);
				$ips_variables_id  		= get_ControlId(c_Property_Variable, $CircleId);
				$ips_instances_id  		= get_ControlId(c_Property_Instances, $CircleId);
				$ips_categories_id 		= get_ControlId(c_Property_Categorys, $CircleId);
				$ips_links_id 		 		= get_ControlId(c_Property_Links, $CircleId);
				$ips_modules_id 	 		= get_ControlId(c_Property_Modules, $CircleId);
				$ips_events_id	  	 		= get_ControlId(c_Property_Events, $CircleId);

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
	function InterfacesSelect($ControlId, $instanceId, $Value) {
			$CategoryIds     	= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
			$CircleIds     	= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_HealthCircles);
			$VisualisationIds	= IPSUtil_ObjectIDByPath('Visualization.WebFront.IPSHealth.Overview_3');
			$configData 		= get_HealthConfiguration();

			foreach ($configData as $Name=>$Data) {
					$VisuId 		= IPS_GetLinkIDByName($Data[c_CircleName], $VisualisationIds);
					$CirclyId   = get_CirclyId($Name, $CircleIds);
					$CircleId 	= get_ControlId(c_Control_Select,$CirclyId);
				   SetValueInteger($CircleId, 0);

				   IPS_SetHidden($VisuId, true);
			}

			set_ControlValue(c_Control_IOInterfaces,$CategoryIds, 1);
			set_ControlValue(c_Control_System,$CategoryIds, 0);

			$VisuId 		= IPS_GetLinkIDByName(c_Control_Modul, $VisualisationIds);
		   IPS_SetHidden($VisuId, true);

			$VisuId 		= IPS_GetLinkIDByName(c_Control_Version, $VisualisationIds);
		   IPS_SetHidden($VisuId, true);

			$VisuId 		= IPS_GetLinkIDByName(c_Control_IOInterfaces, $VisualisationIds);
		   IPS_SetHidden($VisuId, false);


	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function  CircleSelect($ControlId, $instanceId, $Value){
			$CategoryIds     	= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
			$CircleIds     	= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_HealthCircles);
			$VisualisationIds	= IPSUtil_ObjectIDByPath('Visualization.WebFront.IPSHealth.Overview_3');
			$configData 		= get_HealthConfiguration();

			foreach ($configData as $Name=>$Data) {
					$VisuId 		= IPS_GetLinkIDByName($Data[c_CircleName], $VisualisationIds);
					$CirclyId   = get_CirclyId($Name, $CircleIds);
					$CircleId 	= get_ControlId(c_Control_Select,$CirclyId);

					if ($CircleId == $instanceId){
						   SetValueInteger($CircleId, 1);
						   IPS_SetHidden($VisuId, false);

					} else {
						   SetValueInteger($CircleId, 0);
						   IPS_SetHidden($VisuId, true);
					}
			}
			set_ControlValue(c_Control_System,$CategoryIds, 0);
			set_ControlValue(c_Control_IOInterfaces,$CategoryIds, 0);

			$VisuId 		= IPS_GetLinkIDByName(c_Control_Modul, $VisualisationIds);
		   IPS_SetHidden($VisuId, true);

			$VisuId 		= IPS_GetLinkIDByName(c_Control_Version, $VisualisationIds);
		   IPS_SetHidden($VisuId, true);

			$VisuId 		= IPS_GetLinkIDByName(c_Control_IOInterfaces, $VisualisationIds);
		   IPS_SetHidden($VisuId, true);

	}
	
	// ----------------------------------------------------------------------------------------------------------------------------
	function  SystemSelect($ControlId, $instanceId, $Value){
			$CategoryIds     	= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
			$CircleIds     	= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth.'.c_HealthCircles);
			$VisualisationIds	= IPSUtil_ObjectIDByPath('Visualization.WebFront.IPSHealth.Overview_3');
			$configData 		= get_HealthConfiguration();

			foreach ($configData as $Name=>$Data) {
					$VisuId 		= IPS_GetLinkIDByName($Data[c_CircleName], $VisualisationIds);
					$CirclyId   = get_CirclyId($Name, $CircleIds);
					$CircleId 	= get_ControlId(c_Control_Select,$CirclyId);
				   SetValueInteger($CircleId, 0);

				   IPS_SetHidden($VisuId, true);
			}

			set_ControlValue(c_Control_System,$CategoryIds, 1);
			set_ControlValue(c_Control_IOInterfaces,$CategoryIds, 0);

			$VisuId 		= IPS_GetLinkIDByName(c_Control_Modul, $VisualisationIds);
		   IPS_SetHidden($VisuId, false);

			$VisuId 		= IPS_GetLinkIDByName(c_Control_Version, $VisualisationIds);
		   IPS_SetHidden($VisuId, false);

  			$VisuId 		= IPS_GetLinkIDByName(c_Control_IOInterfaces, $VisualisationIds);
		   IPS_SetHidden($VisuId, true);

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
	
   // ------------------------------------------------------------------------------------------------
	function get_CirclyId($DeviceName, $ParentId) {
		$CategoryId = IPS_GetObjectIDByIdent($DeviceName, $ParentId);
		return $CategoryId;
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

	// ----------------------------------------------------------------------------------------------------------------------------
	function DurationToUhrzeitRS($s) {
	 if($s >= 60*60*24)
	 {
	  $d       = floor($s / (3600*24));
	  $s       -= $d *3600*24;
	  $h   = floor($s / 3600);
	  $s   -= $h * 3600;
	  $m   = floor($s / 60);
	  $s   -= $m * 60;

	  return sprintf("%3d".'d'." %02d:%02d", $d, $h, $m);
	 }
	 else
	 {

	  $h   = floor($s / 3600);
	  $s   -= $h * 3600;
	  $m   = floor($s / 60);
	  $s   -= $m * 60;

	  return sprintf("%2d:%02d", $h, $m);
	 }

	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function DurationToUhrzeit($Betriebszeit) {

				$y = floor($Betriebszeit / (60*60*24*365));
				$Betriebszeit -=($y * (60*60*24*365));

				$d = floor($Betriebszeit / (60*60*24));
				$Betriebszeit -= ($d * (60*60*24));

				$h = floor($Betriebszeit / (60*60));
				$Betriebszeit -= ($h * (60*60));

				$m = floor($Betriebszeit / 60);
				$Betriebszeit -= ($m * 60);

				if ($y > 0 )  return sprintf("%2d".' jahre '." %3d".' tage '."%02d".' stunden '."%02d".' minuten ', $y , $d, $h, $m);

				if ($d > 0 )  return sprintf("%3d".' tage '."%02d".' stunden '."%02d".' minuten ', $d, $h, $m);

				if ($h > 0 )  return sprintf("%02d".' stunden '."%02d".' minuten ', $h, $m);

				if ($m > 0 )  return sprintf("%02d".' minuten ', $m);
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function DurationToUhrzeitRS1($rawTS) {
		$y          = floor(($rawTS / (60*60*24*365)));
		$d          = round((($rawTS - ($y * 60*60*24*365)) / (60*60*24)), 2);

		return $y.'J, '.$d."T";
//		return sprintf("%02d".'J '."%03d".'D ' , $y, $d);

		}
	/** @}*/
?>