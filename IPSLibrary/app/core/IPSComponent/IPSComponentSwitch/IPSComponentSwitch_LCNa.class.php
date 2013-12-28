<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentSwitch_LCNa.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentSwitch_LCNa
    *
    * Definiert ein IPSComponentSwitch_LCNa Object, das ein IPSComponentSwitch Object für einen LCN analog Ausgang implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 28.12.2013<br/>
    */

	IPSUtils_Include ('IPSComponentSwitch.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	class IPSComponentSwitch_LCNa extends IPSComponentSwitch {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentSwitch_LCNa Objektes
		 *
		 * @param integer $instanceId InstanceId des LCN Devices
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
		 * @param IPSModuleSwitch $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleSwitch $module) {
			if ($value > 0)
				$module->SyncState(true, $this);
			else
				$module->SyncState(false, $this);
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
		 * @param boolean $value Wert für Schalter
		 * @param integer $onTime Zeit in Sekunden nach der der Aktor automatisch ausschalten soll (nicht unterstützt)
		 */
		public function SetState($value, $onTime=false) {
			if ($value)
				LCN_SetIntensity($this->instanceId, 100, 0.2);
			else
				LCN_SetIntensity($this->instanceId, 0, 0.2);
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Zustand
		 *
		 * @return boolean aktueller Schaltzustand  
		 */
		public function GetState() {
			GetValue(IPS_GetVariableIDByName('Intensity', $this->instanceId));
		}

	}

	/** @}*/
?>