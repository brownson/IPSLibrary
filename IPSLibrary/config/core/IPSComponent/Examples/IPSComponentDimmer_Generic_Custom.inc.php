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
	 * Ger�tespezifische Befehle zur Steuerung generischer Lampen vom Typ Dimmer 
	 *
	 */



	/**
	 *  @brief Setzt den Status eines generischen Dimmers
	 *  
	 *  @param [in] $device_name Ger�tename (wie z.B. in der IPSLight_Config definiert)
	 *  @param [in] $power Zustand des Dimmers (true/false)
	 *  @param [in] $level Level des Dimmers
	 *  @return Ergebniss des Schaltvorgangs
	 *  
	 *  @details �ber den Parameter device_name wird der Funktion mitgeteilt, welche Lampe gesteuert werden soll. 
	 *  Die Funktion muss neben dem Dimmen auch das Auschhalten der Lampe �bernehmen.
	 */
	function IPSComponentDimmer_Generic_SetState($device_name, $power, $level) {
	
	switch ($device_name) {
	
		case 'Kueche_Powermate':
		
			if (!$power) {
				  IPS_RunScript( 52087  /*[Wohnung\K�che\ZWave / Silex USB / Griffin\Switch off]*/ );
				} else {
					
				if (GetValueBoolean(14224 /*[Wohnung\K�che\ZWave / Silex USB / Griffin\Port 1]*/) == false) {
					IPS_RunScript( 24628  /*[Wohnung\K�che\ZWave / Silex USB / Griffin\Switch On]*/ );
					IPS_SetScriptTimer ( 43454  /*[Applikationen (ausgeblendet)\PolluxMM\Powermate K�che initialisieren]*/ , 30 );					}
					IPS_RunScriptEx(12573 /*[Applikationen (ausgeblendet)\PolluxMM\PolluxMM\Execute Script per SSH]*/, Array("Application" => "Powermate_Kueche", "Parameter" => "Dim", "Value" => $level));
				}
				
				return true;
				
				break;
			default:
				return false;
				break;
		}
	}

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		*  @param string $device_name Ger�tename (wie z.B. in der IPSLight_Config definiert)
		 * @param integer $variable ID der ausl�senden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleDimmer $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		function IPSComponentDimmer_Generic_HandleEvent($device_name, $variable, $value, IPSModuleDimmer $module){
		
			return true;
			
		}
		
	/** @}*/

?>