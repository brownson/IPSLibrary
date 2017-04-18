<?
    /*
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

	/**@defgroup ipshighcharts_visualization IPSHighcharts Visualisierung
	 * @ingroup ipshighcharts
	 * @{
	 *
	 * Visualisierungen von IPSHighcharts
	 *
	 * IPSHighcharts WebFront Visualisierung:
	 *
	 *
	 *@}*/

	/**@defgroup ipshighcharts_install IPSHighcharts Installation
	 * @ingroup ipshighcharts
	 * @{
	 *
	 * Script zur kompletten Installation von IPSHighcharts.
	 *
	 * Vor der Installation muß das File IPSHighcharts_Configuration.inc.php an die persönlichen
	 * Bedürfnisse angepasst werden.
	 *
	 * @page requirements_ipshighcharts Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.50.2
	 * - IPSLogger >= 2.50.1
	 * - IPSComponent >= 2.50.1
	 *
	 * @page install_ipshighcharts Installations Schritte
	 * Folgende Schritte sind zur Installation der IPSHighcharts Ansteuerung nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 * @file          IPSHighcharts_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.09.2012<br/>
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSHighcharts');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.2');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.2');

	IPSUtils_Include ("IPSInstaller.inc.php",                 "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSHighcharts.inc.php",                "IPSLibrary::app::modules::Charts::IPSHighcharts");

	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');


?>