<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	 * @file          IPSComponentAVControl.class.php
	 * @author        Andreas Brauneis
	 *
	 */

   /**
    * @class IPSComponentAVControl
    *
    * Definiert ein IPSComponentAVControl Object, das als Wrapper für Audio/Video Receiver verschiedener Hersteller 
    * verwendet werden kann.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	abstract class IPSComponentAVControl extends IPSComponent {

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleAVControl $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		abstract public function HandleEvent($variable, $value, IPSModuleAVControl $module);

		/**
		 * @public
		 *
		 * Ein/Ausschalten eines Raumes/Ausgangs
		 *
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param boolean $value Wert für Power (Wertebereich false=Off, true=On)
		 */
		abstract public function SetPower($outputId, $value);

		/**
		 * @public
		 *
		 * Retourniert Power Zustand eines Raumes
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return boolean Wert der Lautstärke (Wertebereich false=Off, true=On)
		 */
		abstract public function GetPower($outputId);


		/**
		 * @public
		 *
		 * Setzen der Lautstärke für einen Ausgang
		 *
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 */
		abstract public function SetVolume($outputId, $value);

		/**
		 * @public
		 *
		 * Retourniert aktuelle Lautstärke eines Raumes
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return integer Wert der Lautstärke (Wertebereich 0 - 100)
		 */
		abstract public function GetVolume($outputId);

		/**
		 * @public
		 *
		 * Setzen des Eingangs/Source für einen Ausgang
		 *
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param integer $value Eingang der gesetzt werden soll (Wertebereich 0 - x)
		 */
		abstract public function SetSource($outputId, $value);

		/**
		 * @public
		 *
		 * Retourniert aktuellen Eingang eines Raumes
		 *
		 * @param integer $outputId Ausgang (Wertebereich 0 - x)
		 * @return integer Eingang der gerade gewählt ist (Wertebereich 0 - x)
		 */
		abstract public function GetSource($outputId);

	}

	/** @}*/
?>