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

	/**@addtogroup IPSHealth
	 * @{
	 *
	 * @file          IPSHealth_ChangeSettings.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.1, 22.04.2012<br/>
	 *
	 *
	 * Script wird für das WebFront um Änderungen an den Variablen vorzunehmen
	 *
	 */

	include_once "IPSHealth.inc.php";
	$AppPath        = "Program.IPSLibrary.app.modules.IPSHealth";
	$IdApp     = get_ObjectIDByPath($AppPath);

	if ($_IPS['SENDER']=='StatusEvent') {
		$instanceId   	= $_IPS['INSTANCE'];
		$status 			= $_IPS['STATUSTEXT'];
		$name          = IPS_GetName($instanceId);

		CheckIOInterfaces($instanceId, $name, $status);


	}

	/** @}*/
?>