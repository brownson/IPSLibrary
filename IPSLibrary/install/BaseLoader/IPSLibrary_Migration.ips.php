<?
	IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

	//Migrate_Structure('IPSLogger',        'Program.IPSLogger',    'Program.IPSLibrary.app.core.IPSLogger',       'IPSLibrary\\app\\core\\IPSLogger\\');
	//Migrate_Structure('NetPlayer',        'Program.NetPlayer',    'Program.IPSLibrary.app.modules.NetPlayer',    'IPSLibrary\\app\\modules\\NetPlayer\\');
	//Migrate_Structure('Entertainment',    'Program.Entertainment','Program.IPSLibrary.app.modules.Entertainment','IPSLibrary\\app\\modules\\Entertainment\\');
	//Migrate_Structure('IPSWatering',      'Program.IPSWatering',  'Program.IPSLibrary.app.modules.IPSWatering',  'IPSLibrary\\app\\modules\\NetPlayer\\');
	//Migrate_Structure('IPSShadowing',     'Program.IPSShadowing', 'Program.IPSLibrary.app.modules.IPSShadowing', 'IPSLibrary\\app\\modules\\IPSShadowing\\');
	//Migrate_Structure('IPSLight',         'Program.IPSLight',     'Program.IPSLibrary.app.modules.IPSLight',     'IPSLibrary\\app\\modules\\IPSLight\\');
	//Migrate_Structure('IPSEDIP',          'Program.IPSEDIP',      'Program.IPSLibrary.app.hardware.IPSEDIP',     'IPSLibrary\\app\\hardware\\IPSEDIP\\');

	// ---------------------------------------------------------------------------------------------------------------------
	function Migrate_Structure($module, $oldPath, $newPath, $filePath) {
		$oldCategoryId = @get_ObjectIDByPath($oldPath, true);
		if ($oldCategoryId==null) {
			echo "$oldPath not found ...\n";
			return;
		}
		// Download new Module Scripts
		$moduleManager = new IPSModuleManager($module);
		//$moduleManager->DeployModule();
		$moduleManager->LoadModule();

		// Migrate existing Structure
		CreateCategoryPath($newPath);
		$newCategoryPath = str_replace('.app.', '.data.', $newPath);
		$newCategoryId   = CreateCategoryPath($newCategoryPath);
		foreach (IPS_GetChildrenIDs($oldCategoryId) as $idx=>$childId) {
			$object = IPS_GetObject($childId);
			switch($object['ObjectType']) {
				case 0: // Category
				case 1: // Instance
					echo "Migrate Category=$childId, Name=".IPS_GetName($childId)."\n";
					Migrate_Category($childId, $filePath);
					IPS_SetParent($childId, $newCategoryId);
					break;
				case 2: // Variable
					echo "Migrate Variable=$childId, Name=".IPS_GetName($childId)."\n";
					IPS_SetParent($childId, $newCategoryId);
					break;
				case 3: // Script
					Delete_Script($childId, $filePath);
					break;
				default:
					echo "Unknown Object=$childId, Name=".IPS_GetName($childId)."\n";
			}
			echo "Move $childId from $oldCategoryId to $newCategoryId ($newCategoryPath)\n";
		}
		if (!IPS_DeleteCategory($oldCategoryId)) {
			echo "Error deleting old Category ".IPS_GetName($oldCategoryId)."\n";
			exit;
		}
		echo '-------------------------------------------------------------------------------------------------------------'.PHP_EOL;
		echo '---- Bestehende Variablen von Modul '.$module.' wurden in die neue Strukture migriert'.PHP_EOL;
		echo '----'.PHP_EOL;
		echo '---- Anpassung der Konfiguration und erneute Installation muss manuell gemacht werden !!!'.PHP_EOL;
		echo '---- ACHTUNG: Pfad für WebFront und Mobile Installation haben sich geändert (liegt jetzt unter "Visualization.WebFront"'.PHP_EOL;
		echo '----          bzw. "Visualization.Mobile". Pfad im Init File (/IPSLibrary/install/InitializationFiles/<<Module>>.ini'.PHP_EOL;
		echo '----          anpassen oder Links manuell verschieben.'.PHP_EOL;
		echo '----          Im Zweifel einfach die bestehenden Strukture des jeweiligen Modules löschen, und eine erneute Installation'.PHP_EOL;
		echo '----          Installation ausführen.'.PHP_EOL;
		echo '-------------------------------------------------------------------------------------------------------------'.PHP_EOL;
	}
	
	// ---------------------------------------------------------------------------------------------------------------------
	function Delete_Script($scriptId, $filePath) {
		$scriptName = IPS_GetName($scriptId);
		$object     = IPS_GetScript($scriptId);
		$scriptFile = $object['ScriptFile'];

		echo "Remove old Script File $scriptFile\n";
		if (!IPS_DeleteScript($scriptId, false)) {
			echo "Error set deleting Script $scriptId, File=$scriptFile\n";
			exit;
		}
		//if (!unlink(IPS_GetKernelDir().'\\scripts\\'.$scriptFile)) {
		//	echo "Error deleting old Script File '$scriptFile'\n";
		//	exit;
		//}
	}
	
	// ---------------------------------------------------------------------------------------------------------------------
	function Migrate_Category($categoryId, $filePath) {
		foreach (IPS_GetChildrenIDs($categoryId) as $idx=>$childId) {
			$object = IPS_GetObject($childId);
			switch($object['ObjectType']) {
				case 0: // Category
				case 1: // Instance
					Migrate_Category($childId, $filePath);
					break;
				case 3: // Script
					Delete_Script($childId, $filePath);
					break;
				default:
					// Ignore
			}
		}
	}



?>