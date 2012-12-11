<?
	/**@ingroup audiomax_configuration
	 * @{
	 *
	 * AudioMax Server Konfiguration
	 *
	 * @file          AudioMax_DemoConfiguration.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 20.02.2012<br/>
	 *
	 * �nderung an den Parametern erfordert ein erneutes Ausf�hren des Installations Scripts.
	 *
	 */

	/**
	 * Definition des COM Ports, der f�r den AudioMax Server verwendet wird. Ist der Port gesetzt,
	 * wird die Register Variable, die Splitter Instanz und die IO Instanz automatisch angelegt.
	 *
	 * Alternativ kann die zu verwendende Register Variable auch nachtr�glich in der erzeugten
	 * AudioMax Instanz gesetzt werden.
	 *
	 */
	define ('AM_CONFIG_COM_PORT',					'');

	/**
	 * Definition der Anzahl der R�ume
	 */
	define ('AM_CONFIG_ROOM_COUNT',				3);

	/**
	 * Definition des Namens f�r den Eingang 1 des AudioMax Servers
	 */
	define ('AM_CONFIG_INPUTNAME1',				'NetPlayer');

	/**
	 * Definition des Namens f�r den Eingang 2 des AudioMax Servers
	 */
	define ('AM_CONFIG_INPUTNAME2',				'Tuner');

	/**
	 * Definition des Namens f�r den Eingang 3 des AudioMax Servers
	 */
	define ('AM_CONFIG_INPUTNAME3',				'');

	/**
	 * Definition des Namens f�r den Eingang 4 des AudioMax Servers
	 */
	define ('AM_CONFIG_INPUTNAME4',				'');

	/**
	 * Definition des Namens f�r den Raum 1 des AudioMax Servers
	 */
	define ('AM_CONFIG_ROOMNAME1',				'Terrasse');

	/**
	 * Definition des Namens f�r den Raum 2 des AudioMax Servers
	 */
	define ('AM_CONFIG_ROOMNAME2',				'Wohnzimmer');

	/**
	 * Definition des Namens f�r den Raum 3 des AudioMax Servers
	 */
	define ('AM_CONFIG_ROOMNAME3',				'Arbeitszimmer');

	/**
	 * Definition des Namens f�r den Raum 4 des AudioMax Servers
	 */
	define ('AM_CONFIG_ROOMNAME4',				'');

	/** @}*/
?>