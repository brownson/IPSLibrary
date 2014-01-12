<?php
	IPSUtils_Include ("IPSHighcharts_Custom.inc.php",            "IPSLibrary::config::modules::charts::IPSHighcharts");

	Global $CfgDaten;
	$debug = true;

	if (!isset($_GET['VarID']))   AbortWithError ("Keine Variable ID angegeben!");
	if (!isset($_GET['ChartID'])) AbortWithError ("Keine Chart ID angegeben!");

	$varID       = intval($_GET['VarID']); 
	$chartID     = intval($_GET['ChartID']);
	$chartWidth  = '100%';
	$chartHeight = 400;
	$chartStart  = time() - 86400;    // 24 Stunden
	$chartEnd    = time() ;    
 
	if (isset($_GET['Height'])) $chartHeight     = intval($_GET['Height']);
	if (isset($_GET['Width']))  $chartWidth      = $_GET['Width'];
	if (isset($_GET['Start']))  $chartStart      = intval($_GET['Start']);
	if (isset($_GET['End']))    $chartEnd        = intval($_GET['End']);

	// Config Daten erzeugen
	Debug ('Start Chart Configuration');
	InitConfiguration($chartWidth, $chartHeight, $chartStart, $chartEnd);
	BuildConfigurationByChart($chartID);
	AddButtonConfiguration($varID, $chartID);
	Debug ('Finished Chart Configuration');

	IPSUtils_Include ("IPSHighcharts.inc.php","IPSLibrary::app::modules::Charts::IPSHighcharts");
	$CfgDaten = CheckCfgDaten($CfgDaten);
	$CfgDaten = CompatibilityCheck($CfgDaten);
	$CfgDaten = CheckCfg($CfgDaten);

	IPSHighcharts_BeforeBuildChart($varID, $chartID, $CfgDaten);

	$JavaScriptConfigForHighchart = GetHighChartsCfgFile($CfgDaten);
	$AdditionalConfigData         = ReadAdditionalConfigData($CfgDaten);
	$LangOptions                  = GetHighChartsLangOptions($CfgDaten);;

	// --------------------------------------------------------------------------------------------------------------
	function AddChartSerie($variableID, $fillColor, $strokeColor, $timeOffset, $unit, $type) {
		Global $CfgDaten;

		Debug('Add Chart Serie for VariableID='.$variableID.' with Color='.$fillColor);
		$serie = array();
		$serie['name']          = IPS_GetName($variableID);
		$serie['Id']            = $variableID;
		$serie['Unit']          = $unit;
		$serie['ReplaceValues'] = false;
		$serie['RoundValue']    = 1;
		$serie['type']          = $type;
		$serie['color']         = $fillColor;
		$serie['yAxis']         = 0;
		$serie['step']          = false;
		$serie['shadow']        = true;
		//$serie['AggType']       = $aggType;
		$serie['AggValue']      = 'Avg';
		$serie['showInLegend']  = true;
		$serie['allowDecimals'] = false;
		$serie['enableMouseTracking'] = true;
		$serie['states']['hover']['lineWidth'] = 2;
		$serie['marker']['enabled'] = false;
		$serie['marker']['states']['hover']['enabled']   = true;
		$serie['marker']['states']['hover']['symbol']    = 'circle';
		$serie['marker']['states']['hover']['radius']    = 4;
		$serie['marker']['states']['hover']['lineWidth'] = 1;

		$CfgDaten['series'][]   = $serie;
	}

	// --------------------------------------------------------------------------------------------------------------
	function GetHighchartTypeFromIPSChartType($type) {
		switch ($type) {
			case 'line':
				return 'line';
			default:
				return 'column';
		}
	}

	// --------------------------------------------------------------------------------------------------------------
	function BuildConfigurationByChart($chartID) {
		Global $CfgDaten;

		// Load ChartData
		$fileData   = IPS_GetMediaContent($chartID);
		$jsonStr    = base64_decode ($fileData);
		$chart      = json_decode($jsonStr);

		// Get ChartData
		$chartType  = $chart->type;

		// Get Unit,Profile Data
		$profileName = $chart->profile;
		$profile     = IPS_GetVariableProfile($profileName);
		$chartUnit   = $profile['Suffix'];

		Debug('Read ChartData: ChartType='.$chartType);
		Debug('Read ChartData: Profile='.$profileName);
		Debug('Read ChartData: ChartUnit='.$chartUnit);

		$CfgDaten['title']['text']    = IPS_GetName($chartID);
		$CfgDaten['subtitle']['text'] = "Zeitraum: %STARTTIME% - %ENDTIME%";
		$CfgDaten['subtitle']['Ips']['DateTimeFormat'] = "(D) d.m.Y H:i";

		$CfgDaten['yAxis'][0]['title']['text']             = IPS_GetName($chartID);;
		$CfgDaten['yAxis'][0]['stackLabels']['enabled']    = true;
		$CfgDaten['yAxis'][0]['stackLabels']['formatter']  = "@function() { return this.total.toFixed(1) }@";
		$CfgDaten['yAxis'][0]['labels']['enabled'] = true;
		$CfgDaten['yAxis'][0]['Unit']                      = $chartUnit;
		$CfgDaten['yAxis'][0]['opposite'] = false;
		$CfgDaten['yAxis'][0]['lineWidth'] = 0;
		$CfgDaten['yAxis'][0]['lineColor'] =  'transparent';
		$CfgDaten['yAxis'][0]['minorGridLineWidth'] = 0;
		$CfgDaten['yAxis'][0]['minorTickLength'] = 0;
		$CfgDaten['yAxis'][0]['tickLength'] = 0;

		// Analyze Datasets
		$datasets = $chart->datasets;
		foreach ($datasets as $idx=>$dataset) {
			AddChartSerie($dataset->variableID, 
			              $dataset->fillColor, 
			              $dataset->strokeColor, 
			              $dataset->timeOffset, 
			              $chartUnit, 
			              GetHighchartTypeFromIPSChartType($chart->type));
		}
	}

	// --------------------------------------------------------------------------------------------------------------
	function InitConfiguration($width, $height, $start, $end) {
		GLOBAL $CfgDaten;

		// Highcharts-Theme
		$CfgDaten['HighChart']['Theme']="ips.js";

		// Abmessungen des erzeugten Charts
		$CfgDaten['HighChart']['Width']  = 0;
		$CfgDaten['HighChart']['Height'] = $height;

		$CfgDaten['StartTime'] = $start;
		$CfgDaten['EndTime']   = $end;

		$CfgDaten['RunMode'] = "file";

		//***************************************************************************
		// Serienübergreifende Einstellung für das Laden von Werten
		// Systematik funktioniert jetzt additiv.
		// D.h. die angegebenen Werte gehen ab dem letzten Wert
		//
		//            -5 Tage           -3 Tage                 EndTime
		// |           |                 |                        |
		// |           |DayValue = 2     |HourValues = 3          |
		// |Tageswerte |Stundenwerte     |jeder geloggte Wert     |
		//***************************************************************************
		$CfgDaten['AggregatedValues']['HourValues']      = 6;        // ist der Zeitraum größer als X Tage werden Stundenwerte geladen
		$CfgDaten['AggregatedValues']['DayValues']       = 7;       // ist der Zeitraum größer als X Tage werden Tageswerte geladen
		$CfgDaten['AggregatedValues']['WeekValues']      = 32;       // ist der Zeitraum größer als X Tage werden Wochenwerte geladen
		$CfgDaten['AggregatedValues']['MonthValues']     = 365;       // ist der Zeitraum größer als X Tage werden Monatswerte geladen
		$CfgDaten['AggregatedValues']['YearValues']      = -1;       // ist der Zeitraum größer als X Tage werden Jahreswerte geladen
		$CfgDaten['AggregatedValues']['NoLoggedValues']  = 1000;     // ist der Zeitraum größer als X Tage werden keine Boolean Werte mehr geladen,
																	 // diese werden zuvor immer als Einzelwerte geladen
		$CfgDaten['AggregatedValues']['MixedMode']       = false;    // alle Zeitraumbedingungen werden kombiniert
	}

	// --------------------------------------------------------------------------------------------------------------
	function GetButtonOnClickCode($varID, $chartID, $time) {
		Global $CfgDaten;

		$result = "@function() { new Image().src = '/user/IPSHighcharts/HighchartsCommand.php?";
		$result .= "VarID=".$varID;
		$result .= "&ChartID=".$chartID;
		$result .= "&Height=".$CfgDaten['HighChart']['Height'];
		$result .= "&Time=".$time;
		$result .= "&Start=".$CfgDaten['StartTime'];
		$result .= "&End=".$CfgDaten['EndTime'];
		$result .= " '; }@";
		
		return $result;
	}
	
	// --------------------------------------------------------------------------------------------------------------
	function AddSingleButtonConfiguration($varID, $chartID, $command, $x, $y, $align='') {
		Global $CfgDaten;

		$CfgDaten['exporting']['buttons'][$command]['x']              = $x;
		$CfgDaten['exporting']['buttons'][$command]['y']              = $y;
		$CfgDaten['exporting']['buttons'][$command]['symbol']         = "url(/user/IPSHighcharts/icons/".$command.".png)";
		$CfgDaten['exporting']['buttons'][$command]['symbolX']        = 0;
		$CfgDaten['exporting']['buttons'][$command]['symbolY']        = 0;
		$CfgDaten['exporting']['buttons'][$command]['height']         = 32;
		$CfgDaten['exporting']['buttons'][$command]['width']          = 54;
		$CfgDaten['exporting']['buttons'][$command]['text']           = 'XX';
		$CfgDaten['exporting']['buttons'][$command]['_titleKey']      = $command.'Button';
		$CfgDaten['exporting']['buttons'][$command]['onclick']        = GetButtonOnClickCode($varID, $chartID, $command);
		if ($align != '')
			$CfgDaten['exporting']['buttons'][$command]['align']      = $align;


	}
	
	// --------------------------------------------------------------------------------------------------------------
	function AddButtonConfiguration($varID, $chartID) {
		Global $CfgDaten;

		AddSingleButtonConfiguration($varID, $chartID, 'Backward', -122, 1);
		AddSingleButtonConfiguration($varID, $chartID, 'Home',      -61, 1);
		AddSingleButtonConfiguration($varID, $chartID, 'Forward',     0, 1);

		AddSingleButtonConfiguration($varID, $chartID, 'Hour',        5, 1, 'left');
		AddSingleButtonConfiguration($varID, $chartID, 'Day',        66, 1, 'left');
		AddSingleButtonConfiguration($varID, $chartID, 'Week',      127, 1, 'left');
		AddSingleButtonConfiguration($varID, $chartID, 'Month',     188, 1, 'left');
		AddSingleButtonConfiguration($varID, $chartID, 'Year',      249, 1, 'left');
	}

	// --------------------------------------------------------------------------------------------------------------
	function Debug($message) {
		Global $debug;
		IPSUtils_Include ("IPSLogger.inc.php","IPSLibrary::app::core::IPSLogger");
		if ($debug) IPSLogger_Dbg(__FILE__, $message);
	}

	// --------------------------------------------------------------------------------------------------------------
	function AbortWithError($errorMessage) {
		IPSUtils_Include ("IPSLogger.inc.php","IPSLibrary::app::core::IPSLogger");
		IPSLogger_Err(__FILE__, $errorMessage);
		echo $errorMessage;
		die();
	}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts</title>
		
		<!-- 1. Add these JavaScript inclusions in the head of your page -->
		<script type="text/javascript" src="/user/IPSHighcharts/Highcharts/js/jquery.min.js"></script> 
		<!-- wenn lokal vorhanden .... <script type="text/javascript" src="jquery/1.7.2/jquery.js"></script> -->
		<script type="text/javascript" src="/user/IPSHighcharts/Highcharts/js/highcharts.js"></script>
		
		<!-- 1a) add a theme file -->
		<script type="text/javascript" src="/user/IPSHighcharts/Highcharts/js/themes/ips.js"></script>
			
		<!-- 1b) the exporting module -->
		<script type="text/javascript" src="/user/IPSHighcharts/Highcharts/js/modules/exporting.js"></script>
		
		<!-- 2. Add the JavaScript to initialize the chart on document ready -->

		<script type="text/javascript">
			var chart;
			Highcharts.setOptions({<?php echo $LangOptions; ?>});
		
			$(document).ready(function() {
				chart = new Highcharts.Chart({<?php echo $JavaScriptConfigForHighchart; ?>});
		</script>
	</head>
		
	<body>
	
		<!-- 3. Add the container -->
		<div id="container" style="width: <?php echo $chartWidth; ?>; height: <?php echo $chartHeight; ?>; margin: 0 auto"></div>
		<?php IPSHighcharts_AfterBuildChart($varID, $chartID, $CfgDaten); ?>

	</body>
</html>
