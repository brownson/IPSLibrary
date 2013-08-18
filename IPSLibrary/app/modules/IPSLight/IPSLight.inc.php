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

	/**@defgroup ipslight IPSLight
	 * @ingroup modules
	 * @{
	 *
	 * @file          IPSLight.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 26.07.2012<br/>
	 *
	 * IPSLight API
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                  "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSComponent.class.php",             "IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSLight_Constants.inc.php",         "IPSLibrary::app::modules::IPSLight");
	IPSUtils_Include ("IPSLight_Configuration.inc.php",     "IPSLibrary::config::modules::IPSLight");
	IPSUtils_Include ("IPSLight_Custom.inc.php",            "IPSLibrary::config::modules::IPSLight");
	IPSUtils_Include ("IPSLight_Simulator.class.php",       "IPSLibrary::app::modules::IPSLight");
	IPSUtils_Include ("IPSLight_Manager.class.php",         "IPSLibrary::app::modules::IPSLight");

	/**
	 * Setzt den Wert einer Variable (Schalter, Dimmer, Gruppe, ...) anhand der zugehörigen ID
	 *
	 * @param int $variableId ID der Variable
	 * @param variant $value Neuer Wert der Variable
	 */
	function IPSLight_SetValue($variableId, $value) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetValue($variableId, $value);
	}

	/**
	 * Setzt den Wert eines Schalters anhand der zugehörigen ID
	 *
	 * @param int $switchId ID der Variable
	 * @param bool $value Neuer Wert der Variable
	 */
	function IPSLight_SetSwitch($switchId, $value) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetValue($switchId, $value);
	}

	/**
	 * "Toggle" eines Schalters anhand der zugehörigen ID
	 *
	 * @param int $switchId ID der Variable
	 */
	function IPSLight_ToggleSwitch($switchId) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetValue($switchId, !$lightManager->GetValue($switchId));
	}

	/**
	 * Setzt den Wert eines Dimmers anhand der zugehörigen Level ID
	 *
	 * @param int $levelId ID der Variable
	 * @param int $value Neuer Wert der Variable
	 */
	function IPSLight_SetDimmerAbs($levelId, $value) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetDimmer($levelId, $value);
	}

	/**
	 * Verändert den Wert eines Dimmers anhand der zugehörigen Level ID um einen bestimmten Delta Wert
	 *
	 * @param int $levelId ID der Variable
	 * @param int $value Delta Wert um den der Dimmer Wert erhöht bzw. erniedrigt werden soll
	 */
	function IPSLight_SetDimmerRel($levelId, $value) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetDimmer($levelId, $lightManager->GetValue($levelId) + $value);
	}

	/**
	 * Setzt den Wert eines Gruppen Schalters anhand der zugehörigen ID
	 *
	 * @param int $groupId ID des Gruppen Schalters
	 * @param bool $value Neuer Wert der Gruppe
	 */
	function IPSLight_SetGroup($groupId, $value) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetGroup($groupId, $value);
	}

	/**
	 * "Toogle" Gruppen Schalter anhand der zugehörigen ID
	 *
	 * @param int $groupId ID des Gruppen Schalters
	 */
	function IPSLight_ToggleGroup($groupId) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetGroup($groupId, !$lightManager->GetValue(groupId));
	}

	/**
	 * Setzt den Wert eines Programm Schalters anhand der zugehörigen ID
	 *
	 * @param int $programId ID des Programm Schalters
	 * @param bool $value Neuer Wert des Programm Schalters
	 */
	function IPSLight_SetProgram($programId, $value) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetProgram($programId, $value);
	}

	/**
	 * Setzt des nächtsten Programms anhand der zugehörigen ID
	 *
	 * @param int $programId ID des Programm Schalters
	 */
	function IPSLight_SetProgramNext($programId) {
		$lightManager = new IPSLight_Manager();
		$lightManager->SetProgram($programId, $lightManager->GetValue($programId) + 1);
	}


	/**
	 * Setzt den Wert eines Schalters anhand des zugehörigen Namens
	 *
	 * @param string $lightName Name des Schalters
	 * @param bool $value Neuer Wert des Schalters
	 */
	function IPSLight_SetSwitchByName($lightName, $value) {
		$lightManager = new IPSLight_Manager();
		$switchId = $lightManager->GetSwitchIdByName($lightName);
		$lightManager->SetValue($switchId, $value);
	}

	/**
	 * "Toogle" Schalter anhand des zugehörigen Namens
	 *
	 * @param string $lightName Name des Schalters
	 */
	function IPSLight_ToggleSwitchByName($lightName) {
		$lightManager = new IPSLight_Manager();
		$switchId = $lightManager->GetSwitchIdByName($lightName);
		$lightManager->SetValue($switchId, !$lightManager->GetValue($switchId));
	}

	/**
	 * Setzt den Wert eines Dimmers anhand des zugehörigen Namens
	 *
	 * @param string $lightName Name des Dimmers
	 * @param int $value Neuer Wert des Dimmers
	 */
	function IPSLight_DimAbsoluteByName($lightName, $value) {
		$lightManager = new IPSLight_Manager();
		$levelId = $lightManager->GetLevelIdByName($lightName);
		$lightManager->SetDimmer($levelId, $value);
	}

	/**
	 * Verändert den Wert eines Dimmers anhand des zugehörigen Namens um einen übergebenen Delta Wert
	 *
	 * @param string $lightName Name des Dimmers
	 * @param int $value Delta Wert (positiv oder negativ)
	 */
	function IPSLight_DimRelativByName($lightName, $value) {
		$lightManager = new IPSLight_Manager();
		$levelId = $lightManager->GetLevelIdByName($lightName);
		$lightManager->SetDimmer($levelId, $lightManager->GetValue($levelId) + $value);
	}

	/**
	 * Setzt den Wert einer Gruppe anhand des zugehörigen Namens
	 *
	 * @param string $groupName Name der Gruppe
	 * @param bool $value Neuer Wert der Gruppe
	 */
	function IPSLight_SetGroupByName($groupName, $value) {
		$lightManager = new IPSLight_Manager();
		$groupId = $lightManager->GetGroupIdByName($groupName);
		$lightManager->SetGroup($groupId, $value);
	}

	/**
	 * "Toogle" Wert einer Gruppe anhand des zugehörigen Namens
	 *
	 * @param string $groupName Name der Gruppe
	 */
	function IPSLight_ToggleGroupByName($groupName) {
		$lightManager = new IPSLight_Manager();
		$groupId = $lightManager->GetGroupIdByName($groupName);
		$lightManager->SetGroup($groupId, !$lightManager->GetValue($groupId));
	}

	/**
	 * Setzt den Wert eines Programms anhand des zugehörigen Namens
	 *
	 * @param string $programName Name des Programms
	 * @param bool $value Neuer Wert des Programms
	 */
	function IPSLight_SetProgramName($programName, $value) {
		$lightManager = new IPSLight_Manager();
		$programId = $lightManager->GetProgramIdByName($programName);
		$lightManager->SetProgram($programId, $value);
	}

	/**
	 * Setzt das nächste Programm eines Programwahlschalters anhand des zugehörigen Namens
	 *
	 * @param string $programName Name des Programms
	 */
	function IPSLight_SetProgramNextByName($programName) {
		$lightManager = new IPSLight_Manager();
		$programId = $lightManager->GetProgramIdByName($programName);
		$lightManager->SetProgram($programId, $lightManager->GetValue($programId) + 1);
	}

	/**
	 * Aktiviert oder deaktiviert den Simulations Mode
	 *
	 * @param boolean $value neuer Status für Simulations Mode (true=ein, false=aus)
	 */
	function IPSLight_SetSimulationState($value) {
		$lightSimulator = new IPSLight_Simulator();
		$lightSimulator->SetState($value);
	}

	/**
	 * Setzt den Simulations Modus
	 *
	 * @param integer $value Modus für Simulation (0=eingestellte Tage zurück verwenden, 1=Datum 2000-01-01, 2=Datum 2001-01-02)
	 */
	function IPSLight_SetSimulationMode($value) {
		$lightSimulator = new IPSLight_Simulator();
		$lightSimulator->SetMode($value);
	}

	/**
	 * Setzt den Offset von Tagen, der für die Simulation verwendet werden soll. 
	 *
	 * @param integer $value Tage zurück (zB 7 --> Simulation der Beleuchtung wie vor 7 Tagen)
	 */
	function IPSLight_SetSimulationDays($value) {
		$lightSimulator = new IPSLight_Simulator();
		$lightSimulator->SetDays($value);
	}
    /** @}*/
?>