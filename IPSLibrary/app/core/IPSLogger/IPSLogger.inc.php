<?
	/**@defgroup ipslogger IPSLogger
	 * @ingroup core
	 *
	 * Logging Handler f�r IP-Symcon
	 *
   /** @}*/

	/**@defgroup ipslogger_webfront IPSLogger Visualisierung
	 * @ingroup ipslogger
	 * @{
	 *
	 * @page visu_logger_webfront IPSLogger WebFront Visualisierung
	 *
	 * Der IPSLogger bietet im WebFront die M�glichkeit die letzten x Messages anzusehen und die Settings
	 * der diversen Outputs einzustellen.
	 *
	 * Visualiserung der Meldungen
	 *@image html IPSLogger_WebFrontOutput.jpg
	 *
	 * �bersicht �ber die verf�gbaren Outputs
	 *@image html IPSLogger_WebFrontOverview.jpg
	 *
	 * Parametrisierung des EMAil Outputs
	 *@image html IPSLogger_WebFrontEMailSettings.jpg
	 *
	 * Zus�tzlich bietet der Logger die M�glichkeit eines Widgets, das �ber einen aufgetretenen Fehler sofort
	 * informiert.
	 *@image html IPSLogger_WebFrontWidget.jpg
	 *
	 */
   /** @}*/

	/**@defgroup ipslogger_api IPSLogger API
	 * @ingroup ipslogger
	 * @{
	 *
	 *	Der IPSLogger ist ein PHP Modul, mit dem es m�glich ist Anwendungsmeldungen und PHP Meldungen zu Loggen und auf verschiedene Outputs zu verteilen.
	 *
	 * Anwendungsmeldungen
	 *	Im allgemeinen werden Logging Meldungen vom User typisiert (Error, Information, Debug, usw.) und in die eigenen Scripts eingebaut.
	 *
	 *	PHP Meldungen
	 *	Alle PHP Meldungen werden als "Fatal Error" oder normaler "Error" reportet, voraussetzung ist die Registrierung eines PHP ErrorHandlers (Installation siehe weiter unten).
	 *
	 *
	 *	@page types Log Types
	 *	Der Logger bietet 9 verschiedene LogTypen, mit ihnen wird die Art bzw. Wichtigkeit einer Meldung kategorisiert. F�r jeden LogType steht eine eigene Logging Routine zur Verf�gung.
	 *
	 *	<ul>
	 *		<li>Fatal: Schwerwiegende Fehler, f�hrt normalerweise zu einem kompletten Programmabbruch (LogLevel=0)</li>
	 *		<li>Error: 'Normale' Fehler (LogLevel=1)</li>
	 *		<li>Warning: Warnungen (LogLevel=2)</li>
	 *		<li>Notification: Notifizierung, wird benutzt um sich �ber bestimmte Ereignisse im System informieren zu lassen (Beschattung wurde aktiviert, oder Rasenbew�sserung gestartet) (LogLevel=3)</li>
	 *		<li>Information: Informationsmeldungen, zur Protokollierung von Schaltvorg�ngen usw. (LogLevel=4)</li>
	 *		<li>Debug: Debug Meldungen (LogLevel=5)</li>
	 *		<li>Communication: Protokollierung von Kommunikations Instanzen (Senden/Empfangen �ber RS232, Sockets, ...) (LogLevel=6)</li>
	 *		<li>Trace: Sehr detailierte Meldungen, um diverse Ablauffehler zu finden (LogLevel=7)</li>
	 *		<li>Test: Test Meldungen, verwende ich nur tempor�r um Fehler zu finden, man kann nach diesen Meldungen suchen und sie nach finden des Fehlerers wieder entfernen(LogLevel=8)</li>
	 *	</ul>
	 *
	 *	@page outputs Unterst�tzte Outputs
	 *	Es werden zur Zeit 8 verschiedene Outputs unterst�tzt, f�r jeden kann man den LogLevel spezifisch definieren:
	 *
	 *	<ul>
	 *		<li>SingleMsg Output: enth�lt die letzte LogMeldung, vorgesehen als Widget, um sofort auf einen Fehler aufmerksam gemacht zu werden (verwendung von LogLevel Error oder Warning). Anzeige der Meldung kann durch Klick auf die Meldung Quittiert/Resetet werden.</li>
	 *		<li>Html Output: Output in HTML Form zur Verwendung im WebFront, Anzahl der Messages kann konfiguriert werden</li>
	 *		<li>IPS Output: IPS interner Logging Output</li>
	 *		<li>File Output: Protokollierung in ein File, f�r jeden Tag wird ein neues LogFile angelegt, Anzahl der Tage kann definiert werden, danach werden die Files autom. gel�scht. Standardm��ig wird in das IPS "logs" Verzeichnis gelogged, kann aber �ber eine Konstante auch ver�ndert werden.</li>
	 *		<li>Log4IPS Output: XML Protokollierung, die sich an die allgemein bekannte Form Log4Net,Log4Java usw. anlehnt. Ansehen kann man sich das File am besten mit diversen Tools (ich pers�nlich verwende Log4View, freie Version kann man sich auf der Homepage des Herstellers Log4View downloaden ).</li>
	 *		<li>Email Output: Bietet die M�glichkeit sich �ber Fehler im System per Email informieren zu lassen. Ein Sende Verz�gerung kann konfiguriert werden, bei 0 wird bei jedem Fehler (wenn LogLevel auf Error) sofort gesendet, ansonsten wird die angegebene Zeit gewartet und danach alle aufgetretenen Fehler als Liste versandt. Um den Email Output verwenden zu k�nnen mu� man im File "IPSLogger_Configuration.ips.php" die ID f�r das SMTP Device eintragen. Optional kann man auch noch 1-3 Email Adressen eingtragen, standardm��ig wird der Emf�nger verwendet, der im Device angegeben ist. Vorsicht: bei Delay 0 kann man sich potenziell einen Spam-Server basteln falls man irgenwo einen Endlos-Loop mit Fehler hat ...</li>
	 *		<li>Echo Output: Echo Output, kann in der Entwicklungs Phase von Scripten gen�tzt werden.</li>
	 *		<li>Prowl Output: Erm�glicht das Senden von Messages direkt auf das iPhone.</li>
	 *	</ul>
	 *
	 * @page practie Best Practice
	 * @section logging Logging mit Script Konstante oder FileNamen
	 * @code
	   IPSUtils_Include ("IPSLogger.inc.php", "IPSLibrary::app::core::IPSLogger");

	   define ("c_LogId", "MyProcedure");

	   // Code �
	   IPSLogger_Inf(c_LogId, "Schalte Licht �");
		@endcode
	 * @code
	   IPSUtils_Include ("IPSLogger.inc.php", "IPSLibrary::app::core::IPSLogger");
	   IPSLogger_Inf(__file__, "Schalte Licht �");
		@endcode
	 *
	 * @section logerrors Loggen von Fehlern
	 * @code
	   IPSUtils_Include ("IPSLogger.inc.php", "IPSLibrary::app::core::IPSLogger");
	   $VariableId = @IPS_GetVariableIDByName('MeineVariable', 0);

	   if ($VariableId==0) {
	      IPSLogger_Err(__file__, "Variable mit Namen 'MeineVariable' konnte nicht gefunden werden");
	      exit; // Abbruch der aktuellen Verarbeitung
	   }
	@endcode
	 *
	 * @file          IPSLogger.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 */
	global $_IPS;
	if (!array_key_exists('ABORT_ON_ERROR',$_IPS)) {
		$_IPS['ABORT_ON_ERROR'] = false;
	}

	include_once "IPSLogger_Constants.inc.php";
	include_once "IPSLogger_Output.inc.php";


	// ---------------------------------------------------------------------------------------------------------------------------
	/** Procedure to log a Fatal Error
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 */
   function IPSLogger_Fat($LogContext, $LogMessage) {
      IPSLogger_Out(c_LogLevel_Fatal, c_LogType_Fatal, $LogContext, $LogMessage);
   }

	// ---------------------------------------------------------------------------------------------------------------------------
	/** Procedure to log a Error
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 */
	function IPSLogger_Err($LogContext, $LogMessage) {
		IPSLogger_Out(c_LogLevel_Error, c_LogType_Error, $LogContext, $LogMessage);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	/** Procedure to log a Warning
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 */
   function IPSLogger_Wrn($LogContext, $LogMessage) {
      IPSLogger_Out(c_LogLevel_Warning, c_LogType_Warning, $LogContext, $LogMessage);
   }

	// ---------------------------------------------------------------------------------------------------------------------------
	/** Procedure to log a Notification
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 * @param $Priority - Priority of Notificaton Message (0 means high priority, higher values indicates a lower priority)
	 */
   function IPSLogger_Not($LogContext, $LogMessage, $Priority=0) {
      IPSLogger_Out(c_LogLevel_Notification, c_LogType_Notification, $LogContext, $LogMessage, $Priority);
   }

	// ---------------------------------------------------------------------------------------------------------------------------
	/** Procedure to log Informations
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 */
   function IPSLogger_Inf($LogContext, $LogMessage) {
      IPSLogger_Out(c_LogLevel_Information, c_LogType_Information, $LogContext, $LogMessage);
   }

	// ---------------------------------------------------------------------------------------------------------------------------
	/** Debugging Procedure for IPS LogHandler
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 */
   function IPSLogger_Dbg($LogContext, $LogMessage) {
      IPSLogger_Out(c_LogLevel_Debug, c_LogType_Debug, $LogContext, $LogMessage);
   }

	// ---------------------------------------------------------------------------------------------------------------------------
	/** Procedure to log Communications
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 */
   function IPSLogger_Com($LogContext, $LogMessage) {
      IPSLogger_Out(c_LogLevel_Communication, c_LogType_Communication, $LogContext, $LogMessage);
   }

	// ---------------------------------------------------------------------------------------------------------------------------
	/** Procedure to log Test Messages
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 */
   function IPSLogger_Tst($LogContext, $LogMessage) {
      IPSLogger_Out(c_LogLevel_Test, c_LogType_Test, $LogContext, $LogMessage);
   }

	// ---------------------------------------------------------------------------------------------------------------------------
	/** Procedure for Tracing Messages
	 *
	 * @param $LogContext - Context of Logging (Identifier or Filename).
	 * @param $LogMessage - Message to be logged
	 */
   function IPSLogger_Trc($LogContext, $LogMessage) {
      IPSLogger_Out(c_LogLevel_Trace, c_LogType_Trace, $LogContext, $LogMessage);
   }
   
   // ---------------------------------------------------------------------------------------------------------------------------
    /** Procedure to set a custom trace level for a specific logcontext
     *
     * @param $LogContext - Context of Logging (Identifier or Filename).
     * @param $LogLevel - the level at which logging should start
     */
    function IPSLogger_SetLoggingLevel($LogContext, $LogLevel) {
        IPSLogger_SetContextLoggingLevel($LogContext, $LogLevel);
    }
   /** @}*/
?>