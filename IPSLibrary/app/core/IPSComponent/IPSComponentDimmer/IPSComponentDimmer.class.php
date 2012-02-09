<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	 * @file          IPSComponentDimmer.class.php
	 * @author        Andreas Brauneis
	 *
	 */

   /**
    * @class IPSComponentDimmer
    *
    * Definiert ein IPSComponentDimmer Object, das als Wrapper für Dimmer Geräte verschiedener Hersteller 
    * verwendet werden kann.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSComponentDimmer extends IPSComponent {

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
		 * @param boolean $power Geräte Power
		 * @param integer $level Wert für Dimmer Einstellung
		 */
		abstract public function SetState($power, $level);

		/**
		 * @public
		 *
		 * Liefert aktuellen Zustand des Dimmers
		 *
		 * @return integer aktueller Dimmer Zustand  
		 */
		abstract public function GetLevel();

		/**
		 * @public
		 *
		 * Liefert aktuellen Power Zustand des Dimmers
		 *
		 * @return boolean Gerätezustand On/Off des Dimmers
		 */
		abstract public function GetPower();
	}

	/** @}*/
?>