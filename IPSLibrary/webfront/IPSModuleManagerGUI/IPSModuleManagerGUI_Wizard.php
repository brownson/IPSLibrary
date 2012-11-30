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
	$html = '<h2>Installation von Modul '.$module.'</h2>';

	$file      = IPS_GetKernelDir().'scripts\\IPSLibrary\\install\\InitializationFiles\\'.$module.'.ini';
	$configUsr = parse_ini_file($file, true);
	$file      = IPS_GetKernelDir().'scripts\\IPSLibrary\\install\\InitializationFiles\\Default\\'.$module.'.ini';
	$configDef = parse_ini_file($file, true);

		$html2  = '';
	if (array_key_exists('WFC10', $configDef)) {
		if (!array_key_exists('ID', $configDef['WFC10'])) {
			$configDef['WFC10']['ID'] = GetWFCIdDefault();
		}
		if (!array_key_exists('ID', $configUsr['WFC10'])) {
			$configUsr['WFC10']['ID'] = GetWFCIdDefault();
		}
		$html .= '<h4>WebFront (10" Optimierung)</h4>';
		$html .= '<table border=0>';
		$html .= '  <tr><th>Parameter</th><th>Wert</th><th>Default Wert</th><th>Beschreibung</th></tr>';
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'Enabled',         'checkbox', 'WebFront Interface',       'Mit diesem Parameter kann gesteuert werden, ob eine WebFront Installation durchgeführt wird');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'Path',            'text',     'Installation Pfad',        'Legt fest in welchem Pfad die Struktur für das WebFront Interface in IP-Symcon abgelegt wird');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'ID',              'text',     'WebFront Konfigurator ID', 'ID des WebFront Konfigurator, der für die Installation des WebFront Interfaces verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabPaneItem',     'text',     'TabPane ID',               'Bestimmt den internen Namen im WebFront Konfigurator');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabPaneParent',   'text',     'TabPane Parent',           'Übergeordnetes Element im WebFront Konfigurator (normalerweise roottp)');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabPaneName',     'text',     'TabPane Titel',            'Text, der für das Element in der obersten Navigations Leiste verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabPaneIcon',     'text',     'TabPane Icon',             'Icon, das für das Element in der obersten Navigations Leiste verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabPaneOrder',    'text',     'TabPane Order',            'Reihenfolge, die für das Element in der obersten Navigations Leiste verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabPaneExclusive','checkbox', 'TabPane Exclusive',        'Element in der obersten Navigations Leiste steht dem Modul "exklusiv" zur Verfügung (ACHTUNG: Bei Aktivierung wird bei einer Neuinstallation der komplette Inhalt des Elementes gelöscht!)');

		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabItem',         'text',     'ID',                       'Bestimmt den internen Namen im WebFront Konfigurator (die verwendete ID setzt sich aus der TabPane ID und dieser ID zusammen)');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabName',         'text',     'Titel',                    'Text, der für das Element in der unteren Navigations Leiste verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabIcon',         'text',     'Icon',                     'Icon, das für das Element in der unteren Navigations Leiste verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'WFC10', 'TabOrder',        'text',     'Order',                    'Reihenfolge, die für das Element in der unteren Navigations Leiste verwendet werden soll');
		$html  .= '</table>';
	}
	
	if (array_key_exists('Mobile', $configDef)) {
		$html .= '<h4>Mobile Interface</h4>';
		$html .= '<table border=0>';
		$html .= '  <tr><th>Parameter</th><th>Wert</th><th>Default Wert</th><th>Beschreibung</th></tr>';
		$html .= Get_TableRow($configUsr, $configDef, 'Mobile', 'Enabled',      'checkbox', 'Mobile Interface',           'Mit diesem Parameter kann gesteuert werden, ob eine WebFront Installation durchgeführt wird');
		$html .= Get_TableRow($configUsr, $configDef, 'Mobile', 'Path',         'text',     'Installation Pfad',          'Legt fest in welchem Pfad die Struktur für das Mobile Interface in IP-Symcon abgelegt wird');
		$html .= Get_TableRow($configUsr, $configDef, 'Mobile', 'PathOrder',    'text',     'Reihenfolge (Pfad',          'Reihenfolge, die für das Element in der übergeordneten Ebene im Mobile Interface verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'Mobile', 'PathIcon',     'text',     'Icon (Pfad)',                'Icon, das für das Element in der übergeordneten Ebene im Mobile Interface verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'Mobile', 'Name',         'text',     'Modul Titel',                'Text, der für das Modul im Mobile Interface verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'Mobile', 'Icon',         'text',     'Modul Icon',                 'Icon, das für das Modul im Mobile Interface verwendet werden soll');
		$html .= Get_TableRow($configUsr, $configDef, 'Mobile', 'Order',        'text',     'Modul Reihenfolge',          'Reihenfolge, die für das Modul im Mobile Interface verwendet werden soll');
		$html  .= '</table>';
	}

	
	// Update Section
	$html .= '<p>';
	if (!$processing) {
		$html .= '<input type="button" name="Text" value="Einstellungen Speichern und Installieren" onclick="trigger_button2(\'StoreAndInstall\',\''.$module.'\', \'\')">';
		$html .= '<input type="button" name="Text" value="Einstellungen Speichern" onclick="trigger_button2(\'Store\',\''.$module.'\', \'\')">';
	} else {
		echo 'processing ...';
	}
	$html .= '</p>';
	

	echo $html;

	
	function Get_BooleanValue($value) {
		if ($value=='false') {
			return false;
		} elseif ($value=='true') {
			return true;
		} else {
			return (boolean)$value;
		}
	}
	
	function Get_TableRow($configUsr, $configDef, $section, $property, $type, $name, $description) {
		$html = '';
		if (array_key_exists($property, $configDef[$section])) { 
			$valueUsr = '';
			$valueDef = '';
			if (array_key_exists($section, $configUsr) and array_key_exists($property, $configUsr[$section])) { $valueUsr .= $configUsr[$section][$property]; }
			if (array_key_exists($property, $configDef[$section])) { $valueDef .= $configDef[$section][$property]; }
			$valueUsr = htmlentities($valueUsr, ENT_COMPAT, 'ISO-8859-1');
			$valueDef = htmlentities($valueDef, ENT_COMPAT, 'ISO-8859-1');
			switch ($type) {
				case 'checkbox':
					$inputUsr = 'type="'.$type.'" value="Aktiv"';
					if (Get_BooleanValue($valueUsr)) { $inputUsr .= 'checked'; }
					$inputDef = 'type="'.$type.'" readonly value="Aktiv"';
					if (Get_BooleanValue($valueDef)) { $inputDef .= 'checked'; }
					break;
				default;
					$inputUsr = 'type="text" size="30" maxlength="30" value="'.$valueUsr.'"';
					$inputDef = 'type="text" size="30" maxlength="30" value="'.$valueDef.'" disabled';
			}

			$html = '<tr>'
						 .'<td><div style="text-align:left; width:150px; color:grey; padding-left:10px; padding-right:10px;">'.htmlentities($name, ENT_COMPAT, 'ISO-8859-1').'</div></td>'
						 .'<td><div style="text-align:left; color:white; padding-left:10px; padding-right:10px;">'
							  .'<input id="'.$section.$property.'" name="'.$property.'" '.$inputUsr.' ></div></td>'
						 .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'
							  .'<input name="'.$property.'def" '.$inputDef.' ></div></td>'
						 .'<td><div style="text-align:left; color:grey; padding-left:10px; padding-right:10px;">'.htmlentities($description, ENT_COMPAT, 'ISO-8859-1').'</div></td>'
				   .'</tr>';
		}
		return $html;
	}
	
?>


