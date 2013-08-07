<?
	/**@defgroup netplayer NetPlayer
	 * @ingroup modules
	 * @{
	 *
	 * Der NetPlayer ermöglicht das Abspielen von MP3 Files und das Streamen von WebRadios.
	 *
	 * Der Player bietet zur Auswahl von MP3s die Möglichkeit zur Auflistung von Verzeichnissen. Nach Auswahl
	 * eines Verzeichnisses werden die entsprechenden Lieder abgespielt.
	 *
	 * Konfiguration des Musik Verzeichnisses und der WebRadio Sender, muss im File NetPlayer_Configuration.ips.php
	 * vorgenommen werden.
	 *
	 *
   /** @}*/


	/**@defgroup netplayer_visu NetPlayer Visualisierung
	 * @ingroup netplayer
	 * @{
	 *
	 * @page visu_netplayer_WebFront NetPlayer WebFront
	 *
	 *  Der NetPlayer bietet für die Visualisierung im WebFront Variablen mit denen eine komplette Steuerung des Players
	 *  möglich ist. Diese Variablen können auch verwendet werden um den Player zum Beispiel über ein EDIP Display zu steuern.
	 *
	 *  Variablen zur Steuerung des NetPlayers
	 *  @image html NetPlayer_WebFront.png
	 *
	 * @page visu_netplayer_HTML NetPlayer HTML
	 *
	 *  Der NetPlayer bietet für die Visualisierung im WebFront auch eine HTML Variable mit dem Namen "RemoteControl". Diese
	 *  Variable kann über einen Link in das eigene Webfront eingebunden werden und erlaubt es MP3 Files abzuspielen, eine
	 *  CD aus einem Verzeichnis auszuwählen und Web Radios abzuspielen.
	 *
	 *  Abspielen von MP3 Files
	 *  @image html NetPlayer_HTMLPlayer.png
	 *
	 *  Auswahl eines MP3 Verzeichnisses
	 *  @image html NetPlayer_HTMLSelection.png
	 *
	 *  Abspielen von Web Radios
	 *  @image html NetPlayer_HTMLRadio.png
	 *
	 * @page visu_netplayer_mobile NetPlayer Mobile
	 *
	 *  Der NetPlayer bietet für die Visualisierung im Mobile Frontend von IPS eine HTML Variable mit dem Namen "MobileControl". Diese
	 *  Variable kann über einen Link in das eigene Interface eingebunden werden und erlaubt es MP3 Files abzuspielen, eine
	 *  CD aus einem Verzeichnis auszuwählen und Web Radios abzuspielen.
	 *
	 *  Abspielen von MP3 Files
	 *  @image html NetPlayer_MobilePlayer.png "Player" width=5cm
	 *
	 *  Auswahl eines MP3 Verzeichnisses
	 *  @image html NetPlayer_MobileSelection.png
	 *
	 *  Abspielen von Web Radios
	 *  @image html NetPlayer_MobileRadio.png
	 *
	 */
   /** @}*/

	/**@defgroup netplayer_api NetPlayer API
	 * @ingroup netplayer
	 * @{
	 *
	 * NetPlayer API
	 *
	 * @file          NetPlayer.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * NetPlayer API
	 *
	 * Dieses File kann von anderen Scripts per INCLUDE eingebunden werden und enthält Funktionen
	 * um alle Funktionen des NetPlayers bequem per Funktionsaufruf steueren zu können.
	 *
	 */
	 
	IPSUtils_Include ("IPSLogger.inc.php",      "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSComponent.class.php", "IPSLibrary::app::core::IPSComponent");
	include_once "NetPlayer_Constants.inc.php";
	IPSUtils_Include ("NetPlayer_Configuration.inc.php", "IPSLibrary::config::modules::NetPlayer2");
	include_once "NetPlayer_Utils.inc.php";
	include_once "NetPlayer_ListFiles.inc.php";
	include_once "NetPlayer_LoadFiles.inc.php";


	function NetPlayer_GetIPSComponentPlayer() {
		$player = IPSComponent::CreateObjectByParams(NETPLAYER_COMPONENT);
	   return $player ;
	}

	function NetPlayer_Switch() {
		if (GetValue(NP_ID_CONTROLTYPE)==2) {
			NetPlayer_SwitchToMP3Player();
		} else {
			NetPlayer_SwitchToRadio();
		}
	}

	function NetPlayer_SwitchToMP3Player($loadDirectory=true,$forceLoad=false) {
		if (GetValue(NP_ID_CONTROLTYPE)==0 and !$forceLoad) return;
		if ($loadDirectory) {
			$directory = GetValue(NP_ID_CDDIRECTORYPATH);
			IPSLogger_Inf(__file__, "Play MP3 '$directory'");
			NetPlayer_Power(true);
			NetPlayer_LoadFiles ($directory);
			SetValue(NP_ID_CDTRACKIDX, 0);
			NetPlayer_RefreshTrackListProfile();
			NetPlayer_RefreshTrackListValue();
		}
		SetValue(NP_ID_SOURCE, NP_IDX_SOURCECD);
		SetValue(NP_ID_CONTROLTYPE, 0);
		SetValue(NP_ID_REMOTECONTROL,     '<iframe frameborder="0" width="100%" '.NP_RC_MP3CONTROL.'</iframe>');
		SetValue(NP_ID_MOBILECONTROL,     '<iframe frameborder="0" width="100%" '.NP_RC_MOBILE.'</iframe>');
	}

	function NetPlayer_SwitchToMP3Selection() {
		if (GetValue(NP_ID_CONTROLTYPE)==1) return;
		SetValue(NP_ID_CONTROLTYPE, 1);
		SetValue(NP_ID_REMOTECONTROL,     '<iframe frameborder="0" width="100%" '.NP_RC_MP3SELECTION.'</iframe>');
		SetValue(NP_ID_MOBILECONTROL,     '<iframe frameborder="0" width="100%" '.NP_RC_MOBILE.'</iframe>');
	}

	function NetPlayer_SwitchToRadio($forceLoad=false) {
		if (GetValue(NP_ID_CONTROLTYPE)==2 and !$forceLoad) return;
		$radiourl   = GetValue(NP_ID_RADIOURL);
		$radiotitel = GetValue(NP_ID_RADIONAME);
		IPSLogger_Inf(__file__, "Play RadioStation '$radiotitel', Url='$radiourl'");
		NetPlayer_Power(true);
		$player = NetPlayer_GetIPSComponentPlayer();
		$player->ClearPlaylist();
		$player->AddPlaylist($radiourl);
		$player->Play();
		SetValue(NP_ID_SOURCE, NP_IDX_SOURCERADIO);

		SetValue(NP_ID_CDINTERPRET, '');
		SetValue(NP_ID_CDALBUM,     $radiotitel);

		SetValue(NP_ID_CONTROLTYPE, 2);
		SetValue(NP_ID_REMOTECONTROL,     '<iframe frameborder="0" width="100%" '.NP_RC_RADIOCONTROL.'</iframe>');
		SetValue(NP_ID_MOBILECONTROL,     '<iframe frameborder="0" width="100%" '.NP_RC_MOBILE.'</iframe>');
	}

	function NetPlayer_RefreshRemoteControl() {
	   SetValue(NP_ID_REMOTECONTROL, GetValue(NP_ID_REMOTECONTROL));
	}

	function NetPlayer_NavigateTrackForward($count=10) {
		IPSLogger_Trc(__file__, "Navigate Forward Track, Count=$count");
		NetPlayer_NavigateTrack($count-1);
		NetPlayer_RefreshTrackListProfile();
		NetPlayer_RefreshTrackListValue();
	}

	function NetPlayer_NavigateTrackBack($count=10) {
		IPSLogger_Trc(__file__, "Navigate Back Track, Count=$count");
		NetPlayer_NavigateTrack($count*-1+1);
		NetPlayer_RefreshTrackListProfile();
		NetPlayer_RefreshTrackListValue();
	}

	function NetPlayer_NavigateCDForward($count=10) {
		IPSLogger_Trc(__file__, "Navigate Forward CD, Count=$count");
		NetPlayer_NavigateCD($count-1);
		NetPlayer_RefreshCategoryProfile();
		NetPlayer_RefreshCDListProfile();
		NetPlayer_RefreshCDListValue();
		NetPlayer_SwitchToMP3Selection();
		NetPlayer_RefreshRemoteControl();
	}

	function NetPlayer_NavigateCDBack($count=10) {
		IPSLogger_Trc(__file__, "Navigate Back CD, Count=$count");
		NetPlayer_NavigateCD($count*-1+1);
		NetPlayer_RefreshCategoryProfile();
		NetPlayer_RefreshCDListProfile();
		NetPlayer_RefreshCDListValue();
		NetPlayer_SwitchToMP3Selection();
		NetPlayer_RefreshRemoteControl();
	}

	function NetPlayer_SetCategory($category) {
		IPSLogger_Trc(__file__, "Set NetPlayer Category '$category'");
		SetValue(NP_ID_CDCATEGORYNAME, $category);
		SetValue(NP_ID_CDDIRECTORYIDX, 0);
		NetPlayer_RefreshCategoryProfile();
		NetPlayer_RefreshCDListProfile();
		NetPlayer_RefreshCDListValue();
		NetPlayer_SwitchToMP3Selection();
		NetPlayer_NavigateCDBack();
	}

	function NetPlayer_PlayDirectory($directory) {
		IPSLogger_Inf(__file__, "Load MusicFiles from Directory '$directory'");
		SetValue(NP_ID_CDDIRECTORYPATH,$directory);
		SetValue(NP_ID_CDDIRECTORYNAME, pathinfo($directory, PATHINFO_FILENAME));
		NetPlayer_SwitchToMP3Player(true, true);
		NetPlayer_RefreshCDListValue();
	}

	function NetPlayer_NavigateRadioForward($count=NP_COUNT_RADIODEFAULT) {
		IPSLogger_Trc(__file__, "Navigate Forward Radio, Count=$count");
		NetPlayer_NavigateRadio($count-1);
		NetPlayer_RefreshRadioListProfile();
		NetPlayer_RefreshRadioListValue();
		NetPlayer_SwitchToRadio();
		NetPlayer_RefreshRemoteControl();
	}

	function NetPlayer_NavigateRadioBack($count=NP_COUNT_RADIODEFAULT) {
		IPSLogger_Trc(__file__, "Navigate Back Radio, Count=$count");
		NetPlayer_NavigateRadio($count*-1+1);
		NetPlayer_RefreshRadioListProfile();
		NetPlayer_RefreshRadioListValue();
		NetPlayer_SwitchToRadio();
		NetPlayer_RefreshRemoteControl();
	}

	function NetPlayer_Pause() {
		NetPlayer_Power(true);
		SetValue(NP_ID_CONTROL, NP_IDX_CONTROLPAUSE);
		NetPlayer_GetIPSComponentPlayer()->Pause();
	}

	function NetPlayer_PlayRadio($radiourl, $radiotitel) {
		IPSLogger_Inf(__file__, "Play RadioStation '$radiotitel', Url='$radiourl'");
		SetValue(NP_ID_RADIOURL,    $radiourl);
		SetValue(NP_ID_RADIONAME,   $radiotitel);
		SetValue(NP_ID_CDINTERPRET, '');
		SetValue(NP_ID_CDALBUM,     $radiotitel);
		NetPlayer_Power(true);
		NetPlayer_SwitchToRadio();
		NetPlayer_RefreshRadioListValue();
		$player = NetPlayer_GetIPSComponentPlayer();
		$player->ClearPlaylist();
		$player->AddPlaylist($radiourl);
		$player->Play();
	}

	function NetPlayer_RadioByIndex($radioIdx=0) {
		$radioName = GetValue(NP_ID_RADIONAME);
		$radioList = NetPlayer_GetRadioList();
		$radioKeys = array_keys($radioList);
		if (!array_key_exists($radioIdx, $radioKeys))  {
			IPSLogger_Err(__file__, "Unbekannter Sender in der Radioliste ->  $radioIdx");
		} else {
			$radioName  = $radioKeys[$radioIdx];
			$radioUrl   = $radioList[$radioKeys[$radioIdx]];
			NetPlayer_PlayRadio($radioUrl, $radioName);
			NetPlayer_RefreshRemoteControl();
		}
	}  

	function NetPlayer_NextRadio($next=1) {
		$radioName = GetValue(NP_ID_RADIONAME);
		$radioList = NetPlayer_GetRadioList();
		$radioKeys = array_keys($radioList); // 0=>'OE3',...
		$radioFlip = array_flip($radioKeys); // 'OE3'=>0
		print_r($radioFlip);
		$radioIdx  = $radioFlip[$radioName]+$next;
		echo $radioIdx;
		if (!array_key_exists($radioIdx, $radioKeys)) {
			if ($next>0) {
				$radioIdx = 0;
	  		} else {
				$radioIdx = count($radioKeys)-1;
	  		}
		}
		$radioName  = $radioKeys[$radioIdx];
		$radioUrl   = $radioList[$radioName];
		NetPlayer_PlayRadio($radioUrl, $radioName);
		NetPlayer_RefreshRemoteControl();
	}

	function NetPlayer_Next() {
		NetPlayer_Power(true);
		$plaverState = GetValue(NP_ID_CONTROL);
		SetValue(NP_ID_CONTROL, NP_IDX_CONTROLNEXT);
		if (GetValue(NP_ID_CONTROLTYPE)==2) { // Radio
			NetPlayer_NextRadio(1);
		} else {
			NetPlayer_GetIPSComponentPlayer()->Next();
		}
		IPS_SLEEP(200);
		SetValue(NP_ID_CONTROL, $plaverState);
	}

	function NetPlayer_Prev() {
		NetPlayer_Power(true);
		$plaverState = GetValue(NP_ID_CONTROL);
		SetValue(NP_ID_CONTROL, NP_IDX_CONTROLPREV);
		if (GetValue(NP_ID_CONTROLTYPE)==2) { // Radio
			NetPlayer_NextRadio(-1);
		} else {
			NetPlayer_GetIPSComponentPlayer()->Prev();
		}
		IPS_SLEEP(200);
		SetValue(NP_ID_CONTROL, $plaverState);
	}

	function NetPlayer_SetPlayListPosition($position) {
		IPSLogger_Inf(__file__, "Set NetPlayer PlayListPosition=$position");
		NetPlayer_SwitchToMP3Player();
		NetPlayer_GetIPSComponentPlayer()->SetPlaylistPosition((int)$position);
	}

	function NetPlayer_SetSource($value) {
		if (GetValue(NP_ID_SOURCE)<>$value) {
			switch ($value) {
				case NP_IDX_SOURCECD:
					NetPlayer_SwitchToMP3Player(true, true);
					break;
				case NP_IDX_SOURCERADIO:
					NetPlayer_SwitchToRadio();
					break;
				default:
					IPSLogger_Err(__file__, "Unknown SourceValue '$value'");
					exit;
			}
	   }
	}

	function NetPlayer_Power($value) {
		// Set Power State
		if ($value != GetValue(NP_ID_POWER)) {
			SetValue(NP_ID_POWER, $value);
			NetPlayer_RefreshRemoteControl();
		}
		$plaverState = GetValue(NP_ID_CONTROL);
		
		// Set Player Control
		if ($value and $plaverState<>NP_IDX_CONTROLPLAY) {
			IPSLogger_Trc(__file__, 'Start Netplayer');
			SetValue(NP_ID_CONTROL, NP_IDX_CONTROLPLAY);
			$player = NetPlayer_GetIPSComponentPlayer(); 
			if (!$player->Play()) {
				if (GetValue(NP_ID_CONTROLTYPE)==2) {
					NetPlayer_SwitchToRadio(true); 
				} else {
					NetPlayer_SwitchToMP3Player(true, true);
				}
			}
		}
		if (!$value and $plaverState<>NP_IDX_CONTROLSTOP) {
			IPSLogger_Trc(__file__, 'Stop Netplayer');
			SetValue(NP_ID_CONTROL, NP_IDX_CONTROLSTOP);
			NetPlayer_GetIPSComponentPlayer()->Stop();
		}
	}

   /** @}*/

?>