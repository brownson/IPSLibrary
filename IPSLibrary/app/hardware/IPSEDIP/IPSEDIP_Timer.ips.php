<?
	/**@addtogroup ipsedip
	 * @{
	 *
	 * Timer Script
	 *
	 * @file          IPSEDIP_Timer.ips.php
	 * @author        Andreas Brauneis
	 * @author        André Czwalina
	 * @version
	 * Version 2.50.2, 16.04.2012<br/>
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


// Backlight Funktionen
			$eventName 		= IPS_GetName($_IPS['EVENT']);				                                                								
			$backlightId   = IPS_GetObjectIDbyIdent(EDIP_VAR_BACKLIGHT, $instanceId);           															

			if ($eventName == $configData[EDIP_CONFIG_NAME].'_'.EDIP_CONFIG_BACKLIGHT_TIMER){          													
				IPS_SetEventActive($_IPS['EVENT'],false);           									  																
				if (GetValue($backlightId) > $configData[EDIP_CONFIG_BACKLIGHT_LOW]) SetValue($backlightId,$configData[EDIP_CONFIG_BACKLIGHT_LOW]); 
			}          									  																															

// AutoRoot Funktion
			if ($eventName == $configData[EDIP_CONFIG_NAME].'_'.EDIP_CONFIG_ROOT_TIMER and $configData[EDIP_CONFIG_ROOT_TIMER] > 0){         
				$currentId   = IPS_GetObjectIDbyIdent(EDIP_VAR_CURRENT, $instanceId);           																
				$root   	= GetValue(IPS_GetObjectIDbyIdent(EDIP_VAR_ROOT, $instanceId));           															
				IPS_SetEventActive($_IPS['EVENT'],false);           									  																
				SetValue($currentId, $root);
			}

			include_once $instanceClass.'.class.php';
			$edip = new $instanceClass($instanceId);
			$edip->RefreshDisplay();
		}
	}

	/** @}*/
?>