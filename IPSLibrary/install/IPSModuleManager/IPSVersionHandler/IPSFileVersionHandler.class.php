<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
	 * @file          IPSFileVersionHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	include_once 'IPSVersionHandler.class.php';

   /**
    * @class IPSFileVersionHandler
    *
    * Implementierung einer Versions-Verwaltung der IPSLibrary Module auf Basis Files
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 17.02.2012<br/>
    */
	class IPSFileVersionHandler extends IPSVersionHandler {

		const FILE_INSTALLED_MODULES   = 'IPSLibrary\\config\\InstalledModules.ini';

		private $fileName;
		private $moduleList;

		/**
       * @public
		 *
		 * Initialisierung des IPSFileVersionHandler
		 *
		 * @param string $moduleName Name des Modules
		 */
		public function __construct($moduleName) {
			if ($moduleName=="") {
				die("ModuleName must have a Value!");
			}
			parent::__construct($moduleName);
			$this->fileName   = IPS_GetKernelDir().'scripts\\'.$this::FILE_INSTALLED_MODULES;

			$this->LoadVersionFile();
			if (!array_key_exists($moduleName, $this->moduleList)) {
			   $this->moduleList[$moduleName] = '';
			}
			
		}
		
		private function LoadVersionFile() {
		   if (file_exists($this->fileName)) {
			   $fileContent = file_get_contents($this->fileName);
			   $lines = explode(PHP_EOL, $fileContent);
			   foreach ($lines as $line) {
			      $content = explode('=', $line);
					if (count($content)>0) {
               	$this->moduleList[$content[0]] = $content[1];
               }
			   }
			   
		   } else {
				$this->moduleList = array();
			}
		}

		private function WriteVersionFile() {
			$fileContent = '';
			foreach ($this->moduleList as $moduleName=>$moduleVersion) {
			   if ($fileContent <> '') {
			      $fileContent .= PHP_EOL;
			   }
				$fileContent .= $moduleName.'='.$moduleVersion;
			}
			file_put_contents($this->fileName, $fileContent);
		}

		private function VersionToArray($moduleVersion) {
			if ($moduleVersion=='') {
				$moduleVersion = IPS_GetKernelVersion();
			}
			$moduleVersionArray = explode('.', $moduleVersion);
			$moduleVersionArray = array_pad($moduleVersionArray, 3, 0);
			$moduleVersionArray = array_pad($moduleVersionArray, 5, '');
			return $moduleVersionArray;
		}

		/**
		 * @public
		 *
		 * Lesen der aktuellen Module Version
		 *
		 * @return string Liefert die aktuelle Module Version
		 */
		public function GetModuleVersion() {
		   if ($this->moduleName == $this::MODULE_IPS) {
				$moduleVersion=IPS_GetKernelVersion();
		   } else {
				$moduleVersion=$this->moduleList[$this->moduleName];
		   }
			return $moduleVersion;
		}

		/**
		 * @public
		 *
		 * Lesen der aktuellen Module Version
		 *
		 * @return string Liefert die aktuelle Module Version as Array
		 */
		public function GetModuleVersionArray() {
			return $this->VersionToArray($this->GetModuleVersion());
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
			$versionInstalled = $versionHandler->GetModuleVersionArray();
			$versionRequired  = $this->VersionToArray($moduleVersion);
			
			if ($versionRequired[0] > $versionInstalled[0] or
			    $versionRequired[1] > $versionInstalled[1] or
				 $versionRequired[2] > $versionInstalled[2]) {
				 throw new IPSVersionHandlerException('Versions Fehler:'.PHP_EOL
																  .'========================================================================'.PHP_EOL
				                                      .'=== Modul '.$moduleName.' ist veraltet und benötigt ein Update'.PHP_EOL
				                                      .'===   Aktuelle Version:  '.$versionHandler->GetModuleVersion().PHP_EOL
				                                      .'===   Benötigte Version: '.$moduleVersion.PHP_EOL
																  .'========================================================================'.PHP_EOL
																  );
			}
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
			$versionInstalled = $this->GetModuleVersionArray();
			$versionRequired  = $this->VersionToArray($moduleVersion);

			$result = ($versionRequired[0] == $versionInstalled[0] and
			           $versionRequired[1] == $versionInstalled[1] and
				        $versionRequired[2] == $versionInstalled[2]);

			return $result;
	 	}
	 	
		/**
		 * @public
		 *
		 * Löschen eines Modules
		 */
		public function DeleteModule() {
			$this->logHandler->Log('Remove Module '.$this->moduleName.' from Versioning System');
		   unset($this->moduleList[$this->moduleName]);
		   $this->WriteVersionFile();
		}

		/**
		 * @public
		 *
		 * Schreiben der aktuellen Module Version
		 *
		 * @param string $moduleVersion Name des Modules
		 */
		public function SetModuleVersion($moduleVersion) {
			$this->logHandler->Log('Set Version '.$this->moduleName.'='.$moduleVersion);
		   $this->moduleList[$this->moduleName] = $moduleVersion;
		   $this->WriteVersionFile();
		}

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Loading" setzen
		 */
		public function SetModuleVersionLoading() {
		   $moduleVersionArray = $this->GetModuleVersionArray();
		   $moduleVersionArray[4] = $this::VERSION_LOADING;
		   $moduleVersion = implode('.',$moduleVersionArray);
			$this->SetModuleVersion($moduleVersion);
		}

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Loaded" setzen
		 */
		public function SetModuleVersionLoaded() {
		   $moduleVersionArray = $this->GetModuleVersionArray();
		   $moduleVersionArray[4] = $this::VERSION_LOADED;
		   $moduleVersion = implode('.',$moduleVersionArray);
			$this->SetModuleVersion($moduleVersion);
		}

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Installing" setzen
		 */
		public function SetModuleVersionInstalling() {
		   $moduleVersionArray = $this->GetModuleVersionArray();
		   $moduleVersionArray[4] = $this::VERSION_INSTALLING;
		   $moduleVersion = implode('.',$moduleVersionArray);
			$this->SetModuleVersion($moduleVersion);
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
			foreach ($this->moduleList as $moduleName=>$moduleVersion) {
				$result[$moduleName] = $moduleVersion;
			}
			return $result;
		}


	}

	/** @}*/
?>