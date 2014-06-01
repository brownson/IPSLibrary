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

	/**@addtogroup ipscomponent_configuration
	 * @{
	 *
	 * 
	 *
	 *
	 * @file          IPSComponentDimmer_Generic.inc.php
	 * @author        Joerg Kling
	 * @version
	 *   Version 2.50.1, 01.06.2014<br/>
	 *
	 * Gerätespezifische Befehle zur Steuerung generischer Lampen vom Typ Dimmer 
	 *
	 */



	/**
	 *  @brief Setzt den Status eines generischen Dimmers
	 *  
	 *  @param [in] $device_name Gerätename (wie z.B. in der IPSLight_Config definiert)
	 *  @param [in] $power Zustand des Dimmers (true/false)
	 *  @param [in] $level Level des Dimmers
	 *  @return Ergebniss des Schaltvorgangs
	 *  
	 *  @details Über den Parameter device_name wird der Funktion mitgeteilt, welche Lampe gesteuert werden soll. 
	 *  Die Funktion muss neben dem Dimmen auch das Auschhalten der Lampe übernehmen.
	 */
	function IPSComponentDimmer_Generic_SetState($device_name, $power, $level) {
	
	return true;
	
	}

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		*  @param string $device_name Gerätename (wie z.B. in der IPSLight_Config definiert)
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleDimmer $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		function IPSComponentDimmer_Generic_HandleEvent($device_name, $variable, $value, IPSModuleDimmer $module){
		
			return true;
			
		}
		
	/** @}*/

?>