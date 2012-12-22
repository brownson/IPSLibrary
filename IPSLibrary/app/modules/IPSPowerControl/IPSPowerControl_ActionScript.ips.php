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
	 * @file          IPSPowerControl_ActionScript.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.09.2012<br/>
	 *
	 * IPSPowerControl ActionScript 
	 *
	 */

	include_once "IPSPowerControl.inc.php";

	// ----------------------------------------------------------------------------------------------------------------------------
	if ($_IPS['SENDER']=='WebFront') {
		$variableId   = $_IPS['VARIABLE'];
		$value        = $_IPS['VALUE'];

		$pcManager = new IPSPowerControl_Manager();
		$pcManager->ChangeSetting($variableId, $value);

	} elseif ($_IPS['SENDER']=='TimerEvent') {
		$eventId   = $_IPS['EVENT'];

		$pcManager = new IPSPowerControl_Manager();
		$pcManager->ActivateTimer($eventId);

	// ----------------------------------------------------------------------------------------------------------------------------
	} else {
		$eventId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSPowerControl.IPSPowerControl_ActionScript.CalculateWattValues');
		$pcManager = new IPSPowerControl_Manager();
		$pcManager->ActivateTimer($eventId);

		$eventId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSPowerControl.IPSPowerControl_ActionScript.CalculateKWHValues');
		$pcManager = new IPSPowerControl_Manager();
		$pcManager->ActivateTimer($eventId);
	}

    /** @}*/
?>