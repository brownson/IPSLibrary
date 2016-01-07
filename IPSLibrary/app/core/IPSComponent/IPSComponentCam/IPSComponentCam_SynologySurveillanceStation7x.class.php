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
     * @file          IPSComponentCam_SynologySurveillanceStation7x.class.php
     * @author        Bayaro
     * @comment       Diese Class funktioniert bei der Surveillance Station 7.x (evtl. auch bei höheren Versionen)
     *
     */

    /**
    * @class IPSComponentCam_SynologySurveillanceStation7x
    *
    * Definiert ein IPSComponentCam Object, das die Funktionen einer Cam Componente für eine
    * Kamera aus der Synology Surveillance Station implementiert
    *
    * @author Bayaro
    * @version
    *   Version 1.01, 05.07.2015<br/>
    */

    IPSUtils_Include ('IPSComponentCam.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentCam');
    
    class IPSComponentCam_SynologySurveillanceStation7x extends IPSComponentCam {
        private $ipAddress;
        private $port;
        private $username;
        private $password;
        private $cameraID;


          /**
         * @public
         *
         * Authentifizierung an der Synology Surveillance Station WebAPI und auslesen der SID (SessionID)
         */
        public function Get_SSSID($ipAddress, $port, $username, $password) {
                $json = Sys_GetURLContent('http://'.$ipAddress.':'.$port.'/webapi/auth.cgi?api=SYNO.API.Auth&method=Login&version=3&account='.$username.'&passwd='.$password.'&session=SurveillanceStation&format=sid');
                $obj = json_decode($json, true);
                @$sid = $obj["data"]["sid"];
                IPS_Sleep(300);
                  $this->SSSID = $sid;
          }


          /**
         * @public
         *
         * Initialisierung eines IPSComponentCam_SynologySurveillanceStation7x Objektes
         *
         * @param string $ipAddress IP Adresse der Synology Surveillance Station
         * @param string $Port der Synology Surveillance Station
         * @param string $username Username für Kamera Zugriff
         * @param string $password Passwort für Kamera Zugriff
         * @param string $cameraID ID der Kamera in der Surveillance Station für Kamera Zugriff (1. Kamera = 1, 2. Kamera = 2, ...)
         */
        public function __construct($ipAddress, $port, $username, $password, $cameraID) {
            $this->ipAddress  = $ipAddress;
            $this->port       = $port;
            $this->username   = $username;
            $this->password   = $password;
            $this->cameraID   = $cameraID;
            IPSComponentCam_SynologySurveillanceStation7x::Get_SSSID($ipAddress, $port, $username, $password);   // so funktioniert alles außer PTZ
        }

            /**
         * @public
         *
         * Funktion liefert String IPSComponent Constructor String.
         * String kann dazu benutzt werden, das Object mit der IPSComponent::CreateObjectByParams
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

            $url = 'http://'.$this->ipAddress.':'.$this->port.'/webapi/SurveillanceStation/videoStreaming.cgi?api=SYNO.SurveillanceStation.VideoStream&version=1&method=Stream&cameraId='.$this->cameraID.'&format=mjpeg&_sid='.$this->SSSID;

            switch ($size) {
                case  IPSCOMPONENTCAM_SIZE_SMALL:
                    $url .= '';
                    break;
                case  IPSCOMPONENTCAM_SIZE_MIDDLE:
                    $url .= '';
                    break;
                case  IPSCOMPONENTCAM_SIZE_LARGE:
                    $url .= '';
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

            $url = 'http://'.$this->ipAddress.':'.$this->port.'/webapi/_______________________________________________________entry.cgi?api=SYNO.SurveillanceStation.Camera&version=1&method=GetSnapshot&preview=true&camStm=1&cameraId='.$this->cameraID.'&_sid='.$this->SSSID;
            
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

            // IDs der Presets auslesen
            $json = Sys_GetURLContent('http://'.$this->ipAddress.':'.$this->port.'/webapi/_______________________________________________________entry.cgi?api=SYNO.SurveillanceStation.PTZ&method=ListPreset&version=3&cameraId='.$this->cameraID.'&_sid='.$this->SSSID);
            $obj = json_decode($json, true);
            @$presets = $obj["data"]["presets"];

            $url = 'http://'.$this->ipAddress.':'.$this->port.'/webapi/_______________________________________________________entry.cgi?api=SYNO.SurveillanceStation.PTZ&version=3&cameraId='.$this->cameraID.'&_sid='.$this->SSSID;

            switch ($urlType) {
                    case IPSCOMPONENTCAM_URL_MOVELEFT:
                        $url .= '&method=Move&direction=dir_4';
                        break;
                    case IPSCOMPONENTCAM_URL_MOVERIGHT:
                        $url .= '&method=Move&direction=dir_0';
                        break;
                    case IPSCOMPONENTCAM_URL_MOVEUP:
                        $url .= '&method=Move&direction=dir_2';
                        break;
                    case IPSCOMPONENTCAM_URL_MOVEDOWN:
                        $url .= '&method=Move&direction=dir_6';
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS1:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][0]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS2:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][1]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS3:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][2]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS4:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][3]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS5:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][4]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS6:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][5]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS7:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][6]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS8:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][7]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS9:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][8]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS10:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][9]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS11:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][10]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS12:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][11]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS13:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][12]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS14:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][13]["id"];
                        Sys_GetURLContent($url);
                        break;
                    case IPSCOMPONENTCAM_URL_PREDEFPOS15:
                        $url = $url.'&method=GoPreset&presetId='.$obj["data"]["presets"][14]["id"];
                        Sys_GetURLContent($url);
                        break;

                default:
                    trigger_error('Diese Funktion ist für die Synology Surveillance Station noch nicht implementiert !!!');
            }
          IPS_LogMessage("...",$url);
          return $url;

    }

        /**
         * @public
         *
         * Liefert die Breite des Kamera Bildes
         *
         * @param integer $size Größe des Bildes, mögliche Werte:
         *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
         * @return integer Breite des Bildes in Pixel
         */
        public function Get_Width($size=IPSCOMPONENTCAM_SIZE_MIDDLE) {
            switch ($size) {
                case  IPSCOMPONENTCAM_SIZE_SMALL:
                    $return = 240;
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
         * Liefert die Höhe des Kamera Bildes
         *
         * @param integer $size Größe des Bildes, mögliche Werte:
         *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
         * @return integer Höhe des Bildes in Pixel
         */
        public function Get_Height($size=IPSCOMPONENTCAM_SIZE_MIDDLE) {
            switch ($size) {
                case  IPSCOMPONENTCAM_SIZE_SMALL:
                    $return = 100;
                    break;
                case  IPSCOMPONENTCAM_SIZE_MIDDLE:
                    $return = 300;
                    break;
                case  IPSCOMPONENTCAM_SIZE_LARGE:
                    $return = 600;
                    break;
                default:
                    trigger_error('Unknown Size '.$size);
            }
            return $return;
        }
    }

    /** @}*/
?> 