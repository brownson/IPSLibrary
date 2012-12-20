<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentDimmer_EIB.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

   /**
    * @class IPSComponentDimmer_EIB
    *
    * Definiert ein IPSComponentDimmer_EIB Object, das ein IPSComponentDimmer Object für EIB implementiert.
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 18.12.2012<br/>
    */

	IPSUtils_Include ('IPSComponentDimmer.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentDimmer');

	class IPSComponentDimmer_EIB extends IPSComponentDimmer {

		private $instanceId;
		private $groupFunction;
		private $groupInterpretation;
	
		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentDimmer_EIB Objektes
		 *
		 * @param integer $instanceId InstanceId des EIB Devices
		 */
		public function __construct($instanceId) {
			$this->instanceId          = IPSUtil_ObjectIDByPath($instanceId);
			$this->groupFunction       = EIB_GetGroupFunction($this->instanceId);
			$this->groupInterpretation = EIB_GetGroupInterpretation($this->instanceId);
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
		 * @param IPSModuleDimmer $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleDimmer $module){
		}

		/**
		 * @public
		 *
		 * Zustand Setzen 
		 *
		 * @param integer $power Geräte Power
		 * @param integer $level Wert für Dimmer Einstellung (Wertebereich 0-100)
		 */
		public function SetState($power, $level) {
			if (!$power) {
				switch ($this->groupFunction) {
					case 'Scale':
						EIB_Scale($this->instanceId, 0);
						break;
					case 'DimControl':
						EIB_DimControl($this->instanceId, 0);
						break;
					case 'DimValue':
						EIB_DimValue($this->instanceId, 0);
						break;
					default:
						trigger_error('Unsupported EIB GroupFunction "'.$this->groupFunction.'"');
				}
			} else {
				switch ($this->groupFunction) {
					case 'Scale':
						if ($this->groupInterpretation=='Standard') { /* 0 .. 255 */
							EIB_Scale($this->instanceId, round($level/100*255));
						} elseif ($this->groupInterpretation=='Percent') { /* 0 .. 100 */
							EIB_Scale($this->instanceId, round($level));
						} else {
							trigger_error('Unsupported EIB GroupInterpretation "'.$this->groupInterpretation.'"');
						}
						break;
					case 'DimControl':
						if ($this->groupInterpretation=='Standard') { /* 0 .. 15 */
							EIB_DimControl($this->instanceId, round($level/100*15));
						} elseif ($this->groupInterpretation=='Enhanced') { /* 0 .. 7 */
							EIB_DimControl($this->instanceId, round($level/100*7));
						} else {
							trigger_error('Unsupported EIB GroupInterpretation "'.$this->groupInterpretation.'"');
						}
						break;
					case 'DimValue':
						if ($this->groupInterpretation=='Standard') { /* 0 .. 255 */
							EIB_Value($this->instanceId, round($level/100*255));
						} elseif ($this->groupInterpretation=='Percent') { /* 0 .. 100 */
							EIB_DimValue($this->instanceId, round($level));
						} else {
							trigger_error('Unsupported EIB GroupInterpretation "'.$this->groupInterpretation.'"');
						}
						break;
					default:
						trigger_error('Unsupported EIB GroupFunction "'||$this->groupFunction.'"');
				}
			}
			
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Level des Dimmers
		 *
		 * @return integer aktueller Dimmer Level
		 */
		public function GetLevel() {
			$value = GetValue(IPS_GetObjectIdByIdent('Value', $this->instanceId));
			switch ($this->groupFunction) {
				case 'Scale':
					if ($this->groupInterpretation=='Standard') { /* 0 .. 255 */
						$value = round($value / 255 * 100);
					} elseif ($this->groupInterpretation=='Percent') { /* 0 .. 100 */
						null;
					} else {
						trigger_error('Unsupported EIB GroupInterpretation "'.$this->groupInterpretation.'"');
					}
					break;
				case 'DimControl':
					if ($this->groupInterpretation=='Standard') { /* 0 .. 15 */
						$value = round($value / 15 * 100);
					} elseif ($this->groupInterpretation=='Enhanced') { /* 0 .. 7 */
						$value = round($value / 7 * 100);
					} else {
						trigger_error('Unsupported EIB GroupInterpretation "'.$this->groupInterpretation.'"');
					}
					break;
				case 'DimValue':
					if ($this->groupInterpretation=='Standard') { /* 0 .. 255 */
						$value = round($value / 255 * 100);
					} elseif ($this->groupInterpretation=='Percent') { /* 0 .. 100 */
						null;
					} else {
						trigger_error('Unsupported EIB GroupInterpretation "'.$this->groupInterpretation.'"');
					}
					break;
				default:
					trigger_error('Unsupported EIB GroupFunction "'||$this->groupFunction.'"');
			}
			return $value;
		}

		/**
		 * @public
		 *
		 * Liefert aktuellen Power Zustand des Dimmers
		 *
		 * @return boolean Gerätezustand On/Off des Dimmers
		 */
		public function GetPower() {
			return GetValue(IPS_GetObjectIdByIdent('Value', $this->instanceId)) > 0;
		}

	}

	/** @}*/
?>