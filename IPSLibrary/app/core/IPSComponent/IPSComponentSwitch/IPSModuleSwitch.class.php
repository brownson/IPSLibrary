<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleSwitch.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleSwitch
	 *
	 * Definiert ein IPSModuleSwitch Object, das als Wrapper für Schaltgeräte in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */

	abstract class IPSModuleSwitch extends IPSModule {

		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation des aktuellen Zustands 
		 *
		 * @param boolean $state aktueller Status des Gerätes
		 */
		abstract public function SyncState($state);

	}

	/** @}*/
?>