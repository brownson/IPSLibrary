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
	 * Dieses Script aktualisiert die Wetterdaten in IPS mit Yahoo
	 *
	 * @file          IPSWeatherForcastAT_RefreshYahoo.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 15.02.2012<br/>
	 *
	 */

	function IPSWeatherFAT_RefreshYahoo() {

		$urlYahoo       = 'http://weather.yahooapis.com/forecastrss?w='.IPSWEATHERFAT_YAHOO_WOEID.'&u=c';
		$DaySourceArray  = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
		$DayDisplayArray = array('Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag');

		$icon_array = array(
			'mixed rain and snow','chance_of_snow',
			'mixed rain and sleet','chance_of_snow',
			'mixed snow and sleet','chance_of_snow',
			'freezing drizzle','chancerain',
			'drizzle','chancerain',
			'chancerain','chance_of_rain',
			'chancesleet','chance_of_rain',
			'chancesnow','chance_of_snow',
			'chancetstorms','chance_of_storms',
			'clear','sunny',
			'cloudy','cloudy',
			'flurries','flurries',
			'flurries','flurries',
			'smoky','fog',
			'fog','fog',
			'fog','fog',
			'dust','fog',
			'foggy','fog',
			'hazy','haze',
			'mostlycloudy','mostly_cloudy',
			'mostly cloudy','mostly_cloudy',
			'mostly cloudy (day)','mostly_cloudy',
			'mostly cloudy (night)','mostly_cloudy',
			'partly cloudy (night)','partly_cloudy',
			'partly cloudy (day)','partly_cloudy',
			'partly cloudy','partly_cloudy',
			'partlycloudy','partly_cloudy',
			'mostly sunny','mostly_sunny',
			'partly sunny','mostly_sunny',
			'showers','rain',
			'hail','rain',
			'rain','rain',
			'sleet','sleet',
			'snow','snow',
			'mixed rain and hail','snow',
			'scattered showers','snow',
			'heavy snow','snow',
			'scattered snow showers','snow',
			'heavy snow','snow',
			'snow showers','snow',
			'snow flurries','snow',
			'light snow showers','snow',
			'blowing snow','snow',
			'sunny','sunny',
			'clear (night)','sunny',
			'fair (night)','sunny',
			'fair (day)','sunny',
			'hot','sunny',
			'tstorms','tstorms',
			'tstorms','tstorms',
			'windy','tstorms',
			'blustery','tstorms',
			'tornado','tstorms',
			'tropical storm','tstorms',
			'hurricane','tstorms',
			'severe thunderstorms','tstorms',
			'thunderstorms','tstorms',
			'scattered thunderstorms','tstorms',
			'isolated thunderstorms','tstorms',
			'scattered thunderstorms','tstorms',
			'thundershowers','tstorms',
			'isolated thundershowers','tstorms',
			'cloudy','cloudy',
		);

		echo $urlYahoo.PHP_EOL;
		$urlContent = @Sys_GetURLContent($urlYahoo);
		if ($urlContent===false) {
			echo 'Yahoo Weather API is empty ...'.PHP_EOL;
			return;
		}
		$api = @simplexml_load_string(utf8_encode($urlContent));
		if ($api===false) {
			echo 'Error processing Yahoo Weather API ...'.PHP_EOL;
			return;
		}

		IPSWeatherFAT_SetValue('LastRefreshDateTime', date("Y-m-j H:i:s"));
		IPSWeatherFAT_SetValue('LastRefreshTime', date("H:i"));

		IPSWeatherFAT_SetValueXML('TodayForecastShort', IPSWeatherFAT_GetConditionYahoo($api->xpath('//yweather:condition/@text')[0]));
		IPSWeatherFAT_SetValueXML('TodayTempCurrent',   $api->xpath('//yweather:condition/@temp'));
		IPSWeatherFAT_SetValueXML('AirHumidity',        $api->xpath('//yweather:atmosphere/@humidity'), 'Feuchtigkeit: ', ' %');
		IPSWeatherFAT_SetValueXML('Wind',               $api->xpath('//yweather:wind/@speed'),          'Wind:', ' km/h');

		IPSWeatherFAT_SetValueXML('Tomorrow1Day',     '');
		IPSWeatherFAT_SetValueXML('Tomorrow2Day',     '');
		IPSWeatherFAT_SetValueXML('Tomorrow1ForecastShort', '');
		IPSWeatherFAT_SetValueXML('Tomorrow2ForecastShort', '');
		IPSWeatherFAT_SetValueXML('Tomorrow1Icon', '');
		IPSWeatherFAT_SetValueXML('Tomorrow2Icon', '');

		$names = array('TodayDay', 'TomorrowDay', 'Tomorrow1Day', 'Tomorrow2Day');
		foreach($api->xpath('//yweather:forecast/@day') as $idx=>$weather) {
			IPSWeatherFAT_SetValueXML($names[$idx],$weather, '', '', array($DaySourceArray, $DayDisplayArray));
		}

		// Wettervorhersage heute, morgen, in zwei und in drei Tagen ($wetter[1] bis $wetter[4])
		$names = array('TodayForecastShort', 'TomorrowForecastShort', 'Tomorrow1ForecastShort', 'Tomorrow2ForecastShort');
		foreach($api->xpath('//yweather:forecast/@text') as $idx=>$weather) {
			IPSWeatherFAT_SetValueXML($names[$idx],$weather);
		}

		$names = array('TodayTempMin','TomorrowTempMin', 'Tomorrow1TempMin', 'Tomorrow2TempMin');
		foreach($api->xpath('//yweather:forecast/@low') as $idx=>$weather) {
			IPSWeatherFAT_SetValueXML($names[$idx],$weather);
		}
		$names = array('TodayTempMax','TomorrowTempMax', 'Tomorrow1TempMax', 'Tomorrow2TempMax');
		foreach($api->xpath('//yweather:forecast/@high') as $idx=>$weather) {
			IPSWeatherFAT_SetValueXML($names[$idx],$weather);
		}

		$names = array('TodayIcon', 'TomorrowIcon', 'Tomorrow1Icon', 'Tomorrow2Icon');
		foreach($api->xpath('//yweather:forecast/@text') as $idx=>$weather) {
			if ($idx==0) {
				IPSWeatherFAT_SetValueXML($names[$idx],strtolower($weather[0]), IPSWEATHERFAT_ICONS_LARGE, '.png', $icon_array);
			} else {
				IPSWeatherFAT_SetValueXML($names[$idx],strtolower($weather[0]), IPSWEATHERFAT_ICONS_SMALL, '.png', $icon_array);
			}
		}
	}

	function IPSWeatherFAT_GetConditionYahoo($condition) {
		if($condition == 'AM Clouds/PM Sun') $return = 'vormittags bewölkt/nachmittags sonnig';
		elseif($condition == 'AM Drizzle') $return = 'vormittags Nieselregen';
		elseif($condition == 'AM Drizzle/Wind') $return = 'vorm. Nieselregen/Wind';
		elseif($condition == 'AM Fog/PM Clouds') $return = 'vormittags Nebel/nachmittags bewölkt';
		elseif($condition == 'AM Fog/PM Sun') $return = 'vormittags Nebel, nachmittags sonnig';
		elseif($condition == 'AM Ice') $return = 'vorm. Eis';
		elseif($condition == 'AM Light Rain') $return = 'vormittags leichter Regen';
		elseif($condition == 'AM Light Rain/Wind') $return = 'vorm. leichter Regen/Wind';
		elseif($condition == 'AM Light Snow') $return = 'vormittags leichter Schneefall';
		elseif($condition == 'AM Rain') $return = 'vormittags Regen';
		elseif($condition == 'AM Rain/Snow Showers') $return = 'vorm. Regen-/Schneeschauer';
		elseif($condition == 'AM Rain/Snow') $return = 'vormittags Regen/Schnee';
		elseif($condition == 'AM Rain/Snow/Wind') $return = 'vorm. Regen/Schnee/Wind';
		elseif($condition == 'AM Rain/Wind') $return = 'vorm. Regen/Wind';
		elseif($condition == 'AM Showers') $return = 'vormittags Schauer';
		elseif($condition == 'AM Showers/Wind') $return = 'vormittags Schauer/Wind';
		elseif($condition == 'AM Snow Showers') $return = 'vormittags Schneeschauer';
		elseif($condition == 'AM Snow') $return = 'vormittags Schnee';
		elseif($condition == 'AM Thundershowers') $return = 'vorm. Gewitterschauer';
		elseif($condition == 'Blowing Snow') $return = 'Schneetreiben';
		elseif($condition == 'Clear') $return = 'Klar';
		elseif($condition == 'Clear/Windy') $return = 'Klar/Windig';
		elseif($condition == 'Clouds Early/Clearing Late') $return = 'früh Wolken/später klar';
		elseif($condition == 'Cloudy') $return = 'Bewölkt';
		elseif($condition == 'Cloudy/Wind') $return = 'Bewölkt/Wind';
		elseif($condition == 'Cloudy/Windy') $return = 'Wolkig/Windig';
		elseif($condition == 'Drifting Snow') $return = 'Schneetreiben';
		elseif($condition == 'Drifting Snow/Windy') $return = 'Schneetreiben/Windig';
		elseif($condition == 'Drizzle Early') $return = 'früh Nieselregen';
		elseif($condition == 'Drizzle Late') $return = 'später Nieselregen';
		elseif($condition == 'Drizzle') $return = 'Nieselregen';
		elseif($condition == 'Drizzle/Fog') $return = 'Nieselregen/Nebel';
		elseif($condition == 'Drizzle/Wind') $return = 'Nieselregen/Wind';
		elseif($condition == 'Drizzle/Windy') $return = 'Nieselregen/Windig';
		elseif($condition == 'Fair') $return = 'Heiter';
		elseif($condition == 'Fair/Windy') $return = 'Heiter/Windig';
		elseif($condition == 'Few Showers') $return = 'vereinzelte Schauer';
		elseif($condition == 'Few Showers/Wind') $return = 'vereinzelte Schauer/Wind';
		elseif($condition == 'Few Snow Showers') $return = 'vereinzelt Schneeschauer';
		elseif($condition == 'Fog Early/Clouds Late') $return = 'früh Nebel, später Wolken';
		elseif($condition == 'Fog Late') $return = 'später neblig';
		elseif($condition == 'Fog') $return = 'Nebel';
		elseif($condition == 'Fog/Windy') $return = 'Nebel/Windig';
		elseif($condition == 'Foggy') $return = 'neblig';
		elseif($condition == 'Freezing Drizzle') $return = 'gefrierender Nieselregen';
		elseif($condition == 'Freezing Drizzle/Windy') $return = 'gefrierender Nieselregen/Windig';
		elseif($condition == 'Freezing Rain') $return = 'gefrierender Regen';
		elseif($condition == 'Haze') $return = 'Dunst';
		elseif($condition == 'Heavy Drizzle') $return = 'starker Nieselregen';
		elseif($condition == 'Heavy Rain Shower') $return = 'Starker Regenschauer';
		elseif($condition == 'Heavy Rain') $return = 'Starker Regen';
		elseif($condition == 'Heavy Rain/Wind') $return = 'starker Regen/Wind';
		elseif($condition == 'Heavy Rain/Windy') $return = 'Starker Regen/Windig';
		elseif($condition == 'Heavy Snow Shower') $return = 'Starker Schneeschauer';
		elseif($condition == 'Heavy Snow') $return = 'Starker Schneefall';
		elseif($condition == 'Heavy Snow/Wind') $return = 'Starker Schneefall/Wind';
		elseif($condition == 'Heavy Thunderstorm') $return = 'Schweres Gewitter';
		elseif($condition == 'Heavy Thunderstorm/Windy') $return = 'Schweres Gewitter/Windig';
		elseif($condition == 'Ice Crystals') $return = 'Eiskristalle';
		elseif($condition == 'Ice Late') $return = 'später Eis';
		elseif($condition == 'Isolated T-storms') $return = 'Vereinzelte Gewitter';
		elseif($condition == 'Isolated Thunderstorms') $return = 'Vereinzelte Gewitter';
		elseif($condition == 'Light Drizzle') $return = 'Leichter Nieselregen';
		elseif($condition == 'Light Freezing Drizzle') $return = 'Leichter gefrierender Nieselregen';
		elseif($condition == 'Light Freezing Rain') $return = 'Leichter gefrierender Regen';
		elseif($condition == 'Light Freezing Rain/Fog') $return = 'Leichter gefrierender Regen/Nebel';
		elseif($condition == 'Light Rain Early') $return = 'anfangs leichter Regen';
		elseif($condition == 'Light Rain') $return = 'Leichter Regen';
		elseif($condition == 'Light Rain Late') $return = 'später leichter Regen';
		elseif($condition == 'Light Rain Shower') $return = 'Leichter Regenschauer';
		elseif($condition == 'Light Rain Shower/Fog') $return = 'Leichter Regenschauer/Nebel';
		elseif($condition == 'Light Rain Shower/Windy') $return = 'Leichter Regenschauer/windig';
		elseif($condition == 'Light Rain with Thunder') $return = 'Leichter Regen mit Gewitter';
		elseif($condition == 'Light Rain/Fog') $return = 'Leichter Regen/Nebel';
		elseif($condition == 'Light Rain/Freezing Rain') $return = 'Leichter Regen/Gefrierender Regen';
		elseif($condition == 'Light Rain/Wind Early') $return = 'früh leichter Regen/Wind';
		elseif($condition == 'Light Rain/Wind Late') $return = 'später leichter Regen/Wind';
		elseif($condition == 'Light Rain/Wind') $return = 'leichter Regen/Wind';
		elseif($condition == 'Light Rain/Windy') $return = 'Leichter Regen/Windig';
		elseif($condition == 'Light Sleet') $return = 'Leichter Schneeregen';
		elseif($condition == 'Light Snow Early') $return = 'früher leichter Schneefall';
		elseif($condition == 'Light Snow Grains') $return = 'Leichter Schneegriesel';
		elseif($condition == 'Light Snow Late') $return = 'später leichter Schneefall';
		elseif($condition == 'Light Snow Shower') $return = 'Leichter Schneeschauer';
		elseif($condition == 'Light Snow Shower/Fog') $return = 'Leichter Schneeschauer/Nebel';
		elseif($condition == 'Light Snow with Thunder') $return = 'Leichter Schneefall mit Gewitter';
		elseif($condition == 'Light Snow') $return = 'Leichter Schneefall';
		elseif($condition == 'Light Snow/Fog') $return = 'Leichter Schneefall/Nebel';
		elseif($condition == 'Light Snow/Freezing Rain') $return = 'Leichter Schneefall/Gefrierender Regen';
		elseif($condition == 'Light Snow/Wind') $return = 'Leichter Schneefall/Wind';
		elseif($condition == 'Light Snow/Windy') $return = 'Leichter Schneeschauer/Windig';
		elseif($condition == 'Light Snow/Windy/Fog') $return = 'Leichter Schneefall/Windig/Nebel';
		elseif($condition == 'Mist') $return = 'Nebel';
		elseif($condition == 'Mostly Clear') $return = 'überwiegend Klar';
		elseif($condition == 'Mostly Cloudy') $return = 'Überwiegend bewölkt';
		elseif($condition == 'Mostly Cloudy/Wind') $return = 'meist bewölkt/Wind';
		elseif($condition == 'Mostly sunny') $return = 'Überwiegend sonnig';
		elseif($condition == 'Partial Fog') $return = 'teilweise Nebel';
		elseif($condition == 'Partly Cloudy') $return = 'Teilweise bewölkt';
		elseif($condition == 'Partly Cloudy/Wind') $return = 'teilweise bewölkt/Wind';
		elseif($condition == 'Patches of Fog') $return = 'Nebelfelder';
		elseif($condition == 'Patches of Fog/Windy') $return = 'Nebelfelder/Windig';
		elseif($condition == 'PM Drizzle') $return = 'nachm. Nieselregen';
		elseif($condition == 'PM Fog') $return = 'nachmittags Nebel';
		elseif($condition == 'PM Light Snow') $return = 'nachmittags leichter Schneefall';
		elseif($condition == 'PM Light Rain') $return = 'nachmittags leichter Regen';
		elseif($condition == 'PM Light Rain/Wind') $return = 'nachm. leichter Regen/Wind';
		elseif($condition == 'PM Light Snow/Wind') $return = 'nachm. leichter Schneefall/Wind';
		elseif($condition == 'PM Rain') $return = 'nachmittags Regen';
		elseif($condition == 'PM Rain/Snow Showers') $return = 'nachmittags Regen/Schneeschauer';
		elseif($condition == 'PM Rain/Snow') $return = 'nachmittags Regen/Schnee';
		elseif($condition == 'PM Rain/Wind') $return = 'nachm. Regen/Wind';
		elseif($condition == 'PM Showers') $return = 'nachmittags Schauer';
		elseif($condition == 'PM Showers/Wind') $return = 'nachmittags Schauer/Wind';
		elseif($condition == 'PM Snow Showers') $return = 'nachmittags Schneeschauer';
		elseif($condition == 'PM Snow Showers/Wind') $return = 'nachm. Schneeschauer/Wind';
		elseif($condition == 'PM Snow') $return = 'nachm. Schnee';
		elseif($condition == 'PM T-storms') $return = 'nachmittags Gewitter';
		elseif($condition == 'PM Thundershowers') $return = 'nachmittags Gewitterschauer';
		elseif($condition == 'PM Thunderstorms') $return = 'nachm. Gewitter';
		elseif($condition == 'Rain and Snow') $return = 'Schneeregen';
		elseif($condition == 'Rain and Snow/Windy') $return = 'Regen und Schnee/Windig';
		elseif($condition == 'Rain/Snow Showers/Wind') $return = 'Regen/Schneeschauer/Wind';
		elseif($condition == 'Rain Early') $return = 'früh Regen';
		elseif($condition == 'Rain Late') $return = 'später Regen';
		elseif($condition == 'Rain Shower') $return = 'Regenschauer';
		elseif($condition == 'Rain Shower/Windy') $return = 'Regenschauer/Windig';
		elseif($condition == 'Rain to Snow') $return = 'Regen, in Schnee übergehend';
		elseif($condition == 'Rain') $return = 'Regen';
		elseif($condition == 'Rain/Snow Early') $return = 'früh Regen/Schnee';
		elseif($condition == 'Rain/Snow Late') $return = 'später Regen/Schnee';
		elseif($condition == 'Rain/Snow Showers Early') $return = 'früh Regen-/Schneeschauer';
		elseif($condition == 'Rain/Snow Showers Late') $return = 'später Regen-/Schneeschnauer';
		elseif($condition == 'Rain/Snow Showers') $return = 'Regen/Schneeschauer';
		elseif($condition == 'Rain/Snow') $return = 'Regen/Schnee';
		elseif($condition == 'Rain/Snow/Wind') $return = 'Regen/Schnee/Wind';
		elseif($condition == 'Rain/Thunder') $return = 'Regen/Gewitter';
		elseif($condition == 'Rain/Wind Early') $return = 'früh Regen/Wind';
		elseif($condition == 'Rain/Wind Late') $return = 'später Regen/Wind';
		elseif($condition == 'Rain/Wind') $return = 'Regen/Wind';
		elseif($condition == 'Rain/Windy') $return = 'Regen/Windig';
		elseif($condition == 'Scattered Showers') $return = 'vereinzelte Schauer';
		elseif($condition == 'Scattered Showers/Wind') $return = 'vereinzelte Schauer/Wind';
		elseif($condition == 'Scattered Snow Showers') $return = 'vereinzelte Schneeschauer';
		elseif($condition == 'Scattered Snow Showers/Wind') $return = 'vereinzelte Schneeschauer/Wind';
		elseif($condition == 'Scattered T-storms') $return = 'vereinzelte Gewitter';
		elseif($condition == 'Scattered Thunderstorms') $return = 'vereinzelte Gewitter';
		elseif($condition == 'Shallow Fog') $return = 'flacher Nebel';
		elseif($condition == 'Showers') $return = 'Schauer';
		elseif($condition == 'Showers Early') $return = 'früh Schauer';
		elseif($condition == 'Showers Late') $return = 'später Schauer';
		elseif($condition == 'Showers in the Vicinity') $return = 'Regenfälle in der Nähe';
		elseif($condition == 'Showers/Wind') $return = 'Schauer/Wind';
		elseif($condition == 'Sleet and Freezing Rain') $return = 'Schneeregen und gefrierender Regen';
		elseif($condition == 'Sleet/Windy') $return = 'Schneeregen/Windig';
		elseif($condition == 'Snow Grains') $return = 'Schneegriesel';
		elseif($condition == 'Snow Late') $return = 'später Schnee';
		elseif($condition == 'Snow Shower') $return = 'Schneeschauer';
		elseif($condition == 'Snow Showers Early') $return = 'früh Schneeschauer';
		elseif($condition == 'Snow Showers Late') $return = 'später Schneeschauer';
		elseif($condition == 'Snow Showers') $return = 'Schneeschauer';
		elseif($condition == 'Snow Showers/Wind') $return = 'Schneeschauer/Wind';
		elseif($condition == 'Snow to Rain') $return = 'Schneeregen';
		elseif($condition == 'Snow') $return = 'Schneefall';
		elseif($condition == 'Snow/Wind') $return = 'Schneefall/Wind';
		elseif($condition == 'Snow/Windy') $return = 'Schnee/Windig';
		elseif($condition == 'Squalls') $return = 'Böen';
		elseif($condition == 'Sunny') $return = 'Sonnig';
		elseif($condition == 'Sunny/Wind') $return = 'Sonnig/Wind';
		elseif($condition == 'Sunny/Windy') $return = 'Sonnig/Windig';
		elseif($condition == 'T-showers') $return = 'Gewitterschauer';
		elseif($condition == 'Thunder in the Vicinity') $return = 'Gewitter in der Umgebung';
		elseif($condition == 'Thunder') $return = 'Gewitter';
		elseif($condition == 'Thundershowers Early') $return = 'früh Gewitterschauer';
		elseif($condition == 'Thundershowers') $return = 'Gewitterschauer';
		elseif($condition == 'Thunderstorm') $return = 'Gewitter';
		elseif($condition == 'Thunderstorm/Windy') $return = 'Gewitter/Windig';
		elseif($condition == 'Thunderstorms Early') $return = 'früh Gewitter';
		elseif($condition == 'Thunderstorms Late') $return = 'später Gewitter';
		elseif($condition == 'Thunderstorms') $return = 'Gewitter';
		elseif($condition == 'Unknown Precipitation') $return = 'Niederschlag';
		elseif($condition == 'Unknown') $return = 'unbekannt';
		elseif($condition == 'Wintry Mix') $return = 'Winterlicher Mix';
		else $return = $condition;
		return $return;
	}	/** @}*/
?>