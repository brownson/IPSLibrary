<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	 * @file          IPSModuleAVControl.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	/**
	 * @class IPSModuleAVControl
	 *
	 * Definiert ein IPSModuleAVControl Object, das die Rückmeldung von Audio/Video Receiver Komponenten
	 * an Module der IPSLibrary erlaubt.
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	IPSUtils_Include ('IPSModule.class.php', 'IPSLibrary::app::core::IPSComponent');

	abstract class IPSModuleAVControl extends IPSModule {

		/**
		 * @public
		 *
		 * Synchronisation Ein/Ausschalten eines Raumes/Ausgangs
		 *
		 * @param boolean $value Wert für Power (Wertebereich false=Off, true=On)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		abstract public function SyncPower($value, $outputId, IPSComponentAVControl $component);

		/**
		 * @public
		 *
		 * Synchronisation der Lautstärke für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		abstract public function SyncVolume($value, $outputId, IPSComponentAVControl $component);

		/**
		 * @public
		 *
		 * Synchronisation des Mutings für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		abstract public function SyncMute($value, $outputId, IPSComponentAVControl $component);

		/**
		 * @public
		 *
		 * Synchronisation der Balance für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		abstract public function SyncBalance($value, $outputId, IPSComponentAVControl $component);

		/**
		 * @public
		 *
		 * Synchronisation des Eingangs/Source für einen Ausgang
		 *
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param integer $value Eingang der gesetzt werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		abstract public function SyncSource($value, $outputId, IPSComponentAVControl $component);
		/**
		 * @public
		 *
		 * Synchronisation der Höhen für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		abstract public function SyncTreble($value, $outputId, IPSComponentAVControl $component);

		/**
		 * @public
		 *
		 * Synchronisation der Mitten für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		abstract public function SyncMiddle($value, $outputId, IPSComponentAVControl $component) ;

		/**
		 * @public
		 *
		 * Synchronisation der Bässe für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		abstract public function SyncBass($value, $outputId, IPSComponentAVControl $component);


	}

	/** @}*/
?>