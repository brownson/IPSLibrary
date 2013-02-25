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
	 * @file          IPSModuleManagerGUI_Overview.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 01.11.2012<br/>
	 *
	 * Anzeige aller installierten IPSLibrary Module
	 *
	 */

	/** @}*/
?>

<table border=1>
  <tr><th>Modul</th><th>Aktuelle<BR>Version</th><th>Verf&uuml;gbare<BR>Version</th><th>Status</th><th>Beschreibung</th></tr>
<?php
	IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");
	$moduleManager = new IPSModuleManager();
	$modules = $moduleManager->GetInstalledModules();
	$html = '';
	$updateButton = false;
	foreach ($modules as $module=>$version) {
		$moduleManager = new IPSModuleManager($module);
		$infos   = $moduleManager->GetModuleInfos();
		$infos['LastRepository']   = str_replace('https://raw.github.com/','...',$infos['LastRepository']);
		$infos['AvailableVersion']  = '';
		$versionAttribute = '';
		$stateAttribute = '';
		if ($moduleManager->VersionHandler()->CompareVersionsNewer($infos['CurrentVersion'], $infos['Version'])) {
			$infos['AvailableVersion']  = $infos['Version'];
			$versionAttribute = 'color:red;';
			$updateButton = true;
		} 
		if ($infos['State']<>'OK') { 
			$stateAttribute = 'color:red;';
		}
		$html .= '  <tr>';
		$html .=  GetTableData(null, null, $module, '', 'Module', $module);
		$html .=  GetTableData($infos, 'CurrentVersion');
		$html .=  GetTableData($infos, 'AvailableVersion', '', $versionAttribute, 'Updates');
		$html .=  GetTableData($infos, 'State', '', $stateAttribute);
		//$html .=  GetTableData($infos, 'LastRepository');
		//$html .=  GetTableData($infos, 'Repository');
		$html .=  GetTableData($infos, 'Description');
		$html .= '</tr>'.PHP_EOL;
	}
	echo $html;
?>
</table>


<?php
	$properties = '';
	if ($processing) {
		$properties = 'disabled';
	}
	if (count($modules)>0) {
		echo '<input type="button" '.$properties.' name="Text" value="Update aller Module" onclick="trigger_button(\'UpdateAll\', \'\', \'\')">';
	}
	echo '<input type="button" '.$properties.' name="Text" value="Nach neuen Update\'s Suchen" onclick="trigger_button(\'SearchUpdates\', \'\', \'\')">';
?>


<?php
	function GetTableData($infos, $property, $defaultValue='', $displayAttributes='', $action='', $module='', $info='') {
		$text = $defaultValue;
		if (is_array($infos) and array_key_exists($property, $infos)) {
			$text = $infos[$property];
		}
		if ($text=='') {
			$text='-';
		}
		$text = htmlentities($text, ENT_COMPAT, 'ISO-8859-1');
		if ($action<>'') {
			$text = '<a style="'.$displayAttributes.'" href="#" onClick=trigger_button(\''.$action.'\',\''.$module.'\',\''.$info.'\')>'.$text.'</a>';
		}
		$return = '<td><div style="text-align:left; overflow:hidden; padding-left:10px; padding-right:10px; '.$displayAttributes.'">'.$text.'</div></td>';
		return $return;
	}
?>

