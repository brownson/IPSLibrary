<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
	 * @file          IPSLogHandler.class.php
	 * @author        Andreas Brauneis
	 *
	 */

	IPSUtils_Include ("IPSLogHandler.class.php", "IPSLibrary::install::IPSModuleManager::IPSLogHandler");

	/**
	 * @class IPSLogHandler
	 *
	 * LogHandler des IPSModule Managers
	 *
	 * @author Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 */
	class IPSLogHandler{

		private static $logDirectory="";
		private static $logFile="";
		private static $debugMode=false;
		private $logContext="";

		/**
		 * @public
		 *
		 * Initialisierung des LogHandlers
		 *
		 * @param string $logContext Aktueller Logging Context (File, Class, Identifier...)
		 * @param string $logDirectory Verzeichnis für Log File
		 * @param string $logFile Name des Log Files
		 * @param string $debugMode Debug Mode, Switch für Ausgae von Debug Meldungen
		 */
		public function __construct($logContext, $logDirectory ,$logFile='' , $debugMode=true) {
			if ($logFile=='') {
				$logFile = 'IPSModuleManager_'.date("Y-m-d_Hi").'.log';
			}
			self::$logDirectory = $logDirectory;
			self::$logFile      = $logFile;
			self::$debugMode    = $debugMode;
			$this->logContext   = $logContext;
		}


		/**
		 * @public
		 *
		 * Liefert Namen des aktuellen LogFiles
		 *
		 * @return string Name des LogFiles
		 */
		public function GetLogFileName() {
			return self::$logDirectory.self::$logFile;
		}

		/**
		 * @public
		 *
		 * Liefert eine Instance des LogHandlers, Logger muß in der aktuellen Session bereits initialisiert 
		 * worden sein.
		 *
		 * @param string $logContext Aktueller Logging Context (File, Class, Identifier...)
		 */
		public static function GetLogger($logContext) {
			if (self::$logDirectory=='' or self::$logFile=='') {
			   die('LogFile NOT assigned !!!');
			}
			return new IPSLogHandler($logContext, IPSLogHandler::$logDirectory, IPSLogHandler::$logFile, IPSLogHandler::$debugMode);
		}

		private function WriteFile($msg) {
			$out  = 'IPSModuleManager-Log-';
			$out .= substr(str_pad($this->logContext,30,' '),0,30);
			$out .= date('Y-m-d H:i:s').substr(microtime(),1,3).'  '.$msg;

			$file = self::$logDirectory.self::$logFile;
			if(($fileHandle = fopen($file, "a")) === false) {
				die('File "" could NOT be opened!');
			}
			fwrite($fileHandle, $out.PHP_EOL);
			fclose($fileHandle);
		}

		private function WritePHPConsole($msg) {
			$out  = 'IPSModuleManager-Log-';
			$out .= substr(str_pad($this->logContext,20,' '),0,20);
			$out .= date('Y-m-d H:i:s').substr(microtime(),1,3).'  '.$msg;
			echo $out."\n";
		}
		
		private function WriteIPSConsole($msg) {
			IPS_LogMessage($this->logContext, $msg);
		}
		
		/**
		 * @public
		 *
		 * Protokollierung von Messages
		 *
		 * @param string $msg Logging Message
		 */
		public function Log($msg) {
			$this->WritePHPConsole($msg);
			$this->WriteIPSConsole($msg);
			$this->WriteFile($msg);
		}

		/**
		 * @public
		 *
		 * Protokollierung von Debug Messages
		 *
		 * @param string $msg Debug Message
		 */
		public function Debug($msg) {
			if (self::$debugMode) {
				$this->WritePHPConsole($msg);
				$this->WriteIPSConsole($msg);
				$this->WriteFile($msg);
			}
		}

		/**
		 * @public
		 *
		 * Protokollierung von Error Messages
		 *
		 * @param string $msg Error Message
		 */
		public function Error($msg) {
			$debugTrace = debug_backtrace();
			$stackTxt   = '';
			foreach ($debugTrace as $idx=>$stack) {
				if (array_key_exists('line', $stack) and array_key_exists('function', $stack) and array_key_exists('file', $stack)) {
					$file     = str_replace('scripts\\', '', str_replace(IPS_GetKernelDir(), '', $stack['file']));
					$function = $stack['function'];
					$line     = str_pad($stack['line'],3,' ', STR_PAD_LEFT);
					$stackTxt  .= PHP_EOL."  $line in $file (call $function)";
				} elseif (array_key_exists('function', $stack)) {
					$stackTxt  .= PHP_EOL.'      in '.$stack['function'];
				} else {
					$stackTxt  .= PHP_EOL.'      Unknown Stack ...';
				}
			}
			$this->WritePHPConsole($msg.$stackTxt);
			$this->WriteIPSConsole($msg.$stackTxt);
			$this->WriteFile($msg.$stackTxt);
		}

	}

	/** @}*/
?>