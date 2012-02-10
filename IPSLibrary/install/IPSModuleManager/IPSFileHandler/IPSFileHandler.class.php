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
		 * Liefert den "User" Filenamen anhand des Default Filenames
		 *
		 * @param string $defaultFile Default Dateiname / Pfad 
		 */
		public static function GetUserFilenameByDefaultFilename($defaultFile) {
			$fileName = pathinfo($defaultFile, PATHINFO_BASENAME);
			$filePathDefault = pathinfo($defaultFile, PATHINFO_DIRNAME);
				
			$filePath = pathinfo($filePathDefault, PATHINFO_DIRNAME);
			$file = $filePath.'\\'.$fileName;
				
			return $file;
		}

		/**
		 * @public
		 *
		 * Erzeugt Files aus den zugrundeliegenden Default Files 
		 *
		 * @param string $sourceFile Datei die kopiert werden soll
		 * @param string $destinationFile Datei die erzeugt werden soll
		 * @throws IPSFileHandlerException wenn Fehler beim erzeugen der Zieldatei auftritt
		 */
		public function CopyFile($sourceFile, $destinationFile) {

			$destinationFilePath = pathinfo($destinationFile, PATHINFO_DIRNAME);
			if (!file_exists($destinationFilePath)) {
				$this->logHandler->Log("Create Directory $destinationFilePath");
				if (!mkdir($destinationFilePath, 0, true)) {
					throw new IPSFileHandlerException('Create Directory '.$destinationFilePath.' failed !',
													E_USER_ERROR);
				}
			}


			if (strpos($sourceFile, 'https')===0) {
			   $sourceFile = str_replace('\\','/', $sourceFile);
			   $sourceFile = str_replace('//','/', $sourceFile);
			   $sourceFile = str_replace('https:/','https://', $sourceFile);
				$this->logHandler->Log("Copy $sourceFile --> $destinationFile");

				$curl_handle=curl_init();
				curl_setopt($curl_handle,CURLOPT_URL,$sourceFile);
				curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
				curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,true);
				curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
				$fileContent = curl_exec($curl_handle);
				$fileContent = html_entity_decode($fileContent, ENT_COMPAT, 'ISO-8859-1');
				if (strpos($fileContent, 'Something went wrong with that request. Please try again') > 0 and
				    strpos($fileContent, 'IPSFileHandler.class.php') === false) {
					throw new IPSFileHandlerException('File '.$destinationFile.' could NOT be found on the Server !!!',
													E_USER_ERROR);
				}
				curl_close($curl_handle);

			   $result = file_put_contents($destinationFile, $fileContent);
			   if ($result===false) {
					throw new IPSFileHandlerException('Error writing File Content to '.$destinationFile,
													E_USER_ERROR);
			   }
			} else {
				$this->logHandler->Log("Copy $sourceFile --> $destinationFile");
				if (!copy ($sourceFile, $destinationFile)) {
					throw new IPSFileHandlerException('Error while copy File '.$sourceFile.' to '.$destinationFile,
													E_USER_ERROR);
				}
			}
		}

		/**
		 * @public
		 *
		 * Erzeugt Files aus den zugrundeliegenden Default Files 
		 *
		 * @param string $scriptList Liste der Default Files, von denen eine User Version erzeugt werden soll
		 * @throws IPSFileHandlerException wenn Fehler beim erzeugen der Zieldatei auftritt
		 */
		public function CreateScriptsFromDefault($scriptList) {
			foreach ($scriptList as $idx=>$scriptDefault) {
				$script = self::GetUserFilenameByDefaultFilename($scriptDefault);
				if (!file_exists($script)) {
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
		 * @throws IPSFileHandlerException wenn Fehler beim erzeugen der Zieldatei auftritt
		 */
		public function CopyFiles ($sourceList, $destinationList) {
			foreach ($sourceList as $idx=>$sourceScript) {
				$destinationScript     = $destinationList[$idx];
				$this->CopyFile($sourceScript, $destinationScript);
			}
		}
	
		/**
		 * @public
		 *
		 * Laden von Files aus dem Repository 
		 *
		 * @param string $repositoryList Liste der Files, die geladen werden soll
		 * @param string $localList Liste der Files, die erzeugt werden soll
		 * @throws IPSFileHandlerException wenn Fehler beim erzeugen der Zieldatei auftritt
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
		 * @throws IPSFileHandlerException wenn Fehler beim erzeugen der Zieldatei auftritt
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
		      $exampleFile = IPS_GetKernelDir().'\\scripts\\'.str_replace('::','\\',$namespace).'\\examples\\'.$exampleFile;
		      $configFile  = IPS_GetKernelDir().'\\scripts\\'.str_replace('::','\\',$namespace).'\\'.$configFile;
			}
			
			$this->CopyFile($exampleFile, $configFile);
		}
	}

	/** @}*/
?>