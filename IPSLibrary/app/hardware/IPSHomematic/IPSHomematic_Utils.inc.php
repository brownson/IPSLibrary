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

	/**@ingroup ipshomematic
	 * @{
	 *
	 * @file          IPSHomematic_Utils.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 14.07.2012<br/>
	 *
	 */

	// ====================================================================================================
	/** 
	 * @public
	 *
	 * Liefert die Instance ID zu einer Homematic Adresse
	 *
	 * @param string $sid Homematic Adresse
	 * @return int ID der Homematic Instance
	 */
	function HM_GetInstanceIDFromHMAddress($sid) {
		$ids = IPS_GetInstanceListByModuleID("{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");
		foreach($ids as $id) {
			$a = explode(":", IPS_GetProperty($id, 'Address'));
			$b = explode(":", $sid);
			if($a[0] == $b[0]) {
				return $id;
			}
		}
		return 0;
	}

	/** @}*/
?>