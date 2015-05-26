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
	 * @file          IPSComponentCam_Instar5907.class.php
	 * @author        Digihouse
	 *
	 */

	/**
    * @class IPSComponentCam_Instar5907
    *
    * Definiert ein IPSComponentCam Object, das die Funktionen einer Cam Componente für eine 
    * Instar5907 Kamera implementiert
    *
    * @author Digihouse
    * @version
    *   Version 2.50.1, 26.05.2015<br/>
    */

	IPSUtils_Include ('IPSComponentCam.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentCam');

	class IPSComponentCam_Instar5907 extends IPSComponentCam {

		private $ipAddress;
		private $username;
		private $password;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentCam_Instar5907 Objektes
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
			$url = 'http://'.$this->ipAddress.'/cgi-bin/hi3510/mjpegstream.cgi?-chn=11&-usr='.$this->username.'&-pwd='.$this->password;  
			switch ($size) {
				case  IPSCOMPONENTCAM_SIZE_SMALL:
					$url .= '&resolution=8';
					break;
				case  IPSCOMPONENTCAM_SIZE_MIDDLE:
					$url .= '&resolution=8';
					break;
				case  IPSCOMPONENTCAM_SIZE_LARGE:
					$url .= '&resolution=32';
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
		public function Get_URLPicture($size=IPSCOMPONENTCAM_SIZE_MIDDLE) {
			$url = 'http://'.$this->ipAddress.'/tmpfs/snap.jpg?usr='.$this->username.'&pwd='.$this->password.'&next_url=snapshot.jpg';  
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
			$url = 'http://'.$this->ipAddress.'/decoder_control.cgi?';
			switch ($urlType) {
					case IPSCOMPONENTCAM_URL_MOVELEFT:
						$url = $url.'command=4&onestep=1';
						break;
					case IPSCOMPONENTCAM_URL_MOVERIGHT: 
						$url = $url.'command=6&onestep=1';
						break;
					case IPSCOMPONENTCAM_URL_MOVEUP:
						$url = $url.'command=0&onestep=1';                  
						break;
					case IPSCOMPONENTCAM_URL_MOVEDOWN: 
						$url = $url.'command=2&onestep=1';
						break;
					case IPSCOMPONENTCAM_URL_MOVEHOME:
						$url = $url.'command=25';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS1:
						$url = $url.'command=31';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS2:
						$url = $url.'command=33';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS3:
						$url = $url.'command=35';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS4:
						$url = $url.'command=37';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS5:
						$url = $url.'command=39';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS6:
						$url = $url.'command=41';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS7:
						$url = $url.'command=43';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS8:
						$url = $url.'command=45';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS9:
						$url = $url.'command=47';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS10:
						$url = $url.'command=49';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS11:
						$url = $url.'command=51';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS12:
						$url = $url.'command=53';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS13:
						$url = $url.'command=55';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS14:
						$url = $url.'command=57';
						break;
					case IPSCOMPONENTCAM_URL_PREDEFPOS15:
						$url = $url.'command=59';
						break;
						
				default:
					trigger_error('Diese Funktion ist für eine Instar5907 Kamera noch NICHT implementiert !!!');
			}
			$url = $url.	'&user='.$this->username.'&pwd='.$this->password ;
			return $url;
		
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
					$return = 320;
					break;
				case  IPSCOMPONENTCAM_SIZE_LARGE:
					$return = 640;
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
					$return = 240;
					break;
				case  IPSCOMPONENTCAM_SIZE_LARGE:
					$return = 480;
					break;
				default:
					trigger_error('Unknown Size '.$size);
			}
			return $return;
		}
	}

	/** @}*/
?>