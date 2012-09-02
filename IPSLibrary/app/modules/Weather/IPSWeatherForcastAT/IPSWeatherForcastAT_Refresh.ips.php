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

		if (IPSWEATHERFAT_WONDERGROUND_KEY<>'') {
			IPSUtils_Include ("IPSWeatherForcastAT_RefreshWonderground.inc.php",  "IPSLibrary::app::modules::Weather::IPSWeatherForcastAT");
			$refreshWonderground = IPSWeatherFAT_RefreshWonderground();
		}

		if (IPSWEATHERFAT_YAHOO_WOEID<>'' and !$refreshWonderground) {
			IPSUtils_Include ("IPSWeatherForcastAT_RefreshYahoo.inc.php",  "IPSLibrary::app::modules::Weather::IPSWeatherForcastAT");
			IPSWeatherFAT_RefreshYahoo();
		}

		if (IPSWEATHERFAT_ORF_URL<>'') {
			IPSUtils_Include ("IPSWeatherForcastAT_RefreshORF.inc.php",  "IPSLibrary::app::modules::Weather::IPSWeatherForcastAT");
			IPSWeatherFAT_RefreshORF();
		}
	}

	/** @}*/
?>