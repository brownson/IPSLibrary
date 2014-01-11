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
	 * @file          IPSShadowing_ProfileWeather.class.php
	 *
	 * Wetter Profil 
	 */

   /**
    * @class IPSShadowing_ProfileWeather
    *
    * Definiert ein IPSShadowing_ProfileWeather Objekt
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 29.04.2012<br/>
    */

	 /**@defgroup IPSShadowing_ProfileWeather 
	 * update 			Windlevel with Beaufort level functions
	 * @author        Günter Strassnigg
	 */

	class IPSShadowing_ProfileWeather {

		/**
		 * @private
		 * ID des Wetter Profiles
		 */
		private $instanceId;
		
		/**
		 * @private
		 * Aktivierung bei Sonnenstand und Helligkeit
		 */
		private $activationByWeather;

		private $rainSensor;
		private $windSensor;
		private $rainCheck;
		private $windLevel;

		/**
		 * @public
		 *
		 * Initialisierung des IPSShadowing_ProfileWeather Objektes
		 *
		 * @param integer $instanceId InstanceId Profile
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
			$this->windLevel = GetValue(IPS_GetObjectIDByIdent(c_Control_WindLevel, $this->instanceId));
			$this->rainCheck = GetValue(IPS_GetObjectIDByIdent(c_Control_RainCheck, $this->instanceId));
			$this->windSensor = null;
			$this->rainSensor = null;
			
			$activationByWeather = false;
			if (IPSSHADOWING_WINDSENSOR <> '') {
				$this->windSensor = GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_WINDSENSOR));
				if (IPSSHADOWING_WINDLEVEL_CLASSIFICATION==false) {
					$activationByWeather = ($activationByWeather or ($this->windSensor >= $this->windLevel));
				} else {
					$activationByWeather = ($activationByWeather or ($this->ConvertKMHtoBeaufort($this->windSensor) >= $this->windLevel));
				}
			}

			if (IPSSHADOWING_RAINSENSOR <> '') {
				$this->rainSensor = GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_RAINSENSOR));
				if ($this->rainCheck) {
					$activationByWeather = ($activationByWeather and ($this->rainSensor and $this->rainCheck));
				}
			}

			$this->activationByWeather = $activationByWeather;
		}

		public function UpdateProfileInfo() {
			$info  = ''.($this->activationByWeather?'Profil aktiv':'Profil inaktiv').' (WindSensor='.($this->windSensor===null?'"nicht vorhanden"':$this->windSensor.' kmh');
			if (IPSSHADOWING_RAINSENSOR <> '') {
				$info .= ', RegenSensor='.($this->rainSensor?'Regen)':'kein Regen)');
			} else {
				$info .= ', RegenSensor="nicht vorhanden")';
			}
			if (GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId)) <> $info or true) {
				SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId), $info);
				IPS_SetVariableProfileAssociation('IPSShadowing_ProfileWeather', 
												  $this->instanceId,  
												  GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileName, $this->instanceId)), 
												  '', 
												  ($this->activationByWeather?c_Color_ProfileActive:-1));
			}
		}

		
		public function GetProfileInfo() {
			$info = '';
			if ($this->windSensor <> '') {
				$activationByWeather = ($activationByWeather or ($this->windSensor >= $this->windLevel));
				$info .= 'Wind='.$this->windSensor.' kmh';
			}
			if ($this->RainSensor <> '') {
				if ($info<>'') { $info.=', ';}
				' Regen='.($this->RainSensor?'Ja':'Nein');
			}
			return $info;
		}
		
		public function ActivationByWeather() {
			return $this->activationByWeather;
		}

		/**
		 * @public
		 *
		 * Neues Profile generieren
		 *
		 * @param string $profileName Name des Profiles
		 * @param boolean $rainCheck Überprüfung Regensensor
		 * @param integer $windLevel Level für Wind Aktivierung
		 */
		public static function Create($profileName, $rainCheck=true, $windLevel=40) {
    			IPSUtils_Include ("IPSShadowing_Configuration.inc.php",     "IPSLibrary::config::modules::IPSShadowing");
	 			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			
			$ScriptIdChangeSettings  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_ChangeSettings');
			$categoryIdprofiles      = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.Weather');
			$profileIdx              = count(IPS_GetChildrenIds($categoryIdprofiles)) + 10;
			$profileId               = CreateCategory ($profileName, $categoryIdprofiles, $profileIdx);
			IPS_SetIdent($profileId, (string)$profileId);
			CreateVariable(c_Control_ProfileName,       3 /*String*/,   $profileId, 0,  '~String',            $ScriptIdChangeSettings, $profileName,  'Title');
			CreateVariable(c_Control_RainCheck,         0 /*Boolean*/,  $profileId, 10, '~Switch',            $ScriptIdChangeSettings, $rainCheck,    'Drops');
			if (IPSSHADOWING_WINDLEVEL_CLASSIFICATION) {
				$windLevel=intval($windLevel/3.6);
				CreateVariable(c_Control_WindLevel,         1 /*Integer*/,  $profileId, 20, 'IPSShadowing_WindBeaufort',  $ScriptIdChangeSettings, $windLevel,    'WindSpeed');
			} else {
				CreateVariable(c_Control_WindLevel,         1 /*Integer*/,  $profileId, 20, 'IPSShadowing_Wind',  $ScriptIdChangeSettings, $windLevel,    'WindSpeed');
			}
			CreateVariable(c_Control_ProfileInfo,       3 /*String*/,   $profileId, 30, '~String',            null,                    '',            'Information');

			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileWeather', $profileId, $profileName, "", -1);
			
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
			$instanceId = CreateDummyInstance("Regen", $categoryId, 20);
			CreateLink('Aktivierung bei Regen',  IPS_GetObjectIDByIdent(c_Control_RainCheck,   $this->instanceId), $instanceId, 10);
			$instanceId = CreateDummyInstance("Wind", $categoryId, 30);
			CreateLink('Windgeschwindigkeits Grenze',  IPS_GetObjectIDByIdent(c_Control_WindLevel, $this->instanceId), $instanceId, 10);
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
			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileWeather', $this->instanceId, '', '', -1);
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
			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileWeather', $this->instanceId, $newName, '', -1);
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
		

		/**
		 * @private
		 *
		 * Umrechnung der Windgeschwindigkeit in Beaufort
		 *
		 * @param variant $value    Windgeschwindigkeit (in km/h)
		 * @param variant $beaufort Windgeschwindigkeit (lt Beaufortskala) 
		 */
		private function ConvertKMHtoBeaufort($value) {
			$beauforttable=explode(';','0;0.3;1.6;3.4;5.5;8;10.8;13.9;17.2;20.8;24.8;28.5;32.7');
			for ($beaufort=count($beauforttable)-1;$beaufort>0;$beaufort--) {
				if (($value/3.6)>=$beauforttable[$beaufort]) {break;}
			}
			return $beaufort;
		}
		
	}

	/** @}*/

?>