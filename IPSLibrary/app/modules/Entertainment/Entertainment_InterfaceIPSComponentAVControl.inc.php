<?
	/**@addtogroup entertainment_interface
	 * @ingroup entertainment
	 * @{
	 *
	 * @file          Entertainment_InterfaceIPSComponent.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 31.01.2012<br/>
	 *
	 * Anbindung von IPSComponents
	 *
	 */

	include_once "Entertainment.inc.php";
	IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');


	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_IPSComponent_ReceiveData($componentParams, $function, $output, $value) {
		$interfaceList = get_CommunicationConfiguration();

		$interface = '';
		foreach ($interfaceList as $interfaceName=>$interfaceData) {
			if (array_key_exists(c_Property_ComponentParams, $interfaceData)) {
				if ($interfaceData[c_Property_ComponentParams]==$componentParams or 
				    $interfaceData[c_Property_ComponentParams]=='IPSComponentAVControl_AudioMax,null') {
					$interface = $interfaceName;
				}
			}
		}
		Entertainment_ReceiveData(array($interface, $function, $output, $value), c_MessageType_Info);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function Entertainment_IPSComponent_SendData($parameters) {
		$interfaceName  = $parameters[0];
		$function       = $parameters[1];
		$roomId         = $parameters[2];
		$value          = $parameters[3];

		$CommConfig = get_CommunicationConfiguration();
		$componentConstructorParams  = $CommConfig[$interfaceName][c_Property_ComponentParams];

		$component = IPSComponent::CreateObjectByParams($componentConstructorParams);
		$component->$function($roomId, $value);
	}

  /** @}*/
?>