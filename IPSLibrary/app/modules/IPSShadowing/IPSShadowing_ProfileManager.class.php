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
	 * @file          IPSShadowing_ProfileManager.class.php
	 *
	 * Verwaltung von Profilen zur Beschattungssteuerung
	 */

   /**
    * @class IPSShadowing_ProfileManager
    *
    * Definiert ein IPSShadowing_ProfileManager Objekt
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 06.04.2012<br/>
    */
	class IPSShadowing_ProfileManager {

		private $instanceId;
		private $profilesTemp;
		private $profilesSun;
		private $profilesWeather;
		private $profilesBgnOfDay;
		private $profilesEndOfDay;
		private $profileIDsTemp;
		private $profileIDsSun;
		private $profileIDsWeather;
		private $profileIDsBgnOfDay;
		private $profileIDsEndOfDay;
		private $present;

		/**
		 * @public
		 *
		 * Initialisierung des IPSShadowing_ProfileManager Objektes
		 *
		 */
		public function __construct() {
			$this->instanceId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.ProfileManager');
			$this->Init();
			$this->present = null;
			if (IPSSHADOWING_PRESENT <> '') {
				$this->present = GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_PRESENT));
			}
			if (IPSSHADOWING_ABSENCE <> '') {
				$this->present = !GetValue(IPSUtil_ObjectIDByPath(IPSSHADOWING_ABSENCE));
			}
		}

		public function UpdateProfileInfos() {
			foreach ($this->profilesTemp as $profile) {
				$profile->UpdateProfileInfo();
			}
			foreach ($this->profilesSun as $profile) {
				$profile->UpdateProfileInfo();
			}
			foreach ($this->profilesWeather as $profile) {
				$profile->UpdateProfileInfo();
			}
			foreach ($this->profilesBgnOfDay as $profile) {
				$profile->UpdateProfileInfo();
			}
			foreach ($this->profilesEndOfDay as $profile) {
				$profile->UpdateProfileInfo();
			}
		}
		
		public function GetProfileInfo($profileIdBgnOfDay, $profileIdEndOfDay, $profileIdTemp, $tempIndoorPath='') {
			$profileBgnOfDay     = $this->GetProfileBgnOfDay($profileIdBgnOfDay);
			$profileEndOfDay     = $this->GetProfileEndOfDay($profileIdEndOfDay);
			$profileBgnOfDayInfo = date('H:i', $profileBgnOfDay->GetTime());
			$profileEndOfDayInfo = date('H:i', $profileEndOfDay->GetTime());
			$profileTemp         = $this->GetProfileTemp($profileIdTemp);
			$profileTempInfo     = $profileTemp->GetProfileInfo($tempIndoorPath);
			
			$info = 'Tag='.$profileBgnOfDayInfo.'-'.$profileEndOfDayInfo.', '.$profileTempInfo;
			return $info;
		}
		
		public function AssignAllProfileAssociations() {
			foreach ($this->profileIDsTemp as $profileId) {
				IPS_SetVariableProfileAssociation('IPSShadowing_ProfileTemp', $profileId, IPS_GetName($profileId), '', -1);
			}
			foreach ($this->profileIDsSun as $profileId) {
				IPS_SetVariableProfileAssociation('IPSShadowing_ProfileSun', $profileId, IPS_GetName($profileId), '', -1);
			}
			foreach ($this->profileIDsWeather as $profileId) {
				IPS_SetVariableProfileAssociation('IPSShadowing_ProfileWeather', $profileId, IPS_GetName($profileId), '', -1);
			}
			foreach ($this->profileIDsBgnOfDay as $profileId) {
				IPS_SetVariableProfileAssociation('IPSShadowing_ProfileBgnOfDay', $profileId, IPS_GetName($profileId), '', -1);
			}
			foreach ($this->profileIDsEndOfDay as $profileId) {
				IPS_SetVariableProfileAssociation('IPSShadowing_ProfileEndOfDay', $profileId, IPS_GetName($profileId), '', -1);
			}
		}

		public function CorrectDeletedDeviceProfile($controlName, $idList, $profileList) {
			$categoryIdDevices = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Devices');
			$deviceIds         = IPS_GetChildrenIDs($categoryIdDevices);
			foreach ($deviceIds as $deviceIdx=>$deviceId) {
				$controlId = @IPS_GetObjectIDByIdent($controlName, $deviceId);
				if ($controlId!==false) {
					$profileId = GetValue($controlId);
					if (!array_key_exists($profileId, $profileList)) {
						SetValue($controlId, $idList[0]);
						IPSShadowing_LogChange($deviceId, $idList[0], $controlId);
					}
				}
			}
		}

		public function CorrectDeletedDeviceProfiles() {
			$this->CorrectDeletedDeviceProfile(c_Control_ProfileBgnOfDay, $this->profileIDsBgnOfDay, $this->profilesBgnOfDay);
			$this->CorrectDeletedDeviceProfile(c_Control_ProfileEndOfDay, $this->profileIDsEndOfDay, $this->profilesEndOfDay);
			$this->CorrectDeletedDeviceProfile(c_Control_ProfileTemp,     $this->profileIDsTemp,     $this->profilesTemp);
			$this->CorrectDeletedDeviceProfile(c_Control_ProfileSun,      $this->profileIDsSun,      $this->profilesSun);
			$this->CorrectDeletedDeviceProfile(c_Control_ProfileWeather,  $this->profileIDsWeather,  $this->profilesWeather);
		}
		
		private function Init() {
			$categoryId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.Temp');
			$this->profileIDsTemp = IPS_GetChildrenIds($categoryId);
			foreach ($this->profileIDsTemp as $profileId) {
				$this->profilesTemp[$profileId] = new IPSShadowing_ProfileTemp($profileId);
			}

			$categoryId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.Sun');
			$this->profileIDsSun = IPS_GetChildrenIds($categoryId);
			foreach ($this->profileIDsSun as $profileId) {
				$this->profilesSun[$profileId] = new IPSShadowing_ProfileSun($profileId);
			}

			$categoryId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.Weather');
			$this->profileIDsWeather = IPS_GetChildrenIds($categoryId);
			foreach ($this->profileIDsWeather as $profileId) {
				$this->profilesWeather[$profileId] = new IPSShadowing_ProfileWeather($profileId);
			}

			$categoryId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.BgnOfDay');
			$this->profileIDsBgnOfDay = IPS_GetChildrenIds($categoryId);
			foreach ($this->profileIDsBgnOfDay as $profileId) {
				$this->profilesBgnOfDay[$profileId] = new IPSShadowing_ProfileTime($profileId);
			}

			$categoryId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Profiles.EndOfDay');
			$this->profileIDsEndOfDay = IPS_GetChildrenIds($categoryId);
			foreach ($this->profileIDsEndOfDay as $profileId) {
				$this->profilesEndOfDay[$profileId] = new IPSShadowing_ProfileTime($profileId);
			}
		}
		
		public function GetProfileTemp($profileId) {
			return $this->profilesTemp[$profileId];
		}

		public function GetProfileSun($profileId) {
			return $this->profilesSun[$profileId];
		}

		public function GetProfileWeather($profileId) {
			return $this->profilesWeather[$profileId];
		}

		public function GetProfileBgnOfDay($profileId) {
			return $this->profilesBgnOfDay[$profileId];
		}

		public function GetProfileEndOfDay($profileId) {
			return $this->profilesEndOfDay[$profileId];
		}

		public function ShadowingByTemp($profileIdSun, $profileIdTemp, $tempIndoorPath='') {
			$profileTemp = $this->profilesTemp[$profileIdTemp];
			$profileSun  = $this->profilesSun[$profileIdSun];
			return ($profileTemp->ShadowingByTemp($tempIndoorPath) and $profileSun->ActivationBySun());
		}

		public function CloseByTemp($profileIdSun, $profileIdTemp, $tempIndoorPath='') {
			$profileTemp = $this->profilesTemp[$profileIdTemp];
			$profileSun  = $this->profilesSun[$profileIdSun];
			return ($profileTemp->CloseByTemp($tempIndoorPath) and $profileSun->ActivationBySun());
		}

		public function OpenByTemp($profileIdSun, $profileIdTemp, $tempIndoorPath='') {
			$profileTemp = $this->profilesTemp[$profileIdTemp];
			$profileSun  = $this->profilesSun[$profileIdSun];
			return ($profileTemp->OpenByTemp($tempIndoorPath));
		}

		public function ActivationByWeather($profileIdWeather) {
			if ($profileIdWeather==null) {
				return;
			}
			$profileWeather = $this->profilesWeather[$profileIdWeather];
			return $profileWeather->ActivationByWeather();
		}

		public function IsDay($profileIdBgnOfDay, $profileIdEndOfDay) {
			$profileBgnOfDay = $this->profilesBgnOfDay[$profileIdBgnOfDay];
			$profileEndOfDay = $this->profilesEndOfDay[$profileIdEndOfDay];
			return  (time() >= $profileBgnOfDay->GetTime() and  time() < $profileEndOfDay->GetTime());
		}

		public function IsNight($profileIdBgnOfDay, $profileIdEndOfDay) {
			return !$this->IsDay($profileIdBgnOfDay, $profileIdEndOfDay);
		}
		
		public function IsDayNightChange($profileIdBgnOfDay, $profileIdEndOfDay) {
			$profileBgnOfDay = $this->profilesBgnOfDay[$profileIdBgnOfDay];
			$profileEndOfDay = $this->profilesEndOfDay[$profileIdEndOfDay];
			$timeCurrent = time();
			$timeLast    = time()-300;

			// Examples
			//   BgnOfDay=07:00, timeCurrent=06:55, timeLast=06:50 ==> FALSE
			//   BgnOfDay=07:00, timeCurrent=07:00, timeLast=06:55 ==> TRUE
			//   BgnOfDay=07:00, timeCurrent=07:05, timeLast=07:00 ==> FALSE
			return (($timeCurrent >= $profileBgnOfDay->GetTime() and  $timeLast < $profileBgnOfDay->GetTime()) or 
			        ($timeCurrent >= $profileEndOfDay->GetTime() and  $timeLast < $profileEndOfDay->GetTime()));
		}

		public function GetPresent() {
			return $this->present;
		}

		public function SelectTemp($profileId) {
			$displayId = IPS_GetObjectIDByIdent('DisplayTemp', $this->instanceId); 
			$profile = new IPSShadowing_ProfileTemp($profileId);
			$profile->Display($displayId);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileTempSelect, $this->instanceId), $profileId);
		}

		public function SelectSun($profileId) {
			$displayId = IPS_GetObjectIDByIdent('DisplaySun', $this->instanceId); 
			$profile = new IPSShadowing_ProfileSun($profileId);
			$profile->Display($displayId);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileSunSelect, $this->instanceId), $profileId);
		}

		public function SelectWeather($profileId) {
			$displayId = IPS_GetObjectIDByIdent('DisplayWeather', $this->instanceId); 
			$profile = new IPSShadowing_ProfileWeather($profileId);
			$profile->Display($displayId);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileWeatherSelect, $this->instanceId), $profileId);
		}

		public function SelectBgnOfDay($profileId) {
			$displayId = IPS_GetObjectIDByIdent('DisplayBgnOfDay', $this->instanceId); 
			$profile = new IPSShadowing_ProfileTime($profileId);
			$profile->Display($displayId);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileBgnOfDaySelect, $this->instanceId), $profileId);
		}

		public function SelectEndOfDay($profileId) {
			$displayId = IPS_GetObjectIDByIdent('DisplayEndOfDay', $this->instanceId); 
			$profile = new IPSShadowing_ProfileTime($profileId);
			$profile->Display($displayId);
			SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileEndOfDaySelect, $this->instanceId), $profileId);
		}

		public function CreateTemp($profileName='Neues Profile') {
			$profileId = IPSShadowing_ProfileTemp::Create($profileName);
			$this->SelectTemp($profileId);
		}
		public function CreateSun($profileName='Neues Profile', $anzimutBgn=120, $anzimutEnd=240, $elevation=25) {
			$profileId = IPSShadowing_ProfileSun::Create($profileName, $anzimutBgn, $anzimutEnd, $elevation);
			$this->SelectSun($profileId);
		}
		public function CreateWeather($profileName='Neues Profile', $rainCheck=true, $windLevel=40) {
			$profileId = IPSShadowing_ProfileWeather::Create($profileName, $rainCheck, $windLevel);
			$this->SelectWeather($profileId);
		}
		public function CreateBgnOfDay($name='Neues Profil', $workdayMode=c_ModeId_Individual, $workdayTime='07:00', $workdayOffset=0, $weekendMode=c_ModeId_Individual, $weekendTime='08:00', $weekendOffset=0) {
			$profileId = IPSShadowing_ProfileTime::Create($name, 'BgnOfDay', $workdayMode, $workdayTime, $workdayOffset, $weekendMode, $weekendTime, $weekendOffset);
			$this->SelectBgnOfDay($profileId);
		}
		public function CreateEndOfDay($name='Neues Profil', $workdayMode=c_ModeId_Individual, $workdayTime='20:00', $workdayOffset=0, $weekendMode=c_ModeId_Individual, $weekendTime='21:00', $weekendOffset=0) {
			$profileId = IPSShadowing_ProfileTime::Create($name, 'EndOfDay', $workdayMode, $workdayTime, $workdayOffset, $weekendMode, $weekendTime, $weekendOffset);
			$this->SelectEndOfDay($profileId);
		}

		public function DeleteTemp() {
			if (count($this->profilesTemp)==1) { 
				return; 
			}
			$controlId = IPS_GetObjectIDByIdent(c_Control_ProfileTempSelect, $this->instanceId);
			$profileId = GetValue($controlId);
			$profile = new IPSShadowing_ProfileTemp($profileId);
			$profile->Delete();
			$this->Init();
			$this->CorrectDeletedDeviceProfiles();
			$this->SelectTemp($this->profileIDsTemp[0]);
		}

		public function DeleteSun() {
			if (count($this->profilesSun)==1) { 
				return; 
			}
			$controlId = IPS_GetObjectIDByIdent(c_Control_ProfileSunSelect, $this->instanceId);
			$profileId = GetValue($controlId);
			$profile = new IPSShadowing_ProfileSun($profileId);
			$profile->Delete();
			$this->Init();
			$this->CorrectDeletedDeviceProfiles();
			$this->SelectSun($this->profileIDsSun[0]);
		}

		public function DeleteWeather() {
			if (count($this->profilesWeather)==1) { 
				return; 
			}
			$controlId = IPS_GetObjectIDByIdent(c_Control_ProfileWeatherSelect, $this->instanceId);
			$profileId = GetValue($controlId);
			$profile = new IPSShadowing_ProfileWeather($profileId);
			$profile->Delete();
			$this->Init();
			$this->CorrectDeletedDeviceProfiles();
			$this->SelectWeather($this->profileIDsWeather[0]);
		}

		public function DeleteBgnOfDay() {
			if (count( $this->profilesBgnOfDay)==1) { 
				return; 
			}
			$controlId = IPS_GetObjectIDByIdent(c_Control_ProfileBgnOfDaySelect, $this->instanceId);
			$profileId = GetValue($controlId);
			$profile = new IPSShadowing_ProfileTime($profileId);
			$profile->Delete();
			$this->Init();
			$this->CorrectDeletedDeviceProfiles();
			$this->SelectBgnOfDay($this->profileIDsBgnOfDay[0]);
		}

		public function DeleteEndOfDay() {
			if (count($this->profilesEndOfDay)==1) { 
				return; 
			}
			$controlId = IPS_GetObjectIDByIdent(c_Control_ProfileEndOfDaySelect, $this->instanceId);
			$profileId = GetValue($controlId);
			$profile = new IPSShadowing_ProfileTime($profileId);
			$profile->Delete();
			$this->Init();
			$this->CorrectDeletedDeviceProfiles();
			$this->SelectEndOfDay($this->profileIDsEndOfDay[0]);
		}

		private function GetProfileByControlId($controlId) {
			$profileId = IPS_GetParent($controlId);
			$profileType = IPS_GetIdent(IPS_GetParent($profileId));
			switch ($profileType) {
				case 'Temp';
					$profile = $this->GetProfileTemp($profileId);
					break;
				case 'Sun';
					$profile = $this->GetProfileSun($profileId);
					break;
				case 'Weather';
					$profile = $this->GetProfileWeather($profileId);
					break;
				case 'BgnOfDay';
					$profile = $this->GetProfileBgnOfDay($profileId);
					break;
				case 'EndOfDay';
					$profile = $this->GetProfileEndOfDay($profileId);
					break;
				default:
					throw new Exception('Unknown Profile Type '.$profileType);
			}
			return $profile;
		}
		
		public function Rename($controlId, $newName) {
			$profile = $this->GetProfileByControlId($controlId);
			$profile->Rename($newName);
		}
		
		public function SetValue($controlId, $value) {
			$profile = $this->GetProfileByControlId($controlId);
			$profile->SetValue($controlId, $value);
		}
		
	}

	/** @}*/

?>