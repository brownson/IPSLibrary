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
	 * Dieses Script aktualisiert die Wetterdaten in IPS mit Google
	 *
	 * @file          IPSWeatherForcastAT_RefreshGoogle.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 15.02.2012<br/>
	 *
	 */

	function IPSWeatherFAT_RefreshGoogle() {
		IPSLogger_Trc(__file__, "Refresh Weather Data Google");
		
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
		$api = @simplexml_load_string(utf8_encode($urlContent));
		if ($api===false) {
			echo 'Error processing Google Weather API ...'.PHP_EOL;
			return;
		}

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
			if ($idx==0) {
				IPSWeatherFAT_SetValueXML($names[$idx],$weather, array(".gif", ".png", IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_LARGE));
			} else {
				IPSWeatherFAT_SetValueXML($names[$idx],$weather, array(".gif", ".png", IPSWEATHERFAT_ICONS_GOOGLE1, IPSWEATHERFAT_ICONS_SMALL));
			}
		}
	}


	/** @}*/
?>