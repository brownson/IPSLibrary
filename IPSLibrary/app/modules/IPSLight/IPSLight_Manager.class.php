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

	/**@addtogroup ipslight
	 * @{
	 *
	 * @file          IPSLight_Manager.class.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 26.07.2012<br/>
	 *
	 * IPSLight Licht Management
	 */

	/**
	 * @class IPSLight_Manager
	 *
	 * Definiert ein IPSLight_Manager Objekt
	 *
	 * @author Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 26.07.2012<br/>
	 */
	class IPSLight_Manager {

		/**
		 * @private
		 * ID Kategorie mit Schalter und Dimmern
		 */
		private $switchCategoryId;

		/**
		 * @private
		 * ID Kategorie mit Schalter
		 */
		private $groupCategoryId;

		/**
		 * @private
		 * ID Kategorie mit Programmen
		 */
		private $programCategoryId;

		/**
		 * @private
		 * LightSimulator 
		 */
		private $lightSimulator;

		/**
		 * @public
		 *
		 * Initialisierung des IPSLight_Manager Objektes
		 *
		 */
		public function __construct() {
			$baseId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSLight');
			$this->switchCategoryId  = IPS_GetObjectIDByIdent('Switches', $baseId);
			$this->groupCategoryId   = IPS_GetObjectIDByIdent('Groups', $baseId);
			$this->programCategoryId = IPS_GetObjectIDByIdent('Programs', $baseId);
			$this->lightSimulator    = new IPSLight_Simulator();
		}

		/**
		 * @public
		 *
		 * Liefert ID eines Schalters anhand des Namens
		 *
		 * @param string $name Name des Schalters
		 * @return int ID des Schalters 
		 */
		public function GetSwitchIdByName($name) {
			return IPS_GetVariableIDByName($name, $this->switchCategoryId);
		}

		/**
		 * @public
		 *
		 * Liefert ID einer Level Variable eines Dimmers anhand des Namens
		 *
		 * @param string $name Name des Dimmers
		 * @return int ID der Level Variable
		 */
		public function GetLevelIdByName($name) {
			return IPS_GetVariableIDByName($name.IPSLIGHT_DEVICE_LEVEL, $this->switchCategoryId);
		}

		/**
		 * @public
		 *
		 * Liefert ID einer RGB Variable anhand des Namens
		 *
		 * @param string $name Name des RGB Lichtes
		 * @return int ID der RGB Variable
		 */
		public function GetColorIdByName($name) {
			return IPS_GetVariableIDByName($name.IPSLIGHT_DEVICE_COLOR, $this->switchCategoryId);
		}

		/**
		 * @public
		 *
		 * Liefert ID eines Gruppen Schalters anhand des Namens
		 *
		 * @param string $name Name der Gruppe
		 * @return int ID der Gruppe
		 */
		public function GetGroupIdByName($name) {
			return IPS_GetVariableIDByName($name, $this->groupCategoryId);
		}

		/**
		 * @public
		 *
		 * Liefert ID eines Programm Schalters anhand des Namens
		 *
		 * @param string $name Name des Programm Schalters
		 * @return int ID des Programm Schalters
		 */
		public function GetProgramIdByName($name) {
			return IPS_GetVariableIDByName($name, $this->programCategoryId);
		}

		/**
		 * @public
		 *
		 * Liefert Wert einer Control Variable (Schalter, Dimmer, Gruppe, ...) anhand der zugehörigen ID
		 *
		 * @param string $variableId ID der Variable
		 * @return int Wert der Variable
		 */
		public function GetValue($variableId) {
			return GetValue($variableId);
		}

		/**
		 * @public
		 *
		 * Setzt den Wert einer Control Variable (Schalter, Dimmer, Gruppe, ...) anhand der zugehörigen ID
		 *
		 * @param int $variableId ID der Variable
		 * @param int $value Neuer Wert der Variable
		 */
		public function SetValue($variableId, $value) {
			$parentId = IPS_GetParent($variableId);
			switch($parentId) {
				case $this->switchCategoryId:
					$configName = $this->GetConfigNameById($variableId);
					$configLights = IPSLight_GetLightConfiguration();
					$lightType    = $configLights[$configName][IPSLIGHT_TYPE];
					if ($lightType==IPSLIGHT_TYPE_SWITCH) {
						$this->SetSwitch($variableId, $value);
					} elseif ($lightType==IPSLIGHT_TYPE_DIMMER) {
						$this->SetDimmer($variableId, $value);
					} elseif ($lightType==IPSLIGHT_TYPE_RGB) {
						$this->SetRGB($variableId, $value);
					} else {
						trigger_error('Unknown LightType '.$lightType.' for Light '.$configName);
					}
					break;
				case $this->groupCategoryId:
					$this->SetGroup($variableId, $value);
					break;
				case $this->programCategoryId:
					$this->SetProgram($variableId, $value);
					break;
				default:
					trigger_error('Unknown ControlId '.$variableId);
			}
		}

		/**
		 * @public
		 *
		 * Setzt den Wert einer Schalter Variable anhand der zugehörigen ID
		 *
		 * @param int $switchId ID der Variable
		 * @param bool $value Neuer Wert der Variable
		 */
		public function SetSwitch($switchId, $value, $syncGroups=true, $syncPrograms=true) {
			if (GetValue($switchId)==$value) {
				return;
			}
			$configName      = $this->GetConfigNameById($switchId);
			$configLights    = IPSLight_GetLightConfiguration();
			$componentParams = $configLights[$configName][IPSLIGHT_COMPONENT];
			$component       = IPSComponent::CreateObjectByParams($componentParams);

			SetValue($switchId, $value);
			IPSLogger_Inf(__file__, 'Turn Light '.$configName.' '.($value?'On':'Off'));

			if (IPSLight_BeforeSwitch($switchId, $value)) {
				$component->SetState($value);
				$this->lightSimulator->StoreStatusChange($switchId, $value);
			}
			IPSLight_AfterSwitch($switchId, $value);

			if ($syncGroups) {
				$this->SynchronizeGroupsBySwitch($switchId);
			}
			if ($syncPrograms) {
				$this->SynchronizeProgramsBySwitch ($switchId);
			}
		}

		/**
		 * @public
		 *
		 * Setzt den Wert einer Dimmer Variable anhand der zugehörigen ID
		 *
		 * @param int $variableId ID der Variable
		 * @param bool $value Neuer Wert der Variable
		 */
		public function SetDimmer($variableId, $value, $syncGroups=true, $syncPrograms=true) {
			if (GetValue($variableId)==$value) {
				return;
			}
			$configName   = $this->GetConfigNameById($variableId);
			$configLights = IPSLight_GetLightConfiguration();
			$switchId     = IPS_GetVariableIDByName($configName, $this->switchCategoryId);
			$switchValue  = GetValue($switchId);
			$levelId      = IPS_GetVariableIDByName($configName.IPSLIGHT_DEVICE_LEVEL, $this->switchCategoryId);

			$componentParams = $configLights[$configName][IPSLIGHT_COMPONENT];
			$component       = IPSComponent::CreateObjectByParams($componentParams);

			if (!$switchValue and $variableId==$levelId) {
				SetValue($switchId, true);
				$switchValue = true;
				if (GetValue($levelId) > 100) { $value = 100; }
				if (GetValue($levelId) < 0)   { $value = 0; }
			}
			SetValue($variableId, $value);
			IPSLogger_Inf(__file__, 'Turn Light '.$configName.' '.($switchValue?'On, Level='.GetValue($levelId):'Off'));

			if (IPSLight_BeforeSwitch($switchId, $switchValue)) {
				$component->SetState(GetValue($switchId), GetValue($levelId));
				$this->lightSimulator->StoreStatusChange($switchId, GetValue($switchId), GetValue($levelId));
			}
			IPSLight_AfterSwitch($switchId, $switchValue);

			if ($syncGroups) {
				$this->SynchronizeGroupsBySwitch($switchId);
			}
			if ($syncPrograms) {
				$this->SynchronizeProgramsBySwitch ($switchId);
			}
		}

		/**
		 * @public
		 *
		 * Setzt den Wert einer RGB Farb Variable anhand der zugehörigen ID
		 *
		 * @param int $variableId ID der Variable
		 * @param bool $value Neuer Wert der Variable
		 */
		public function SetRGB($variableId, $value, $syncGroups=true, $syncPrograms=true) {
			if (GetValue($variableId)==$value) {
				return;
			}
			$configName   = $this->GetConfigNameById($variableId);
			$configLights = IPSLight_GetLightConfiguration();
			$switchId     = IPS_GetVariableIDByName($configName, $this->switchCategoryId);
			$colorId      = IPS_GetVariableIDByName($configName.IPSLIGHT_DEVICE_COLOR, $this->switchCategoryId);
			$levelId      = IPS_GetVariableIDByName($configName.IPSLIGHT_DEVICE_LEVEL, $this->switchCategoryId);
			$switchValue  = GetValue($switchId);

			$componentParams = $configLights[$configName][IPSLIGHT_COMPONENT];
			$component       = IPSComponent::CreateObjectByParams($componentParams);

			SetValue($variableId, $value);
			if (!$switchValue and ($variableId==$levelId or $variableId==$colorId)) {
				SetValue($switchId, true);
				$switchValue = true;
			}
			IPSLogger_Inf(__file__, 'Turn Light '.$configName.' '.($switchValue?'On, Level='.GetValue($levelId).', Color='.GetValue($colorId):'Off'));

			if (IPSLight_BeforeSwitch($switchId, $switchValue)) {
				$component->SetState(GetValue($switchId), GetValue($colorId), GetValue($levelId));
				$this->lightSimulator->StoreStatusChange($switchId, GetValue($switchId), GetValue($levelId), GetValue($colorId));
			}
			IPSLight_AfterSwitch($switchId, $switchValue);

			if ($syncGroups) {
				$this->SynchronizeGroupsBySwitch($switchId);
			}
			if ($syncPrograms) {
				$this->SynchronizeProgramsBySwitch ($switchId);
			}
		}

		/**
		 * @public
		 *
		 * Setzt den Wert einer Gruppen Variable anhand der zugehörigen ID
		 *
		 * @param int $variableId ID der Gruppe
		 * @param bool $value Neuer Wert der Gruppe
		 */
		public function SetGroup($groupId, $value) {
			$groupConfig = IPSLight_GetGroupConfiguration();
			$groupName   = IPS_GetName($groupId);
			if ($value and !$groupConfig[$groupName][IPSLIGHT_ACTIVATABLE]) {
				IPSLogger_Trc(__file__, "Ignore ".($value?'On':'Off')." forLightGroup '$groupName' (not allowed)");
			} else {
				SetValue($groupId, $value);
				IPSLogger_Inf(__file__, "Turn LightGroup '$groupName' ".($value?'On':'Off'));
				$this->SetAllSwitchesByGroup($groupId);
			}
		}

		/**
		 * @public
		 *
		 * Setzt den Wert einer Programm Variable anhand der zugehörigen ID
		 *
		 * @param int $variableId ID der Programm Variable
		 * @param bool $value Neuer Wert der Programm Variable
		 */
		public function SetProgram($programId, $value) {
			$programName     = IPS_GetName($programId);
			$programConfig   = IPSLight_GetProgramConfiguration();
			$programKeys     = array_keys($programConfig[$programName]);
			if ($value>(count($programKeys)-1)) { 
				$value=0;
			}
			$programItemName = $programKeys[$value];

			IPSLogger_Inf(__file__, "Set Program $programName=$value ");

			// Light On
			if (array_key_exists(IPSLIGHT_PROGRAMON,  $programConfig[$programName][$programItemName])) {
				$switches = $programConfig[$programName][$programItemName][IPSLIGHT_PROGRAMON];
				$switches = explode(',',  $switches);
				foreach ($switches as $idx=>$switchName) {
					$switchId = $this->GetSwitchIdByName($switchName);
					$configLights = IPSLight_GetLightConfiguration();
					$lightType    = $configLights[$switchName][IPSLIGHT_TYPE];
					if ($lightType==IPSLIGHT_TYPE_SWITCH) {
						$this->SetSwitch($switchId, true);
					} elseif ($lightType==IPSLIGHT_TYPE_DIMMER) {
						$this->SetDimmer($switchId, true);
					} elseif ($lightType==IPSLIGHT_TYPE_RGB) {
						$this->SetRGB($switchId, true);
					} else {
						trigger_error('Unknown LightType '.$lightType.' for Light '.$configName);
					}
				}
			}
			// Light Off
			if (array_key_exists(IPSLIGHT_PROGRAMOFF,  $programConfig[$programName][$programItemName])) {
				$switches = $programConfig[$programName][$programItemName][IPSLIGHT_PROGRAMOFF];
				$switches = explode(',',  $switches);
				foreach ($switches as $idx=>$switchName) {
					$switchId = $this->GetSwitchIdByName($switchName);
					$configLights = IPSLight_GetLightConfiguration();
					$lightType    = $configLights[$switchName][IPSLIGHT_TYPE];
					if ($lightType==IPSLIGHT_TYPE_SWITCH) {
						$this->SetSwitch($switchId, false);
					} elseif ($lightType==IPSLIGHT_TYPE_DIMMER) {
						$this->SetDimmer($switchId, false);
					} elseif ($lightType==IPSLIGHT_TYPE_RGB) {
						$this->SetRGB($switchId, false);
					} else {
						trigger_error('Unknown LightType '.$lightType.' for Light '.$configName);
					}
				}
			}
			// Light Level
			if (array_key_exists(IPSLIGHT_PROGRAMLEVEL,  $programConfig[$programName][$programItemName])) {
				$switches = $programConfig[$programName][$programItemName][IPSLIGHT_PROGRAMLEVEL];
				$switches = explode(',',  $switches);
				for ($idx=0; $idx<Count($switches)-1; $idx=$idx+2) {
					$switchName  = $switches[$idx];
					$switchValue = (float)$switches[$idx+1];
					$switchId    = $this->GetSwitchIdByName($switchName);
					$this->SetDimmer($switchId, true, true, false);
					$switchId    = $this->GetSwitchIdByName($switchName.IPSLIGHT_DEVICE_LEVEL);
					$this->SetDimmer($switchId, $switchValue, true, false);
				}
			}
			SetValue($programId, $value);
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function GetConfigNameById($switchId) {
			$switchName = IPS_GetName($switchId);
			$switchName = str_replace(IPSLIGHT_DEVICE_COLOR, '', $switchName);
			$switchName = str_replace(IPSLIGHT_DEVICE_LEVEL, '', $switchName);

			return $switchName;
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function SynchronizeGroupsBySwitch ($switchId) {
			$switchName  = IPS_GetName($switchId);
			$lightConfig = IPSLight_GetLightConfiguration();
			$groups      = explode(',', $lightConfig[$switchName][IPSLIGHT_GROUPS]);
			foreach ($groups as $groupName) {
				$groupId  = IPS_GetVariableIDByName($groupName, $this->groupCategoryId);
				$this->SynchronizeGroup($groupId);
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function SynchronizeGroup ($groupId) {
			$lightConfig = IPSLight_GetLightConfiguration();
			$groupName   = IPS_GetName($groupId);
			$groupState  = false;
			foreach ($lightConfig as $switchName=>$deviceData) {
				$switchId      = IPS_GetVariableIDByName($switchName, $this->switchCategoryId);
				$switchState   = GetValue($switchId);
				$switchInGroup = array_key_exists($groupName, array_flip(explode(',', $deviceData[IPSLIGHT_GROUPS])));
				if ($switchInGroup and GetValue($switchId)) {
					$groupState = true;
					break;
				}
			}
			if (GetValue($groupId) <> $groupState) {
				IPSLogger_Trc(__file__, "Synchronize ".($switchState?'On':'Off')." to Group '$groupName' from Switch '$switchName'");
				SetValue($groupId, $groupState);
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function SynchronizeProgramsBySwitch($switchId) {
			$switchName = IPS_GetName($switchId);
			$programConfig   = IPSLight_GetProgramConfiguration();

			foreach ($programConfig as $programName=>$programData) {
				foreach ($programData as $programItemName=>$programItemData) {
					if (array_key_exists(IPSLIGHT_PROGRAMON, $programItemData)) {
						if ($this->SynchronizeProgramItemBySwitch($switchName, $programName, $programItemData[IPSLIGHT_PROGRAMON])) {
							return;
						}
					}
					if (array_key_exists(IPSLIGHT_PROGRAMOFF, $programItemData)) {
						if ($this->SynchronizeProgramItemBySwitch($switchName, $programName, $programItemData[IPSLIGHT_PROGRAMOFF])) {
							return;
						}
					}
					if (array_key_exists(IPSLIGHT_PROGRAMLEVEL, $programItemData)) {
						if ($this->SynchronizeProgramItemBySwitch($switchName, $programName, $programItemData[IPSLIGHT_PROGRAMLEVEL])) {
							return;
						}
					}
				}
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function SynchronizeProgramItemBySwitch($switchName, $programName, $property) {
			$propertyList = explode(',', $property);
			$switchList = array_flip($propertyList);
			if (array_key_exists($switchName,  $switchList)) {
				$programId   = IPS_GetVariableIDByName($programName, $this->programCategoryId);
				IPSLogger_Trc(__file__, "Reset Program '$programName' by manual Change of '$switchName'");
				SetValue($programId, 0);
				return true;
			}
			return false;
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function SetAllSwitchesByGroup ($groupId) {
			$groupName    = IPS_GetName($groupId);
			$lightConfig  = IPSLight_GetLightConfiguration();
			$groupState   = GetValue($groupId);
			foreach ($lightConfig as $switchName=>$deviceData) {
				$switchId      = IPS_GetVariableIDByName($switchName, $this->switchCategoryId);
				$switchInGroup = array_key_exists($groupName, array_flip(explode(',', $deviceData[IPSLIGHT_GROUPS])));
				if ($switchInGroup and GetValue($switchId)<>$groupState) {
					IPSLogger_Trc(__file__, "Set Light $switchName=".($groupState?'On':'Off')." for Group '$groupName'");
					$this->SetValue($switchId, $groupState);
					$this->SynchronizeGroupsBySwitch ($switchId);
				}
			}
		}

		public function SynchronizeSwitch($switchName, $deviceState) {
			IPSLogger_Trc(__file__, "Received StateChange from Light '$switchName'=$deviceState");
			$switchId    = IPS_GetVariableIDByName($switchName, $this->switchCategoryId);

			$lightConfig = IPSLight_GetLightConfiguration();
			$deviceType  = $lightConfig[$switchName][IPSLIGHT_TYPE];

			if (IPSLight_BeforeSynchronizeSwitch($switchId, $deviceState)) {
				if (GetValue($switchId) <> $deviceState) {
					IPSLogger_Inf(__file__, 'Synchronize StateChange from Light '.$switchName.', State='.($deviceState?'On':'Off'));
					SetValue($switchId, $deviceState);
					$this->lightSimulator->StoreStatusChange($switchId, $deviceState);
					$this->SynchronizeGroupsBySwitch($switchId);
					$this->SynchronizeProgramsBySwitch($switchId);
				}
			}
			IPSLight_AfterSynchronizeSwitch($switchId, $deviceState);
		}


		public function SynchronizeDimmer($switchName, $deviceState, $deviceLevel) {
			IPSLogger_Trc(__file__, 'Received StateChange from Light '.$switchName.', State='.$deviceState.', Level='.$deviceLevel);
			$switchId    = IPS_GetVariableIDByName($switchName, $this->switchCategoryId);
			$levelId     = IPS_GetVariableIDByName($switchName.IPSLIGHT_DEVICE_LEVEL, $this->switchCategoryId);

			$lightConfig = IPSLight_GetLightConfiguration();
			$deviceType  = $lightConfig[$switchName][IPSLIGHT_TYPE];

			if (IPSLight_BeforeSynchronizeSwitch($switchId, $deviceState)) {
				if (GetValue($switchId)<>$deviceState or GetValue($levelId)<>$deviceLevel) {
					IPSLogger_Inf(__file__, 'Synchronize StateChange from Light '.$switchName.', State='.($deviceState?'On':'Off').', Level='.$deviceLevel);
					SetValue($switchId, $deviceState);
					SetValue($levelId, $deviceLevel);
					$this->SynchronizeGroupsBySwitch($switchId);
					$this->SynchronizeProgramsBySwitch($switchId);
				}
			}
			IPSLight_AfterSynchronizeSwitch($switchId, $deviceState);
		}
		
		public function GetPowerConsumption($powerCircle) {
			$powerConsumption = 0;
			$lightConfig      = IPSLight_GetLightConfiguration();
			foreach ($lightConfig as $switchName=>$deviceData) {
				$lightType  = $lightConfig[$switchName][IPSLIGHT_TYPE];
				if (array_key_exists(IPSLIGHT_POWERCIRCLE, $deviceData) and $deviceData[IPSLIGHT_POWERCIRCLE]==$powerCircle) {
					$switchId = IPS_GetVariableIDByName($switchName, $this->switchCategoryId);
					if (GetValue($switchId)) {
						switch ($lightType) {
							case IPSLIGHT_TYPE_SWITCH:
								$powerConsumption = $powerConsumption + $deviceData[IPSLIGHT_POWERWATT];
								break;
							case IPSLIGHT_TYPE_DIMMER:
							case IPSLIGHT_TYPE_RGB:
								$levelId = IPS_GetVariableIDByName($switchName.IPSLIGHT_DEVICE_LEVEL, $this->switchCategoryId);
								$powerConsumption = $powerConsumption + $deviceData[IPSLIGHT_POWERWATT]*GetValue($levelId)/100;
								break;
							default:
								trigger_error('Unknown LightType '.$lightType.' for Light '.$configName);
						}

					}
				}
			}
			return $powerConsumption;
		}
		
	}

	/** @}*/
?>