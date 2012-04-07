<?
	/**
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

	 /**@addtogroup audiomax
	 * @{
	 *
	 *
	 * @file          AudioMax_Receive.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 */
 
 	include_once 'AudioMax.inc.php';

	$msg = $_IPS['VALUE'];

	$serverId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.AudioMax.AudioMax_Server');

	$server = new AudioMax_Server($serverId);
	$server->ReceiveData($msg);

 	/** @}*/

?>