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
	 * @file          IPSCam_NavigateNext4.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 20.01.2015<br/>
	 *
	 * Navigiert für die Kamera 4 in der History ein Bild vorwärts
	 *
	 */

	include_once "IPSCam.inc.php";

	$camManager = new IPSCam_Manager();
	$camManager->NavigatePictures(IPSCAM_NAV_FORWARD, 1, 3 /*Index of Camera in Config*/);

    /** @}*/
?>