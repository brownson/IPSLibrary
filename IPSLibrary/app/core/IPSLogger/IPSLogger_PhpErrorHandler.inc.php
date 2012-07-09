<?
	/**@addtogroup ipslogger
	 * @{
	 *
	 * @file          IPSLogger_PhpErrorHandler.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Script für PHP ErrorHandler
	 *
	 * Dieses Script dient zur Anbindung des PHP ErrorHandlers. Registriert wird das Script in PHP
	 * durch folgenden Eintrag in der Datei "php.ini" im Root Verzeichnis von IPS:
	 *
	 * <pre>auto_prepend_file="<<ReplacePathToIPSymcon>>\scripts\IPSLogger_PhpErrorHandler.ips.php"</pre>
	 *
	 * Mit der IPS Version 2.5 ist dieses File über die Datei "__autoload.php" zu registrieren, die
	 * bei jedem Script Aufruf automatisch geladen wird.
	 *
	 *	<pre>include_once "\IPSLibrary\app\core\IPSLogger\IPSLogger_PhpErrorHandler.ips.php";</pre>
	 *
	 */

	function IPSLogger_PhpErrorHandler ($ErrType, $ErrMsg, $FileName, $LineNum, $Vars)
	{
		if (error_reporting() == 0) {return false;}   // No Reporting of suppressed Erros (suppressed @)

		require_once "IPSLogger.inc.php";

		$ErrorDetails = c_lf."   Error in Script ".$FileName." on Line ".$LineNum;
		$FatalError   = false;
    	switch ($ErrType) {
			case E_ERROR:
				IPSLogger_Err("PHP", 'Error: '.$ErrMsg.$ErrorDetails);
				$FatalError = true;
				break;
			case E_WARNING:
				IPSLogger_Err("PHP", 'Warning: '.$ErrMsg.$ErrorDetails);
				break;
			case E_PARSE:
				IPSLogger_Err("PHP", 'Parsing Error: '.$ErrMsg.$ErrorDetails);
				$FatalError = true;
				break;
			case E_NOTICE:
				IPSLogger_Err("PHP", 'Notice: '.$ErrMsg.$ErrorDetails);
				break;
			case E_CORE_ERROR:
				IPSLogger_Err("PHP", 'Core Error: '.$ErrMsg.$ErrorDetails);
				$FatalError = true;
				break;
			case E_CORE_WARNING:
				IPSLogger_Err("PHP", 'Core Warning: '.$ErrMsg.$ErrorDetails);
				break;
			case E_COMPILE_ERROR:
				IPSLogger_Err("PHP", 'Compile Error: '.$ErrMsg.$ErrorDetails);
				$FatalError = true;
				break;
			case E_COMPILE_WARNING:
				IPSLogger_Err("PHP", 'Compile Warning: '.$ErrMsg.$ErrorDetails);
				break;
			case E_USER_ERROR:
				IPSLogger_Err("PHP", 'User Error: '.$ErrMsg.$ErrorDetails);
				break;
			case E_USER_WARNING:
				IPSLogger_Err("PHP", 'User Warning: '.$ErrMsg.$ErrorDetails);
				break;
			case E_USER_NOTICE:
				IPSLogger_Err("PHP", 'User Notice: '.$ErrMsg.$ErrorDetails);
				break;
			case E_STRICT:
				IPSLogger_Err("PHP", 'Runtime Notice: '.$ErrMsg.$ErrorDetails);
				break;
			default:
				IPSLogger_Err("PHP", 'Unknown Error: '.$ErrMsg.$ErrorDetails);
				$FatalError = true;
				break;
		}

		// Abort Processing during "Abort Flag"
		if (array_key_exists('ABORT_ON_ERROR', $_IPS) and $_IPS['ABORT_ON_ERROR']) {
			exit('Abort Processing during Error: '.$ErrMsg.$ErrorDetails);
		// Abort Processing during "FATAL Error"
		} elseif ($FatalError) {
			exit('Abort Processing during Fatal-Error: '.$ErrMsg.$ErrorDetails);
		} else {
			return false;
		}
	}

	$old_error_handler = set_error_handler("IPSLogger_PhpErrorHandler",E_ALL);

	function IPSLogger_PhpFatalErrorHandler() {
	  	if (@is_array($e = @error_get_last())) {
			//print_r($e); echo "Reporting=".error_reporting()."\n";
	    	$code = isset($e['type']) ? $e['type'] : 0;
	    	$msg  = isset($e['message']) ? $e['message'] : '';
	    	$file = isset($e['file']) ? $e['file'] : '';
	    	$line = isset($e['line']) ? $e['line'] : '';
	    	if ($code==1 /*Error*/) {
		 		IPSLogger_PhpErrorHandler ($code, $msg, $file, $line, null);
		 	}
		}
	}
	register_shutdown_function('IPSLogger_PhpFatalErrorHandler');



	/** @}*/
?>