<?
	/**@addtogroup ipsmessagehandler 
	 * @{
	 *
	 * @file          IPSMessageHandler_Event.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Script dient als EventScript um den IPSMessageHandler über Variablen Änderungen der Componenten zu informieren
	 *
	 */

	$variable = $_IPS['VARIABLE'];
	$value    = $_IPS['VALUE'];

	IPSUtils_Include ('IPSMessageHandler.class.php', 'IPSLibrary::app::core::IPSMessageHandler');

	$messageHandler = new IPSMessageHandler();
	$messageHandler->HandleEvent($variable, $value);

	/** @}*/
?>