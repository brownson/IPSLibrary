<?
	/**@defgroup ipsmessagehandler IPSMessageHandler
	 * @ingroup core
	 * @{
	 *
 	 *
	 * @file          IPSMessageHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 */

  /**
    * @class IPSMessageHandlerException
    *
    * Definiert eine IPSMessageHandler Exception
    *
    */
	class IPSMessageHandlerException extends Exception {
	}

   /**
    * @class IPSMessageHandler
    *
    * Definiert ein IPSMessageHandler Object, das für diverse Hardware Komponenten unterschiedlicher Hersteller 
    * eine gemeinsame Zugriffs Basis bildet.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */

	IPSUtils_Include ('IPSLogger.inc.php',      'IPSLibrary::app::core::IPSLogger');
	IPSUtils_Include ('IPSMessageHandler_Configuration.inc.php', 'IPSLibrary::config::core::IPSMessageHandler');
	IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');
	IPSUtils_Include ('IPSModule.class.php',    'IPSLibrary::app::core::IPSComponent');

	class IPSMessageHandler {

		private static $eventConfigurationAuto = null;
		private static $eventConfigurationCust = null;

		/**
		 * @public
		 *
		 * Initialisierung des IPSMessageHandlers
		 *
		 */
		public function __construct() {
		}

		/**
		 * @private
		 *
		 * Liefert die aktuelle Auto Event Konfiguration
		 *
		 * @return string[] Event Konfiguration
		 */
		private static function Get_EventConfigurationAuto() {
			if (self::$eventConfigurationAuto == null) {
				self::$eventConfigurationAuto = IPSMessageHandler_GetEventConfiguration();
			}
			return self::$eventConfigurationAuto;
		}

		/**
		 * @private
		 *
		 * Liefert die aktuelle Customer Event Konfiguration
		 *
		 * @return string[] Event Konfiguration
		 */
		private static function Get_EventConfigurationCust() {
			if (self::$eventConfigurationCust == null and function_exists('IPSMessageHandler_GetEventConfigurationCust')) {
				self::$eventConfigurationCust = IPSMessageHandler_GetEventConfigurationCust();
			}
			return self::$eventConfigurationCust;
		}

		/**
		 * @private
		 *
		 * Setzen der aktuellen Event Konfiguration
		 *
		 * @param string[] $configuration Neue Event Konfiguration
		 */
		private static function Set_EventConfigurationAuto($configuration) {
		   self::$eventConfigurationAuto = $configuration;
		}

		/**
		 * @public
		 *
		 * Erzeugt anhand der Konfiguration alle Events
		 *
		 */
		public static function CreateEvents() {
			$configuration = self::Get_EventConfigurationAuto();

			foreach ($configuration as $variableId=>$params) {
				self::CreateEvent($variableId, $params[0]);
			}
		}

		/**
		 * @public
		 *
		 * Erzeugt ein Event für eine übergebene Variable, das den IPSMessageHandler beim Auslösen
		 * aufruft.
		 *
		 * @param integer $variableId ID der auslösenden Variable
		 * @param string $eventType Type des Events (OnUpdate oder OnChange)
		 */
		public static function CreateEvent($variableId, $eventType) {
			switch ($eventType) {
				case 'OnChange':
					$triggerType = 1;
					break;
				case 'OnUpdate':
					$triggerType = 0;
					break;
				default:
					throw new IPSMessageHandlerException('Found unknown EventType '.$eventType);
			}
			$eventName = $eventType.'_'.$variableId;
			$scriptId  = IPS_GetObjectIDByIdent('IPSMessageHandler_Event', IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.core.IPSMessageHandler'));
			$eventId   = @IPS_GetObjectIDByIdent($eventName, $scriptId);
			if ($eventId === false) {
				$eventId = IPS_CreateEvent(0);
				IPS_SetName($eventId, $eventName);
				IPS_SetIdent($eventId, $eventName);
				IPS_SetEventTrigger($eventId, $triggerType, $variableId);
				IPS_SetParent($eventId, $scriptId);
				IPS_SetEventActive($eventId, true);
				IPSLogger_Dbg (__file__, 'Created IPSMessageHandler Event for Variable='.$variableId);
			}
		}


		/**
		 * @private
		 *
		 * Speichert die aktuelle Event Konfiguration
		 *
		 * @param string[] $configuration Konfigurations Array
		 */
		private static function StoreEventConfiguration($configuration) {

			// Build Configuration String
			$configString = '$eventConfiguration = array(';
			foreach ($configuration as $variableId=>$params) {
				$configString .= PHP_EOL.chr(9).chr(9).chr(9).$variableId.' => array(';
				for ($i=0; $i<count($params); $i=$i+3) {
					if ($i>0) $configString .= PHP_EOL.chr(9).chr(9).chr(9).'               ';
					$configString .= "'".$params[$i]."','".$params[$i+1]."','".$params[$i+2]."',";
				}
				$configString .= '),';
			}
			$configString .= PHP_EOL.chr(9).chr(9).chr(9).');'.PHP_EOL.PHP_EOL.chr(9).chr(9);

			// Write to File
			$fileNameFull = IPS_GetKernelDir().'scripts\\IPSLibrary\\config\\core\\IPSMessageHandler\\IPSMessageHandler_Configuration.inc.php';
			if (!file_exists($fileNameFull)) {
				throw new IPSMessageHandlerException($fileNameFull.' could NOT be found!', E_USER_ERROR);
			}
			$fileContent = file_get_contents($fileNameFull, true);
			$pos1 = strpos($fileContent, '$eventConfiguration = array(');
			$pos2 = strpos($fileContent, 'return $eventConfiguration;');

			if ($pos1 === false or $pos2 === false) {
				throw new IPSMessageHandlerException('EventConfiguration could NOT be found !!!', E_USER_ERROR);
			}
			$fileContentNew = substr($fileContent, 0, $pos1).$configString.substr($fileContent, $pos2);
			file_put_contents($fileNameFull, $fileContentNew);
			self::Set_EventConfiguration($configuration);
		}

		/**
		 * @public
		 *
		 * Registriert ein Event im IPSMessageHandler. Die Funktion legt ein ensprechendes Event
		 * für die übergebene Variable an und registriert die dazugehörigen Parameter im MessageHandler
		 * Konfigurations File.
		 *
		 * @param integer $variableId ID der auslösenden Variable
		 * @param string $eventType Type des Events (OnUpdate oder OnChange)
		 * @param string $componentParams Parameter für verlinkte Hardware Komponente (Klasse+Parameter)
		 * @param string $moduleParams Parameter für verlinktes Module (Klasse+Parameter)
		 */
		public static function RegisterEvent($variableId, $eventType, $componentParams, $moduleParams) {
			$configurationAuto = self::Get_EventConfigurationAuto();
			$configurationCust = self::Get_EventConfigurationCust();

			// Search Configuration
			$found = false;
			if (array_key_exists($variableId, $configurationCust)) {
				$found = true;
			}

			if (!$found) {
				if (array_key_exists($variableId, $configurationAuto)) {
					$moduleParamsNew = explode(',', $moduleParams);
					$moduleClassNew  = $moduleParamsNew[0];

					$params = $configuration[$variableId];
				   
					for ($i=0; $i<count($params); $i=$i+3) {
						$moduleParamsCfg = $params[$i+2];
						$moduleParamsCfg = explode(',', $moduleParamsCfg);
						$moduleClassCfg  = $moduleParamsCfg[0];
						// Found Variable and Module --> Update Configuration
						if ($moduleClassCfg=$moduleClassNew) {
							$found = true;
							$configuration[$variableId][$i]   = $eventType;
							$configuration[$variableId][$i+1] = $componentParams;
							$configuration[$variableId][$i+2] = $moduleParams;
						}
					}
				}


				// Variable NOT found --> Create Configuration
				if (!$found) {
					$configuration[$variableId][] = $eventType;
					$configuration[$variableId][] = $componentParams;
					$configuration[$variableId][] = $moduleParams;
				}

				self::StoreEventConfiguration($configuration);
				self::CreateEvent($variableId, $eventType);
			}
		}

		/**
		 * @public
		 *
		 * Registriert ein OnChange Event im IPSMessageHandler. Die Funktion legt ein ensprechendes Event
		 * für die übergebene Variable an und registriert die dazugehörigen Parameter im MessageHandler
		 * Konfigurations File.
		 *
		 * @param integer $variableId ID der auslösenden Variable
		 * @param string $componentParams Parameter für verlinkte Hardware Komponente (Klasse+Parameter)
		 * @param string $moduleParams Parameter für verlinktes Module (Klasse+Parameter)
		 */
		public static function RegisterOnChangeEvent($variableId, $componentParams, $moduleParams) {
			self::RegisterEvent($variableId, 'OnChange', $componentParams, $moduleParams);
		}

		/**
		 * @public
		 *
		 * Registriert ein OnUpdate Event im IPSMessageHandler. Die Funktion legt ein ensprechendes Event
		 * für die übergebene Variable an und registriert die dazugehörigen Parameter im MessageHandler
		 * Konfigurations File.
		 *
		 * @param integer $variableId ID der auslösenden Variable
		 * @param string $componentParams Parameter für verlinkte Hardware Komponente (Klasse+Parameter)
		 * @param string $moduleParams Parameter für verlinktes Module (Klasse+Parameter)
		 */
		public static function RegisterOnUpdateEvent($variableId, $componentParams, $moduleParams) {
			self::RegisterEvent($variableId, 'OnUpdate', $componentParams, $moduleParams);
		}

		/**
		 * @public
		 *
		 * Methode um autretende IR Events zu processen
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 */
		public function HandleIREvent($variable, $value) {
			$configuration = IPSMessageHandler_GetEventConfigurationIR();
			
			if ($value == '') {
				return;
			}

			$irButton = $value;
			$irInstanceId = IPS_GetParent($variable);
			$childrenIds = IPS_GetChildrenIDs($irInstanceId);
			foreach ($childrenIds as $id) {
				if ($id <> $variable) {
					$irRemoteControl = GetValue($id);
				}
			}
			IPSLogger_Com(__file__, "Received Data from IR-Variable, Control='$irRemoteControl', Command='$irButton'");
			
			$irMessage = $irRemoteControl.','.$irButton;
			if (array_key_exists($irRemoteControl.'.'.$irButton, $configuration)) {
				$params = $configuration[$irRemoteControl.'.'.$irButton];
			} elseif (array_key_exists($irRemoteControl.'.*', $configuration)) {
				$params = $configuration[$irRemoteControl.'.'.$irButton];
			} else {
				$params = '';
			}

			if ($params<>'') {
				if (count($params) < 2) {
					throw new IPSMessageHandlerException('Invalid IPSMessageHandler Configuration, Event Defintion needs 2 parameters');
				}
				$component = IPSComponent::CreateObjectByParams($params[0]);
				$module    = IPSModule::CreateObjectByParams($params[1]);

				if (function_exists('IPSMessageHandler_BeforeHandleEvent')) {
					if (IPSMessageHandler_BeforeHandleEvent($variable, $value, $component, $module)) {
						$component->HandleEvent($variable, $value, $module);
					}
				} else {
					$component->HandleEvent($variable, $value, $module);
				}
				if (function_exists('IPSMessageHandler_AfterHandleEvent')) {
					IPSMessageHandler_AfterHandleEvent($variable, $value, $component, $module);
				}
			}
}
		
		/**
		 * @public
		 *
		 * Methode um autretende Events zu processen
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 */
		public function HandleEvent($variable, $value) {
			$configurationAuto = IPSMessageHandler_GetEventConfiguration();
			$configurationCust = IPSMessageHandler_GetEventConfigurationCust();

			if (array_key_exists($variable, $configurationCust)) {
				$params = $configurationCust[$variable];
			} elseif (array_key_exists($variable, $configurationAuto)) {
				$params = $configurationAuto[$variable];
			//} elseif ($variable==IPSMH_IRTRANS_BUTTON_VARIABLE_ID) {
				//$params = '';
				//$this->HandleIREvent($variable, $value);
			} else {
				$params = '';
				IPSLogger_Wrn(__file__, 'Variable '.$variable.' NOT found in IPSMessageHandler Configuration!');
			}

			if ($params<>'') {
				if (count($params) < 3) {
					throw new IPSMessageHandlerException('Invalid IPSMessageHandler Configuration, Event Defintion needs 3 parameters');
				}
				$component = IPSComponent::CreateObjectByParams($params[1]);
				$module    = IPSModule::CreateObjectByParams($params[2]);

				if (function_exists('IPSMessageHandler_BeforeHandleEvent')) {
					if (IPSMessageHandler_BeforeHandleEvent($variable, $value, $component, $module)) {
						$component->HandleEvent($variable, $value, $module);
						if (function_exists('IPSMessageHandler_AfterHandleEvent')) {
							IPSMessageHandler_AfterHandleEvent($variable, $value, $component, $module);
						}
					}
				} else {
					$component->HandleEvent($variable, $value, $module);
					if (function_exists('IPSMessageHandler_AfterHandleEvent')) {
						IPSMessageHandler_AfterHandleEvent($variable, $value, $component, $module);
					}
				}
			}
		}

	}

	/** @}*/
?>