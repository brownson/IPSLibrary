<?
	/**
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

	/**@defgroup audiomax AudioMax Multiroom Steuerung
	 * @ingroup hardware
	 * @{
	 *
	 * Klasse zur Kommunikation mit dem Audiomax Device 
	 *
	 * @file          AudioMax_Server.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",              "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("AudioMax_Constants.inc.php",     "IPSLibrary::app::hardware::AudioMax");
	IPSUtils_Include ("AudioMax_Configuration.inc.php", "IPSLibrary::config::hardware::AudioMax");
	IPSUtils_Include ("AudioMax_Room.class.php",         "IPSLibrary::app::hardware::AudioMax");

   /**
    * @class AudioMax_Server
    *
    * Definiert ein AudioMax_Server Objekt
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	class AudioMax_Server {

		/**
		 * @private
		 * ID des AudioMax Server
		 */
		private $instanceId;

		/**
		 * @private
		 * Debugging of AudioMax Server Enabled/Disabled
		 */
		private $debugEnabled;

		/** 
		 * @public
		 *
		 * Initializes the AudioMax Server
		 *
		 * @param integer $instanceId - ID des AudioMax Server.
		 */
		public function __construct($instanceId) {
			$this->instanceId = $instanceId;
			$this->debugEnabled = GetValue(IPS_GetObjectIDByIdent(AM_VAR_MODESERVERDEBUG, $this->instanceId));
		}

		/**
		 * @private
		 *
		 * Protokollierung einer Meldung im AudioMax Log
		 *
		 * @param string $logType Type der Logging Meldung 
		 * @param string $msg Meldung 
		 */
		private function Log ($logType, $msg) {
			if ($this->debugEnabled) {
				IPSLogger_WriteFile("", 'AudioMax.log', date('Y-m-d H:i:s').'  '.$logType.' - '.$msg, null);
			}
		}
		
		/**
		 * @private
		 *
		 * Protokollierung einer Error Meldung
		 *
		 * @param string $msg Meldung 
		 */
		private function LogErr($msg) {
			IPSLogger_Err(__file__, $msg);
			$this->Log('Inf', $msg);
			$variableId  = IPS_GetObjectIDByIdent(AM_VAR_LASTERROR, $this->instanceId);
			SetValue($variableId, $errorText);
		}
		
		/**
		 * @private
		 *
		 * Protokollierung einer Info Meldung
		 *
		 * @param string $msg Meldung 
		 */
		private function LogInf($msg) {
			IPSLogger_Inf(__file__, $msg);
			$this->Log('Inf', $msg);
		}
		
		/**
		 * @private
		 *
		 * Protokollierung einer Debug Meldung
		 *
		 * @param string $msg Meldung 
		 */
		private function LogDbg($msg) {
			IPSLogger_Dbg(__file__, $msg);
			$this->Log('Dbg', $msg);
		}

		/**
		 * @private
		 *
		 * Protokollierung einer Kommunikations Meldung
		 *
		 * @param string $msg Meldung 
		 */
		private function LogCom($msg) {
			IPSLogger_Com(__file__, $msg);
			$this->Log('Com', $msg);
		}
		
		/**
		 * @private
		 *
		 * Protokollierung einer Trace Meldung
		 *
		 * @param string $msg Meldung 
		 */
		private function LogTrc($msg) {
			IPSLogger_Trc(__file__, $msg);
			$this->Log('Trc', $msg);
		}
		
		/**
		 * @public
		 *
		 * Setz den Status der Verbindung auf Active oder Inactiv.
		 * Im inaktiven Zustand wird der IO Port deaktiviert und alle ausgehenden Meldungen ignoriert.
		 *
		 * @param boolean $value Status der Verbindung.
		 */
		public function SetConnection($value) {
			$variableId  = IPS_GetObjectIDByIdent(AM_VAR_CONNECTION, $this->instanceId);
			SetValue($variableId, $value);

			$this->LogInf('Set AudioMax Connection Status to '.($value ? 'Connection Active' : 'Connection Inactiv'));

			$comPortId = GetValue(IPS_GetObjectIDByIdent('PORT_ID', $this->instanceId));
			COMPort_SetOpen($comPortId, $value);
			IPS_ApplyChanges($comPortId);
			if ($value) {
				$this->SendData(AM_TYP_SET, AM_CMD_KEEPALIVE, null, null, '0');
			}
		}

		/**
		 * @private
		 *
		 * Ermittelt ob die Instanzen der Räume installiert sind
		 *
		 * @return boolean Räume installiert
		 */
		private function GetAudioMaxRoomVariablesEnabled() {
			$roomIds = GetValue(IPS_GetObjectIDByIdent(AM_VAR_ROOMIDS, $this->instanceId));
			return ($roomIds <> '');
		}

		/**
		 * @private
		 *
		 * Liefert ein AudioMaxRoom Objekt für eine Raum Nummer, sind keine Räume vorhanden
		 * liefert die Funktion false.
		 *
		 * @param integer $roomId Nummer des Raumes (1-4).
		 * @return AudioMax_Room AudioMax Room Object
		 */
		private function GetAudioMaxRoom($roomId) {
			$roomIds = GetValue(IPS_GetObjectIDByIdent(AM_VAR_ROOMIDS, $this->instanceId));
			if ($roomIds=="") {
				return false;
			}
			$roomIds        = explode(',',  $roomIds);
			$roomInstanceId = false;
			$audioMaxRoom   = false;
			if (array_key_exists($roomId, $roomIds)) {
				$roomInstanceId = (int)$roomIds[$roomId];
				$audioMaxRoom = new AudioMax_Room($roomInstanceId);
			}

			return $audioMaxRoom;
		}


		/**
		 * @private
		 *
		 * Validieren der Daten und Variablen setzen
		 *
		 * @param string $type Kommando Type
		 * @param string $command Kommando
		 * @param integer $roomId Nummer des Raumes
		 * @param string $function Funktion
		 * @param string $value Wert
		 */
		private function ValidateAndSetValue ($type, $command, $roomId, $function, $value) {
			if ($this->ValidateData($type, $command, $roomId, $function, $value)) {
				$this->SetValue($type, $command, $roomId, $function, $value);
			}
		}

		/**
		 * @private
		 *
		 * Setzt den ensprechenden Wert einer Variable auf den Wert der Message
		 *
		 * @param string $type Kommando Type
		 * @param string $command Kommando
		 * @param integer $roomId Nummer des Raumes
		 * @param string $function Funktion
		 * @param string $value Wert
		 */
		private function SetValue ($type, $command, $roomId, $function, $value) {
		   if ($type==AM_TYP_GET) {
		      return;
			}
		   if ($command==AM_CMD_POWER or $command==AM_CMD_ROOM) {
				if ($value===AM_VAL_BOOLEAN_TRUE)  $value=true;
				if ($value===AM_VAL_BOOLEAN_FALSE) $value=false;
			}
			$modification = false;
			switch ($command) {
				case AM_CMD_POWER:
					$variableId  = IPS_GetObjectIDByIdent(AM_VAR_MAINPOWER, $this->instanceId);
					if (GetValue($variableId)<>$value) {
						SetValue($variableId, $value);
						$modification = true;
					}
					break;
				case AM_CMD_TEXT:
					break;
				case AM_CMD_MODE:
					if ($function==AM_MOD_SERVERDEBUG) {
						$variableId  = IPS_GetObjectIDByIdent(AM_VAR_MODESERVERDEBUG, $this->instanceId);
						if (GetValue($variableId)<>$value) {
					 		SetValue($variableId, $value);
							$modification = true;
						}
					}
					if ($function==AM_MOD_POWERREQUEST) {
						$variableId  = IPS_GetObjectIDByIdent(AM_VAR_MODEPOWERREQUEST, $this->instanceId);
						if (GetValue($variableId)<>$value) {
							SetValue($variableId, $value);
							$modification = true;
						}
					}
					if ($function==AM_MOD_ACKNOWLEDGE) {
						$variableId  = IPS_GetObjectIDByIdent(AM_VAR_MODEACKNOWLEDGE, $this->instanceId);
						if (GetValue($variableId)<>$value) {
							SetValue($variableId, $value);
							$modification = true;
						}
					}
					break;
				case AM_CMD_ROOM:
					$room = $this->GetAudioMaxRoom($roomId);
					if ($room===false) {
						$modification = true;
						break;
					}
					if ($room->GetValue($command, '')<>$value) {
						$room->SetValue($command, '', $value);
						$modification = true;
					}
					break;
				case AM_CMD_AUDIO:
					if ($function==AM_FNC_VOLUME)      $value=AM_VAL_VOLUME_MAX-$value;
					$room = $this->GetAudioMaxRoom($roomId);
					if ($room===false) {
						$modification = true;
						break;
					}
					if ($room->GetValue($command, $function)<>$value) {
						$room->SetValue($command, $function, $value);
						$modification = true;
					}
					break;
				case AM_CMD_KEEPALIVE:
					$modification = true;
					break;
				default:
					$this->LogErr('Unknown Command '.$command);
			}
			if ($modification) {
				SetValue(IPS_GetObjectIDByIdent(AM_VAR_LASTCOMMAND, $this->instanceId), $this->BuildMsg($type, $command, $roomId, $function, $value, false));
				SetValue(IPS_GetObjectIDByIdent(AM_VAR_LASTERROR,   $this->instanceId), "");
			}
		}

		/**
		 * @private
		 *
		 * Lesen der AudioMax Werte aus den Instanz Variablen
		 *
		 * @param string $type Kommando Type
		 * @param string $command Kommando
		 * @param integer $roomId Nummer des Raumes
		 * @param string $function Funktion
		 * @return string Wert
		 */
		private function GetValue ($type, $command, $roomId, $function) {
			$result = '';
		   if ($type==AM_TYP_GET) {
				switch ($command) {
					case AM_CMD_POWER:
						$result = GetValue(IPS_GetObjectIDByIdent(AM_VAR_MAINPOWER, $this->instanceId));
						break;
					case AM_CMD_TEXT:
					case AM_CMD_MODE:
					case AM_CMD_KEEPALIVE:
						break;
					case AM_CMD_ROOM:
						$room = $this->GetAudioMaxRoom($roomId);
						if ($room!==false) {
							$result = $room->GetValue($command, '');
						}
						break;
					case AM_CMD_AUDIO:
						$room = $this->GetAudioMaxRoom($roomId);
						if ($room!==false) {
							$result = $room->GetValue($command, $function);
						}
						break;
					default:
						$this->LogErr('Unknown Command '.$command);
				}
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Setzt die AudioMax Variablen Werte auf den DEFAULT Wert zurück.
		 */
		public function Reset () {
			$this->LogDbg("Execute AudioMax Reset ...");
			$this->SetValue(AM_TYP_SET, AM_CMD_POWER, null, null, AM_VAL_POWER_DEFAULT);

			$roomCount  = GetValue(IPS_GetObjectIDByIdent(AM_VAR_ROOMCOUNT, $this->instanceId));
			for ($roomId=0;$roomId<$roomCount;$roomId++) {
				$this->SetValue(AM_TYP_SET, AM_CMD_ROOM,  $roomId, null,               AM_VAL_POWER_DEFAULT);
				$this->SetValue(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_TREBLE,      AM_VAL_TREBLE_DEFAULT);
				$this->SetValue(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_MIDDLE,      AM_VAL_MIDDLE_DEFAULT);
				$this->SetValue(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_BASS,        AM_VAL_BASS_DEFAULT);
				$this->SetValue(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_VOLUME,      AM_VAL_VOLUME_DEFAULT);
				$this->SetValue(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_MUTE,        AM_VAL_MUTE_DEFAULT);
				$this->SetValue(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_BALANCE,     AM_VAL_BALANCE_DEFAULT);
				$this->SetValue(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTSELECT, AM_VAL_INPUTSELECT_DEFAULT);
				$this->SetValue(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTGAIN,   AM_VAL_INPUTGAIN_DEFAULT);
			}
		}

		/**
		 * @public
		 *
		 * Initialisiert den AudioMax Server und setzt alle Einstellungen auf den aktuellen Wert von IPS zurück.
		 */
		public function Initialize () {
			$this->LogDbg("Execute AudioMax Initialization ...");

			$this->SendData(AM_TYP_SET, AM_CMD_POWER, null, null, GetValue(IPS_GetObjectIDByIdent(AM_VAR_MAINPOWER, $this->instanceId)));

			$roomCount  = GetValue(IPS_GetObjectIDByIdent(AM_VAR_ROOMCOUNT, $this->instanceId));
			for ($roomId=0;$roomId<$roomCount;$roomId++) {
				$room=$this->GetAudioMaxRoom($roomId);
				$this->SendData(AM_TYP_SET, AM_CMD_ROOM,  $roomId, null,               $room->GetValue(AM_CMD_ROOM, ''));
				$this->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_TREBLE,      $room->GetValue(AM_CMD_AUDIO, AM_FNC_TREBLE));
				$this->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_MIDDLE,      $room->GetValue(AM_CMD_AUDIO, AM_FNC_MIDDLE));
				$this->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_BASS,        $room->GetValue(AM_CMD_AUDIO, AM_FNC_BASS));
				$this->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_VOLUME,      $room->GetValue(AM_CMD_AUDIO, AM_FNC_VOLUME));
				$this->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_MUTE,        $room->GetValue(AM_CMD_AUDIO, AM_FNC_MUTE));
				$this->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_BALANCE,     $room->GetValue(AM_CMD_AUDIO, AM_FNC_BALANCE));
				$this->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTSELECT, $room->GetValue(AM_CMD_AUDIO, AM_FNC_INPUTSELECT));
				$this->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTGAIN,   $room->GetValue(AM_CMD_AUDIO, AM_FNC_INPUTGAIN));
			}
		}

		/**
		 * @private
		 *
		 * Setzt das BUSY Flag des AudioMax Server
		 */
		private function SetBusy() {
			$result = IPS_SemaphoreEnter('AudioMax', 1000);
			$VariableId = IPS_GetObjectIDByIdent(AM_VAR_BUSY, $this->instanceId);
			SetValue($VariableId, true);
			return $result;
		}

		/**
		 * @private
		 *
		 * Zurücksetzen des BUSY Flags des AudioMax Servers.
		 */
		private function ResetBusy() {
			$VariableId = IPS_GetObjectIDByIdent(AM_VAR_BUSY, $this->instanceId);
			SetValue($VariableId, false);
			IPS_SemaphoreLeave('AudioMax');
		}



		/**
		 * @private
		 *
		 * Initializes the AudioMax Server Object
		 *
		 * @param string $type Kommando Type
		 * @param string $command Kommando
		 * @param integer $roomId Raum (1-4)
		 * @param string $function Funktion
		 * @param string $value Wert
		 * @return boolean TRUE für OK, FALSE bei Fehler
		 */
		private function BuildMsg($type, $command, $roomId, $function, $value, $addTerminator=true) {
			if ($value===true)  $value=AM_VAL_BOOLEAN_TRUE;
			if ($value===false) $value=AM_VAL_BOOLEAN_FALSE;

			if ($type==AM_TYP_GET) {
				$msg = $type.AM_COM_SEPARATOR.AM_DEV_SERVER.AM_COM_SEPARATOR.$command;
				switch ($command) {
					case AM_CMD_POWER:
					case AM_CMD_KEEPALIVE:
					case AM_CMD_TEXT:
					case AM_CMD_MODE:
						break;
					case AM_CMD_ROOM:
						$msg .= AM_COM_SEPARATOR.$roomId;
						break;
					case AM_CMD_AUDIO:
						$msg .= AM_COM_SEPARATOR.$roomId.AM_COM_SEPARATOR.$function;
						break;
					default:
						$this->LogErr("Unable to build Message - unknown Command  '.$command'");
					   exit;
				}
			} else {
				$msg = $type.AM_COM_SEPARATOR.AM_DEV_SERVER.AM_COM_SEPARATOR.$command.AM_COM_SEPARATOR;
				switch ($command) {
					case AM_CMD_POWER:
					case AM_CMD_KEEPALIVE:
					case AM_CMD_TEXT:
						$msg .= $value;
						break;
					case AM_CMD_MODE:
						$msg .= $function.AM_COM_SEPARATOR.$value;
						break;
					case AM_CMD_ROOM:
						$msg .= $roomId.AM_COM_SEPARATOR.$value;
						break;
					case AM_CMD_AUDIO:
						$msg .= $roomId.AM_COM_SEPARATOR.$function.AM_COM_SEPARATOR;
						//$msg .= str_pad($value, 2, '0', STR_PAD_LEFT);//$value;
						$msg .= $value;
						break;
					default:
						$this->LogErr("Unable to build Message - unknown Command  '.$command'");
					   exit;
				}
			}
			if ($addTerminator) {
				$msg .= AM_COM_TERMINATOR;
			}
			return $msg;
		}

		/**
		 * @private
		 *
		 * Validierung der Daten
		 *
	    * @param string $type Kommando Type
	    * @param string $command Kommando
	    * @param integer $roomId Raum (1-4)
	    * @param string $function Funktion
	    * @param string $value Wert
	    * @return boolean TRUE für OK, FALSE bei Fehler
		 */
		private function ValidateData($type, $command, $roomId, $function, $value) {
		   $errorMsg = '';
			$result   = false;
			switch($command) {
				case AM_CMD_POWER:
					$result   = ($value==true or $value==AM_VAL_BOOLEAN_TRUE or $value==false or $value==AM_VAL_BOOLEAN_FALSE);
					$errorMsg = "Value '$value' for MainPower NOT in Range (use 0,1 or boolean)";
					break;
				case AM_CMD_KEEPALIVE:  /*0..1*/
					$result   = ($value=='0');
					$errorMsg = "Value '$value' for KeepAlive NOT in Range (use '0')";
					break;
				case AM_CMD_TEXT:
				   $result = true;
					break;
				case AM_CMD_MODE:  /*0..1*/
					$result   = ($value=='0' or $value=='1');
					$errorMsg = "Value '$value' for Mode NOT in Range (use '0')";
					break;
				case AM_CMD_ROOM:
				   $roomOk   = $roomId>=0 and $roomId<GetValue(IPS_GetObjectIDByIdent('ROOM_COUNT', $this->instanceId));
					$result = $roomOk and ($value==true or $value==AM_VAL_BOOLEAN_TRUE or $value==false or $value==AM_VAL_BOOLEAN_FALSE);
					$errorMsg = "Value '$value' for RoomPower NOT in Range (use 0,1 or boolean)";
					break;
				case AM_CMD_AUDIO:
				   $roomOk   = $roomId>=0 and $roomId<GetValue(IPS_GetObjectIDByIdent('ROOM_COUNT', $this->instanceId));
					switch($function) {
						case AM_FNC_VOLUME: /*0..78*/
							$result = $roomOk and ($value>=AM_VAL_VOLUME_MIN and $value<=AM_VAL_VOLUME_MAX);
							$errorMsg = "Value '$value' for Volume NOT in Range (use ".AM_VAL_VOLUME_MIN." <= value <=".AM_VAL_VOLUME_MAX.")";
							break;
						case AM_FNC_MUTE: /*0..78*/
							$result = $roomOk and ($value==true or $value==AM_VAL_BOOLEAN_TRUE or $value==false or $value==AM_VAL_BOOLEAN_FALSE);
							$errorMsg = "Value '$value' for Mute NOT in Range (use 0,1 or boolean)";
							break;
						case AM_FNC_BALANCE: /*0..78*/
							$result = $roomOk and ($value>=AM_VAL_BALANCE_MIN and $value<=AM_VAL_BALANCE_MAX);
							$errorMsg = "Value '$value' for Balance NOT in Range (use ".AM_VAL_BALANCE_MIN." <= value <=".AM_VAL_BALANCE_MAX.")";
							break;
						case AM_FNC_INPUTGAIN: /*1..15*/
							$result = $roomOk and ($value>=AM_VAL_INPUTGAIN_MIN and $value<=AM_VAL_INPUTGAIN_MAX);
							$errorMsg = "Value '$value' for InputGain NOT in Range (use ".AM_VAL_INPUTGAIN_MIN." <= value <=".AM_VAL_INPUTGAIN_MAX.")";
							break;
						case AM_FNC_INPUTSELECT: /*0..3*/
							$result = $roomOk and ($value>=AM_VAL_INPUTSELECT_MIN and $value<=AM_VAL_INPUTSELECT_MAX);
							$errorMsg = "Value '$value' for InputSelect NOT in Range (use ".AM_VAL_INPUTSELECT_MIN." <= value <=".AM_VAL_INPUTSELECT_MAX.")";
							break;
						case AM_FNC_TREBLE: /*0..14*/
							$result = $roomOk and ($value>=AM_VAL_TREBLE_MIN and $value<=AM_VAL_TREBLE_MAX);
							$errorMsg = "Value '$value' for Treble NOT in Range (use ".AM_VAL_TREBLE_MIN." <= value <=".AM_VAL_TREBLE_MAX.")";
							break;
						case AM_FNC_MIDDLE: /*0..14*/
							$result = $roomOk and ($value>=AM_VAL_MIDDLE_MIN and $value<=AM_VAL_MIDDLE_MAX);
							$errorMsg = "Value '$value' for Middle NOT in Range (use ".AM_VAL_MIDDLE_MIN." <= value <=".AM_VAL_MIDDLE_MAX.")";
							break;
						case AM_FNC_BASS: /*0..14*/
							$result = $roomOk and ($value>=AM_VAL_BASS_MIN and $value<=AM_VAL_BASS_MAX);
							$errorMsg = "Value '$value' for Bass NOT in Range (use ".AM_VAL_BASS_MIN." <= value <=".AM_VAL_BASS_MAX.")";
							break;
						default:
							$errorMsg = "Unknonw function '$function' for Command '$command'";
					}
					break;
				default:
					$errorMsg = "Unknonw Command '$command'";
			}
			if (!$result) {
				$this->LogErr($errorMsg);
			}
			return $result;
		}


		/**
		 * @private
		 *
		 * Senden von Befehlen zum AudioMax Server per COM Port
		 *
	    * @param string $type Kommando Type
	    * @param string $command Kommando
	    * @param integer $roomId Raum (0-15)
	    * @param string $function Funktion
	    * @param string $value Wert
	    * @return boolean TRUE für OK, FALSE bei Fehler
		 */
		private function SendDataComPort($type, $command, $roomId, $function, $value) {
		   $result = false;

			if (GetValue(IPS_GetObjectIDByIdent(AM_VAR_CONNECTION, $this->instanceId))) {
				$this->LogCom('Snd Message: '.$this->BuildMsg($type, $command, $roomId, $function, $value, false));
				$comPortId = GetValue(IPS_GetObjectIDByIdent('PORT_ID', $this->instanceId));
				$msg = $this->BuildMsg($type, $command, $roomId, $function, $value);
				$result = @COMPort_SendText($comPortId, $msg);
				if ($result===false) {
					$this->LogDbg('Write to ComPort failed --> Try Reconnect');
					COMPort_SetOpen($comPortId,false);
					COMPort_SetOpen($comPortId,true);
					IPS_ApplyChanges($comPortId);
					$result = COMPort_SendText($comPortId, $msg);
				}
			} else {
				$this->LogCom('Snd Message: '.$this->BuildMsg($type, $command, $roomId, $function, $value, false).' (Connection Inactive - Msg will be ignored)!');
				$result = true;
			}

			return $result;
		}

		/**
		 * @private
		 *
		 * Warten auf die Anwort vom Server
		 *
		 * @param string $type Kommando Type
		 * @param string $command Kommando
		 * @param integer $roomId Raum (0-15)
		 * @param string $function Funktion
		 * @param string $value Wert
		 * @return boolean TRUE für OK, FALSE bei Fehler
		 */
		private function WaitForServerResponse($type, $command, $roomId, $function, $value) {
			$result = false;

			$inputBufferId = IPS_GetObjectIDByIdent(AM_VAR_INPUTBUFFER, $this->instanceId);
			$waited = 0;
			while ($waited < AM_COM_MAXWAIT) {
				IPS_Sleep(AM_COM_WAIT);
				$waited  = $waited + AM_COM_WAIT;
				$message = GetValue($inputBufferId);
				if ($message<>'') {
					$waited = AM_COM_MAXWAIT;
					$params  = explode(AM_COM_SEPARATOR, $message);
					if ($params[2] == AM_CMD_POWER) {
						$result = $value==$params[3];
					} elseif ($params[2] == AM_CMD_KEEPALIVE) {
						$result = $value==$params[3];
					} elseif ($params[2] == AM_CMD_ROOM) {
						$result = $roomId==$params[3] and $value==$params[4];
					} elseif ($params[2] == AM_CMD_AUDIO) {
						$result = $roomId==$params[3] and $function==$params[4] and $value==$params[5];
					} elseif ($params[2] == AM_CMD_MODE) {
						$result = $function==$params[3] and $value==$params[4];
					} elseif (GetValue(IPS_GetObjectIDByIdent(AM_VAR_MODEACKNOWLEDGE, $this->instanceId))==1) {
						if ($message=='0') {
							$result = true;
						} else {
							$this->LogErr('Received invalid Acknowledge from Server: '.$message);
							$result = false;
						}
					} else {
						$this->LogErr('Received invalid Acknowledge from Server: '.$message);
						$result = false;
					}
				}
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Senden von Befehlen zum AudioMax Server
		 *
	    * @param string $type Kommando Type
	    * @param string $command Kommando
	    * @param integer $roomId Raum (0-15)
	    * @param string $function Funktion
	    * @param string $value Wert
	    * @return boolean TRUE für OK, FALSE bei Fehler
		 */
		public function SendData($type, $command, $roomId, $function, $value) {
		   $result = false;

			if ($type==AM_TYP_GET and $this->GetAudioMaxRoomVariablesEnabled()) {
				if ($this->ValidateData($type, $command, $roomId, $function, $value)) {
					$result = $this->GetValue($type, $command, $roomId, $function);
				}
				return $result;
		   }
		
			if ($this->SetBusy()) {
				$this->LogTrc("Process Type='$type', Command='$command', Function='$function' and Value='$value' for Room $roomId");
				if ($this->ValidateData($type, $command, $roomId, $function, $value)) {
					SetValue(IPS_GetObjectIDByIdent(AM_VAR_INPUTBUFFER, $this->instanceId), '');

					$result = $this->SendDataComPort($type, $command, $roomId, $function, $value);
					if ($result) {
						if (GetValue(IPS_GetObjectIDByIdent(AM_VAR_MODEEMULATESTATE, $this->instanceId))) {
							$this->SetValue($type, $command, $roomId, $function, $value);
							IPS_Sleep(AM_COM_WAIT);
						} elseif (!GetValue(IPS_GetObjectIDByIdent(AM_VAR_CONNECTION, $this->instanceId))) {
							$this->SetValue($type, $command, $roomId, $function, $value);
						} else {
							$retryCount = 1;
							while ($retryCount<=AM_COM_MAXRETRIES) {
								if ($this->WaitForServerResponse($type, $command, $roomId, $function, $value)) {
									$this->SetValue($type, $command, $roomId, $function, $value);
									$retryCount = AM_COM_MAXRETRIES;
								} else {
									$this->LogDbg('Timeout or invalid Response while waiting for Server Response (Retry='.$retryCount.') --> Resend Message '.
									              $this->BuildMsg($type, $command, $roomId, $function, $value, false));
									SetValue(IPS_GetObjectIDByIdent(AM_VAR_INPUTBUFFER, $this->instanceId), '');
									$result = $this->SendDataComPort($type, $command, $roomId, $function, $value);
								}
								$retryCount = $retryCount + 1;
							}
						}
					}
				}
				$this->ResetBusy();
			} else {
				$this->LogErr("AudioMax is already BUSY, ignore Message".$this->BuildMsg($type, $command, $roomId, $function, $value, false));
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Empfangen von Befehlen vom AudioMax Server
		 *
		 * @param string $message Message vom AudioMax Server
		 */
		public function ReceiveData($message) {
			$message = str_replace(chr(13), '', $message);
			$message = str_replace(chr(10), '', $message);
			$params  = explode(AM_COM_SEPARATOR, $message);

			if ($message=='') return;

			$this->LogCom('Rcv Message: '.$message);
			switch ($params[0]) {
				case AM_TYP_EVT:
					if ($params[2] == AM_CMD_POWER) {

					} elseif ($params[2] == AM_CMD_MODE) {
						$this->ValidateAndSetValue(AM_TYP_SET, AM_CMD_MODE, null, $params[3], $params[4]);
					} elseif ($params[2] == AM_CMD_KEEPALIVE) {
						SetValue(IPS_GetObjectIDByIdent(AM_VAR_KEEPALIVEFLAG, $this->instanceId), true);
						$this->ValidateAndSetValue(AM_TYP_SET, AM_CMD_POWER, null, null, $params[3]);
					} elseif ($params[2] == AM_CMD_ROOM) {
						$this->ValidateAndSetValue(AM_TYP_SET, AM_CMD_ROOM, $params[3], null, $params[4]);
					} elseif ($params[2] == AM_CMD_AUDIO) {
						$this->ValidateAndSetValue(AM_TYP_SET, AM_CMD_AUDIO, $params[3], $params[4], $params[5]);
					} else {
						//$this->LogErr("Received invalid Message".$message);
					}
					break;
				case AM_TYP_GET:
				case AM_TYP_SET:
					SetValue(IPS_GetObjectIDByIdent(AM_VAR_INPUTBUFFER, $this->instanceId), $message);
					break;
				default:
					//$this->LogErr("Received invalid Message=".$message.', Type='.$params[0]);
					break;
			}
		}
	}

	/** @}*/
?>