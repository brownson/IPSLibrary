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
	 * @file          IPSShadowing_Scenario.class.php
	 *
	 * Einzelnes Scenario der Beschattungssteuerung
	 */

   /**
    * @class IPSShadowing_Scenario
    *
    * Definiert ein IPSShadowing_Scenario Objekt
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 06.04.2012<br/>
    */
	class IPSShadowing_Scenario {

		/**
		 * @private
		 * ID des Zeit Profiles
		 */
		private $instanceId;

		/**
		 * @public
		 *
		 * Initialisierung des IPSShadowing_Scenario Objektes
		 *
		 * @param integer $instanceId InstanceId des Scenarios
		 */
		public function __construct($instanceId) {
			$this->instanceId = $instanceId;
		}

		/**
		 * @public
		 *
		 * Scenario aktivieren
		 *
		 */
		public function Activate() {
			IPSShadowing_LogActivateScenario($this->instanceId);
			$categoryIdDevices = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Devices');
			$devices           = get_ShadowingConfiguration();
			foreach ($devices as $deviceIdent=>$device) {
				$controlId = @IPS_GetObjectIDByIdent($deviceIdent, $this->instanceId);
				if ($controlId!==false) {
					$movementValue = GetValue($controlId);
					if ($movementValue<>c_MovementId_NoAction) {
						$deviceId = IPS_GetObjectIDByIdent($deviceIdent, $categoryIdDevices);
						$device = new IPSShadowing_Device($deviceId);
						$device->MoveByControl($movementValue);
					}
				}
			}
		}

		/**
		 * @public
		 *
		 * Umbenennen eines Scenarios
		 *
		 * @param string $newName Neuer Name des Scenarios
		 */
		public function Rename($newName) {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			IPS_SetVariableProfileAssociation('IPSShadowing_ScenarioSelect', $this->instanceId, $newName, '', -1);
			IPS_SetVariableProfileAssociation('IPSShadowing_ScenarioActivate', $this->instanceId, $newName, '', -1);
			IPS_SetName($this->instanceId, $newName);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ScenarioName, $this->instanceId), $newName);
		}

		/**
		 * @public
		 *
		 * Szenario verändern verändern
		 *
		 * @param integer $controlId ID der Variable die verändert werden soll
		 * @param integer $value neuer Wert
		 */
		public function SetValue($controlId, $value) {
			if (GetValue($controlId)<>$value) {
				SetValue($controlId, $value);
				IPSShadowing_LogChange($this->instanceId, $value, $controlId);
			}
		}
		
		/**
		 * @public
		 *
		 * Scenario löschen
		 *
		 */
		public function Delete() {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			IPS_SetVariableProfileAssociation('IPSShadowing_ScenarioSelect', $this->instanceId, '', '', -1);
			IPS_SetVariableProfileAssociation('IPSShadowing_ScenarioActivate', $this->instanceId, '', '', -1);
			DeleteCategory($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Scenario in den EDIT Mode setzen
		 *
		 * @param integer $defaultValue default Wert für alle Beschattungs Elemente
		 */
		public function SetEditMode($defaultValue=c_MovementId_NoAction) {
			$ScriptIdChangeSettings  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_ChangeSettings');
			IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_ScenarioName, $this->instanceId), $ScriptIdChangeSettings);
			//IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_ScenarioEdit, $this->instanceId), $ScriptIdChangeSettings);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ScenarioEdit, $this->instanceId), true);

			$devices           = get_ShadowingConfiguration();
			$position          = 100;
			foreach ($devices as $deviceIdent=>$device) {
				$position = $position + 10;
				$controlId = @IPS_GetObjectIDByIdent($deviceIdent, $this>instanceId);
				if ($controlId===false) {
					if ($device[c_Property_ShadowingType]==c_ShadowingType_Shutter) {
						if ($defaultValue==c_MovementId_Closed) $defaultValue=c_MovementId_Dimout;
						$controlId = CreateVariable ($deviceIdent, 1 /*Boolean*/, $this->instanceId, $position, 'IPSShadowing_ScenarioShutter', null, $defaultValue, 'Shutter');
					} elseif ($device[c_Property_ShadowingType]==c_ShadowingType_Jalousie) {
						if ($defaultValue==c_MovementId_Dimout) $defaultValue=c_MovementId_Close;
						$controlId = CreateVariable ($deviceIdent, 1 /*Boolean*/, $this->instanceId, $position, 'IPSShadowing_ScenarioJalousie', null, $defaultValue, 'Shutter');
					} elseif ($device[c_Property_ShadowingType]==c_ShadowingType_Marquees) {
						$controlId = CreateVariable ($deviceIdent, 1 /*Boolean*/, $this->instanceId, $position, 'IPSShadowing_ScenarioMarquees', null, c_MovementId_NoAction, 'Shutter');
					} else {
						throw new Exception ('Unknown ShadowingType='.$device[c_Property_ShadowingType]);
					}
					IPS_SetName($controlId, $device[c_Property_Name]);
				}
				IPS_SetVariableCustomAction($controlId, $ScriptIdChangeSettings);
			}

		}

		/**
		 * @public
		 *
		 * Reset des EDIT Modes
		 *
		 */
		public function ReSetEditMode() {
			IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_ScenarioName, $this->instanceId), null);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ScenarioEdit, $this->instanceId), false);

			$devices           = get_ShadowingConfiguration();
			foreach ($devices as $deviceIdent=>$device) {
				$controlId = @IPS_GetObjectIDByIdent($deviceIdent, $this->instanceId);
				if ($controlId===false) {
				} elseif (GetValue($controlId)==c_MovementId_NoAction) {
					IPS_DeleteVariable($controlId);
				} else {
					IPS_SetVariableCustomAction($controlId, null);
				}
			}
		}

		/**
		 * @public
		 *
		 * Visualisierung des Szenarios in einer übergebenen Kategorie
		 *
		 * @param integer $categoryId ID der Kategory in der die Visualisierungs Links abgelegt werden sollen
		 */
		public function Display($categoryId) {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			CreateLink('Szenario Name',  IPS_GetObjectIDByIdent(c_Control_ScenarioName, $this->instanceId), $categoryId, 10);
			CreateLink('Editier Modus',  IPS_GetObjectIDByIdent(c_Control_ScenarioEdit, $this->instanceId), $categoryId, 20);
			$instanceIdDevices = CreateDummyInstance("Beschattungs Elemente", $categoryId, 30);

			$devices = get_ShadowingConfiguration();
			$position = 100;
			foreach ($devices as $deviceIdent=>$device) {
				$position = $position+10;
				$controlId = @IPS_GetObjectIDByIdent($deviceIdent, $this->instanceId);
				if ($controlId!==false) {
					CreateLink($device[c_Property_Name], $controlId, $instanceIdDevices, $position);
				} else {
					$linkId = @IPS_GetObjectIDByName($device[c_Property_Name], $instanceIdDevices);
					if ($linkId!==false) {
						IPS_DeleteLink($linkId);
					}
				}
			}
		}
		
		/**
		 * @public
		 *
		 * Neues Scenario erzeugen
		 *
		 * @param string $name Name des Scenarios
		 * @param integer $defaultValue default Wert für alle Beschattungs Elemente
		 * @return IPSShadowing_Scenario das erzeugte IPSShadowing_Scenario Object
		*/
		public static function Create($name='Neues Szenario', $defaultValue=c_MovementId_NoAction) {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			$ScriptIdChangeSettings  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_ChangeSettings');
			$instanceId = CreateCategoryPath('Program.IPSLibrary.data.modules.IPSShadowing.Scenarios.'.$name);
			IPS_SetIdent($instanceId, $instanceId);
			$controlId  = CreateVariable (c_Control_ScenarioName, 3 /*String*/,  $instanceId, 10, '', $ScriptIdChangeSettings, $name, 'Title');
			$controlId  = CreateVariable (c_Control_ScenarioEdit, 0 /*Boolean*/, $instanceId, 20, '~Switch', $ScriptIdChangeSettings, false, 'Gear');
			$scenario = new IPSShadowing_Scenario($instanceId);
			$scenario->SetEditMode($defaultValue);
			IPS_SetVariableProfileAssociation('IPSShadowing_ScenarioSelect', $instanceId, $name, '', -1);
			IPS_SetVariableProfileAssociation('IPSShadowing_ScenarioActivate', $instanceId, $name, '', -1);
			return $instanceId;
		}
		
	}

	/** @}*/

?>