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
	 */

	IPSUtils_Include ("IPSInstaller.inc.php",                  "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSFileVersionHandler.class.php",   	  "IPSLibrary::install::IPSModuleManager::IPSVersionHandler");
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
	 *  Version 2.5.1, 05.01.2012<br/>
	 *  Version 2.5.3, 29.10.2012  Adapted Version Handling<br/>
	 */
	class IPSModuleManager{

 		const DOWNLOADLISTFILE_PATH            = "IPSLibrary\\install\\DownloadListFiles\\";
		const DOWNLOADLISTFILE_SUFFIX          = '_FileList.ini';
 		const INSTALLATIONSCRIPT_PATH          = "IPSLibrary\\install\\InstallationScripts\\";
		const INSTALLATIONSCRIPT_SUFFIX        = '_Installation.ips.php';
		const DEINSTALLATIONSCRIPT_SUFFIX      = '_Deinstallation.ips.php';
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
		 * @param string $logDirectory Vrezeichnis das zum Loggen verwendet werden soll 
		 * @param string $silentMode bei TRUE werden Meldungen nicht mit ECHO gelogged
		 */
		public function __construct($moduleName='', $sourceRepository='', $logDirectory='', $silentMode=false) {
			$_IPS['ABORT_ON_ERROR'] = true;
			$_IPS['MODULEMANAGER']  = $this;

			if ($moduleName=='') {
				$moduleName = 'IPSModuleManager';
			}
			$this->moduleName           = $moduleName;

			// Create ConfigHandler for ModuleManager INI File
			$this->managerConfigHandler = new IPSIniConfigHandler($this->GetModuleInitializationFile('IPSModuleManager'));

			$this->sourceRepository = $sourceRepository;
			if ($this->sourceRepository=='') {
				$this->sourceRepository = $this->managerConfigHandler->GetValue(IPSConfigHandler::SOURCEREPOSITORY, '');
			}
			$this->sourceRepository = IPSFileHandler::AddTrailingPathDelimiter($this->sourceRepository);

			// Create Log Handler
			if ($logDirectory=='') {
				$logDirectory = $this->managerConfigHandler->GetValueDef(IPSConfigHandler::LOGDIRECTORY, '', IPS_GetKernelDir().'logs\\');
			}
			$this->logHandler = new IPSLogHandler(get_class($this), $logDirectory, $moduleName, true, $silentMode);
		   
			// Create Version Handler
			$this->versionHandler   = new IPSFileVersionHandler($moduleName);

			// Create Script Handler
			$libraryBasePath           = 'Program';
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
			$this->fileConfigHandler = new IPSIniConfigHandler($this->GetModuleDownloadListFile(IPS_GetKernelDir().'scripts\\'));

			// ConfigHandler for Module INI File
			$moduleIniFile = $this->GetModuleInitializationFile($moduleName);
			if (!file_exists($moduleIniFile)) {
				$moduleLocalDefaultIniFile      = $this->GetModuleDefaultInitializationFile($moduleName, IPS_GetKernelDir().'scripts\\');
				$moduleRepositoryDefaultIniFile = $this->GetModuleDefaultInitializationFile($moduleName, $this->sourceRepository);
				$this->logHandler->Log('Module Ini File doesnt exists -> Load Default Ini File "'.$moduleLocalDefaultIniFile.'"');
				$this->fileHandler->LoadFiles(array($moduleRepositoryDefaultIniFile), array($moduleLocalDefaultIniFile));
				$this->fileHandler->CreateScriptsFromDefault(array($moduleLocalDefaultIniFile));
			}
			$this->moduleConfigHandler  = new IPSIniConfigHandler($moduleIniFile);

			// Increase PHP Timeout for current Session
			$timeLimit = $this->managerConfigHandler->GetValueIntDef('TimeLimit', '', '300'); /*5 Minuten*/
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
		 * @public
		 *
		 * Liefert die ensprechenden Pfad im IPSLibrary Objektbaum
		 *
		 * @param string $type Zweig im Objektbaum ('app','config' oder 'data')
		 * @return int Pfad der Kategorie
		 */
		public function GetModuleCategoryPath($type='app') {
		   if ($type<>'app' and $type<>'config' and $type<>'data') {
		      throw new Exception('Unknown Category Type '.$type);
		   }
			$namespace  = $this->fileConfigHandler->GetValue(IPSConfigHandler::MODULENAMESPACE);
			$namespace  = str_replace('::app::','::'.$type.'::',$namespace);
		   $path       = 'Program.'.str_replace('::','.',$namespace);

			return $path;
		}

		/**
		 * @public
		 *
		 * Liefert die ensprechende ID im IPSLibrary Objektbaum
		 *
		 * @param string $type Zweig im Objektbaum ('app','config' oder 'data')
		 * @param boolean $createNonExisting Anlegen des Baumes, falls nicht vorhanden
		 * @return int ID der Kategorie
		 */
		public function GetModuleCategoryID($type='app', $createNonExisting=true) {
		   $path       = $this->GetModuleCategoryPath($type);
			$categoryID = IPSUtil_ObjectIDByPath($path, true);
			
			if ($categoryID===false and $createNonExisting) {
			   $categoryID = CreateCategoryPath($path);
			}

			return $categoryID;
		}

		/**
		 * @public
		 *
		 * Liefert Namen des aktuellen LogFiles
		 *
		 * @return string Name des LogFiles
		 */
		public function GetLogFileName() {
		   return $this->logHandler->GetLogFileName();
		}

		/**
		 * @public
		 *
		 * Liefert ein Array aller installierten Module
		 *
		 * Aufbau:
		 * array('Module1' => Version,
		 *       'Module2' => Version,
		 *       ...
		 *       'ModuleX' => Version)
		 *
		 * @return string Array der Installierten Module
		 */
		public function GetInstalledModules() {
			$resultList = $this->versionHandler->GetInstalledModules();

			return $resultList;
		}

		/**
		 * @public
		 *
		 * Liefert die ID des Objectes, mit dem das Modul konfiguriert werden kann.
		 * Falls kein Objekt gefunden wird, dann liefert die Funktion FALSE zurück.
		 *
		 * @return integer ID des Objectes
		 */
		public function GetConfigurationObjectID() {
			$configList       = $this->GetScriptList('DefaultFiles', 'Config', IPS_GetKernelDir().'scripts\\');
			if (count($configList)==0) {
			   return false;
			}
			$configDefaultFile = $configList[0];
			$configFile = IPSFileHandler::GetUserFilenameByDefaultFilename($configDefaultFile);
			$scriptPath = $this->scriptHandler->GetScriptPathByFileName($configFile);
			$scriptName = $this->scriptHandler->GetScriptNameByFileName($configFile);
			$scriptID   = IPSUtil_ObjectIDByPath($scriptPath.'.'.$scriptName,true);
			return $scriptID;
		}

		/**
		 * @public
		 *
		 * Liefert ein Array mit Informationen zu dem Module zurück
		 *
		 * @return string[] Infos zu Modul
		 */
		public function GetModuleInfos() {
			$infos = $this->versionHandler->GetModuleInfos($this->moduleName);

			$infos['Installed']      = ($this->versionHandler->IsModuleInstalled($this->moduleName)?'Yes':'No');
			$infos['CurrentVersion'] = $this->versionHandler->GetScriptVersion();
			$infos['State']          = $this->versionHandler->GetModuleState();
			$infos['LastRepository'] = $this->versionHandler->GetModuleRepository();

			return $infos;
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
		 * @return string liefert Installation Filename des Modules
		 */
		private function GetModuleDeinstallationScript() {
			$path = IPS_GetKernelDir().'scripts\\'.$this::INSTALLATIONSCRIPT_PATH;
			$file = $this->moduleName.$this::DEINSTALLATIONSCRIPT_SUFFIX;
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
			if ($fileKey=='DownloadFiles') {
				return array($this->GetModuleDownloadListFile($baseDirectory));
			}
		
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
							} else {
								$fullScriptName   = $baseDirectory.'\\IPSLibrary\\install\\InstallationScripts\\'.$script;
							}
							break;
						default:
							die('Unknown fileTypeSection '.$fileTypeSection);
					}
					$fullScriptName   = str_replace('::', '\\', $fullScriptName);
					$fullScriptName   = str_replace('\\\\', '\\', $fullScriptName);
					$fullScriptName   = str_replace('\\192.168', '\\\\192.168', $fullScriptName);

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
			$registerInstallFiles = $this->GetConfigValueBoolDef('RegisterInstallFiles', '', '');

			if ($fileKey=='DefaultFiles') {
				$this->scriptHandler->RegisterUserScriptsListByDefaultFilename($fileList);
			}

			if ((!$registerDefaultFiles and $fileKey=='DefaultFiles') or
				(!$registerExampleFiles and $fileKey=='ExampleFiles')) {
				return;
			}

			if (!$registerInstallFiles and
			    ($this->moduleName=='IPSModuleManager' or $fileTypeSection=='Install')) {
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
		 * @param boolean $overwriteUserFiles bestehende User Files mit Default überschreiben
		 */
		private function LoadModuleFiles($fileKey, $fileTypeSection, $sourceRepository, $overwriteUserFiles=false) {
			$localList       = $this->GetScriptList($fileKey, $fileTypeSection, IPS_GetKernelDir().'scripts\\');
			$repositoryList  = $this->GetScriptList($fileKey, $fileTypeSection, $sourceRepository);
			$backupList      = $this->GetScriptList($fileKey, $fileTypeSection, $this->backupHandler->GetBackupDirectory());

			$this->backupHandler->CreateBackup($localList, $backupList);

			$this->fileHandler->LoadFiles($repositoryList, $localList);
			if ($fileKey=='DefaultFiles') {
				$this->fileHandler->CreateScriptsFromDefault($localList, $overwriteUserFiles);
			}
			$this->RegisterModuleFiles($fileKey, $fileTypeSection, $localList);
		}

		/**
		 * @public
		 *
		 * Lädt alle zugehörigen Files des Modules von einem Source Repository
		 *
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Laden verwendet werden soll
		 * @param boolean $overwriteUserFiles bestehende User Files mit Default überschreiben
		 */
		public function LoadModule($sourceRepository='', $overwriteUserFiles=false) {
			if ($sourceRepository=='') {
				$sourceRepository = $this->sourceRepository;
			}
			$sourceRepository = IPSFileHandler::AddTrailingPathDelimiter($sourceRepository);

			$this->LoadModuleFiles('DownloadFiles','Install',  $sourceRepository, $overwriteUserFiles);
			$this->fileConfigHandler = new IPSIniConfigHandler($this->GetModuleDownloadListFile(IPS_GetKernelDir().'scripts\\'));

			$newVersion = $this->fileConfigHandler->GetValueDef(IPSConfigHandler::SCRIPTVERSION, null, 
			                                                    $this->fileConfigHandler->GetValue(IPSConfigHandler::SCRIPTVERSION));
			$this->versionHandler->SetVersionLoading($newVersion);
			$this->versionHandler->SetModuleRepository($sourceRepository);

			$this->LoadModuleFiles('InstallFiles', 'Install',  $sourceRepository, $overwriteUserFiles);
			$this->LoadModuleFiles('DefaultFiles', 'Install',  $sourceRepository, $overwriteUserFiles);
			$this->LoadModuleFiles('ExampleFiles', 'Install',  $sourceRepository, $overwriteUserFiles);

			$this->LoadModuleFiles('ScriptFiles',  'App',      $sourceRepository, $overwriteUserFiles);
			$this->LoadModuleFiles('DefaultFiles', 'App',      $sourceRepository, $overwriteUserFiles);

			$this->LoadModuleFiles('ScriptFiles',  'Config',   $sourceRepository, $overwriteUserFiles);
			$this->LoadModuleFiles('DefaultFiles', 'Config',   $sourceRepository, $overwriteUserFiles);
			$this->LoadModuleFiles('ExampleFiles', 'Config',   $sourceRepository, $overwriteUserFiles);

			$this->LoadModuleFiles('ScriptFiles',  'WebFront', $sourceRepository, $overwriteUserFiles);
			$this->LoadModuleFiles('ExampleFiles', 'WebFront', $sourceRepository, $overwriteUserFiles);

			$this->versionHandler->SetVersionLoaded($newVersion);
		}

		/**
		 * @public
		 *
		 * Installiert ein Module,
		 *
		 * @param string $forceInstallation wenn true, wird auch eine Installation ausgeführt, wenn sich die Version des Modules nicht geändert hat
		 */
		public function InstallModule($forceInstallation = true) {
			$newVersion = $this->fileConfigHandler->GetValueDef(IPSConfigHandler::INSTALLVERSION, null, 
			                                                    $this->fileConfigHandler->GetValue(IPSConfigHandler::SCRIPTVERSION));
			if (!$this->versionHandler->IsVersionInstalled($newVersion) or $forceInstallation) {
				$this->versionHandler->SetVersionInstalling($newVersion);
				$moduleManager = $this;
				$file =  $this->GetModuleInstallationScript();
				include $file;
			} else {
				$this->logHandler->Debug('Module '.$this->moduleName.' is already at installed Version '.$newVersion);
			}
			$this->versionHandler->SetVersionInstalled($newVersion);
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
			$sourceRepository = IPSFileHandler::AddTrailingPathDelimiter($sourceRepository);
			$this->LoadModule($sourceRepository);
			$this->InstallModule(false /*dont force Installation*/);
		}

		/**
		 * @public
		 *
		 * Update aller installierter Module
		 *
		 */
		public function UpdateAllModules() {
			$moduleList = $this->versionHandler->GetKnownUpdates();
			foreach ($moduleList as $idx=>$module) {
				$moduleInfos = $this->versionHandler->GetModuleInfos($module);
				$repository = $moduleInfos['Repository'];

				$moduleManager = new IPSModuleManager($module, $repository);
				$moduleManager->UpdateModule();
			}
		}

		/**
		 * @public
		 *
		 * Der Aufruf der Funktion versucht eine bereits installierte ModuleVersion zu reparieren.
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
		private function DeleteModuleFiles($fileKey, $fileTypeSection) {
			$backupDirectory = $this->managerConfigHandler->GetValueDef('DeployBackupDirectory', '', IPS_GetKernelDir().'backup\\IPSLibrary_Delete\\');
			$backupHandler   = new IPSBackupHandler($backupDirectory);

			$localList       = $this->GetScriptList($fileKey, $fileTypeSection, IPS_GetKernelDir().'scripts\\');
			$backupList      = $this->GetScriptList($fileKey, $fileTypeSection, $backupHandler->GetBackupDirectory());

			$this->logHandler->Log('Delete Files with Key='.$fileKey.' and Section='.$fileTypeSection);
			foreach ($localList as $idx=>$file) {
				if ($fileKey=='DefaultFiles') {
					$userFile   = IPSFileHandler::GetUserFilenameByDefaultFilename($file);
					$backupFile = IPSFileHandler::GetUserFilenameByDefaultFilename($backupList[$idx]);
					$this->backupHandler->CreateBackupFromFile($userFile, $backupFile);
					$this->scriptHandler->UnregisterScriptByFilename($userFile);
					$this->fileHandler->DeleteFile($userFile);
				}
				$this->backupHandler->CreateBackupFromFile($file, $backupList[$idx]);
				$this->scriptHandler->UnregisterScriptByFilename($file);
				$this->fileHandler->DeleteFile($file);
				$this->fileHandler->DeleteEmptyDirectories($file);
			}
		}

		private function DeleteModuleObjects($path, $exclusiveSwitch=true) {
		   if ($path <> '' and $exclusiveSwitch) {
				$categoryID = IPSUtil_ObjectIDByPath($path, true);
				if ($categoryID===false) {
					$this->logHandler->Debug('Path '.$path.' not found...');
					return;
				}
				$this->logHandler->Log('Delete Objects in Category='.$path.', ID='.$categoryID);

				DeleteCategory($categoryID);
			}
		}

		private function DeleteWFCItems($wfcItemPrefix, $exclusiveSwitch=true) {
			if ($wfcItemPrefix <> '' and $exclusiveSwitch) {
				$wfcConfigID = $this->GetConfigValueIntDef('ID', 'WFC10', GetWFCIdDefault());
				$this->logHandler->Log('Delete WFC Items with Prefix='.$wfcItemPrefix);
				DeleteWFCItems($wfcConfigID, $wfcItemPrefix);
				ReloadAllWebFronts();
		   }
		}

		/**
		 * @public
		 *
		 * Löscht ein Module aus IP-Symcon
		 *
		 * Es werden folgende Komponenten gelöscht
		 *   - Alle WebFront Seiten, die autom. generiert wurden
		 *   - Alle Mobile Interface Einträge
		 *   - Alle Variablen und Scripte in IPS
		 *   - Alle zugehörigen Dateien
		 *
		 */
		public function DeleteModule() {
			if ($this->moduleName=='IPSModuleManager') {
				throw new Exception('Deinstallation of IPSModuleManager currenty NOT supported !!!');
				
				$this->DeleteModuleObjects('Program.IPSLibrary.install');
				$this->DeleteModuleObjects('Program.IPSLibrary.app.core.IPSUtils');
				$this->DeleteModuleObjects('Program.IPSLibrary.app.core.IPSConfigHandler');
			} else {
				if ($this->moduleConfigHandler->GetValueDef('TabItem', 'WFC10', '') <> '') {
					$this->DeleteWFCItems($this->moduleConfigHandler->GetValueDef('TabPaneItem', 'WFC10', '').$this->moduleConfigHandler->GetValueDef('TabItem', 'WFC10', ''));
				}
				for ($idx=1;$idx<=10;$idx++) {
					if ($this->moduleConfigHandler->GetValueDef('TabItem'.$idx, 'WFC10', '') <> '') {
						$this->DeleteWFCItems($this->moduleConfigHandler->GetValueDef('TabPaneItem', 'WFC10', '').$this->moduleConfigHandler->GetValueDef('TabItem'.$idx, 'WFC10', ''));
					}
				}
				$this->DeleteWFCItems($this->moduleConfigHandler->GetValueDef('TabPaneItem', 'WFC10', ''),
				                      $this->moduleConfigHandler->GetValueBoolDef('TabPaneExclusive', 'WFC10', false));

				$namespace  = $this->fileConfigHandler->GetValue(IPSConfigHandler::MODULENAMESPACE);
				$this->DeleteModuleObjects($this->GetModuleCategoryPath('app'));
				$this->DeleteModuleObjects($this->GetModuleCategoryPath('data'));
				$this->DeleteModuleObjects($this->GetModuleCategoryPath('config'));
				$this->DeleteModuleObjects($this->moduleConfigHandler->GetValueDef('Path', 'WFC10', ''));
				$this->DeleteModuleObjects($this->moduleConfigHandler->GetValueDef('Path', 'Mobile', ''),
				                           $this->moduleConfigHandler->GetValueBoolDef('PathExclusive', 'Mobile', false));
				$this->DeleteModuleObjects($this->moduleConfigHandler->GetValueDef('Path', 'Mobile', '').'.'.$this->moduleConfigHandler->GetValueDef('Name', 'Mobile', ''));
				for ($idx=1;$idx<=10;$idx++) {
					$this->DeleteModuleObjects($this->moduleConfigHandler->GetValueDef('Path', 'Mobile', '').'.'.$this->moduleConfigHandler->GetValueDef('Name'.$idx, 'Mobile', ''));
				}
			}

			$deinstallationScriptName = $this->GetModuleDeinstallationScript();
			if (file_exists($deinstallationScriptName)) {
				$this->logHandler->Log('Execute Deinstallation Script '.$deinstallationScriptName);
				include_once $deinstallationScriptName;
			}

			$this->DeleteModuleFiles('DefaultFiles', 'App');
			$this->DeleteModuleFiles('ScriptFiles',  'App');
			$this->DeleteModuleFiles('ScriptFiles',  'Config');
			$this->DeleteModuleFiles('DefaultFiles', 'Config');
			$this->DeleteModuleFiles('ExampleFiles', 'Config');
			$this->DeleteModuleFiles('ScriptFiles',  'WebFront');
			$this->DeleteModuleFiles('ExampleFiles', 'WebFront');
			$this->DeleteModuleFiles('InstallFiles', 'Install');
			$this->DeleteModuleFiles('DefaultFiles', 'Install');
			$this->DeleteModuleFiles('ExampleFiles', 'Install');
			$this->DeleteModuleFiles('DownloadFiles','Install');

			$this->versionHandler->DeleteModule();
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

			$this->fileHandler->FilterEqualFiles($localList, $repositoryList);
			$this->fileHandler->WriteFiles($localList, $repositoryList);
		}

		/**
		 * @public
		 *
		 * Exportiert einkomplettes Module zu einem Ziel Verzeichnis
		 *
		 * @param string $sourceRepository Pfad/Url zum Source Repository, das zum Speichern verwendet werden soll
		 * @param string $changeText Text der für die ChangeList verwendet werden soll
		 * @param boolean $installationRequired Installation durch Änderung notwendig
		 */
		public function DeployModule($sourceRepository='', $changeText='', $installationRequired=false) {
			if ($sourceRepository=='') {
				$sourceRepository = $this->sourceRepository;
			}
			$sourceRepository = IPSFileHandler::AddTrailingPathDelimiter($sourceRepository);

			$this->logHandler->Log('Start Deploy of Module "'.$this->moduleName.'"');
			if ($changeText<>'') {
				$this->versionHandler->IncreaseModuleVersion($changeText, $installationRequired);
			}
			
			$this->DeployModuleFiles('DownloadFiles','Install',  $sourceRepository);

			$this->DeployModuleFiles('DefaultFiles', 'App',      $sourceRepository);
			$this->DeployModuleFiles('ScriptFiles',  'App',      $sourceRepository);

			$this->DeployModuleFiles('ScriptFiles',  'Config',   $sourceRepository);
			$this->DeployModuleFiles('DefaultFiles', 'Config',   $sourceRepository);
			$this->DeployModuleFiles('ExampleFiles', 'Config',   $sourceRepository);

			$this->DeployModuleFiles('DownloadFiles','Install',  $sourceRepository);
			$this->DeployModuleFiles('InstallFiles', 'Install',  $sourceRepository);
			$this->DeployModuleFiles('DefaultFiles', 'Install',  $sourceRepository);
			$this->DeployModuleFiles('ExampleFiles', 'Install',  $sourceRepository);

			$this->DeployModuleFiles('ScriptFiles',  'WebFront', $sourceRepository);
			$this->DeployModuleFiles('ExampleFiles', 'WebFront', $sourceRepository);

			$this->logHandler->Log('Finished Deploy of Module "'.$this->moduleName.'"');
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
			foreach ($moduleList as $module=>$version) {
				$moduleManager = new IPSModuleManager($module, $sourceRepository);
				$moduleManager->DeployModule();
			}
		}

	}

	/** @}*/
?>