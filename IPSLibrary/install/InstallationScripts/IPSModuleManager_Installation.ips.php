<?
	/**@defgroup ipsmodulemanager_overview IPSModuleManager Übersicht
	 * @ingroup ipsmodulemanager
	 * @{
	 *
	 * Übersicht IPSModuleManager
	 * @image html IPSModuleManager_Overview.png
	 *
	 * Der IPSModuleManager supportet die folgenden Möglichkeiten:
	 * - Initialer Download und Installation neuer Module
	 * - Update auf neuere Version von Modulen
	 * - Versionsverwaltung für Module
	 * - Backup Handler
	 * - Konfigurations Handler
	 * - Log Handler
	 *
	 * @page ipsmodulemanager_download Initialer Download von Modulen
	 *  Die Installation neuer Module gliedert sich in 3 Phasen:
	 *  - Module Download
	 *  - Konfiguration
	 *  - Installation
	 *
	 *  Ein Module kann nach mit folgendem Code geladen werden:
	 *  @code
	      IPSUtils_Include ("IPSModuleManager.class.php","IPSLibrary::install::IPSModuleManager");
	      $moduleManager = new IPSModuleManager('IPSLogger');
	      $moduleManager.LoadModule();
	    @endcode
	 *
	 * Eine Konfiguration ist nicht bei jedem Module nötig, Details über die jeweilige Konfiguration kann der jeweiligen Module Dokumentation entnommen werden.
	 *
	 *  Folgende Schritte warden bei einer Installation ausgeführt:
	 *  - Check von anderen Modulen, die für die Installation Voraussetzung sind
	 *  Versions Check von referenzierten Modulen
	 *  Installation (beinhaltet das Anlegen aller benötigten Variablen und Scripte)
	 *
	 *  Beispiel zur Installation des IPSLogger Modules:
	 *  @code
          IPSUtils_Include ("IPSModuleManager.class.php","IPSLibrary::install::IPSModuleManager");
          $moduleManager = new IPSModuleManager('IPSLogger');
          $moduleManager.InstallModule();
        @endcode
	 *
	 * @page ipsmodulemanager_update Update eines oder mehrerer Module
	 *  Update eines einzelnen Modules erfolgt mit folgendem Code:
	 *  @code
          IPSUtils_Include ("IPSModuleManager.class.php","IPSLibrary::install::IPSModuleManager");
          $moduleManager = new IPSModuleManager('IPSLogger');
          $moduleManager.UpdateModule();
        @endcode
	 *
	 * Ein Update aller installierten Module kann mit folgendem Code gemacht werden;
	 *  @code
          IPSUtils_Include ("IPSModuleManager.class.php","IPSLibrary::install::IPSModuleManager");
          $moduleManager = new IPSModuleManager('');
          $moduleManager.UpdateAllModules();
        @endcode
	 *
	 *  Bei einem Update werden alle Scripte des jeweiligen Modules neu aus dem Repository geladen
	 *  (Konfigurations Scripte und Dateien werden nicht verändert). Sollte nach dem Update eine neue
	 *  Versionsnummer gefunden werden, wird automatisch das Installations Script ausgeführt (inklusive
	 *  Anpassung der WebFront und Mobile Strukturen).
	 *
	 * @page ipsmodulemanager_version Versions Verwaltung
	 *  Der IPSModuleManager beinhaltet eine interne Versionsverwaltung, der die aktuelle IPS Version überprüft und auch alle Module überprüft, die
	 *  Voraussetzung des aktuellen Modules sind.
	 *
	 * @page ipsmodulemanager_logging Logging des IPSModuleManagers
	 *  Der LogHandler legt bei jedem Update oder Installations Vorgang ein Protokoll File an, wo man die einzelnen Schritte des Installations Ablaufes
	 *  nachvollziehen kann. Standardmäßig ist das Output Verzeichnis auf "logs" gesetzt, kann aber jederzeit über den Parameter "LogDirectory" verändert werden.
	 *  Output erfolgt in eine Datei mit dem Namen IPSModuleManager_YYYY-MM-DD_HHMI.log
	 *
	 * @page ipsmodulemanager_config Konfigurations Handler
	 *  Der Konfigurations Handler bietet die Möglichkeit Installation Parameter (wie zum Beispiel WebFront Konfigurator ID, persönliche
	 *  Icons, Namen usw.) in einem Initialisierungs File abzulegen und so die Installation der Module auf die persönlichen Bedürfnisse abzustimmen.
	 *
	 * @page ipsmodulemanager_backup Backup Handler
	 *  Vor dem Download der neuen Scripte wird auch automatisch ein Backup der Scripte des betroffen Modules gemacht.
	 *  Ablageort für Backups ist standardmäßig auf "backups" gesetzt und kann über den Konfigurations Parameter "BackupDirectory" jederzeit verändert
	 *  werden. Das Backup des jeweiligen Update Vorgangs wird dann in einem Folder IPSLibrary_YYYY-MM-DD_HH:MI abgelegt.
	 *
	 * @}*/

	/**@defgroup ipsmodulemanager_configuration IPSModuleManager Konfiguration
	 * @ingroup ipsmodulemanager
	 * @{
	 * Alle Konfigurations Einstellung, die für Installation von Modulen benötigt werden, sind in Initialisierungs Files abgelegt. Ablageort
	 * für diese Files ist "IPSLibray.install.InitializationFiles", die Files werden beim initialen Download des Modules aus den Files im "Default"
	 * Verzeichnis generiert und bei späteren Updates nicht mehr verändert.
	 * Bei Problemen kann das File wieder mit der Version im Default Verzeichnis repariert werden. Im Verzeichnis "Examples" finden sich noch weitere
	 * Files, die Beispiele für die jeweilige Konfiguration beinhalten.
	 *
	 * Eine kurze Beschreibung des jeweiligen Parameters, ist im jeweilen Initialisierungs File zu finden.
	 *
	 * Allgemeine Parameter sind im Konfigurations File des Modulemanagers abgelegt (Backup Directory, Logging Directory), spezielle Module Parameter
	 * sind dann im jeweiligen Ini File des Modules abgelegt.
	 *
	 * Teilweise werden Parameter auch in beiden Files gesucht. Zum Beispiel werden die diversen Parameter zur Installation des WebFronts (Enabled,
	 * Root, WFCId) zuerst im jeweiligen Module Ini File gesucht (zB IPSLogger.ini), ist es dort nicht definiert, wird im File IPSModuleManager.ini gesucht.
	 *
	 * Beispiel:
	 *  @code
         [WFC10]
         Enabled=true
         Path=Visualization.WebFront.Entertainment
         ID=
         TabName=
         TabIcon=Speaker
         TabOrder=20

         [Mobile]
         Enabled=true
         Path=Visualization.Mobile
         Name=Entertainment
         Order=20
         Icon=Speaker
	    @endcode
	 *
	 * [] markiert immer eine Gruppe von Einstellungen, In der Gruppe "WFC10” werden die Einstellungen gesucht, die für die WebFront
	 * Installation benötigt werden. (WFC10 - WebFront mit 10 Zoll Optimierung). Analog gibt es eine Gruppe "Mobile", die für das Mobile
	 * Interface (iPhone, iPad und Android) verwendet wird.
	 *
	 * Beschreibung der wichtigsten Parameter:
	 * - "Enabled" definiert, ob das jeweilige Interface installiert wird
	 * - "Path" bestimmt den Installations Pfad in IP-Symcon
	 * - "ID" bezeichnet die ID des Webfront Konfigurators der verwendet werden soll, wenn nichts angegeben wird, verwendet die Installations
	 *   Prozedure den erst Besten der gefunden wird.
	 * - "TabName" definiert Namen im SplitPane des WebFronts
	 * - "TabIcon" definiert Icon im SplitPane des WebFronts
	 * - "TabOrder" definiert Position im SplitPane des WebFronts
	 * - "Name" für Mobile Frontend Installation
	 * - "Order" Position Mobile Frontend
	 * - "Icon" Icon für Mobile Frontend
	 *
	 * @}*/

	 /**@defgroup ipsmodulemanager_installation IPSModuleManager Installation
	 * @ingroup ipsmodulemanager
	 * @{
	 *
	 * Installations Script für IPSModuleManager
	 *
	 * @file          IPSModuleManager_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 31.01.2012<br/>
	 *
	 * @page requirements_modulemanager Installations Voraussetzungen IPSModuleManager
	 * - IPS Kernel >= 2.50
	 *
	 * @page install_modulemanager Installations Schritte
	 * Der IPSModuleManager wird bereits bei der Basis Installation angelegt (Siehe BaseInstallation im Forum).
	 *
	 */

	return; 

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager';
		$moduleManager = new IPSModuleManager('IPSModuleManager');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');

	/** @}*/
?>