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
	 * @file          IPSModuleManagerGUI_NewModule.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 01.11.2012<br/>
	 *
	 * Laden von Neuen Modulen der IPSLibrary
	 *
	 */

	/** @}*/
?>

<table border=1>
  <tr><th>Modul</th><th>Version</th><th>Laden</th><th>Beschreibung</th></tr>
<?php
	IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");
	$moduleManager = new IPSModuleManager();
	$knownModules     = $moduleManager->VersionHandler()->GetKnownModules();
	$installedModules = $moduleManager->VersionHandler()->GetInstalledModules();
	$html = '';
	foreach ($knownModules as $module=>$data) {
		$infos   = $moduleManager->GetModuleInfos($module);

		$html .= '  <tr>';
		$html .=  GetTableData($module, '', 'Module', $module);
		$html .=  GetTableData($infos['Version']);
		if (array_key_exists($module, $installedModules)) {
			$html .= '<td></td>';
		} elseif ($processing) {
			$html .= '<td><input type="button" disabled name="Text" value="Modul Laden" onclick="trigger_button(\'Load\', \''.$module.'\', \'\')"></td>';
		} else {
			$html .= '<td><input type="button"  name="Text" value="Modul Laden" onclick="trigger_button(\'Load\', \''.$module.'\', \'\')"></td>';
		}
		$html .=  GetTableData($infos['Description']);
		$html .= '</tr>'.PHP_EOL;
	}
	echo $html;
?>
</table>


<?php
	function GetTableData($value='', $displayAttributes='', $action='', $module='', $info='') {
		$text = $value;
		if ($text=='') {
			$text='-';
		}
		$text = htmlentities($text);
		if ($action<>'') {
			$text = '<a style="'.$displayAttributes.'" href="#" onClick=trigger_button(\''.$action.'\',\''.$module.'\',\''.$info.'\')>'.$text.'</a>';
		}
		$return = '<td><div style="text-align:left; overflow:hidden; padding-left:10px; padding-right:10px; '.$displayAttributes.'">'.$text.'</div></td>';
		return $return;
	}
?>

