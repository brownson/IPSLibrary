<?php

	//namespace domizei\koubachi;

	/**@defgroup plugwise_configuration Koubachi Konfiguration
	* @ingroup koubachi
	* @{
	*
	* Konfigurations File fuer Koubachi.
	*
	* @file Plugwise_Configuration.inc.php
	* @author Dominik Zeiger
	* @version Version 0.1, 15.10.2012<br/>
	*
	*/
	
	define("APP_KEY", "appKey");
	define("USER_CREDENTIALS", "userCredentials");
	
	function getConfiguration() {
		return array(
			USER_CREDENTIALS => "UABdsgh045z6ljdfhs",
			APP_KEY => "LKJLJ987H6GLLFK856",
		);
	}
?>