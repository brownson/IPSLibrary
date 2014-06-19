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
	 * @file          IPSShadowing_ProfileTemp.class.php
	 *
	 * Temperatur Profil Verwaltung
	 */

   /**
    * @class IPSShadowing_ProfileTemp
    *
    * Definiert ein IPSShadowing_ProfileTemp Objekt
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 01.04.2012<br/>
    */
	class IPSShadowing_ProfileTemp {

		private $instanceId;
		private $profileWasActive;
		private $activationByBrigthness;
		private $brightnessLevelHigh;
		private $brightnessLevelLow;
		private $brightnessValue;
		private $tempOutdoor;
		private $tempIndoor;
		private $tempLevelOutShadow;
		private $tempLevelOutClose;
		private $tempLevelOutOpen;
		private $tempLevelInShadow;
		private $tempLevelInClose;
		private $tempLevelInOpen;

		/**
		 * @public
		 *
		 * Initialisierung des IPSShadowing_ProfileTime Objektes
		 *
		 * @param integer $instanceId InstanceId Profiles
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			$this->Init();
		}

		/**
		 * @private
		 *
		 * Initialisierung der internen Variablen
		 *
		 */
		private function Init() {
			$this->profileWasActive       = strpos(GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId)), 'Profil aktiv') === 0;
			$this->brightnessLevelHigh    = GetValue(IPS_GetObjectIDByIdent(c_Control_BrightnessHigh, $this->instanceId));
			$this->brightnessLevelLow     = GetValue(IPS_GetObjectIDByIdent(c_Control_BrightnessLow, $this->instanceId));
			$this->brightnessValue        = null;
			$this->tempLevelOutShadow     = GetValue(IPS_GetObjectIDByIdent(c_Control_TempLevelOutShadow, $this->instanceId));
			$this->tempLevelOutClose      = GetValue(IPS_GetObjectIDByIdent(c_Control_TempLevelOutClose, $this->instanceId));
			$this->tempLevelOutOpen       = GetValue(IPS_GetObjectIDByIdent(c_Control_TempLevelOutOpen, $this->instanceId));
			$this->tempLevelInShadow      = GetValue(IPS_GetObjectIDByIdent(c_Control_TempLevelInShadow, $this->instanceId));
			$this->tempLevelInClose       = GetValue(IPS_GetObjectIDByIdent(c_Control_TempLevelInClose, $this->instanceId));
			$this->tempLevelInOpen        = GetValue(IPS_GetObjectIDByIdent(c_Control_TempLevelInOpen, $this->instanceId));
			$this->tempIndoor             = null;
			$this->tempOutdoor            = null;
			$this->activationByBrigthness = true;
			if (IPSSHADOWING_TEMPSENSORINDOOR <> '') {
				$this->tempIndoor = round(GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_TEMPSENSORINDOOR)),1);
			}
			if (IPSSHADOWING_TEMPSENSOROUTDOOR <> '') {
				$this->tempOutdoor = round(GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_TEMPSENSOROUTDOOR)),1);
			}
			if (IPSSHADOWING_BRIGHTNESSSENSOR <> '') {
				$this->brightnessValue  = round(GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_BRIGHTNESSSENSOR)),1);
				if ($this->profileWasActive) {
					$this->activationByBrigthness = ($this->brightnessValue >= $this->brightnessLevelLow);
				} else {
					$this->activationByBrigthness = ($this->brightnessValue >= $this->brightnessLevelHigh);
				}
			}
		}
		
		public function UpdateProfileInfo() {
			$tempIndoor       = (IPSSHADOWING_TEMPSENSORINDOOR<>'' ? $this->tempIndoor.'°C'  :'"nicht vorhanden"');
			$tempOutdoor      = (IPSSHADOWING_TEMPSENSOROUTDOOR<>''? $this->tempOutdoor.'°C' :'"nicht vorhanden"');
			$brightness       = (IPSSHADOWING_BRIGHTNESSSENSOR<>'' ? $this->brightnessValue.' Lux':'"nicht vorhanden"');
			$activationByTemp = $this->CloseByTemp('');
			$info             = ''.($activationByTemp?'Profil aktiv':'Profil inaktiv').' (Innen='.$tempIndoor.', Aussen='.$tempOutdoor.', Helligkeit='.$brightness.')';
			if (GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId)) <> $info) {
				SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId), $info);
				IPS_SetVariableProfileAssociation('IPSShadowing_ProfileTemp', 
												  $this->instanceId,  
												  GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileName, $this->instanceId)), 
												  '', 
												  ($activationByTemp?c_Color_ProfileActive:-1));
			}
		}

		public function GetProfileInfo($tempIndoorPath) {
			$info = '';
			if ($tempIndoorPath <> '') {
				$info .= ' Innen='.round(GetValue(IPSUtil_ObjectIDByPath($tempIndoorPath)),1).'°C';
			} elseif (IPSSHADOWING_TEMPSENSORINDOOR <> '') {
				$info .= ' Innen='.$this->tempIndoor.'°C';
			}
			if (IPSSHADOWING_TEMPSENSOROUTDOOR <> '') {
				if ($info<>'') { $info.=', ';}
				$info .= ' Aussen='.$this->tempOutdoor.'°C';
			}
			return $info;
		}
		
		private function GetActivationByIndoorTemp($tempIndoorPath, $tempLevel, $reverse=false) {
			$activationByTemp = true;
			if ($tempLevel<>c_TempLevel_Ignore) {
				if (IPSSHADOWING_TEMPSENSORINDOOR<>'' or $tempIndoorPath<>'') {
					$tempIndoor       = $this->tempIndoor;
					if ($tempIndoorPath <> '') {
						$tempIndoor = round(GetValue(IPSUtil_ObjectIDByPath($tempIndoorPath)),1);
					}
					if ($reverse) {
						$activationByTemp = ($tempIndoor <= $tempLevel);
					} else {
						$activationByTemp = ($tempIndoor >= $tempLevel);
					}
				}
			}
			return $activationByTemp;
		}
		
		private function GetActivationByOutdoorTemp($tempLevel, $reverse=false) {
			$activationByTemp = true;
			if ($tempLevel<>c_TempLevel_Ignore) {
				if (IPSSHADOWING_TEMPSENSOROUTDOOR) {
					if ($reverse) {
						$activationByTemp = ($this->tempOutdoor <= $tempLevel);
					} else {
						$activationByTemp = ($this->tempOutdoor >= $tempLevel);
					}
				}
			}
			return $activationByTemp;
		}
		
		public function ShadowingByTemp($tempIndoorPath) {
			if ($this->tempLevelOutShadow==c_TempLevel_Ignore and $this->tempLevelInShadow==c_TempLevel_Ignore) {
				return false;
			} else {
				return ($this->activationByBrigthness and
				        $this->GetActivationByOutdoorTemp($this->tempLevelOutShadow) and
				        $this->GetActivationByIndoorTemp($tempIndoorPath, $this->tempLevelInShadow));
			}
		}

		public function CloseByTemp($tempIndoorPath) {
			return ($this->activationByBrigthness and 
			        $this->GetActivationByOutdoorTemp($this->tempLevelOutClose) and
			        $this->GetActivationByIndoorTemp($tempIndoorPath, $this->tempLevelInClose));
		}

		public function OpenByTemp($tempIndoorPath) {
			return ($this->GetActivationByOutdoorTemp($this->tempLevelOutOpen, true) and 
			        $this->GetActivationByIndoorTemp($tempIndoorPath, $this->tempLevelInOpen, true));
		}

		/**
		 * @public
		 *
		 * Neues Profile generieren
		 *
		 * @param string $profileName Name des Profiles
		 * @param integer $tempLevelOutShadow Temperatur Grenze Aussen für Beschattung
		 * @param integer $tempLevelInShadow Temperatur Grenze Innen für Beschattung
		 * @param integer $tempLevelOutClose Temperatur Grenze Aussen für Abdunkelung
		 * @param integer $tempLevelInClose Temperatur Grenze Innen für Abdunkelung
		 * @param integer $tempLevelOutOpen Temperatur Grenze Aussen für Öffnen
		 * @param integer $tempLevelInOpen Temperatur Grenze Innen für Öffnen
		 * @param integer $brightness Helligkeit
		 */
		public static function Create($profileName, $tempLevelOutShadow=c_TempLevel_Ignore, $tempLevelOutClose=c_TempLevel_Ignore, 
		                              $tempLevelOutOpen=c_TempLevel_Ignore, $tempLevelInShadow=c_TempLevel_Ignore, $tempLevelInClose=c_TempLevel_Ignore, 
		                              $tempLevelInOpen=c_TempLevel_Ignore, $brightnessLow=0, $brightnessHigh=0) {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			
			$ScriptIdChangeSettings  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_ChangeSettings');
			$categoryIdprofiles      = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.Temp');
			$profileIdx              = count(IPS_GetChildrenIds($categoryIdprofiles)) + 10;
			$profileId               = CreateCategory ($profileName, $categoryIdprofiles, $profileIdx);
			IPS_SetIdent($profileId, (string)$profileId);
			CreateVariable(c_Control_ProfileName,        3 /*String*/,   $profileId, 0,  '~String',                         $ScriptIdChangeSettings, $profileName,        'Title');
			CreateVariable(c_Control_TempLevelOutShadow, 1 /*Integer*/,  $profileId, 10, 'IPSShadowing_TempLevelOutShadow', $ScriptIdChangeSettings, $tempLevelOutShadow, 'Temperature');
			CreateVariable(c_Control_TempLevelInShadow,  1 /*Integer*/,  $profileId, 20, 'IPSShadowing_TempLevelInShadow',  $ScriptIdChangeSettings, $tempLevelInShadow,  'Temperature');
			CreateVariable(c_Control_TempLevelOutClose,  1 /*Integer*/,  $profileId, 30, 'IPSShadowing_TempLevelOutClose',  $ScriptIdChangeSettings, $tempLevelOutClose,  'Temperature');
			CreateVariable(c_Control_TempLevelInClose,   1 /*Integer*/,  $profileId, 40, 'IPSShadowing_TempLevelInClose',   $ScriptIdChangeSettings, $tempLevelInClose,   'Temperature');
			CreateVariable(c_Control_TempLevelOutOpen,   1 /*Integer*/,  $profileId, 50, 'IPSShadowing_TempLevelOutOpen',   $ScriptIdChangeSettings, $tempLevelOutOpen,   'Temperature');
			CreateVariable(c_Control_TempLevelInOpen,    1 /*Integer*/,  $profileId, 60, 'IPSShadowing_TempLevelInOpen',    $ScriptIdChangeSettings, $tempLevelInOpen,    'Temperature');
			CreateVariable(c_Control_BrightnessLow,      1 /*Integer*/,  $profileId, 70, 'IPSShadowing_Brightness',         $ScriptIdChangeSettings, $brightnessLow,      'Sun');
			CreateVariable(c_Control_BrightnessHigh,     1 /*Integer*/,  $profileId, 75, 'IPSShadowing_Brightness',         $ScriptIdChangeSettings, $brightnessHigh,     'Sun');
			CreateVariable(c_Control_ProfileInfo,        3 /*String*/,   $profileId, 80, '~String',                         null,                    '',                  'Information');

			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileTemp', $profileId, $profileName, "", -1);
			
			return $profileId;
		}

		/**
		 * @public
		 *
		 * Visualisierung des Profiles in einer übergebenen Kategorie
		 *
		 * @param integer $categoryId ID der Kategory in der die Visualisierungs Links abgelegt werden sollen
		 */
		public function Display($categoryId) {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			CreateLink('Profil Name',  IPS_GetObjectIDByIdent(c_Control_ProfileName, $this->instanceId), $categoryId, 10);
			$instanceId = CreateDummyInstance("Temperatur Grenzen", $categoryId, 20);
			CreateLink('Beschattung Aussen',  IPS_GetObjectIDByIdent(c_Control_TempLevelOutShadow,  $this->instanceId), $instanceId, 10);
			CreateLink('Beschattung Innen',   IPS_GetObjectIDByIdent(c_Control_TempLevelInShadow,   $this->instanceId), $instanceId, 20);
			CreateLink('Schliessen Aussen',   IPS_GetObjectIDByIdent(c_Control_TempLevelOutClose,   $this->instanceId), $instanceId, 30);
			CreateLink('Schliessen Innen',    IPS_GetObjectIDByIdent(c_Control_TempLevelInClose,    $this->instanceId), $instanceId, 40);
			CreateLink('Öffnen Aussen',       IPS_GetObjectIDByIdent(c_Control_TempLevelOutOpen,    $this->instanceId), $instanceId, 50);
			CreateLink('Öffnen Innen',        IPS_GetObjectIDByIdent(c_Control_TempLevelInOpen,     $this->instanceId), $instanceId, 60);
			$id = @IPS_GetObjectIdByName("Hellingkeits Grenze", $categoryId);
			if ($id!==false) {
				EmptyCategory($id);
				IPS_DeleteInstance($id);
			}
			$instanceId = CreateDummyInstance("Helligkeit", $categoryId, 30);
			CreateLink('Untere Grenzwert',   IPS_GetObjectIDByIdent(c_Control_BrightnessLow,  $this->instanceId), $instanceId, 10);
			CreateLink('Oberer Grenzwert',   IPS_GetObjectIDByIdent(c_Control_BrightnessHigh, $this->instanceId), $instanceId, 20);

			CreateLink('Profil Info',  IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId), $categoryId, 40);
		}

		/**
		 * @public
		 *
		 * Profile löschen
		 *
		 */
		public function Delete() {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileTemp', $this->instanceId, '', '', -1);
			DeleteCategory($this->instanceId);
		}

		/**
		 * @public
		 *
		 * Profile umbenennen
		 *
		 * @param string $newName Neuer Name des Profiles
		 */
		public function Rename($newName) {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileTemp', $this->instanceId, $newName, '', -1);
			IPS_SetName($this->instanceId, $newName);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileName, $this->instanceId), $newName);
		}
		
		/**
		 * @public
		 *
		 * Profil verändern
		 *
		 * @param integer $controlId ID der Variable die verändert werden soll
		 * @param integer $value neuer Wert
		 */
		public function SetValue($controlId, $value) {
			SetValue($controlId, $value);
			IPSShadowing_LogChange($this->instanceId, $value, $controlId);
			$this->Init();
			$this->UpdateProfileInfo();
		}

	}

	/** @}*/

?>