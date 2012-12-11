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

	 /**@addtogroup ipscomponent
	 * @{
	 *
	 * @file          IPSComponentCam.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	define ('IPSCOMPONENTCAM_URL_MOVEHOME',			0);
	define ('IPSCOMPONENTCAM_URL_MOVELEFT',			1);
	define ('IPSCOMPONENTCAM_URL_MOVERIGHT',		2);
	define ('IPSCOMPONENTCAM_URL_MOVEUP',			3);
	define ('IPSCOMPONENTCAM_URL_MOVEDOWN',			4);
	define ('IPSCOMPONENTCAM_URL_PREDEFPOS1',		100);
	define ('IPSCOMPONENTCAM_URL_PREDEFPOS2',		101);
	define ('IPSCOMPONENTCAM_URL_PREDEFPOS3',		102);
	define ('IPSCOMPONENTCAM_URL_PREDEFPOS4',		103);
	define ('IPSCOMPONENTCAM_URL_PREDEFPOS5',		104);

	define ('IPSCOMPONENTCAM_SIZE_SMALL',			0);
	define ('IPSCOMPONENTCAM_SIZE_MIDDLE',			1);
	define ('IPSCOMPONENTCAM_SIZE_LARGE',			2);

	/**
    * @class IPSComponentCam
    *
    * Definiert ein IPSComponentCam Object, das als Wrapper für Cam Geräte verschiedener Hersteller 
    * verwendet werden kann.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');

	abstract class IPSComponentCam extends IPSComponent {

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleCam $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		abstract public function HandleEvent($variable, $value, IPSModuleCam $module);

		/**
		 * @public
		 *
		 * Liefert URL des Kamera Live Streams 
		 *
		 * @param integer $size Größe des Streams, mögliche Werte:
		 *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
		 * @return string URL des Streams
		 */
		abstract public function Get_URLLiveStream($size=IPSCOMPONENTCAM_SIZE_MIDDLE);

		/**
		 * @public
		 *
		 * Liefert URL des Kamera Bildes 
		 *
		 * @param integer $size Größe des Bildes, mögliche Werte:
		 *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE 
		 * @return string URL des Bildes
		 */
		abstract public function Get_URLPicture($size=IPSCOMPONENTCAM_SIZE_MIDDLE);

		/**
		 * @public
		 *
		 * Bewegen der Kamera
		 *
		 * @param integer $urlType Type der URL die geliefert werden soll.
		 *                         mögliche Werte: IPSCOMPONENTCAM_URL_MOVEHOME
		                                           IPSCOMPONENTCAM_URL_MOVELEFT
		                                           IPSCOMPONENTCAM_URL_MOVERIGHT
		                                           IPSCOMPONENTCAM_URL_MOVEUP
		                                           IPSCOMPONENTCAM_URL_MOVEDOWN
		                                           IPSCOMPONENTCAM_URL_PREDEFPOS1
		                                           IPSCOMPONENTCAM_URL_PREDEFPOS2
		                                           IPSCOMPONENTCAM_URL_PREDEFPOS3
		                                           IPSCOMPONENTCAM_URL_PREDEFPOS4
		                                           IPSCOMPONENTCAM_URL_PREDEFPOS5
		 */
		abstract public function Get_URL($urlType);


		/**
		 * @public
		 *
		 * Liefert Breite des Kamera Bildes 
		 *
		 * @param integer $size Größe des Bildes, mögliche Werte:
		 *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE 
		 * @return integer Breite des Bildes in Pixel
		 */
		abstract public function Get_Width($size=IPSCOMPONENTCAM_SIZE_MIDDLE);

		/**
		 * @public
		 *
		 * Liefert Höhe des Kamera Bildes 
		 *
		 * @param integer $size Größe des Bildes, mögliche Werte:
		 *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE 
		 * @return integer Höhe des Bildes in Pixel
		 */
		abstract public function Get_Height($size=IPSCOMPONENTCAM_SIZE_MIDDLE);
	}

	/** @}*/
?>