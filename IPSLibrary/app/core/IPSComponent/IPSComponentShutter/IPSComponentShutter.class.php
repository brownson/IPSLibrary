<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentShutter
    *
    * Definiert ein IPSComponentShutter Object, das als Wrapper für Rollo Ansteuerungen verschiedener Hersteller 
    * verwendet werden kann.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');

	abstract class IPSComponentShutter extends IPSComponent {

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleShutter $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		abstract public function HandleEvent($variable, $value, IPSModuleShutter $module);

		/**
		 * @public
		 *
		 * Hinauffahren der Beschattung
		 */
		abstract public function MoveUp();

		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		abstract public function MoveDown();

		/**
		 * @public
		 *
		 * Stop
		 */
		abstract public function Stop();

	}

	/** @}*/
?>