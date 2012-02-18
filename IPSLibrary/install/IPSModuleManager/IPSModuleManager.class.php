<?

	/**@defgroup ipsmodulemanager IPSModuleManager
	 * @{
	 *
	 * Der IPSModuleManager bildet das Herzstück des IPSLibrary Installers. Er beinhaltet diverse Konfigurations Möglichkeiten, die
	 * man in der Datei IPSModuleManager.ini verändern kann (Ablagerort: IPSLibrary.install.InitializationFile).
	 *
	 * @file          IPSModuleManager.class.php
	 * @author        Andreas Brauneis
	 *
	 *
	 * @}*/

	/**@defgroup ipsmodulemanager_overview IPSModuleManager Übersicht
	 * @ingroup ipsmodulemanager
	 * @{
	 * 
	 * Übersicht IPSModuleManager
	 * @image html IPSModuleManager_Overview.png
	 * 
	 * Der IPSModuleManager supportet die folgenden Möglichkeiten:
	 * - Initialer Download und Installation neuer Module
	 * - Update auf neuere Version von Modulen
	 * - Versionsverwaltung für Module
	 * - Backup Handler
	 * - Konfigurations Handler
	 * - Log Handler
	 * 
	 * @section ipsmodulemanager_download Initialer Download von Modulen
	 *  Die Installation neuer Module gliedert sich in 3 Phasen:
	 *  - Module Download
	 *  - Konfiguration
	 *  - Installation
	 *  
	 *  Ein Module kann nach mit folgendem Code geladen werden:
	 *  @code
	      IPSUtils_Include ("IPSModuleManager.class.php","IPSLibrary::install::IPSModuleManager");
	      $moduleManager = new IPSModuleManager('IPSLogger');
	      $moduleManager.LoadModule();
	    @endcode
	 *  
	 * Eine Konfiguration ist nicht bei jedem Module nötig, Details über die jeweilige Konfiguration kann der jeweiligen Module Dokumentation entnommen werden.
	 *  
	 *  Folgende Schritte warden bei einer Installation ausgeführt:
	 *  - Check von anderen Modulen, die für die Installation Voraussetzung sind
	 *  Versions Check von referenzierten Modulen
	 *  Installation (beinhaltet das Anlegen aller benötigten Variablen und Scripte)
	 *  
	 *  Beispiel zur Installation des IPSLogger Modules:
	 *  @code
          IPSUtils_Include ("IPSModuleManager.class.php","IPSLibrary::install::IPSModuleManager");
          $moduleManager = new IPSModuleManager('IPSLogger');
          $moduleManager.InstallModule();
        @endcode
	 * 
	 * @section ipsmodulemanager_update Update eines oder mehrerer Module
	 *  Update eines einzelnen Modules erfolgt mit folgendem Code:
	 *  @code
          IPSUtils_Include ("IPSModuleManager.class.php","IPSLibrary::install::IPSModuleManager");
          $moduleManager = new IPSModuleManager('IPSLogger');
          $moduleManager.UpdateModule();
        @endcode
	 * 
	 * Ein Update aller installierten Module kann mit folgendem Code gemacht werden;
	 *  @code
          IPSUtils_Include ("IPSModuleManager.class.php","IPSLibrary::install::IPSModuleManager");
          $moduleManager = new IPSModuleManager('');
          $moduleManager.UpdateAllModules();
        @endcode
	 * 
	 *  Bei einem Update werden alle Scripte des jeweiligen Modules neu aus dem Repository geladen 
	 *  (Konfigurations Scripte und Dateien werden nicht verändert). Sollte nach dem Update eine neue
	 *  Versionsnummer gefunden werden, wird automatisch das Installations Script ausgeführt (inklusive 
	 *  Anpassung der WebFront und Mobile Strukturen).
	 * 
	 * @section ipsmodulemanager_version Versions Verwaltung
	 *  Der IPSModuleManager beinhaltet eine interne Versionsverwaltung, der die aktuelle IPS Version überprüft und auch alle Module überprüft, die 
	 *  Voraussetzung des aktuellen Modules sind.
	 * 
	 * @section ipsmodulemanager_logging Logging des IPSModuleManagers
	 *  Der LogHandler legt bei jedem Update oder Installations Vorgang ein Protokoll File an, wo man die einzelnen Schritte des Installations Ablaufes
	 *  nachvollziehen kann. Standardmäßig ist das Output Verzeichnis auf "logs" gesetzt, kann aber jederzeit über den Parameter "LogDirectory" verändert werden.
	 *  Output erfolgt in eine Datei mit dem Namen IPSModuleManager_YYYY-MM-DD_HHMI.log
	 *
	 * @section ipsmodulemanager_config Konfigurations Handler
	 *  Der Konfigurations Handler bietet die Möglichkeit Installation Parameter (wie zum Beispiel WebFront Konfigurator ID, persönliche 
	 *  Icons, Namen usw.) in einem Initialisierungs File abzulegen und so die Installation der Module auf die persönlichen Bedürfnisse abzustimmen.
	 * 
	 * @section ipsmodulemanager_backup Backup Handler
	 *  Vor dem Download der neuen Scripte wird auch automatisch ein Backup der Scripte des betroffen Modules gemacht.
	 *  Ablageort für Backups ist standardmäßig auf "backups" gesetzt und kann über den Konfigurations Parameter "BackupDirectory" jederzeit verändert
	 *  werden. Das Backup des jeweiligen Update Vorgangs wird dann in einem Folder IPSLibrary_YYYY-MM-DD_HH:MI abgelegt.
	 * 
	 * @}*/

	/**@defgroup ipsmodulemanager_configuration IPSModuleManager Konfiguration
	 * @ingroup ipsmodulemanager
	 * @{
	 * Alle Konfigurations Einstellung, die für Installation von Modulen benötigt werden, sind in Initialisierungs Files abgelegt. Ablageort 
	 * für diese Files ist "IPSLibray.install.InitializationFiles", die Files werden beim initialen Download des Modules aus den Files im "Default"
	 * Verzeichnis generiert und bei späteren Updates nicht mehr verändert.
	 * Bei Problemen kann das File wieder mit der Version im Default Verzeichnis repariert werden. Im Verzeichnis "Examples" finden sich noch weitere 
	 * Files, die Beispiele für die jeweilige Konfiguration beinhalten.
	 * 
	 * Eine kurze Beschreibung des jeweiligen Parameters, ist im jeweilen Initialisierungs File zu finden.
	 * 
	 * Allgemeine Parameter sind im Konfigurations File des Modulemanagers abgelegt (Backup Directory, Logging Directory), spezielle Module Parameter 
	 * sind dann im jeweiligen Ini File des Modules abgelegt.
	 * 
	 * Teilweise werden Parameter auch in beiden Files gesucht. Zum Beispiel werden die diversen Parameter zur Installation des WebFronts (Enabled, 
	 * Root, WFCId) zuerst im jeweiligen Module Ini File gesucht (zB IPSLogger.ini), ist es dort nicht definiert, wird im File IPSModuleManager.ini gesucht.
	 * 
	 * Beispiel:
	 *  @code 
         [WFC10]
         Enabled=true
         Path=Visualization.WebFront.Entertainment
         ID=
         TabName=
         TabIcon=Speaker
         TabOrder=20
         
         [Mobile]
         Enabled=true
         Path=Visualization.Mobile
         Name=Entertainment
         Order=20
         Icon=Speaker
	    @endcode
	 * 
	 * [] markiert immer eine Gruppe von Einstellungen, In der Gruppe "WFC10” werden die Einstellungen gesucht, die für die WebFront 
	 * Installation benötigt werden. (WFC10 - WebFront mit 10 Zoll Optimierung). Analog gibt es eine Gruppe "Mobile", die für das Mobile
	 * Interface (iPhone, iPad und Android) verwendet wird. 
	 * 
	 * Beschreibung der wichtigsten Parameter:
	 * - "Enabled" definiert, ob das jeweilige Interface installiert wird 
	 * - "Path" bestimmt den Installations Pfad in IP-Symcon
	 * - "ID" bezeichnet die ID des Webfront Konfigurators der verwendet werden soll, wenn nichts angegeben wird, verwendet die Installations 
	 *   Prozedure den erst Besten der gefunden wird.
	 * - "TabName" definiert Namen im SplitPane des WebFronts
	 * - "TabIcon" definiert Icon im SplitPane des WebFronts
	 * - "TabOrder" definiert Position im SplitPane des WebFronts
	 * - "Name" für Mobile Frontend Installation 
	 * - "Order" Position Mobile Frontend
	 * - "Icon" Icon für Mobile Frontend
	 * 
	 * @}*/

	 /**@addtogroup ipsmodulemanager
	 * @{
	 */

	$_IPS['ABORT_ON_ERROR'] = true;

	IPSUtils_Include ("IPSInstaller.inc.php",                  "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSVariableVersionHandler.class.php",   "IPSLibrary::install::IPSModuleManager::IPSVersionHandler");
	IPSUtils_Include ("IPSScriptHandler.class.php",            "IPSLibrary::install::IPSModuleManager::IPSScriptHandler");
	IPSUtils_Include ("IPSFileHandler.class.php",              "IPSLibrary::install::IPSModuleManager::IPSFileHandler");
	IPSUtils_Include ("IPSBackupHandler.class.php",            "IPSLibrary::install::IPSModuleManager::IPSBackupHandler");
	IPSUtils_Include ("IPSLogHandler.class.php",               "IPSLibrary::install::IPSModuleManager::IPSLogHandler");
	IPSUtils_Include ("IPSIniConfigHandler.class.php",         "IPSLibrary::app::core::IPSConfigHandler");

	/**
	 * @class IPSModuleManager
	 *
	 * Klasse zur Installation neuer Module und zum Update bestehender Module
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.5.1, 05.01.2012<br/>
	 */
	class IPSModuleManager{

 		const DOWNLOADLISTFILE_PATH            = "IPSLibrary\\install\\DownloadListFiles\\";
		const DOWNLOADLISTFILE_SUFFIX          = '_FileList.ini';
 		const INSTALLATIONSCRIPT_PATH          = "IPSLibrary\\install\\InstallationScripts\\";
		const INSTALLATIONSCRIPT_SUFFIX        = '_Installation.ips.php';
		const INITIALIZATIONFILE_PATH          = "IPSLibrary\\install\\InitializationFiles\\";
		const INITIALIZATIONDEFAULTFILE_PATH   = "IPSLibrary\\install\\InitializationFiles\\Default\\";
		const INITIALIZATIONFILE_SUFFIX        = '.ini';

		private $moduleName="";
		private $sourceRepository="";
		private $versionHandler;
		private $scriptHandler;
		private $backupHandler;
		private $fileHandler;
		private $logHandler;
		private $fileConfigHandler;
		private $managerConfigHandler;
		private $moduleConfigHandler;

		/**
		 * @public
		 *
		 * Initialisierung des ModuleManagers
		 *
		 * @param string $moduleName Name des Modules
		 * @param string $sourceRepository Pfad/Url zum SourceRepository, das zum Download der Module verwendet werden soll
		 * @param IPSVersionHandler $versionHandler Version Handler
		 * @param IPSConfigHandler $fileConfigHandler Konfigurations Handler für Download Liste
		 * @param IPSConfigHandler $moduleConfigHandler Konfigurations Handler für übergebenes Module
		 * @param IPSConfigHandler $managerConfigHandler Konfigurations Handler für ModuleManager
		 */
		public function __construct($moduleName, $sourceRepository='', $versionHandler=null, $fileConfigHandler=null, $moduleConfigHandler=null, $managerConfigHandler=null) {
			if ($moduleName=='') {
				$moduleName = 'IPSModuleManager';
			}
			$this->moduleName           = $moduleName;

			// Create ConfigHandler for ModuleManager INI File
			$this->managerConfigHandler = $managerConfigHandler;
			if ($managerConfigHandler==null) {
				$this->managerConfigHandler = new IPSIniConfigHandler($this->GetModuleInitializationFile('IPSModuleManager'));
			}

			$this->sourceRepository     = $sourceRepository;
			if ($this->sourceRepository=='') {
				$this->sourceRepository = $this->managerConfigHandler->GetValue(IPSConfigHandler::SOURCEREPOSITORY, '');
			}

			// Create Log Handler
			$logDirectory = $this->managerConfigHandler->GetValueDef(IPSConfigHandler::LOGDIRECTORY, '', IPS_GetKernelDir().'logs\\');
			$this->logHandler = new IPSLogHandler(get_class($this), $logDirectory, '');
		   
			// Create Version Handler
			$libraryBasePath           = 'Program';
			$this->versionHandler      = $versionHandler;
			if ($versionHandler==null) {
				$this->versionHandler   = new IPSVariableVersionHandler($moduleName, $libraryBasePath);
			}

			// Create Script Handler
			$this->scriptHandler       = new IPSScriptHandler($libraryBasePath);

			// Create File Handler
			$this->fileHandler         = new IPSFileHandler();

			// Create Backup Handler
			$backupDirectory           = $this->managerConfigHandler->GetValueDef('BackupLoadDirectory', '', IPS_GetKernelDir().'backup\\IPSLibrary_Load\\');
			$this->backupHandler       = new IPSBackupHandler($backupDirectory);

			// ConfigHandler for Module Filelist File
			$localDownloadIniFile      = $this->GetModuleDownloadListFile(IPS_GetKernelDir().'scripts\\');
			if (!file_exists($localDownloadIniFile)) {
				$repositoryDownloadIniFile = $this->GetModuleDownloadListFile($this->sourceRepository);
				$this->logHandler->Log('Module Download Ini File doesnt exists -> Load Ini File "'.$repositoryDownloadIniFile.'"');
				$this->fileHandler->LoadFiles(array($repositoryDownloadIniFile), array($localDownloadIniFile));
			}
			$this->fileConfigHandler = $fileConfigHandler;
			if ($fileConfigHandler==null) {
		   	$this->fileConfigHandler = new IPSIniConfigHandler($this->GetModuleDownloadListFile(IPS_GetKernelDir().'scripts\\'));
			}

			// ConfigHandler for Module INI File
			$moduleIniFile = $this->GetModuleInitializationFile($moduleName);
			if (!file_exists($moduleIniFile)) {
				$moduleLocalDefaultIniFile      = $this->GetModuleDefaultInitializationFile($moduleName, IPS_GetKernelDir().'scripts\\');
				$moduleRepositoryDefaultIniFile = $this->GetModuleDefaultInitializationFile($moduleName, $this->sourceRepository);
				$this->logHandler->Log('Module Ini File doesnt exists -> Load Default Ini File "'.$moduleLocalDefaultIniFile.'"');
				$this->fileHandler->LoadFiles(array($moduleRepositoryDefaultIniFile), array($moduleLocalDefaultIniFile));
				$this->fileHandler->CreateScriptsFromDefault(array($moduleLocalDefaultIniFile));
			}
			$this->moduleConfigHandler  = $moduleConfigHandler;
			if ($moduleConfigHandler==null) {
		   	$this->moduleConfigHandler  = new IPSIniConfigHandler($moduleIniFile);
			}
			// Increase PHP Timeout for current Session
			$timeLimit = $this->managerConfigHandler->GetValueIntDef('TimeLimit', '', '120'); /*2 Minuten*/
			set_time_limit($timeLimit);
		}

		/**
       * @public
		 *
		 * Liefert aktuellen Versions Handler
		 *
	    * @return IPSVersionHandler aktuellen Versions Handler
		 */
		public function VersionHandler() {
		   return $this->versionHandler;
		}

		/**
       * @public
		 *
		 * Liefert aktuellen ConfigHandler für ModuleManager
		 *
	    * @return IPSConfigHandler aktuellen Config Handler
		 */
		public function ManagerConfigHandler() {
		   return $this->managerConfigHandler;
		}

		/**
       * @public
		 *
		 * Liefert aktuellen ConfigHandler für Module
		 *
	    * @return IPSConfigHandler aktuellen Config Handler
		 */
		public function ModuleConfigHandler() {
		   return $this->moduleConfigHandler;
		}

		/**
       * @public
		 *
		 * Liefert aktuellen LogHandler des ModuleManagers
		 *
	    * @return IPSLogHandler aktueller Log Handler
		 */
		public function LogHandler() {
		   return $this->logHandler;
		}

		/**
		 * @public
		 *
		 * Liefert den Wert eines übergebenen Parameters, es wird zuerst im ConfigHandler des aktuellen
		 * Modules gesucht, wird er dort nicht gefunden erfolgt die Suche im ModuleManager Config Handler.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return string liefert den Wert des übergebenen Parameters
		 * @throws ConfigurationException wenn der betroffene Parameter nicht gefunden wurde
		 */
		public function GetConfigValue($key, $section=null) {
		   if ($this->moduleConfigHandler->ExistsValue($key, $section)) {
		      return $this->moduleConfigHandler->GetValue($key, $section);
		   } else {
		      return $this->managerConfigHandler->GetValue($key, $section);
		   }
		}

		/**
		 * @public
		 *
		 * Liefert den integer Wert eines übergebenen Parameters, es wird zuerst im ConfigHandler des aktuellen
		 * Modules gesucht, wird er dort nicht gefunden erfolgt die Suche im ModuleManager Config Handler.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return integer liefert den Wert des übergebenen Parameters
		 * @throws ConfigurationException wenn der betroffene Parameter nicht gefunden wurde
		 */
		public function GetConfigValueInt ($key, $section=null) {
		   return (int)$this->GetConfigValue($key, $section);
		}

		/**
		 * @public
		 *
		 * Liefert den boolean Wert eines übergebenen Parameters, es wird zuerst im ConfigHandler des aktuellen
		 * Modules gesucht, wird er dort nicht gefunden erfolgt die Suche im ModuleManager Config Handler.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return boolean liefert den Wert des übergebenen Parameters
		 * @throws ConfigurationException wenn der betroffene Parameter nicht gefunden wurde
		 */
		public function GetConfigValueBool ($key, $section=null) {
		   return (boolean)$this->GetConfigValue($key, $section);
		}

		/**
		 * @public
		 *
		 * Liefert den float Wert eines übergebenen Parameters, es wird zuerst im ConfigHandler des aktuellen
		 * Modules gesucht, wird er dort nicht gefunden erfolgt die Suche im ModuleManager Config Handler.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @return float liefert den Wert des übergebenen Parameters
		 * @throws ConfigurationException wenn der betroffene Parameter nicht gefunden wurde
		 */
		public function GetConfigValueFloat ($key, $section=null) {
		   return (float)$this->GetConfigValue($key, $section);
		}

		/**
		 * @public
		 *
		 * Liefert den Wert eines übergebenen Parameters, es wird zuerst im ConfigHandler des aktuellen
		 * Modules gesucht, wird er dort nicht gefunden erfolgt die Suche im ModuleManager Config Handler.
		 * Ist er dort auch nicht definiert, wird der Default Wert retouniert.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @param string $defaultValue Default Wert wenn Parameter nicht gefunden wurde
		 * @return string liefert den Wert des übergebenen Parameters
		 */
		public function GetConfigValueDef($key, $section=null, $defaultValue="") {
			if ($this->moduleConfigHandler->ExistsValue($key, $section)) {
				return $this->moduleConfigHandler->GetValue($key, $section);
			} elseif ($this->managerConfigHandler->ExistsValue($key, $section)) {
				return $this->managerConfigHandler->GetValue($key, $section);
			} else {
				return $defaultValue;
			}
		}

		/**
		 * @public
		 *
		 * Liefert den integer Wert eines übergebenen Parameters, es wird zuerst im ConfigHandler des aktuellen
		 * Modules gesucht, wird er dort nicht gefunden erfolgt die Suche im ModuleManager Config Handler.
		 * Ist er dort auch nicht definiert, wird der Default Wert retouniert.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @param string $defaultValue Default Wert wenn Parameter nicht gefunden wurde
		 * @return integer liefert den Wert des übergebenen Parameters
		 */
		public function GetConfigValueIntDef($key, $section=null, $defaultValue="") {
			return (int)$this->GetConfigValueDef($key, $section, $defaultValue);
		}

		/**
		 * @public
		 *
		 * Liefert den boolean Wert eines übergebenen Parameters, es wird zuerst im ConfigHandler des aktuellen
		 * Modules gesucht, wird er dort nicht gefunden erfolgt die Suche im ModuleManager Config Handler.
		 * Ist er dort auch nicht definiert, wird der Default Wert retouniert.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @param string $defaultValue Default Wert wenn Parameter nicht gefunden wurde
		 * @return boolean liefert den Wert des übergebenen Parameters
		 */
		public function GetConfigValueBoolDef ($key, $section=null, $defaultValue="") {
			return (boolean)$this->GetConfigValueDef($key, $section, $defaultValue);
		}

		/**
		 * @public
		 *
		 * Liefert den float Wert eines übergebenen Parameters, es wird zuerst im ConfigHandler des aktuellen
		 * Modules gesucht, wird er dort nicht gefunden erfolgt die Suche im ModuleManager Config Handler.
		 * Ist er dort auch nicht definiert, wird der Default Wert retouniert.
		 *
		 * @param string $key Name des Parameters
		 * @param string $section Name der Parameter Gruppe, kann auch leer sein
		 * @param string $defaultValue Default Wert wenn Parameter nicht gefunden wurde
		 * @return float liefert den Wert des übergebenen Parameters
		 */
		public function GetConfigValueFloatDef ($key, $section=null, $defaultValue="") {
		   return (float)$this->GetConfigValueDef($key, $section, $defaultValue);
		}


		/**
		 * @private
		 *
		 * @return string liefert Installation Filename des Modules
		 */
		private function GetModuleInstallationScript() {
			$path = IPS_GetKernelDir().'scripts\\'.$this::INSTALLATIONSCRIPT_PATH;
			$file = $this->moduleName.$this::INSTALLATIONSCRIPT_SUFFIX;
			return $path.$file;
		}

		/**
		 * @private
		 *
		 * @param string $baseDirectory Pfad/Url des Basis Directories
		 * @return string liefert DownloadList Filename des Modules
		 */
		private function GetModuleDownloadListFile($baseDirectory) {
			$path = $baseDirectory.$this::DOWNLOADLISTFILE_PATH;
			$file = $this->moduleName.$this::DOWNLOADLISTFILE_SUFFIX;
			return $path.$file;
		}

		/**
		 * @private
		 *
		 * @param string $module Name des Modules
		 * @return string liefert Initialization Filename des Modules
		 */
		private function GetModuleInitializationFile($moduleName) {
			$path = IPS_GetKernelDir().'scripts\\'.$this::INITIALIZATIONFILE_PATH;
			$file = $moduleName.$this::INITIALIZATIONFILE_SUFFIX;
			return $path.$file;
		}

		/**
		 * @private
		 *
		 * @param string $module Name des Modules
		 * @param string $baseDirectory Pfad/Url des Basis Directories
		 * @return string liefert Default Initialization Filename des Modules
		 */
		private function GetModuleDefaultInitializationFile($moduleName, $baseDirectory) {
			$path = $baseDirectory.$this::INITIALIZATIONDEFAULTFILE_PATH;
			$file = $moduleName.$this::INITIALIZATIONFILE_SUFFIX;
			return $path.$file;
		}

		/**
		 * @private
		 *
		 * Liefert die ScriptListe für einen übergebenen FileType
		 *
		 * @param string $fileKey Type des Files (ScriptList, DefaultList, ExampleList, ...)
		 * @param string $fileTypeSection Filetype Section (app, config, webfront ...)
		 * @param string $baseDirectory Basis Verzeichnis für die Generierung der Filenamen
		 * @return array[] Liste mit Filenamen
		 */
		private function GetScriptList($fileKey, $fileTypeSection, $baseDirectory) {
		   $resultList = array();
		   $scriptList = $this->fileConfigHandler->GetValueDef($fileKey, $fileTypeSection, array());

			foreach ($scriptList as $idx=>$script) {
			   if ($script<>'') {
					if ($fileKey=='DefaultFiles') {
					   $script   = 'Default\\'.$script;
					} elseif ($fileKey=='ExampleFiles') {
					   $script   = 'Examples\\'.$script;
					} else {
					}

					switch ($fileTypeSection) {
						case 'App':
							$namespace = $this->fileConfigHandler->GetValue(IPSConfigHandler::MODULENAMESPACE);
						   $fullScriptName   = $baseDirectory.'::'.$namespace.'::'.$script;
						   break;
						case 'Config':
							$namespace = $this->fileConfigHandler->GetValue(IPSConfigHandler::MODULENAMESPACE);
						   $namespace = str_replace('IPSLibrary::app', 'IPSLibrary::config', $namespace);
						   $fullScriptName   = $baseDirectory.'::'.$namespace.'::'.$script;
							break;
						case 'WebFront':
						   if ($baseDirectory==IPS_GetKernelDir().'scripts\\') {
						   	$fullScriptName   = IPS_GetKernelDir().'webfront\\user\\'.$this->moduleName.'\\'.$script;
						   } else {
						   	$fullScriptName   = $baseDirectory.'\\IPSLibrary\\webfront\\'.$this->moduleName.'\\'.$script;
						   }
						   break;
						case 'Install':
							if ($fileKey=='DefaultFiles' or $fileKey=='ExampleFiles') {
							   $fullScriptName   = $baseDirectory.'\\IPSLibrary\\install\\InitializationFiles\\'.$script;
							} elseif ($fileKey=='DownloadFiles') {
							   $fullScriptName   = $baseDirectory.'\\IPSLibrary\\install\\DownloadListFiles\\'.$script;
							} else {
							   $fullScriptName   = $baseDirectory.'\\IPSLibrary\\install\\InstallationScripts\\'.$script;
							}
						   break;
						default:
						   die('Unknown fileTypeSection '.$fileTypeSection);
					}
				   $fullScriptName   = str_replace('::', '\\', $fullScriptName);
				   $fullScriptName   = str_replace('\\\\', '\\', $fullScriptName);

				   $resultList[] = $fullScriptName;
				}
			}
			return $resultList;
		}

		/**
		 * @public
		 *
		 * Registriert eine List von Files in IP-Symcon anhand des Filenames
		 *
		 * @param string $fileKey Type des Files (ScriptList, DefaultList, ExampleList, ...)
		 * @param string $fileTypeSection Filetype Section (app, config, webfront ...)
		 * @param string $fileList Liste mit Filenamen
		 */
		private function RegisterModuleFiles($fileKey, $fileTypeSection, $fileList) {
			$registerDefaultFiles = $this->GetConfigValueBoolDef('RegisterDefaultFiles', '', '');
			$registerExampleFiles = $this->GetConfigValueBoolDef('RegisterExampleFiles', '', '');

			if ($fileKey=='DefaultFiles') {
				$this->scriptHandler->RegisterUserScriptsListByDefaultFilename($fileList);
			}

			if ((!$registerDefaultFiles and $fileKey=='DefaultFiles') or
				(!$registerExampleFiles and $fileKey=='ExampleFiles')) {
				return;
			}

			$this->scriptHandler->RegisterScriptListByFilename($fileList);
		}

		/**
		 * @public
		 *
		 * Lädt eine Liste von Dateien anhand des Filetypes von einem Source Repository
		 *
		 * @param string $fileKey Type des Files (ScriptList, DefaultList, ExampleList, ...)
		 * @param string $fileTypeSection Filetype Section (app, config, webfront ...)
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Laden verwendet werden soll
		 */
		private function LoadModuleFiles($fileKey, $fileTypeSection, $sourceRepository) {
			$localList       = $this->GetScriptList($fileKey, $fileTypeSection, IPS_GetKernelDir().'scripts\\');
			$repositoryList  = $this->GetScriptList($fileKey, $fileTypeSection, $sourceRepository);
			$backupList      = $this->GetScriptList($fileKey, $fileTypeSection, $this->backupHandler->GetBackupDirectory());

			$this->backupHandler->CreateBackup($localList, $backupList);

			$this->fileHandler->LoadFiles($repositoryList, $localList);
			if ($fileKey=='DefaultFiles') {
				$this->fileHandler->CreateScriptsFromDefault($localList);
			}
			$this->RegisterModuleFiles($fileKey, $fileTypeSection, $localList);
		}

		/**
		 * @public
		 *
		 * Lädt alle zugehörigen Files des Modules von einem Source Repository
		 *
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Speichern verwendet werden soll
		 */
		public function LoadModule($sourceRepository='') {
		   if ($sourceRepository=='') {
		   	$sourceRepository = $this->sourceRepository;
		   }
			$this->versionHandler->SetModuleVersionLoading();

			$this->LoadModuleFiles('DownloadFiles','Install',  $sourceRepository);
			$this->fileConfigHandler = new IPSIniConfigHandler($this->GetModuleDownloadListFile(IPS_GetKernelDir().'scripts\\'));

			$this->LoadModuleFiles('InstallFiles', 'Install',  $sourceRepository);
			$this->LoadModuleFiles('DefaultFiles', 'Install',  $sourceRepository);
			$this->LoadModuleFiles('ExampleFiles', 'Install',  $sourceRepository);

			$this->LoadModuleFiles('ScriptFiles',  'App',      $sourceRepository);
			$this->LoadModuleFiles('DefaultFiles', 'App',      $sourceRepository);

			$this->LoadModuleFiles('ScriptFiles',  'Config',   $sourceRepository);
			$this->LoadModuleFiles('DefaultFiles', 'Config',   $sourceRepository);
			$this->LoadModuleFiles('ExampleFiles', 'Config',   $sourceRepository);

			$this->LoadModuleFiles('ScriptFiles',  'WebFront', $sourceRepository);
			$this->LoadModuleFiles('ExampleFiles', 'WebFront', $sourceRepository);

			$this->versionHandler->SetModuleVersionLoaded();
		}

		/**
		 * @public
		 *
		 * Installiert ein Module,
		 *
		 * @param string $forceInstallation wenn true, wird auch eine Installation ausgeführt, wenn sich die Version des Modules nicht geändert hat
		 */
		public function InstallModule($forceInstallation = true) {
			$newVersion = $this->fileConfigHandler->GetValue(IPSConfigHandler::VERSION);
			if (!$this->versionHandler->IsVersionInstalled($newVersion) or $forceInstallation) {
			   $file =  $this->GetModuleInstallationScript();
				$this->versionHandler->SetModuleVersionInstalling();
				$moduleManager = $this;
				include $file;
				$this->versionHandler->SetModuleVersion($newVersion);
			} else {
			   $this->logHandler->Debug('Module '.$this->moduleName.' is already at Version '.$newVersion);
			}
		}

		/**
		 * @public
		 *
		 * Update des aktuellen Modules auf die neueste Version. Es erfolgt zuerst ein Download des Modules,
		 * sollte sich die Version des Modules verändert haben, wird autom. auch das Installations Script
		 * ausgeführt.
		 *
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Speichern verwendet werden soll
		 */
		public function UpdateModule($sourceRepository='') {
		   if ($sourceRepository=='') {
		   	$sourceRepository = $this->sourceRepository;
		   }
		   $this->LoadModule($sourceRepository);
		   $this->InstallModule(false /*dont force Installation*/);
		}

		/**
		 * @public
		 *
		 * Update aller installierter Module
		 *
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Speichern verwendet werden soll
		 */
		public function UpdateAllModules($sourceRepository='') {
		   if ($sourceRepository=='') {
		   	$sourceRepository = $this->sourceRepository;
		   }
		   $moduleList = $this->versionHandler->GetInstalledModules();
			foreach ($moduleList as $module) {
				$moduleManager = new IPSModuleManager($module, $sourceRepository);
				$moduleManager->UpdateModule();
			}
		}

		/**
		 * @public
		 *
		 * Der Aufruf der Funktion versucht eine bereits installierte ModuleVersion zu reparier.
		 *
		 */
		public function RepairModule() {
		   $this->InstallModule(true /*ForceInstallation*/);
		}

		/**
		 * @public
		 *
		 * Exportiert eine Liste von Dateien anhand des Filetypes
		 *
		 * @param string $fileKey Type des Files (ScriptList, DefaultList, ExampleList, ...)
		 * @param string $fileTypeSection Filetype Section (app, config, webfront ...)
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Speichern verwendet werden soll
		 */
		private function DeployModuleFiles($fileKey, $fileTypeSection, $sourceRepository) {
		   $backupDirectory = $this->managerConfigHandler->GetValueDef('DeployBackupDirectory', '', IPS_GetKernelDir().'backup\\IPSLibrary_Deploy\\');
			$backupHandler   = new IPSBackupHandler($backupDirectory);

			$localList       = $this->GetScriptList($fileKey, $fileTypeSection, IPS_GetKernelDir().'scripts\\');
			$repositoryList  = $this->GetScriptList($fileKey, $fileTypeSection, $sourceRepository);
			$backupList      = $this->GetScriptList($fileKey, $fileTypeSection, $backupHandler->GetBackupDirectory());

			$this->backupHandler->CreateBackup($repositoryList, $backupList);
			$this->fileHandler->WriteFiles($localList, $repositoryList);
		}

		/**
		 * @public
		 *
		 * Exportiert einkomplettes Module zu einem Ziel Verzeichnis
		 *
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Speichern verwendet werden soll
		 */
		public function DeployModule($sourceRepository='') {
		   if ($sourceRepository=='') {
				$sourceRepository = $this->sourceRepository;
			}

			$this->DeployModuleFiles('DefaultFiles', 'App', $sourceRepository);
			$this->DeployModuleFiles('ScriptFiles',  'App', $sourceRepository);

			$this->DeployModuleFiles('ScriptFiles',  'Config', $sourceRepository);
			$this->DeployModuleFiles('DefaultFiles', 'Config', $sourceRepository);
			$this->DeployModuleFiles('ExampleFiles', 'Config', $sourceRepository);

			$this->DeployModuleFiles('DownloadFiles','Install', $sourceRepository);
			$this->DeployModuleFiles('InstallFiles', 'Install', $sourceRepository);
			$this->DeployModuleFiles('DefaultFiles', 'Install', $sourceRepository);
			$this->DeployModuleFiles('ExampleFiles', 'Install', $sourceRepository);

			$this->DeployModuleFiles('ScriptFiles',  'WebFront', $sourceRepository);
			$this->DeployModuleFiles('ExampleFiles', 'WebFront', $sourceRepository);
		}

		/**
		 * @public
		 *
		 * Exportiert alle installierten Module zu einem Ziel Verzeichnis
		 *
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Speichern verwendet werden soll
		 */
		public function DeployAllModules($sourceRepository='') {
		   if ($sourceRepository=='') {
				$sourceRepository = $this->sourceRepository;
			}
		   $moduleList = $this->versionHandler->GetInstalledModules();
			foreach ($moduleList as $module) {
				$moduleManager = new IPSModuleManager($module, $sourceRepository);
				$moduleManager->DeployModule();
			}
		}

	}

	/** @}*/
?>