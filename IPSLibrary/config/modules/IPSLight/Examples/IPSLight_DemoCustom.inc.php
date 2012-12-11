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

	/**@addtogroup ipslight_configuration
	 * @{
	 *
	 * Es gibt derzeit 4 Callback Methoden, diese ermöglichen es vor oder nach dem Schalten/Dimmen eines Lichtes eigene Aktionen auszuführen
	 *
	 * Funktionen:
	 *  - function IPSLight_BeforeSwitch($control, $value)
	 *  - function IPSLight_AfterSwitch($control, $value)
	 *  - function IPSLight_BeforeSynchronizeSwitch ($SwitchId, $DeviceState)
	 *  - function IPSLight_AfterSynchronizeSwitch ($SwitchId, $DeviceState)
	 *
	 * @file          IPSLight_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 26.07.2012<br/>
	 *
	 * Callback Methoden für IPSLight
	 *
	 */

	/**
	 * Diese Funktion wird vor dem Schalten eines Lichtes ausgeführt.
	 *
	 * Parameters:
	 *   @param integer $lightId  ID des Beleuchtungs Switches in IPSLight
	 *   @param boolean $value Wert für Ein/Aus
	 *   @result boolean TRUE für OK, bei FALSE wurde die Ansteuerung der Beleuchtung bereits in der Callback Funktion erledigt
	 *
	 */
	function IPSLight_BeforeSwitch($lightId, $value) {
		return true;
	}

	/**
	 * Diese Funktion wird nach dem Schalten eines Lichtes ausgeführt.
	 *
	 * Parameters:
	 *   @param integer $lightId  ID des Beleuchtungs Switches in IPSLight
	 *   @param boolean $value Wert für Ein/Aus
	 *
	 */
	function IPSLight_AfterSwitch($lightId, $value) {

	}

	/**
	 * Diese Funktion wird vor dem Synchronisieren eines Licht Schaltvorganges durch ein externes System ausgeführt.
	 *
	 * Parameters:
	 *   @param integer $lightId  ID des Beleuchtungs Switches in IPSLight
	 *   @param boolean $value Wert für Ein/Aus
	 *   @result boolean TRUE für OK, bei FALSE erfolgt keine Synchronisierung
	 *
	 */
	function IPSLight_BeforeSynchronizeSwitch ($lightId, $value) {

		return true;
	}

	/**
	 * Diese Funktion wird nach dem Synchronisieren eines Licht Schaltvorganges durch ein externes System ausgeführt.
	 *
	 * Parameters:
	 *   @param integer $lightId  ID des Beleuchtungs Switches in IPSLight
	 *   @param boolean $value Wert für Ein/Aus
	 *
	 */
	function IPSLight_AfterSynchronizeSwitch ($lightId, $value) {
	}

	/** @}*/

?>