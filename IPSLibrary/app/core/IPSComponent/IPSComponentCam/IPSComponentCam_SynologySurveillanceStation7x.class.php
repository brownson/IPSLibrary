<?php
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
 * @class  IPSComponentCam_SynologySurveillanceStation7x
 *
 * Definiert ein IPSComponentCam Object, das die Funktionen einer Cam Componente für eine
 * Kamera aus der Synology Surveillance Station implementiert
 *
 * @author Bayaro
 * @version
 *   Version 1.01, 05.07.2015<br/>
 */

IPSUtils_Include('IPSComponentCam.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentCam');

class IPSComponentCam_SynologySurveillanceStation7x extends IPSComponentCam
{

    private $ipAddress;

    private $port;

    private $username;

    private $password;

    private $cameraID;

    private $sid;

    private $apiInfo;


    /**
     * @private
     *
     * Authentifizierung an der Synology Surveillance Station WebAPI, auslesen der SID (SessionID) und der API Infos
     *
     * @return bool
     */
    private function Login()
    {
        $fileName     = IPS_GetLogDir() . __CLASS__ . '_' . str_replace('.', '', $this->ipAddress) . '.log';
        $content_json = @file_get_contents($fileName);
        if ($content_json !== false) {
            $content = json_decode($content_json, true);
            if ($content['timestamp'] > strtotime('-10 secs')) {
                $this->sid     = $content['SID'];
                $this->apiInfo = $content['apiInfo'];
                echo time() . ': ' . __CLASS__ . '::' . __FUNCTION__ . ': SID ' . $this->sid . ' cached' . PHP_EOL;
                return true;
            }
        }

        // 1: API Infos auslesen
        $api        = 'SYNO.API.Info';
        $path       = 'query.cgi';
        $method     = 'Query';
        $version    = 1;
        $param_list = ['query' => 'SYNO.API.Auth,SYNO.SurveillanceStation.Info,SYNO.SurveillanceStation.Camera,SYNO.SurveillanceStation.VideoStream,SYNO.SurveillanceStation.PTZ'];

        $response      = $this->httpRequest($path, $api, $method, $version, $param_list);
        $this->apiInfo = json_decode($response, true)['data'];

        // 2: Anmeldung
        $api        = 'SYNO.API.Auth';
        $path       = $this->apiInfo[$api]['path'];
        $method     = 'Login';
        $version    = $this->apiInfo[$api]['maxVersion'];
        $param_list = [
            'account' => $this->username,
            'passwd'  => $this->password,
            'session' => 'SurveillanceStation',
            'format'  => 'sid'];

        $response = $this->httpRequest($path, $api, $method, $version, $param_list);
        $obj      = json_decode($response, true);

        $this->sid = $obj['data']['sid'];
        //echo time() . ': ' . __CLASS__ . '::' . __FUNCTION__ . ': SID ' . $this->sid . ' new' . PHP_EOL;

        file_put_contents($fileName, json_encode(['timestamp' => time(), 'SID' => $this->sid, 'apiInfo' => $this->apiInfo]));

        return true;
    }

    private function getVersionSpecificUrl($path, $api, $method, $version, $param_list){
        $url =
            'http://' . $this->ipAddress . ':' . $this->port . '/webapi/' . $path . '?api=' . $api . '&method=' . $method . '&version=' . $version;
        if (count($param_list)) {
            $url .= '&' . http_build_query($param_list);
        }
        return $url;
    }

    private function httpRequest($path, $api, $method, $version, $param_list)
    {

        $url = $this->getVersionSpecificUrl($path, $api, $method, $version, $param_list);

        $context = stream_context_create(['http'=> ['timeout' => 5]]);
        $ret = @file_get_contents($url, false , $context);

        if (($ret === false) || !json_decode($ret, true)['success']) {
            trigger_error('request failed');
        }

        return $ret;
    }

    /**
     * @public
     *
     * Initialisierung eines IPSComponentCam_SynologySurveillanceStation7x Objektes
     *
     * @param string $ipAddress IP Adresse der Synology Surveillance Station
     * @param string $port      Port der Synology Surveillance Station
     * @param string $username  Username für Kamera Zugriff
     * @param string $password  Passwort für Kamera Zugriff
     * @param string $cameraID  ID der Kamera in der Surveillance Station für Kamera Zugriff (1. Kamera = 1, 2. Kamera = 2, ...)
     */
    public function __construct($ipAddress, $port, $username, $password, $cameraID)
    {
        $this->ipAddress = $ipAddress;
        $this->port      = $port;
        $this->username  = $username;
        $this->password  = $password;
        $this->cameraID  = $cameraID;
        $this->Login();   // todo: es funktioniert alles außer PTZ
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
    public function GetComponentParams()
    {
        return get_class($this) . ',' . $this->ipAddress . ',' . $this->port . ',' . $this->username . ',' . $this->password . ',' . $this->cameraID;
    }

    /**
     * @public
     *
     * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event
     * an das entsprechende Module zu leiten.
     *
     * @param integer      $variable ID der auslösenden Variable
     * @param string       $value    Wert der Variable
     * @param IPSModuleCam $module   Module Object an das das aufgetretene Event weitergeleitet werden soll
     *
     * @throws \IPSComponentException
     */
    public function HandleEvent($variable, $value, IPSModuleCam $module)
    {
        $name = IPS_GetName($variable);
        throw new IPSComponentException('Event Handling NOT supported for Variable ' . $variable . '(' . $name . ')');
    }

    /**
     * @public
     *
     * Liefert URL des Kamera Live Streams
     *
     * @param integer $size Größe des Streams, mögliche Werte:
     *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
     *
     * @return string URL des Streams
     */
    public function Get_URLLiveStream($size = IPSCOMPONENTCAM_SIZE_MIDDLE)
    {

        // VideoStream
        $api        = 'SYNO.SurveillanceStation.VideoStream';
        $path       = $this->apiInfo[$api]['path'];
        $method     = 'Stream';
        $version    = $this->apiInfo[$api]['maxVersion'];
        $param_list = [
            'cameraId'      => $this->cameraID,
            'format' => 'mjpeg',
            '_sid'       => $this->sid];

        return $this->getVersionSpecificUrl($path, $api, $method, $version, $param_list);

    }

    /**
     * @public
     *
     * Liefert URL des Kamera Bildes
     *
     * @param integer $size Größe des Bildes, mögliche Werte:
     *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
     *
     * @return string URL des Bildes
     */
    public function Get_URLPicture($size = IPSCOMPONENTCAM_SIZE_MIDDLE)
    {

        // Camera, GetSnapshot
        $api        = 'SYNO.SurveillanceStation.Camera';
        $path       = $this->apiInfo[$api]['path'];
        $method     = 'GetSnapshot';
        $version    = $this->apiInfo[$api]['maxVersion'];
        $param_list = [
            'cameraId'      => $this->cameraID,
            '_sid'       => $this->sid];

        return $this->getVersionSpecificUrl($path, $api, $method, $version, $param_list);

    }

    /**
     * @public
     *
     * Bewegen der Kamera
     *
     * @param integer $urlType Type der URL die geliefert werden soll.
     *                         mögliche Werte: IPSCOMPONENTCAM_URL_MOVEHOME
     *                         IPSCOMPONENTCAM_URL_MOVELEFT
     *                         IPSCOMPONENTCAM_URL_MOVERIGHT
     *                         IPSCOMPONENTCAM_URL_MOVEUP
     *                         IPSCOMPONENTCAM_URL_MOVEDOWN
     *                         IPSCOMPONENTCAM_URL_PREDEFPOS1
     *                         IPSCOMPONENTCAM_URL_PREDEFPOS2
     *                         IPSCOMPONENTCAM_URL_PREDEFPOS3
     *                         IPSCOMPONENTCAM_URL_PREDEFPOS4
     *                         IPSCOMPONENTCAM_URL_PREDEFPOS5
     *
     * @return string
     */
    public function Get_URL($urlType)
    {

        // IDs der Presets auslesen
        $json = Sys_GetURLContent(
            'http://' . $this->ipAddress . ':' . $this->port
            . '/webapi/_______________________________________________________entry.cgi?api=SYNO.SurveillanceStation.PTZ&method=ListPreset&version=3&cameraId='
            . $this->cameraID . '&_sid=' . $this->sid
        );
        $obj  = json_decode($json, true);
        @$presets = $obj['data']['presets'];

        $url = 'http://' . $this->ipAddress . ':' . $this->port
               . '/webapi/_______________________________________________________entry.cgi?api=SYNO.SurveillanceStation.PTZ&version=3&cameraId='
               . $this->cameraID . '&_sid=' . $this->sid;

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
                $url = $url . '&method=GoPreset&presetId=' . $obj['data']['presets'][0]['id'];
                Sys_GetURLContent($url);
                break;
            case IPSCOMPONENTCAM_URL_PREDEFPOS2:
                $url = $url . '&method=GoPreset&presetId=' . $obj['data']['presets'][1]['id'];
                Sys_GetURLContent($url);
                break;
            case IPSCOMPONENTCAM_URL_PREDEFPOS3:
                $url = $url . '&method=GoPreset&presetId=' . $obj['data']['presets'][2]['id'];
                Sys_GetURLContent($url);
                break;
            case IPSCOMPONENTCAM_URL_PREDEFPOS4:
                $url = $url . '&method=GoPreset&presetId=' . $obj['data']['presets'][3]['id'];
                Sys_GetURLContent($url);
                break;
            case IPSCOMPONENTCAM_URL_PREDEFPOS5:
                $url = $url . '&method=GoPreset&presetId=' . $obj['data']['presets'][4]['id'];
                Sys_GetURLContent($url);
                break;

            default:
                trigger_error('Diese Funktion ist für die Synology Surveillance Station noch nicht implementiert !!!');
        }
        IPS_LogMessage('...', $url);
        return $url;

    }

    /**
     * @public
     *
     * Liefert die Breite des Kamera Bildes
     *
     * @param integer $size Größe des Bildes, mögliche Werte:
     *                      IPSCOMPONENTCAM_SIZE_SMALL, IPSCOMPONENTCAM_SIZE_MIDDLE oder IPSCOMPONENTCAM_SIZE_LARGE
     *
     * @return integer Breite des Bildes in Pixel
     */
    public function Get_Width($size = IPSCOMPONENTCAM_SIZE_MIDDLE)
    {
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
                $return = 0;
                trigger_error('Unknown Size ' . $size);
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
     *
     * @return integer Höhe des Bildes in Pixel
     */
    public function Get_Height($size = IPSCOMPONENTCAM_SIZE_MIDDLE)
    {
        switch ($size) {
            case  IPSCOMPONENTCAM_SIZE_SMALL:
                $return = 100;
                break;
            case  IPSCOMPONENTCAM_SIZE_MIDDLE:
                $return = 480;
                break;
            case  IPSCOMPONENTCAM_SIZE_LARGE:
                $return = 600;
                break;
            default:
                $return = 0;
                trigger_error('Unknown Size ' . $size);
        }
        return $return;
    }
}

/** @}*/
?>