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

		$api = simplexml_load_string(utf8_encode(@Sys_GetURLContent($urlGoogle)));
		echo $urlGoogle;

		IPSWeatherFAT_SetValue('LastRefreshDateTime', date("Y-m-j H:i:s"));
		IPSWeatherFAT_SetValue('LastRefreshTime', date("H:i"));

		// Aktuelles Wetter
		IPSWeatherFAT_SetValue('TodayForecastShort', (string)$api->weather->current_conditions->condition->attributes()->data);
		IPSWeatherFAT_SetValue('TodayTempCurrent',   (string)$api->weather->current_conditions->temp_c->attributes()->data);
		IPSWeatherFAT_SetValue('AirHumidity',        str_replace("Feuchtigkeit", "rel.Luftfeuchte", $api->weather->current_conditions->humidity->attributes()->data));
		IPSWeatherFAT_SetValue('Wind',               (string)$api->weather->current_conditions->wind_condition->attributes()->data);
		IPSWeatherFAT_SetValue('TodayIcon',          str_replace(".gif", ".png", str_replace(IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_LARGE, $api->weather->current_conditions->icon->attributes()->data)));

		// Wettervorhersage heute, morgen, in zwei und in drei Tagen ($wetter[1] bis $wetter[4])
		$i = 1;
		foreach($api->weather->forecast_conditions as $weather)
		{
			if ($i==1) {
				IPSWeatherFAT_SetValue('TodayDay',               str_replace($DaySourceArray, $DayDisplayArray, $weather->day_of_week->attributes()->data));
				IPSWeatherFAT_SetValue('TodayForecastShort',     (string)$weather->condition->attributes()->data);
				IPSWeatherFAT_SetValue('TodayTempMin',           (string)$weather->low->attributes()->data);
				IPSWeatherFAT_SetValue('TodayTempMax',           (string)$weather->high->attributes()->data);
				IPSWeatherFAT_SetValue('TodayIcon',              str_replace(".gif", ".png", str_replace(IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_LARGE, $weather->icon->attributes()->data)));
			} else if ($i==2) {
				IPSWeatherFAT_SetValue('TomorrowDay',            str_replace($DaySourceArray, $DayDisplayArray, $weather->day_of_week->attributes()->data));
				IPSWeatherFAT_SetValue('TomorrowForecastShort',  (string)$weather->condition->attributes()->data);
				IPSWeatherFAT_SetValue('TomorrowTempMin',        (string)$weather->low->attributes()->data);
				IPSWeatherFAT_SetValue('TomorrowTempMax',        (string)$weather->high->attributes()->data);
				IPSWeatherFAT_SetValue('TomorrowIcon',           str_replace(".gif", ".png", str_replace(IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_SMALL, $weather->icon->attributes()->data)));
			} else if ($i==3) {
				IPSWeatherFAT_SetValue('Tomorrow1Day',           str_replace($DaySourceArray, $DayDisplayArray, $weather->day_of_week->attributes()->data));
				IPSWeatherFAT_SetValue('Tomorrow1ForecastShort', (string)$weather->condition->attributes()->data);
				IPSWeatherFAT_SetValue('Tomorrow1TempMin',       (string)$weather->low->attributes()->data);
				IPSWeatherFAT_SetValue('Tomorrow1TempMax',       (string)$weather->high->attributes()->data);
				IPSWeatherFAT_SetValue('Tomorrow1Icon',          str_replace(".gif", ".png", str_replace(IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_SMALL, $weather->icon->attributes()->data)));
			} else if ($i==4) {
				IPSWeatherFAT_SetValue('Tomorrow2Day',           str_replace($DaySourceArray, $DayDisplayArray, $weather->day_of_week->attributes()->data));
				IPSWeatherFAT_SetValue('Tomorrow2ForecastShort', (string)$weather->condition->attributes()->data);
				IPSWeatherFAT_SetValue('Tomorrow2TempMin',       (string)$weather->low->attributes()->data);
				IPSWeatherFAT_SetValue('Tomorrow2TempMax',       (string)$weather->high->attributes()->data);
				IPSWeatherFAT_SetValue('Tomorrow2Icon',          str_replace(".gif", ".png", str_replace(IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_SMALL, $weather->icon->attributes()->data)));
			} else  {
				IPSLogger_Err(__file__, "Receive unknown Weather Forecast Condition");
			}
			$i++;
		}

		// Wetter für Niederösterreich von ORF auslesen
		$lHTML=file_get_contents(IPSWEATHERFAT_ORF_URL);

		$forcast = ExtractData($lHTML, '<div class="fulltextWrapper" role="article">', '<div class="webcamLinks">', true, false);
		$forcastToday = '<h2>'.ExtractData($forcast, '<h2>', '</h2>').'</h2>'.ExtractData($forcast, '<p>', '</p>', false, false);

		$forcast = ExtractData($forcast, '</p>', '<div class="webcamLinks">', true, false);
		$forcastTomorrow  = '<h2>'.ExtractData($forcast, '<h2>', '</h2>').'</h2>'.ExtractData($forcast, '<p>', '</p>', false, false);

		$forcast = ExtractData($forcast, '</p>', '<div class="webcamLinks">', true, false);
		$forcastTomorrow1 = '<h2>'.ExtractData($forcast, '<h2>', '</h2>').'</h2>'.ExtractData($forcast, '<p>', '</p>', false, false);

		$forcast = ExtractData($forcast, '</p>', '<div class="webcamLinks">', true, false);
		$forcastTomorrow2 = '<h2>'.ExtractData($forcast, '<h2>', '</h2>').'</h2>'.ExtractData($forcast, '<p>', '</p>', false, false);

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
	   if ($removeKey1 and $removeKey2) {
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