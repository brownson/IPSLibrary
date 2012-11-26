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
	 * @file          IPSModuleManagerGUI_Updates.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 01.11.2012<br/>
	 *
	 * Anzeige aller verfügbaren IPSLibrary Module Updates
	 *
	 */

	/** @}*/
?>

<table border=1>
  <tr><th>Modul</th><th>Aktuelle<BR>Version</th><th>Neue<BR>Version</th><th>Update</th><th>&Auml;nderungen</th></tr>
<?php
	IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");
	$moduleManager = new IPSModuleManager();
	$modules = $moduleManager->VersionHandler()->GetKnownUpdates();
	$html = '';
	foreach ($modules as $idx=>$module) {
		$moduleManager = new IPSModuleManager($module);
		$infos   = $moduleManager->GetModuleInfos();
		$changes = $moduleManager->VersionHandler()->GetChangeList($module);
		$text  = '<table><tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">Aktuelles Repository</div></td>'
		                   .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.$infos['LastRepository'].'</div></td></tr>';
		$text .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">Neues Repository</div></td>'
		            .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.$infos['Repository'].'</div></td></tr>';
		foreach ($changes as $version=>$change) {
			$text .= '<tr><td><div style="text-align:left; color:white; padding-left:10px; padding-right:10px;">Version '.$version.'</div></td>'
			            .'<td><div style="text-align:left; color:white; padding-left:10px; padding-right:10px;">'.htmlentities($change, ENT_COMPAT, 'ISO-8859-1').'</div></td></tr>';
		}
		$text .= '</table>';
		$html .= '  <tr>';
		$html .=  GetTableData($module, '', 'Module', $module);
		$html .=  GetTableData($infos['CurrentVersion']);
		$html .=  GetTableData($infos['Version']);
		if (!$processing) {
			$html .= '<td><input type="button" name="Text" value="Update" onclick="trigger_button(\'Update\', \''.$module.'\', \'\')"></td>';
		} else {
			$html .= '<td>processing ...</td>';
		}
		$html .= '<td><div style="text-align:left; overflow:hidden; padding-left:10px; padding-right:10px;">'.$text.'</div></td>';
		$html .= '</tr>'.PHP_EOL;
	}
	echo $html;
?>
</table>


<?php
	if (!$processing) {
		if (count($modules)>0) {
			echo '<input type="button" name="Text" value="Update aller Module" onclick="trigger_button(\'UpdateAll\', \'\', \'\')">';
		}
		echo '<input type="button" name="Text" value="Nach neuen Update\'s Suchen" onclick="trigger_button(\'SearchUpdates\', \'\', \'\')">';
	} else {
		echo 'processing ...';
	}
?>


<?php
	function GetTableData($value='', $displayAttributes='', $action='', $module='', $info='') {
		$text = $value;
		if ($text=='') {
			$text='-';
		}
		$text = htmlentities($text, ENT_COMPAT, 'ISO-8859-1'));
		if ($action<>'') {
			$text = '<a style="'.$displayAttributes.'" href="#" onClick=trigger_button(\''.$action.'\',\''.$module.'\',\''.$info.'\')>'.$text.'</a>';
		}
		$return = '<td><div style="text-align:left; overflow:hidden; padding-left:10px; padding-right:10px; '.$displayAttributes.'">'.$text.'</div></td>';
		return $return;
	}
?>

