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
	 * Dieses Script aktualisiert die Wetterdaten in IPS mit Wunderground
	 *
	 * @file          IPSWeatherForcastAT_RefreshWunderground.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 15.02.2012<br/>
	 *
	 */


	function IPSWeatherFAT_RefreshWunderground() {

		$urlWunderground      = 'http://api.wunderground.com/api/'.IPSWEATHERFAT_WUNDERGROUND_KEY.'/forecast/lang:DL/q/'.IPSWEATHERFAT_WUNDERGROUND_COUNTRY.'/'.IPSWEATHERFAT_WUNDERGROUND_TOWN.'.xml';
		IPSLogger_Trc(__file__, 'Load Weather Data from Wunderground, URL='.$urlWunderground);
		$urlContent = @Sys_GetURLContent($urlWunderground);
		if ($urlContent===false) {
			IPSLogger_Dbg(__file__, 'Wunderground Weather API is empty ...');
			return false;
		}
		$api = @simplexml_load_string($urlContent);
		if ($api===false) {
			IPSLogger_Dbg(__file__, 'Error processing Wunderground Weather API ...');
			return false;
		}
		$icon_array = array(
			'chanceflurries','chance_of_snow',
			'chancerain','chance_of_rain',
			'chancesleet','chance_of_rain',
			'chancesnow','chance_of_snow',
			'chancetstorms','chance_of_storm',
			'clear','sunny',
			'cloudy','cloudy',
			'flurries','flurries',
			'fog','fog',
			'hazy','haze',
			'mostlycloudy','mostly_cloudy',
			'mostlysunny','mostly_sunny',
			'partlycloudy','partly_cloudy',
			'partlysunny','mostly_sunny',
			'rain','rain',
			'sleet','sleet',
			'snow','snow',
			'sunny','sunny',
			'tstorms','thunderstorm',
			'cloudy','cloudy',
		);

		
		IPSWeatherFAT_SetValue('LastRefreshDateTime', date("Y-m-j H:i:s"));
		IPSWeatherFAT_SetValue('LastRefreshTime', date("H:i"));

		IPSWeatherFAT_SetValueXML('TodayDay',         $api->forecast->simpleforecast->forecastdays->forecastday[0]->date->weekday);
		IPSWeatherFAT_SetValueXML('TomorrowDay',      $api->forecast->simpleforecast->forecastdays->forecastday[1]->date->weekday);
		IPSWeatherFAT_SetValueXML('Tomorrow1Day',     $api->forecast->simpleforecast->forecastdays->forecastday[2]->date->weekday);
		IPSWeatherFAT_SetValueXML('Tomorrow2Day',     $api->forecast->simpleforecast->forecastdays->forecastday[3]->date->weekday);

		IPSWeatherFAT_SetValueXML('TodayTempMin',     $api->forecast->simpleforecast->forecastdays->forecastday[0]->low->celsius);
		IPSWeatherFAT_SetValueXML('TomorrowTempMin',  $api->forecast->simpleforecast->forecastdays->forecastday[1]->low->celsius);
		IPSWeatherFAT_SetValueXML('Tomorrow1TempMin', $api->forecast->simpleforecast->forecastdays->forecastday[2]->low->celsius);
		IPSWeatherFAT_SetValueXML('Tomorrow2TempMin', $api->forecast->simpleforecast->forecastdays->forecastday[3]->low->celsius);

		IPSWeatherFAT_SetValueXML('TodayTempMax',     $api->forecast->simpleforecast->forecastdays->forecastday[0]->high->celsius);
		IPSWeatherFAT_SetValueXML('TomorrowTempMax',  $api->forecast->simpleforecast->forecastdays->forecastday[1]->high->celsius);
		IPSWeatherFAT_SetValueXML('Tomorrow1TempMax', $api->forecast->simpleforecast->forecastdays->forecastday[2]->high->celsius);
		IPSWeatherFAT_SetValueXML('Tomorrow2TempMax', $api->forecast->simpleforecast->forecastdays->forecastday[3]->high->celsius);

		IPSWeatherFAT_SetValueXML('TodayForecastShort',     utf8_decode($api->forecast->simpleforecast->forecastdays->forecastday[0]->conditions));
		IPSWeatherFAT_SetValueXML('TomorrowForecastShort',  utf8_decode($api->forecast->simpleforecast->forecastdays->forecastday[1]->conditions));
		IPSWeatherFAT_SetValueXML('Tomorrow1ForecastShort', utf8_decode($api->forecast->simpleforecast->forecastdays->forecastday[2]->conditions));
		IPSWeatherFAT_SetValueXML('Tomorrow2ForecastShort', utf8_decode($api->forecast->simpleforecast->forecastdays->forecastday[3]->conditions));

		IPSWeatherFAT_SetValueXML('TodayIcon',     $api->forecast->simpleforecast->forecastdays->forecastday[0]->icon, IPSWEATHERFAT_ICONS_SMALL, '.png', $icon_array);
		IPSWeatherFAT_SetValueXML('TomorrowIcon',  $api->forecast->simpleforecast->forecastdays->forecastday[1]->icon, IPSWEATHERFAT_ICONS_SMALL, '.png', $icon_array);
		IPSWeatherFAT_SetValueXML('Tomorrow1Icon', $api->forecast->simpleforecast->forecastdays->forecastday[2]->icon, IPSWEATHERFAT_ICONS_SMALL, '.png', $icon_array);
		IPSWeatherFAT_SetValueXML('Tomorrow2Icon', $api->forecast->simpleforecast->forecastdays->forecastday[3]->icon, IPSWEATHERFAT_ICONS_SMALL, '.png', $icon_array);


		$urlWunderground      = 'http://api.wunderground.com/api/'.IPSWEATHERFAT_WUNDERGROUND_KEY.'/conditions/lang:DL/q/'.IPSWEATHERFAT_WUNDERGROUND_COUNTRY.'/'.IPSWEATHERFAT_WUNDERGROUND_TOWN.'.xml';
		IPSLogger_Trc(__file__, 'Load Weather Data from Wunderground, URL='.$urlWunderground);
		$urlContent = @Sys_GetURLContent($urlWunderground);
		if ($urlContent===false) {
			IPSLogger_Dbg(__file__, 'Wunderground Weather API is empty ...');
			return false;
		}
		$api = @simplexml_load_string(utf8_encode($urlContent));
		if ($api===false) {
			IPSLogger_Dbg(__file__, 'Error processing Wunderground Weather API ...');
			return false;
		}

		IPSWeatherFAT_SetValueXML('TodayForecastShort', $api->xpath('//current_observation/weather'));
		IPSWeatherFAT_SetValueXML('TodayTempCurrent',   str_replace('.',',',$api->current_observation->temp_c));
		IPSWeatherFAT_SetValueXML('AirHumidity',        $api->xpath('//current_observation/relative_humidity'), 'Feuchtigkeit: ', '');
		//IPSWeatherFAT_SetValueXML('Wind',               utf8_decode($api->current_observation->wind_dir),          'Wind:', '');
		IPSWeatherFAT_SetValueXML('Wind',               utf8_decode(utf8_decode($api->current_observation->wind_dir)),          'Wind:', '');
		IPSWeatherFAT_SetValueXML('TodayIcon',          $api->xpath('//current_observation/icon'), IPSWEATHERFAT_ICONS_LARGE, '.png', $icon_array);

		return true;
	}

	/** @}*/
?>