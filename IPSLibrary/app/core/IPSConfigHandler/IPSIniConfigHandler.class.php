<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
	 * @file          IPSConfigHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	include_once "IPSConfigHandler.class.php";

   /**
    * @class IPSIniConfigHandler
    *
    * Implementierung eines IPSConfigHandlers für INI Files.
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	class IPSIniConfigHandler extends IPSConfigHandler {

		private $iniFileName="";

		/**
		 * @public
		 *
		 * Initialisierung INI File ConigurationHandlers
		 *
		 * @param string $iniFileName Name der INI Datei.
		 * @param string $namespace Namespace des INI Files
		 */
		public function __construct($iniFileName, $namespace="") {
			$this->iniFileName = $this->GetFileName($iniFileName, $namespace);
			$this->LoadFile($this->iniFileName);
		}

		/**
		 * @public
		 *
		 * Initialisierung INI File ConfigHandlers
		 *
		 * @param string $iniFileName Name der INI Datei.
		 * @param string $namespace Namespace des INI Files
		 */
		private function LoadFile () {
			if (!file_exists($this->iniFileName)) {
				throw new Exception('script '.$this->iniFileName.' could NOT be found!', E_USER_ERROR);
			}
			$this->configData = parse_ini_file($this->iniFileName, true);
		}

		private function GetFileName ($iniFileName, $namespace="") {
			if ($namespace=="") {
				$result = $iniFileName;
			} else {
				$result = IPS_GetKernelDir().'scripts/'.str_replace('::','/',$namespace).'/'.$iniFileName;;
			}
			$result = str_replace('\\','/',$result);
			return $result;
		}
	}

	/** @}*/
?>