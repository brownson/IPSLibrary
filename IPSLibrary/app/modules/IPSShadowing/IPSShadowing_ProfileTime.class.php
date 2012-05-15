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
	 * @file          IPSShadowing_ProfileTime.class.php
	 *
	 * Zeit Profil Verwaltung
	 */

   /**
    * @class IPSShadowing_ProfileTime
    *
    * Definiert ein IPSShadowing_ProfileTime Objekt
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 01.04.2012<br/>
    */
	class IPSShadowing_ProfileTime {

		/**
		 * @private
		 * ID des Zeit Profiles
		 */
		private $instanceId;

		/**
		 * @private
		 * Type of Profile (BgnOfDay or EndOfDay)
		 */
		private $profileType;

		/**
		 * @private
		 * current Time of Profile
		 */
		private $time;

		/**
		 * @public
		 *
		 * Initialisierung des IPSShadowing_ProfileTime Objektes
		 *
		 * @param integer $instanceId InstanceId Profiles
		 */
		public function __construct($instanceId) {
			$this->instanceId  = IPSUtil_ObjectIDByPath($instanceId);
			$this->profileType = IPS_GetName(IPS_GetParent($this->instanceId));
			$this->CalculateTime();
		}

		private function GetTimeByParams($mode, $time, $offset) {
			switch ($mode) {
				case c_ModeId_Individual:
					break;
				case c_ModeId_Twillight:
					if ($this->profileType=='BgnOfDay') {
						$time = GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_TWILIGHTSUNRISE));
					} else {
						$time = GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_TWILIGHTSUNSET));
					}
					break;
				case c_ModeId_LimitedTwillight:
					if ($this->profileType=='BgnOfDay') {
						$time = GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_TWILIGHTSUNRISELIMITED));
					} else {
						$time = GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_TWILIGHTSUNSETLIMITED));
					}
					break;
				default:
					throw new Exception('Unknown Mode '.$mode.' for Profile '.$this->instanceId);
			}
			$time = mktime(substr($time,0,2), substr($time,3,2), 0);
			$time = strtotime("$offset minutes", $time);
			return $time;
		}
		
		private function CalculateTime() {
			$timeWorkday = $this->GetTimeByParams(
				GetValue(IPS_GetObjectIDByIdent(c_Control_WorkdayMode, $this->instanceId)),
				GetValue(IPS_GetObjectIDByIdent(c_Control_WorkdayTime, $this->instanceId)),
				GetValue(IPS_GetObjectIDByIdent(c_Control_WorkdayOffset, $this->instanceId)));
			$timeWeekend = $this->GetTimeByParams(
				GetValue(IPS_GetObjectIDByIdent(c_Control_WeekendMode, $this->instanceId)),
				GetValue(IPS_GetObjectIDByIdent(c_Control_WeekendTime, $this->instanceId)),
				GetValue(IPS_GetObjectIDByIdent(c_Control_WeekendOffset, $this->instanceId)));

			$timeStringWorkday = date('H:i', $timeWorkday);
			if ($timeStringWorkday<>GetValue(IPS_GetObjectIDByIdent(c_Control_WorkdayTime, $this->instanceId))
			    and GetValue(IPS_GetObjectIDByIdent(c_Control_WorkdayMode, $this->instanceId))<>c_ModeId_Individual) {
				SetValue(IPS_GetObjectIDByIdent(c_Control_WorkdayTime, $this->instanceId), $timeStringWorkday);
			}
			$timeStringWeekend = date('H:i', $timeWeekend);
			if ($timeStringWeekend<>GetValue(IPS_GetObjectIDByIdent(c_Control_WeekendTime, $this->instanceId))
			    and GetValue(IPS_GetObjectIDByIdent(c_Control_WeekendMode, $this->instanceId))<>c_ModeId_Individual) {
				SetValue(IPS_GetObjectIDByIdent(c_Control_WeekendTime, $this->instanceId), $timeStringWeekend);
			}
			
				
			$isWorkingDay = IPSShadowing_IsWorkingDay();
			if ($isWorkingDay===null) {
				$dayOfWeek = date("w"); 
				$isWorkingDay = ($dayOfWeek >= 1 and $dayOfWeek <= 5);
			}
			if ($isWorkingDay) {
				$this->time = $timeWorkday;
			} else {
				$this->time = $timeWeekend;
			}
		}
		
		public function UpdateProfileInfo() {
			$info = 'Zeit='.date('H:i', $this->time);
			if (GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId)) <> $info) {
				SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId), $info);
			}
		}

		
		/**
		 * @public
		 *
		 * Liefert die aktuelle Zeit des Zeit Profiles
		 *
		 * @return float Zeitpunkt des eingestellten Profiles
		 */
		public function GetTime() {
			return $this->time;
		}
		
		/**
		 * @public
		 *
		 * Neues Profile generieren
		 *
		 * @param string $profileName Name des Profiles
		 * @param string $profileType Type des Profiles (BgnOfDay or EndOfDay)
		 * @param integer $workdayMode Zeitmodus für Werktage
		 * @param string $workdayTime Zeitpunkt  für Werktage
		 * @param integer $workdayOffset Offset in Minuten für Werktage
		 * @param integer $weekendMode Zeitmodus für Wochenende
		 * @param string $weekendTime Zeitpunkt für Wochenende
		 * @param integer $weekendOffset Offset in Minuten für Wochenende
		 */
		public static function Create($profileName, $profileType='BgnOfDay', $workdayMode=c_ModeId_Individual, $workdayTime='07:00', $workdayOffset=0, $weekendMode=c_ModeId_Individual, $weekendTime='08:00', $weekendOffset=0) {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			
			$ScriptIdChangeSettings  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_ChangeSettings');
			$categoryIdprofiles      = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.'.$profileType);
			$profileIdx              = count(IPS_GetChildrenIds($categoryIdprofiles)) + 10;

			$profileId = CreateCategory ($profileName, $categoryIdprofiles, $profileIdx);
			IPS_SetIdent($profileId, (string)$profileId);

			CreateVariable(c_Control_ProfileName,   3 /*String*/,   $profileId, 0,  '~String',                 $ScriptIdChangeSettings, $profileName,   'Title');
			CreateVariable(c_Control_WorkdayMode,   1 /*Integer*/,  $profileId, 10, 'IPSShadowing_TimeMode',   $ScriptIdChangeSettings, $workdayMode,   'Gear');
			CreateVariable(c_Control_WorkdayTime,   3 /*String*/,   $profileId, 20, '~String',                 ($workdayMode==c_ModeId_Individual?$ScriptIdChangeSettings:null), $workdayTime,   'Clock');
			CreateVariable(c_Control_WorkdayOffset, 1 /*Integer*/,  $profileId, 30, 'IPSShadowing_TimeOffset', ($workdayMode==c_ModeId_Individual?null:$ScriptIdChangeSettings), $workdayOffset, 'Distance');
			CreateVariable(c_Control_WeekendMode,   1 /*Integer*/,  $profileId, 40, 'IPSShadowing_TimeMode',   $ScriptIdChangeSettings, $weekendMode,   'Gear');
			CreateVariable(c_Control_WeekendTime,   3 /*String*/,   $profileId, 50, '~String',                 ($weekendMode==c_ModeId_Individual?$ScriptIdChangeSettings:null), $weekendTime,   'Clock');
			CreateVariable(c_Control_WeekendOffset, 1 /*Integer*/,  $profileId, 60, 'IPSShadowing_TimeOffset', ($weekendMode==c_ModeId_Individual?null:$ScriptIdChangeSettings), $weekendOffset, 'Distance');
			CreateVariable(c_Control_ProfileInfo,   3 /*String*/,   $profileId, 70, '~String',                 null,                    '',             'Information');

			IPS_SetVariableProfileAssociation('IPSShadowing_Profile'.$profileType, $profileId, $profileName, "", -1);

			return $profileId;
		}

		/**
		 * @public
		 *
		 * Profile löschen
		 *
		 */
		public function Delete() {
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			IPS_SetVariableProfileAssociation('IPSShadowing_Profile'.$this->profileType, $this->instanceId, '', '', -1);
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
			IPS_SetVariableProfileAssociation('IPSShadowing_Profile'.$this->profileType, $this->instanceId, $newName, '', -1);
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
				$controlIdent = IPS_GetIdent($controlId);
				$scriptIdChangeSettings = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_ChangeSettings');

				if ($controlIdent==c_Control_WorkdayMode) {
					if ($value==c_ModeId_Individual) {
						IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_WorkdayTime, $this->instanceId), $scriptIdChangeSettings);
						SetValue(IPS_GetObjectIDByIdent(c_Control_WorkdayOffset, $this->instanceId), 0);
						IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_WorkdayOffset, $this->instanceId), null);
					} else {
						IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_WorkdayTime, $this->instanceId), null);
						IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_WorkdayOffset, $this->instanceId), $scriptIdChangeSettings);
					}
				}
				if ($controlIdent==c_Control_WeekendMode) {
					if ($value==c_ModeId_Individual) {
						IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_WeekendTime, $this->instanceId), $scriptIdChangeSettings);
						SetValue(IPS_GetObjectIDByIdent(c_Control_WeekendOffset, $this->instanceId), 0);
						IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_WeekendOffset, $this->instanceId), null);
					} else {
						IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_WeekendTime, $this->instanceId), null);
						IPS_SetVariableCustomAction(IPS_GetObjectIDByIdent(c_Control_WeekendOffset, $this->instanceId), $scriptIdChangeSettings);
					}
				}
				SetValue($controlId, $value);
				IPSShadowing_LogChange($this->instanceId, $value, $controlId);
				$this->CalculateTime();
			}
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
			CreateLink('Profil Name', IPS_GetObjectIDByIdent(c_Control_ProfileName, $this->instanceId), $categoryId, 10);
			$instanceId = CreateDummyInstance("Werktag", $categoryId, 20);
			CreateLink('Modus',       IPS_GetObjectIDByIdent(c_Control_WorkdayMode,   $this->instanceId), $instanceId, 10);
			CreateLink('Zeit',        IPS_GetObjectIDByIdent(c_Control_WorkdayTime,   $this->instanceId), $instanceId, 20);
			CreateLink('Versatz',     IPS_GetObjectIDByIdent(c_Control_WorkdayOffset, $this->instanceId), $instanceId, 30);
			$instanceId = CreateDummyInstance("Wochenende", $categoryId, 30);
			CreateLink('Modus',       IPS_GetObjectIDByIdent(c_Control_WeekendMode,   $this->instanceId), $instanceId, 10);
			CreateLink('Zeit',        IPS_GetObjectIDByIdent(c_Control_WeekendTime,   $this->instanceId), $instanceId, 20);
			CreateLink('Versatz',     IPS_GetObjectIDByIdent(c_Control_WeekendOffset, $this->instanceId), $instanceId, 30);
			CreateLink('Profil Info', IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->instanceId), $categoryId, 40);
		}
		

	}


	/** @}*/

?>