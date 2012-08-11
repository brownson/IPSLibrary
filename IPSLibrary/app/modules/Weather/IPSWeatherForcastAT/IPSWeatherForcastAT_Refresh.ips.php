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


	/**@defgroup ipsweatherforcastat IPSWeatherForcastAT 
	 * @ingroup modules_weather
	 * @{
	 *
	 * Dieses Script aktualisiert die Wetterdaten in IPS
	 *
	 * @file          IPSWeatherForcastAT_Refresh.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 15.02.2012<br/>
	 *
	 */


	IPSUtils_Include ("IPSWeatherForcastAT_Constants.inc.php",     "IPSLibrary::app::modules::Weather::IPSWeatherForcastAT");
	IPSUtils_Include ("IPSWeatherForcastAT_Configuration.inc.php", "IPSLibrary::config::modules::Weather::IPSWeatherForcastAT");
	IPSUtils_Include ("IPSWeatherForcastAT_Utils.inc.php",         "IPSLibrary::app::modules::Weather::IPSWeatherForcastAT");
	IPSUtils_Include ("IPSLogger.inc.php",                         "IPSLibrary::app::core::IPSLogger");

	if (Sys_Ping(IPSWEATHERFAT_EXTERNAL_IP, 100)) {
		IPSLogger_Trc(__file__, "Refresh Weather Data");
		
		$stationGoogle   = IPSWEATHERFAT_GOOGLE_PLACE."-".IPSWEATHERFAT_GOOGLE_COUNTRY;
		$urlGoogle       = "http://www.google.com/ig/api?weather=".$stationGoogle."&hl=".IPSWEATHERFAT_GOOGLE_LANG;
		$DaySourceArray  = array('Mo.','Di.','Mi.','Do.','Fr.','Sa.','So.');
		$DayDisplayArray = array('Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag');

		echo $urlGoogle.PHP_EOL;
		$urlContent = @Sys_GetURLContent($urlGoogle);
		if ($urlContent===false) {
			echo 'Google Weather API is empty ...'.PHP_EOL;
			return;
		}
		$api = simplexml_load_string(utf8_encode($urlContent));

		IPSWeatherFAT_SetValue('LastRefreshDateTime', date("Y-m-j H:i:s"));
		IPSWeatherFAT_SetValue('LastRefreshTime', date("H:i"));

		IPSWeatherFAT_SetValueXML('TodayForecastShort', $api->xpath('//weather/current_conditions/condition/@data'));
		IPSWeatherFAT_SetValueXML('TodayTempCurrent',   $api->xpath('//weather/current_conditions/temp_c/@data'));
		IPSWeatherFAT_SetValueXML('AirHumidity',        $api->xpath('//weather/current_conditions/humidity/@data'), array("Feuchtigkeit", "rel.Luftfeuchte"));
		IPSWeatherFAT_SetValueXML('Wind',               $api->xpath('//weather/current_conditions/wind_condition/@data'));
		IPSWeatherFAT_SetValueXML('TodayIcon',          $api->xpath('//weather/current_conditions/icon/@data'), array(".gif", ".png", IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_LARGE));

		// Wettervorhersage heute, morgen, in zwei und in drei Tagen ($wetter[1] bis $wetter[4])
		$names = array('TodayDay', 'TomorrowDay', 'Tomorrow1Day', 'Tomorrow2Day');
		foreach($api->xpath('//weather/forecast_conditions/day_of_week/@data') as $idx=>$weather) {
			//print_r($weather);
			IPSWeatherFAT_SetValueXML($names[$idx],$weather, array($DaySourceArray, $DayDisplayArray));
		}
		$names = array('TodayForecastShort', 'TomorrowForecastShort', 'Tomorrow1ForecastShort', 'Tomorrow2ForecastShort');
		foreach($api->xpath('//weather/forecast_conditions/condition/@data') as $idx=>$weather) {
			//print_r($weather);
			IPSWeatherFAT_SetValueXML($names[$idx],$weather);
		}
		$names = array('TodayTempMin', 'TomorrowTempMin', 'Tomorrow1TempMin', 'Tomorrow2TempMin');
		foreach($api->xpath('//weather/forecast_conditions/low/@data') as $idx=>$weather) {
			//print_r($weather);
			IPSWeatherFAT_SetValueXML($names[$idx],$weather);
		}
		$names = array('TodayTempMax', 'TomorrowTempMax', 'Tomorrow1TempMax', 'Tomorrow2TempMax');
		foreach($api->xpath('//weather/forecast_conditions/high/@data') as $idx=>$weather) {
			//print_r($weather);
			IPSWeatherFAT_SetValueXML($names[$idx],$weather);
		}
		$names = array('TodayIcon', 'TomorrowIcon', 'Tomorrow1Icon', 'Tomorrow2Icon');
		foreach($api->xpath('//weather/forecast_conditions/icon/@data') as $idx=>$weather) {
			//print_r($weather);
			IPSWeatherFAT_SetValueXML($names[$idx],$weather, array(".gif", ".png", IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_SMALL));
		}

		// Wetter für Niederösterreich von ORF auslesen
		$lHTML=file_get_contents(IPSWEATHERFAT_ORF_URL);

		$forcast = ExtractData($lHTML, '<div class="fulltextWrapper" role="article">', '<div class="webcamLinks', true, false);
		$forcastToday = '<h2>'.ExtractData($forcast, '<h2>', '</h2>').'</h2>';
		$forcast = ExtractData($forcast, '</h2>', '<div class="webcamLinks', true, false);
		$forcastToday .= ExtractData($forcast, '<p>', '<h2>', false, true);

		$forcast = ExtractData($forcast, '<h2>', '<div class="webcamLinks', false, false);
		$forcastTomorrow  = '<h2>'.ExtractData($forcast, '<h2>', '</h2>').'</h2>';
		$forcast = ExtractData($forcast, '</h2>', '<div class="webcamLinks', true, false);
		$forcastTomorrow  .= ExtractData($forcast, '<p>', '<h2>', false, true);

		$forcast = ExtractData($forcast, '<h2>', '<div class="webcamLinks', false, false);
		$forcastTomorrow1 = '<h2>'.ExtractData($forcast, '<h2>', '</h2>').'</h2>';
		$forcast = ExtractData($forcast, '</h2>', '<div class="webcamLinks', true, false);
		$forcastTomorrow1 .= ExtractData($forcast, '<p>', '<h2>', false, true);

		$forcast = ExtractData($forcast, '<h2>', '<div class="webcamLinks', false, false);
		$forcastTomorrow2 = '<h2>'.ExtractData($forcast, '<h2>', '</h2>').'</h2>';
		$forcast = ExtractData($forcast, '<h2>', '<div class="webcamLinks', true, false);
		$forcastTomorrow2 .= ExtractData($forcast, '<p>',  '<div class="webcamLinks', false, true);

		//echo 'Heute    = '.$forcastToday.PHP_EOL;
		//echo 'Morgen   = '.$forcastTomorrow.PHP_EOL;
		//echo 'Morgen+1 = '.$forcastTomorrow1.PHP_EOL;
		//echo 'Morgen+2 = '.$forcastTomorrow2.PHP_EOL;
		IPSWeatherFAT_SetValue('TodayForecastLong',     $forcastToday);
		IPSWeatherFAT_SetValue('TomorrowForecastLong',  $forcastTomorrow);
		IPSWeatherFAT_SetValue('Tomorrow1ForecastLong', $forcastTomorrow1);
		IPSWeatherFAT_SetValue('Tomorrow2ForecastLong', $forcastTomorrow2);

	} else {
		IPSLogger_Trc(__file__, "No Connection - Refresh of Weather Data NOT possible");

	}


	function ExtractData($data, $key1, $key2, $removeKey1=true, $removeKey2=true) {
	   $strPos1 = strpos($data, $key1);
	   $strPos2 = strpos($data, $key2);
	   if ($strPos1===false) {
	      $result = 0;
	      echo 'Key1 "'.$key1.'" NOT found !'.PHP_EOL;
	   } elseif ($strPos2===false) {
	      $result = strlen($data);
	      echo 'Key2 "'.$key2.'" NOT found !'.PHP_EOL;
		} elseif ($removeKey1 and $removeKey2) {
	   	$result  =substr($data, $strPos1+strlen($key1), $strPos2-$strPos1-strlen($key1));
	   } elseif ($removeKey1) {
	   	$result  =substr($data, $strPos1+strlen($key1), $strPos2-$strPos1-strlen($key1)+strlen($key2));
	   } elseif ($removeKey2) {
	   	$result  =substr($data, $strPos1, $strPos2-$strPos1-strlen($key2));
	   } else {
	   	$result  =substr($data, $strPos1, $strPos2-$strPos1+strlen($key2));
	   }
	   return $result;
	}

	/** @}*/
?>