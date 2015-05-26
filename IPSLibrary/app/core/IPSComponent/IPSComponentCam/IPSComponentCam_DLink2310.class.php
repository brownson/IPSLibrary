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
     * @file          IPSComponentCam_DLink2310.class.php
     * @author        tommy86
     *
     */

    /**
    * @class IPSComponentCam_DLink2310
    *
    * Definiert ein IPSComponentCam Object, das die Funktionen einer Cam Componente für eine 
    * DLink2310 Kamera implementiert
    *
    * @author tommy86
    * @version
    *   Version 2.50.1, 24.05.2013<br/>
    */

    IPSUtils_Include ('IPSComponentCam.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentCam');

    class IPSComponentCam_DLink2310 extends IPSComponentCam {

        private $ipAddress;
        private $username;
        private $password;

        /**
         * @public
         *
         * Initialisierung eines IPSComponentCam_DLink2310 Objektes
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
            //$url = 'http://'.$this->ipAddress.'/MJPEG.cgi?user='.$this->username.'&password='.$this->password;

            $url = 'http://'.$this->username.':'.$this->password.'@'.$this->ipAddress.'/video/mjpg.cgi?profileid=1';
            switch ($size) {
                case  IPSCOMPONENTCAM_SIZE_SMALL:
                    $url .= ''; // Not supported
                    break;
                case  IPSCOMPONENTCAM_SIZE_MIDDLE:
                    $url .= ''; // Not supported
                    break;
                case  IPSCOMPONENTCAM_SIZE_LARGE:
                    $url .= ''; // Not supported
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
            $url = 'http://'.$this->username.':'.$this->password.'@'.$this->ipAddress.'/image/jpeg.cgi';
            switch ($size) {
                case  IPSCOMPONENTCAM_SIZE_SMALL:
                    $url .= ''; // Not supported
                    break;
                case  IPSCOMPONENTCAM_SIZE_MIDDLE:
                    $url .= ''; // Not supported
                    break;
                case  IPSCOMPONENTCAM_SIZE_LARGE:
                    $url .= ''; // Not supported
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
            $url = '';
            switch ($urlType) {
                    case IPSCOMPONENTCAM_URL_MOVELEFT:
                    case IPSCOMPONENTCAM_URL_MOVERIGHT: 
                    case IPSCOMPONENTCAM_URL_MOVEUP:
                    case IPSCOMPONENTCAM_URL_MOVEDOWN: 
                    case IPSCOMPONENTCAM_URL_MOVEHOME:
                    case IPSCOMPONENTCAM_URL_PREDEFPOS1:
                    case IPSCOMPONENTCAM_URL_PREDEFPOS2:
                    case IPSCOMPONENTCAM_URL_PREDEFPOS3:
                    case IPSCOMPONENTCAM_URL_PREDEFPOS4:
                    case IPSCOMPONENTCAM_URL_PREDEFPOS5:
                        $url = $url.''; // Not supported
                        break;
                default:
                    trigger_error('Diese Funktion ist für eine DLink2310 Kamera noch NICHT implementiert !!!');
            }
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