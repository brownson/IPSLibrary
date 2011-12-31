<?
	/**@addtogroup ipslogger 
	 * @{
	 *
	 * @file          IPSLogger_ClearHtmlOut.ips.php
	 * @author        Andreas Brauneis
	 *
	 * Dieses Script löscht den Inhalt des HTML Outputs, der für die Anzeige im WebFront
	 * benützt wird.
	 *
	 */
	include "IPSLogger_Constants.inc.php";
	define ("c_LogId", "IPSLogger_ClearHtmlOut");

   SetValue(c_ID_HtmlOutMsgList, '');

	/** @}*/
?>