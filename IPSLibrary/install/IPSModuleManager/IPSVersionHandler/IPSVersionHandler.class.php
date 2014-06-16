<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
 	 *
	 * @file          IPSVersionHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ("IPSLogHandler.class.php", "IPSLibrary::install::IPSModuleManager::IPSLogHandler");

   /**
    * @class IPSVersionHandlerException
    *
    * Definiert eine VersionHandler Exception
    *
    */
   class IPSVersionHandlerException extends Exception {
   }

   /**
    * @class IPSVersionHandler
    *
    * Versions Verwaltung der IPSLibrary Module
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	abstract class IPSVersionHandler{
		protected $moduleName = "";
		protected $logHandler = null;
		protected $installedModules;
		protected $knownRepositories;
		protected $knownModules;
		protected $repositoryVersions;
		protected $changeList;
		protected $requiredModules;

		const MODULE_IPS              = 'IPS';

		const STATE_LOADING           = 'Loading';
		const STATE_LOADED            = 'Loaded';
		const STATE_INSTALLING        = 'Installing';
		const STATE_DELETING          = 'Deleting';
		const STATE_INSTALLED         = 'OK';
		
		const PROPERTY_VERSION        = 0;
		const PROPERTY_SCRIPTVERSION  = 1;
		const PROPERTY_INSTALLVERSION = 2;
		const PROPERTY_STATE          = 3;
		const PROPERTY_REPOSITORY     = 4;

		/**
		 * @public
		 *
		 * Initialisierung des ModuleManagers
		 *
		 * @param string $moduleName Name des Modules
		 */
		public function __construct($moduleName) {
			$this->moduleName = $moduleName;
			$this->logHandler = IPSLogHandler::GetLogger(get_class($this));
			$this->installedModules   = array();
			$this->knownRepositories  = array();
			$this->knownModules       = array();
			$this->repositoryVersions = array();
			$this->changeList         = array();
			$this->requiredModules    = array();
		}

		/**
		 * @public
		 *
		 * Speicherung der Versions Daten
		 *
		 * @param string $moduleName Name des Modules
		 */
		abstract protected function StoreModuleVersions();

		/**
		 * @public
		 *
		 * Versions Daten neu laden
		 */
		abstract public function ReloadVersionData();

		/**
		 * @public
		 *
		 * Erzeugt das File KnownModules
		 */
		abstract public function BuildKnownModules();

		/**
		 * @public
		 *
		 * Erhöht die Versionsnummer im entsprechenden Download File und legt den übergebenen Text 
		 * unter der ChangeList des Modules ab.
		 * 
		 */
		abstract public function IncreaseModuleVersion($changeText);

		protected function VersionToArray($moduleVersion) {
			if ($moduleVersion=='') {
				$moduleVersion = IPS_GetKernelVersion();
			}
			$moduleVersionArray = explode('.', $moduleVersion);
			$moduleVersionArray = array_pad($moduleVersionArray, 3, 0);
			$moduleVersionArray = array_pad($moduleVersionArray, 5, '');
			
			return $moduleVersionArray;
		}
		
		protected function ArrayToVersion($moduleVersion) {
			unset($moduleVersion[4]);
			if ($moduleVersion[3]=='') {
				unset($moduleVersion[3]);
			}
			$moduleVersion = implode('.',$moduleVersion);
			
			return $moduleVersion;
		}
		
		private function GetProperty($property, $moduleName='') {
			if ($moduleName=='') {
				$moduleName = $this->moduleName;
			}
			$propertyList = explode('|', $this->installedModules[$moduleName]);
			$propertyList = array_pad($propertyList, 5, '');
			
			return $propertyList[$property];
		}

		private function SetProperty($property, $value) {
			$propertyList = explode('|', $this->installedModules[$this->moduleName]);
			$propertyList = array_pad($propertyList, 5, '');

			$propertyList[$property] = $value;

			$this->installedModules[$this->moduleName] = implode('|', $propertyList);
			$this->StoreModuleVersions();
		}

		/**
		 * @public
		 *
		 * Lesen der aktuellen Install Version
		 *
		 * @return string Install Version
		 */
		public function GetInstallVersion() {
			$moduleVersion = $this->GetProperty($this::PROPERTY_INSTALLVERSION);
			if ($moduleVersion=='') {
				$moduleVersion = $this->GetProperty($this::PROPERTY_VERSION);
			}
			$moduleVersion = $this->VersionToArray($moduleVersion);
			$moduleVersion = $this->ArrayToVersion($moduleVersion);

			return $moduleVersion;
		}

		
		/**
		 * @public
		 *
		 * Lesen der aktuellen Version eines Modules
		 *
		 * @param string $moduleName Name des Modules, das überprüft werden soll
		 * @return string Version
		 */
		public function GetVersion($moduleName) {
			$moduleVersion = $this->GetProperty($this::PROPERTY_SCRIPTVERSION, $moduleName);
			if ($moduleVersion=='') {
				$moduleVersion = $this->GetProperty($this::PROPERTY_VERSION, $moduleName);
			}
			$moduleVersion = $this->VersionToArray($moduleVersion);
			$moduleVersion = $this->ArrayToVersion($moduleVersion);

			return $moduleVersion;
		}
		
		/**
		 * @public
		 *
		 * Lesen der aktuellen Install Version
		 *
		 * @return string Install Version
		 */
		public function GetScriptVersion() {
			$moduleVersion = $this->GetProperty($this::PROPERTY_SCRIPTVERSION);
			if ($moduleVersion=='') {
				$moduleVersion = $this->GetProperty($this::PROPERTY_VERSION);
			}
			$moduleVersion = $this->VersionToArray($moduleVersion);
			$moduleVersion = $this->ArrayToVersion($moduleVersion);

			return $moduleVersion;
		}

		/**
		 * @public
		 *
		 * Lesen des aktuellen Modul Status
		 *
		 * @return string Liefert Modul Status
		 */
		public function GetModuleState() {
			$state = $this->GetProperty($this::PROPERTY_STATE);
			if ($state=='') {
				$moduleVersion = $this->GetProperty($this::PROPERTY_VERSION);
				$moduleVersion = $this->VersionToArray($moduleVersion);
				$state   = $moduleVersion[4];
				if ($state == '') {
					$state = $this::STATE_INSTALLED;
				}
			}
			return $state;
		}

		/**
		 * @public
		 *
		 * Überprüft die Version eines Modules mit dem Namen $moduleName and erzeugt einen Fehler, wenn
		 * die installierte Version < der übergebenen Version ist.
		 *
		 * Examples:
		 *   installed=2.5.3, required=2.5.3 --> OK
		 *   installed=2.5.3, required=2.5.2 --> OK
		 *   installed=2.5.3, required=2.4.4 --> OK
		 *   installed=2.4.3, required=2.5.2 --> Error
		 *   installed=2.5.1, required=2.5.2 --> Error
		 *
		 * @param string $moduleName Name des Modules, das überprüft werden soll
		 * @param string $moduleVersion Version des Modules
		 * @throws IPSVersionHandlerException wenn Version nicht korrekt ist
		 */
		public function CheckModuleVersion($moduleName, $moduleVersion) {
			$versionHandler = new IPSFileVersionHandler($moduleName);
			$versionInstalled = $versionHandler->GetInstallVersion();
			$versionInstalled = $this->VersionToArray($versionInstalled);
			$versionRequired  = $this->VersionToArray($moduleVersion);
			
			if (   (    $versionRequired[0] >  $versionInstalled[0]) 
			    or (    $versionRequired[0] == $versionInstalled[0]
			        and $versionRequired[1] >  $versionInstalled[1])
			    or (    $versionRequired[0] == $versionInstalled[0]
			        and $versionRequired[1] == $versionInstalled[1]
			        and $versionRequired[2] >  $versionInstalled[2])
			        ) {
				throw new IPSVersionHandlerException('Versions Fehler:'.PHP_EOL
				                                    .'========================================================================'.PHP_EOL
				                                    .'=== Modul '.$moduleName.' ist veraltet und benötigt ein Update'.PHP_EOL
				                                    .'===   Aktuelle Version:  '.$versionHandler->GetScriptVersion().PHP_EOL
				                                    .'===   Benötigte Version: '.$moduleVersion.PHP_EOL
				                                    .'========================================================================'.PHP_EOL
				                                    );
			}
		}

		/**
		 * @public
		 *
		 * Überprüft ob die installierte Version eines Modules < der übergebenen Version ist.
		 *
		 * Examples:
		 *   installed=2.5.3, required=2.5.3 --> OK
		 *   installed=2.5.3, required=2.5.2 --> OK
		 *   installed=2.5.3, required=2.4.4 --> OK
		 *   installed=2.4.3, required=2.5.2 --> Newer
		 *   installed=2.5.1, required=2.5.2 --> Newer
		 *
		 * @param string $moduleName Name des Modules, das überprüft werden soll
		 * @param string $moduleVersion Version des Modules
		 * @return boolean Übergebene Version ist neuer oder nicht
		 */
		public function IsVersionNewer($moduleName, $moduleVersion) {
			$versionHandler = new IPSFileVersionHandler($moduleName);
			$versionInstalled = $versionHandler->GetScriptVersion();
			$versionInstalled = $this->VersionToArray($versionInstalled);
			$versionRequired  = $this->VersionToArray($moduleVersion);
			
			return (    (    $versionRequired[0] >  $versionInstalled[0]) 
			         or (    $versionRequired[0] == $versionInstalled[0]
			             and $versionRequired[1] >  $versionInstalled[1])
			         or (    $versionRequired[0] == $versionInstalled[0]
			             and $versionRequired[1] == $versionInstalled[1]
			             and $versionRequired[2] >  $versionInstalled[2])
			        );
		}

		/**
		 * @public
		 *
		 * Vergleicht 2 Versionsnummern und überprüft ob die 2. übergebene Version neuer ist als die 1.
		 *
		 * @param string $moduleVersion1 Version 1 des Modules
		 * @param string $moduleVersion2 Version 2 des Modules 
		 * @return boolean Übergebene Version ist neuer oder nicht
		 */
		public function CompareVersionsNewer($moduleVersion1, $moduleVersion2) {
			$moduleVersion1 = $this->VersionToArray($moduleVersion1);
			$moduleVersion2 = $this->VersionToArray($moduleVersion2);
			
			return (    (    $moduleVersion2[0] >  $moduleVersion1[0]) 
			         or (    $moduleVersion2[0] == $moduleVersion1[0]
			             and $moduleVersion2[1] >  $moduleVersion1[1])
			         or (    $moduleVersion2[0] == $moduleVersion1[0]
			             and $moduleVersion2[1] == $moduleVersion1[1]
			             and $moduleVersion2[2] >  $moduleVersion1[2])
			        );
		}

		/**
		 * @public
		 *
		 * Vergleicht 2 Versionsnummern und überprüft ob die 2. übergebene Version gleich der 1.
		 *
		 * @param string $moduleVersion1 Version 1 des Modules
		 * @param string $moduleVersion2 Version 2 des Modules 
		 * @return boolean Übergebene Version ist neuer oder nicht
		 */
		public function CompareVersionsEqual($moduleVersion1, $moduleVersion2) {
			$moduleVersion1 = $this->VersionToArray($moduleVersion1);
			$moduleVersion2 = $this->VersionToArray($moduleVersion2);
			
			return ($moduleVersion1[0] == $moduleVersion2[0] or
			        $moduleVersion1[1] == $moduleVersion2[1] or
			        $moduleVersion1[2] == $moduleVersion2[2]);
		}


		/**
		 * @public
		 *
		 * Überprüft ob ein Module in der spezifizierten Version installiert ist.
		 *
		 * @param string $moduleVersion Version des Modules (Format IPSMajorVersion.IPSMinorVersion.ModuleVersion.ModuleStatus.InstallationStatus)
		 * @return boolean Liefert true wenn Module installiert, andernfalls false
		 */
	 	public function IsVersionInstalled($moduleVersion) {
			$versionInstalled = $this->GetInstallVersion();
			$versionInstalled = $this->VersionToArray($versionInstalled);
			$versionRequired  = $this->VersionToArray($moduleVersion);

			$result = ($versionRequired[0] == $versionInstalled[0] and
			           $versionRequired[1] == $versionInstalled[1] and
			           $versionRequired[2] == $versionInstalled[2]);

			return $result;
		}
		
		/**
		 * @public
		 *
		 * Überprüft ob ein Module in der spezifizierten Version installiert ist.
		 *
		 * @param string $moduleVersion Version des Modules (Format IPSMajorVersion.IPSMinorVersion.ModuleVersion.ModuleStatus.InstallationStatus)
		 * @return boolean Liefert true wenn Module installiert, andernfalls false
		 */
	 	public function IsModuleInstalled($module) {
			$result = array_key_exists($module, $this->installedModules);
			return $result;
		}
		
		/**
		 * @public
		 *
		 * Löschen eines Modules
		 */
		public function DeleteModule() {
			$this->logHandler->Log('Remove Module '.$this->moduleName.' from Versioning System');
			unset($this->installedModules[$this->moduleName]);
			$this->StoreModuleVersions();
		}

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Deleting" setzen
		 */
		public function SetVersionDeleting() {
			$this->logHandler->Log('Set State '.$this->moduleName.'=Deleting');
			$this->SetProperty($this::PROPERTY_STATE, $this::STATE_DELETING);
		}

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Loading" setzen
		 *
		 * @param string $version aktuelle Version des Modules
		 */
		public function SetVersionLoading($version) {
			$this->logHandler->Log('Set Version '.$this->moduleName.'='.$version.' (Loading)');
			$this->SetProperty($this::PROPERTY_SCRIPTVERSION, $version);
			$this->SetProperty($this::PROPERTY_STATE, $this::STATE_LOADING);
		}

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Loaded" setzen
		 *
		 * @param string $version aktuelle Version des Modules
		 */
		public function SetVersionLoaded($version) {
			$this->logHandler->Log('Set Version '.$this->moduleName.'='.$version.' (Loaded)');
			$this->SetProperty($this::PROPERTY_SCRIPTVERSION, $version);
			$this->SetProperty($this::PROPERTY_STATE, $this::STATE_LOADED);
		}

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Installing" setzen
		 *
		 * @param string $version aktuelle Version des Modules
		 */
		public function SetVersionInstalling($version) {
			$this->logHandler->Log('Set Version '.$this->moduleName.'='.$version.' (Installing)');
			$this->SetProperty($this::PROPERTY_INSTALLVERSION, $version);
			$this->SetProperty($this::PROPERTY_STATE, $this::STATE_INSTALLING);
		}

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Installed" setzen
		 *
		 * @param string $version aktuelle Version des Modules
		 */
		public function SetVersionInstalled($version) {
			$this->logHandler->Log('Set Version '.$this->moduleName.'='.$version.' (Installed)');
			$this->SetProperty($this::PROPERTY_INSTALLVERSION, $version);
			$this->SetProperty($this::PROPERTY_VERSION, $version);
			$this->SetProperty($this::PROPERTY_STATE, $this::STATE_INSTALLED);
		}

		/**
		 * @public
		 *
		 * Liefert verwendetes Repository des Modules
		 *
		 * @return string Modul Repository
		 */
		public function GetModuleRepository($defaultRepository='') {
			$repository = $this->GetProperty($this::PROPERTY_REPOSITORY);
			if ($repository=='') {
				$repository = $defaultRepository;
			}
			return $repository;
		}

		/**
		 * @public
		 *
		 * Setzen des verwendeten Modul Repositories
		 *
		 * @param string $repository Modul Repository
		 */
		public function SetModuleRepository($repository='') {
			$this->logHandler->Log('Set Repository '.$this->moduleName.'='.$repository);
			$this->SetProperty($this::PROPERTY_REPOSITORY, $repository);
		}

		/**
		 * @public
		 *
		 * Liefert eine Liste aller installierten Module
		 *
		 * @return string[] Liste der installierten Module
		 */
		public function GetInstalledModules() {
			$result = array();
			foreach ($this->installedModules as $moduleName=>$moduleVersion) {
				$result[$moduleName] = $this->GetProperty($this::PROPERTY_VERSION, $moduleName);
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Liefert kurze Beschreibung des Modules
		 *
		 * @param string $moduleName Name des Modules
		 * @return string Modul Description
		 */
		public function GetModuleInfos($moduleName) {
			$result = array();
			if (array_key_exists($moduleName, $this->knownModules)) {
				$result = $this->knownModules[$moduleName];
			} else {
				$result['Version'] =  '';
				$result['Repository'] = '';
				$result['Description'] = '';
				$result['Path'] = '';
				$result['LastRepository'] = '';
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Liefert eine Liste aller bekannten Module
		 *
		 * @return string[] Liste der bekannten Module
		 */
		public function GetKnownModules() {
			$result = $this->knownModules;

			return $result;
		}

		/**
		 * @public
		 *
		 * Liefert eine Liste aller bekannten Repositories
		 *
		 * @return string[] Liste der installierten Module
		 */
		public function GetKnownRepositories() {
			return $this->knownRepositories['Repository'];
		}

		/**
		 * @public
		 *
		 * Liefert eine Liste der Repositories und Versionen eines Modules
		 *
		 * @param string $moduleName Name des Modules
		 * @return string[] Liste der Repository Versionen
		 */
		public function GetRepositoryVersions($moduleName) {
			if (array_key_exists($moduleName, $this->repositoryVersions)) {
				return $this->repositoryVersions[$moduleName];
			} else {
				return array();
			}
		}

		/**
		 * @public
		 *
		 * Liefert eine Liste Changes zu einer übergebenen Version
		 *
		 * @param string $moduleName Name des Modules
		 * @return string[] Liste der Repository Versionen
		 */
		public function GetChangeList($moduleName, $filterInstalledChanges=true) {
			$changeList = array();
			$version = '';
			if ($this->IsModuleInstalled($moduleName)) {
				$version = $this->GetVersion($moduleName);
			}
			if (array_key_exists($moduleName, $this->changeList)) {
				foreach ($this->changeList[$moduleName] as $changeVersion=>$changeText) {
					if ($this->CompareVersionsNewer($version, $changeVersion) or !$filterInstalledChanges) {
						$changeList[$changeVersion] = $changeText;
					}
				}
			}
			uksort($changeList, 'version_compare_custom');  
			
			return $changeList;
		}

		
		
		/**
		 * @public
		 *
		 * Liefert eine Liste der benötigten Module und Versionen zu einem Module
		 *
		 * @param string $moduleName Name des Modules
		 * @return string[] Liste der benötigten Module
		 */
		public function GetRequiredModules($moduleName='') {
			if ($moduleName=='') {
				$moduleName = $this->moduleName;
			}
			$requiredModules = array();
			if (array_key_exists($moduleName, $this->requiredModules)) {
				$requiredModules = $this->requiredModules[$moduleName];
			}
			return $requiredModules;
		}

		/**
		 * @public
		 *
		 * Liefert eine Liste der benötigten Updates zu einem Module (benötigte SubModule
		 * die nicht vorhanden sind oder die ein Update benötigen).
		 *
		 * @param string $moduleName Name des Modules
		 * @return string[] Liste der benötigten Module
		 */
		public function GetRequiredUpdates($moduleName='') {
			if ($moduleName=='') {
				$moduleName = $this->moduleName;
			}
			$requiredUpdates = array();
			if (array_key_exists($moduleName, $this->requiredModules)) {
				foreach ($this->requiredModules[$moduleName] as $requiredModule=>$requiredVersion) {
					if ($this->IsVersionNewer($requiredModule, $requiredVersion)) {
						$requiredUpdates[$requiredModule] = $requiredVersion;
					}
				}
			}
			return $requiredUpdates;
		}

		private function GetKnownModuleUpdates($moduleName) {
			$result           = array();
			$moduleInfos      = $this->GetModuleInfos($moduleName);
			$requiredVersion  = null;
			$installedVersion = null;
			if (array_key_exists('Version', $moduleInfos)) {
				$requiredVersion  = $moduleInfos['Version'];
			}
			if ($this->IsModuleInstalled($moduleName)) {
				$installedVersion = $this->GetVersion($moduleName);
			}
			if (   !$this->IsModuleInstalled($moduleName) 
			    or $this->CompareVersionsNewer($installedVersion, $requiredVersion)) {
				//echo $moduleName.'--> Installed='.$installedVersion.', Required='.$requiredVersion.PHP_EOL;
				$requiredModules = $this->GetRequiredModules($moduleName);
				foreach($requiredModules as $requiredModule=>$version) {
					$requiredUpdates = $this->GetKnownModuleUpdates($requiredModule);
					foreach($requiredUpdates as $idx=>$requiredUpdate) {
						$result[] = $requiredUpdate;
					}
				}
				$result[] = $moduleName;
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Liefert eine Liste der mit Modulen, für die ein Update verfügbar ist
		 *
		 * @return string[] Liste der Module
		 */
		public function GetKnownUpdates() {
			$result           = array();
			$knownUpdates     = array();
			$installedModules = $this->GetInstalledModules();
			
			foreach ($installedModules as $moduleName=>$version) {
				if (array_key_exists($moduleName, $this->knownModules)) {
					$updates = $this->GetKnownModuleUpdates($moduleName); 
					
					foreach ($updates as $idx=>$module) {
						if (!array_key_exists($module, $knownUpdates)) {
							$knownUpdates[$module] = $module;
							$result[]              = $module;
						}
					}
				}
			}
			return $result;
		}

	}

	
	function version_compare_custom($a, $b) {
		$result = 0;
		$partsA = explode(".", $a);
		$partsB = explode(".", $b);
		$maxParts = min(count($partsA), count($partsB));
		for($i = 0; $i < $maxParts; $i++) {
			if(is_numeric($partsA[$i]) && is_numeric($partsB[$i])) {
				$va = (int) $partsA[$i];
				$vb = (int) $partsB[$i];
				$result = $va == $vb ? 0 : ($va > $vb ? -1 : 1);
			} else {
				$result = $a == $b ? 0 : ($a > $b ? -1 : 1);
			}
			if($result != 0) {
				break;
			}
		}
		return $result;
	} 
	/** @}*/
?>