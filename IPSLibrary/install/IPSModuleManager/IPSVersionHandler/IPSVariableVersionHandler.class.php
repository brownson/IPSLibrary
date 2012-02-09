<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
	 * @file          IPSVariableVersionHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	include 'IPSVersionHandler.class.php';

   /**
    * @class IPSVariableVersionHandler
    *
    * Implementierung einer Versions-Verwaltung der IPSLibrary Module auf Basis von
    * IPS Variablen.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	class IPSVariableVersionHandler extends IPSVersionHandler {

		private $instanceId;
		private $librayBasePath;

		/**
       * @public
		 *
		 * Initialisierung des IPSVariableVersionHandler
		 *
		 * @param string $moduleName Name des Modules
		 * @param string $libraryBasePath BasisPfad zur IPSLibrary (z.B 'Program')
		 */
		public function __construct($moduleName, $libraryBasePath) {
			if ($moduleName=="") {
				die("ModuleName must have a Value!");
			}
			parent::__construct($moduleName);
			$this->InitIPSStructure($libraryBasePath);
		}

		private function InitIPSStructure($libraryBasePath) {
			$path = 'IPSLibrary.install.IPSModuleManager.IPSVersionHandler';
			if ($libraryBasePath <> '') {
				$path = $libraryBasePath.'.'.$path;
			}
			$categoryId            = CreateCategoryPath($path);
			$this->instanceId      = CreateDummyInstance("IPSLibrary", $categoryId, 30);
			$this->libraryBasePath = $libraryBasePath;
		}

		private function GetVariableId() {
			$id = @IPS_GetObjectIDByName($this->moduleName, $this->instanceId);
			if ($id===false) {
				$position = count(IPS_GetChildrenIDs($this->instanceId)) * 10 + 10;
				$id = CreateVariable ($this->moduleName, 3 /*String*/, $this->instanceId, $position, "~String");
			}
			return $id;
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
				$moduleVersion=GetValue($this->GetVariableId());
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
		   $versionHandler = new IPSVariableVersionHandler($moduleName, $this->libraryBasePath);
			$versionInstalled = $versionHandler->GetModuleVersionArray();
			$versionRequired  = $this->VersionToArray($moduleVersion);
			
			if ($versionRequired[0] > $versionInstalled[0] or
			    $versionRequired[1] > $versionInstalled[1] or
				 $versionRequired[2] > $versionInstalled[2]) {
				 throw new IPSVersionHandlerException('Required Version '.$moduleVersion.' is lower current Version '.$this->GetModuleVersion());
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
		 * Schreiben der aktuellen Module Version
		 *
		 * @param string $moduleVersion Name des Modules
		 */
		public function SetModuleVersion($moduleVersion) {
			$this->logHandler->Log('Set Version '.$this->moduleName.'='.$moduleVersion);
			SetValue($this->GetVariableId(), $moduleVersion);
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
			$childrenIds = IPS_GetChildrenIDs($this->instanceId);
			$result = array();
			foreach ($childrenIds as $id) {
				$result[] = IPS_GetName($id);
			}
			return $result;
		}
	}

	/** @}*/
?>