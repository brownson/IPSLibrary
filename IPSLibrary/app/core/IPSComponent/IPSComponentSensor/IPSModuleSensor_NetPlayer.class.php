<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSModuleSensor_NetPlayer.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

	/**
	 * @class IPSModuleSensor_NetPlayer
	 *
	 * Definiert ein IPSModuleSensor Object, das als Wrapper für Sensoren in der IPSLibrary
	 * verwendet werden kann.
	 *
	 * @author Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.06.2012<br/>
	 */

	IPSUtils_Include ("NetPlayer.inc.php",           "IPSLibrary::app::modules::NetPlayer");
	IPSUtils_Include ('IPSModuleSensor.class.php',   'IPSLibrary::app::core::IPSComponent::IPSComponentSensor');

	class IPSModuleSensor_NetPlayer extends IPSModuleSensor {

		private $functionToCall;
		private $param1;
		private $param2;
		private $param3;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSModuleSensor_NetPlayer Objektes
		 *
		 * @param integer $functionToCall Funktion
		 * @param boolean $param1 Parameter 1
		 * @param boolean $param2 Parameter 2
		 * @param boolean $param3 Parameter 3
		 */
		public function __construct($functionToCall, $param1='', $param2='', $param3='') {
			$this->functionToCall = $functionToCall;
			$this->param1         = $param1;
			$this->param2         = $param2;
			$this->param3         = $param3;
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
			$parameters = array();
			if ($this->param3<>'') {
				$parameters[] = $this->param1;
				$parameters[] = $this->param2;
				$parameters[] = $this->param3;
			} elseif ($this->param2<>'') {
				$parameters[] = $this->param1;
				$parameters[] = $this->param2;
			} elseif ($this->param1<>'') {
				$parameters[] = $this->param1;
			} else {
			}
			
			call_user_func_array($this->functionToCall, $parameters);
		}


	}

	/** @}*/
?>