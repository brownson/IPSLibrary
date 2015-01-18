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
	 * @file          IPSModuleManagerGUI_SearchUpdates.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.10.2012<br/>
	 *
	 * Sucht nach Module Updates der IPSLibrary
	 *
	 */

	include_once "IPSModuleManagerGUI.inc.php";

	IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");

	$moduleManager = new IPSModuleManager('', '', sys_get_temp_dir().'/', true);
	$versionHandler = $moduleManager->VersionHandler();
	$versionHandler->BuildKnownModules();

	IPSModuleManagerGUI_SetPage(IPSMMG_ACTION_OVERVIEW);
	
    /** @}*/
?>