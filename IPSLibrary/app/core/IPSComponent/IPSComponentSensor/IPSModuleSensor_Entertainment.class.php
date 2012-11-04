<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleSensor_Entertainment.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleSensor_Entertainment
	 *
	 * Definiert ein IPSModuleSensor Object, das als Wrapper für Sensoren in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.06.2012<br/>
	 */

	IPSUtils_Include ("Entertainment_InterfaceIPSComponentSensor.inc.php", "IPSLibrary::app::modules::Entertainment");
	IPSUtils_Include ('IPSModuleSensor.class.php',                         'IPSLibrary::app::core::IPSComponent::IPSComponentSensor');

	class IPSModuleSensor_Entertainment extends IPSModuleSensor {

		private $functionToCall;
		private $param1;
		private $param2;
		private $param3;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSModuleSensor_Entertainment Objektes
		 *
		 * @param integer $functionToCall Funktion, die aufgerufen werden soll
		 * @param boolean $param1 Parameter 1
		 * @param boolean $param2 Parameter 2
		 * @param boolean $param3 Parameter 3
		 */
		public function __construct($functionToCall, $param1='', $param2='', $param3='') {
			$this->functionToCall = $functionToCall;
			$this->param1   = $param1;
			$this->param2   = $param2;
			$this->param3   = $param3;
		}
		
		private function GetParam($param) {
			if (is_numeric($param)) {
				return (int)$param;
			} elseif ($param=='true') {
				return true;
			} elseif ($param=='false') {
				return false;
			} else {
				return $param;
			}
		}
		
		private function GetParamArray() {
			$parameters = array();
			if ($this->param3<>'') {
				$parameters[] = $this->GetParam($this->param1);
				$parameters[] = $this->GetParam($this->param2);
				$parameters[] = $this->GetParam($this->param3);
			} elseif ($this->param2<>'') {
				$parameters[] = $this->GetParam($this->param1);
				$parameters[] = $this->GetParam($this->param2);
			} elseif ($this->param1<>'') {
				$parameters[] = $this->GetParam($this->param1);
			} else {
			}
			return $parameters;
		}
	
		/**
		 * @public
		 *
		 * Ermöglicht die Synchronisation von Sensorwerten mit Modulen
		 *
		 * @param string $value Sensorwert
		 * @param IPSComponentSensor $component Sensor Komponente
		 */
		public function SyncButton($value, IPSComponentSensor $component) {
			$this->ExecuteButton();
		}

		/**
		 * @public
		 *
		 * Ermöglicht das Verarbeiten eines Taster Signals
		 *
		 */
		public function ExecuteButton () {
			if (function_exists($this->functionToCall)) {
				call_user_func_array($this->functionToCall, $this->GetParamArray());
			} else {
				Entertainment_IPSComponentSensor_ReceiveData($this->functionToCall, $this->param1, $this->param2, $this->param3);
			}
		}

	}

	/** @}*/
?>