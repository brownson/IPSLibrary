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
     * @file          IPSComponentCam_Cisco.class.php
     * @author        Andreas Brauneis
     *
     */

    /**
    * @class IPSComponentCam_Cisco
    *
    * Definiert ein IPSComponentCam Object, das die Funktionen einer Cam Componente für eine
    * Cisco Kamera implementiert
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 10.08.2013<br/>
    */

    IPSUtils_Include ('IPSComponentCam.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentCam');

    class IPSComponentCam_Cisco extends IPSComponentCam {

        private $ipAddress;
        private $username;
        private $password;

        /**
         * @public
         *
         * Initialisierung eines IPSComponentCam_Cisco Objektes
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
            $url = 'http://'.$this->username.':'.$this->password.'@'.$this->ipAddress.'/img/mjpeg.cgi';

            switch ($size) {
                case  IPSCOMPONENTCAM_SIZE_SMALL:
                    $url .= '?size=1&framerate=30';
                    break;
                case  IPSCOMPONENTCAM_SIZE_MIDDLE:
                    $url .= '?size=2&framerate=30';
                    break;
                case  IPSCOMPONENTCAM_SIZE_LARGE:
                    $url .= '?size=3&framerate=30';
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
            $url = 'http://'.$this->username.':'.$this->password.'@'.$this->ipAddress.'/img/snapshot.cgi';

            switch ($size) {
                case  IPSCOMPONENTCAM_SIZE_SMALL:
                    $url .= '?size=1&quality=3';
                    break;
                case  IPSCOMPONENTCAM_SIZE_MIDDLE:
                    $url .= '?size=2&quality=3';
                    break;
                case  IPSCOMPONENTCAM_SIZE_LARGE:
                    $url .= '?size=3&quality=3';
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
            trigger_error('Diese Funktion ist für eine Cisco Kamera noch NICHT implementiert !!!');
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
                    $return = 160;
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
                    $return = 120;
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
