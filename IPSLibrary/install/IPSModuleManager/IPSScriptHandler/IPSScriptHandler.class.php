<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
 	 *
	 * @file          IPSScriptHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ("IPSInstaller.inc.php",    "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSLogHandler.class.php", "IPSLibrary::install::IPSModuleManager::IPSLogHandler");

   /**
    * @class IPSScriptHandler
    *
    * Script Loader der IPSLibrary 
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	class IPSScriptHandler{

		private $libraryBasePath="";
		private $logHandler=null;

		/**
		 * @public
		 *
		 * Initialisierung des ScriptHandlers
		 *
		 * @param string $libraryBasePath Basis Pfad der IPSLibrary (Pfad in IP-Symcon, Kategorien durch . getrennt)
		 */
		public function __construct($libraryBasePath) {
			$this->libraryBasePath = $libraryBasePath;
			$this->logHandler      = IPSLogHandler::GetLogger(get_class($this));
		}

		/**
		 * @public
		 *
		 * Liefert den Pfad eines übergebenen Scriptes in der IPS Structure
		 *
		 * @param string $file Name des Script Files
		 */
		public function GetScriptPathByFileName($file) {
			$scriptPath = pathinfo($file, PATHINFO_DIRNAME);
			$scriptPath = str_replace(IPS_GetKernelDir().'scripts\\', '', $scriptPath);
			$scriptPath = str_replace('\\', '.', $scriptPath);
			$scriptPath = str_replace('/', '.', $scriptPath);
			if ($this->libraryBasePath<>'') {
				$scriptPath = $this->libraryBasePath.'.'.$scriptPath;
			}
			return $scriptPath;
		}

		/**
		 * @public
		 *
		 * Liefert den Namen eines übergebenen Scriptes in der IPS Structure
		 *
		 * @param string $file Name des Script Files
		 */
		public function GetScriptNameByFileName($file) {
			$scriptName = pathinfo($file, PATHINFO_BASENAME);
			$scriptName = str_replace('.class', '', $scriptName);
			$scriptName = str_replace('.ips', '', $scriptName);
			$scriptName = str_replace('.inc', '', $scriptName);
			$scriptName = str_replace('.php', '', $scriptName);
			return $scriptName;
		}

		private function GetScriptExtensionByFileName($script) {
			$scriptExt = pathinfo($script, PATHINFO_EXTENSION);
			return $scriptExt;
		}
		

		/**
		 * @public
		 *
		 * Die Funktion registriert ein ScriptFile anhand des Filenames und Directory Pfades in IPS
		 *
		 * @param string $scriptList Liste von Scripts, die registriert werden soll
		 */
		public function UnregisterScriptByFilename($file) {
			$scriptPath = $this->GetScriptPathByFileName($file);
			$scriptName = $this->GetScriptNameByFileName($file);
			$this->logHandler->Debug("Search Script $scriptPath.$scriptName");
			$pathId = IPSUtil_ObjectIDByPath($scriptPath, true);
			$scriptId = @IPS_GetObjectIDByIdent(Get_IdentByName($scriptName), $pathId);
			if ($scriptId!==false) {
				$this->logHandler->Debug("Unegister Script $scriptName in $scriptPath (File=$file)");
			   IPS_DeleteScript($scriptId, true);
			}
		}

		/**
		 * @public
		 *
		 * Die Funktion registriert ein ScriptFile anhand des Filenames und Directory Pfades in IPS
		 *
		 * @param string $scriptList Liste von Scripts, die registriert werden soll
		 */
		public function RegisterScriptByFilename($file) {
			$scriptPath = $this->GetScriptPathByFileName($file);
			$scriptName = $this->GetScriptNameByFileName($file);
			if (strpos($file, IPS_GetKernelDir().'scripts\\')===0) {
				$this->logHandler->Debug("Register Script $scriptName in $scriptPath (File=$file)");
				$categoryId = CreateCategoryPath($scriptPath);
				CreateScript($scriptName, $file, $categoryId);
			} else {
				$this->logHandler->Debug("Script $scriptName NOT registered (Filepath)");
			}
		}

		/**
		 * @public
		 *
		 * Die Funktion registriert eine Liste von Files anhand ihres Filenames und Directory Pfades in IPS
		 *
		 * @param string $scriptList Liste von Scripts, die registriert werden soll
		 */
		public function RegisterScriptListByFilename($scriptList) {
			foreach ($scriptList as $idx=>$script) {
			   $this->RegisterScriptByFilename($script);
			}
		}

		/**
		 * @public
		 *
		 * Die Funktion registriert eine Liste von User Files anhand ihres Degfault Filenames und Directory Pfades in IPS
		 *
		 * @param string $defaultScriptList Liste von Scripts, die registriert werden soll
		 */
		public function RegisterUserScriptsListByDefaultFilename($defaultScriptList) {
			$scriptList = array();
			foreach ($defaultScriptList as $idx=>$defaultScript) {
				$scriptList[] = IPSFileHandler::GetUserFilenameByDefaultFilename($defaultScript);
			}
			$this->RegisterScriptListByFilename($scriptList);
		}
	}

	/** @}*/
?>