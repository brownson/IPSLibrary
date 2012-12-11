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

	/**@ingroup ipshomematic_configuration
	 * @{
	 *
	 * @file          IPSShadowing_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 19.03.2012<br/>
	 *
	 * File mit Customer spezifischen Funktionen f�r IPSHomematic
	 *
	 */

	function IPSHomematic_OnActivateSmokeDetector($variableId) {
		$detectorName = IPS_GetName(IPS_GetParent($variableId));
		IPSLogger_Wrn(__file__, 'Alarm by SmokeDetector '.$detectorName);
	}

	/** @}*/
?>