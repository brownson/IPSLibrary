<?
	/**@addtogroup ipslogger 
	 * @{
	 *
	 * @file          IPSLogger_Constants.ips.php
	 * @author        Andreas Brauneis
	 *
	 * Definition der Konstanten die für den Betrieb des Loggers nötig sind.
	 *
	 */

  include_once "\..\..\..\config\core\IPSLogger\IPSLogger_Configuration.inc.php";

  define ("c_LogLevel_Fatal",          0);
  define ("c_LogLevel_Error",          1);
  define ("c_LogLevel_Warning",        2);
  define ("c_LogLevel_Notification",   3);
  define ("c_LogLevel_Information",    4);
  define ("c_LogLevel_Debug",          5);
  define ("c_LogLevel_Communication",  6);
  define ("c_LogLevel_Trace",          7);
  define ("c_LogLevel_Test",           8);
  define ("c_LogLevel_All",            9);

  define ("c_LogType_Fatal",          'Fatal');
  define ("c_LogType_Error",          'Error');
  define ("c_LogType_Warning",        'Warning');
  define ("c_LogType_Notification",   'Notification');
  define ("c_LogType_Information",    'Information');
  define ("c_LogType_Debug",          'Debug');
  define ("c_LogType_Communication",  'Communication');
  define ("c_LogType_Trace",          'Trace');
  define ("c_LogType_Test",           'Test');

  define ("c_lf",    "\n");

  function IPSLogger_LogTypeShort ($LogType) {
		$LogTypeShort =  array (
			c_LogType_Fatal                 => 'Fat',
			c_LogType_Error                 => 'Err',
			c_LogType_Warning               => 'Wrn',
			c_LogType_Notification          => 'Not',
			c_LogType_Information           => 'Inf',
			c_LogType_Debug                 => 'Dbg',
			c_LogType_Communication         => 'Com',
			c_LogType_Trace                 => 'Trc',
			c_LogType_Test                  => 'Tst'
		);
		return $LogTypeShort[$LogType];
	}

	function IPSLogger_LogTypeXml ($LogType) {
		$LogTypeXml = array (
			c_LogType_Fatal                 => 'FATAL',
			c_LogType_Error                 => 'ERROR',
			c_LogType_Warning               => 'WARN',
			c_LogType_Notification          => 'NOT',
			c_LogType_Information           => 'INFO',
			c_LogType_Debug                 => 'DEBUG',
			c_LogType_Communication         => 'VERBOSE',
			c_LogType_Trace                 => 'TRACE',
			c_LogType_Test                  => 'NOTICE'
		);
		return $LogTypeXml[$LogType];
	}

  function IPSLogger_LogTypeStyle ($LogType) {
  		$LogTypeStyle= array (
			c_LogType_Fatal                 => 'color:#000000;background:#FF6347;',
			c_LogType_Error                 => 'color:#000000;background:#FF0000;',
			c_LogType_Warning               => 'color:#FFFFFF;background:#696969;',
			c_LogType_Notification          => 'color:#FFFFFF;',
			c_LogType_Information           => 'color:#FFFFFF;',
			c_LogType_Debug                 => 'color:#B0C4DE;',
			c_LogType_Communication         => 'color:#A9A9A9;',
			c_LogType_Trace                 => 'color:#808080;',
			c_LogType_Test                  => 'color:#4169E1;'
		);
		return $LogTypeStyle[$LogType];
	}

  function IPSLogger_HtmlEncode($s) {
    $source = array("&", "ä", "ö", "ü", "Ä", "Ö", "Ü", "ß", "<", ">", "€", "", "¹", "²", "³");
    $dest = array("&amp;", "&auml;", "&ouml;", "&uuml;", "&Auml;", "&Ouml;", "&Uuml;", "&szlig;", "&lt;", "&gt;", "&euro;", "¹", "&#178", "³");
    $s = str_replace($source, $dest, $s);
    return $s;
  }
	/** @}*/
?>