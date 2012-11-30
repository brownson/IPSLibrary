<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleSwitch_IPSLight.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleSwitch_IPSLight
	 *
	 * Definiert ein IPSModuleSwitch_IPSLight Object, das als Wrapper für Beschattungsgeräte in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	IPSUtils_Include ("IPSLight.inc.php",          "IPSLibrary::app::modules::IPSLight");
	IPSUtils_Include ('IPSModuleSwitch.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	class IPSModuleSwitch_IPSLight extends IPSModuleSwitch {

		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation einer Beleuchtung zu IPSLight
		 *
		 * @param string $state Aktueller Status des Switch
		 */
		public function SyncState($state, IPSComponentSwitch $componentToSync) {
			$componentParamsToSync = $componentToSync->GetComponentParams();
			$deviceConfig          = IPSLight_GetLightConfiguration();
			foreach ($deviceConfig as $deviceIdent=>$deviceData) {
				$componentConfig       = IPSComponent::CreateObjectByParams($deviceData[IPSLIGHT_COMPONENT]);
				$componentParamsConfig = $componentConfig->GetComponentParams();
				if ($componentParamsConfig==$componentParamsToSync) {
					$lightManager = new IPSLight_Manager();
					$lightManager->SynchronizeSwitch($deviceIdent, $state);
				}
			}
		}


	}

	/** @}*/
?>