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

	/**@defgroup ipshomematic ipshomematic
	 * @ingroup hardware
	 * @{
	 *
	 * @file          IPSHomematic.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 14.07.2012<br/>
	 *
	 */

    IPSUtils_Include ('IPSLogger.inc.php',                   'IPSLibrary::app::core::IPSLogger');
	IPSUtils_Include ('IPSHomematic_Constants.inc.php',      'IPSLibrary::app::hardware::IPSHomematic');
	IPSUtils_Include ('IPSHomematic_Configuration.inc.php',  'IPSLibrary::config::hardware::IPSHomematic');
	IPSUtils_Include ('IPSHomematic_Custom.inc.php',         'IPSLibrary::config::hardware::IPSHomematic');
	IPSUtils_Include ('IPSHomematic_Utils.inc.php',          'IPSLibrary::app::hardware::IPSHomematic');
	IPSUtils_Include ('IPSHomematic_Manager.class.php',      'IPSLibrary::app::hardware::IPSHomematic');

	/** @}*/
?>