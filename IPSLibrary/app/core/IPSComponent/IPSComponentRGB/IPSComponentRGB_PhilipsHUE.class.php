<?
    /**@addtogroup ipscomponent
     * @{
     *
      *
     * @file          IPSComponentRGB_PhilipsHUE.class.php
     * @author        Andreas Brauneis
     *
     *
     */

   /**
    * @class IPSComponentRGB_PhilipsHUE
    *
    * Definiert ein IPSComponentRGB_PhilipsHUE Object, das ein IPSComponentRGB Object für PhilipsHUE implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 28.01.2014<br/>
    */

    IPSUtils_Include ('IPSComponentRGB.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentRGB');
    IPSUtils_Include ("IPSLogger.inc.php", "IPSLibrary::app::core::IPSLogger");

    class IPSComponentRGB_PhilipsHUE extends IPSComponentRGB {

        private $bridgeIP;
        private $lampNr;
        private $hueKey;
    
        /**
         * @public
         *
         * Initialisierung eines IPSComponentRGB_PhilipsHUE Objektes
         *
         * @param string $bridgeIP IP Addresse der HUE Lampe
         * @param string $hueKey Key zum Zugriff auf die Lampe
         * @param string $lampNr Nummer der Lampe
         */
        public function __construct($bridgeIP, $hueKey, $lampNr) {
            $this->bridgeIP = $bridgeIP;
            $this->hueKey   = $hueKey;
            $this->lampNr   = $lampNr;
        }

        /**
         * @public
         *
         * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
         * an das entsprechende Module zu leiten.
         *
         * @param integer $variable ID der auslösenden Variable
         * @param string $value Wert der Variable
         * @param IPSModuleRGB $module Module Object an das das aufgetretene Event weitergeleitet werden soll
         */
        public function HandleEvent($variable, $value, IPSModuleRGB $module){
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
            return get_class($this).','.$this->bridgeIP.','.$this->hueKey.','.$this->lampNr;
        }

        /**
         * @private
         *
         * Ansteuerung der HUE Lampe
         *
         */
        private function hue_SendLampCommand($cmd) {
            $json_url = 'http://'.$this->bridgeIP.'/api/'.$this->hueKey.'/lights/'.$this->lampNr.'/state';
            $json_string = '{'.$cmd.'}';

            // Configuring curl 
            $ch = curl_init($json_url);
            $options = array(
                           CURLOPT_RETURNTRANSFER => true,
                           CURLOPT_CUSTOMREQUEST => 'PUT', 
                           CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
                           CURLOPT_POSTFIELDS => $json_string
                           );
            curl_setopt_array($ch, $options);
            IPSLogger_Inf(__file__, 'Send PhilipsHUE: JsonURL='.$json_url.', Command='.$json_string);

            // Execute
            if ($this->bridgeIP <> '') {
                $result = curl_exec($ch);
            }
        }
        

       /**
         * @private
         *
         * Cmd aus Hex zusammensetzen
         * Umrechnung übernommen von http://www.everyhue.com/vanilla/discussion/83/simple-php-gateway/p1
         */
        private function getXYCmdFromHEX($color, $level) {

        // Convert each tuple to decimal
        $hex = sprintf("%06X", $color);
        $r = hexdec(substr($hex, 0, 2))/255;
        $g = hexdec(substr($hex, 2, 2))/255;
        $b = hexdec(substr($hex, 4, 2))/255;
                
        if ($r>0.04045){$rf=pow(($r + 0.055) / (1.0 + 0.055), 2.4);} else {$rf=$r/12.92;};
        if ($r>0.04045) {$gf=pow(($g + 0.055) / (1.0 + 0.055), 2.4);} else {$gf=$g/12.92;};
        if ($r>0.04045) {$bf=pow(($b + 0.055) / (1.0 + 0.055), 2.4);} else {$bf=$b/12.92;};

        $x = $rf * 0.649926 + $gf * 0.103455 + $bf * 0.197109;
        $y = $rf * 0.234327 + $gf * 0.743075 + $bf * 0.022598;
        $z = $rf * 0.000000 + $gf * 0.053077 + $bf * 1.035763;

        $cx = $x / ($x + $y + $z);
        $cy = $y / ($x + $y + $z);

        if (is_nan($cx)) {$cx=0;};
        if (is_nan($cy)) {$cy=0;};
   
        return '"bri":'.$level.', "xy":['.$cx.','.$cy.'], "on":true'; 
   }
        
        /**
         * @public
         *
         * Zustand Setzen 
         *
         * @param boolean $power RGB Gerät On/Off
         * @param integer $color RGB Farben (Hex Codierung)
         * @param integer $level Dimmer Einstellung der RGB Beleuchtung (Wertebereich 0-100)
         */
        public function SetState($power, $color, $level) {
            if (!$power) {
                $cmd = '"bri":255, "ct":0, "hue":0, "sat":0, "on":false';  
            } else {
                $cmd = $this->getXYCmdFromHEX($color, $level);
            }
            $this->hue_SendLampCommand($cmd);
        }

    }
  
    /** @}*/
?>