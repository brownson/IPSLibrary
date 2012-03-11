<?
	$remoteRepository = 'https://github.com/brownson/IPSLibrary/tree/Development';
	$localRepository = IPS_GetKernelDir().'scripts\\';

	$fileList = array(
		'IPSLibrary\\install\\IPSInstaller\\IPSInstaller.inc.php',
		'IPSLibrary\\install\\IPSModuleManager\\IPSModuleManager.class.php',
		'IPSLibrary\\install\\IPSModuleManager\\IPSVersionHandler\\IPSVersionHandler.class.php',
		'IPSLibrary\\install\\IPSModuleManager\\IPSVersionHandler\\IPSVariableVersionHandler.class.php',
		'IPSLibrary\\install\\IPSModuleManager\\IPSScriptHandler\\IPSScriptHandler.class.php',
		'IPSLibrary\\install\\IPSModuleManager\\IPSFileHandler\\IPSFileHandler.class.php',
		'IPSLibrary\\install\\IPSModuleManager\\IPSLogHandler\\IPSLogHandler.class.php',
		'IPSLibrary\\install\\IPSModuleManager\\IPSBackupHandler\\IPSBackupHandler.class.php',
		'IPSLibrary\\app\\core\\IPSConfigHandler\\IPSConfigHandler.class.php',
		'IPSLibrary\\app\\core\\IPSConfigHandler\\IPSIniConfigHandler.class.php',
		'IPSLibrary\\app\\core\\IPSUtils\\IPSUtils.inc.php',
		'IPSLibrary\\install\\InstallationScripts\\IPSModuleManager_Installation.ips.php',
		'IPSLibrary\\install\\InitializationFiles\\Default\\IPSModuleManager.ini',
		'IPSLibrary\\install\\DownloadListFiles\\IPSModuleManager_FileList.ini',
	);

	// Download Files
	echo 'Download of ModuleManager'.PHP_EOL;
	foreach ($fileList as $file) {
		LoadFile($remoteRepository.$file, $localRepository.$file);
	}

	//Registration of IPSUtils in autoload.php
	Register_IPSUtils();

	// Installation of ModuleManager
	echo 'Installation of ModuleManager';
	include_once IPS_GetKernelDir().'scripts\\IPSLibrary\\app\\core\\IPSUtils\\IPSUtils.inc.php';
	IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');
	$moduleManager = new IPSModuleManager('IPSModuleManager');
   $moduleManager->LoadModule($remoteRepository, true);
   $moduleManager->InstallModule();

	// -------------------------------------------------------------------------------
	function LoadFile($sourceFile, $destinationFile) {
		if (strpos($sourceFile, 'https')===0) {
      	$sourceFile = str_replace('\\','/',$sourceFile);
			echo 'Load File '.$sourceFile."\n";
			$curl_handle=curl_init();
			curl_setopt($curl_handle, CURLOPT_URL,$sourceFile);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT,10);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_handle, CURLOPT_FAILONERROR, true);
			$fileContent = curl_exec($curl_handle);

			if ($fileContent===false) {
				throw new Exception('Download of File '.$sourceFile.' failed !!!');
			}
			curl_close($curl_handle);

	//		$fileContent = html_entity_decode($fileContent, ENT_COMPAT, 'UTF-8');
		} else {
		   $fileContent = file_get_contents($sourceFile);
		}

      $destinationFile = str_replace('/','\\',$destinationFile);
		$destinationFilePath = pathinfo($destinationFile, PATHINFO_DIRNAME);
		if (!file_exists($destinationFilePath)) {
			if (!mkdir($destinationFilePath, 0, true)) {
				throw new Exception('Create Directory '.$destinationFilePath.' failed!');
			}
		}
      $destinationFile = str_replace('\\InitializationFiles\\Default\\','\\InitializationFiles\\',$destinationFile);
	   if (!file_put_contents($destinationFile, $fileContent)) {
			sleep(1);
			echo 'Create File '.$destinationFile.' failed --> Retry ...';
			if (!file_put_contents($destinationFile, $fileContent)) {
				throw new Exception('Create File '.$destinationFile.' failed!');
		   }
	   }
	}

	// ------------------------------------------------------------------------------------------------
	function Register_IPSUtils() {
		$file = IPS_GetKernelDir().'scripts\\__autoload.php';

		if (!file_exists($file)) {
			file_put_contents($file, '<?'.PHP_EOL.PHP_EOL.'?>');
		}

		$FileContent = file_get_contents($file);
		$pos = strpos($FileContent, 'IPSUtils');

		if ($pos === false) {
			$includeCommand = '    include_once IPS_GetKernelDir()."\scripts\IPSLibrary\app\core\IPSUtils\IPSUtils.inc.php";';
			$FileContent = str_replace('?>', $includeCommand.PHP_EOL.'?>', $FileContent);
			file_put_contents($file, $FileContent);
		}
	}
?>