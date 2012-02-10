<?
	/**@addtogroup ipsedip
	 * @{
	 *
	 * EDIP Empfangs Script
	 *
	 * @file          IPSEDIP_Receive.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Dieses Script wird von der EDIP Steuerung aufgerufen, wenn Daten über die serielle Schnittstelle
	 * hereinkommen.
	 *
	 */

	include_once "IPSEDIP.class.php";

	$value      = $_IPS['VALUE'];
	$registerId = $_IPS['INSTANCE'];

	$buffer = RegVar_GetBuffer($registerId);
	if ($buffer <> '') {
		$value = $buffer.$value;
		RegVar_SetBuffer($registerId, '');
	}

	// Search Start of Message
	$stx = strlen($value)-1;
	for ($idx=0 ; $idx<strlen($value) ; $idx++) {
	   if (ord(substr($value, $idx, 1))== 27) {
	      $stx = $idx;
	   }
	}

	$message = substr($value, $stx);

	// Logging
	$log = '';
	for ($idx=0 ; $idx<strlen($value) ; $idx++) {
		$log .= ord(substr($value, $idx, 1)).',';
	}
	$log .= ', Message=';
	for ($idx=0 ; $idx<strlen($message) ; $idx++) {
		$log .= ord(substr($message, $idx, 1)).',';
	}
	IPSLogger_Trc(__file__, 'Received: '.$log);


	// Message beyond minimum Length
	if (strlen($message) < 4) {
	   RegVar_SetBuffer($registerId, $message);
		IPSLogger_Trc(__file__, 'Message beyond minimum Length of 4');
	   return;
	}

	$messageLength = ord(substr($message, 2,2));

	// Message beyond Message Length (esc cmd len data[])
	if (strlen($message) < 3+$messageLength) {
	   RegVar_SetBuffer($registerId, $message);
		IPSLogger_Trc(__file__, 'Message beyond Message Length of '.$messageLength);
	   return;
	}

	foreach (IPSEDIP_GetConfiguration() as $configId=>$configData) {
		if ($configData[EDIP_CONFIG_REGISTER]==$registerId) {
			$instanceClass = $configData[EDIP_CONFIG_CLASSNAME];
			$instanceId    = IPS_GetObjectIDByIdent($configId, EDIP_ID_PROGRAM);

			include_once $instanceClass.'.class.php';
			$edip = new $instanceClass($instanceId);
			$edip->receiveText($message);
		}
	}

	/** @}*/
?>