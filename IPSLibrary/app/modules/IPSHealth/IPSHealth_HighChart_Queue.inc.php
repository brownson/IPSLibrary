<?
// bei der Konfiguration unbedingt auf die Groß/Kleinschreibung achten

Global $CfgDaten; // damit kann der Script auch von anderen Scripten aufgerufen werden und bereits mit CfgDaten vorkonfiguriert werden

// RS-Special: Konfig/externe Daten --------------------------------------------
	include_once "IPSHealth.inc.php";
   include 				IPS_GetKernelDir() .  "scripts\IPSLibrary\app\modules\IPSHealth\IPSHealth_Log_raw_Analysis.inc.php";         // IPS Log Analyse-Script eintragen
   include 				IPS_GetKernelDir() .  "scripts\IPSLibrary\app\modules\IPSHealth\IPSHelath_Highchart.inc.php";         // IPS Log Analyse-Script eintragen

// RS-Special: Ende ------------------------------------------------------------

	// Zeitraum welcher dargestellt werden soll
	$CfgDaten["StartTime"] 										= mktime(0,0,0, date("m", time()), date("d",time()), date("Y",time()));
	$CfgDaten["EndTime"] 										= mktime(23,59,59, date("m", time()), date("d",time()), date("Y",time()));
	$ts_yet  														= date("(D) d.m.Y H:i", $CfgDaten["StartTime"]);
	$te_yet  														= date("(D) d.m.Y H:i", time());

	// Überschriften
	$CfgDaten['title']['text'] 								= "";
	$CfgDaten['title']['text'] 								= NULL;
	$CfgDaten['subtitle']['text'] 							= "Zeitraum: $ts_yet - $te_yet"; // "" = Automatisch über Zeitraum
	$CfgDaten['subtitle']['Ips']['DateTimeFormat'] 		= "(D) d.m.Y H:i"; 			// z.B.: "(D) d.m.Y H:i" (wird auch als Default herangezogen wenn nichts konfiguriert wurde)

	// IPS Variablen ID´s
	$CfgDaten["ContentVarableId"]								= get_ControlId(c_Control_HCQueue,get_CirclyIdByCircleIdent(c_Control_HightChart));
//	$CfgDaten["ContentVarableId"]								= 15439 ;  // ID der String Variable in welche die Daten geschrieben werden (-1 oder überhaupt nicht angeben wenn die Content Variable das übergordnete Element ist)

	// damit wird die Art des Aufrufes festgelegt
	$CfgDaten["RunMode"] 										= "file"; 	// file, script oder popup
	if ($CfgDaten["RunMode"] 									== "popup")
	{
		$CfgDaten["WebFrontConfigId"] 						= NULL;
		$CfgDaten["WFCPopupTitle"] 							= "Ich bin der Text, welcher als Überschrift im Popup gezeigt wird";
	}

	// Serienübergreifende Einstellung für das Laden von Werten
	$CfgDaten["AggregatedValues"]["HourValues"] 			= 100;      // ist der Zeitraum größer als X Tage werden Stundenwerte geladen
	$CfgDaten["AggregatedValues"]["DayValues"] 			= 100;       // ist der Zeitraum größer als X Tage werden Tageswerte geladen
	$CfgDaten["AggregatedValues"]["WeekValues"] 			= 100;      // ist der Zeitraum größer als X Tage werden Wochenwerte geladen
	$CfgDaten["AggregatedValues"]["MonthValues"] 		= 100;      // ist der Zeitraum größer als X Tage werden Monatswerte geladen
	$CfgDaten["AggregatedValues"]["YearValues"] 			= -1;      	// ist der Zeitraum größer als X Tage werden Jahreswerte geladen
	$CfgDaten["AggregatedValues"]["NoLoggedValues"] 	= 1000; 	// ist der Zeitraum größer als X Tage werden keine Boolean Werte mehr geladen, diese werden zuvor immer als Einzelwerte geladen	$CfgDaten["AggregatedValues"]["MixedMode"] = false;     // alle Zeitraumbedingungen werden kombiniert
	$CfgDaten["AggregatedValues"]["MixedMode"] 			= false;

	// Die Parameter für die einzelnen Chart Serien (Achtung! unbedingt auf Groß-Kleinschreibung achten)

	// IPS QUEUE Items
   $serie['Data'] 												= $HC_Data_MQ_Items;
   $serie['Name'] 												= "Message Queue Items";
   $serie['Unit'] 												= "";
	$serie['AggValue'] 											= "Avg";
	$serie['RoundValue'] 										= 0;
	$serie['ReplaceValues'] 									= false;
	$serie['type'] 												= "spline";
	$serie['step'] 												= false;
	$serie['yAxis'] 												= 0;
	$serie['visible'] 											= true;
	$serie['color'] 												= '#C01E1E';
	$serie['shadow'] 												= true;
	$serie['lineWidth'] 											= 1;
	$serie['states']['hover']['lineWidth'] 				= 2;
	$serie['marker']['enabled'] 								= false;
	$serie['marker']['symbol'] 								= 'circle';
	$serie['marker']['states']['hover']['enabled'] 		= true;
	$serie['marker']['states']['hover']['symbol'] 		= 'circle';
	$serie['marker']['states']['hover']['radius'] 		= 4;
	$serie['marker']['states']['hover']['lineWidth'] 	= 1;
	$CfgDaten["Series"][] 										= $serie;

	// IPS Delay
   $serie['Data'] 												= $HC_Data_MQ_Delay;
   $serie['Name'] 												= "Message Queue Delay";
   $serie['Unit'] 												= "ms";
	$serie['type'] 												= "spline";
	$serie['AggValue'] 											= "Avg";
	$serie['RoundValue'] 										= 0;
	$serie['ReplaceValues'] 									= false;
	$serie['step'] 												= false;
	$serie['yAxis'] 												= 1;
	$serie['visible'] 											= true;
	$serie['color'] 												= '#428900';
	$serie['shadow'] 												= true;
	$serie['lineWidth'] 											= 1;
	$serie['states']['hover']['lineWidth'] 				= 2;
	$serie['marker']['enabled'] 								= false;
	$serie['marker']['symbol'] 								= 'circle';
	$serie['marker']['states']['hover']['enabled'] 		= true;
	$serie['marker']['states']['hover']['symbol'] 		= 'circle';
	$serie['marker']['states']['hover']['radius'] 		= 4;
	$serie['marker']['states']['hover']['lineWidth'] 	= 1;
	$CfgDaten["Series"][] 										= $serie;

	// IPS Delay
   $serie['Data'] 												= $HC_Data_VM_Delay;
   $serie['Name'] 												= "VM Delay";
   $serie['Unit'] 												= "ms";
	$serie['type'] 												= "spline";
	$serie['AggValue'] 											= "Avg";
	$serie['RoundValue'] 										= 0;
	$serie['ReplaceValues'] 									= false;
	$serie['step'] 												= false;
	$serie['yAxis'] 												= 2;
	$serie['visible'] 											= true;
	$serie['color'] 												= '#FFA600';
	$serie['shadow'] 												= true;
	$serie['lineWidth'] 											= 1;
	$serie['states']['hover']['lineWidth'] 				= 2;
	$serie['marker']['enabled'] 								= false;
	$serie['marker']['symbol'] 								= 'circle';
	$serie['marker']['states']['hover']['enabled'] 		= true;
	$serie['marker']['states']['hover']['symbol'] 		= 'circle';
	$serie['marker']['states']['hover']['radius'] 		= 4;
	$serie['marker']['states']['hover']['lineWidth'] 	= 1;
	$CfgDaten["Series"][] 										= $serie;

//print_r($CfgDaten);

	// Achsen-Definitionen +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

   // Definition Y-Achse 1
   // Definition Y-Achse 2
   $Ai                                                = 0;
	$CfgDaten["yAxis"][$Ai]['title']['text'] 				= "Items";
	$CfgDaten["yAxis"][$Ai]['opposite'] 					= false;
	$CfgDaten["yAxis"][$Ai]['tickInterval'] 				= NULL;
	$CfgDaten["yAxis"][$Ai]['min'] 							= 0;
	$CfgDaten["yAxis"][$Ai]['max'] 							= NULL;
	$CfgDaten["yAxis"][$Ai]['labels']['style']['color']= $CfgDaten["Series"][0]['color'] ;
   $CfgDaten["yAxis"][$Ai]['title']['style']['color']	= $CfgDaten["yAxis"][$Ai]['labels']['style']['color'];

   // Definition Y-Achse 2
   $Ai                                                = 1;
	$CfgDaten["yAxis"][$Ai]['title']['text'] 				= "Message Queue Delay (ms)";
	$CfgDaten["yAxis"][$Ai]['opposite'] 					= true;
	$CfgDaten["yAxis"][$Ai]['tickInterval'] 				= NULL;
	$CfgDaten["yAxis"][$Ai]['min'] 							= 0;
	$CfgDaten["yAxis"][$Ai]['max'] 							= NULL;
	$CfgDaten["yAxis"][$Ai]['labels']['style']['color']= $CfgDaten["Series"][1]['color'];
   $CfgDaten["yAxis"][$Ai]['title']['style']['color']	= $CfgDaten["yAxis"][$Ai]['labels']['style']['color'];

   // Definition Y-Achse 3
   $Ai                                                = 2;
	$CfgDaten["yAxis"][$Ai]['title']['text'] 				= "VM Delay (ms)";
	$CfgDaten["yAxis"][$Ai]['opposite'] 					= true;
	$CfgDaten["yAxis"][$Ai]['tickInterval'] 				= NULL;
	$CfgDaten["yAxis"][$Ai]['min'] 							= 0;
	$CfgDaten["yAxis"][$Ai]['max'] 							= NULL;
	$CfgDaten["yAxis"][$Ai]['labels']['style']['color']= $CfgDaten["Series"][2]['color'];
   $CfgDaten["yAxis"][$Ai]['title']['style']['color']	= $CfgDaten["yAxis"][$Ai]['labels']['style']['color'];

	// *** exporting *** http://www.highcharts.com/ref/#exporting
	$CfgDaten['exporting']['enabled'] 						= false;
	$CfgDaten['navigation']['enabled'] 						= false;

	// plotOptions
	$CfgDaten['plotOptions']['column']['borderColor']	= "#666666";
	$CfgDaten['plotOptions']['column']['borderWidth']	= 1;
	$CfgDaten['plotOptions']['pie']['borderColor']		= "#333333";
	$CfgDaten['plotOptions']['pie']['borderWidth']		= 0;

	// lang
	$CfgDaten['lang']['decimalPoint']						= ",";
	$CfgDaten['lang']['thousandsSep']						= ".";

	// Chart-Optionen "Tooltip"
   $CfgDaten['tooltip']['useHTML'] 							= true;
   $CfgDaten['tooltip']['shared'] 							= true;
   $CfgDaten['tooltip']['crosshairs'][] 					= array('width' =>1,'color' =>'grey','dashStyle'=>'dashdot' );
	$CfgDaten['tooltip']['crosshairs'][] 					= array('width' =>1,'color' =>'grey','dashStyle'=>'dashdot' );
	$CfgDaten['tooltip']['formatter'] 						= "@function() {
	                                                      var s;
															            var s = '<b>' + Highcharts.dateFormat('%H:%M', this.x) + ' Uhr</b>';
															            $.each(this.points, function(i, point)
																				{var unit = {
																			'".@$CfgDaten['Series'][0]['Name']."': '".@$CfgDaten['Series'][0]['Unit']."',
																			'".@$CfgDaten['Series'][1]['Name']."': '".@$CfgDaten['Series'][1]['Unit']."',
																			'".@$CfgDaten['Series'][2]['Name']."': '".@$CfgDaten['Series'][2]['Unit']."',
																			'".@$CfgDaten['Series'][3]['Name']."': '".@$CfgDaten['Series'][3]['Unit']."',
																			'".@$CfgDaten['Series'][4]['Name']."': '".@$CfgDaten['Series'][4]['Unit']."',
																			'".@$CfgDaten['Series'][5]['Name']."': '".@$CfgDaten['Series'][5]['Unit']."',
																			'".@$CfgDaten['Series'][6]['Name']."': '".@$CfgDaten['Series'][6]['Unit']."',
																			'".@$CfgDaten['Series'][7]['Name']."': '".@$CfgDaten['Series'][7]['Unit']."',
																			'".@$CfgDaten['Series'][8]['Name']."': '".@$CfgDaten['Series'][8]['Unit']."',
																			'".@$CfgDaten['Series'][9]['Name']."': '".@$CfgDaten['Series'][9]['Unit']."',
																			}[point.series.name];
																			s += '<br>' + this.series.name + ': ' + '<b><span style=color:' + this.series.color + '>'
								 													+ this.y + unit + '</b></span>'
														             	});
																			return s;
																			}@";

	// Highcharts-Theme
	$CfgDaten['HighChart']['Theme']			="ips.js";   // von Highcharts mitgeliefert: dark-green.js, dark-blue.js, gray.js, grid.js
	//$CfgDaten['HighChart']['Theme']				="rs_net.js";   // IPS-Theme muss per Hand in in Themes kopiert werden....

	// Abmessungen des erzeugten Charts
	$CfgDaten['HighChart']['Width'] 				= 0; 	// in px,  0 = 100%
	$CfgDaten['HighChart']['Height'] 			= 300; 	// in px

	// -------------------------------------------------------------------------------------------------------------------------------------
	// und jetzt los ......
//	$s=IPS_GetScript($CfgDaten["HighChartScriptId"]);      // Id des Highcharts-Scripts
//	include($s['ScriptFile']);

  	// => ab V1.0003
  	// hier werden die CfgDaten geprüft und bei Bedarf vervollständigt
	$CfgDaten = CheckCfgDaten($CfgDaten);

	// => ab V1.0003 neu, ab V1.0006 Erweiterung Parameter "popup"
	if (isset($CfgDaten["RunMode"])
		&& ($CfgDaten["RunMode"] == "script" || $CfgDaten["RunMode"] == "popup"))
	{
		// Variante1: Übergabe der ScriptId. Daten werden beim Aufruf der PHP Seite erzeugt und direkt übergeben. Dadurch kann eine autom. Aktualisierung der Anzeige erfolgen
		if ($IPS_SENDER != "WebInterface")
		{
			WriteContentWithScriptId ($CfgDaten, $IPS_SELF);     	// und jetzt noch die ContentTextbox
			return;                                               // Ende, weil durch die Zuweisung des Script sowieso nochmals aufgerufen wird
		}

		$sConfig = CreateConfigString($CfgDaten);             	// erzeugen und zurückgeben des Config Strings
	}
	else
	{
		//Variante2: Übergabe des Textfiles. Daten werden in tmp-File gespeichert. Eine automatische Aktualisierung beim Anzeigen der Content-Textbox erfolgt nicht
		$sConfig = CreateConfigString($CfgDaten);             // erzeugen und zurückgeben des Config Strings

		$tmpFilename = CreateConfigFile($sConfig, $IPS_SELF);            // und ab damit ins tmp-Files
		if ($IPS_SENDER != "WebInterface")
		{
			WriteContentWithFilename ($CfgDaten, $tmpFilename);        // und jetzt noch die ContentTextbox
		}
	}

?>