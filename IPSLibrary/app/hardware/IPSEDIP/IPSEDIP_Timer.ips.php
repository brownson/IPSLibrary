<?
	/**@addtogroup ipsedip
	 * @{
	 *
	 * Timer Script
	 *
	 * @file          IPSEDIP_Timer.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Dieses Script wird vom internen Timer aufgerufen, um die visualisierten Daten wieder neu
	 * anzuzeigen.
	 * Voraussetzung für den Refresh mit Timer ist, dass der Konfigurations Parameter EDIP_CONFIG_REFRESHMETHOD
	 * auf dem Wert EDIP_REFRESHMETHOD_TIMER steht.
	 *
	 */

	include_once "IPSEDIP.class.php";

	foreach (IPSEDIP_GetConfiguration() as $configId=>$configData) {
		if ($configData[EDIP_CONFIG_REFRESHMETHOD]==EDIP_REFRESHMETHOD_TIMER or
			$configData[EDIP_CONFIG_REFRESHMETHOD]==EDIP_REFRESHMETHOD_BOTH) {
			$instanceClass = $configData[EDIP_CONFIG_CLASSNAME];
			$instanceId    = IPS_GetObjectIDByIdent($configId, EDIP_ID_PROGRAM);

			include_once $instanceClass.'.class.php';
			$edip = new $instanceClass($instanceId);
			$edip->RefreshDisplay();
		}
	}

	/** @}*/
?>