<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
 	 *
	 * @file          IPSBackupHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ("IPSLogHandler.class.php",     "IPSLibrary::install::IPSModuleManager::IPSLogHandler");
	IPSUtils_Include ("IPSConfigHandler.class.php",  "IPSLibrary::app::core::IPSConfigHandler");

   /**
    * @class IPSBackupHandler
    *
    * BackupHandler der IPSLibrary 
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	class IPSBackupHandler {

		private $backupBaseDirectory = '';
		private $backupDirectory     = '';
		private $logHandler          = null;

		
		/**
		 * @public
		 *
		 * Initialisierung des BackupHandlers
		 *
		 * @param string $backupBaseDirectory Backup Directory
		 */
		public function __construct($backupBaseDirectory) {
			if ($backupBaseDirectory=='') {
				throw new IPSConfigurationException('Backup Directory cannot be empty!',
												E_USER_ERROR);
			}
			$this->backupBaseDirectory = $backupBaseDirectory;
			$this->backupDirectory     = $backupBaseDirectory.'IPSLibrary_'.date("Y-m-d_Hi").'\\';
			$this->logHandler          = IPSLogHandler::GetLogger(get_class($this));
		}
	
		/**
		 * @public
		 *
		 * Liefert das aktuelle Backup Verzeichnis
		 */
		public function GetBackupDirectory() {
			return $this->backupDirectory;
		}
		
		/**
		 * @public
		 *
		 * Backup einer Datei erzeugen
		 *
		 * @param string $sourceFile Datei die gesichert werden soll
		 * @param string $backupFile Ziel Datei
		 */
		public function CreateBackupFromFile($sourceFile, $backupFile) {
			$fileHandler = new IPSFileHandler();
			if (file_exists($sourceFile)) {
				$fileHandler->CopyFile($sourceFile, $backupFile);
			} else {
				$this->logHandler->Debug('Backup NOT possible - Source File '.$sourceFile.' doesnt exists');
			}
   	}

		/**
		 * @public
		 *
		 * Backup von Dateien erzeugen
		 *
		 * @param string $sourceList Liste der Dateien die gesichert werden soll
		 * @param string $backupList Liste der Ziel Dateien
		 */
		public function CreateBackup($sourceList, $backupList) {
			$fileHandler = new IPSFileHandler();
			foreach ($sourceList as $idx=>$sourceFile) {
				$backupFile = $backupList[$idx];
				$this->CreateBackupFromFile($sourceFile, $backupFile);
			}
		}
	}

	/** @}*/
?>