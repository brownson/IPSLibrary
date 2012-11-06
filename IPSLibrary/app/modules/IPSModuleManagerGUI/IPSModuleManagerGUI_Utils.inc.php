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

	/**@ingroup ipsmodulemanagergui
	 * @{
	 *
	 * @file          IPSModuleManagerGUI_Utils.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.10.2012<br/>
	 *
	 * Utiltity Functionen von IPSModuleManagerGUI
	 *
	 */


	function IPSModuleManagerGUI_GetLock($action='', $refresh=false) {
		$result = IPS_SemaphoreEnter('IPSModuleManagerGUI', 10);
		if (!$result and $action<>'') {
			IPSLogger_Wrn(__file__, 'IPSModuleManager Action "'.$action.'"  ignored - other Action already in progess!!!');
		}
		if ($refresh) {
			IPSModuleManagerGUI_Refresh();
		}
		return $result;
	}

	function IPSModuleManagerGUI_ReleaseLock() {
		$result = IPS_SemaphoreLeave('IPSModuleManagerGUI');

		return $result;
	}

    /** @}*/
?>