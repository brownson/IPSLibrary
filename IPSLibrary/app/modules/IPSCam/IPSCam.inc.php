<?
	/*
	 * This file is part of the IPSLibrary.
	 *
	 * The IPSLibrary is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published
	 * by the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * The IPSLibrary is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
	 */    

	/**@defgroup ipscam IPSCam
	 * @ingroup modules
	 * @{
	 *
	 * @file          IPSCam.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.08.2012<br/>
	 *
	 * IPSCam API
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSComponent.class.php",           "IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSModule.class.php",              "IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSCam_Constants.inc.php",         "IPSLibrary::app::modules::IPSCam");
	IPSUtils_Include ("IPSCam_Configuration.inc.php",     "IPSLibrary::config::modules::IPSCam");
	IPSUtils_Include ("IPSCam_Custom.inc.php",            "IPSLibrary::config::modules::IPSCam");
	IPSUtils_Include ("IPSCam_Manager.class.php",         "IPSLibrary::app::modules::IPSCam");

	/**
	 * Selektiert die spezifizierte Kamera und schaltet auf den gewünschten Mode um
	 *
	 * @param int $cameraIdx Index der Kamera
	 * @param int $mode Kamera Modus
	 */
	function IPSCam_ActivateCam($cameraIdx, $mode) {
		$camManager = new IPSCam_Manager();
		$camManager->ActivateCamera($cameraIdx, $mode);
	}
	
	/**
	 * Liefert den Camera Index (Position im Konfigurations Array) einer bestimmten Kamera
	 * Dieser Index wird zur Ansteuerung der meisten IPSCam Funktionen benötigt.
	 *
	 * @param string $name Name der Kamera
	 * @return int Index der Kamera
	 */
	function IPSCam_GetCamIdxByName($name) {
		$config             = IPSCam_GetConfiguration();
		foreach ($config as $idx=>$data) {
			if ($data[IPSCAM_PROPERTY_NAME]==$name) {
				return $idx;
			}
		}
		trigger_error('Camera with Name "'.$name.'" could NOT be found in Configuration Array!!!');
	}

    // Keep for backward compatibility
    function IPSCam_IPSCam_GetCamIdxByName($name) {
		return IPSCam_GetCamIdxByName($name);
	}

	/**
	 * Aktualisiert das entsprechende Kamera Bild, wenn der Kamera Index nicht gesetzt wird, 
	 * wird das aktuell gewählte Kamera Bild aktualisiert
	 *
	 * @param int $cameraIdx Index der Kamera
	 */
	function IPSCam_RefreshPicture($cameraIdx=null) {
		$camManager = new IPSCam_Manager();
		$camManager->PictureRefresh($cameraIdx);
	}

	/**
	 * Speichert ein Bild der entsprechende Kamera , wenn der Kamera Index nicht gesetzt wird, 
	 * wird das aktuell gewählte Kamera Bild gespeichert
	 *
	 * @param int $cameraIdx Index der Kamera
	 * @return string Name des gespeicherten Bildes bzw. false wenn die Kamera nicht in Betrieb ist.
	 */
	function IPSCam_StorePicture($cameraIdx=null) {
		$camManager = new IPSCam_Manager();
		return $camManager->PictureStore($cameraIdx);
	}


    /** @}*/
?>