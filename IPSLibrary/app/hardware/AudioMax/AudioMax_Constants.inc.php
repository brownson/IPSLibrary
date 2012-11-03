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
	 * AudioMax Server Konstanten
	 *
	 * @file          AudioMax_Constants.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Prinzipieller Aufbau der Kommunikation:
	 *   CommandType Command Room Function Value
	 *
	 * Jeder Kommando Teil wird durch einen Separator voneinander getrennt (BLANK). Terminiert
	 * wird jedes Kommando von einem CR.
	 *
	 * Examples:
	 *   set svr pwr 1\<cr\>		        AudioMax Server Ein
	 *   evt svr kal 0\<cr\>		        Keep alive Message von Server
	 *   set svr roo 00 1\<cr\>		     Raumverstärker einschalten
	 *   set svr aud 00 inp 1\<cr\>		  Eingang 2 in Raum 1
	 *   set svr aud 02 vol 08\<cr\>	     Volume  Raum 2 auf 08
	 *   set svr aud 04 bas 14\<cr\> 	  Bass Raum 4 auf 14
	 *
	 *
	 */
	define ('AM_COM_SEPARATOR',					';');
	define ('AM_COM_TERMINATOR',				chr(13));
	define ('AM_COM_KEEPALIVE',					60);
	define ('AM_COM_MAXRETRIES',				3);
	define ('AM_COM_WAIT',						50);
	define ('AM_COM_MAXWAIT',					500);

	// Kommunikations Kommando Typen
	define ('AM_TYP_SET',						'SET');
	define ('AM_TYP_GET',						'GET');
	define ('AM_TYP_EVT',						'EVT');
	define ('AM_TYP_DBG',						'DBG');

	// Kommunikations Device Type
	define ('AM_DEV_SERVER',					'SVR');

	// Error Codes
	define ('AM_ERR_UNKNOWNCMD1',				'1');
	define ('AM_ERR_UNKNOWNCMD2',				'2');
	define ('AM_ERR_UNKNOWNCMD3',				'3');
	define ('AM_ERR_UNKNOWNCMD4',				'4');
	define ('AM_ERR_UNKNOWNCMD5',				'5');

	// Acknowledge
	define ('AM_VAL_ACKNOWLEDGE',				'0');

	// Kommunikations Kommandos
	define ('AM_CMD_POWER',						'PWR');
	define ('AM_CMD_KEEPALIVE',					'KAL');
	define ('AM_CMD_AUDIO',						'AUD');
	define ('AM_CMD_ROOM',						'ROO');
	define ('AM_CMD_TEXT',						'TEX');
	define ('AM_CMD_MODE',						'MOD');

	// Kommunikations Actions
	define ('AM_FNC_POWERREQUEST',				'PUS');
	define ('AM_FNC_BALANCE',					'BAL');
	define ('AM_FNC_VOLUME',					'VOL');
	define ('AM_FNC_MUTE',						'MUT');
	define ('AM_FNC_TREBLE',					'TRE');
	define ('AM_FNC_MIDDLE',					'MID');
	define ('AM_FNC_BASS',						'BAS');
	define ('AM_FNC_INPUTSELECT',				'INP');
	define ('AM_FNC_INPUTGAIN',					'GAI');

	// Modes
	define ('AM_MOD_ACKNOWLEDGE',				0);
	define ('AM_MOD_SERVERDEBUG',				1);
	define ('AM_MOD_POWERREQUEST',				2);
	define ('AM_MOD_KEEPALIVE',					3);

	// Max, Min und Default Werte
	define ('AM_VAL_ROOM_MIN',					0);
	define ('AM_VAL_ROOM_MAX',					15);

	define ('AM_VAL_BOOLEAN_FALSE',				0);
	define ('AM_VAL_BOOLEAN_TRUE',				1);

	define ('AM_VAL_VOLUME_MIN',				0);
	define ('AM_VAL_VOLUME_MAX',				40);
	define ('AM_VAL_VOLUME_DEFAULT',			20);

	define ('AM_VAL_MUTE_OFF',					AM_VAL_BOOLEAN_FALSE);
	define ('AM_VAL_MUTE_ON',					AM_VAL_BOOLEAN_TRUE);
	define ('AM_VAL_MUTE_DEFAULT',				AM_VAL_BOOLEAN_FALSE);

	define ('AM_VAL_TREBLE_MIN',				0);
	define ('AM_VAL_TREBLE_MAX',				15);
	define ('AM_VAL_TREBLE_DEFAULT',			7);

	define ('AM_VAL_BALANCE_MIN',				0);
	define ('AM_VAL_BALANCE_MAX',				15);
	define ('AM_VAL_BALANCE_DEFAULT',			0);

	define ('AM_VAL_MIDDLE_MIN',				0);
	define ('AM_VAL_MIDDLE_MAX',				15);
	define ('AM_VAL_MIDDLE_DEFAULT',			7);

	define ('AM_VAL_BASS_MIN',					0);
	define ('AM_VAL_BASS_MAX',					15);
	define ('AM_VAL_BASS_DEFAULT',				7);

	define ('AM_VAL_INPUTSELECT_MIN',			0);
	define ('AM_VAL_INPUTSELECT_MAX',			3);
	define ('AM_VAL_INPUTSELECT_DEFAULT',		0);

	define ('AM_VAL_INPUTGAIN_MIN',				0);
	define ('AM_VAL_INPUTGAIN_MAX',				15);
	define ('AM_VAL_INPUTGAIN_DEFAULT',			7);

	define ('AM_VAL_POWER_OFF',					AM_VAL_BOOLEAN_FALSE);
	define ('AM_VAL_POWER_ON',					AM_VAL_BOOLEAN_TRUE);
	define ('AM_VAL_POWER_DEFAULT',				AM_VAL_BOOLEAN_FALSE);


	// Variablen Definitionen
	define ('AM_VAR_MAINPOWER',					'MAINPOWER');
	define ('AM_VAR_BUSY',						'BUSY');
	define ('AM_VAR_CONNECTION',				'CONNECTION');
	define ('AM_VAR_ROOMCOUNT',					'ROOM_COUNT');
	define ('AM_VAR_ROOMIDS',					'ROOM_IDS');
	define ('AM_VAR_PORTID',					'PORT_ID');
	define ('AM_VAR_KEEPALIVEFLAG',				'KEEP_ALIVE_FLAG');
	define ('AM_VAR_KEEPALIVESTATUS',			'KEEP_ALIVE_STATUS');
	define ('AM_VAR_LASTERROR',					'LAST_ERROR');
	define ('AM_VAR_LASTCOMMAND',				'LAST_COMMAND');
	define ('AM_VAR_INPUTBUFFER',				'INPUT_BUFFER');
	define ('AM_VAR_MODESERVERDEBUG',			'MODE_SERVERDEBUG');
	define ('AM_VAR_MODEPOWERREQUEST',			'MODE_POWERREQUEST');
	define ('AM_VAR_MODEEMULATESTATE',			'MODE_EMULATESTATE');
	define ('AM_VAR_MODEACKNOWLEDGE',			'MODE_ACKNOWLEDGE');

	define ('AM_VAR_ROOMPOWER',					'ROOMPOWER');
	define ('AM_VAR_BALANCE',					'BALANCE');
	define ('AM_VAR_MUTE',						'MUTE');
	define ('AM_VAR_VOLUME',					'VOLUME');
	define ('AM_VAR_TREBLE',					'TREBLE');
	define ('AM_VAR_MIDDLE',					'MIDDLE');
	define ('AM_VAR_BASS',						'BASS');
	define ('AM_VAR_INPUTSELECT',				'INPUTSELECT');
	define ('AM_VAR_INPUTGAIN',					'INPUTGAIN');
	/** @}*/
?>