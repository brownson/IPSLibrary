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

	/**@defgroup ipsmodulemanagergui IPSModuleManagerGUI
	 * @ingroup modules
	 * @{
	 *
	 * @file          IPSModuleManagerGUI.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.08.2012<br/>
	 *
	 * IPSModuleManagerGUI API
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                      "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSModuleManagerGUI_Constants.inc.php",  "IPSLibrary::app::modules::IPSModuleManagerGUI");
	IPSUtils_Include ("IPSModuleManagerGUI_Utils.inc.php",      "IPSLibrary::app::modules::IPSModuleManagerGUI");

	/**
	 * Setz eine bestimmte Seite in der IPSModuleManagerGUI
	 *
	 * @param string $action Action String
	 * @param string $module optionaler Module String
	 * @param string $info optionaler Info String
	 */
	function IPSModuleManagerGUI_SetPage($action, $module='', $info='') {
		$baseId  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSModuleManagerGUI');

		SetValue(IPS_GetObjectIDByIdent(IPSMMG_VAR_ACTION, $baseId), $action);
		SetValue(IPS_GetObjectIDByIdent(IPSMMG_VAR_MODULE, $baseId), $module);
		SetValue(IPS_GetObjectIDByIdent(IPSMMG_VAR_INFO, $baseId), $info);
	}

	/**
	 * Refresh der IPSModuleManager GUI
	 *
	 */
	function IPSModuleManagerGUI_Refresh() {
		$baseId  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSModuleManagerGUI');
		$variableIdHTML = IPS_GetObjectIDByIdent(IPSMMG_VAR_HTML, $baseId);
		SetValue($variableIdHTML, GetValue($variableIdHTML));
	}

    /** @}*/
?>