<?
	/**@addtogroup ipscomponent
	 * @{
	 *
	 * @file          IPSComponentSwitch_ZW.class.php
	 * @author        Andreas Tibud
	 *
	 */

   /**
    * @class IPSComponentSwitch_ZW
    *
    * Definiert ein IPSComponentSwitch_ZW Object, das ein IPSComponentSwitch Object für Z-Wave implementiert.
    *
    * @author Andreas Tibud
    * @version
    *   Version 2.50.1, 22.04.2012<br/>
    */

	IPSUtils_Include ('IPSComponentSwitch.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');
   
	class IPSComponentSwitch_ZW extends IPSComponentSwitch {

		private $instanceId;
		private $channel;

		// Welche Klassen unterstütz der Schalter?
		private $b_class_basic = false;
		private $b_class_switch = false;
		private $b_class_multi = false;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentSwitch_ZW_Basic Objektes
		 *
		 * @param integer $instanceId InstanceId des Z-Wave Devices
		 */
		public function __construct($instanceId, $channel=0) {
			$this->instanceId = IPSUtil_ObjectIDByPath($instanceId);
			// Bei Multiinstanz fähigen Schalter gleich den Kanal merken
			$this->channel = (int)$channel;

			//Ermittlung der unterstützten Klassen
			if ((float)IPS_GetKernelVersion() >= 5.1) {
				$i = json_decode(ZW_GetInformation((int)$instanceId), true);
				$classes = array_merge(json_decode($i["NodeClasses"], true), json_decode($i["NodeSecureClasses"], true));
			} else {
				$classes = ZW_GetNodeClasses((int)$instanceId);
			}

			foreach ($classes as $class) {
				switch ((int)$class){
					case 32:
						$this->b_class_basic = true;
						break;
					case 37:
						$this->b_class_switch = true;
						break;
					case 96:
						$this->b_class_multi = true;
						break;
				}
			}
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
			return get_class($this).','.$this->instanceId.','.$this->channel;
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
		 * @param integer $onTime Zeit in Sekunden nach der der Aktor automatisch ausschalten soll (nicht unterstützt)
		 */
		public function SetState($value, $onTime=false) {
			// Ein Binary Switch
			if ($this->b_class_switch and ($this->channel == 0))
				ZW_SwitchMode((int)$this->instanceId, $value);
			// Ein Basic Switch
			elseif ($this->b_class_basic and ($this->channel == 0))
				ZW_Basic((int)$this->instanceId, $value);
			// Ein Multiswitch
			elseif (($this->channel > 0) and $this->b_class_multi)
				ZW_SwitchModeEx((int)$this->instanceId, $value, $this->channel);
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Zustand
		 *
		 * @return boolean aktueller Schaltzustand
		 */
		public function GetState() {
			if ($this->b_class_multi and ($this->channel > 0))
				$id = @IPS_GetObjectIDByIdent("MultiInstance".$this->channel."Variable",(int)$this->instanceId);
			else
				$id = @IPS_GetObjectIDByIdent("DataVariableBoolean",(int)$this->instanceId);
		   
		   if ($id > 0)
				return GetValue($id);
			else
			   return false;
		}

	}

	/** @}*/
?>