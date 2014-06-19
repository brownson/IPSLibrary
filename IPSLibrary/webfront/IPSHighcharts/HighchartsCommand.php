<?php
	IPSUtils_Include ("IPSLogger.inc.php","IPSLibrary::app::core::IPSLogger");
	$debug = true;
	IPS_Sleep(50);

	if (!isset($_GET['VarID']))   { echo "VarID NOT assigned!"; die(); }
	if (!isset($_GET['ChartID'])) { echo "ChartID NOT assigned!"; die(); }

	$starttime  = time()-3600;
	$endtime    = time();
	$height     = 400;

	if (isset($_GET['VarID']))     $varID      = intval($_GET['VarID']); else die() ;
	if (isset($_GET['ChartID']))   $chartID    = intval($_GET['ChartID']); else die() ;
	if (isset($_GET['Height']))    $height     = intval($_GET['Height']); else die() ;
	if (isset($_GET['Time']))      $time       = $_GET['Time']; else die() ;
	if (isset($_GET['Start']))     $starttime  = $_GET['Start']; else die() ;
	if (isset($_GET['End']))       $endtime    = $_GET['End']; else die() ;

	$starttime_string = date('d.m.Y h:i:s',$starttime);
	$endtime_string   = date('d.m.Y h:i:s',$endtime);
	$akt_zeitraum     = $endtime - $starttime ;     // aktueller Zeitraum
	$sprungweite      = $akt_zeitraum/2; 
	if ($debug) IPSLogger_Dbg(__FILE__, $varID ."-". $time ."-".$starttime_string."-".$endtime_string);


	$CfgDaten = array();
	switch($time) {
		case "Backward" : 
			$CfgDaten['StartTime'] =  $starttime - $sprungweite;
			$CfgDaten['EndTime']   =  $endtime   - $sprungweite;
			break;
		case "Home": 
			$CfgDaten['StartTime'] =  time() - 3600;
			$CfgDaten['EndTime']   =  time() ;
			break;
		case "Forward": 
			$CfgDaten['StartTime'] =  $starttime + $sprungweite;
			$CfgDaten['EndTime']   =  $endtime   + $sprungweite;
			if ( $CfgDaten['EndTime'] > time()) { 
				$CfgDaten['StartTime'] =  time() - 3600;
				$CfgDaten['EndTime']   =  time() ;
			}
			break;
		case "Hour":
			$CfgDaten['StartTime'] =  time() - 3600;
			$CfgDaten['EndTime']   =  time() ;
			break;
		case "Day": 
			$CfgDaten['StartTime'] =  time() - ( 3600 * 24 );
			$CfgDaten['EndTime']   =  time() ;
			break;
		case "Week": 
			$CfgDaten['StartTime'] =  time() - ( 3600 * 24 * 7 );
			$CfgDaten['EndTime']   =  time() ;
			break;
		case "Month": 
			$CfgDaten['StartTime'] =  time() - ( 3600 * 24 * 31);
			$CfgDaten['EndTime']   =  time() ;
			break;
		case "Year": 
			$CfgDaten['StartTime'] =  time() - ( 3600 * 24 * 365);
			$CfgDaten['EndTime']   =  time() ;
			break;
		default: 
			$CfgDaten['StartTime'] =  time() - 3600;
			$CfgDaten['EndTime']   =  time() ;
	}

	$value = "<iframe src='/User/IPSHighcharts/IPSChart.php?VarID=".$varID."&ChartID=".$chartID."&Height=".$height."&Start=".$CfgDaten['StartTime']."&End=".$CfgDaten['EndTime']."' width='100%' height='".$height."' frameborder='0' scrolling='no'></iframe>";
	SetValue($varID, $value);
?>