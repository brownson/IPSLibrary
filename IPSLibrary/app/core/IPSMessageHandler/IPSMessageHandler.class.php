<?
	/**@defgroup ipsmessagehandler IPSMessageHandler
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

		private static $eventConfiguration = null;

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
		 * Liefert die aktuelle Event Konfiguration
		 *
		 * @return string[] Event Konfiguration
		 */
		private static function Get_EventConfiguration() {
		   if (self::$eventConfiguration == null) {
		      self::$eventConfiguration = IPSMessageHandler_GetEventConfiguration();
		   }
		   return self::$eventConfiguration;
		}

		/**
		 * @private
		 *
		 * Setzen der aktuellen Event Konfiguration
		 *
		 * @param string[] $configuration Neue Event Konfiguration
		 */
		private static function Set_EventConfiguration($configuration) {
		   self::$eventConfiguration = $configuration;
		}

		/**
		 * @public
		 *
		 * Erzeugt anhand der Konfiguration alle Events
		 *
		 */
		public static function CreateEvents() {
			$configuration = self::Get_EventConfiguration();

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
			$configuration = self::Get_EventConfiguration();

			// Search Configuration
			$found = false;
			if (array_key_exists($variableId, $configuration)) {
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
		 * Methode um autretende Events zu processen
		 *
		 * @param integer $variable ID der auslösenden Variable
		 * @param string $value Wert der Variable
		 */
		public function HandleEvent($variable, $value) {
			$configuration = IPSMessageHandler_GetEventConfiguration();
			
			if (array_key_exists($variable, $configuration)) {
				$params = $configuration[$variable];
				if (count($params) < 3) {
					throw new IPSMessageHandlerException('Invalid IPSMessageHandler Configuration, Event Defintion needs 2 parameters');
				}
				$component = IPSComponent::CreateObjectByParams($params[1]);
				$module    = IPSModule::CreateObjectByParams($params[2]);
				
				$component->HandleEvent($variable, $value, $module);
			} else {
				IPSLogger_Wrn(__file__, 'Variable '.$variable.' NOT found in IPSMessageHandler Configuration!');
			}
		}
		
	}

	/** @}*/
?>