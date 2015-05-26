<?
    /**@addtogroup ipscomponent
     * @{
     *
      *
     * @file          IPSComponentRGB_Milight.class.php
     * @author        Rüdiger Weronetzki
     *
     * based on IPSComponentRGB_Hue
     */

   /**
    * @class IPSComponentRGB_Milight
    *
    * Definiert ein IPSComponentRGB_Milight Object, das ein IPSComponentRGB Object fuer Milight implementiert.
    *
    * Information 
    * http://www.limitlessled.com/dev/
    *
    * https://code.google.com/p/openhab/source/browse/bundles/binding/org.openhab.binding.milight/src/main/java/org/openhab/binding/milight/internal/MilightBinding.java?spec=svnf1af83ad0fef8a05616de4543f39743b89ef9128&r=f1af83ad0fef8a05616de4543f39743b89ef9128
    * @version
    * Version 2.50.1, 04.03.2015<br/>
    */

    IPSUtils_Include ('IPSComponentRGB.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentRGB');
    IPSUtils_Include ("IPSLogger.inc.php", "IPSLibrary::app::core::IPSLogger");

    class IPSComponentRGB_Milight extends IPSComponentRGB {

        private $bridge;
        private $groupnr;
        
    
        /**
         * @public
         *
         * Initialisierung eines IPSComponentRGB_Milight Objektes
         *
         * @param string $bridge Object ID der UDP Instanz
         * @param string $groupnr Nummer der Lampe
    
         *
         */
        public function __construct($bridge, $groupnr) {
           
            $this->bridge = intval ($bridge);
            $this->groupnr   = $groupnr;
        
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
		   return get_class($this).','.$this->bridge.','.$this->groupnr;
        }

        /**
        *  @brief Sends command to Milight bridge using JSON
        *  
        *  @param [in] $type Type of parameter ([Lights, Bridge]
        *  @param [in] $request [GET,PUT]
        *  @param [in] $cmd Command string
        *  @return Returns the result of the JSON command
        *  
        */
        private function mil_SendLampCommand($power, $brightness = null, $color = null) {
			
			 IPS_LogMessage("Power", $power);
             IPS_LogMessage("Brightness", $brightness);
             IPS_LogMessage("Color", $color);
             IPS_LogMessage("Group", $this->groupnr );
             IPS_LogMessage("Bridge", $this->bridge );
             $cmd = "";
             $cmd2 = "";
             $cmd3 = "";
             $cmd4 = "";
            
            if ($power == false) {
                if ($this->groupnr == "GROUP1") $cmd = "\x46\x00\x55";
                if ($this->groupnr == "GROUP2") $cmd = "\x48\x00\x55";
                if ($this->groupnr == "GROUP3") $cmd = "\x4A\x00\x55";
                if ($this->groupnr == "GROUP4") $cmd = "\x4C\x00\x55";
                
                //UDP RGB Socket öffnen
				IPS_SetProperty($this->bridge, "Open", true);
                //CSCK_SetOpen($this->bridge, true);
                IPS_ApplyChanges($this->bridge);

                USCK_SendText($this->bridge, $cmd);

                //UDP RGP Socket wieder schließen
                //CSCK_SetOpen($this->bridge, false);
				IPS_SetProperty($this->bridge, "Open", false);
                IPS_ApplyChanges($this->bridge);
            }else{
                if ($this->groupnr == "GROUP1"){
                    $cmd = "\x45\x00\x55";
                    $cmd4 = "\xC5\x00\x55";
                }
                if ($this->groupnr == "GROUP2") {
                    $cmd = "\x47\x00\x55";
                    $cmd4 = "\xC7\x00\x55";
                }
                if ($this->groupnr == "GROUP3") {
                    $cmd = "\x49\x00\x55";
                    $cmd4 = "\xC9\x00\x55";
                }
                if ($this->groupnr == "GROUP4") {
                    $cmd = "\x4B\x00\x55";
                    $cmd4 = "\xCB\x00\x55";
                }
                
                $cmd2 = "\x40".chr ($color)."\x55";
                $cmd3 = "\x4e".chr ($brightness)."\x55";

                //UDP RGB Socket öffnen
				IPS_SetProperty($this->bridge, "Open", true);
                //CSCK_SetOpen($this->bridge, true);
                IPS_ApplyChanges($this->bridge);

                USCK_SendText($this->bridge, $cmd); 
                usleep(100000);
                if ($color == -1) {
                    USCK_SendText($this->bridge, $cmd4);
                }else{
                    USCK_SendText($this->bridge, $cmd2);
                }
                usleep(100000);
                USCK_SendText($this->bridge, $cmd);                
                usleep(100000);
                USCK_SendText($this->bridge, $cmd3);
                usleep(100000);

                //UDP RGP Socket wieder schließen
                IPS_SetProperty($this->bridge, "Open", false);
				//CSCK_SetOpen($this->bridge, false);
                IPS_ApplyChanges($this->bridge);
            }
        }
        
        
        /**
         * @public
         *
         * @brief Zustand Setzen 
         *
         * @param boolean $power RGB Gerät On/Off
         * @param integer $color RGB Farben (Hex Codierung)
         * @param integer $level Dimmer Einstellung der RGB Beleuchtung (Wertebereich 0-100)
         */
        public function SetState($power, $color, $level) {
            if (!$power) {
                $this->mil_SendLampCommand($power, 255, 255);
            } else {

               $rotDec = (($color >> 16) & 0xFF);
               $gruenDec = (($color >> 8) & 0xFF);
               $blauDec = (($color >> 0) & 0xFF); 
               $color_array = array($rotDec,$gruenDec,$blauDec);
               
             
               
               //Convert RGB to XY values
               $colorS = 0;
               $colorS = $this->calculateColor($color_array);
               //IPSLight is using percentage in variable Level, MiLight is using [2..27] 
               $level = round($level * 0.25) + 2;
               IPS_LogMessage("level", $level);
               //Send command to Mi lamp
               $this->mil_SendLampCommand($power, $level, $colorS);
            }
        }
        
        
        private function calculateColor($color) {
            
            $rgb = array (
                'red' => $color[0],
                'green' => $color[1],
                'blue' => $color[2]
            );
            //IPS_LogMessage("red", $color[0]);
            $rgbMin = min($rgb);
            $rgbMax = max($rgb);

            if ($rgbMin == $rgbMax) {
                if ($rgbMin == 0 || $rgbMin == 255) return -1;
            }
            
            $hsv = array(
              'hue'   => 0,
              'sat'   => 0,
              'val'   => $rgbMax
            );

            // If v is 0, color is black
            if ($hsv['val'] != 0) {
                // Normalize RGB values to 1
                $rgb['red'] /= $hsv['val'];
                $rgb['green'] /= $hsv['val'];
                $rgb['blue'] /= $hsv['val'];
                $rgbMin = min($rgb);
                $rgbMax = max($rgb);

                // Calculate saturation
                $hsv['sat'] = $rgbMax - $rgbMin;
                //IPS_LogMessage("sat", $hsv['sat']);
                if ($hsv['sat'] == 0) {
                  $hsv['hue'] = 0;
                }else{

                    // Normalize saturation to 1
                    $rgb['red'] = ($rgb['red'] - $rgbMin) / ($rgbMax - $rgbMin);
                    $rgb['green'] = ($rgb['green'] - $rgbMin) / ($rgbMax - $rgbMin);
                    $rgb['blue'] = ($rgb['blue'] - $rgbMin) / ($rgbMax - $rgbMin);
                    $rgbMin = min($rgb);
                    $rgbMax = max($rgb);

                    // Calculate hue
                    if ($rgbMax == $rgb['red']) {
                        $hsv['hue'] = 0.0 + 60 * ($rgb['green'] - $rgb['blue']);
                      if ($hsv['hue'] < 0) {
                          //IPS_LogMessage("huelogic", 2);
                        $hsv['hue'] += 360;
                      }
                    } else if ($rgbMax == $rgb['green']) {
                        //IPS_LogMessage("huelogic", 3);
                      $hsv['hue'] = 120 + (60 * ($rgb['blue'] - $rgb['red']));
                    } else {
                        //IPS_LogMessage("huelogic", 4);
                      $hsv['hue'] = 240 + (60 * ($rgb['red'] - $rgb['green']));
                    }
                }
            }
            $hue = $hsv['hue'];
            //IPS_LogMessage("hue", $hue);

            $milightColorNo = (256 + 176 - intval ($hue / 360.0 * 255.0)) % 256;
            IPS_LogMessage("milightColorNo", $milightColorNo);
            
            return $milightColorNo;
            
        }
        
        
    } 
?>