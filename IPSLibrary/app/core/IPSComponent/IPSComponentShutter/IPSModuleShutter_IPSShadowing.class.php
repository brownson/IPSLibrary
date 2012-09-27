<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleShutter_IPSShadowing.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleShutter_IPSShadowing
	 *
	 * Definiert ein IPSModuleShutter_IPSShadowing Object, das als Wrapper f�r Beschattungsger�te in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	IPSUtils_Include ("IPSShadowing.inc.php", "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ('IPSModuleShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSModuleShutter_IPSShadowing extends IPSModuleShutter {

		/**
		 * @public
		 *
		 * Erm�glicht die Synchronisation der aktuellen Position der Beschattung
		 *
		 * @param string $position Aktuelle Position der Beschattung (Wertebereich 0-100)
		 */
		public function SyncPosition($position, IPSComponentShutter $componentToSync) {
			$componentParamsToSync = $componentToSync->GetComponentParams();
			$deviceConfig          = get_ShadowingConfiguration();
			foreach ($deviceConfig as $deviceIdent=>$deviceData) {
				$componentConfig       = IPSComponent::CreateObjectByParams($deviceData[c_Property_Component]);
				$componentParamsConfig = $componentConfig->GetComponentParams();
				if ($componentParamsConfig==$componentParamsToSync) {
					$categoryIdDevices = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSShadowing.Devices');
					$deviceId = IPS_GetObjectIDByIdent($deviceIdent, $categoryIdDevices);

					$device = new IPSShadowing_Device($deviceId);
					$device->MoveByEvent($position);
				}
			}
		}


	}

	/** @}*/
?>