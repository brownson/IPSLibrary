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

	/**@addtogroup ipscam_configuration
	 * @{
	 *
	 * Es gibt derzeit x Callback Methoden, diese erm�glichen es ...
	 *
	 * Funktionen:
	 *  - function IPSCam_BeforeXXXX()
	 *
	 * @file          IPSCam_Custom.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 09.08.2012<br/>
	 *
	 * Callback Methoden f�r IPSCam
	 *
	 */

	/**
	 * Diese Funktion wird vor dem Speichern eines Bildes aufgerufen
	 *
	 * Hier ist es zum Beispiel m�glich die Stromversorgung und das WLAN f�r eine Kamera einzuschalten.
	 * Oder man kann nach der D�mmerung die Speicherung des Bildes unterbinden.
	 *
	 * Parameters:
	 *   @param integer $cameraIdx  Idx der Kamera in der Konfiguration
	 *   @result boolean TRUE f�r OK, bei FALSE erfolgt keine Speicherung des Bildes
	 *
	 */
	function IPSCam_BeforeStorePicture($cameraIdx) {
		return true;
	}
	/**
	 * Diese Funktion wird nach dem Speichern eines Bildes aufgerufen
	 *
	 * Parameters:
	 *   @param integer $cameraIdx  Idx der Kamera in der Konfiguration
	 *   @result boolean TRUE f�r OK, bei FALSE erfolgt keine Speicherung des Bildes
	 *
	 */
	function IPSCam_AfterStorePicture($cameraIdx) {
		return true;
	}

	/**
	 * Diese Funktion wird vor dem Speichern eines Zeitraffer Bildes aufgerufen
	 *
	 * Analog zur Speicherung eines normalen Bildes, kann auch hier die Stromversorgung und das WLAN f�r
	 * eine Kamera einzuschaltet werden oder w�hrend der Nachtstunden die Aufnahme der Zeitraffer Bilder
	 * unterbunden werden.
	 *
	 * Parameters:
	 *   @param integer $cameraIdx  Idx der Kamera in der Konfiguration
	 *   @result boolean TRUE f�r OK, bei FALSE erfolgt keine Speicherung des Bildes
	 *
	 */
	function IPSCam_BeforeStoreMotion($cameraIdx) {
		return true;
	}
	/**
	 * Diese Funktion wird nach dem Speichern eines Zeitraffer Bildes aufgerufen
	 *
	 * Parameters:
	 *   @param integer $cameraIdx  Idx der Kamera in der Konfiguration
	 *   @result boolean TRUE f�r OK, bei FALSE erfolgt keine Speicherung des Bildes
	 *
	 */
	function IPSCam_AfterStoreMotion($cameraIdx) {
		return true;
	}


	/** @}*/

?>