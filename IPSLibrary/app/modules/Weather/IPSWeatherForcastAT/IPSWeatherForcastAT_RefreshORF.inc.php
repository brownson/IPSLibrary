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
	 * Dieses Script aktualisiert die Wetterdaten in IPS mit ORF
	 *
	 * @file          IPSWeatherForcastAT_RefreshORF.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 15.02.2012<br/>
	 *
	 */
 
	function IPSWeatherFAT_RefreshORF() {
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