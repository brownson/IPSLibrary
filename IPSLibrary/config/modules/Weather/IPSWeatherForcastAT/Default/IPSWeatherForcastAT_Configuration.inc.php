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

	/**@defgroup ipsweatherforcastat_configuration IPSWeatherForcastAT Konfiguration
	 * @ingroup ipsweatherforcastat
	 * @{
	 *
	 * Konfigurations File für IPSWeatherForcastAT
	 *
	 * @file          IPSWeatherForcastAT_Configuration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 15.02.2012<br/>
	 *
	 */

	/**
	 * IP Adresse die verwendet wird um festzustellen ob eine Internet Verbindung vorhanden ist.
	 */
	define ("IPSWEATHERFAT_EXTERNAL_IP",            '195.34.133.10');

	/**
	 * Anzeige für Wohnort im WebFront und Mobile GUI
	 */
	define ("IPSWEATHERFAT_DISPLAY",                'Wien');

	/**
	 * Länderwahl für Google Weather API
	 */
	define ("IPSWEATHERFAT_GOOGLE_COUNTRY",         'Austria');

	/**
	 * Wohnort für Google Weather API
	 */
	define ("IPSWEATHERFAT_GOOGLE_PLACE",           'Wien');

	/**
	 * Sprache für Google Weather API
	 */
	define ("IPSWEATHERFAT_GOOGLE_LANG",            'de');

	/**
	 * URL für ORF Wetter
	 */
	define ("IPSWEATHERFAT_ORF_URL",                "http://wetter.orf.at/wien/prognose");

	/**
	 * Anzahl der Detailanzeigen (Vorschau für 1-3 Tage)
	 */
	define ("IPSWEATHERFAT_COUNT_DETAILS",           2);

	/** @}*/
?>