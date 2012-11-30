<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentRGB_IPS868.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentRGB_IPS868
    *
    * Definiert ein IPSComponentRGB_IPS868 Object, das ein IPSComponentRGB Object für Homematic implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponentRGB.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentRGB');

	class IPSComponentRGB_IPS868 extends IPSComponentRGB {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentRGB_IPS868 Objektes
		 *
		 * @param integer $instanceId InstanceId des IPS-RGBW868 Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
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
			return get_class($this).','.$this->instanceId;
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
				@PJ_DimRGBW ($this->instanceId, 0, 2, 0, 2, 0, 2, 0, 2);
			} else {
				$red    = floor($color/256/256);
				$green  = floor(($color-$red*256*256)/256);
				$blue   = floor(($color-$red*256*256-$green*256));
				$red    = floor($red*$level/100);
				$green  = floor($green*$level/100);
				$blue   = floor($blue*$level/100);

				@PJ_DimRGBW ($this->instanceId, $red, 2, $green, 2, $blue, 2, 0, 2);
			}
		}

	}

	/** @}*/
?>