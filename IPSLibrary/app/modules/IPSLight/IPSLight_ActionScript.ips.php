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

	/**@ingroup ipslight
	 * @{
	 *
	 * @file          IPSLight_ActionScript.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 26.07.2012<br/>
	 *
	 * IPSLight ActionScript 
	 *
	 */

	include_once "IPSLight.inc.php";
	
	$variableId   = $_IPS['VARIABLE'];
	$value        = $_IPS['VALUE'];
	$categoryName = IPS_GetName(IPS_GetParent($_IPS['VARIABLE']));
	
	// ----------------------------------------------------------------------------------------------------------------------------
	if ($_IPS['SENDER']=='WebFront' || $_IPS['SENDER']=='Action') {
		switch ($categoryName) {
			case 'Switches':
				IPSLight_SetValue($variableId, $value);
				break;
			case 'Groups':
				IPSLight_SetGroup($variableId, $value);
				break;
			case 'Programs':
				IPSLight_SetProgram($variableId, $value);
				break;
			default:
				trigger_error('Unknown Category '.$categoryName);
		}

	// ----------------------------------------------------------------------------------------------------------------------------
	} else {
      echo 'Sender=>>'.$_IPS['SENDER'].'<<';
	}

    /** @}*/
?>