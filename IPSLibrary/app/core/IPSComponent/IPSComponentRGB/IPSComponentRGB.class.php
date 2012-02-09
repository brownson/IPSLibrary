<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentRGB.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentRGB
    *
    * Definiert ein IPSComponentRGB Object, das als Wrapper für Dimmer Geräte verschiedener Hersteller 
    * verwendet werden kann.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSComponentRGB extends IPSComponent {

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleDimmer $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		abstract public function HandleEvent($variable, $value, IPSModuleDimmer $module);

		/**
		 * @public
		 *
		 * Zustand Setzen 
		 *
		 * @param boolean $power RGB Gerät On/Off
		 * @param integer $color RGB Farben (Hex Codierung)
		 * @param integer $level Dimmer Einstellung der RGB Beleuchtung (Wertebereich 0-100)
		 */
		abstract public function SetState($power, $color, $level);

	}

	/** @}*/
?>