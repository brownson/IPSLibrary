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

	/**@ingroup ipslight
	 * @{
	 *
	 * @file          IPSLight_Simulator.class.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 26.01.2013<br/>
	 *
	 * Funktionen zur Anwesenheits Simulation 
	 *
	 */

	/**
	 * @class IPSLight_Simulator
	 *
	 * Definiert ein IPSLight_Simulator Objekt
	 *
	 * @author Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 26.01.2013<br/>
	 */
	class IPSLight_Simulator {

		private $categoryIdSwitches;
		private $categoryIdSimulation;

		// ============================================================================================================================
		public function __construct() {
			$_IPS['ABORT_ON_ERROR'] = true;

			$baseId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSLight');
			$this->categoryIdSwitches    = IPS_GetObjectIDByIdent('Switches', $baseId);
			$this->categoryIdSimulation  = IPS_GetObjectIDByIdent('Simulation', $baseId);
		}

		// ============================================================================================================================
		private function GetFileName($date=null) {
			if ($date === null) {
				$date = date(IPSLIGHT_SIMULATION_DATEFMT);
			}
			$file = IPS_GetKernelDir().'\\Simulation\\IPSLight_'.$date.'.txt';
			
			return $file;
		}

		// ============================================================================================================================
		private function WriteStatusChange($text) {
			$fileName   = $this->GetFileName();
			$fileHandle = fopen($fileName, "a");
			if ($fileHandle===false) {
				IPSLogger_Err(__file__, 'Error opening File '.$fileName);
				return;
			}
			fwrite($fileHandle, $text.chr(13));
			fclose($fileHandle);
		}

		// ============================================================================================================================
		public function StoreStatusChange ($switchId, $value, $level=null, $color=null) {
			$text = array(date(IPSLIGHT_SIMULATION_TIMEFMT), 
			              IPS_GetName($switchId), 
			              ($value?'On':'Off'),
			              $level,
			              $color);
			$text = implode(',', $text);
			$this->WriteStatusChange($text);
		}

		// ============================================================================================================================
		public function SetState($value) {
			$lastTime = '';
			if ($value) {
				$lastTime = date(IPSLIGHT_SIMULATION_TIMEFMT, time());
			}
			$variableIdLastTime = IPS_GetObjectIDByIdent(IPSLIGHT_SIMULATION_VARTIME, $this->categoryIdSimulation);
			SetValue($variableIdLastTime, $lastTime);
			
			if ($this->StartSimulationTimer($value)===true) {
				$variableIdState = IPS_GetVariableIdByName(IPSLIGHT_SIMULATION_VARSTATE, $this->categoryIdSimulation);
				SetValue($variableIdState, $value);
			}
		}

		// ============================================================================================================================
		private function CheckFileExists($date) {
			$fileName = $this->GetFileName($date);
			return file_exists($fileName);
		}
		
		// ============================================================================================================================
		public function SetMode($value) {
			$variableId = IPS_GetVariableIdByName(IPSLIGHT_SIMULATION_VARMODE, $this->categoryIdSimulation);

			switch($value) {
				case IPSLIGHT_SIMULATION_MODEDAYS:
					SetValue($variableId, $value);
					break;
				case IPSLIGHT_SIMULATION_MODEUSR1:
					if ($this->CheckFileExists('20000101')) {
						SetValue($variableId, $value);
					}
					break;
				case IPSLIGHT_SIMULATION_MODEUSR2:
					if ($this->CheckFileExists('20000102')) {
						SetValue($variableId, $value);
					}
					break;
				default:
					IPSLogger_Err(__file__,'Unknown Mode '.$mode);
					exit;
			}
		}

		// ============================================================================================================================
		public function SetDays($value) {
			$variableId = IPS_GetVariableIdByName(IPSLIGHT_SIMULATION_VARDAYS, $this->categoryIdSimulation);
			SetValue($variableId, $value);
		}

		// ============================================================================================================================
		private function GetFileContentByDate($date) {
			$fileName    = $this->GetFileName($date);
			$fileContent = file_get_contents($fileName);
			$fileLines   = explode(chr(13), $fileContent);
			
			return $fileLines;
		}

		// ============================================================================================================================
		private function GetLastTime() {
			$variableIdLastTime = IPS_GetObjectIDByIdent(IPSLIGHT_SIMULATION_VARTIME, $this->categoryIdSimulation);
			$time = GetValue($variableIdLastTime);
			
			return $time;
		}

		// ============================================================================================================================
		private function GetLastDate() {
			$variableIdFileDate = IPS_GetObjectIDByIdent(IPSLIGHT_SIMULATION_VARDATE, $this->categoryIdSimulation);
			$date = GetValue($variableIdFileDate);
			
			return $date;
		}

		// ============================================================================================================================
		private function GetFileDate($useNextDayFile=false) {
			$variableIdMode = IPS_GetVariableIdByName(IPSLIGHT_SIMULATION_VARMODE, $this->categoryIdSimulation);
			$mode = GetValue($variableIdMode);
			switch($mode) {
				case IPSLIGHT_SIMULATION_MODEDAYS:
					$variableIdDays = IPS_GetVariableIdByName(IPSLIGHT_SIMULATION_VARDAYS, $this->categoryIdSimulation);
					$days           = GetValue($variableIdDays);
					if ($useNextDayFile) {
						$days = $days - 1;
					}
					$fileDate   = date(IPSLIGHT_SIMULATION_DATEFMT, time()-$days*24*60*60);
					break;
				case IPSLIGHT_SIMULATION_MODEUSR1:
					$fileDate = '20000101';
					break;
				case IPSLIGHT_SIMULATION_MODEUSR2:
					$fileDate = '20000102';
					break;
				default:
					IPSLogger_Err(__file__,'Unknown Mode '.$mode);
					exit;
			}
			return $fileDate;
		}

		// ============================================================================================================================
		private Function GetDateTimeFromString($timeString, $useNextDay) {
			if ($timeString===null) {
				return null;
			}
			$time = time();
			if ($useNextDay) {
				$time = time() + 60*60*24;
			}
			//012345678901234
			//YYYYMMDD HHMISS
			$dateTime = mktime(substr($timeString,0,2),
			                   substr($timeString,2,2),
			                   substr($timeString,4,2),
			                   date('m', $time),
			                   date('d', $time),
			                   date('Y', $time)
			                   );
			return $dateTime;
		}
		
		
		
		// ============================================================================================================================
		private function GetNextDateTime($lastTime, $fileDate, $useNextDay=false) {
			$nextTime       = null;
			$fileLines      = $this->GetFileContentByDate($fileDate);

			IPSLogger_Dbg(__file__, 'Seach next Simulation DateTime for '.$lastTime.' in FileDate '.$fileDate);
			$configLights = IPSLight_GetLightConfiguration();
			foreach ($fileLines as $idx=>$line) {
				$switchData    = explode(',',$line);
				if (count($switchData) >= 2) {
					$switchTime    = $switchData[0];
					$switchName    = $switchData[1];
					$toBeSimulated = false;
					if (array_key_exists(IPSLIGHT_SIMULATION, $configLights[$switchName])) {
						$toBeSimulated = $configLights[$switchName][IPSLIGHT_SIMULATION];
					}
					if ($switchTime > $lastTime and $toBeSimulated==true) {
						$nextTime = $switchTime;
						break;
					}
				}
			}

			$nextTime = $this->GetDateTimeFromString($nextTime, $useNextDay);

			return $nextTime;
		}

		// ============================================================================================================================
		private function GetLightsToSwitch() {
			$result         = array();
			$lastTime       = $this->GetLastTime();
			$lastDate       = $this->GetLastDate();
			$fileLines      = $this->GetFileContentByDate($lastDate);
			$configLights   = IPSLight_GetLightConfiguration();
			
			foreach ($fileLines as $idx=>$line) {
				$switchData    = explode(',',$line);
				if (count($switchData) >= 2) {
					$switchTime    = $switchData[0];
					$switchName    = $switchData[1];
					$toBeSimulated = false;
					if (array_key_exists(IPSLIGHT_SIMULATION, $configLights[$switchName])) {
						$toBeSimulated = $configLights[$switchName][IPSLIGHT_SIMULATION];
					}
					if ($switchTime==$lastTime and $toBeSimulated==true) {
						echo "Found Light ".$switchName." with $lastTime".PHP_EOL;
						$result[] = $line;
					}
				}
			}
			return $result;
		}
		
		// ============================================================================================================================
		private function StartSimulationTimer($enabled) {
			$scriptId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSLight.IPSLight_ActionScript');
			$timerId  = @IPS_GetObjectIDByIdent('Simulation', $scriptId);
			if ($enabled) {
				if ($timerId === false) {
					$timerId = IPS_CreateEvent(1 /*Cyclic Event*/);
					IPS_SetParent($timerId, $scriptId);
					IPS_SetName($timerId, 'Simulation');
					IPS_SetIdent($timerId, 'Simulation');
					IPS_SetEventCyclic($timerId, 1 /*Once*/, 1,0,0,0,0);
				}
				$variableIdLastDate = IPS_GetObjectIDByIdent(IPSLIGHT_SIMULATION_VARDATE, $this->categoryIdSimulation);

				$nextDateTime = $this->GetNextDateTime($this->GetLastTime(), $this->GetFileDate(), false);
				SetValue($variableIdLastDate, $this->GetFileDate());
				if ($nextDateTime===null) {
					$nextDateTime = $this->GetNextDateTime('000000', $this->GetFileDate(true), true);
				SetValue($variableIdLastDate, $this->GetFileDate(true));
				}
				if ($nextDateTime===null) {
					IPS_SetEventActive($timerId, false);
					//IPS_DeleteEvent($timerId);
					IPSLogger_Err(__file__, 'Next DateTime could NOT be found for new Simulation Timer');
					return false;
				} else {
					IPS_SetEventCyclicDateBounds($timerId, $nextDateTime, 0);
					IPS_SetEventCyclicTimeBounds($timerId, $nextDateTime, 0);
					IPS_SetEventActive($timerId, true);
					$variableIdLastTime = IPS_GetObjectIDByIdent(IPSLIGHT_SIMULATION_VARTIME, $this->categoryIdSimulation);
					SetValue($variableIdLastTime, date(IPSLIGHT_SIMULATION_TIMEFMT, $nextDateTime));
				}
			} else {
				if ($timerId !== false) {
					IPS_SetEventActive($timerId, false);
					//IPS_DeleteEvent($timerId);
				}
			}
			return true;
		}

		// ============================================================================================================================
		public function ExecuteTimer() {
			IPSLogger_Dbg(__file__, 'Execute Simulation Timer');
			$lightManager = new IPSLight_Manager();

			$lightsToSwitch = $this->GetLightsToSwitch();
			foreach ($lightsToSwitch as $idx=>$switchData) {
				$switchData = explode(',',$switchData);
				$switchName = $switchData[1];
				switch ($switchData[2]) {
					case 'On':  $switchValue = true;  break; 
					case 'Off': $switchValue = false; break; 
					default: IPSLogger_Dbg(__file__, 'Found unknown SwitchStatus in Simulation File'); return; 
				}
				$switchLevel   = $switchData[3];
				$switchColor   = $switchData[4];
				$switchStateId = @IPS_GetVariableIdByName($switchName, $this->categoryIdSwitches);
				$switchLevelId = @IPS_GetVariableIdByName($switchName.IPSLIGHT_DEVICE_LEVEL, $this->categoryIdSwitches);
				$switchColorId = @IPS_GetVariableIdByName($switchName.IPSLIGHT_DEVICE_COLOR, $this->categoryIdSwitches);
				if ($switchStateId===false) {
					IPSLogger_Dbg(__file__, 'Found unknown switchStateId in Simulation File'); 
					return; 
				}
				$configLights = IPSLight_GetLightConfiguration();
				$lightType    = $configLights[$switchName][IPSLIGHT_TYPE];
				if ($lightType==IPSLIGHT_TYPE_SWITCH) {
					$lightManager->SetSwitch($switchStateId, $switchValue);
				} elseif ($lightType==IPSLIGHT_TYPE_DIMMER) {
					$lightManager->SetDimmer($switchStateId, $switchValue);
					$lightManager->SetDimmer($switchLevelId, $switchLevel);
				} elseif ($lightType==IPSLIGHT_TYPE_RGB) {
					$lightManager->SetRGB($switchStateId, $switchValue);
					$lightManager->SetRGB($switchLevelId, $switchLevel);
					$lightManager->SetRGB($switchColorId, $switchColor);
				} else {
					trigger_error('Unknown LightType '.$lightType.' for Light '.$switchName);
				}
			}
			$this->StartSimulationTimer(true);

		}
	}

	/** @}*/
?>