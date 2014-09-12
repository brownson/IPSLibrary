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

	/**@ingroup ipscam
	 * @{
	 *
	 * @file          IPSCam_Constants.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.08.2012<br/>
	 *
	 * Definition der Konstanten für IPSCam
	 *
	 */

	// Confguration Property Definition
	define ('IPSCAM_PROPERTY_NAME',				'Name');
	define ('IPSCAM_PROPERTY_TYPE',				'Type');
	define ('IPSCAM_PROPERTY_COMPONENT',		'Component');
	define ('IPSCAM_PROPERTY_SWITCHPOWER',		'SwitchPower');
	define ('IPSCAM_PROPERTY_SWITCHWLAN',		'SwitchWLAN');
	define ('IPSCAM_PROPERTY_PREDEFPOS1',		'PredefinedPosition1');
	define ('IPSCAM_PROPERTY_PREDEFPOS2',		'PredefinedPosition2');
	define ('IPSCAM_PROPERTY_PREDEFPOS3',		'PredefinedPosition3');
	define ('IPSCAM_PROPERTY_PREDEFPOS4',		'PredefinedPosition4');
	define ('IPSCAM_PROPERTY_COMMAND1',			'Command1');
	define ('IPSCAM_PROPERTY_COMMAND2',			'Command2');
	define ('IPSCAM_PROPERTY_COMMAND3',			'Command3');
	define ('IPSCAM_PROPERTY_COMMAND4',			'Command4');
	define ('IPSCAM_PROPERTY_ACTION1',			'Action1');
	define ('IPSCAM_PROPERTY_ACTION2',			'Action2');
	define ('IPSCAM_PROPERTY_ACTION3',			'Action3');
	define ('IPSCAM_PROPERTY_ACTION4',			'Action4');

	define ('IPSCAM_TYPE_FIXEDCAM',				'FixedCam');
	define ('IPSCAM_TYPE_MOVABLECAM',			'MovableCam');

	define ('IPSCAM_MODE_LIVE',					0);
	define ('IPSCAM_MODE_PICTURE',				1);
	define ('IPSCAM_MODE_HISTORY',				2);
	define ('IPSCAM_MODE_SETTINGS',				3);

	define ('IPSCAM_URL_LIVE',					0);
	define ('IPSCAM_URL_PICTURE',				1);
	define ('IPSCAM_URL_DISPLAY',				2);
	define ('IPSCAM_URL_MOVEUP',				100);
	define ('IPSCAM_URL_MOVEDOWN',				101);
	define ('IPSCAM_URL_MOVELEFT',				102);
	define ('IPSCAM_URL_MOVERIGHT',				103);
	define ('IPSCAM_URL_MOVEHOME',				104);
	define ('IPSCAM_URL_PREDEFPOS1',			110);
	define ('IPSCAM_URL_PREDEFPOS2',			111);
	define ('IPSCAM_URL_PREDEFPOS3',			112);
	define ('IPSCAM_URL_PREDEFPOS4',			113);
	define ('IPSCAM_URL_PREDEFPOS5',			114);

	define ('IPSCAM_VAL_DISABLED',				100000);

	define ('IPSCAM_SIZE_SMALL',				0);
	define ('IPSCAM_SIZE_MIDDLE',				1);
	define ('IPSCAM_SIZE_LARGE',				2);

	define ('IPSCAM_NAV_BACK',					0);
	define ('IPSCAM_NAV_FORWARD',				1);

	define ('IPSCAM_NAV_DATEFORMATFILE',		'Ymd_His');
	define ('IPSCAM_NAV_DATEFORMATDISP',		'Y-m-d H:i');

	define ('IPSCAM_DAY_BACK',					0);
	define ('IPSCAM_DAY_FORWARD',				1);

	define ('IPSCAM_VAR_CAMSELECT',				'CamSelect');
	define ('IPSCAM_VAR_CAMPOWER',				'CamPower');
	define ('IPSCAM_VAR_CAMHTML',				'CamHtml');
	define ('IPSCAM_VAR_HTML',					'Html');
	define ('IPSCAM_VAR_IHTML',					'iHtml');
	define ('IPSCAM_VAR_CAMPICT',				'CamPict');
	define ('IPSCAM_VAR_CAMHIST',				'CamHist');
	define ('IPSCAM_VAR_CAMSTREAM',				'CamStream');
	define ('IPSCAM_VAR_MODE',					'Mode');
	define ('IPSCAM_VAR_MODELIVE',				'ModeLive');
	define ('IPSCAM_VAR_MODEPICT',				'ModePicture');
	define ('IPSCAM_VAR_MODEHIST',				'ModeHistory');
	define ('IPSCAM_VAR_MODESETT',				'ModeSettings');
	define ('IPSCAM_VAR_SIZE',					'Size');
	define ('IPSCAM_VAR_NAVPICT',				'NavigatePict');
	define ('IPSCAM_VAR_NAVDAYS',				'NavigateDays');
	define ('IPSCAM_VAR_NAVTIME',				'NavigateTime');
	define ('IPSCAM_VAR_NAVFILE',				'NavigateFile');
	define ('IPSCAM_VAR_MOTMODE',				'MotionMode');
	define ('IPSCAM_VAR_MOTTIME',				'MotionTime');
	define ('IPSCAM_VAR_MOTHIST',				'MotionHist');
	define ('IPSCAM_VAR_MOTSIZE',				'MotionSize');
	define ('IPSCAM_VAR_PICTREF',				'PictureRefresh');
	define ('IPSCAM_VAR_PICTSTORE',				'PictureStore');
	define ('IPSCAM_VAR_PICTRESET',				'PictureReset');
	define ('IPSCAM_VAR_PICTHIST',				'PictureHist');
	define ('IPSCAM_VAR_PICTSIZE',				'PictureSize');



	/** @}*/
?>