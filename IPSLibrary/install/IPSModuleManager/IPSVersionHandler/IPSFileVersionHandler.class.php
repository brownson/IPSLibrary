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

		const FILE_INSTALLED_MODULES       = 'IPSLibrary/config/InstalledModules.ini';
		const FILE_AVAILABLE_MODULES       = 'IPSLibrary/config/AvailableModules.ini';
		const FILE_KNOWN_MODULES           = 'IPSLibrary/config/KnownModules.ini';
		const FILE_KNOWN_REPOSITORIES      = 'IPSLibrary/config/KnownRepositories.ini';
		const FILE_KNOWN_USERREPOSITORIES  = 'IPSLibrary/config/KnownUserRepositories.ini';
		const FILE_REPOSITORY_VERSIONS     = 'IPSLibrary/config/RepositoryVersions.ini';
		const FILE_CHANGELIST              = 'IPSLibrary/config/ChangeList.ini';
		const FILE_REQUIRED_MODULES        = 'IPSLibrary/config/RequiredModules.ini';
 		const FILE_DOWNLOADLIST_PATH       = "IPSLibrary/install/DownloadListFiles/";
		const FILE_DOWNLOADLIST_SUFFIX     = '_FileList.ini';

		private $fileNameAvailableModules;
		private $fileNameInstalledModules;
		private $fileNameKnownModules;
		private $fileNameKnownRepositories;
		private $fileNameRepositoryVersions;
		private $fileNameChangeList;
		private $fileNameRequiredModules;
		private $fileNameDownloadList;

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
			$this->fileNameInstalledModules      = IPS_GetKernelDir().'scripts/'.$this::FILE_INSTALLED_MODULES;
			$this->fileNameAvailableModules      = IPS_GetKernelDir().'scripts/'.$this::FILE_AVAILABLE_MODULES;
			$this->fileNameKnownModules          = IPS_GetKernelDir().'scripts/'.$this::FILE_KNOWN_MODULES;
			$this->fileNameKnownRepositories     = IPS_GetKernelDir().'scripts/'.$this::FILE_KNOWN_REPOSITORIES;
			$this->fileNameKnownUserRepositories = IPS_GetKernelDir().'scripts/'.$this::FILE_KNOWN_USERREPOSITORIES;
			$this->fileNameRepositoryVersions    = IPS_GetKernelDir().'scripts/'.$this::FILE_REPOSITORY_VERSIONS;
			$this->fileNameChangeList            = IPS_GetKernelDir().'scripts/'.$this::FILE_CHANGELIST;
			$this->fileNameRequiredModules       = IPS_GetKernelDir().'scripts/'.$this::FILE_REQUIRED_MODULES;
			$this->fileNameDownloadList          = IPS_GetKernelDir().'scripts/'.$this::FILE_DOWNLOADLIST_PATH.$moduleName.$this::FILE_DOWNLOADLIST_SUFFIX;

			$this->ReloadVersionData();
		}

		public function ReloadVersionData() {
			$this->LoadFileInstalledModules();
			$this->LoadFileKnownRepositories();
			$this->LoadFileKnownModules();
			$this->LoadFileRepositoryVersions();
			$this->LoadFileChangeList();
			$this->LoadFileRequiredModules();

			if (!array_key_exists($this->moduleName, $this->installedModules)) {
				$this->installedModules[$this->moduleName] = '';
			}
		}
		
		private function LoadFileRequiredModules() {
			if (file_exists($this->fileNameRequiredModules)) {
				$this->requiredModules = parse_ini_file($this->fileNameRequiredModules, true);
			}
		}

		private function LoadFileChangeList() {
			if (file_exists($this->fileNameChangeList)) {
				$this->changeList = parse_ini_file($this->fileNameChangeList, true);
			}
		}

		private function LoadFileKnownModules() {
			if (file_exists($this->fileNameKnownModules)) {
				$this->knownModules = parse_ini_file($this->fileNameKnownModules, true);
			}
		}

		private function LoadFileKnownRepositories() {
			if (!file_exists($this->fileNameKnownRepositories)) {
				die($this->fileNameKnownRepositories.' does NOT exist!');
			} elseif (file_exists($this->fileNameKnownUserRepositories)) {
				$this->knownRepositories = parse_ini_file($this->fileNameKnownUserRepositories, true);
			} else {
				$this->knownRepositories = parse_ini_file($this->fileNameKnownRepositories, true);
			}
		}

		private function LoadFileRepositoryVersions() {
			if (file_exists($this->fileNameRepositoryVersions)) {
				$this->repositoryVersions = parse_ini_file($this->fileNameRepositoryVersions, true);
			}
		}

		private function LoadFileInstalledModules() {
			if (file_exists($this->fileNameInstalledModules)) {
				$fileContent = file_get_contents($this->fileNameInstalledModules);
				$lines = explode(PHP_EOL, $fileContent);
				foreach ($lines as $line) {
					$content = explode('=', $line);
					if (count($content)>0) {
						$this->installedModules[$content[0]] = $content[1];
					}
				}
			} else {
				$this->installedModules = array();
			}
		}

		private function WriteFileInstalledModules() {
			$fileContent = '';
			foreach ($this->installedModules as $moduleName=>$moduleVersion) {
				if ($fileContent <> '') {
					$fileContent .= PHP_EOL;
				}
				$fileContent .= $moduleName.'='.$moduleVersion;
			}
			file_put_contents($this->fileNameInstalledModules, $fileContent);
		}

		/**
		 * @public
		 *
		 * Speicherung der Versions Daten
		 *
		 * @param string $moduleName Name des Modules
		 */
		protected function StoreModuleVersions() {
			$this->WriteFileInstalledModules();
		}

		/**
		 * @public
		 *
		 * Erzeugt das File KnownModules
		 */
		public function BuildKnownModules() {
			$knownRepositories    = $this->GetKnownRepositories();
			$knownModules         = array();
			$repositoryVersions   = array();
			$changeList           = array();
			$requiredModules      = array();
			foreach ($knownRepositories as $repositoryIdx=>$repository) {
				echo 'Process Repsoitory '.$repository.PHP_EOL;
				$fileHandler         = new IPSFileHandler();
				$repository = IPSFileHandler::AddTrailingPathDelimiter($repository);
				$localAvailableModuleList      = sys_get_temp_dir().'/AvailableModules.ini';
				$repositoryAvailableModuleList = $repository.'IPSLibrary/config/AvailableModules.ini';
				$fileHandler->CopyFiles(array($repositoryAvailableModuleList), array($localAvailableModuleList));

				$availableModules = parse_ini_file($localAvailableModuleList, true);
				foreach ($availableModules as $moduleName=>$moduleData) {
					$moduleProperties  = explode('|',$moduleData);
					$modulePath        = $moduleProperties[0];
					$moduleDescription = '';
					if (array_key_exists(1, $moduleProperties)) {
						$moduleDescription = $moduleProperties[1];
					}
					
					$localDownloadIniFile      = sys_get_temp_dir().'/DownloadListfile.ini';
					$repositoryDownloadIniFile = $repository.'IPSLibrary/install/DownloadListFiles/'.$moduleName.'_FileList.ini';
					$result = $fileHandler->CopyFiles(array($repositoryDownloadIniFile), array($localDownloadIniFile), false);
					if ($result===false) {
						echo '   '.$moduleName.'could NOT be found in '.$repository.PHP_EOL;
					} else {
						echo '   Processing '.$moduleName.' in '.$repository.PHP_EOL;
						$configHandler    = new IPSIniConfigHandler($localDownloadIniFile);
						$availableVersion = $configHandler->GetValue(IPSConfigHandler::SCRIPTVERSION);
						$changeListModule = $configHandler->GetValueDef(IPSConfigHandler::CHANGELIST, null, array());
						$requiredModulesOfModule = $configHandler->GetValueDef(IPSConfigHandler::REQUIREDMODULES, null, array());

						$replaceModule = false;
						if (!array_key_exists($moduleName, $knownModules)) {
							$replaceModule = true;
						} elseif ($versionHandler->CompareVersionsNewer($knownModules[$moduleName]['Version'], $availableVersion)) {
							$replaceModule = true;
						} elseif ($versionHandler->CompareVersionsEqual($knownModules[$moduleName]['Version'], $availableVersion)
								  and $versionHandler->IsModuleInstalled($moduleName)) {
							$versionHandler   = new IPSFileVersionHandler($moduleName);
							if ($versionHandler->GetModuleRepository()==$repository) {
								$replaceModule = true;
							}
						} else {
						}

						if ($replaceModule) {
							$knownModules[$moduleName]['Version']     = $availableVersion;
							$knownModules[$moduleName]['Repository']  = $repository;
							$knownModules[$moduleName]['Description'] = $moduleDescription;
							$knownModules[$moduleName]['Path']        = $modulePath;
							if ($this->IsModuleInstalled($moduleName)) {
								$versionHandler   = new IPSFileVersionHandler($moduleName);
							}
							$knownModules[$moduleName]['LastRepository'] = $versionHandler->GetModuleRepository();
							$changeList[$moduleName] = $changeListModule;
							$requiredModules[$moduleName] = $requiredModulesOfModule;
						}
						$repositoryVersions[$moduleName][$repository] = $availableVersion;
					}
				}

			}

			$fileContent = '';
			foreach ($knownModules as $moduleName=>$moduleData) {
				$fileContent .= '['.$moduleName.']'.PHP_EOL;
				foreach ($moduleData as $property=>$value) {
					// "//192.168..." not handled correct in case of usage ""
					if ($property=='Repository') {
						$fileContent .= $property.'='.$value.''.PHP_EOL;
					} else {
						$fileContent .= $property.'="'.$value.'"'.PHP_EOL;
					}
				}
			}
			file_put_contents($this->fileNameKnownModules, $fileContent);

			$fileContent = '';
			foreach ($repositoryVersions as $moduleName=>$moduleData) {
				$fileContent .= '['.$moduleName.']'.PHP_EOL;
				foreach ($moduleData as $property=>$value) {
					$fileContent .= $property.'="'.$value.'"'.PHP_EOL;
				}
			}
			file_put_contents($this->fileNameRepositoryVersions, $fileContent);

			$fileContent = '';
			foreach ($changeList as $moduleName=>$moduleData) {
				$fileContent .= '['.$moduleName.']'.PHP_EOL;
				foreach ($moduleData as $property=>$value) {
					$fileContent .= $property.'="'.$value.'"'.PHP_EOL;
				}
			}
			file_put_contents($this->fileNameChangeList, $fileContent);

			$fileContent = '';
			foreach ($requiredModules as $moduleName=>$moduleData) {
				$fileContent .= '['.$moduleName.']'.PHP_EOL;
				foreach ($moduleData as $property=>$value) {
					$fileContent .= $property.'="'.$value.'"'.PHP_EOL;
				}
			}
			file_put_contents($this->fileNameRequiredModules, $fileContent);

			$this->LoadFileKnownModules();
			$this->LoadFileRepositoryVersions();
			$this->LoadFileChangeList();
			$this->LoadFileRequiredModules();
		}
		
		/**
		 * @public
		 *
		 * Erhöht die Versionsnummer im entsprechenden Download File und legt den übergebenen Text 
		 * unter der ChangeList des Modules ab.
		 * 
		 */
		public function IncreaseModuleVersion($changeText, $installationRequired=false) {
			$file       = parse_ini_file($this->fileNameDownloadList, true);
			$version    = $file['Version'];
			$version    = $this->VersionToArray($version);
			$version[2] = (int)$version[2] + 1;
			unset($version[4]);
			if ($version[3]=='') {
				unset($version[3]);
			}
			$version = implode('.',$version);

			$file['Version'] = $version;
			if ($installationRequired) {
				$file['InstallVersion'] = $version;
			}
			$file['ChangeList'][$version] = $changeText;
			
			$fileContent = '';
			foreach ($file as $section=>$sectionValue) {
				if ($section=='Version' or $section=='InstallVersion' or $section=='ModuleNamespace') {
					$fileContent .= $section.'="'.$sectionValue.'"'.PHP_EOL;
				} else {
					$fileContent .= '['.$section.']'.PHP_EOL;
					foreach ($sectionValue as $property=>$value) {
						if ($section=='ChangeList' or $section=='RequiredModules') {
							$fileContent .= $property.'="'.$value.'"'.PHP_EOL;
						} else {
							foreach ($value as $key=>$data) {
								$fileContent .= $property.'[]="'.$data.'"'.PHP_EOL;
							}
						}
					}
				}
			}
			file_put_contents($this->fileNameDownloadList, $fileContent);
		}
	}

	/** @}*/
?>