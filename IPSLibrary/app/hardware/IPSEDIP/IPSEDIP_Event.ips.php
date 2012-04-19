<?
	/**@addtogroup ipsedip
	 * @{
	 *
	 * Event Script
	 *
	 * @file          IPSEDIP_Event.ips.php
	 * @author        Andreas Brauneis
	 * @author        André Czwalina
	 * @version
	 * Version 2.50.2, 16.04.2012<br/>
	 *
	 * Dieses Script wird von Events aufgerufen, um die EDIP Steuerung über Änderungen an den
	 * gerade angezeigten Daten zu informieren.
	 * Angelegt werden die Events von der EDIP Steuerung selbst, sobald eine entsprechende Variable
	 * visualisiert wird.
	 * Voraussetzung für den Refresh mit Event ist, dass der Konfigurations Parameter EDIP_CONFIG_REFRESHMETHOD
	 * auf dem Wert EDIP_REFRESHMETHOD_EVENT steht.
	 *
	 */

	include_once "IPSEDIP.class.php";

	$eventName = IPS_GetName($_IPS['EVENT']);

	foreach (IPSEDIP_GetConfiguration() as $configId=>$configData) {
		if (substr($eventName,0,strlen($configId))==$configId) {

			if ($configData[EDIP_CONFIG_REFRESHMETHOD]==EDIP_REFRESHMETHOD_EVENT or
			    $configData[EDIP_CONFIG_REFRESHMETHOD]==EDIP_REFRESHMETHOD_BOTH) {

				$result = IPS_SemaphoreEnter($configId, 0);
				if (!$result) {
				   IPSLogger_Trc(__file__, "Refresh is already in Process - ignore ...");
					return;
				}
				IPSLogger_Trc(__file__, "Refresh EDIP by Event=$eventName");
				IPS_Sleep(1000);
				IPS_SemaphoreLeave($configId);


				$instanceClass = $configData[EDIP_CONFIG_CLASSNAME];
				$instanceId    = IPS_GetObjectIDByIdent($configId, EDIP_ID_PROGRAM);

				include_once $instanceClass.'.class.php';
				$edip = new $instanceClass($instanceId);
				$edip->RefreshDisplay();
			}
		}
	}

	/** @}*/
?>