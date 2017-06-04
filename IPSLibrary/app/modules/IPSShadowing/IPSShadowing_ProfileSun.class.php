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
	 * @file          IPSShadowing_ProfileSun.class.php
	 *
	 * Sonnenstand Profil
	 */

   /**
    * @class IPSShadowing_ProfileSun
    *
    * Definiert ein IPSShadowing_ProfileSun Objekt
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 29.04.2012<br/>
    */
	class IPSShadowing_ProfileSun {

		/**
		 * @private
		 * ID des Sonnenstand Profiles
		 */
		private $instanceId;
		
		/**
		 * @private
		 */
		private $azimuth;

		/**
		 * @private
		 */
		private $activationBySun;

		/**
		 * @private
		 */
		private $elevation;

		/**
		 * @public
		 *
		 * Initialisierung des IPSShadowing_ProfileSun Objektes
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
			$data = Get_AnzimuatAndElevation(time(), IPSSHADOWING_LONGITUDE, IPSSHADOWING_LATITUDE);
			$this->azimuth   = $data['Azimuth'];
			$this->elevation = $data['Elevation'];

			$azimuthBgn        = GetValue(IPS_GetObjectIDByIdent(c_Control_AzimuthBgn, $this->instanceId));
			$azimuthEnd        = GetValue(IPS_GetObjectIDByIdent(c_Control_AzimuthEnd, $this->instanceId));
			$elevationLevel    = GetValue(IPS_GetObjectIDByIdent(c_Control_Elevation, $this->instanceId));

			$activationBySun = true;
			$activationBySun = ($activationBySun and $this->elevation>=$elevationLevel);
			$activationBySun = ($activationBySun and $this->azimuth>=$azimuthBgn);
			$activationBySun = ($activationBySun and $this->azimuth<=$azimuthEnd);

			$this->activationBySun = $activationBySun;
		}

		public function UpdateProfileInfo() {
			$info=''.($this->activationBySun?'Profil aktiv':'Profil inaktiv').' (Azimuth='.round($this->azimuth).', Elevation='.round($this->elevation).')';
			if (GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId)) <> $info or true) {
				SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId), $info);
				IPS_SetVariableProfileAssociation('IPSShadowing_ProfileSun', 
												  $this->instanceId,  
												  GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileName, $this->instanceId)), 
												  '', 
												  ($this->activationBySun?c_Color_ProfileActive:-1));
			}
		}
		
		public function GetProfileInfo() {
			$info = '';
			return $info;
		}
		
		public function ActivationBySun() {
			return $this->activationBySun;
		}

		/**
		 * @public
		 *
		 * Neues Profile generieren
		 *
		 * @param string $profileName Name des Profiles
		 * @param integer $azimuthBgn Startwert Sonnenstand 
		 * @param integer $azimuthEnd Endwert Sonnendstand
		 * @param integer $elevation Sonnenstand (Höhe)
		 */
		public static function Create($profileName, $azimuthBgn=0, $azimuthEnd=360, $elevation=30) {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			
			$ScriptIdChangeSettings  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_ChangeSettings');
			$categoryIdprofiles      = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.Sun');
			$profileIdx              = count(IPS_GetChildrenIds($categoryIdprofiles)) + 10;
			$profileId               = CreateCategory ($profileName, $categoryIdprofiles, $profileIdx);
			IPS_SetIdent($profileId, (string)$profileId);
			CreateVariable(c_Control_ProfileName,       3 /*String*/,   $profileId, 0,  '~String',                        $ScriptIdChangeSettings, $profileName,       'Title');
			CreateVariable(c_Control_AzimuthBgn,        1 /*Integer*/,  $profileId, 10, 'IPSShadowing_AzimuthBgn',        $ScriptIdChangeSettings, $azimuthBgn,        'HollowLargeArrowLeft');
			CreateVariable(c_Control_AzimuthEnd,        1 /*Integer*/,  $profileId, 20, 'IPSShadowing_AzimuthEnd',        $ScriptIdChangeSettings, $azimuthEnd,        'HollowLargeArrowRight');
			CreateVariable(c_Control_Elevation,         1 /*Integer*/,  $profileId, 30, 'IPSShadowing_Elevation',         $ScriptIdChangeSettings, $elevation,         'HollowLargeArrowUp');
			CreateVariable(c_Control_Date,              1 /*Integer*/,  $profileId, 40, 'IPSShadowing_Date',              $ScriptIdChangeSettings, 1,                  'Calendar');
			CreateVariable(c_Control_Simulation,        0 /*Boolean*/,  $profileId, 50, '~Switch',                        $ScriptIdChangeSettings, false,              'Repeat');
			CreateVariable(c_Control_Orientation,       0 /*Boolean*/,  $profileId, 60, '~Switch',                        $ScriptIdChangeSettings, false,              'WindDirection');
			CreateVariable(c_Control_ProfileInfo,       3 /*String*/,   $profileId, 70, '~String',                        null,                    '',                 'Information');

			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileSun', $profileId, $profileName, "", -1);
			
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
			$instanceId = CreateDummyInstance("Sonnenstand", $categoryId, 20);
			CreateLink('Azimuth Start', IPS_GetObjectIDByIdent(c_Control_AzimuthBgn, $this->instanceId), $instanceId, 10);
			CreateLink('Azimuth Ende',  IPS_GetObjectIDByIdent(c_Control_AzimuthEnd, $this->instanceId), $instanceId, 20);
			CreateLink('Elevation',     IPS_GetObjectIDByIdent(c_Control_Elevation,  $this->instanceId), $instanceId, 30);
			$instanceId = CreateDummyInstance("Visualisierung", $categoryId, 30);
			CreateLink('Monat',         IPS_GetObjectIDByIdent(c_Control_Date,       $this->instanceId), $instanceId, 10);
			CreateLink('Simulation',    IPS_GetObjectIDByIdent(c_Control_Simulation, $this->instanceId), $instanceId, 20);
			CreateLink('Ausrichtung Süden',  IPS_GetObjectIDByIdent(c_Control_Orientation, $this->instanceId), $instanceId, 30);
			CreateLink('Profil Info',  IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId), $categoryId, 40);

			$this->GenerateGraphic();
		}

		/**
		 * @public
		 *
		 * Profile löschen
		 *
		 */
		public function Delete() {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileSun', $this->instanceId, '', '', -1);
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
			IPS_SetVariableProfileAssociation('IPSShadowing_ProfileSun', $this->instanceId, $newName, '', -1);
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
			if (GetValue($controlId)<>$value) {
				SetValue($controlId, $value);
				IPSShadowing_LogChange($this->instanceId, $value, $controlId);
				$controlIdent = IPS_GetIdent($controlId);
				if ($controlIdent==c_Control_Date) {
					$this->GenerateGraphic(mktime(12, 0, 0, $value, date('d'), date("Y")));
				} elseif ($controlIdent==c_Control_Orientation) {
					$this->GenerateGraphic(mktime(12, 0, 0, GetValue(IPS_GetObjectIDByIdent(c_Control_Date, $this->instanceId)), date('d'), date("Y")));
				} elseif ($controlIdent==c_Control_Simulation) {
					for ($month=1;$month<=12;$month++) {
						$this->GenerateGraphic(mktime(12, 0, 0, $month, date('d'), date("Y")));
						SetValue(IPS_GetObjectIDByIdent(c_Control_Date, $this->instanceId), $month);
						usleep(500000);
					}
					$this->GenerateGraphic();
					SetValue(IPS_GetObjectIDByIdent(c_Control_Date, $this->instanceId), date('n'));
					SetValue($controlId, false);
				} else {
					SetValue(IPS_GetObjectIDByIdent(c_Control_Date, $this->instanceId), date('n'));
					$this->GenerateGraphic();
					$this->Init();
					$this->UpdateProfileInfo();
				}
			}
		}
		
		/**
		 * @private
		 *
		 * Grafik generieren
		 */
		private function GenerateGraphic ($date=null) {
			if ($date==null) {
				$date = time();
			}
			IPSShadowing_GenerateSunGraphic($date, 
					GetValue(IPS_GetObjectIDByIdent(c_Control_AzimuthBgn, $this->instanceId)),
					GetValue(IPS_GetObjectIDByIdent(c_Control_AzimuthEnd, $this->instanceId)),
					GetValue(IPS_GetObjectIDByIdent(c_Control_Elevation,  $this->instanceId)),
					GetValue(IPS_GetObjectIDByIdent(c_Control_Orientation,$this->instanceId)));
					
			$mediaID = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.ProfileManager.GraphsSun.Sonnenstand');
			if ($mediaID > 0) {
				IPS_SendMediaEvent($mediaID);
			}	
		
					
		}
	}

	/** @}*/

?>