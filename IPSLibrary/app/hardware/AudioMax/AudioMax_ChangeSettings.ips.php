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

	 /**@addtogroup audiomax
	 * @{
	 *
	 * @file          AudioMax_ChangeSettings.ips.php
	 * @author        Andreas Brauneis
	 *
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 * AudioMax Action Script
	 *
	 * Dieses Script ist als Action Script für diverse AudioMax Variablen hinterlegt, um
	 * eine Änderung über das WebFront zu ermöglichen.
	 *
	 */

 	include_once 'AudioMax.inc.php';

	$variableId    = $IPS_VARIABLE;
	$variableValue = $IPS_VALUE;
	$variableIdent = IPS_GetIdent($variableId);

	$serverId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.AudioMax.AudioMax_Server');
	$instanceId = IPS_GetParent($variableId);
	if ($serverId<>$instanceId) {
		$roomIds = GetValue(IPS_GetObjectIDByIdent(AM_VAR_ROOMIDS, $serverId));
		$roomList = array_flip(explode(',',$roomIds));
		$roomId = $roomList[$instanceId];
	}

	switch($variableIdent) {
		case AM_VAR_CONNECTION:
			$server = new AudioMax_Server($serverId);
			$server->SetConnection($variableValue);
			break;

		case AM_VAR_MODESERVERDEBUG:
			AudioMax_SetMode($serverId, AM_MOD_SERVERDEBUG, $variableValue);
			break;
		case AM_VAR_MODEACKNOWLEDGE:
			AudioMax_SetMode($serverId, AM_MOD_ACKNOWLEDGE, $variableValue);
			break;
		case AM_VAR_MODEPOWERREQUEST:
			AudioMax_SetMode($serverId, AM_MOD_POWERREQUEST, $variableValue);
			break;
		case AM_VAR_MODEEMULATESTATE:
			SetValue(IPS_GetObjectIDByIdent(AM_VAR_MODEEMULATESTATE, $instanceId), $variableValue);
			break;

		case AM_VAR_MAINPOWER:
			AudioMax_SetMainPower($serverId, $variableValue);
			break;

		case AM_VAR_ROOMPOWER:
			AudioMax_SetRoomPower($serverId , $roomId, $variableValue);
			break;
		case AM_VAR_BALANCE:
			AudioMax_SetBalance($serverId , $roomId, $variableValue);
			break;
		case AM_VAR_VOLUME:
			AudioMax_SetVolume($serverId , $roomId, $variableValue);
			break;
		case AM_VAR_MUTE:
			AudioMax_SetMute($serverId , $roomId, $variableValue);
			break;
		case AM_VAR_TREBLE:
			AudioMax_SetTreble($serverId , $roomId, $variableValue);
			break;
		case AM_VAR_MIDDLE:
			AudioMax_SetMiddle($serverId , $roomId, $variableValue);
			break;
		case AM_VAR_BASS:
			AudioMax_SetBass($serverId , $roomId, $variableValue);
			break;
		case AM_VAR_INPUTSELECT:
			AudioMax_SetInputSelect($serverId , $roomId, $variableValue);
			break;
		case AM_VAR_INPUTGAIN:
			AudioMax_SetInputGain($serverId , $roomId, $variableValue);
			break;
		default:
			break;
	}
	;
	/** @}*/
?>