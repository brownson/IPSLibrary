<?php 
	/**@ingroup ipshighcharts
	 * @{
	 *
	 * @file          IPSHighstock.php
	 * @author        khc
	 * @version
	 *  Version 2.50.0, 01.06.2012 khc: Initiale Version 2.02 im Forums Thread<br/>
	 *  Version 2.50.1, 05.10.2012  ab: Integration IPSLibrary (Anpassung der Pfade)<br/>
	 *
	 * Template File zur Darstellung von "Highchart" Charts
	 *
	 */

	 if (!isset($_GET['CfgFile'])) 
      { 
       $CfgFile = false; 
      } 
   else 
      { 
       $CfgFile = $_GET['CfgFile']; 
      } 

   if (!isset($_GET['ScriptId'])) 
      { 
       $iScriptId = false; 
      } 
   else 
      { 
       $iScriptId = (int)$_GET['ScriptId']; 
      } 

	
	// ScriptId wurde übergeben -> aktuelle Daten werden geholt
	if ($iScriptId != false)
	{
		$ConfigScript=IPS_GetScript($iScriptId);      // Id des Config Scripts
	
		include_once(IPS_GetKernelDir() . "scripts\\" .$ConfigScript['ScriptFile']);
		global $sConfig;
		//$sConfig = IPS_RunScriptWait($iScriptId);
		$s = utf8_encode($sConfig);	
		
	}
	// Filename würde übergeben -> Daten aus Datei lesen
	else if ($CfgFile != false)
	{
		// prüfen ob übergeben Datei existiert
		if (!file_exists($CfgFile))
		{
			echo "Datei '$CfgFile' nicht vorhanden!!!";
			return;
		}

		// file vorhanden -> einlesen
		$handle = fopen($CfgFile,"r");
		$s ="";
		while (!feof($handle))
		{
			$s .= fgets($handle);
		}
		fclose($handle);
		$s = utf8_encode($s);	
	}
	else
	{
		echo "Achtung! Fehlerhafte Parameter CfgFile bzw ScriptId";
		return;
	}
	
	// Bereiche splitten -> erster Teil sind diverse Config Infos, zweiter Teik sind die Daten für Highcharts
	$s = explode("|||" , $s);
	
	if (count($s) >= 2)
	{
		$TempString = trim($s[0],"\n ");
		$JavaScriptConfigForHighchart = $s[1];	
		
		$LangOptions="lang: {}";
		if (count($s) > 2)
			$LangOptions = trim($s[2],"\n ");
		
		// aus den Daten ein schönes Array machen
		$TempStringArr = explode("\n", $TempString);
		foreach($TempStringArr as $Item)
		{
			$KeyValue = explode("=>", $Item);
			$AdditionalConfigData[trim($KeyValue[0]," []")] = trim($KeyValue[1]," ");
		}
		
		// Verzeichnis + Theme
		if ($AdditionalConfigData['Theme'] != '')
			$AdditionalConfigData['Theme']= '/user/IPSHighcharts/Highcharts/js/themes/' . $AdditionalConfigData['Theme'];

		//$AdditionalConfigData["Height"]="1200px";
		//$AdditionalConfigData["Width"]="600px";

			
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts</title>
		
		<!-- 1. Add these JavaScript inclusions in the head of your page -->
		<script type="text/javascript" src="/user/IPSHighcharts/Highcharts/js/jquery.min.js"></script> 
		<script type="text/javascript" src="/user/IPSHighcharts/Highcharts/js/highstock.js"></script>
		
		<!-- 1a) add a theme file -->
		<script type="text/javascript" src="<?php echo $AdditionalConfigData['Theme'] ?>"></script>
			
		<!-- 1b) the exporting module -->
		<script type="text/javascript" src="/user/IPSHighcharts/Highcharts/js/modules/exporting.js"></script>
		
		<!-- 2. Add the JavaScript to initialize the chart on document ready -->
		
		<script type="text/javascript">

		var chart;
			Highcharts.setOptions({<?php echo $LangOptions; ?>});
				
			$(document).ready(function() {
				chart = new Highcharts.StockChart({<?php echo $JavaScriptConfigForHighchart; ?>});

		</script>
	</head>
		
	<body>
	
		<!-- 3. Add the container -->
		<div id="container" style="width: <?php echo $AdditionalConfigData['Width'] ?>; height: <?php echo $AdditionalConfigData['Height'] ?>; margin: 0 auto"></div>
	</body>
</html>
