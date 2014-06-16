<?php 
	/**
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

	/**@addtogroup ipsweatherforcastat
	 * @{
	 *
	 * @file          Weather.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 16.02.2012<br/>
	 *
	 * File kann in das WebFront bzw. MobileFFront eingebunden und visualisiert die aktuelle Wettervorhersage
	 *
	 */

	/** @}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="Content-Style-Type" content="text/css">
		<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">


		<?
			$agent  = $_SERVER['HTTP_USER_AGENT'];
			$mobile = preg_match("@ipod@i", $agent) || preg_match("@iphone@i",$agent);
			if ($mobile) {
				echo '<link rel="stylesheet" type="text/css" href="/user/IPSWeatherForcastAT/iWeather.css" />';
			} else {
				echo '<link rel="stylesheet" type="text/css" href="/user/IPSWeatherForcastAT/Weather.css" />';
			}
			$background  = '';
			$textcolor   = 'color:#fff;';
			if (isset($_GET['foreground'])) {
					$textcolor = 'color:#'.$_GET['foreground'].';';
			}
			if (isset($_GET['background'])) {
					$background = 'background:#'.$_GET['background'].';';
					if ($_GET['background'] == 'e4e4e4' and $textcolor='') $textcolor='color:#000000;';
			}
			$style = 'style="'.$background.$textcolor.'"';
		?>

		<script type="text/JavaScript">
			<!--
			function timedRefresh(timeoutPeriod) {
				setTimeout("location.reload(true);",timeoutPeriod);
			}
			//   -->
		</script>
	</head>
	<body onload="JavaScript:timedRefresh(1000*60*5);" <?php echo $style?>>
		<?php
			IPSUtils_Include ("IPSWeatherForcastAT_Constants.inc.php",     "IPSLibrary::app::modules::Weather::IPSWeatherForcastAT");
			IPSUtils_Include ("IPSWeatherForcastAT_Configuration.inc.php", "IPSLibrary::config::modules::Weather::IPSWeatherForcastAT");
			IPSUtils_Include ("IPSWeatherForcastAT_Utils.inc.php",         "IPSLibrary::app::modules::Weather::IPSWeatherForcastAT");
			IPSUtils_Include ("IPSLogger.inc.php",                         "IPSLibrary::app::core::IPSLogger");

			//IPSLogger_Inf(__file__, $_SERVER['HTTP_USER_AGENT']);
			if(IPSWEATHERFAT_COUNT_DETAILS == 1) $berichtGesamt = IPSWeatherFAT_GetValue('TomorrowForecastLong');
			if(IPSWEATHERFAT_COUNT_DETAILS == 2) $berichtGesamt = IPSWeatherFAT_GetValue('TomorrowForecastLong') . IPSWeatherFAT_GetValue('Tomorrow1ForecastLong');
			if(IPSWEATHERFAT_COUNT_DETAILS == 3) $berichtGesamt = IPSWeatherFAT_GetValue('TomorrowForecastLong') . IPSWeatherFAT_GetValue('Tomorrow1ForecastLong') . IPSWeatherFAT_GetValue('Tomorrow2ForecastLong');

			if($berichtGesamt == "") {
			  $berichtGesamt = "Der Wetterbericht steht momentan nicht zur Verfügung!";
			}


			$TodayData = "<b>Aktuell:</b><br/>\n";
			$TodayData .= htmlentities(IPSWeatherFAT_GetValue('TodayForecastShort'), ENT_COMPAT, 'ISO-8859-1')."<br/>\n";
			$TodayData .= "Temperatur: ".IPSWeatherFAT_GetValue('TodayTempCurrent')." &deg;C<br/>\n";
			$TodayData .= "Minimal: ".IPSWeatherFAT_GetValue('TodayTempMin')." &deg;C <br/> Maximal: ".IPSWeatherFAT_GetValue('TodayTempMax')." &deg;C<br/>";
			$TodayData .= IPSWeatherFAT_GetValue('AirHumidity')."<br/>";
			$TodayData .= htmlentities(IPSWeatherFAT_GetValue('Wind'), ENT_COMPAT, 'ISO-8859-1')."<br/>";
			$TodayData .= "Höhe: ".IPSWeatherFAT_GetValue('SeaLevel')."<br/>\n";
			$TodayData .= "LastRefresh: ".IPSWeatherFAT_GetValue('LastRefreshTime')."<br/>\n";

			$ForecastData1  = "<b>".IPSWeatherFAT_GetValue('TomorrowDay')."</b><br/>\n";
			$ForecastData1 .= htmlentities(IPSWeatherFAT_GetValue('TomorrowForecastShort'), ENT_COMPAT, 'ISO-8859-1')."<br/>\n";
			$ForecastData1 .= "min. ".IPSWeatherFAT_GetValue('TomorrowTempMin')." &deg;C <br/> max. ".IPSWeatherFAT_GetValue('TomorrowTempMax')." &deg;C<br/><br/>";

			$ForecastData2  = "<b>".IPSWeatherFAT_GetValue('Tomorrow1Day')."</b><br/>\n";
			if (IPSWeatherFAT_GetValue('Tomorrow1Day')<>'') {
				$ForecastData2 .= htmlentities(IPSWeatherFAT_GetValue('Tomorrow1ForecastShort'), ENT_COMPAT, 'ISO-8859-1')."<br/>\n";
				$ForecastData2 .= "min. ".IPSWeatherFAT_GetValue('Tomorrow1TempMin')." &deg;C <br/> max. ".IPSWeatherFAT_GetValue('Tomorrow1TempMax')." &deg;C<br/><br/>";
			}
			$ForecastData3  = "<b>".IPSWeatherFAT_GetValue('Tomorrow2Day')."</b><br/>\n";
			if (IPSWeatherFAT_GetValue('Tomorrow1Day')<>'') {
				$ForecastData3 .= htmlentities(IPSWeatherFAT_GetValue('Tomorrow2ForecastShort'), ENT_COMPAT, 'ISO-8859-1')."<br/>\n";
				$ForecastData3 .= "min. ".IPSWeatherFAT_GetValue('Tomorrow2TempMin')." &deg;C <br/> max. ".IPSWeatherFAT_GetValue('Tomorrow2TempMax')." &deg;C<br/><br/>";
			}
		?>

		<div id="containerToday">
			<div class="heading"><?php echo IPSWEATHERFAT_DISPLAY;?></div>
			<div class="containerTodayBorder">
				<div class="containerTodayData"><?php echo $TodayData;?></div>
				<div class="containerTodayIcon"><?php echo '<div class="pictureToday"><span></span><img src="'.IPSWeatherFAT_GetValue('TodayIcon').'" alt="'.htmlentities(IPSWeatherFAT_GetValue('TodayForecastShort')).'"/></div>';?></div>
				<div class="containerTodayText"><?php echo IPSWeatherFAT_GetValue('TodayForecastLong');?></div>
			</div>
		</div>
		<div id="containerForecast">
			<div class="heading">Vorhersage f&uuml;r die n&auml;chsten 3 Tage</div>
			<div class="containerForecastBorder">
				<div class="containerForecastData1"><?php echo $ForecastData1;?></div>
				<div class="containerForecastData2"><?php echo $ForecastData2;?></div>
				<div class="containerForecastData3"><?php echo $ForecastData3;?></div>
				<div class="containerForecastIcon1"><?php echo '<div class="pictureForecast"><img src="'.IPSWeatherFAT_GetValue('TomorrowIcon').'" alt="'.htmlentities(IPSWeatherFAT_GetValue('TomorrowForecastShort')).'"/></div>';?></div>
				<div class="containerForecastIcon2"><?php echo '<div class="pictureForecast"><img src="'.IPSWeatherFAT_GetValue('Tomorrow1Icon').'" alt="'.htmlentities(IPSWeatherFAT_GetValue('Tomorrow1ForecastShort')).'"/></div>';?></div>
				<div class="containerForecastIcon3"><?php echo '<div class="pictureForecast"><img src="'.IPSWeatherFAT_GetValue('Tomorrow2Icon').'" alt="'.htmlentities(IPSWeatherFAT_GetValue('Tomorrow2ForecastShort')).'"/></div>';?></div>
				<?php
					if (IPSWEATHERFAT_COUNT_DETAILS >= 1 or $mobile) {
						echo '<div class="containerForecastText1">'.IPSWeatherFAT_GetValue('TomorrowForecastLong').'</div>';
					}
					if (IPSWEATHERFAT_COUNT_DETAILS >= 2 or $mobile) {
						echo '<div class="containerForecastText2">'.IPSWeatherFAT_GetValue('Tomorrow1ForecastLong').'</div>';
					}
					if (IPSWEATHERFAT_COUNT_DETAILS >= 3 or $mobile) {
						echo '<div class="containerForecastText3">'.IPSWeatherFAT_GetValue('Tomorrow2ForecastLong').'</div>';
					}
				?>
			</div>
		</div>

	</body>
</html>