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

	/**@ingroup ipspowercontrol
	 * @{
	 *
	 * @file          IPSPowerControl_NavigateMinus.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.09.2012<br/>
	 *
	 * IPSPowerControl Script zur Navigation 
	 *
	 */

	include_once "IPSPowerControl.inc.php";

	$variableId   = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSPowerControl.Common.'.IPSPC_VAR_PERIODCOUNT);
	$value        = IPSPC_COUNT_MINUS;

	$pcManager = new IPSPowerControl_Manager();
	$pcManager->ChangeSetting($variableId, $value);

    /** @}*/
?>