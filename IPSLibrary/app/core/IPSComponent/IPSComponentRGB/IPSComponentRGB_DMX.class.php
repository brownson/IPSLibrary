<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentRGB_DMX.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentRGB_DMX
    *
    * Definiert ein IPSComponentRGB_DMX Object, das ein IPSComponentRGB Object für DMX implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 10.01.2014<br/>
    */

	IPSUtils_Include ('IPSComponentRGB.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentRGB');

	class IPSComponentRGB_DMX extends IPSComponentRGB {

		private $instanceId;
		private $channel1;
		private $channel2;
		private $channel3;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentRGB_DMX Objektes
		 *
		 * @param integer $instanceId InstanceId des IPS-RGBW868 Devices
		 */
		public function __construct($instanceId, $channel1=1, $channel2=2, $channel3=3) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			$this->channel1 = $channel1;
			$this->channel2 = $channel2;
			$this->channel3 = $channel3;
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
			return get_class($this).','.$this->instanceId.','.$this->channel1.','.$this->channel2.','.$this->channel3;
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
				DMX_SetValue ($this->instanceId, 0, 0);
			} else {
				$red    = floor($color/256/256);
				$green  = floor(($color-$red*256*256)/256);
				$blue   = floor(($color-$red*256*256-$green*256));
				$red    = floor($red*$level/100);
				$green  = floor($green*$level/100);
				$blue   = floor($blue*$level/100);

				DMX_SetValue ($this->instanceId, $this->channel1, $red);
				DMX_SetValue ($this->instanceId, $this->channel2, $green);
				DMX_SetValue ($this->instanceId, $this->channel3, $blue);
			}
		}

	}

	/** @}*/
?>