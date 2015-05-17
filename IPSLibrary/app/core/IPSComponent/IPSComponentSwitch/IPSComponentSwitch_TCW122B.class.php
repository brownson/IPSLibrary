 <?
    /**@addtogroup ipscomponent
     * @{
     *
      *
     * @file          IPSComponentSwitch_TCW122B.class.php
     * @author        Christian Lechner
     *
     *
     */

   /**
    * @class IPSComponentSwitch_TCW122B
    *
    * Definiert ein IPSComponentSwitch_TCW122B Object, das ein IPSComponentSwitch Object für TCW122B implementiert.
    *
    * @author Christian Lechner
    * @version
    * Version x.xx.x, 15.05.2015<br/>
    */

    IPSUtils_Include ('IPSComponentSwitch.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

    class IPSComponentSwitch_TCW122B extends IPSComponentSwitch {

        private $IPAddress;
        private $Relais;

        /**
         * @public
         *
         * Initialisierung eines IPSComponentSwitch_TCW122B Objektes
         *
         * @param string $IPAddress IP Adresse des Homematic Devices
         * @param integer $Relay welches Relais geschalten werden soll
         */
        public function __construct($IPAddress, $Relais) {
            $this->IPAddress     = $IPAddress;
            $this->Relais = $Relais;
        }

        /**
         * @public
         *
         * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event
         * an das entsprechende Module zu leiten.
         *
         * @param integer $variable ID der auslösenden Variable
         * @param string $value Wert der Variable
         * @param IPSModuleSwitch $module Module Object an das das aufgetretene Event weitergeleitet werden soll
         */
        public function HandleEvent($variable, $value, IPSModuleSwitch $module){
            $module->SyncState($value, $this);
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
            return get_class($this).','.$this->IPAddress.','.$this->Relais;
        }
        /**
        *  @brief Sends command to TCW122B using the XML interface
        *
        *  @param [in] $IP IP Adresse des TCW122B
        *  @param [in] $rel Relais Kanal des TCW122B
        *  @param [in] $val Value
        *  @return Gibt den Status des Relais zurück
        *
        */
        private function TCW_SendCommand($IP, $rel, $val) {
             IPS_LogMessage("IPAdresse", $IP);
             IPS_LogMessage("Relais", $rel);
             IPS_LogMessage("State", $val);
                 If ($val) {
                $TCWReturn = Sys_GetURLContent("http://".$IP."/status.xml?r".$rel."=1");
                 }
                 Else {
                   $TCWReturn = Sys_GetURLContent("http://".$IP."/status.xml?r".$rel."=0");
                 }
            $xml = simplexml_load_string($TCWReturn) or die ("Feed not loading");
                Switch($rel) {
                    Case 1:
                        If ($xml->Relay1 == "ON") {
                            Return TRUE;
                        }
                        Else {
                            Return FALSE;
                        }
                        Break;
                    Case 2:
                        If ($xml->Relay2 == "ON") {
                            Return TRUE;
                        }
                        Else {
                            Return FALSE;
                        }
                        Break;
                  }
        }
        /**
         * @public
         *
         * Zustand Setzen
         *
         * @param boolean $value Wert für Schalter
         */
        public function SetState($value, $onTime = false) {
            echo $this->IPAddress;
            echo $this->Relais;
            echo $value;
             $this->TCW_SendCommand($this->IPAddress, $this->Relais, $value);
        }

        /**
         * @public
         *
         * Liefert aktuellen Zustand
         *
         * @return boolean aktueller Schaltzustand
         */
        public function GetState() {
            $TCWReturn = Sys_GetURLContent("http://".$this->IPAddress."/status.xml");
         $xml = simplexml_load_string($TCWReturn) or die ("Feed not loading");
            Switch($this->Relais) {
               Case 1:
                       If ($xml->Relay1 == "ON") {
                            Return TRUE;
                        }
                        Else {
                            Return FALSE;
                        }
                        Break;
                Case 2:
                       If ($xml->Relay2 == "ON") {
                            Return TRUE;
                        }
                        Else {
                            Return FALSE;
                        }
                        Break;
            }
        }
    }

    /** @}*/
?> 