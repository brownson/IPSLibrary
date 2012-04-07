<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	 * @file          IPSModuleAVControl_Entertainment.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ("Entertainment_InterfaceIPSComponentAVControl.inc.php", "IPSLibrary::app::modules::Entertainment");
	IPSUtils_Include ('IPSModuleAVControl.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentAVControl');

	/**
	 * @class IPSModuleAVControl_Entertainment
	 *
	 * Klasse zur Rückmeldung von Audio/Video Receivern an die Entertainment Steuerung
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	class IPSModuleAVControl_Entertainment extends IPSModuleAVControl {

		/**
		 * @public
		 *
		 * Initialisierung des IPSModuleAVControl_Entertainment
		 *
		 */
		public function __construct() {
		}

		/**
		 * @public
		 *
		 * Synchronisation Ein/Ausschalten eines Raumes/Ausgangs
		 *
		 * @param boolean $value Wert für Power (Wertebereich false=Off, true=On)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		public function SyncPower($value, $outputId, IPSComponentAVControl $component) {
		   Entertainment_IPSComponent_ReceiveData($component->GetComponentParams(), 'SetPower', $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Synchronisation der Lautstärke für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		public function SyncVolume($value, $outputId, IPSComponentAVControl $component) {
		   Entertainment_IPSComponent_ReceiveData($component->GetComponentParams(), 'SetVolume', $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Synchronisation des Mutings für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		public function SyncMute($value, $outputId, IPSComponentAVControl $component) {
			Entertainment_IPSComponent_ReceiveData($component->GetComponentParams(), 'SetMute', $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Synchronisation der Balance für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		public function SyncBalance($value, $outputId, IPSComponentAVControl $component) {
		   Entertainment_IPSComponent_ReceiveData($component->GetComponentParams(), 'SetBalance', $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Synchronisation des Eingangs/Source für einen Ausgang
		 *
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param integer $value Eingang der gesetzt werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		public function SyncSource($value, $outputId, IPSComponentAVControl $component) {
		   Entertainment_IPSComponent_ReceiveData($component->GetComponentParams(), 'SetSource', $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Synchronisation der Höhen für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		public function SyncTreble($value, $outputId, IPSComponentAVControl $component) {
		   Entertainment_IPSComponent_ReceiveData($component->GetComponentParams(), 'SetTreble', $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Synchronisation der Mitten für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		public function SyncMiddle($value, $outputId, IPSComponentAVControl $component) {
		   Entertainment_IPSComponent_ReceiveData($component->GetComponentParams(), 'SetMiddle', $outputId, $value);
		}

		/**
		 * @public
		 *
		 * Synchronisation der Bässe für einen Ausgang
		 *
		 * @param integer $value Wert der Lautstärke (Wertebereich 0 - 100)
		 * @param integer $outputId Ausgang der geändert werden soll (Wertebereich 0 - x)
		 * @param IPSComponentAVControl $component Component Object das einen Werte synchronisieren will
		 */
		public function SyncBass($value, $outputId, IPSComponentAVControl $component) {
		   Entertainment_IPSComponent_ReceiveData($component->GetComponentParams(), 'SetBass', $outputId, $value);
		}


	}

	/** @}*/
?>