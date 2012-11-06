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
	 * @file          IPSModuleManagerGUI_Module.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 01.11.2012<br/>
	 *
	 * Anzeige aller verfügbaren IPSLibrary Module Updates
	 *
	 */

	/** @}*/
?>

<?php
	IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");
	$html = '<h2>'.$module.'</h2>';

	$moduleManager = new IPSModuleManager();
	$modules = $moduleManager->GetInstalledModules();
	$moduleInstalled = array_key_exists($module, $modules);
	
	$moduleManager = new IPSModuleManager($module);
	$infos   = $moduleManager->GetModuleInfos();


	// Common Section
	$color='white';
	if ($infos['CurrentVersion'] <> $infos['Version'] and $moduleInstalled) { 
		$color='red'; 
	}
	$html  .= '<table>';
	$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">Beschreibung</div></td>'
		                   .'<td><div style="text-align:left; color:white; padding-left:10px; padding-right:10px;">'.htmlentities($infos['Description']).'</div></td></tr>';
	$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">Modul Status</div></td>'
		                   .'<td><div style="text-align:left; color:white; padding-left:10px; padding-right:10px;">'.htmlentities($infos['State']).'</div></td></tr>';
	$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">Verf&uuml;gbare Version</div></td>'
		                   .'<td><div style="text-align:left; color:'.$color.'; padding-left:10px; padding-right:10px;">'.htmlentities($infos['Version']).'</div></td></tr>';
	$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">Repository</div></td>'
		                   .'<td><div style="text-align:left; color:white; padding-left:10px; padding-right:10px;">'.htmlentities($infos['Repository']).'</div></td></tr>';
	if ($moduleInstalled) {
		$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">Aktuelle Version</div></td>'
							   .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.htmlentities($infos['CurrentVersion']).'</div></td></tr>';
		$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">Aktuelles Repository</div></td>'
							   .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.htmlentities($infos['LastRepository']).'</div></td></tr>';
	}
	$html  .= '</table>';
	
	// Update Section
	$html .= '<p>';
	if (!$processing) {
		if (!$moduleInstalled) {
			$html .= '<input type="button" name="Text" value="Modul Laden"       onclick="trigger_button(\'Load\', \''.$module.'\', \'\')">';
		} else {
			$html .= '<input type="button" name="Text" value="Modul Update"       onclick="trigger_button(\'Update\', \''.$module.'\', \'\')">';
			$html .= '<input type="button" name="Text" value="Modul Installieren" onclick="trigger_button(\'Install\',\''.$module.'\', \'\')">';
			$html .= '<input type="button" name="Text" value="Modul L&ouml;schen" onclick="trigger_button(\'Delete\', \''.$module.'\', \'\')">';
		}
	} else {
		echo 'processing ...';
	}
	$html .= '</p>';
	
	// Required Modules
	$html .= '<h5>Ben&ouml;tigte Module</h5>';
	$modules = $moduleManager->VersionHandler()->GetRequiredModules($module);
	$html  .= '<table>';
	foreach ($modules as $subModule=>$version) {
		$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'
		                        .'<a style="" href="#" onClick="trigger_button(\'Module\',\''.$subModule.'\',\'\')">'.$subModule.'</a></div></td>'
		                   .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.$version.'</div></td></tr>';
	}
	$html  .= '</table>';

	// Versions
	$html .= '<h5>Repository Versionen</h5>';
	$modules = $moduleManager->VersionHandler()->GetRepositoryVersions($module);
	$html  .= '<table>';
	foreach ($modules as $repository=>$version) {
		$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.$repository.'</div></td>'
		                   .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.$version.'</div></td></tr>';
	}
	$html  .= '</table>';

	// ChangeList
	$html .= '<h5>Liste der &Auml;nderungen</h5>';
	$changes = $moduleManager->VersionHandler()->GetChangeList($module, false);
	$html  .= '<table>';
	foreach ($changes as $version=>$change) {
		$html  .= '<tr><td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.$version.'</div></td>'
		                   .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.htmlentities($change).'</div></td></tr>';
	}
	$html  .= '</table>';

	echo $html;
?>


