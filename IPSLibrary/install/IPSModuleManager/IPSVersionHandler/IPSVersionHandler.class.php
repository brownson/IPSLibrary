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
		
		const MODULE_IPS         = 'IPS';
		const VERSION_LOADING    = 'Loading';
		const VERSION_LOADED     = 'Loaded';
		const VERSION_INSTALLING = 'Installing';

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
		}

		/**
		 * @public
		 *
		 * Lesen der aktuellen Module Version
		 *
		 * @return string Liefert die aktuelle Module Version
		 */
		abstract public function GetModuleVersion();

		/**
		 * @public
		 *
		 * Lesen der aktuellen Module Version
		 *
		 * @return string Liefert die aktuelle Module Version as Array zurück
		 */
		abstract public function GetModuleVersionArray();

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
		abstract public function CheckModuleVersion($moduleName, $moduleVersion);

		/**
		 * @public
		 *
		 * Überprüft ob ein Module in der spezifizierten Version installiert ist.
		 *
		 * @param string $moduleVersion Version des Modules (Format IPSMajorVersion.IPSMinorVersion.ModuleVersion.ModuleStatus.InstallationStatus)
		 * @return boolean Liefert true wenn Module installiert, andernfalls false
		 */
		abstract public function IsVersionInstalled($moduleVersion);

		/**
		 * @public
		 *
		 * Schreiben der aktuellen Module Version
		 *
		 * @param string $moduleVersion Version des Modules
		 */
		abstract public function SetModuleVersion($moduleVersion);

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Loading" setzen
		 */
		abstract public function SetModuleVersionLoading();

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Loaded" setzen
		 */
		abstract public function SetModuleVersionLoaded();

		/**
		 * @public
		 *
		 * Aktuelle Module Version auf "Installing" setzen
		 */
		abstract public function SetModuleVersionInstalling();

		/**
		 * @public
		 *
		 * Liefert eine Liste aller installierten Module
		 *
		 * @return string[] Liste der installierten Module
		 */
		abstract public function GetInstalledModules();

	}

	/** @}*/
?>