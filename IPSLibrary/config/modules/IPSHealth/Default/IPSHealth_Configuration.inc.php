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
	*
	*
	* Beispiel:
	* @code
	* @endcode
	*
	* @return string Liefert Array mit Bewässerungs Kreisen
	*/

	function get_HealthConfiguration() {
		return array(

         c_HealthCircle.'1'   => array(
		      	c_HealthTimeout  	=>    60,
					c_HealthVariables  =>		array(
						37772  	  ,
					),
			),

         c_HealthCircle.'2'   => array(
					c_HealthTimeout  	=>    300,
					c_HealthVariables  =>	array(
						37772  ,
						15530 ,
						40599 ,
						15256 ,
						55970 ,
					),
			),

         c_HealthCircle.'3'   => array(
					c_HealthTimeout  	=>    600,
					c_HealthVariables  =>		array(
						37772  	  ,
					),
			),
		);
	}


	/** @}*/
?>