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
	 * @file          IPSModuleManagerGUI_LogFile.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 01.11.2012<br/>
	 *
	 * Anzeige eines LogFile's des IPSModuleManager's
	 *
	 */

	/** @}*/
?>

<table border=1 style="font-family:courier; font-size:11px;">
  <tr><th>App</th><th>Type</th><th>Context</th><th>Date/Time</th><th>Message</th></tr>
<?php
	IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");
	$moduleManager = new IPSModuleManager();
	$logLines = $moduleManager->LogHandler()->GetLogFileContent($info);
	$html = '';
	foreach ($logLines as $idx=>$line) {
		$pos1  = strpos($line, '-');
		if ($pos1!==false) {
			$pos2  = strpos($line, '-', $pos1+1);
			$pos3  = $pos2+30;

			$app      = substr($line, 0, $pos1);
			$logtype  = substr($line, $pos1+1, $pos2-$pos1-1);
			$function = substr($line, $pos2+1, $pos3-$pos2-1);
			$date     = substr($line, $pos3+1, 22);
			$msg      = substr($line, $pos3+23);
		} else {
			$app      = '';
			$logtype  = '';
			$function = '';
			$date     = '';
			$msg      = '';
		}
		
		$html .= '<tr>';
		$html .= '<td><div style="text-align:left; color:white; padding-left:4px; padding-right:4px;">'.$app.'</div></td>';
		$html .= '<td><div style="text-align:left; color:white; padding-left:4px; padding-right:4px;">'.$logtype.'</div></td>';
		$html .= '<td><div style="text-align:left; color:white; padding-left:4px; padding-right:4px;">'.$function.'</div></td>';
		$html .= '<td><div style="text-align:left; color:white; padding-left:4px; padding-right:4px;">'.$date.'</div></td>';
		$html .= '<td><div style="text-align:left; color:white; padding-left:4px; padding-right:4px;">'.$msg.'</div></td>';
		$html .= '</tr>';
		$html .= PHP_EOL;
	}
	echo $html;
?>
</table>



