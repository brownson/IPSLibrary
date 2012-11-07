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

	/**@ingroup ipscam
	 * @{
	 *
	 * @file          IPSCam_NavDayNext.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.08.2012<br/>
	 *
	 * IPSCam Script zur History Navigation
	 *
	 */

	include_once "IPSCam.inc.php";

	$variableId   = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSCam.Common.'.IPSCAM_VAR_NAVDAYS);
	$value        = IPSCAM_DAY_FORWARD;

	$camManager = new IPSCam_Manager();
	$camManager->ChangeSetting($variableId, $value);


    /** @}*/
?>