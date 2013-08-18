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
	 * @file          IPSComponentCam_AxisM7001.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	/**
    * @class IPSComponentCam_AxisM7001
    *
    * Definiert ein IPSComponentCam Object, das die Funktionen einer Cam Componente für eine
    * Axis Kamera implementiert
    *
    * @author "OnkelDirk"
    * @version
    *   Version 2.50.1, 03.06.2013<br/>
    */

	IPSUtils_Include ('IPSComponentCam.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentCam');

	class IPSComponentCam_AxisM7001 extends IPSComponentCam {

		private $ipAddress;
		private $username;
		private $password;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentCam_AxisM7001 Objektes
		 *
		 * @param string $ipAddress IP Adresse der Kamera
		 * @param string $username Username für Kamera Zugriff
		 * @param string $password Passwort für Kamera Zugriff
		 */
		public function __construct($ipAddress, $username, $password) {
			$this->ipAddress  = $ipAddress;
			$this->username   = $username;
			$this->password   = $password;
		}

			/**
		 * @public
		 *
		 * Funktion liefert String IPSComponent Constructor String.
		 * String kann dazu benützt werden, das Object mit der IPSComponent::CreateObjectByParams
		 * wieder neu zu erzeugen.
		 *
		 * @return string Parameter String des IPSComponent Object
		 */
		public function GetComponentParams() {
			return get_class($this).','.$this->instanceId;
		}

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
		public function HandleEvent($variable, $value, IPSModuleCam $module) {
			$name = IPS_GetName($variable);
			throw new IPSComponentException('Event Handling NOT supported for Variable '.$variable.'('.$name.')');
		}

		/**
		 * @public
		 *
		 * Liefert URL des Kamera Live Streams
		 *
		 * @param integer $size Größe des Streams, mögliche Werte:
		 *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
		 * @return string URL des Streams
		 */
		public function Get_URLLiveStream($size=IPSCOMPONENTCAM_SIZE_MIDDLE) {
			$url = 'http://'.$this->username.':'.$this->password.'@'.$this->ipAddress.'/axis-cgi/mjpg/video.cgi';
			switch ($size) {
				case  IPSCOMPONENTCAM_SIZE_SMALL:
					$url .= '?resolution=CIF';
					break;
				case  IPSCOMPONENTCAM_SIZE_MIDDLE:
					$url .= '?resolution=4CIF';
					break;
				case  IPSCOMPONENTCAM_SIZE_LARGE:
					$url .= '?resolution=D1';
					break;
				default:
					trigger_error('Unknown Size '.$size);
			}
			return $url;
		}

		/**
		 * @public
		 *
		 * Liefert URL des Kamera Bildes
		 *
		 * @param integer $size Größe des Bildes, mögliche Werte:
		 *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
		 * @return string URL des Bildes
		 */
//http://video-m7001.narwald-home.de/axis-cgi/jpg/image.cgi?resolution=D1&clock=0&date=0&text=0&nocache=1370185453237&camera=1
		public function Get_URLPicture($size=IPSCOMPONENTCAM_SIZE_MIDDLE) {
			$url = 'http://'.$this->username.':'.$this->password.'@'.$this->ipAddress.'/axis-cgi/jpg/image.cgi';
			switch ($size) {
				case  IPSCOMPONENTCAM_SIZE_SMALL:
					$url .= '?resolution=176x144';
					break;
				case  IPSCOMPONENTCAM_SIZE_MIDDLE:
					$url .= '?resolution=352x288';
					break;
				case  IPSCOMPONENTCAM_SIZE_LARGE:
					$url .= '?resolution=720x576';
					break;
				default:
					trigger_error('Unknown Size '.$size);
			}
			return $url;
		}

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
		public function Get_URL($urlType) {
			trigger_error('Diese Funktion ist für eine Axis Kamera noch NICHT implementiert !!!');
		}

		/**
		 * @public
		 *
		 * Liefert Breite des Kamera Bildes
		 *
		 * @param integer $size Größe des Bildes, mögliche Werte:
		 *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
		 * @return integer Breite des Bildes in Pixel
		 */
		public function Get_Width($size=IPSCOMPONENTCAM_SIZE_MIDDLE) {
			switch ($size) {
				case  IPSCOMPONENTCAM_SIZE_SMALL:
					$return = 320;
					break;
				case  IPSCOMPONENTCAM_SIZE_MIDDLE:
					$return = 640;
					break;
				case  IPSCOMPONENTCAM_SIZE_LARGE:
					$return = 1024;
					break;
				default:
					trigger_error('Unknown Size '.$size);
			}
			return $return;
		}

		/**
		 * @public
		 *
		 * Liefert Höhe des Kamera Bildes
		 *
		 * @param integer $size Größe des Bildes, mögliche Werte:
		 *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
		 * @return integer Höhe des Bildes in Pixel
		 */
		public function Get_Height($size=IPSCOMPONENTCAM_SIZE_MIDDLE) {
			switch ($size) {
				case  IPSCOMPONENTCAM_SIZE_SMALL:
					$return = 240;
					break;
				case  IPSCOMPONENTCAM_SIZE_MIDDLE:
					$return = 480;
					break;
				case  IPSCOMPONENTCAM_SIZE_LARGE:
					$return = 768;
					break;
				default:
					trigger_error('Unknown Size '.$size);
			}
			return $return;
		}
	}

	/** @}*/
?>