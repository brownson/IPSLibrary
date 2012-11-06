<?php 
	/**
	 * This file is part of the IPSLibrary.
	 *
	 * The IPSLibrary is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published
	 * by the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * The IPSLibrary is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
	 */

	/**@addtogroup ipsmodulemanagergui
	 * @{
	 *
	 * @file          IPSModuleManagerGUI_Logs.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 01.11.2012<br/>
	 *
	 * Anzeige aller Log Files des IPSModuleManager's
	 *
	 */

	/** @}*/
?>

<table border=1>
  <tr><th>Datum/Zeit</th><th>Modul</th><th>LogFile</th></tr>
<?php
	IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");
	$moduleManager = new IPSModuleManager();
	$files = $moduleManager->LogHandler()->GetLogFileList(30);
	$html = '';
	foreach ($files as $idx=>$fullfile) {
		$file = basename($fullfile);
		$data = str_replace('IPSModuleManager_', '', $file);
		$data = str_replace('.log', '', $data);
		$pos  = strpos($data, '_');
		$pos  = strpos($data, '_', $pos+1);
		if ($pos===false) {
			$module = '';
			$date   = $data;
		} else {
			$module = substr($data, $pos+1);
			$date   = substr($data, 0, $pos);
		}
		$date = str_replace('_', ' ', $date);
		$date = substr($date, 0, 13).':'.substr($date, 13, 2);
		
		$html .= '<tr><td><div style="text-align:left; color:white; padding-left:10px; padding-right:10px;">'.
		          '<a style="" href="#" onClick=trigger_button(\'LogFile\',\''.$module.'\',\''.$file.'\')>'.$date.'</a></div></td>';
		$html .= '<td><div style="text-align:left; color:white; padding-left:10px; padding-right:10px;">'.$module.'</div></td>';
		$html .= '<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.
		           '<a style="" href="#" onClick=trigger_button(\'LogFile\',\''.$module.'\',\''.$file.'\')>'.$file.'</a></div></td></tr>';
		$html .= PHP_EOL;
	}
	echo $html;
?>
</table>



