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

	/**@defgroup IPSHealth_configuration IPSHealth Konfiguration
	* @ingroup IPSHealth
	* @{
	*
	* @file          IPSHealth_Configuration.inc.php
	* @author        André Czwalina
	* @version
	* Version 1.00.3, 22.04.2012<br/>
	*
	* Konfigurations File für IPSHealth
	* c_HealthTimeout = Timeout zur Meldung in Sekunden
	* c_HealthVariables = Array der zu überwachenden Variablen
	*
	*/
	IPSUtils_Include ("IPSHealth_Constants.inc.php",      "IPSLibrary::app::modules::IPSHealth");


	/**
	* Definiert die Anzahl der Meldungen, die im Applikation Logging Window angezeigt werden.
	*/
	define ("c_LogMessage_Count",			19);



	/**
	* SYSTEM - INFO
	*	Definiert die Zeit bis Meldung, des letzte Schreibzugriffs in die Datenbank Logging.
	*/
	define ("c_Warn_Schwellwert",     180);
	define ("c_Log_Content",     		'Alarm: die logging.db ist nicht mehr aktuell, die letzte Änderung vor: ');
	define ("c_Mail_Subject",     	'IPS Alert: logging.db nicht aktuell!');
	define ("c_Mail_Content",     	'IPS meldet: die IPS-DB wurde zu lange nicht aktualisiert, siehe folgende Detaildaten:');
	define ("c_Mail_Instanz",        23452 );
	define ("c_SYS_Logging",        	true );
	define ("c_SYS_HDD",             'HDD1'); //HDD auf dem IPS installiert ist


	/*
	* CCU Konfiguration
	*
	*/
	define ("c_CCU_Control",        	true);               // CCU Überwachung Ein/Aus
	define ("c_CCU_IP",     			'192.168.1.10');     // IP-Adresse der CCU
	define ("c_CCU_IPSID",           '14603');            // CCU ID Systemvariable zur Triggerung (ID aus der CCU nicht IPS)
	// Die ID ist in http://IP-CCU/config/xmlapi/sysvarlist.cgi zufinden. Variable in der CCU vorher anlegen. Typ Boolean


	/*
	* LAN Adapter Konfiguration
	*
	*/
	function get_CCU_LANAdapter() {
		return array(
				"LAN1" 	=>    'HEQ0136842',     // Lan-AdapterName =>  GeräteID
				"LAN2"   =>    'GEQ0208307',
				"LAN3"   =>    'IEQ0245026',
		);
	}

	/**
	*
	*
	* Beispiel:
	* @code
	* @endcode
	*
	* @return string Liefert Array mit Bewässerungs Kreisen
	*/
//print_r(get_HealthConfiguration());

	function get_HealthConfiguration() {
		return array(

         c_HealthCircle.'1'   => array(
               c_CircleName      =>    '60 Sekunden Überwachung',
		      	c_HealthTimeout  	=>    65,
					c_HealthVariables =>		array(
						12665  ,
					),
			),

         c_HealthCircle.'2'   => array(
               c_CircleName      =>    '360 Sekunden Überwachung',
					c_HealthTimeout  	=>    360,
					c_HealthVariables =>		array(
						37772  ,
						19278,
						15530 ,
						15256 ,
						55970 ,
					),
			),

         c_HealthCircle.'3'   => array(
               c_CircleName      =>    '25 Stunden Überwachung',
					c_HealthTimeout  	=>    25*60*60,
					c_HealthVariables =>		array(
						40599 ,
					),
			),
		);
	}


	/** @}*/
?>