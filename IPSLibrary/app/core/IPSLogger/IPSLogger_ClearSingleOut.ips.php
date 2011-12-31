<?
	/**@addtogroup ipslogger 
	 * @{
	 *
	 * @file          IPSLogger_ClearSingleOut.ips.php
	 * @author        Andreas Brauneis
	 *
	 * Dieses Script löscht die Message, die in der Variable des "Single Outputs" steht. Das Script ist als
	 * Action Script des IPSLogger Widgets definiert.
	 *
	 */
	include "IPSLogger_Constants.inc.php";
	define ("c_LogId", "IPSLogger_ClearSingleOut");

   SetValue(c_ID_SingleOutMsg, '');
	/** @}*/
?>