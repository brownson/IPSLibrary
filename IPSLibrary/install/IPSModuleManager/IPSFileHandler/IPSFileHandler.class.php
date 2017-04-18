<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
 	 *
	 * @file          IPSFileHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ("IPSLogHandler.class.php", "IPSLibrary::install::IPSModuleManager::IPSLogHandler");

   /**
    * @class IPSFileHandlerException
    *
    * Definiert eine FileHandler Exception
    *
    */
   class IPSFileHandlerException extends Exception {
   }

   /**
    * @class IPSFileHandler
    *
    * FileHandler der IPSLibrary 
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	class IPSFileHandler {

		private $logHandler=null;

		/**
		 * @public
		 *
		 * Initialisierung des FileHandlers
		 *
		 */
		public function __construct() {
			$this->logHandler = IPSLogHandler::GetLogger(get_class($this));
		}

		/**
		 * @public
		 *
		 * Liefert den übergebenen Pfadnamen mit einem abschließenden Pfad Delimiter
		 *
		 * @param string $path Pfad
		 */
		public static function AddTrailingPathDelimiter($path) {
			if (substr($path, -1) <> '\\' and substr($path, -1) <> '/') {
			   $path = $path.DIRECTORY_SEPARATOR;
			}
			return $path;
		}

		/**
		 * @public
		 *
		 * Liefert den "User" Filenamen anhand des Default Filenames
		 *
		 * @param string $defaultFile Default Dateiname / Pfad 
		 */
		public static function GetUserFilenameByDefaultFilename($defaultFile) {
			$fileName = pathinfo($defaultFile, PATHINFO_BASENAME);
			$filePathDefault = pathinfo($defaultFile, PATHINFO_DIRNAME);
				
			$filePath = pathinfo($filePathDefault, PATHINFO_DIRNAME);
			$file = $filePath.'/'.$fileName;
				
			return $file;
		}

		/**
		 * @public
		 *
		 * Filtert Dateien die gleich sind (CRLF wird ignoriert) aus den übergebenen
		 * File Listen heraus.
		 *
		 * @param string $sourceList Liste der Files, die kopiert werden soll
		 * @param string $destinationList Liste der Files, die erzeugt werden soll
		 */
		public function FilterEqualFiles(&$sourceList, &$destinationList) {
			$sourceOut      = array();
			$destinationOut = array();
			foreach ($sourceList as $idx=>$sourceScript) {
				$sourceFile          = $sourceList[$idx];
				$destinationFile     = $destinationList[$idx];
				
				$addFileToList = true;
				if (!file_exists($destinationFile)) {
					$addFileToList = true;
				} else {
					$sourceContent      = file_get_contents($sourceFile);
					$destinationContent = file_get_contents($destinationFile);
					$sourceContent      = str_replace(chr(13), '',$sourceContent);
					$destinationContent = str_replace(chr(13), '',$destinationContent);
					if ($sourceContent == $destinationContent) {
						$addFileToList = false;
					}
				}
				if ($addFileToList) {
					$sourceOut[]      = $sourceFile;
					$destinationOut[] = $destinationFile;
				};
			}
			$sourceList      = $sourceOut;
			$destinationList = $destinationOut;
		}


		/**
		 * @public
		 *
		 * Erzeugt Files aus den zugrundeliegenden Default Files 
		 *
		 * @param string $sourceFile Datei die kopiert werden soll
		 * @param string $destinationFile Datei die erzeugt werden soll
		 * @param boolean $raiseError gibt an ob ein Error geraised werden soll oder ob die Function im Falle eines Fehlers false retounieren soll
		 * @return boolean true für OK, false bei Fehler beim Kopiervorgang
		 * @throws IPSFileHandlerException wenn Fehler beim Erzeugen der Zieldatei auftritt
		 */
		public function CopyFile($sourceFile, $destinationFile, $raiseError=true) {
			$destinationFilePath = pathinfo($destinationFile, PATHINFO_DIRNAME);
			if (!file_exists($destinationFilePath)) {
				$this->logHandler->Log("Create Directory $destinationFilePath");
				if (!mkdir($destinationFilePath, 0755, true)) {
					throw new IPSFileHandlerException('Create Directory '.$destinationFilePath.' failed !',
													E_USER_ERROR);
				}
			}
			
			$destinationFile = str_replace('\\','/', $destinationFile);
			$destinationFile = str_replace('//','/', $destinationFile);
			if (strpos($sourceFile, 'https')===0) {
				$sourceFile = str_replace('\\','/', $sourceFile);
				$sourceFile = str_replace('//','/', $sourceFile);
				$sourceFile = str_replace('https:/','https://', $sourceFile);
				$this->logHandler->Log("Copy $sourceFile ---> $destinationFile");

				$curl_handle=curl_init();
				global $_IPS;
				if (array_key_exists('PROXY', $_IPS)) {
					$proxy = $_IPS['PROXY'];
					if ( $proxy != '' ) {
						curl_setopt($curl_handle, CURLOPT_HTTPPROXYTUNNEL, 1);
						curl_setopt($curl_handle, CURLOPT_PROXY, $proxy);
					}
				}
				curl_setopt($curl_handle, CURLOPT_URL,$sourceFile);
				curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,true);
				curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($curl_handle, CURLOPT_FAILONERROR, true);
				$fileContent = curl_exec($curl_handle);
				//$fileContent = html_entity_decode($fileContent, ENT_COMPAT, 'ISO-8859-1');
				if ($fileContent===false) {
					$this->logHandler->Log("Download Destination File $sourceFile failed --> Retry ...");
					$fileContent = curl_exec($curl_handle);
				}
				if ($fileContent===false) {
					$this->logHandler->Log("Download Destination File $sourceFile failed --> Retry ...");
					$fileContent = curl_exec($curl_handle);
				}
				if ($fileContent===false) {
					if ($raiseError) {
						throw new IPSFileHandlerException('File '.$destinationFile.' could NOT be found on the Server !!!',
						                                  E_USER_ERROR);
					} else {
						return false;
					}
				}
				curl_close($curl_handle);

				$result = file_put_contents($destinationFile, $fileContent);
				if ($result===false) {
					$this->logHandler->Log("Write Destination File $destinationFile failed --> Retry ...");
					sleep(1);
					$result = file_put_contents($destinationFile, $fileContent);
					if ($result===false and $raiseError) {
						throw new IPSFileHandlerException('Error writing File Content to '.$destinationFile,
														E_USER_ERROR);
					}
				}
			} else {
				$this->logHandler->Log("Copy $sourceFile --> $destinationFile");
				$result = @copy ($sourceFile, $destinationFile);
				//ToDo - Check Errorhandling ...
				if ($result===false and $raiseError) {
					throw new IPSFileHandlerException('Error while copy File '.$sourceFile.' to '.$destinationFile,
					                                  E_USER_ERROR);
				}
			}
			return $result;
		}

		/**
		 * @public
		 *
		 * Erzeugt Files aus den zugrundeliegenden Default Files 
		 *
		 * @param string $scriptList Liste der Default Files, von denen eine User Version erzeugt werden soll
		 * @param boolean $overwriteUserFiles bestehende User Files mit Default überschreiben
		 * @throws IPSFileHandlerException wenn Fehler beim Erzeugen der Zieldatei auftritt
		 */
		public function CreateScriptsFromDefault($scriptList, $overwriteUserFiles=false) {
			foreach ($scriptList as $idx=>$scriptDefault) {
				$script = self::GetUserFilenameByDefaultFilename($scriptDefault);
				if (!file_exists($script) or $overwriteUserFiles) {
					$this->logHandler->Log("Create User File $script from Default File $scriptDefault");
					$this->CopyFile ($scriptDefault, $script);
				}
			}
		}
		
		/**
		 * @public
		 *
		 * Kopieren von Files
		 *
		 * @param string $sourceList Liste der Files, die kopiert werden soll
		 * @param string $destinationList Liste der Files, die erzeugt werden soll
		 * @param boolean $raiseError gibt an ob ein Error geraised werden soll oder ob die Function im Falle eines Fehlers false retounieren soll
		 * @return boolean true für OK, false bei Fehler beim Kopiervorgang
		 * @throws IPSFileHandlerException wenn Fehler beim Erzeugen der Zieldatei auftritt
		 */
		public function CopyFiles ($sourceList, $destinationList, $raiseError=true) {
			foreach ($sourceList as $idx=>$sourceScript) {
				$destinationScript     = $destinationList[$idx];
				$result = $this->CopyFile($sourceScript, $destinationScript, $raiseError);
				if ($result===false) {
					return false;
				}
			}
			return true;
		}
	
		/**
		 * @public
		 *
		 * Laden von Files aus dem Repository 
		 *
		 * @param string $repositoryList Liste der Files, die geladen werden soll
		 * @param string $localList Liste der Files, die erzeugt werden soll
		 * @throws IPSFileHandlerException wenn Fehler beim Erzeugen der Zieldatei auftritt
		 */
		public function LoadFiles($repositoryList, $localList) {
			$this->CopyFiles($repositoryList, $localList);
		}

		/**
		 * @public
		 *
		 * Schreiben von Files ins Repository 
		 *
		 * @param string $localList Liste der Files, die geschrieben werden soll
		 * @param string $repositoryList Liste der Files, die erzeugt werden soll
		 * @throws IPSFileHandlerException wenn Fehler beim Erzeugen der Zieldatei auftritt
		 */
		public function WriteFiles($localList, $repositoryList) {
			$this->CopyFiles($localList, $repositoryList);
		}
		
		/**
		 * @public
		 *
		 * Die Funktion kopiert ein Example File auf ein Konfigurationsfile
		 *
		 * @param string $exampleFile Name der Beispiel Datei
		 * @param string $configFile Name der Ziel Datei (Konfigurations File Name)
		 * @param string $namespace Namespace wo das ExampleFile zu finden ist (ohne Angabe des "Example" Verzeichnisses)
		 * @throws IPSFileHandlerException wenn Fehler beim erzeugen der Zieldatei auftritt
		 */
		public function CreateFileFromExample($exampleFile, $configFile, $namespace='') {
		   if ($namespace <> '') {
		      $exampleFile = IPS_GetKernelDir().'scripts/'.str_replace('::','/',$namespace).'/Examples/'.$exampleFile;
		      $configFile  = IPS_GetKernelDir().'scripts/'.str_replace('::','/',$namespace).'/'.$configFile;
			}

			$this->CopyFile($exampleFile, $configFile);
		}

		/**
		 * @public
		 *
		 * Löschen eines Files
		 *
		 * @param string $file File das gelöscht werden soll
		 * @throws IPSFileHandlerException wenn Fehler beim Löschen der Zieldatei auftritt
		 */
		public function DeleteFile($file) {
		   if (file_exists($file)) {
				$this->logHandler->Log('Delete File '.$file);
				if (!unlink($file)) {
					throw new IPSFileHandlerException('Error while deleting File '.$file, E_USER_ERROR);
				}
			}
		}

		/**
		 * @public
		 *
		 * Löschen von Files
		 *
		 * @param string $localList Liste der Files, die gelöscht werden soll
		 * @throws IPSFileHandlerException wenn Fehler beim Löschen der Zieldatei auftritt
		 */
		public function DeleteFiles($localList) {
			foreach ($localList as $idx=>$file) {
			  $this->DeleteFile($file);
			}
		}

		/**
		 * @public
		 *
		 * Löschen eines Files
		 *
		 * @param string $file File das gelöscht werden soll
		 * @throws IPSFileHandlerException wenn Fehler beim Löschen der Zieldatei auftritt
		 */
		public function DeleteEmptyDirectories($file) {
			$filePath = pathinfo($file, PATHINFO_DIRNAME);
			if (!is_dir($filePath)) {
			   return;
			}

	      $fileList = scandir($filePath);
      	$fileList = array_diff($fileList, Array(".",".."));
			if (count($fileList)==0) {
				$this->logHandler->Log('Delete Directory '.$filePath);
			   if (!rmdir($filePath)) {
					throw new IPSFileHandlerException('Error while deleting Directory '.$filePath, E_USER_ERROR);
				}
			   $parentDirectory = pathinfo($filePath, PATHINFO_DIRNAME);
			   $this->DeleteEmptyDirectories($parentDirectory);
			}
		}
	}

	/** @}*/
?>