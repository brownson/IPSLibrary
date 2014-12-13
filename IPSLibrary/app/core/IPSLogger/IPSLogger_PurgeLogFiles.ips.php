<?
	/**@addtogroup ipslogger
	 * @{
	 *
	 * @file          IPSLogger_PurgeLogFiles.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Löschen der alten Log Files
	 *
	 * Dieses Script wird täglich durch einen Timer aufgerufen und löscht die alten Log Files.
	 * Die Anzahlt der LogFiles, die behalten werden, kann über das WebFront eingestellt werden.
	 *
	 */
	include "IPSLogger.inc.php";
	define ("c_LogId", "IPSLogger_PurgeLogFiles");

	PurgeLogFiles(c_File_Directory, c_File_Extension, c_ID_FileOutEnabled, c_ID_FileOutDays);
	PurgeLogFiles(c_Log4IPS_Directory, c_Log4IPS_Extension, c_ID_Log4IPSOutEnabled, c_ID_Log4IPSOutDays);

	function PurgeLogFiles($Directory, $Extension, $ID_OutSwitch, $ID_OutDays) {
	   if (GetValue($ID_OutSwitch)) {
			IPSLogger_Dbg(c_LogId, 'Purging *.'.$Extension.' LogFiles in Directory '.$Directory);
			$Days = GetValue($ID_OutDays);
			$ReferenceDate=Date('Ymd', strtotime("-".$Days." days"));
		    if ($Directory == "") {
				$Directory = IPS_GetKernelDir().'logs/';
				if (function_exists('IPS_GetLogDir'))
					$Directory = IPS_GetLogDir();
		    }

			if (($handle=opendir($Directory))===false) {
			   IPSLogger_Err(c_LogId, 'Error Opening Directory '.$Directory);
				Exit;
			}

			while (($File = readdir($handle))!==false) {
		      $FileDate      = substr($File, strlen('IPSLogger_'), 8);
		      $FileExtension = substr($File, strlen('IPSLogger_')+8+1, 3);
		      if ($Extension==$FileExtension) {
					IPSLogger_Trc(c_LogId, 'Found File: '.$File.', FileDate='.$FileDate.', RefDate='.$ReferenceDate);
					if ($FileDate < $ReferenceDate) {
						IPSLogger_Inf(c_LogId, 'Delete LogFile: '.$Directory.$File);
					   unlink($Directory.$File);
					}
				}
			}
			closedir($handle);
		}
	}

	/** @}*/
?>