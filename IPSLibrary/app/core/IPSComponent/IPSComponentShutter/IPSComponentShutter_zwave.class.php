<<?
	/**@addtogroup ipscomponent
	 * @{
	 *
 	 *
	 * @file          IPSComponentShutter_zwave.class.php
	 * @author        Andreas Brauneis
	 * @zwave         Anpassung Ingo Wupper
	 *
	 */

   /**
    * @class IPSComponentShutter_zwave
    *
    * Definiert ein IPSComponentShutter_Homematic Object, das ein IPSComponentShutter Object für Homematic implementiert.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSComponentShutter.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentShutter');

	class IPSComponentShutter_zwave extends IPSComponentShutter {

		private $instanceId;
		private $reverseControl;

		/**
		 * @public
		 *
		 * Initialisierung eines IPSComponentShutter_zwave Objektes
		 *
		 * @param integer $instanceId InstanceId des Homematic Devices
		 * @param boolean $reverseControl Reverse Ansteuerung des Devices
		 */
		public function __construct($instanceId, $reverseControl=false) {
			$this->instanceId     = IPSUtil_ObjectIDByPath($instanceId);
			$this->reverseControl = $reverseControl;
		}

		/**
		 * @public
		 *
		 * Function um Events zu behandeln, diese Funktion wird vom IPSMessageHandler aufgerufen, um ein aufgetretenes Event
		 * an das entsprechende Module zu leiten.
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 * @param IPSModuleShutter $module Module Object an das das aufgetretene Event weitergeleitet werden soll
		 */
		public function HandleEvent($variable, $value, IPSModuleShutter $module){
		   if ($this->reverseControl) {
				$module->SyncPosition(($value*100), $this);
			} else {
				$module->SyncPosition(100-($value*100), $this);
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
			return get_class($this).','.$this->instanceId;
		}

		/**
		 * @public
		 *
		 * Hinauffahren der Beschattung
		 */
		public function MoveUp(){
		   if ($this->reverseControl) {
				ZW_ShutterMoveDown($this->instanceId);
			} else {
				ZW_ShutterMoveUp($this->instanceId);
			}
		}

		/**
		 * @public
		 *
		 * Hinunterfahren der Beschattung
		 */
		public function MoveDown(){
		   if ($this->reverseControl) {
				ZW_ShutterMoveUp($this->instanceId);
			} else {
				ZW_ShutterMoveDown($this->instanceId);
			}
		}

		/**
		 * @public
		 *
		 * Stop
		 */
		public function Stop() {
			ZW_ShutterStop ($this->instanceId);
		}

	}

	/** @}*/
?>