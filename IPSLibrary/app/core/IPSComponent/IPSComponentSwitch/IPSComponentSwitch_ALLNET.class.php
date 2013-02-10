<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentSwitch_ALLNET.class.php
	 * @author        Juergen Gerharz      
	 *
	 *
	 */

   /**
    * @class IPSComponentSwitch_ALLNET
    *
    * Definiert ein IPSComponentSwitch_ALLNET Object, das ein IPSComponentSwitch Object für ALLNET implementiert.
    *
    * @author Juergen Gerharz
    * @version
    * Version 2.50.1, 06.02.2013<br/>
    */

	IPSUtils_Include ('IPSComponentSwitch.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	class IPSComponentSwitch_ALLNET extends IPSComponentSwitch {

		private $instanceId;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentSwitch_ALLNET Objektes
		 *
		 * @param integer $instanceId InstanceId des ALLNET Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
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
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event 
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleSwitch $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleSwitch $module){
			$module->SyncState($value, $this);
		}

		/**
		 * @public
		 *
		 * Zustand Setzen 
		 *
		 * @param boolean $value Wert für Schalter
		 */
		public function SetState($value) {
			ALL_SwitchMode($this->instanceId, $value);
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Zustand
		 *
		 * @return boolean aktueller Schaltzustand  
		 */
		public function GetState() {
		  $value = GetValueBoolean(IPS_GetObjectIDByIdent("StatusVariable",$this->instanceId)); 
			return $value;
		}

	}

	/** @}*/
?>