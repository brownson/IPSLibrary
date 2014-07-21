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
	 * @file          IPSShadowing_Device.class.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 21.03.2012<br/>
	 *   Version 2.50.2, 29.12.2012  Added Reset of Flags after Change of Day/Night<br/>
	 *
	 * Funktionen zum Bewegen der Beschattung 
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                  "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSInstaller.inc.php",               "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSComponent.class.php",             "IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSShadowing_Constants.inc.php",     "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Configuration.inc.php", "IPSLibrary::config::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Custom.inc.php",        "IPSLibrary::config::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Logging.inc.php",       "IPSLibrary::app::modules::IPSShadowing");

	/**
	 * @class IPSShadowing_Device
	 *
	 * Definiert ein IPSShadowing_Device Objekt
	 *
	 * @author Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 01.04.2012<br/>
	 */
	class IPSShadowing_Device {

		/**
		 * @private
		 * ID des Shadowing Device
		 */
		private $deviceId;

		/**
		 * @public
		 *
		 * Initialisierung des IPSShadowing_Device Objektes
		 *
		 * @param integer $deviceId Instance ID
		 */
		public function __construct($deviceId) {
			$this->deviceId = IPSUtil_ObjectIDByPath($deviceId);
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function GetPropertyValue($propertyName) {
			$deviceConfig = get_ShadowingConfiguration();
			$deviceName   = IPS_GetName($this->deviceId);
		
			$propertyValue = $deviceConfig[$deviceName][$propertyName];
			return $propertyValue;
		}
		
		// ----------------------------------------------------------------------------------------------------------------------------
		private function GetVariableValue($variableIdent) {
			$variableId = IPS_GetObjectIDByIdent($variableIdent, $this->deviceId);
			if ($variableId === false) {
				throw new Exception('Variable '.$variableIdent.' could NOT be found for DeviceId='.$this->deviceId);
			}
			return GetValue($variableId);
		}
		
		// ----------------------------------------------------------------------------------------------------------------------------
		private function SetVariableValue($variableIdent, $value) {
			$variableId = IPS_GetObjectIDByIdent($variableIdent, $this->deviceId);
			if ($variableId === false) {
				throw new Exception('Variable '.$variableIdent.' could NOT be found for DeviceId='.$this->deviceId);
			}
			SetValue($variableId, $value);
		}
		
		// ----------------------------------------------------------------------------------------------------------------------------
		private function SetStatus() {
			if ($this->GetVariableValue(c_Control_StepsToDo)<>"") {
				return;
			}
	
			$Position	= $this->GetVariableValue(c_Control_Position);
			$MovementId = $this->GetVariableValue(c_Control_Movement);
			if (!$this->GetVariableValue(c_Control_Automatic)) {
				if ($MovementId<=c_MovementId_Space and $MovementId>=c_MovementId_Closed) {
					$Status = 'Manuell';
				} elseif ($Position<=10) {
					$Status = 'Manuell';
				} else {
					$Status = "Manuell / $Position%";
				}
			} elseif ($this->GetVariableValue(c_Control_ManualChange)) {
				if ($MovementId<=c_MovementId_Space and $MovementId>=c_MovementId_Closed) {
					$Status = 'Autom./Manuell';
				} elseif ($Position<=10) {
					$Status = 'Autom./Manuell';
				} else {
					$Status = "Autom./Manuell $Position%";
				}
			} else {
				$Status = 'Automatik';
			}
			$this->SetVariableValue(c_Control_Display, $Status);
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function MoveByCommand($command) {
			$componentParams = $this->GetPropertyValue(c_Property_Component);

			// Execute Shutter Command
			if (IPSShadowing_BeforeActivateShutter($this->deviceId, $command)) {
				$component       = IPSComponent::CreateObjectByParams($componentParams);
				switch ($command) {
					case c_MovementId_Up:
					case c_MovementId_MovingIn:
						$component->MoveUp();
						break;
					case c_MovementId_Down:
					case c_MovementId_MovingOut:
						$component->MoveDown();
						break;
					case c_MovementId_Stop:
							$component->Stop();
						break;
					default: 
				}
				IPSShadowing_AfterActivateShutter($this->deviceId, $command);
				
			// Abort Processing in case of false result 
			} else {
				SetValue(IPS_GetObjectIDByIdent(c_Control_StartTime, $this->deviceId),-1);
				SetValue(IPS_GetObjectIDByIdent(c_Control_StepsToDo, $this->deviceId),"");
				SetValue(IPS_GetObjectIDByIdent(c_Control_Step, $this->deviceId),-1);
				$command = c_MovementId_Stop;
			}

			if ($this->GetVariableValue(c_Control_Movement) <> $command) {
				$this->SetVariableValue(c_Control_Movement, $command);
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function MoveByStatus() {
			$this->CalcNextSteps();
			$this->SetVariableValue(c_Control_Movement, -1);
			$this->ExecuteNextStep();
			$this->StartRefreshTimer(true);
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function SyncStatus($status) {
			if ($this->GetVariableValue(c_Control_StepsToDo)=="") {
				IPSLogger_Inf(__file__, "Sync State=".$status." from Shutter '".IPS_GetName($this->deviceId));
				$this->SetVariableValue(c_Control_Movement, $status);
				$this->SetVariableValue(c_Control_ManualChange, true);
 			}
			$this->SetStatus();
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function MoveByEvent($Level) {
			IPSLogger_Dbg(__file__, "Received StateChange from Shutter '".IPS_GetName($this->deviceId)."', NewLevel=".round($Level).", CurrentLevel=".$this->GetVariableValue(c_Control_Position));
			if ($this->GetVariableValue(c_Control_Position) <> $Level and
			    $this->GetVariableValue(c_Control_StepsToDo)=="") {
				IPSLogger_Inf(__file__, "Apply StateChange from Shutter '".IPS_GetName($this->deviceId)."', Level=".round($Level));
				$shadowingType = $this->GetPropertyValue(c_Property_ShadowingType);

				// Set Movement Value
				$this->SetVariableValue(c_Control_Movement, $this->GetMovementByPositionSync($Level)); 

				// Set manual Change Flag
				$this->SetVariableValue(c_Control_Position, $Level);
				if (!$this->GetVariableValue(c_Control_ManualChange) and
				    $this->GetVariableValue(c_Control_Automatic)) {
					$this->SetVariableValue(c_Control_ManualChange, true);
				}
				// Set Status
				$this->SetStatus();
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function MoveByProgram($ProgramId, $logMessage, $DimoutOption=null, $TriggeredByTemp=false) {
			$MovementStatus = $this->GetVariableValue(c_Control_Movement);

			$DoBeMoved = $MovementStatus;
			switch ($ProgramId) {
				case c_ProgramId_Manual:
					break;
				case c_ProgramId_Opened:
				case c_ProgramId_OpenedDay:
				case c_ProgramId_OpenedNight:
					$DoBeMoved = c_MovementId_Opened;
					break;
				case c_ProgramId_MovedIn:
					$DoBeMoved = c_MovementId_MovedIn;
					break;
				case c_ProgramId_OpenedOrShadowing:
					if ($MovementStatus<>c_MovementId_Opened and $MovementStatus<>c_MovementId_Shadowing) {$DoBeMoved = c_MovementId_Shadowing;}
					break;
				case c_ProgramId_MovedOut:
				case c_ProgramId_MovedOutTemp:
					$DoBeMoved = c_MovementId_MovedOut;
					break;
				case c_ProgramId_Closed:
					$DoBeMoved = c_MovementId_Closed;
					break;
				case c_ProgramId_90:
					$DoBeMoved = c_MovementId_90;
					break;
				case c_ProgramId_75:
					$DoBeMoved = c_MovementId_75;
					break;
				case c_ProgramId_50:
					$DoBeMoved = c_MovementId_50;
					break;
				case c_ProgramId_25:
					$DoBeMoved = c_MovementId_25;
					break;
				case c_ProgramId_Dimout:
					$DoBeMoved = c_MovementId_Dimout;
					break;
				case c_ProgramId_DimoutOrShadowing:
					if ($MovementStatus<>c_MovementId_Dimout and $MovementStatus<>c_MovementId_Shadowing) {$DoBeMoved = c_MovementId_Shadowing;}
					break;
				case c_ProgramId_DimoutAndShadowing:
					if ($DimoutOption) {
						$DoBeMoved = c_MovementId_Dimout;
					} else {
						$DoBeMoved = c_MovementId_Shadowing;
					}
					break;
				case c_ProgramId_LastPosition:
					$DoBeMoved = $this->GetVariableValue(c_Control_TempLastPos);
					break;
				default:
					IPSLogger_Err(__file__, "Unknown ProgramId $ProgramId, DeviceId=".$this->DeviceId);
					exit;
			}
			
			if ($DoBeMoved<>$MovementStatus) {
				// Check Program Delay
				$lastProgramTime = $this->GetVariableValue(c_Control_ProgramTime);
				$lastProgramMinutes = (time() - $lastProgramTime)/60;
				if (defined('IPSSHADOWING_PROGRAM_DELAY') and $lastProgramMinutes < IPSSHADOWING_PROGRAM_DELAY ) {
					return round(IPSSHADOWING_PROGRAM_DELAY-$lastProgramMinutes);
				}
				if ($TriggeredByTemp and !$this->GetVariableValue(c_Control_TempChange)) {
					$this->SetVariableValue(c_Control_TempChange, true);
					$this->SetVariableValue(c_Control_TempLastPos, $this->GetMovementLastPosition());
				}
				$this->SetVariableValue(c_Control_ProgramTime, time());
				$this->SetVariableValue(c_Control_Movement, $DoBeMoved);
				$this->MoveByStatus();
				IPSShadowing_LogMoveByProgram($this->deviceId, $ProgramId, $logMessage, $TriggeredByTemp);
			}
			return 0;
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function GetMovementLastPosition() {
			$lastMovment       = $this->GetVariableValue(c_Control_Movement);
			$currentPosition   = $this->GetVariableValue(c_Control_Position);

			if ($lastMovment==c_MovementId_Stop) {
				$lastMovment = $this->GetMovementByPositionSync($currentPosition);
			}
			
			return $lastMovment;
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function GetMovementByPosition($Level) {
			$result = c_MovementId_Stop;

			$shadowingType = $this->GetPropertyValue(c_Property_ShadowingType);
			if ($Level <= 5) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees:  $result = c_MovementId_MovedIn; break;
					default:                        $result = c_MovementId_Opened;  break;
				}
			} else if ($Level > 5 and $Level <= 55) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees: 
					case c_ShadowingType_Shutter:   $result = c_MovementId_50;      break;
					default:                        $result = c_MovementId_Opened;  break;
				}
			} else if ($Level > 55 and $Level <= 80) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees: 
					case c_ShadowingType_Shutter:   $result = c_MovementId_75;      break;
					default:                        $result = c_MovementId_Stop;    break;
				}
			} else if ($Level > 80 and $Level <= 95) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees:  $result = c_MovementId_MovedOut;break; 
					case c_ShadowingType_Shutter:   $result = c_MovementId_90;      break;
					default:                        $result = c_MovementId_Shadowing;
				}
			} else if ($Level >= 95) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees:  $result = c_MovementId_MovedOut;break; 
					case c_ShadowingType_Shutter:   $result = c_MovementId_Closed;  break;
					default:                        $result = c_MovementId_Dimout;
				}
			} else {
				                                    $result = c_MovementId_Stop;
			}

			return $result ;
		}
		
		// ----------------------------------------------------------------------------------------------------------------------------
		private function GetMovementByPositionSync($Level) {
			$result = c_MovementId_Stop;

			$shadowingType = $this->GetPropertyValue(c_Property_ShadowingType);
			if ($Level <= 5) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees:  $result = c_MovementId_MovedIn;   break;
					default:                        $result = c_MovementId_Opened;
				}
			} else if ($Level >= 95) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees:  $result = c_MovementId_MovedOut;  break;
					case c_ShadowingType_Shutter:   $result = c_MovementId_Closed;    break;
					default:                        $result = c_MovementId_Shadowing;
				}
			} else if ($Level > 45 and $Level < 55) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees: 
					case c_ShadowingType_Shutter:   $result = c_MovementId_50;        break;
					default:                        $result = c_MovementId_Stop;
				}
			} else if ($Level > 70 and $Level < 80) {
				switch($shadowingType) {
					case c_ShadowingType_Marquees: 
					case c_ShadowingType_Shutter:   $result = c_MovementId_75;        break;
					default:                        $result = c_MovementId_Stop;
				}
			} else if ($Level > 85 and $Level < 95) {
				switch($shadowingType) {
					case c_ShadowingType_Shutter:   $result = c_MovementId_90;        break;
					default:                        $result = c_MovementId_Stop;
				}
			} else {
				                                    $result = c_MovementId_Stop;
			}
			return $result ;
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function MoveByControl($Value) {
			if ($Value==c_MovementId_Space) {
				return;
			} elseif ($this->GetVariableValue(c_Control_Movement)==$Value) {
				return;
			} else {
				if (!$this->GetVariableValue(c_Control_ManualChange)) {
					$this->SetVariableValue(c_Control_ManualChange, true);
				}
				$this->SetVariableValue(c_Control_Movement, $Value);
				$this->MoveByStatus();
				IPSShadowing_LogMoveByControl($this->deviceId);
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function MoveByLevel($level) {
			$movement = $this->GetMovementByPosition($level);
		
			if ($this->GetVariableValue(c_Control_StepsToDo)<>"") {
				if (!$this->GetVariableValue(c_Control_ManualChange)) {
					$this->SetVariableValue(c_Control_ManualChange, true);
				}
				$this->SetVariableValue(c_Control_Movement, c_MovementId_Stop);
				$this->MoveByStatus();
				IPSShadowing_LogMoveByControl($this->deviceId);
			} else if ($this->GetVariableValue(c_Control_Movement)==$movement) {
				$this->SetVariableValue(c_Control_Position, $this->GetVariableValue(c_Control_Position));
				return;
			} else {
				if (!$this->GetVariableValue(c_Control_ManualChange)) {
					$this->SetVariableValue(c_Control_ManualChange, true);
				}
				$this->SetVariableValue(c_Control_Movement, $movement);
				$this->MoveByStatus();
				IPSShadowing_LogMoveByControl($this->deviceId);
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function AddNextStep(&$StepsToDo, $Command, $SecondsToDo, $Display, $SecondsTotal, $PercentagePosition) {
			$Step = count($StepsToDo);
			$StepsToDo[$Step]   = $Command;
			$StepsToDo[$Step+1] = $SecondsToDo;
			$StepsToDo[$Step+2] = $Display;
			$StepsToDo[$Step+3] = $SecondsTotal;
			$StepsToDo[$Step+4] = $PercentagePosition;
		}
	
		// ----------------------------------------------------------------------------------------------------------------------------
		private function CalcNextSteps() {
			$DeviceName   = IPS_GetIdent($this->deviceId);
			$DeviceConfig = get_ShadowingConfiguration();

			$Position     = $this->GetVariableValue(c_Control_Position);
			$ToBeMoved    = $this->GetVariableValue(c_Control_Movement);
			$StepsToDo    = array();

			if ($ToBeMoved==c_MovementId_Opened or $ToBeMoved==c_MovementId_Up) {
				$SecTotal     = $DeviceConfig[$DeviceName][c_Property_TimeOpening];
				$SecNullToPos = $SecTotal*$Position/100;
				$SecPosTo100  = $SecTotal-$SecNullToPos;
				$this->AddNextStep($StepsToDo, c_MovementId_Up, $SecTotal-$SecPosTo100, null,    $SecTotal, $SecPosTo100);
				$this->AddNextStep($StepsToDo, c_MovementId_Stop,   $DeviceConfig[$DeviceName][c_Property_TimePause],  'Offen (Stop)', null, null);
				$this->AddNextStep($StepsToDo, c_MovementId_Opened, 1, null, null , null);

			} elseif ($ToBeMoved==c_MovementId_MovedIn or $ToBeMoved==c_MovementId_MovingIn) {
				$SecTotal     = $DeviceConfig[$DeviceName][c_Property_TimeOpening];
				$SecNullToPos = $SecTotal*$Position/100;
				$SecPosTo100  = $SecTotal-$SecNullToPos;
				$this->AddNextStep($StepsToDo, c_MovementId_MovingIn, $SecTotal-$SecPosTo100, null,    $SecTotal, $SecPosTo100);
				$this->AddNextStep($StepsToDo, c_MovementId_Stop,   $DeviceConfig[$DeviceName][c_Property_TimePause],  'Offen (Stop)', null, null);
				$this->AddNextStep($StepsToDo, c_MovementId_MovedIn, 1, null, null , null);

			} elseif ($ToBeMoved==c_MovementId_Shadowing or $ToBeMoved==c_MovementId_Down or $ToBeMoved==c_MovementId_Dimout or $ToBeMoved==c_MovementId_Closed or $ToBeMoved==c_MovementId_MovingOut or $ToBeMoved==c_MovementId_MovedOut) {
				$SecTotal     = $DeviceConfig[$DeviceName][c_Property_TimeClosing];
				$SecNullToPos = $SecTotal*$Position/100;
				if ($ToBeMoved==c_MovementId_Dimout) {
					$this->AddNextStep($StepsToDo, c_MovementId_Down,   $SecTotal-$SecNullToPos, null, $SecTotal, $SecNullToPos);
					$this->AddNextStep($StepsToDo, c_MovementId_Stop,   $DeviceConfig[$DeviceName][c_Property_TimePause],    'Abdunkelung (Pause)', null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Up,     $DeviceConfig[$DeviceName][c_Property_TimeDimoutUp], 'Abdunkelung (Hoch)', null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Stop,   $DeviceConfig[$DeviceName][c_Property_TimePause],    'Abdunkelung (Pause)', null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Down,   $DeviceConfig[$DeviceName][c_Property_TimeDimoutDown], 'Abdunkelung (Runter)', null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Stop,   $DeviceConfig[$DeviceName][c_Property_TimePause],    'Abdunkelung (Stop)', null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Dimout, 1,  null, null, null);
				} elseif ($ToBeMoved==c_MovementId_Down) {
					$this->AddNextStep($StepsToDo, c_MovementId_Down, $SecTotal-$SecNullToPos, null, $SecTotal, $SecNullToPos);
					$this->AddNextStep($StepsToDo, c_MovementId_Stop, 1, null, null, null);
				} elseif ($ToBeMoved==c_MovementId_MovedOut or $ToBeMoved==c_MovementId_MovingOut) {
					$this->AddNextStep($StepsToDo, c_MovementId_MovingOut, $SecTotal-$SecNullToPos, null, $SecTotal, $SecNullToPos);
					$this->AddNextStep($StepsToDo, c_MovementId_Stop, 1, null, null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_MovedOut, 1, null, null, null);
				} elseif ($ToBeMoved==c_MovementId_Shadowing) {
					$this->AddNextStep($StepsToDo, c_MovementId_Down, $SecTotal-$SecNullToPos, null, $SecTotal, $SecNullToPos);
					$this->AddNextStep($StepsToDo, c_MovementId_Stop, 1, "$Position%", null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Up,    $DeviceConfig[$DeviceName][c_Property_TimeDimoutUp], 'Beschattung (Hoch)', null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Stop, $DeviceConfig[$DeviceName][c_Property_TimePause],  'Beschattung (Stop)', null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Shadowing, 1,  null, null, null);
				} else {
					$this->AddNextStep($StepsToDo, c_MovementId_Down, $SecTotal-$SecNullToPos, null, $SecTotal, $SecNullToPos);
					$this->AddNextStep($StepsToDo, c_MovementId_Stop, 1, "$Position%", null, null);
					$this->AddNextStep($StepsToDo, c_MovementId_Closed, 1,  null, null, null);
				}
			
			} elseif ($ToBeMoved==c_MovementId_90 or$ToBeMoved==c_MovementId_75 or $ToBeMoved==c_MovementId_50 or $ToBeMoved==c_MovementId_25) {
				$SecTotal      = $DeviceConfig[$DeviceName][c_Property_TimeClosing];
				$ShadowingType = $DeviceConfig[$DeviceName][c_Property_ShadowingType];
				$SecNullToPos  = $SecTotal*$Position/100;
				$SecPosTo100   = $SecTotal-$SecNullToPos;
				if ($ToBeMoved==c_MovementId_90) {
					$SecNullToNew = $SecTotal*90/100;
					$Position     = 90;
				} elseif ($ToBeMoved==c_MovementId_75) {
					$SecNullToNew = $SecTotal*75/100;
					$Position     = 75;
				} elseif ($ToBeMoved==c_MovementId_50) {
					$SecNullToNew = $SecTotal*50/100;
					$Position     = 50;
				} else {
					$SecNullToNew = $SecTotal*25/100;
					$Position     = 25;
				}
				if ($SecNullToNew > $SecNullToPos) {
					if ($ShadowingType==c_ShadowingType_Marquees) {
						$this->AddNextStep($StepsToDo, c_MovementId_MovingOut, $SecNullToNew-$SecNullToPos, null, $SecTotal, $SecNullToPos);
					} else {
						$this->AddNextStep($StepsToDo, c_MovementId_Down, $SecNullToNew-$SecNullToPos, null, $SecTotal, $SecNullToPos);
					}
				} elseif ($SecNullToNew < $SecNullToPos) {
					if ($ShadowingType==c_ShadowingType_Marquees) {
						$this->AddNextStep($StepsToDo, c_MovementId_MovingIn, $SecNullToPos-$SecNullToNew, null, $SecTotal, $SecPosTo100);
					} else {
						$this->AddNextStep($StepsToDo, c_MovementId_Up, $SecNullToPos-$SecNullToNew, null, $SecTotal, $SecPosTo100);
					}
				} else {
				}
				$this->AddNextStep($StepsToDo, c_MovementId_Stop, 1, "$Position%", null, null);
				$this->AddNextStep($StepsToDo, $ToBeMoved,        1, null, null, null);

			} elseif ($ToBeMoved==c_MovementId_Stop) {
				$this->AddNextStep($StepsToDo, c_MovementId_Stop, 1, null, null, null);

			} else {
				throw new Exception ("Unknown MovementId $ToBeMoved, DeviceId=".$this->deviceId);
				exit;
			}

			$this->SetVariableValue(c_Control_StepsToDo, implode('|', $StepsToDo));
			$this->SetVariableValue(c_Control_Step, -5);
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function ExecuteNextStep() {
			$DeviceName    = IPS_GetIdent($this->deviceId);
			$Step          = $this->GetVariableValue(c_Control_Step)+5;
			$NextStepsToDo = $this->GetVariableValue(c_Control_StepsToDo);
			$NextStepsToDo = Explode('|', $NextStepsToDo);

			if ($Step < count($NextStepsToDo)) {
				$Command = $NextStepsToDo[$Step];
				$Time    = $NextStepsToDo[$Step+1];
				IPSLogger_Trc(__file__, "Shadowing for Device '$DeviceName', Step $Step, Command=$Command, Time=$Time");
				$this->MoveByCommand($Command);

				$this->SetVariableValue(c_Control_Step,      $Step);
				$this->SetVariableValue(c_Control_StartTime, time());
			} else {
				IPSLogger_Dbg(__file__, "Finished all Steps for Device '$DeviceName'");
				$this->SetVariableValue(c_Control_Step,      -1);
				$this->SetVariableValue(c_Control_StepsToDo, "");
				$this->SetVariableValue(c_Control_StartTime, -1);
				$this->StartRefreshTimer(false);
				$this->SetStatus();
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function Refresh() {
			$NextStepsToDo = Explode('|', $this->GetVariableValue(c_Control_StepsToDo));
			$StepCount     = count($NextStepsToDo);
			$Step          = $this->GetVariableValue(c_Control_Step);
			if ($StepCount >= ($Step+4) and $Step>=0) {
				$StartTime     = $this->GetVariableValue(c_Control_StartTime);
				$SecsDone      = time()-$StartTime;
				$SecsToDo      = $NextStepsToDo[$Step+1];
				$Display       = $NextStepsToDo[$Step+2];
				$SecsTotal     = $NextStepsToDo[$Step+3];
				$SecStepBegin  = $NextStepsToDo[$Step+4];
				$Command       = $NextStepsToDo[$Step];

				if ($SecsTotal <> null) {
					//  SecTotal   ... 100%
					//  Begin+Done ...   x%
					$Position       = round(($SecStepBegin+$SecsDone)*100/$SecsTotal);
					if ($Command==c_MovementId_Up or $Command==c_MovementId_MovingIn) {
						$Position = 100-$Position;
					}
					if ($Position>100) {$Position=100;}
					if ($Position<0)   {$Position=0;}
					$this->SetVariableValue(c_Control_Position, $Position);
					$SecsOpen      = $SecsToDo-$SecsDone;
					if ($SecsOpen < 0) {$SecsOpen=0;}
					$Display = "$Position% ($SecsOpen Sek)";
				}

				if ($Display!=null) {
					$this->SetVariableValue(c_Control_Display, $Display);
				}

				if (function_exists('IPSShadowing_Refresh')) {
					IPSShadowing_Refresh($this->deviceId, $StepCount, $Step, $Command, $SecsToDo, $SecsDone);
				}
				
				if ($SecsDone >= $SecsToDo) {
					$this->ExecuteNextStep();
				}
				return true;
			} else {
				return false;
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		private function StartRefreshTimer($Value) {
			if ($Value) {
				$Name    = 'Refresh';
				$refreshTimerScriptId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSShadowing.IPSShadowing_RefreshTimer');

				$TimerId = @IPS_GetEventIDByName($Name, $refreshTimerScriptId);
				if ($TimerId === false) {
					$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
					IPS_SetName($TimerId, $Name);
					IPS_SetParent($TimerId, $refreshTimerScriptId);
					if (!IPS_SetEventCyclic($TimerId, 2 /*Daily*/, 1 /*Int*/,0 /*Days*/,0 /*DayInt*/,1 /*TimeType Sec*/,1 /*Sec*/)) {
						throw new Exception ("IPS_SetEventCyclic failed for Refresh Timer!!!");
					}
				}
				IPS_SetEventActive($TimerId, true);
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function ChangeSetting($controlId, $value) {
			if (GetValue($controlId)<>$value) {
				if (IPS_GetIdent($controlId)==c_Control_Automatic) {
					if (GetValue(IPS_GetObjectIDByIdent(c_Control_TempChange, $this->deviceId))) {
						SetValue(IPS_GetObjectIDByIdent(c_Control_TempChange, $this->deviceId), false);
					}
				} else {
				}
				if (GetValue(IPS_GetObjectIDByIdent(c_Control_ManualChange, $this->deviceId))) {
					SetValue(IPS_GetObjectIDByIdent(c_Control_ManualChange, $this->deviceId), false);
				}
				SetValue($controlId, $value);
				IPSShadowing_LogChange($this->deviceId, $value, $controlId);
				$this->SetStatus();
			}
		}

		// ----------------------------------------------------------------------------------------------------------------------------
		public function CheckPrograms($profileManager) {
			$deviceName        = IPS_GetIdent($this->deviceId);
			$profileIdTemp     = GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileTemp, $this->deviceId));
			$profileIdSun      = GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileSun, $this->deviceId));
			$profileIdBgnOfDay = GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileBgnOfDay, $this->deviceId));
			$profileIdEndOfDay = GetValue(IPS_GetObjectIDByIdent(c_Control_ProfileEndOfDay, $this->deviceId));
			$profileIdWeather  = null;
			$programPresent    = GetValue(IPS_GetObjectIDByIdent(c_Control_ProgramPresent, $this->deviceId));
			$programTemp       = GetValue(IPS_GetObjectIDByIdent(c_Control_ProgramTemp, $this->deviceId));
			$programDay        = GetValue(IPS_GetObjectIDByIdent(c_Control_ProgramDay, $this->deviceId));
			$programNight      = GetValue(IPS_GetObjectIDByIdent(c_Control_ProgramNight, $this->deviceId));
			$programWeather    = null;
			$automaticActive   = GetValue(IPS_GetObjectIDByIdent(c_Control_Automatic, $this->deviceId));
			$tempIndoorPath    = $this->GetPropertyValue(c_Property_TempSensorIndoor);

			$controlId = @IPS_GetObjectIDByIdent(c_Control_ProfileWeather, $this->deviceId);
			if ($controlId!==false) {
				$profileIdWeather  = GetValue($controlId);
				$programWeather    = GetValue(IPS_GetObjectIDByIdent(c_Control_ProgramWeather, $this->deviceId));
			}

			$isDay               = $profileManager->IsDay($profileIdBgnOfDay, $profileIdEndOfDay);
			$isDayNightChange    = $profileManager->IsDayNightChange($profileIdBgnOfDay, $profileIdEndOfDay);
			$closeByTemp         = $profileManager->CloseByTemp($profileIdSun, $profileIdTemp, $tempIndoorPath);
			$shadowingByTemp     = $profileManager->ShadowingByTemp($profileIdSun, $profileIdTemp, $tempIndoorPath);
			$openByTemp          = $profileManager->OpenByTemp($profileIdSun, $profileIdTemp, $tempIndoorPath);
			$activationByWeather = $profileManager->ActivationByWeather($profileIdWeather);
			$programInfo         = '';
			$programDelay        = false;


			// Reset Manual Change Flag
			if ($isDayNightChange) {
				if (GetValue(IPS_GetObjectIDByIdent(c_Control_ManualChange, $this->deviceId))) {
					IPSLogger_Dbg(__file__, "Reset ManualChange Flag for Device '$deviceName'");
					SetValue(IPS_GetObjectIDByIdent(c_Control_ManualChange, $this->deviceId), false);
				}
				if (GetValue(IPS_GetObjectIDByIdent(c_Control_TempChange, $this->deviceId))) {
					IPSLogger_Dbg(__file__, "Reset TempChange Flag for Device '$deviceName'");
					SetValue(IPS_GetObjectIDByIdent(c_Control_TempChange, $this->deviceId), false);
				}
			}

			$changeByTemp      = GetValue(IPS_GetObjectIDByIdent(c_Control_TempChange, $this->deviceId));
			$changeByUser      = GetValue(IPS_GetObjectIDByIdent(c_Control_ManualChange, $this->deviceId));
			
			$func_reflection = new ReflectionFunction('IPSShadowing_ProgramCustom');
			$paramCountCustom = $func_reflection->getNumberOfParameters();

			// Check all Programs
			// --------------------------------------------------------------------------------
			// Automatic Off ...
			if (!$automaticActive) {
				$programInfo = 'Automatic Off';

			// Activation by Wind/Rain
			} elseif ($activationByWeather and $programWeather<>c_ProgramId_Manual) {
				$programInfo  = 'Wetterprogramm';
				$programDelay = $this->MoveByProgram($programWeather, 'Wetterprogramm');

			// Custom
			} elseif (    ($paramCountCustom == 3 and IPSShadowing_ProgramCustom($this->deviceId, $isDay, $programInfo))
			           or ($paramCountCustom == 2 and IPSShadowing_ProgramCustom($this->deviceId, $isDay))) {
				if ($programInfo=='') {$programInfo = 'CustomProgram';}
				// Action done in Custom Procedure

			// Manual Change ...
			} elseif ($changeByUser) {
				$programInfo = 'Manuelle Änderung';
        
			// Present ...
			} elseif ($profileManager->GetPresent() and $programPresent==c_ProgramId_OpenedDay and $isDay) {
				$programInfo  = 'Anwesenheit (Tag)';
				$programDelay = $this->MoveByProgram($programPresent, 'Anwesenheitsprogramm');
			} elseif ($profileManager->GetPresent() and $programPresent==c_ProgramId_OpenedNight and !$isDay) {
				$programInfo  = 'Anwesenheit (Nacht)';
				$programDelay = $this->MoveByProgram($programPresent, 'Anwesenheitsprogramm');
			} elseif ($profileManager->GetPresent() and $programPresent==c_ProgramId_Opened) {
				$programInfo  = 'Anwesenheit';
				$programDelay = $this->MoveByProgram($programPresent, 'Anwesenheitsprogramm');
			} elseif ($profileManager->GetPresent() and $programPresent==c_ProgramId_MovedOutTemp and $isDay and $closeByTemp) {
				$programInfo  = 'Anwesenheit (Temperatur)';
				$programDelay = $this->MoveByProgram($programPresent, 'Anwesenheitsprogramm (Beschattung bei Temp und Anwesenheit)');

			// Temperature/Sun
			} elseif ($isDay and ($closeByTemp or $shadowingByTemp) and $programTemp<>c_ProgramId_Manual) {
				if ($closeByTemp) {
					$programInfo  = 'Temperatur';
					$programDelay = $this->MoveByProgram($programTemp, 'Temperaturprogramm', true/*DimoutOption*/, true/*TriggeredByTemp*/);
				} elseif ($changeByTemp) {
					$programInfo  = 'Temperatur (Warte Öffnen)';
				} elseif ($shadowingByTemp) {
					$programInfo  = 'Temperatur (Beschattung)';
					$programDelay = $this->MoveByProgram($programTemp, 'Temperaturprogramm (Beschattung)', false/*DimoutOption*/, true/*TriggeredByTemp*/);
				} else {
					$programInfo  = 'Temperatur (Error)';
				}

			// Day
			} elseif ($isDay) {
				if (!$openByTemp and $changeByTemp) {
					$programInfo = 'Tag (Warte Öffnen)';
				} elseif ($openByTemp and $changeByTemp) {
					SetValue(IPS_GetObjectIDByIdent(c_Control_TempChange, $this->deviceId), false);
					if ($programDay<>c_ProgramId_Manual) {
						$programInfo  = 'Temperatur Reset (Tag)';
						$programDelay = $this->MoveByProgram($programDay, 'Temperatur Reset (Tag)');
					} else {
						$programInfo  = 'Temperatur Reset (LastPosition)';
						$programDelay = $this->MoveByProgram(c_ProgramId_LastPosition, 'Temperatur Reset (LastPosition)');
					}
				} else {
					$programInfo  = 'Tagesprogramm';
					$programDelay = $this->MoveByProgram($programDay, 'Tagesprogramm');
				}
				
			// Night
			} else {
				$programInfo  = 'Nachtprogramm';
				$programDelay = $this->MoveByProgram($programNight, '"Nachtprogramm"');
			}
			
			// Update ProfileInfos
			if ($programDelay > 0) 
				$programInfo = 'Warte '.$programDelay.' Min '.$programInfo;
			$profileInfo = $profileManager->GetProfileInfo($profileIdBgnOfDay, $profileIdEndOfDay, $profileIdTemp, $tempIndoorPath);
			$deviceName = IPSShadowing_GetDeviceName($this->deviceId);
			echo "$deviceName -> $programInfo, $profileInfo \n";
			SetValue(IPS_GetObjectIDByIdent(c_Control_ProfileInfo, $this->deviceId), $programInfo.', '.$profileInfo);
		}
	}


	/** @}*/
?>