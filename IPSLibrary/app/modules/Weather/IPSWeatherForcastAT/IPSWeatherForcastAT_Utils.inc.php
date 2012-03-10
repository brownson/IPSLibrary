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


	/**@addtogroup ipsweatherforcastat  
	 * @{
	 *
	 * Diverse Helper Functions für IPSWeatherForcastAT
	 *
	 * @file          IPSWeatherForcastAT_Utils.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 15.02.2012<br/>
	 *
	 */

	/** 
	 * Lesen von Wetterdaten anhand des Namens
	 *
	 * @param string $name Name der Variablen
	 * @return string Wert der gelesen wurde
	 */
	function IPSWeatherFAT_GetValue($name) {
		$categoryId_Weather = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.Weather.IPSWeatherForcastAT');
		$variableId         = IPS_GetObjectIDByIdent($name, $categoryId_Weather);
		$value              = GetValue($variableId);
		
		return $value;
	}
	 
	/** 
	 * Schreiben von Wetterdaten anhand des Namens
	 *
	 * @param string $name Name der Variablen
	 * @param string $value Wert der geschrieben werden soll
	 */
	function IPSWeatherFAT_SetValue($name, $value) {
		$categoryId_Weather = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.Weather.IPSWeatherForcastAT');
		$variableId         = IPS_GetObjectIDByIdent($name, $categoryId_Weather);
		SetValue($variableId, $value);
	}
	 
	 
	/** @}*/
?>