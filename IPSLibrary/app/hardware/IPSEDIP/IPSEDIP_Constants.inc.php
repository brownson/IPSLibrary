<?
	/**@addtogroup ipsedip
	 * @{
	 *
	 * EDIP Konstanten
	 *
	 * @file          IPSEDIP_Constants.inc.php
	 * @author        Andreas Brauneis
	 * @author        André Czwalina
	 * @version
	 * Version 2.50.2, 16.04.2012<br/>
	 *
	 */

	include 'IPSEDIP_IDs.inc.php';

	// Variable Ident Definitions
	define ('EDIP_VAR_REGISTER',						'REGISTER_ID');
	define ('EDIP_VAR_ROOT',							'ROOT_ID');
	define ('EDIP_VAR_CURRENT',						'CURRENT_ID');
	define ('EDIP_VAR_OBJECTIDS',						'OBJECT_IDS');
	define ('EDIP_VAR_OBJECTVALUES',					'OBJECT_VALUES');
	define ('EDIP_VAR_OBJECTCMDS',					'OBJECT_CMDS');
	define ('EDIP_VAR_OBJECTEDIT',					'OBJECT_EDIT');
	define ('EDIP_VAR_BACKLIGHT',						'OBJECT_BACKLIGHT');                                             
	define ('EDIP_VAR_NOTIFY',							'OBJECT_NOTIFY');                                                
	
	// Configuration
	define ('EDIP_CONFIG_NR1',							'Edip_1');
	define ('EDIP_CONFIG_NR2',							'Edip_2');
	define ('EDIP_CONFIG_NR3',							'Edip_3');
	define ('EDIP_CONFIG_NR4',							'Edip_4');
	define ('EDIP_CONFIG_NR5',							'Edip_5');

	define ('EDIP_CONFIG_NAME',						'Name');
	define ('EDIP_CONFIG_REGISTER',					'RegisterVariableId');
	define ('EDIP_CONFIG_COMPORT',					'ComPort');
	define ('EDIP_CONFIG_ROOT',						'RootId');
	define ('EDIP_CONFIG_REFRESHMETHOD',			'RefreshMethod');
	define ('EDIP_CONFIG_CLASSNAME',					'ClassName');
	define ('EDIP_CONFIG_BACKLIGHT_LOW',			'Backlight_Low');                                             
	define ('EDIP_CONFIG_BACKLIGHT_HIGH',			'Backlight_High');                                            
	define ('EDIP_CONFIG_BACKLIGHT_TIMER',			'Backlight_Timer');                                           
	define ('EDIP_CONFIG_ROOT_TIMER',				'Root_Timer');                                               		

	define ('EDIP_REFRESHMETHOD_TIMER',				'Timer');
	define ('EDIP_REFRESHMETHOD_EVENT',				'Event');
	define ('EDIP_REFRESHMETHOD_NONE',				'None');
	define ('EDIP_REFRESHMETHOD_BOTH',				'Both');

	define ('EDIP_CLASSNAME_EDIP43',					'IPSEDIP_TFT43A');
	define ('EDIP_CLASSNAME_EDIP240',				'IPSEDIP_240');


	/** @}*/
?>