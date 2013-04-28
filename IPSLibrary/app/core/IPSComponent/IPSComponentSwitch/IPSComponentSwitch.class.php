<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentSwitch.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentSwitch
    *
    * Definiert ein IPSComponentSwitch Object, das als Wrapper für Schalt Geräte verschiedener Hersteller 
    * verwendet werden kann.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');

	abstract class IPSComponentSwitch extends IPSComponent {

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
		abstract public function HandleEvent($variable, $value, IPSModuleSwitch $module);

		/**
		 * @public
		 *
		 * Zustand Setzen 
		 *
		 * @param boolean $value Wert für Schalter
		 * @param integer $onTime Zeit in Sekunden nach der der Aktor automatisch ausschalten soll (ACHTUNG: wird nicht von
		 *                        allen Hardware Komponenten unterstützt).
		 */
		abstract public function SetState($value, $onTime=false);

		/**
		 * @public
		 *
		 * Liefert aktuellen Zustand
		 *
		 * @return boolean aktueller Schaltzustand  
		 */
		abstract public function GetState();

	}

	/** @}*/
?>