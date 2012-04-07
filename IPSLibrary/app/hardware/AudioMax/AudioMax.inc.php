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
	 * Dieses File kann von anderen Scripts per INCLUDE eingebunden werden und enthält Funktionen
	 * um alle AudioMax Funktionen bequem per Funktionsaufruf steueren zu können.
	 *
	 */

 	include_once 'AudioMax_Server.class.php';

	/**
	 * Server Ein- und Ausschalten
	 *
	 * @param int $instanceId  ID des AudioMax Servers
	 * @param string $value TRUE oder '1' für An, FALSE oder '0' für Aus
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
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
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @param string $value TRUE oder '1' für An, FALSE oder '0' für Aus
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetRoomPower($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_ROOM, $roomId, null, $value);
	}

	/**
	 * Status Raumverstärker lesen
	 *
	 * @param int $instanceId ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @return boolean Status Raumverstärker
	 */
	function AudioMax_GetRoomPower($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_ROOM, $roomId, null, null);
	}

	/**
	 * Auswahl des Eingangs, der für einen bestimmten Raum verwendet werden soll
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @param int $value Eingang (1-4)
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetInputSelect($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTSELECT, $value);
	}

	/**
	 * Eingangswahlschalter lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @return int Eingangswahl (1-4)
	 */
	function AudioMax_GetInputSelect($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTSELECT, null)+1;
	}

	/**
	 * Eingangsverstärkung setzen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (1-4)
	 * @param int $value Verstärkung (0-15)
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetInputGain($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTGAIN, $value);
	}

	/**
	 * Eingangsverstärkung lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (1-4)
	 * @return int Verstärkung (0-15)
	 */
	function AudioMax_GetInputGain($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_INPUTGAIN, null);
	}

	/**
	 * Laustärke setzen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (1-4)
	 * @param int $value Lautstärke (0-40)
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetVolume($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_VOLUME, AM_VAL_VOLUME_MAX-$value);
	}

	/**
	 * Laustärke lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (1-4)
	 * @return int Lautstärke (0-40)
	 */
	function AudioMax_GetVolume($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_VOLUME, null);
	}

	/**
	 * Muting setzen
	 *
	 * @param int $instanceId ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @param string $value TRUE oder '1' für An, FALSE oder '0' für Aus
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetMute($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_MUTE, $value);
	}

	/**
	 * Status Muting lesen
	 *
	 * @param int $instanceId ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
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
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @param int $value Wert Balance (0-15)
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetBalance($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_BALANCE, $value);
	}

	/**
	 * Balance lesen
	 *
	 * @param int $instanceId ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @return int Wert Balance (0-15)
	 */
	function AudioMax_GetBalance($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_BALANCE, null);
	}

	/**
	 * Einstellung Höhen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @param int $value Wert Höhen (0-15)
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetTreble($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_TREBLE, $value);
	}

	/**
	 * Einstellung Höhen lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @return int Wert Höhen (0-15)
	 */
	function AudioMax_GetTreble($instanceId, $roomId) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_GET, AM_CMD_AUDIO, $roomId, AM_FNC_TREBLE, null);
	}

	/**
	 * Einstellung Mitten
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @param int $value Wert Mitten (0-15)
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetMiddle($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_MIDDLE, $value);
	}

	/**
	 * Einstellung Mitten lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
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
    * @param int $roomId Raum der geändert werden soll (0-15)
	 * @param int $value Wert Bass (0-15)
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
	 */
	function AudioMax_SetBass($instanceId, $roomId, $value) {
		$server = AudioMax_GetServer($instanceId);
		return $server->SendData(AM_TYP_SET, AM_CMD_AUDIO, $roomId, AM_FNC_BASS, $value);
	}

	/**
	 * Einstellung Bass lesen
	 *
	 * @param int $instanceId  ID des AudioMax Servers
    * @param int $roomId Raum der geändert werden soll (0-15)
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
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
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
	 * @param int $mode Mode (0-4)
	 * @param string $value Array mit Texten
	 * @return boolean Funktions Ergebnis, TRUE für OK, FALSE für Fehler
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