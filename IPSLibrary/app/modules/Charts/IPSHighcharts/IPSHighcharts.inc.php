<?
	/**@defgroup ipshighcharts IPSHighcharts
	 * @ingroup charts
	 * @{
	 *
	 * @file          IPSHighcharts.ips.php
	 * @author        khc
	 * @version
	 *  Version 2.50.0, 01.06.2012 khc: Initiale Version 2.02 im Forums Thread<br/>
	 *  Version 2.50.1, 05.10.2012  ab: Integration IPSLibrary<br/>
	 *
	 * IPSHighcharts, ermöglich Darstellung von Charts im Webfront mit Hilfe von "Highcharts" (www.highcharts.com)
	 *
	 */

	 $version = "2.03"; $versionDate = "05.10.2012";

	//ToDo:
	// FEATURE: Plotbands. Timestamp in From und To
	// Fehlerquelle: AggType ist "Größer" als der angezeigte Zeitraum
	// vielleicht alles als cfg direkt json_encoden und nicht jedes Teil einzeln
	//--------------------------------------------------------------------------------------------------------------------------------
	// Für die Darstellung der Graphen wird das HTML5/JS Framework "Highcharts" der Fa. Highslide Software verwendet (www.highcharts.com)
	// Alle Rechte dieses Frameworks liegen bei Highslide Software.
	// 'Highcharts' kann unter folgenden Bedinungen kostenlos eingesetzt werden:
	//  * Namensnennung - Sie müssen den Namen des Autors/Rechteinhabers in der von ihm festgelegten Weise nennen.
	//  * Keine kommerzielle Nutzung - Dieses Werk bzw. dieser Inhalt darf nicht für kommerzielle Zwecke verwendet werden.
	// Download: wwww.highcharts.com/download/ ... und die Dateien einfach in das Webfront (Es sollte ein V 2.2 oder höher verwendet werden.
	// Demos: http://www.highcharts.com/demo/
	// API: http://www.highcharts.com/ref/
	//--------------------------------------------------------------------------------------------------------------------------------
	// Changelog:
	// --- V2.00 ---------------------------------------------------------------------------------------------------------------------
	// 10/2012     AB    NEU      Integration IPSLibrary
	// 04/2012     KHC   REFACT   Umfangreiches Überarbeiten der Highchart-Script Funktionen.
	// bis                        Integration der meisten Original-Highcharts-Options als PHP-Array (siehe http://www.highcharts.com/ref)
	// 05/2012                    Highcharts-Options "lang" aus IPS_Template.php in Highcharts-Script verschoben
	//	--- V2.01 ---------------------------------------------------------------------------------------------------------------------
	// 07.05.2012  KHC   NEU      Test mit Integration Highstock. Neuer Parameter ['Ips']['ChartType'] = 'Highcharts' oder 'Highstock'
	// 07.05.2012  KHC   NEU      IPS_Template.php auf jquery 1.7.2 geändert
	// 07.05.2012  KHC   FIX      krsort durch array_reverse getauscht, da krsort Probleme beim json_encode macht
	// 08.05.2012  KHC   REFACT   intern noch mehr auf Arrays umgestellt und etwas umstrukturiert
	// 09.05.2012  KHC   NEU      über 'CreateConfigFileByPathAndFilename($stringForCfgFile, $path, $filename)' kann eine Tmp_datei mit bel. Namen geschrieben werden
	// 10.05.2012  KHC   FIX      Fehler beim Auswerten der AggregatedValues behoben (ReadDataFromDBAndCreateDataArray)
	// 12.05.2012  KHC   FIX      Tooltip für "ReplaceValues" korrigiert
	// 12.05.2012  KHC   CHANGE   Start- und Endzeitpunkt der X-Achse wurde automatisch um 5 Minuten korrigiert -> dies wurde entfernt
	// 12.05.2012  KHC   NEU      mit ['xAxis']['min']=false und ['xAxis']['min']=false kann festeglegt werden dass Min oder Max nicht autom. festgelegt werden
	//	--- V2.02 ---------------------------------------------------------------------------------------------------------------------
	// 13.05.2012  KHC   FIX      RunType=file: Wenn Highstock vorgewählt wurde wurde das tmp File nicht in die Highstock-Verzeichnis geschrieben
	// 16.05.2012  KHC   NEU      Integration Highstock: ['Navigator'], ['rangeSelector'] und ['scrollbar']
	// 18.05.2012  KHC   FIX      Integration Highstock: Zusätzliche series.type 'candlestick' und 'ohlc' erlauben
	// 19.05.2012  KHC   NEU      Neue Parameter ['Ips']['Dashboard'] für die Darstellung im Dashboard

	//--------------------------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------
	// WriteContentWithFilename
	//    Mit dieser Funktion wird der Content-String geschrieben.
	//    IN: $cfg = ..
	//    IN: $tmpFilename = Der Dateiname welche die Config Daten enthält
	// ------------------------------------------------------------------------
	function WriteContentWithFilename($cfg, $tmpFilename)
	{
		DebugModuleName($cfg,"WriteContentWithFilename");

		if ($tmpFilename != "")
		{
			SetValue($cfg['ContentVarableId'],
						GetContentVariableString ($cfg, "CfgFile", $tmpFilename));
		}
		else
			SetValue($cfg['ContentVarableId'], 'Falsche Parameter beim Funktionsaufruf "WriteContentTextbox"');
	}

	// ------------------------------------------------------------------------
	// WriteContentWithScriptId
	//    Mit dieser Funktion wird der Content-String geschrieben.
	//    IN: $cfg = ..
	//    IN: $scriptId = Die Script Id welche den ConfigString enthält.
	// ------------------------------------------------------------------------
	function WriteContentWithScriptId($cfg, $scriptId)
	{
		DebugModuleName($cfg,"WriteContentWithScriptId");

		if ($cfg['RunMode'] == "popup")
		{
			WFC_SendPopup($cfg['WebFrontConfigId'],
						$cfg['WFCPopupTitle'] ,
						GetContentVariableString ($cfg, "ScriptId", $scriptId));
		}
		else
		{
			SetValue($cfg['ContentVarableId'],
				GetContentVariableString ($cfg, "ScriptId", $scriptId));
		}
	}
	
	// ------------------------------------------------------------------------
	// 05.10.2012  ab:  Adapted Path to Templates
	function GetContentVariableString($cfg, $callBy, $callIdent)
	{
		$chartType = $cfg['Ips']['ChartType'];
		$height = $cfg['HighChart']['Height'] + 16;

		if (isset($cfg['Ips']['Dashboard']['Ip']) && isset($cfg['Ips']['Dashboard']['Port']))
		{
			$s = "http://" . $cfg['Ips']['Dashboard']['Ip'] . ":" . $cfg['Ips']['Dashboard']['Port'] .
				"/user/IPSHighcharts/IPSTemplates/$chartType.php?$callBy="	. $callIdent . " " .
				"width='100%' height='". $height ."' frameborder='1' scrolling='no'";
		}
		else
		{
			$s = "<iframe src='/user/IPSHighcharts/IPSTemplates/$chartType.php?$callBy="	. $callIdent . "' " .
				"width='100%' height='". $height ."' frameborder='0' scrolling='no'></iframe>";
		}
		return $s;
	}

	// ------------------------------------------------------------------------
	// CreateConfigFile
	//    Erzeugt das tmp-Highcharts Config-File mit der $id als Dateinamen
	//    IN: $stringForCfgFile = String welcher in das File geschrieben wird
	// ------------------------------------------------------------------------
	function CreateConfigFile($stringForCfgFile, $id, $charttype = 'Highcharts')
	{
		//$path     = "webfront\user\IPSHighcharts\\" . $charttype;
		$path     = "user\IPSHighcharts\\" . $charttype;
		//to be compatible for older IPS versions (<7.0)
		if (file_exists(IPS_GetKernelDir() . 'webfront')) {
			$path = 'webfront\\' . $path;
		} 

		return CreateConfigFileByPathAndFilename($stringForCfgFile, $path, $filename);
	}

	// ------------------------------------------------------------------------
	// CreateConfigFileByPathAndFilename
	//    Erzeugt das tmp-Highcharts Config-File
	//    IN: $stringForCfgFile = String welcher in das File geschrieben wird
	// 		 $path, $filename = Pfad un Name des Tmp-Files welches erzeugt werden soll
	// ------------------------------------------------------------------------
	function CreateConfigFileByPathAndFilename($stringForCfgFile, $path, $filename)
	{
		// Standard-Dateiname .....
		$tmpFilename = IPS_GetKernelDir() . $path . "\\" . $filename;

		// schreiben der Config Daten
		$handle = fopen($tmpFilename,"w");
		fwrite($handle, $stringForCfgFile);
		fclose($handle);

		return $tmpFilename;
	}

	// ------------------------------------------------------------------------
	// CheckCfgDaten
	//    Aufruf bei jedem Cfg-Start
	//    IN: $cfg = ..
	//    OUT: korrigierte cfg
	// ------------------------------------------------------------------------
 	function CheckCfgDaten($cfg)
	{
      DebugModuleName($cfg,"CheckCfgDaten");

		global $_IPS;

		// Debugging
		IfNotIssetSetValue($cfg['Ips']['Debug']['Modules'], 			false);
		IfNotIssetSetValue($cfg['Ips']['Debug']['ShowJSON'], 			false);
		IfNotIssetSetValue($cfg['Ips']['Debug']['ShowJSON_Data'], 	false);
		IfNotIssetSetValue($cfg['Ips']['Debug']['ShowCfg'], 			false);

		// ChartType
		IfNotIssetSetValue($cfg['Ips']['ChartType'], 'Highcharts');

   	if ($cfg['Ips']['ChartType'] != 'Highcharts' && $cfg['Ips']['ChartType'] != 'Highstock')
		   die ("Abbruch! Es sind nur 'Highcharts' oder 'Highstock' als ChartType zulässig");

		// über WebInterface kommt der Aufruf wenn die Content-Variable aktualisiert wird
		if ($_IPS['SENDER'] != "WebInterface" && $cfg['RunMode'] != "popup")
			$cfg = Check_ContentVariable($cfg, $_IPS['SELF']);

	   return $cfg;
	}

	// ------------------------------------------------------------------------
	// CreateConfigString
	//    Erzeugt den für Higcharts benötigten Config String und gibt diesen als String zurück
	//    IN: $cfg = ..
	//    OUT: der erzeugte Config String
	// ------------------------------------------------------------------------
	function CreateConfigString($cfg)
	{
      DebugModuleName($cfg,"CreateConfigString");

		$cfg = CompatibilityCheck($cfg);
      $cfg = CheckCfg($cfg);

		$cfgString = GetHighChartsCfgFile($cfg);

		// Zusätzliche Config in Highchart Config hinzufügen
		$cfgString = ReadAdditionalConfigData($cfg) . "\n|||\n" .  $cfgString;

		// Language Options aus IPS_Template.php hierher verschoben
		$cfgString .=  "\n|||\n". GetHighChartsLangOptions($cfg);;

		return $cfgString;
	}

	function CompatibilityCheck($cfg)
	{
		DebugModuleName($cfg,"CompatibilityCheck");

		// Series
		if (isset($cfg['Series']) && isset($cfg['series']))
			die ("Abbruch - Es düfen nicht gleichzeitig 'Series' und 'series' definiert werden.");
		if (isset($cfg['Series']) && !isset($cfg['series']))
			$cfg['series'] = $cfg['Series'];
		unset ($cfg['Series']);

		// Title
		if (isset($cfg['Title']) && !isset($cfg['title']['text']))
			$cfg['title']['text'] = $cfg['Title'];
		unset ($cfg['Title']);

		// SubTitle
		if (isset($cfg['SubTitle']) && !isset($cfg['subtitle']['text']))
			$cfg['subtitle']['text'] = $cfg['SubTitle'];
		unset ($cfg['SubTitle']);

		// SubTitleDateTimeFormat
		if (isset($cfg['SubTitleDateTimeFormat']) && !isset($cfg['subtitle']['Ips']['DateTimeFormat']))
			$cfg['subtitle']['Ips']['DateTimeFormat'] = $cfg['SubTitleDateTimeFormat'];
		unset ($cfg['SubTitleDateTimeFormat']);

		// yAxis
		if (isset($cfg['yAxis']))
		{
			$axisArr = array();
			foreach ($cfg['yAxis'] as $Axis)
			{
				$cfgAxis = $Axis;

				// Name
				if (isset($Axis['Name']) && !isset($cfgAxis['title']['text']))
					$cfgAxis['title']['text'] = $Axis['Name'];
				unset ($cfgAxis['Name']);

				// TickInterval
				if (isset($Axis['TickInterval']) && !isset($cfgAxis['tickinterval']))
						$cfgAxis['tickinterval'] = $Axis['TickInterval'];
				unset ($cfgAxis['TickInterval']);

				// Opposite
				if (isset($Axis['Opposite']) && !isset($cfgAxis['opposite']))
						$cfgAxis['opposite'] = $Axis['Opposite'];
				unset ($cfgAxis['Opposite']);

	      	$axisArr[] = $cfgAxis;
			}
	      $cfg['yAxis'] = $axisArr;
		}
  		return $cfg;
	}

	// ------------------------------------------------------------------------
	// CheckCfg
	//    Prüft daKonfiguration und korrigiert und Vervollständigtdiese zum Teil
	//    IN: $cfg = ..
	//    OUT: der erzeugte Config String
	// ------------------------------------------------------------------------
	function CheckCfg($cfg)
	{
		DebugModuleName($cfg,"CheckCfg");

		$cfg = CheckCfg_Common($cfg);
		$cfg = CheckCfg_AreaHighChart($cfg);
		$cfg = CheckCfg_AggregatedValues($cfg);
  		$cfg = CheckCfg_StartEndTime($cfg);
  		$cfg = CheckCfg_Series($cfg);

  		return $cfg;
	}

	// ------------------------------------------------------------------------
	// CheckCfg_Common
	//    wenn RunMode=Popup, prüfen der dazugehörigen Daten wie WebfrontConfigId, usw.
	//		und wenn RunMode=Popup, prüfen der dazugehörigen Daten wie WebfrontConfigId, usw.
	//    IN: $cfg
	//    OUT: korrigiertes $cfg
	// ------------------------------------------------------------------------
	function CheckCfg_Common($cfg)
 	{
      DebugModuleName($cfg,"CheckCfg_Common");

		if (!isset($cfg['series']))
			die ("Abbruch - Es wurden keine Serien definiert.");

		// Id des ArchiveHandler auslesen
		if (!isset($cfg['ArchiveHandlerId']) || $cfg['ArchiveHandlerId'] == -1)
		{
      	$instances = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
			$cfg['ArchiveHandlerId'] = $instances[0];
		}
		// Prüfen des ArchiveHandlers
		$instance = @IPS_GetInstance($cfg['ArchiveHandlerId']);
		if ($instance['ModuleInfo']['ModuleID'] != "{43192F0B-135B-4CE7-A0A7-1475603F3060}")
			die ("Abbruch - 'ArchiveHandlerId' (".$cfg['ArchiveHandlerId'].") ist keine Instance eines ArchiveHandler.");

		if ($cfg['RunMode'] == "popup")
		{
			// keine Webfront Id
			if (!isset($cfg['WebFrontConfigId']))
				die ("Abbruch - Konfiguration von 'WebFrontConfigId' fehlt.");

			// prüfen ob die übergebene Id ein WebFront ist
			$instance = @IPS_GetInstance($cfg['WebFrontConfigId']);
			if ($instance['ModuleInfo']['ModuleID'] != "{3565B1F2-8F7B-4311-A4B6-1BF1D868F39E}")
				die ("Abbruch - 'WebFrontConfigId' ist keine WebFrontId");

			IfNotIssetSetValue($cfg['WFCPopupTitle'], "");
		}

	   return $cfg;
	}


	// ------------------------------------------------------------------------
	// Check_ContentVariable
	//    prüfen ob Angaben der Content Variable stimmen oder ob es das übergeordnete Element ist
	//    IN: $cfg
	//    OUT: korrigiertes $cfg
	// ------------------------------------------------------------------------
	function Check_ContentVariable($cfg, $scriptId)
	{
	   DebugModuleName($cfg,"Check_ContentVariable");

		// wenn keine Id übergeben wurde wird das übergeordnete Objekt als Content verwendet
		if (!isset($cfg['ContentVarableId']) || $cfg['ContentVarableId'] <= 0)
			$cfg['ContentVarableId'] = IPS_GetParent($scriptId);

		$variable = @IPS_GetVariable($cfg['ContentVarableId']);
		if ($variable == false)
	   	die ("Abbruch - Content-Variable nicht gefunden.");

		if (isset($variable['VariableValue']['ValueType']) && ($variable['VariableValue']['ValueType'] != 3))
   		die ("Abbruch - Content-Variable ist keine STRING-Variable.");

		if (isset($variable['VariableType']) && ($variable['VariableType'] != 3))
   		die ("Abbruch - Content-Variable ist keine STRING-Variable.");
   
	   if ($variable['VariableCustomProfile'] != "~HTMLBox")
	   	die ("Abbruch - Content-Variable muss als Profil '~HTMLBox' verwenden.");

		return $cfg;
	}

	// ------------------------------------------------------------------------
	// CheckCfg_AreaHighChart
	//
	//    IN: $cfg
	//    OUT: korrigiertes $cfg
	// ------------------------------------------------------------------------
	function CheckCfg_AreaHighChart($cfg)
 	{
	   DebugModuleName($cfg,"CheckCfg_AreaHighChart");

		IfNotIssetSetValue($cfg['HighChart']['Theme'], "");
		IfNotIssetSetValue($cfg['HighChart']['Width'], 0);
		IfNotIssetSetValue($cfg['HighChart']['Height'], 400);

 		return $cfg;
	}

	// ------------------------------------------------------------------------
	// CheckCfg_StartEndTime
	//    Start- und Endzeit des gesamten Charts errechnen, und an jede Serie übergeben
	//    IN: $cfg
	//    OUT: korrigiertes $cfg
	// ------------------------------------------------------------------------
	function CheckCfg_StartEndTime($cfg)
	{
		DebugModuleName($cfg,"CheckCfg_StartEndTime");

		$cfg['Ips']['ChartStartTime'] = $cfg['StartTime'];
		$cfg['Ips']['ChartEndTime'] = $cfg['EndTime'];

		$offsetExistsAtSerie = false;
		$Count = count($cfg['series']);

		for ($i = 0; $i < $Count; $i++)
		{
			$Serie = $cfg['series'][$i];

			// wenn für die Serie keine Start oder Endzeit übergeben würde wird der Standardwert genommen
			IfNotIssetSetValue($Serie['StartTime'], $cfg['StartTime']);
			IfNotIssetSetValue($Serie['EndTime'], $cfg['EndTime']);

			if ($Serie['StartTime'] < $cfg['Ips']['ChartStartTime'])
				$cfg['Ips']['ChartStartTime'] = $Serie['StartTime'];
			if ($Serie['EndTime'] > $cfg['Ips']['ChartEndTime'])
				$cfg['Ips']['ChartEndTime'] = $Serie['EndTime'];

			$Serie['Ips']['EndTimeString'] = date("/r", $Serie['EndTime']);
			$Serie['Ips']['StartTimeString']= date("/r", $Serie['StartTime']);

			$cfg['series'][$i] = $Serie;

			if (isset($Serie['Offset']) && $Serie['Offset'] != 0)
				$offsetExistsAtSerie =true;
		}

		// wenn ein Offset definiert wurde gilt nur der global eingestellte Start und Endzeitpunkt
		if ($offsetExistsAtSerie = true)
      {
         $cfg['Ips']['ChartStartTime'] = $cfg['StartTime'];
			$cfg['Ips']['ChartEndTime'] = $cfg['EndTime'];
      }

		return $cfg;

	}

	// ------------------------------------------------------------------------
	// CheckCfg_Series
	//    prüfen der Serien
	//    IN: $cfg
	//    OUT: korrigiertes $cfg
	// ------------------------------------------------------------------------
	function CheckCfg_Series($cfg)
 	{
		DebugModuleName($cfg,"CheckCfg_Series");

		$Id_AH = $cfg['ArchiveHandlerId'];

		$series = array();
		foreach ($cfg['series'] as $Serie)
		{
			$VariableId = @$Serie['Id'];

			// hier wird nur geprüft ob Wert von Eingabe passen könnte (wenn vorhanden)
			if  (isset($Serie['AggType']) && ($Serie['AggType']<0 || $Serie['AggType']>4) )
				die ("Abbruch - 'AggType' hat keinen korrekten Wert");

			$Serie['Ips']['IsCounter'] = $VariableId && (@AC_GetAggregationType($Id_AH, $VariableId) == 1);

			// über AggValue kann Min/Max oder Avg vorgewählt werden (zum Lesen der AggregValues)
			IfNotIssetSetValue($Serie['AggValue'], "Avg");

			if ($Serie['AggValue'] != "Avg"
				&& $Serie['AggValue'] != "Min"
				&& $Serie['AggValue'] != "Max")
			   	die ("Abbruch - 'AggValue' hat keinen gültigen Wert");

			// Offset für Darstellung von z.B. Monate und Vormonat in einem Chart
			IfNotIssetSetValue($Serie['Offset'], 0);

			IfNotIssetSetValue($Serie['ReplaceValues'], false);

			// Name (Kompatibilität aus V1.x)
			if (isset($Serie['Name']) && !isset($Serie['name']))
				$Serie['name'] = $Serie['Name'];
			unset($Serie['Name']);

			IfNotIssetSetValue($Serie['name'], "");

			// type & Parameter
			if (isset($Serie['type']) && isset($Serie['Param']))
				die ("Abbruch - Definition von 'Param' und 'type' in Serie gleichzeitig nicht möglich.");
			if (!isset($Serie['type']) && !isset($Serie['Param']))
				die ("Abbruch - Serie muss Definition von 'Param' oder 'type' enthalten.");

			// Mögliche Charttypen
			$allowedSeriesTypes = array();
			if ($cfg['Ips']['ChartType'] == 'Highcharts')
				$allowedSeriesTypes = array('area','areaspline','bar','column','line','pie','scatter','spline');
			else if ($cfg['Ips']['ChartType'] == 'Highstock')
				$allowedSeriesTypes = array('area','areaspline','bar','column','line','pie','scatter','spline','ohlc','candlestick');

			if (!isset($Serie['type']) && isset($Serie['Param']))
			{
				// type aus Param übernehmen
				foreach($allowedSeriesTypes as $item)
				{
					if (strrpos($Serie['Param'],"'$item'") > 0)
			   		$Serie['Ips']['Type'] = $item;
				}
			}
			else
			{
				if (!in_array($Serie['type'], $allowedSeriesTypes))
					die ("Abbruch - Serien-Type (" . $Serie['type'] .  ") nicht erkennbar.");
				else
					$Serie['Ips']['Type'] = $Serie['type'];
			}
			if (!isset($Serie['Ips']['Type']))
				die ("Abbruch - Serien-Type nicht erkennbar.");

			// data
			if (isset($Serie['Data']) && isset($Serie['data']))
				die ("Abbruch - Definition von 'Data' und 'data' in ein und derselben Serie nicht mölglich.");
			if (!isset($Serie['data']) && isset($Serie['Data']))
         {
			   $Serie['data'] = $Serie['Data'];
			   unset($Serie['Data']);
			}

			// diverse Prüfungen bei PIE-Charts
			if ($Serie['Ips']['Type'] == 'pie')
			{
            if (isset($Serie['Id']))
            {
					if (!isset($Serie['AggType']))
						die ("Abbruch - Wird ein Pie über Id definiert muss auch AggType parametriert werden");

					// wenn nichts angegeben wird 'AggNameFormat: automatisch abhängig vom 'AggType' berechnet
					if (!isset($Serie['AggNameFormat']))
					{
						if ($Serie['AggType'] == 0)   //0=Hour
							$Serie['AggNameFormat'] = "d.m.Y H:i";
						else if ($Serie['AggType'] == 1) //1=Day
							$Serie['AggNameFormat'] = "d.m.Y";
						else if ($Serie['AggType'] == 2) //2=Week
							$Serie['AggNameFormat'] = "\K\WW Y";
						else if ($Serie['AggType'] == 3) //3=Month
							$Serie['AggNameFormat'] = "M Y";
						else if ($Serie['AggType'] == 4) //4=Year
							$Serie['AggNameFormat'] = "Y";
					}
            }
            else if (isset($Serie['data']))
            {
               foreach($Serie['data'] as $data)
               {
                  if (isset($data['Id']) && isset($data['y']))
                  	die ("Abbruch - Pie['data']: Id und y sind als gleichzeitige Parameter nicht möglich.");
                  //if (!isset($data['Id']) && !isset($data['y']))
                 	// 	die ("Abbruch - Pie['data']: Id oder y muss definiert sein");
                 	// kann man so nicht prüfen
               }
            }
            else
				{
					die ("Abbruch - Pie kann nie Daten besitzen. Es muss entweder über 'Id' oder über 'data' definiert werden.");
				}

			}

			// geänderte Werte wieder zurückschreiben
			$series[] = $Serie;
		}
		// geänderte Werte wieder zurückschreiben

		$cfg['series'] = $series;
 		return $cfg;
	}

	// ------------------------------------------------------------------------
	// CheckCfg_AggregatedValues
	//    prüfen der AggregatedValues und Übernahme dieser in die Serien
	//    IN: $cfg
	//    OUT: korrigiertes $cfg
	// ------------------------------------------------------------------------
 	function CheckCfg_AggregatedValues($cfg)
 	{
		DebugModuleName($cfg,"CheckCfg_AggregatedValues");

		if (!isset($cfg['AggregatedValues']))
			$cfg['AggregatedValues'] = array();

		// Default - wenn nichts vorbelegt
		IfNotIssetSetValue($cfg['AggregatedValues']['MixedMode'], false);
		IfNotIssetSetValue($cfg['AggregatedValues']['HourValues'], -1);
		IfNotIssetSetValue($cfg['AggregatedValues']['DayValues'], -1);
		IfNotIssetSetValue($cfg['AggregatedValues']['WeekValues'], -1);
		IfNotIssetSetValue($cfg['AggregatedValues']['MonthValues'], -1);
		IfNotIssetSetValue($cfg['AggregatedValues']['YearValues'], -1);
		IfNotIssetSetValue($cfg['AggregatedValues']['NoLoggedValues'], 100);

		$series = array();
		foreach ($cfg['series'] as $Serie)
		{

			// prüfen ob für die Serie Einstellungen für AggregatedValues vorhanden sind,
			// wenn nicht Übernahme aus cfg
			if (isset($Serie['AggregatedValues']))
			{
				IfNotIssetSetValue($Serie['AggregatedValues']['MixedMode'], $cfg['AggregatedValues']['MixedMode']);
				IfNotIssetSetValue($Serie['AggregatedValues']['HourValues'], $cfg['AggregatedValues']['HourValues']);
				IfNotIssetSetValue($Serie['AggregatedValues']['DayValues'], $cfg['AggregatedValues']['DayValues']);
				IfNotIssetSetValue($Serie['AggregatedValues']['WeekValues'], $cfg['AggregatedValues']['WeekValues']);
				IfNotIssetSetValue($Serie['AggregatedValues']['MonthValues'], $cfg['AggregatedValues']['MonthValues']);
				IfNotIssetSetValue($Serie['AggregatedValues']['YearValues'], $cfg['AggregatedValues']['YearValues']);
				IfNotIssetSetValue($Serie['AggregatedValues']['NoLoggedValues'], $cfg['AggregatedValues']['NoLoggedValues']);
			}
			else	// nein -> Daten aus übergeordneter cfg übernehmen
				$Serie['AggregatedValues'] = $cfg['AggregatedValues'];

     		// Umrechnen der Tage in Sekunden ... für direktes addieren zum Timestamp
			$MinPerTag = 24*60*60;

			if ($Serie['AggregatedValues']['HourValues'] != -1)
				$Serie['AggregatedValues']['HourValues'] *= $MinPerTag;
			if ($Serie['AggregatedValues']['DayValues'] != -1)
				$Serie['AggregatedValues']['DayValues'] *= $MinPerTag;
			if ($Serie['AggregatedValues']['WeekValues'] != -1)
				$Serie['AggregatedValues']['WeekValues'] *= $MinPerTag;
			if ($Serie['AggregatedValues']['MonthValues'] != -1)
				$Serie['AggregatedValues']['MonthValues'] *= $MinPerTag;
			if ($Serie['AggregatedValues']['YearValues'] != -1)
				$Serie['AggregatedValues']['YearValues'] *= $MinPerTag;
			if ($Serie['AggregatedValues']['NoLoggedValues'] != -1)
				$Serie['AggregatedValues']['NoLoggedValues'] *= $MinPerTag;

			// geänderte Werte wieder zurückschreiben
			$series[] = $Serie;
		}
		// geänderte Werte wieder zurückschreiben
		$cfg['series'] = $series;

		// die sind jetzt nicht mehr nötig.....
		unset($cfg['AggregatedValues']);

		return $cfg;
	}



	// ------------------------------------------------------------------------
	// ReadAdditionalConfigData
	//    zusätzliche Daten für File (hat jetzt aber nichts mit den eigentlichen Highchart Config String zu tun
	//    IN: $cfg
	//    OUT: der String welcher dann in das IPS_Template geschrieben wird.
	// ------------------------------------------------------------------------
	function ReadAdditionalConfigData($cfg)
	{
		DebugModuleName($cfg,"ReadAdditionalConfigData");

		// z.B.: Breite und Höhe für Container
		// Breite und Höhe anpassen für HTML Ausgabe
		$s['Theme'] = $cfg['HighChart']['Theme'];
		if ($cfg['HighChart']['Width'] == 0)
			$s['Width'] = "100%";
		else
			$s['Width'] = $cfg['HighChart']['Width']. "px";

		$s['Height'] = $cfg['HighChart']['Height']. "px";

		return trim(print_r($s, true), "Array\n()") ;
	}

// ***************************************************************************************************************************

	// ------------------------------------------------------------------------
	// GetHighChartsCfgFile
	//    Falls nicht konfiguriert, wird dies als Default String genommen
	//    OUT: natürlich den String ....
	// ------------------------------------------------------------------------
	function GetHighChartsCfgFile($cfg)
	{
		DebugModuleName($cfg,"GetHighChartsCfgFile");

		$cfgArr['chart'] = CreateArrayForChart($cfg);

		if (isset($cfg['colors']))
			$cfgArr['colors'] = $cfg['colors'];

		$cfgArr['credits'] = CreateArrayForCredits($cfg);

		if (isset($cfg['global']))
			$cfgArr['global'] = $cfg['global'];

		if (isset($cfg['labels']))
			$cfgArr['labels'] = $cfg['labels'];

		// $cfg['lang'])) werden seperat behandelt

		if (isset($cfg['legend']))
			$cfgArr['legend'] = $cfg['legend'];

		if (isset($cfg['loading']))
			$cfgArr['loading'] = $cfg['loading'];

		if (isset($cfg['plotOptions']))
			$cfgArr['plotOptions'] = $cfg['plotOptions'];

		$cfgArr['exporting'] = CreateArrayForExporting($cfg);

		if (isset($cfg['symbols']))
			$cfgArr['symbols'] = $cfg['symbols'];

		$cfgArr['title'] = CreateArrayForTitle($cfg);
		$cfgArr['subtitle'] = CreateArrayForSubTitle($cfg);

		$cfgArr['tooltip'] = CreateArrayForTooltip($cfg);

		$cfgArr['xAxis'] = CreateArrayForXAxis($cfg);
		$cfgArr['yAxis'] = CreateArrayForYAxis($cfg);

		if ($cfg['Ips']['ChartType'] == 'Highstock')
		{
			if (isset($cfg['navigator']))
				$cfgArr['navigator'] = $cfg['navigator'];
			if (isset($cfg['rangeSelector']))
				$cfgArr['rangeSelector'] = $cfg['rangeSelector'];
			if (isset($cfg['scrollbar']))
				$cfgArr['scrollbar'] = $cfg['scrollbar'];
		}


		if ($cfg['Ips']['Debug']['ShowJSON'])
			DebugString(my_json_encode($cfgArr));

		$cfgArr['series'] = CreateArrayForSeries($cfg) ;

		if ($cfg['Ips']['Debug']['ShowJSON_Data'])
			DebugString(my_json_encode($cfgArr));

		// Array in JSON wandeln
		$s = my_json_encode($cfgArr);

		// ersetzten des 'Param'-Parameters (Altlast aus V1.x)
		$s = str_replace(",Param@@@:",",",$s);
		$s = trim($s, "{");
		$s .= ");";

		return $s;
	}

	// ------------------------------------------------------------------------
	// GetHighChartsLangOptions
	//
	//    IN: $cfg
	//    OUT: JSON Options String für den Bereich 'lang'
	// ------------------------------------------------------------------------
	function GetHighChartsLangOptions($cfg)
	{
		DebugModuleName($cfg,"GetHighChartsLangOptions");

		// Default
		IfNotIssetSetValue($cfg['lang']['decimalPoint'], ",");
		IfNotIssetSetValue($cfg['lang']['thousandsSep'], ".");

		IfNotIssetSetValue($cfg['lang']['months'], ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']);
		IfNotIssetSetValue($cfg['lang']['shortMonths'], ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez']);
		IfNotIssetSetValue($cfg['lang']['weekdays'], ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag']);

		$s = "lang:" . my_json_encode($cfg['lang']);

		return $s;
	}


	// ------------------------------------------------------------------------
	// CreateArrayForSeries
	//
	//    IN: $cfg
	//    OUT: der String welcher dann in das IPS_Template geschrieben wird.
	// ------------------------------------------------------------------------
	function CreateArrayForSeries($cfg)
	{
		DebugModuleName($cfg,"CreateArrayForSeries");

		// Daten für einzelne Serien erzeugen
		$dataArr = array();
		foreach ($cfg['series'] as $Serie)
		{
			if ($Serie['Ips']['Type'] == 'pie')
			{
				$Serie['data'] = CreateDataArrayForPie($cfg, $Serie);
			}
			else
			{
            // Daten wurden von extern übergeben
				if (isset($Serie['data']))
				{
					if (is_array($Serie['data']))
						$Serie['data'] = CreateDataArrayFromExternalData($Serie['data'], $Serie);
					else
						$Serie['data'] = $Serie['data'];

				}
				// Daten werden aus DB gelesen
				else
					$Serie['data'] = ReadDataFromDBAndCreateDataArray($cfg, $Serie);
			}

			// ... aus Serie umkopieren
			$serieArr = $Serie;

			// nicht für JSON benötigte Parameter löschen
			unset($serieArr['Param']);
			unset($serieArr['AggregatedValues']);
			unset($serieArr['Unit']);
			unset($serieArr['StartTime']);
			unset($serieArr['EndTime']);
			unset($serieArr['ReplaceValues']);
			unset($serieArr['Ips']);
			unset($serieArr['Offset']);
			unset($serieArr['AggValue']);
			unset($serieArr['AggType']);
			unset($serieArr['AggNameFormat']);
			unset($serieArr['ScaleFactor']);
			unset($serieArr['RoundValue']);

			// ersetzten des 'Param'-Parameters (Altlast aus V1.x)
			if (isset($Serie['Param']))
				$serieArr['Param@@@'] = "@" . $Serie['Param'] . "@";

			$dataArr[] = $serieArr;
		}

		return $dataArr;
	}

	// ------------------------------------------------------------------------
	// PopulateDate
	//
	//    IN: $dt
	//			 $serie
	//    OUT: Date-Value für Data-String
	// ------------------------------------------------------------------------
	function PopulateDate($dt, $serie)
	{
		if ($dt < $serie['StartTime'])
			$dt = $serie['StartTime'] ;

		// z.B.: Date.UTC(2011,4,27,19,42,19),23.4
		return  CreateDateUTC($dt + $serie['Offset']);
	}

	// ------------------------------------------------------------------------
	// PopulateValue
	//
	//    IN: $val
	//			 $serie
	//    OUT: korrigiertes $cfg
	// ------------------------------------------------------------------------
	function PopulateValue($val, $serie)
	{
		// Werte ersetzten (sinnvoll für Boolean, oder Integer - z.B.: Tür/Fenster-Kontakt oder Drehgriffkontakt)
		if ($serie['ReplaceValues'] != false)
		{
			if (is_int($val) && isset($serie['ReplaceValues'][$val]))
				$val = $serie['ReplaceValues'][$val];
		}

		// Skalieren von Loggingdaten
		if (isset($serie['ScaleFactor']))
			$val = $val * $serie['ScaleFactor'];

		// Rounden von Nachkommastellen
		if (isset($serie['RoundValue']))
			$val = round($val, $serie['RoundValue']);


		return $val;
	}

	// ------------------------------------------------------------------------
	// CreateDataArrayForPie
	//    Liest die aktuellen Werte aus den übergebenen Variablen und erzeugt die Daten für das PIE
	//    IN: $cfg, $Serie
	//    OUT: der Data String
	// ------------------------------------------------------------------------
	function CreateDataArrayForPie($cfg, $serie)
	{
		DebugModuleName($cfg,"CreateDataArrayForPie");

		if (isset($serie['Id']))
		{
		  	return ReadPieDataById($cfg, $serie);
		}
		else if (isset($serie['data']))
		{
			$result = array();
			foreach($serie['data'] as $item)
			{
			   if (isset($item['Id']))
			   {
				   $currentValue = ReadCurrentValue($item['Id']);
				   $item['y'] = PopulateValue($currentValue['Value'], $serie) ;
			   }
			   $result[] = $item;
			}
			return $result;
		}
		else
		{
		   Die ("Abbruch - Pie-Definition nicht korrekt");
		}
		return $Data;
	}

	// ------------------------------------------------------------------------
	// ReadPieDataById
	//    liest die Aggregated-Werte einer einer Vriablen aus und erzeugt das entsprechende Array
	//    IN: $cfg, $serie
	//    OUT: Config Array
	// ------------------------------------------------------------------------
	function ReadPieDataById($cfg, $serie)
	{
		$id_AH = $cfg['ArchiveHandlerId'];

		$tempData = @AC_GetAggregatedValues($id_AH, $serie['Id'], $serie['AggType'], $serie['StartTime'], $serie['EndTime'], 0);
		$tempData = array_reverse($tempData);

		$result = array();
		foreach ($tempData as $ValueItem)
	   {
			$item['name'] = ReplaceToGermanDate(date($serie['AggNameFormat'], $ValueItem['TimeStamp']));
			$item['y'] = PopulateValue($ValueItem[$serie['AggValue']], $serie);
			$result[] = $item;
	   }
	   unset ($tempData);

	   return $result;
	}

	// ------------------------------------------------------------------------
	// CalculateStartAndEndTimeForAggreagtedValues
	//       Liest den Start- und Endzeitpunkt des angefragten Bereiches
	//    IN: $Serie, $search : "" für alle Werte, "Hour", "Day", usw
	//    OUT: Array(StartTime,EndTime)
	// ------------------------------------------------------------------------
	function CalculateStartAndEndTimeForAggreagtedValues($Serie, $search ="")
	{
		$start = -1;		$ende = -1;
		$trap = false;
		$sum = 0;

		if ($search == "")
		{
		   $search =="Values";
		   $start = 0;
		   $trap = true;
		}
		foreach($Serie['AggregatedValues'] as $key => $value)
		{
			if (strrpos ($key, "Values") != false)
			{
				if ($value > 0)
					$sum += $value;

				if (strrpos ($key, $search) !== false)
				{
					$trap = true;
					if ($value == -1)
					   return false;
				}

				if (!$trap)
				   continue;

			   if ($value < 0)
			   	continue;

				if ($start == -1)
				{
					$start =  $sum;
			   	continue;
				}

				if ($start != -1 && $ende ==-1)
				{
					$ende =  $sum;
					break;
				}
			}
		}

		$result = false;
		if ($start != -1)
		{
         $result["EndTime"] = $Serie["EndTime"] - $start;
			if ($ende == -1)
	      	$result["StartTime"] = $Serie["StartTime"];
			else
				$result["StartTime"] = $Serie["EndTime"] - $ende;

			if ($result["StartTime"] < $Serie["StartTime"])
			 	$result["StartTime"] = $Serie["StartTime"];

		 	if ($result["StartTime"] == $Serie["EndTime"])
			 	$result = false;
		}

		return $result;
	}

	// ------------------------------------------------------------------------
	// ReadDataFromDBAndCreateDataArray
	//    Liest die Series-Daten aus der DB und schreibt sie in den DataString
	//    IN: $cfg, $Serie
	//    OUT: der Data String
	// ------------------------------------------------------------------------
	function ReadDataFromDBAndCreateDataArray($cfg, $Serie)
	{
		DebugModuleName($cfg,"ReadDataFromDBAndCreateDataArray");

		if (!isset($Serie['Id']))
		   return "";

		// errechne die Zeitspanne
		if ($Serie['EndTime'] > time())
			$Diff = time() - $Serie['StartTime'];
		else
			$Diff = $Serie['EndTime'] - $Serie['StartTime'];



		$Id_AH = $cfg['ArchiveHandlerId'];
		$dataArray = array();
		$VariableId = (int)$Serie['Id'];
		$Agg = -1;
		$ReadCurrentValue = true;

		// wenn ReplaceValues definiert wurden werden nur geloggte und keine Aggregated Werte gelesen
		if ($Serie['ReplaceValues'] != false)
		{
			if ($Diff > $Serie['AggregatedValues']['NoLoggedValues'])
			{
				$Serie['StartTime'] = $Serie['EndTime'] - $Serie['AggregatedValues']['NoLoggedValues'];
			}

			// Einzelwerte lesen
		   $dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, -1 , $Serie["StartTime"], $Serie["EndTime"], "Value", $Serie);
		}
		else if ($Serie['AggregatedValues']['MixedMode'])    // im MixedMode werden anfangs alle Werte, dann die Stunden- und zuletzt Tageswerte ausgelesen
		{
			// zuerst Einzelwerte
			$result = CalculateStartAndEndTimeForAggreagtedValues($Serie, "");
			if ($result != false)
			{
				if ($Serie['Ips']['IsCounter']) 						// wenn Zähler dann immer Agg.Values
					$dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, 0, $result["StartTime"], $result["EndTime"], $Serie['AggValue'], $Serie);
				else
   			   $dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, -1 , $result["StartTime"], $result["EndTime"], "Value", $Serie);
			}

			// -> Stundenwerte
			$result = CalculateStartAndEndTimeForAggreagtedValues($Serie,"Hour");
			if ($result != false)
				$dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, 0, $result["StartTime"], $result["EndTime"], $Serie['AggValue'], $Serie);

			// -> Tageswerte
			$result = CalculateStartAndEndTimeForAggreagtedValues($Serie,"Day");
			if ($result != false)
				$dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, 1, $result["StartTime"], $result["EndTime"], $Serie['AggValue'], $Serie);

			// -> Wochenwerten
			$result = CalculateStartAndEndTimeForAggreagtedValues($Serie,"Week");
			if ($result != false)
				$dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, 2, $result["StartTime"], $result["EndTime"], $Serie['AggValue'], $Serie);

			// -> Monatswerte
			$result = CalculateStartAndEndTimeForAggreagtedValues($Serie,"Month");
			if ($result != false)
				$dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, 3, $result["StartTime"], $result["EndTime"], $Serie['AggValue'], $Serie);

			// -> Jahreswerte
			$result = CalculateStartAndEndTimeForAggreagtedValues($Serie,"Year");
			if ($result != false)
				$dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, 4, $result["StartTime"], $result["EndTime"], $Serie['AggValue'], $Serie);
		}
		else
		{
			$Agg = -1;	// ->  AC_GetLoggedValues

         if  (isset($Serie['AggType']))   // wenn 'AggType' definiert wurde, wird dies vorrangig bearbeitet
         {
         	$Agg = $Serie['AggType'];
         }
			elseif ($Serie['AggregatedValues']['YearValues']!= -1 && $Diff > $Serie['AggregatedValues']['YearValues'])
				$Agg = 4;	//  -> AC_GetAggregatedValues [0=Hour, 1=Day, 2=Week, 3=Month, 4=Year]
			elseif ($Serie['AggregatedValues']['MonthValues']!= -1 && $Diff > $Serie['AggregatedValues']['MonthValues'])
				$Agg = 3;	//  -> AC_GetAggregatedValues [0=Hour, 1=Day, 2=Week, 3=Month, 4=Year]
			elseif ($Serie['AggregatedValues']['WeekValues']!= -1 && $Diff > $Serie['AggregatedValues']['WeekValues'])
				$Agg = 2;	//  -> AC_GetAggregatedValues [0=Hour, 1=Day, 2=Week, 3=Month, 4=Year]
			elseif ($Serie['AggregatedValues']['DayValues']!= -1 && $Diff > $Serie['AggregatedValues']['DayValues'])
				$Agg = 1;	//  -> AC_GetAggregatedValues [0=Hour, 1=Day, 2=Week, 3=Month, 4=Year]
			else if ($Serie['AggregatedValues']['HourValues']!= -1 && $Diff > $Serie['AggregatedValues']['HourValues'])
				$Agg = 0;	//  -> AC_GetAggregatedValues [0=Hour, 1=Day, 2=Week, 3=Month, 4=Year]

			// es wurde noch nichts definiert und es handelt sich um einen Zähler --> Tageswerte
         if ($Agg == -1 && $Serie['Ips']['IsCounter'])
	         $Agg = 0;

			if ($Agg == -1)
			{
				// Zeitraum ist zu groß -> nur bis max. Zeitraum einlesen
				if ($Diff > $Serie['AggregatedValues']['NoLoggedValues'])
					$Serie['StartTime'] = $Serie['EndTime'] - $Serie['AggregatedValues']['NoLoggedValues'];

				// Alle Werte
			   $dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, -1 , $Serie["StartTime"], $Serie["EndTime"], "Value", $Serie);
			}
			else
			{
				$dataArray = ReadAndAddToLoggedData($dataArray, $Id_AH, $VariableId, $Agg, $Serie["StartTime"], $Serie["EndTime"], $Serie['AggValue'], $Serie);
				$ReadCurrentValue = false;
			}
		}

		// sortieren, so , dass der aktuellste Wert zuletzt kommt
		$dataArray = array_reverse($dataArray);

		// aktuellen Wert der Variable noch in Array aufnehmen
		if ($ReadCurrentValue
			//&& $Serie['EndTime'] >= time()    			// nicht wenn Endzeitpunkt vor NOW ist
			&& !$Serie['Ips']['IsCounter'])				// nicht bei Zählervariablen
			{
//                $curValue = ReadCurrentValue($VariableId);
                $curValue    = ReadLoggedValue($Id_AH, $VariableId, $Serie['EndTime']);
				$dataArray[] = CreateDataItem($curValue['TimeStamp'], $curValue['Value'], $Serie);
			}


		return $dataArray ;
	}

    // ------------------------------------------------------------------------
    // ReadLoggedValue
    //    IN: $instanceID, $VariableId, $time
    //    OUT: Aktueller Wert
    // ------------------------------------------------------------------------
    function ReadLoggedValue($instanceID, $variableId, $time)
    {
        if ($time > time()) $time = time();
        $values = AC_GetLoggedValues($instanceID, $variableId, 0, $time+1, 1);
        $currentVal['Value']= $values[0]['Value'];
        $currentVal['TimeStamp'] = $time;

        return $currentVal;
    }  

	// ------------------------------------------------------------------------
	// ReadCurrentValue
	//    IN: $VariableId
	//    OUT: Aktueller Wert
	// ------------------------------------------------------------------------
	function ReadCurrentValue($variableId)
	{
		$currentVal['Value']= GetValue($variableId);
		$currentVal['TimeStamp'] = time();

		return $currentVal;
	}

    // ------------------------------------------------------------------------
    // ReadAndAddToLoggedData
    //    IN: siehe Parameter
    //    OUT: Vervollständigte Logged Data
    // ------------------------------------------------------------------------
    function ReadAndAddToLoggedData($loggedData, $id_AH, $variableId, $aggType, $startTime, $endTime, $aggValueName, $serie)
    {
        $cfg['Ips']['Debug']['Modules'] = true;

        if ($aggType >= 0)
            $tempData = @AC_GetAggregatedValues($id_AH, $variableId, $aggType, $startTime, $endTime, 0);
        else
            //$tempData = @AC_GetLoggedValues($id_AH, $variableId, $startTime, $endTime, 0 );
            $tempData = @AC_GetLoggedValuesCompatibility($id_AH, $variableId, $startTime, $endTime, 0 );

       foreach ($tempData as $item)
       {
            $loggedData[] = CreateDataItem($item['TimeStamp'], $item[$aggValueName], $serie);
       }

       unset ($tempData);

        return $loggedData;
    }

    //Hilfsfunktion, die die Funktionsweise von IP-Symcon 2.x nachbildet
    function AC_GetLoggedValuesCompatibility($instanceID, $variableID, $startTime, $endTime, $limit) {
        $values = AC_GetLoggedValues($instanceID, $variableID, $startTime, $endTime, $limit );
        if((sizeof($values) == 0) || (end($values)['TimeStamp'] > $startTime)) {
            $previousRow = AC_GetLoggedValues($instanceID, $variableID, 0, $startTime - 1, 1 );
            $values = array_merge($values, $previousRow);
        }
        return $values;
    }
	
	function CreateDataItem($dt, $val, $serie)
	{
		// Wert anpassen (Round, Scale)
		$val = PopulateValue($val, $serie);

		// z.B.: Date.UTC(2011,4,27,19,42,19),23.4
		$dtUTC = PopulateDate($dt, $serie);

		return array("@$dtUTC@", $val);
	}

	// ------------------------------------------------------------------------
	// CreateDataArrayFromExternalData
	//    Umwandeln der externen Daten in ein Daten Array
	//    IN: $arr = Aus IPS-Datenbank ausgelesenen Daten (LoggedData)
	//        $Serie = Config Daten der aktuellen Serie
	//    OUT: Highcharts ConfigString für Series-Data
	// ------------------------------------------------------------------------
	function CreateDataArrayFromExternalData($arr, $Serie)
	{
		$result = array();
		foreach($Serie['data'] as $item)
		{
			if (is_array($item))
			{
			   if (isset($item['TimeStamp']) && !isset($item['x']))
			   {
				   $item['x'] = "@" . PopulateDate($item['TimeStamp'], $Serie) . "@";
				   unset($item['TimeStamp']);
			   }
			   if (isset($item['Value']) && !isset($item['y']))
			   {
				   $item['y'] = $item['Value'];
				   unset($item['Value']);
			   }
			   if (isset($item['y']))
					$item['y'] = PopulateValue($item['y'], $Serie);

			   $result[] = $item;
		   }
			else
				$result[] = $item;
		}

		return $result;
	}

	// ------------------------------------------------------------------------
	// CreateTooltipFormatter
	//    Auslesen von immer wieder benötigten Werten aus der Variable
	//    IN: $cfg = Alle Config Daten
	//    OUT: Highcharts ConfigString für Tooltip-Formatter (Interaktive Anzeige des Wertes)
	// ------------------------------------------------------------------------
	function CreateTooltipFormatter($cfg)
	{
		DebugModuleName($cfg,"CreateTooltipFormatter");

		//ToDo: da sollten wir etwas lesbarer arbeiten
		$s = "";
		$offset ="";

		foreach ($cfg['series'] as $Serie )
		{
			if ($Serie['Ips']['Type'] == 'pie')
			{
				if (isset($Serie['data']))
				{
					$s .= "[";
					foreach($Serie['data'] as $data)
					{
						$unit = @$Serie['Unit'];
						if (isset($data['Unit']))
							$unit = $data['Unit'];

						$s .= "this.y +' " . $unit . "',";
					}
					$s = trim($s,",");
					$s .= "][this.point.x],";
				}
				else
				{
					$unit = @$Serie['Unit'];
					$s .= "[this.y + ' " . $unit . "'],";
				}
				$offset .= "0,";  // pies haben nie einen Offset
			}
			else
			{
			   // hier wird das VariableCustomProfile aus IPS übernommen
			   if (!isset($Serie['Unit']))
				{
					// hole das Variablen Profil
					$IPSProfil = @GetIPSVariableProfile($Serie['Id']);
					if ($IPSProfil != false)
					{
						if (array_key_exists("Associations",$IPSProfil) && count($IPSProfil['Associations'])>0)
						{
							$Arr = array();
							foreach($IPSProfil['Associations'] as $Item)
							{
							   $Arr[$Item['Value']] = $Item['Name'];
							}

							if (!is_array($Serie['ReplaceValues']))         // erzeuge Tooltips vollständig aus VariablenProfil
								$s .= CreateTooltipSubValues($Arr, array_keys($Arr));
							else  														// oder nehme ReplaceValues zur Hilfe
								$s .= CreateTooltipSubValues($Arr, $Serie['ReplaceValues']);
						}
						else
						{
							// Suffix als Einheit übernehmen
							$Serie['Unit'] = trim($IPSProfil['Suffix'], " ");
							$s .= "[this.y + ' ". $Serie['Unit']."'],";
						}
					}
					else  // falls VariablenId nicht existiert
					{
						$s .= "[this.y ],";
					}
				}
				// es wurden Unit und ReplaceValues übergeben
				else if (is_array($Serie['Unit']) && is_array($Serie['ReplaceValues']))
				{
					$s .= CreateTooltipSubValues($Serie['Unit'],$Serie['ReplaceValues']);
				}
				else		// Einheit aus übergebenem Parmeter Unit
				{
			      $s .= "[this.y + ' ". $Serie['Unit']."'],";
		      }
				$offset .= $Serie['Offset'] . ",";
	      }

		}

		$s = trim($s , "," );
		$offset = trim($offset , "," );

		//*1000 da JS in [ms] angebgeben wird un php in [s]
/*		$TooltipString="function() {
								var serieIndex = this.series.index;

								if (this.series.type == 'pie')
								{
			               	var pointIndex = this.point.x;
  									var unit = [".$s. "][serieIndex][pointIndex];

  									if (!unit)
									  unit = [".$s. "][serieIndex][0];

									return '<b>' + this.point.name +': </b> '+ unit +'<br/>= ' + this.percentage.toFixed(1) + ' %';
  								}
								else
			               {
				               var pointIndex = 0;
  									var unit = [".$s. "][serieIndex][pointIndex];
									var offset = [".$offset. "][serieIndex] * 1000;

									var offsetInfo ='';
									if (offset != 0)
										offsetInfo = '<br/>(Achtung Zeitwert hat einen Offset)';
									else
										offsetInfo ='';

									return '<b>' + this.series.name + ': </b> '+ unit + '<br/>'
										+ Highcharts.dateFormat('%A %d.%m.%Y %H:%M', this.x - offset)
										+ offsetInfo;


								}
						} ";
*/
		$TooltipString="function() {
								var serieIndex = this.series.index;
								var unit = [".$s. "][serieIndex];
								var offset = [".$offset. "][serieIndex] * 1000;
								var offsetInfo ='';

								if (offset != 0)
									offsetInfo = '<br/>(Achtung Zeitwert hat einen Offset)';
								else
									offsetInfo ='';

								if (this.series.type == 'pie')
								{
									return '<b>' + this.point.name +': </b> '+ unit +'<br/>= ' + this.percentage.toFixed(1) + ' %';
  								}
								else
			               {
									return '<b>' + this.series.name + ': </b> '+ unit + '<br/>'
										+ Highcharts.dateFormat('%A %d.%m.%Y %H:%M', this.x - offset)
										+ offsetInfo;
								}
						} ";



		return $TooltipString;
	}

	// ------------------------------------------------------------------------
	// CreateTooltipSubValues
	//    Erzeugt den Tooltip für Unter-Elemente
	//    IN: shownTooltipArr = Array der Werte (Synonyme) welche im Tooltip angezeigt werden sollen
	//        chartValueArr = Array der Werte welche im Chart eingetragen werden
	//    OUT: Tooltip String
	// ------------------------------------------------------------------------
	function CreateTooltipSubValues($shownTooltipArr, $chartValueArr)
	{
		$s="{";
		$Count = count($shownTooltipArr);
		for ($i = 0; $i < $Count ; $i++)
		{
			if (isset($chartValueArr[$i]) && isset($shownTooltipArr[$i]))
			   $s .= $chartValueArr[$i] .": '" . $shownTooltipArr[$i] ."'," ;
		}
		$s = trim($s, ",") . "}";

		return $s ."[this.y],";
	}

	// ------------------------------------------------------------------------
	// GetIPSVariableProfile
	//    Liest das Variablen Profil der übergeben Variable aus
	//    Versucht zuerst das eigene und wenn nicht verfügbar das Standar Profil auszulesen
	//    IN: variableId = Id der Variablen
	//    OUT: Variablen Profil
	// ------------------------------------------------------------------------
	function GetIPSVariableProfile($variableId)
	{
		$var = @IPS_GetVariable($variableId);
		if ($var == false) // Variabel existiert nicht
		   return false;

		$profilName = $var['VariableCustomProfile']; 	// "Eigenes Profil"

		if ($profilName == false)                     	// "Standard" Profil
			$profilName = $var['VariableProfile'];

		if ($profilName != false)
			return IPS_GetVariableProfile($profilName);  // und jetzt die dazugehörigen Daten laden
		else
			return false;
	}



	// ------------------------------------------------------------------------
	// CreateArrayForChart
	//
	//    IN: $cfg
	//    OUT: Config Array für den Bereich 'chart'
	// ------------------------------------------------------------------------
	function CreateArrayForChart($cfg)
	{
		if (!isset($cfg['chart']))
			$cfg['chart'] = array();

		//Default
		IfNotIssetSetValue($cfg['chart']['renderTo'], "container");
		IfNotIssetSetValue($cfg['chart']['zoomType'], "xy");

		return $cfg['chart'];
	}

	// ------------------------------------------------------------------------
	// CreateArrayForCredits
	//
	//    IN: $cfg
	//    OUT: Config Array für den Bereich 'credits'
	// ------------------------------------------------------------------------
	function CreateArrayForCredits($cfg)
	{
		if (!isset($cfg['credits']))
			$cfg['credits'] = array();

		//Default
		IfNotIssetSetValue($cfg['credits']['enabled'], false);

		return $cfg['credits'];
	}

	// ------------------------------------------------------------------------
	// CreateArrayForTitle
	//
	//    IN: $cfg
	//    OUT: Config Array für den Bereich 'title'
	// ------------------------------------------------------------------------
	function CreateArrayForTitle($cfg)
	{
		if (!isset($cfg['title']))
			$cfg['title'] = array();

		return $cfg['title'];
	}

	// ------------------------------------------------------------------------
	// CreateArrayForExporting
	//
	//    IN: $cfg
	//    OUT: Config Array für den Bereich 'exporting'
	// ------------------------------------------------------------------------
	function CreateArrayForExporting($cfg)
	{
		if (!isset($cfg['exporting']))
			$cfg['exporting'] = array();

		//Default
		IfNotIssetSetValue($cfg['exporting']['buttons']['printButton']['enabled'], false);

		return $cfg['exporting'];
	}

	// ------------------------------------------------------------------------
	// CreateArrayForTooltip
	//
	//    IN: $cfg
	//    OUT: Config Array für den Bereich 'tooltip'
	// ------------------------------------------------------------------------
	function CreateArrayForTooltip($cfg)
	{
		if (!isset($cfg['tooltip']))
			$cfg['tooltip'] = array();

		//Default
		// wenn not isset -> autom. erzeugen durch IPS
		if (!isset($cfg['tooltip']['formatter']))
		   $cfg['tooltip']['formatter'] = "@" . CreateTooltipFormatter($cfg) . "@";
		// wenn "" -> default by highcharts
		else if ($cfg['tooltip']['formatter'] == "")
		{
		   // do nothing
		}

		return $cfg['tooltip'];
	}

	// ------------------------------------------------------------------------
	// CreateArrayForSubTitle
	//
	//    IN: $cfg
	//    OUT: Config Array für den Bereich subtitle
	// ------------------------------------------------------------------------
	function CreateArrayForSubTitle($cfg)
	{
		if (!isset($cfg['subtitle']))
			$cfg['subtitle'] = array();

		//Default
		IfNotIssetSetValue($cfg['subtitle']['text'], "Zeitraum: %STARTTIME% - %ENDTIME%");
		IfNotIssetSetValue($cfg['subtitle']['Ips']['DateTimeFormat'], "(D) d.m.Y H:i");

		$s = $cfg['subtitle']['text'];
		$s = str_ireplace("%STARTTIME%", date($cfg['subtitle']['Ips']['DateTimeFormat'], $cfg['Ips']['ChartStartTime']), $s);
		$s = str_ireplace("%ENDTIME%", date($cfg['subtitle']['Ips']['DateTimeFormat'], $cfg['Ips']['ChartEndTime']), $s);
		$cfg['subtitle']['text'] = ReplaceToGermanDate($s);

		unset($cfg['subtitle']['Ips']);

		return $cfg['subtitle'];
	}
	// ------------------------------------------------------------------------
	// CreateArrayForXAxis
	//    Erzeugen das ArrX-Achsen Strings für Highchart-Config
	//    IN: $cfg
	//       es besteht die Möglichkeit den Achsen String bereits im Highchart Format zu hinterlegen
	//       oder die folgenden Parameter als Array einzustellen: Name, Min, Max, TickInterval, Opposite, Unit
	//    OUT: Highcharts String für die Achsen
	// ------------------------------------------------------------------------
	function CreateArrayForXAxis($cfg)
	{
		if (!isset($cfg['xAxis']))
			$cfg['xAxis'] = array();

		//Default
		IfNotIssetSetValue($cfg['xAxis']['type'], "datetime");
		IfNotIssetSetValue($cfg['xAxis']['dateTimeLabelFormats']['second'], "%H:%M:%S");
		IfNotIssetSetValue($cfg['xAxis']['dateTimeLabelFormats']['minute'], "%H:%M");
		IfNotIssetSetValue($cfg['xAxis']['dateTimeLabelFormats']['hour'], "%H:%M");
		IfNotIssetSetValue($cfg['xAxis']['dateTimeLabelFormats']['day'], "%e. %b");
		IfNotIssetSetValue($cfg['xAxis']['dateTimeLabelFormats']['week'], "%e. %b");
		IfNotIssetSetValue($cfg['xAxis']['dateTimeLabelFormats']['month'], "%b %y");
		IfNotIssetSetValue($cfg['xAxis']['dateTimeLabelFormats']['year'], "%Y");

		IfNotIssetSetValue($cfg['xAxis']['allowDecimals'], false);

		if (isset($cfg['xAxis']['min']) && $cfg['xAxis']['min'] == false)
			unset($cfg['xAxis']['min']);
		else
			IfNotIssetSetValue($cfg['xAxis']['min'], "@" . CreateDateUTC($cfg['Ips']['ChartStartTime']) ."@");

		if (isset($cfg['xAxis']['max']) && $cfg['xAxis']['max'] == false)
			unset($cfg['xAxis']['max']);
		else
			IfNotIssetSetValue($cfg['xAxis']['max'], "@" . CreateDateUTC($cfg['Ips']['ChartEndTime'])."@");



		return $cfg['xAxis'];
	}

	// ------------------------------------------------------------------------
	// CreateArrayForYAxis
	//    Erzeugen der Y-Achsen Strings für Highchart-Config
	//    IN: $cfg
	//       es besteht die Möglichkeit den Achsen String bereits im Highchart Format zu hinterlegen
	//       oder die folgenden Parameter als Array einzustellen: Name, Min, Max, TickInterval, Opposite, Unit
	//    OUT: Highcharts String für die Achsen
	// ------------------------------------------------------------------------
	function CreateArrayForYAxis($cfg)
	{
		if (!isset($cfg['yAxis']))
			return null;

		$result = array();

		foreach ($cfg['yAxis'] as $Axis )
		{
			// erst mal alles kopieren
			$cfgAxis = $Axis;

			if (!isset($cfgAxis['labels']['formatter']) && isset($Axis['Unit']))
				$cfgAxis['labels']['formatter'] ="@function() { return this.value +' ". $Axis['Unit']."'; }@";

      	$result[] = $cfgAxis;
		}

		return $result;
	}

	// ------------------------------------------------------------------------
	// CreateDateUTC
	//    Erzeugen des DateTime Strings für Highchart-Config
	//    IN: $timeStamp = Zeitstempel
	//    OUT: Highcharts DateTime-Format als UTC String ... Date.UTC(1970, 9, 27, )
	//       Achtung! Javascript Monat beginnt bei 0 = Januar
	// ------------------------------------------------------------------------
	function CreateDateUTC($timeStamp)
	{
		$monthForJS = ((int)date("m", $timeStamp))-1 ;	// Monat -1 (PHP->JS)
		return "Date.UTC(" . date("Y,", $timeStamp) .$monthForJS. date(",j,H,i,s", $timeStamp) .")";
	}

	// ------------------------------------------------------------------------
	// ReplaceToGermanDate
	//    Falls nicht konfiguriert, wird dies als Default String genommen
	//    IN: String mit englischen Wochentagen, bzw. Monaten
	//    OUT: der String übersetzt ins Deutsche
	// ------------------------------------------------------------------------
	function ReplaceToGermanDate($value)
	{
			$trans = array(
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
			    'Mar'     	 => 'Mär',
			    'May'       => 'Mai',
			    'Oct'   	 => 'Okt',
			    'Dec'  		 => 'Dez',
		);
		return  strtr($value, $trans);
	}


	// ------------------------------------------------------------------------
	// my_json_encode
	//
	//    IN: PHP-Array
	//    OUT: JSON String
	// ------------------------------------------------------------------------
	function my_json_encode($cfgArr)
	{
		array_walk_recursive($cfgArr, "CheckArrayItems");

		$s = json_encode($cfgArr);

		// alle " entfernen
		$s = str_replace('"', '',$s);

		// Zeilenumbruch, Tabs, etc entfernen ... bin mir nicht so sicher ob das so gut ist
		$s = RemoveUnsupportedStrings($s);

		return $s;
	}

	// ------------------------------------------------------------------------
	// CheckArrayItems
	//
	//    IN: Array-Item
	//    OUT:
	// ------------------------------------------------------------------------
	function CheckArrayItems(&$item)
	{
		if (is_string($item))
		{
			if ($item == "@" || $item == "@@" )
			{
				$item = "'" . $item . "'";
			}
			else if ((substr($item,0,1) == "@" && substr($item,-1) == "@"))
			{
				$item = trim($item, "@");
			}
/*			else if ((substr($item,0,1) == "$" && substr($item,-1) == "$"))
			{

				$item = trim($item, "$");
			}*/
			else
			{
				$item = "'" . trim($item, "'") . "'";
			}

			if (mb_detect_encoding($item, 'UTF-8', true) === false) {       
				$item = utf8_encode($item);   
			}  			

		}
	}

	// ------------------------------------------------------------------------
	// RemoveUnsupportedStrings
	//    Versuchen Sonderzeichen wie Zeilenumbrüche, Tabs, etc. aus dem übergebenen String zu entfernen
	//    IN: $str
	//    OUT: $str
	// ------------------------------------------------------------------------
	function RemoveUnsupportedStrings($str)
	{

		$str = str_replace("\\t","",$str);
		$str = str_replace("\\n","",$str);
		$str = str_replace("\\r","",$str);
		$str = str_ireplace("\\\u00","\\u00",$str);  // da muss man nochmals checken
		$str = str_replace("\\\\","",$str);

		return $str;
	}

	// ------------------------------------------------------------------------
	// IfNotIssetSetValue
	//    pfüft ob isset($item), wenn nicht wird $value in &$item geschrieben
	//    IN: &$item, $value
	//    OUT: &$item
	// ------------------------------------------------------------------------
	function IfNotIssetSetValue(&$item, $value )
	{
		if (!isset($item)
			|| (is_string($item) && $item == ""))   // zusätzliche Abfrage in 2.01
		{
			$item = $value;
			return false;
		}

		return true;
	}

	// ------------------------------------------------------------------------
	// getmicrotime
	//
	//    IN:
	//    OUT:
	// ------------------------------------------------------------------------
	function getmicrotime($short = false)
	{
	   list($usec,$sec)=explode(" ", microtime());

		if ($short )
	      return (float)$usec + (float)substr($sec,-1);
		else
		   return (float)$usec + (float)$sec;
	}

	// ------------------------------------------------------------------------
	// DebugString
	//
	//    IN:
	//    OUT:
	// ------------------------------------------------------------------------
	function DebugString($str)
	{
	   $s = RemoveUnsupportedStrings($str);
	   echo $s;
	}

	// ------------------------------------------------------------------------
	// DebugModuleName
	//
	//    IN:
	//    OUT:
	// ------------------------------------------------------------------------
	function DebugModuleName($cfg, $name)
	{
		if (isset($cfg['Ips']['Debug']['Modules']) && $cfg['Ips']['Debug']['Modules'])
		{
			global $_IPS, $version, $versionDate;

		   IPS_LogMessage($_IPS['SENDER'] ." - " .getmicrotime(true) , "Highcharts $version ($versionDate) - $name");
		 }
	}
?>