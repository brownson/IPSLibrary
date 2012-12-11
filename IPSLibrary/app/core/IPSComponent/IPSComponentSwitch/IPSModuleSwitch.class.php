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
	 * Definiert ein IPSModuleSwitch Object, das als Wrapper f�r Schaltger�te in der IPSLibrary
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
		 * Erm�glicht die Synchronisation des aktuellen Zustands 
		 *
		 * @param boolean $state aktueller Status des Ger�tes
		 */
		abstract public function SyncState($state, IPSComponentSwitch $componentToSync);

	}

	/** @}*/
?>