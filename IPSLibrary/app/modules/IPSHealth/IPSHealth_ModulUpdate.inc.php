<?


		$remoteRepository = 'https://raw.github.com/MCS-51/IPSLibrary/Development/';
		$component = 'IPSHealth';

		IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");
		$moduleManager = new IPSModuleManager($component,$remoteRepository);
		$moduleManager->LoadModule();

		$moduleManager->InstallModule();


?>