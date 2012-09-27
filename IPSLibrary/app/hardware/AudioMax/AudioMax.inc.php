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

	/**@defgroup audiomax_api AudioMax API
	 * @ingroup audiomax
	 * @{
	 *
	 * AudioMax Server API
	 *
	 * @file          AudioMax.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Dieses File kann von anderen Scripts per INCLUDE eingebunden werden und enth�lt Funktionen
	 * um alle AudioMax Funktionen bequem per Funktionsaufruf steueren zu k�nnen.
	 *
	 */

 	include_once 'AudioMax_Server.class.php';

	/**
	 * Server Ein- und Ausschalten
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param string $value TRUE oder '1' f�r An, FALSE oder '0' f�r Aus
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetMainPower($instanceId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_POWER, null, null, $value);
	}

	/**
	 * Status ServerPower lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @return boolean Power Status
	 */
	function AudioMax_GetMainPower($instanceId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_POWER, null, null, null);
	}

	/**
	 * Ein- und Ausschalten eines einzelnen Raumes
	 *
	 * @param int $instanceId ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param string $value TRUE oder '1' f�r An, FALSE oder '0' f�r Aus
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetRoomPower($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_ROOM, $roomId, null, $value);
	}

	/**
	 * Status Raumverst�rker lesen
	 *
	 * @param int $instanceId ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return boolean Status Raumverst�rker
	 */
	function AudioMax_GetRoomPower($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_ROOM, $roomId, null, null);
	}

	/**
	 * Auswahl des Eingangs, der f�r einen bestimmten Raum verwendet werden soll
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param int $value Eingang (1-4)
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetInputSelect($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTSELECT, $value);
	}

	/**
	 * Eingangswahlschalter lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return int Eingangswahl (1-4)
	 */
	function AudioMax_GetInputSelect($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTSELECT, null)+1;
	}

	/**
	 * Eingangsverst�rkung setzen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param int $value Verst�rkung (0-15)
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetInputGain($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTGAIN, $value);
	}

	/**
	 * Eingangsverst�rkung lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return int Verst�rkung (0-15)
	 */
	function AudioMax_GetInputGain($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTGAIN, null);
	}

	/**
	 * Laust�rke setzen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param int $value Lautst�rke (0-40)
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetVolume($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_VOLUME, round(AM_VAL_VOLUME_MAX-$value));
	}

	/**
	 * Laust�rke lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return int Lautst�rke (0-40)
	 */
	function AudioMax_GetVolume($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_VOLUME, null);
	}

	/**
	 * Muting setzen
	 *
	 * @param int $instanceId ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param string $value TRUE oder '1' f�r An, FALSE oder '0' f�r Aus
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetMute($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_MUTE, $value);
	}

	/**
	 * Status Muting lesen
	 *
	 * @param int $instanceId ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return boolean Status Muting
	 */
	function AudioMax_GetMute($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_MUTE, null);
	}

	/**
	 * Balance setzen
	 *
	 * @param int $instanceId ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param int $value Wert Balance (0-15)
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetBalance($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_BALANCE, $value);
	}

	/**
	 * Balance lesen
	 *
	 * @param int $instanceId ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return int Wert Balance (0-15)
	 */
	function AudioMax_GetBalance($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_BALANCE, null);
	}

	/**
	 * Einstellung H�hen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param int $value Wert H�hen (0-15)
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetTreble($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_TREBLE, $value);
	}

	/**
	 * Einstellung H�hen lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return int Wert H�hen (0-15)
	 */
	function AudioMax_GetTreble($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_TREBLE, null);
	}

	/**
	 * Einstellung Mitten
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param int $value Wert Mitten (0-15)
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetMiddle($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_MIDDLE, $value);
	}

	/**
	 * Einstellung Mitten lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return int Wert Mitten (0-15)
	 */
	function AudioMax_GetMiddle($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_MIDDLE, null);
	}

	/**
	 * Einstellung Bass setzen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ge�ndert werden soll (0-3)
	 * @param int $value Wert Bass (0-15)
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetBass($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_BASS, $value);
	}

	/**
	 * Einstellung Bass lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $roomId Raum der ausgelesen werden soll (0-3)
	 * @return int Wert Bass (0-15)
	 */
	function AudioMax_GetBass($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_BASS, null);
	}

	/**
	 * Set Mode
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $mode Mode (0-4)
	 * @param int $value Wert (0 oder 1)
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetMode($instanceId, $mode, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_MODE, null, $mode, $value);
	}

	/**
	 * Get Mode
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param int $mode Mode (0-4)
	 * @return integer Mode Value (0 oder 1)
	 */
	function AudioMax_GetMode($instanceId, $mode) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_MODE, null, $mode, null);
	}

	/**
	 * Set Text
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param string $text1 Text Zeile 1
	 * @param string $text2 Text Zeile 2
	 * @param string $text3 Text Zeile 3
	 * @return boolean Funktions Ergebnis, TRUE f�r OK, FALSE f�r Fehler
	 */
	function AudioMax_SetText($instanceId, $text1, $text2=null, $text3=null) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_TEXT, null, null, $text1.AM_COM_SEPARATOR.$text2.AM_COM_SEPARATOR.$text3);
	}

	/**
	 * Get Server
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @return AudioMax AudioMax Server Object
	 */
	function AudioMax_GetServer($instanceId) {
	   if ($instanceId==null) {
	   	$instanceId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.AudioMax.AudioMax_Server');
		}
		return new AudioMax_Server($instanceId);
	}


   /** @}*/


?>