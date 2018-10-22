<?
	/**@defgroup netplayer_installation NetPlayer Installation
	 * @ingroup netplayer
	 * @{
	 *
	 * NetPlayer Installation
	 *
	 * @file          NetPlayer_Installation.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 31.01.2012<br/>
	 *
	 * NetPlayer Installation
	 *
	 * Script zur kompletten Installation des NetPlayers.
	 *
	 * @page requirements_netplayer Installations Voraussetzungen
	 * - IPS Kernel >= 2.50
	 * - IPSModuleManager >= 2.5.1
	 * - IPSLogger >= 2.5.1
	 *
	 * @page install_netplayer Installations Schritte
	 * Folgende Schritte sind zur Installation des NetPlayers nötig:
	 * - Laden des Modules (siehe IPSModuleManager)
	 * - Konfiguration (Details siehe Konfiguration)
	 * - Installation (siehe IPSModuleManager)
	 *
	 */

	if (!isset($moduleManager)) {
		IPSUtils_Include ('IPSModuleManager.class.php', 'IPSLibrary::install::IPSModuleManager');

		echo 'ModuleManager Variable not set --> Create "default" ModuleManager'.PHP_EOL;
		$moduleManager = new IPSModuleManager('NetPlayer');
	}

	$moduleManager->VersionHandler()->CheckModuleVersion('IPS','2.50');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSLogger','2.50.1');
	$moduleManager->VersionHandler()->CheckModuleVersion('IPSModuleManager','2.50.1');

	IPSUtils_Include ("IPSInstaller.inc.php",            "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSMessageHandler.class.php",     "IPSLibrary::app::core::IPSMessageHandler");
	IPSUtils_Include ("NetPlayer_Constants.inc.php",     "IPSLibrary::app::modules::NetPlayer");
	IPSUtils_Include ("NetPlayer_Configuration.inc.php", "IPSLibrary::config::modules::NetPlayer");


	// ----------------------------------------------------------------------------------------------------------------------------
	// Program Installation
	// ----------------------------------------------------------------------------------------------------------------------------

	echo "--- Create NetPlayer -------------------------------------------------------------------\n";
	$CategoryIdData     = $moduleManager->GetModuleCategoryID('data');
	$CategoryIdApp      = $moduleManager->GetModuleCategoryID('app');
	$CategoryIdHw       = CreateCategoryPath('Hardware.NetPlayer');

	// Scripts
	$actionScriptId = IPS_GetScriptIDByName('NetPlayer_ActionScript',  $CategoryIdApp);
	$eventScriptId  = IPS_GetScriptIDByName('NetPlayer_EventScript',   $CategoryIdApp);

	// Controls
	CreateProfile_Associations ('NetPlayer_Category',    array('Root'));
	CreateProfile_Associations ('NetPlayer_CDAlbumList', array('...'));
	CreateProfile_Associations ('NetPlayer_CDAlbumNav',  array('<<','>>'));
	CreateProfile_Associations ('NetPlayer_CDTrackList', array('...'));
	CreateProfile_Associations ('NetPlayer_CDTrackList2', array('xx', 'yyy'));
	CreateProfile_Associations ('NetPlayer_CDTrackNav',  array('<<','>>'));
	CreateProfile_Associations ('NetPlayer_RadioList',   array('...'));
	CreateProfile_Associations ('NetPlayer_RadioNav',    array('<<','>>'));
	CreateProfile_Associations ('NetPlayer_Control',     array('Play','Pause','Stop','<<','>>'));
	CreateProfile_Associations ('NetPlayer_Source',      array('CD Player','Radio Player'));

	// MP3 Player
	$mp3PlayerInstanceId     = CreateInstance("CDPlayer", $CategoryIdData, "{485D0419-BE97-4548-AA9C-C083EB82E61E}",1000);
	$categoryId       = CreateVariable("Category",        1 /*Integer*/,  $CategoryIdData, 150 , 'NetPlayer_Category', $actionScriptId, 0);
	$cdCategoryNameId = CreateVariable("CategoryName",    3 /*String*/,  $mp3PlayerInstanceId, 10 , '~TextBox', null/*NoAS*/, "");
	$cdIdxId          = CreateVariable("DirectoryIdx",    1 /*Integer*/, $mp3PlayerInstanceId, 20 , '',         null/*NoAS*/,  0);
	$cddirectoryPath  = CreateVariable("DirectoryPath",   3 /*String*/,  $mp3PlayerInstanceId, 30 , '~TextBox');
	$cddirectoryName  = CreateVariable("DirectoryName",   3 /*String*/,  $mp3PlayerInstanceId, 40 , '~TextBox');
	$cdTrackListHtmlId= CreateVariable("TrackListHtml",   3 /*String*/,  $mp3PlayerInstanceId, 50 , '~HTMLBox');
	$cdTrackIdxId     = CreateVariable("TrackIdx",        1 /*Integer*/, $mp3PlayerInstanceId, 60 , '',         null/*NoAS*/, 0);

	// WebRadio
	$webRadioInstanceId     = CreateInstance("RadioPlayer", $CategoryIdData, "{485D0419-BE97-4548-AA9C-C083EB82E61E}",1010);
	$radioNameId     = CreateVariable("Name", 3 /*String*/,   $webRadioInstanceId, 10 , '~TextBox');
	$radioUrlId      = CreateVariable("Url",  3 /*String*/,   $webRadioInstanceId, 20 , '~TextBox');
	$radioIdxId      = CreateVariable("Idx",  1 /*Integer*/,  $webRadioInstanceId, 30 , '', null/*NoAS*/, 0);

	$powerId               = CreateVariable("Power",           0 /*Boolean*/,  $CategoryIdData, 100 , '~Switch', $actionScriptId, 0);
	$sourceId              = CreateVariable("Source",          1 /*Integer*/,  $CategoryIdData, 110 , 'NetPlayer_Source', $actionScriptId, 0 /*CD*/);
	$controlId             = CreateVariable("Control",         1 /*Integer*/,  $CategoryIdData, 120 , 'NetPlayer_Control', $actionScriptId, 2 /*Stop*/);
	$albumId               = CreateVariable("Album",           3 /*String*/,   $CategoryIdData, 130, '');
	$interpretId           = CreateVariable("Interpret",       3 /*String*/,   $CategoryIdData, 140, '');
	$categoryId            = CreateVariable("Category",        1 /*Integer*/,  $CategoryIdData, 150 , 'NetPlayer_Category', $actionScriptId, 0);
	$cdAlbumNavId          = CreateVariable("CDAlbumNav",      1 /*Integer*/,  $CategoryIdData, 160 , 'NetPlayer_CDAlbumNav', $actionScriptId, -1);
	$cdAlbumListId         = CreateVariable("CDAlbumList",     1 /*Integer*/,  $CategoryIdData, 170 , 'NetPlayer_CDAlbumList', $actionScriptId, -1);
	$cdTrackNavId          = CreateVariable("CDTrackNav",      1 /*Integer*/,  $CategoryIdData, 180 , 'NetPlayer_CDTrackNav', $actionScriptId, -1);
	$cdTrackListId         = CreateVariable("CDTrackList",     1 /*Integer*/,  $CategoryIdData, 190 , 'NetPlayer_CDTrackList', $actionScriptId, -1);
	$radioNavId            = CreateVariable("RadioNav",        1 /*Integer*/,  $CategoryIdData, 200 , 'NetPlayer_RadioNav', $actionScriptId, -1);
	$radioListId           = CreateVariable("RadioList",       1 /*Integer*/,  $CategoryIdData, 210 , 'NetPlayer_RadioList', $actionScriptId,-1);
	$controlTypeId         = CreateVariable("ControlType",     1 /*Integer*/,  $CategoryIdData, 300 , '', null, 0);
	$remoteControlId       = CreateVariable("RemoteControl",   3 /*String*/,   $CategoryIdData, 310 , '~HTMLBox', null, '<iframe frameborder="0" width="100%" src="../user/NetPlayer/NetPlayer_MP3Control.php" height=255px </iframe>');
	$mobileControlId       = CreateVariable("MobileControl",   3 /*String*/,   $CategoryIdData, 320 , '~HTMLBox', null, '<iframe frameborder="0" width="100%" src="../user/NetPlayer/NetPlayer_Mobile.php" height=1000px </iframe>');


	// Register Variable Constants
	SetVariableConstant ("NP_ID_CDCATEGORYNAME",  $cdCategoryNameId,       'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDDIRECTORYPATH", $cddirectoryPath,        'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDDIRECTORYNAME", $cddirectoryName,        'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDDIRECTORYIDX",  $cdIdxId,                'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDTRACKLISTHTML", $cdTrackListHtmlId,      'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDTRACKIDX",      $cdTrackIdxId,           'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');

	SetVariableConstant ("NP_ID_RADIONAME",       $radioNameId,            'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_RADIOURL",        $radioUrlId,             'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_RADIOIDX",        $radioIdxId,             'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');

	SetVariableConstant ("NP_ID_POWER",           $powerId,                'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_REMOTECONTROL",   $remoteControlId,        'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_MOBILECONTROL",   $mobileControlId,        'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CONTROLTYPE",     $controlTypeId,          'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ('"NP_ID_CDALBUM"',       $albumId,                'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDINTERPRET",     $interpretId,            'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');

	SetVariableConstant ("NP_ID_CATEGORYLIST",    $categoryId,             'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDALBUMLIST",     $cdAlbumListId,          'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDALBUMNAV",      $cdAlbumNavId,           'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ('"NP_ID_CDTRACKLIST"',   $cdTrackListId,          'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_CDTRACKNAV",      $cdTrackNavId,           'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_RADIOLIST",       $radioListId,            'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_RADIONAV",        $radioNavId,             'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ("NP_ID_SOURCE",          $sourceId,               'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');
	SetVariableConstant ('"NP_ID_CONTROL"',       $controlId,              'NetPlayer_IDs.inc.php', 'IPSLibrary::app::modules::NetPlayer');

	// Installation of Components
	IPSUtils_Include ("NetPlayer_Constants.inc.php",     "IPSLibrary::app::modules::NetPlayer");
	IPSUtils_Include ("NetPlayer_Configuration.inc.php", "IPSLibrary::config::modules::NetPlayer");

	$params = explode(',',NETPLAYER_COMPONENT);
	if ($params[0] == 'IPSComponentPlayer_Mediaplayer') {
	   if (!is_numeric($params[1])) {
	      $pathItems = explode('.',$params[1]);
	      $mediaPlayerName = $pathItems[count($pathItems)-1];
	      unset($pathItems[count($pathItems)-1]);
	      $path = implode('.', $pathItems);
			$categoryId  = CreateCategoryPath($path);

			// Create MediaPlayer
   		$mediaPlayerInstanceId   = CreateMediaPlayer($mediaPlayerName, $categoryId, 0);
   		$mediaPlayerTitel        = IPS_GetVariableIDByName('Titel', $mediaPlayerInstanceId);

   		// Register Message Handler
			IPSMessageHandler::RegisterOnChangeEvent($mediaPlayerTitel/*Var*/, 'IPSComponentPlayer_MediaPlayer,'.$mediaPlayerInstanceId, 'IPSModulePlayer_NetPlayer');
	   }
	}


  /** @}*/
?>