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

	/**@addtogroup ipsshadowing
	 * @{
	 *
	 * @file          IPSShadowing_ScenarioManager.class.php
	 *
	 * Verwaltung von Scenarien zur Beschattungssteuerung
	 */

   /**
    * @class IPSShadowing_ScenarioManager
    *
    * Definiert ein IPSShadowing_ScenarioManager Objekt
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 06.04.2012<br/>
    */
	class IPSShadowing_ScenarioManager {

		/**
		 * @private
		 * ID des Scenarios
		 */
		private $instanceId;
		
		/**
		 * @private
		 * ID der Scenario Visualisierung
		 */
		private $displayId;

		/**
		 * @public
		 *
		 * Initialisierung des IPSShadowing_ScenarioManager Objektes
		 *
		 */
		public function __construct() {
			$this->instanceId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.ScenarioManager');
			$this->displayId  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.ScenarioManager.Display');
		}

		/**
		 * @public
		 *
		 * Initialisierung aller Assoziationen der Variablen Profile
		 *
		 */
		public function AssignAllScenarioAssociations() {
			$categoryId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Scenarios');
			$childIds = IPS_GetChildrenIDs($categoryId);
			foreach ($childIds as $scenarioId) {
				IPS_SetVariableProfileAssociation('IPSShadowing_ScenarioActivate', $scenarioId, IPS_GetName($scenarioId), '', -1);
				IPS_SetVariableProfileAssociation('IPSShadowing_ScenarioSelect',   $scenarioId, IPS_GetName($scenarioId), '', -1);
			}
		}

		/**
		 * @public
		 *
		 * Neues Scenario erzeugen
		 *
		 * @param string $name Name des Scenarios
		 * @return IPSShadowing_Scenario das erzeugte IPSShadowing_Scenario Object
		 */
		public function Create($name='Neues Szenario') {
			$scenarioId =  IPSShadowing_Scenario::Create($name);
			$this->Select($scenarioId);
			return $scenarioId;
		}

		/**
		 * @public
		 *
		 * Umbenennen eines Scenarios
		 *
		 * @param integer $scenarioId InstanceId des Scenarios
		 * @param string $newName Neuer Name des Scenarios
		 */
		public function Rename($scenarioId, $newName) {
			$scenario = new IPSShadowing_Scenario($scenarioId);
			$scenario->Rename($newName);
		}

		/**
		 * @public
		 *
		 * Scenario löschen
		 */
		public function Delete() {
			$categoryId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Scenarios');
			$childIds = IPS_GetChildrenIDs($categoryId);
			if (count($childIds)==1) {
				return;
			}
			$scenarioId = GetValue(IPS_GetObjectIDByIdent(c_Control_ScenarioSelect, $this->instanceId));
			$scenario = new IPSShadowing_Scenario($scenarioId);
			$scenario->Delete();
			$childIds = IPS_GetChildrenIDs($categoryId);
			$this->Select($childIds[0]);
			
		}

		/**
		 * @public
		 *
		 * Scenario in den EDIT Mode setzen
		 *
		 * @param integer $scenarioId InstanceId des Scenarios
		 * @param boolean $mode Set/Reset des Edit Modus
		 */
		public function SetEditMode($scenarioId, $mode) {
			$scenario = new IPSShadowing_Scenario($scenarioId);
			if ($mode) {
				$scenario->SetEditMode();
			} else {
				$scenario->ReSetEditMode();
			}
			$scenario->Display($this->displayId);
		}

		/**
		 * @public
		 *
		 * Scenario auswählen
		 *
		 * @param integer $scenarioId InstanceId des Scenarios
		 */
		public function Select($scenarioId) {
			$dipslayId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.ScenarioManager.Display');
			SetValue(IPS_GetObjectIDByIdent(c_Control_ScenarioSelect, $this->instanceId), $scenarioId);
			$scenario = new IPSShadowing_Scenario($scenarioId);
			$scenario->Display($dipslayId);
		}

		/**
		 * @public
		 *
		 * Scenario aktivieren
		 *
		 * @param integer $scenarioId InstanceId des Scenarios
		 */
		public function Activate($scenarioId) {
			SetValue(IPS_GetObjectIDByIdent(c_Control_ScenarioActivate, $this->instanceId), $scenarioId);
			$scenario = new IPSShadowing_Scenario($scenarioId);
			$scenario->Activate();
			usleep(500000);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ScenarioActivate, $this->instanceId), 0);
		}

		/**
		 * @public
		 *
		 * Scenario aktivieren
		 *
		 * @param integer $controlId ID der Variable die verändert werden soll
		 * @param string $value Wert der gesetzt werden soll
		 */
		public function SetValue($controlId, $value) {
			$scenarioId = IPS_GetParent($controlId);
			$scenario = new IPSShadowing_Scenario($scenarioId);
			$scenario->SetValue($controlId, $value);
		}

	}

	/** @}*/

?>