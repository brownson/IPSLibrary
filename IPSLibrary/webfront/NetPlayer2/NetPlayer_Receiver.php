<?
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_Receiver.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Empfangs Script um Requests (JQuery) der HTML Seiten zu bearbeiten.
	 * Das Script wird durch die Java Script Funktionen im File NetPlayer_Sender.php getriggert.
	 *
	 */

	$id = $_GET['id'];
	IPSUtils_Include ("NetPlayer.inc.php", "IPSLibrary::app::modules::NetPlayer2");

	if ($id<>"rc_mp_current" and $id<>"rc_mp_status" and $id<>"rc_mp_length"  and $id<>"rc_mp_interpret" and $id<>"rc_mp_album" and $id<>"rc_mp_titel") {
		IPSLogger_Trc(__file__, "RECEIVED Command '$id'");
	}
	
	// Player Control
	if ($id=="rc_mp_play") {
		NetPlayer_Power(true);
	} else if ($id=="rc_mp_last") {
		NetPlayer_Prev();
	} else if ($id=="rc_mp_next") {
		NetPlayer_Next();
	} else if ($id=="rc_mp_pause") {
		NetPlayer_Pause();
	} else if ($id=="rc_mp_stop") {
		NetPlayer_Power(false);

	// Player State and Data
	} else if ($id=="rc_mp_titel") {
		$player =  NetPlayer_GetIPSComponentPlayer();
		$titel = $player->GetTrackName();
		//$titel = NetPlayer_GetMediaPlayerValue("Titel");
		$Interpret = GetValue(NP_ID_CDINTERPRET);
		$titel = str_replace(trim($Interpret, " "), "", $titel); 
		$titel = trim ($titel,"? -");
		$titel = trim ($titel," -");
		echo  htmlentities(html_entity_decode($titel), ENT_COMPAT, 'ISO-8859-1');
	} else if ($id=="rc_mp_current") {
		echo NetPlayer_GetIPSComponentPlayer()->GetTrackPosition();
		//echo NetPlayer_GetMediaPlayerValue('Titelposition'); 
	} else if ($id=="rc_mp_status") {
		echo GetValue(NP_ID_CONTROL);
		//echo NetPlayer_GetMediaPlayerValue('Status'); 
	} else if ($id=="rc_mp_length") {
		echo NetPlayer_GetIPSComponentPlayer()->GetTrackLength();
		//echo NetPlayer_GetMediaPlayerValue('Titellänge'); 
	} else if ($id=="rc_mp_interpret") {
		echo htmlentities(GetValue(NP_ID_CDINTERPRET), ENT_COMPAT, 'ISO-8859-1'); 
	} else if ($id=="rc_mp_album") {
		echo htmlentities(GetValue(NP_ID_CDALBUM), ENT_COMPAT, 'ISO-8859-1'); 

	// Mediaplayer Source
	} else if ($id=="rc_mp_player") {
		NetPlayer_SwitchToMP3Player();
	} else if ($id=="rc_mp_select") {
		NetPlayer_SwitchToMP3Selection();
	} else if ($id=="rc_mp_radio") {
		NetPlayer_SwitchToRadio();

	// CD Selection - List CDs in MP3 Repository
	} else if (substr($id,0,5)=="rc_cd") {
		NetPlayer_PlayDirectory($_GET['cd_path']);
	} else if ($id=="rc_mp_cdselectprev") {
		NetPlayer_NavigateCDBack(NP_COUNT_CDHTML);
	} else if ($id=="rc_mp_cdselectnext") {
		NetPlayer_NavigateCDForward(NP_COUNT_CDHTML);
	} else if (substr($id,0,6)=="rc_cat") {
		NetPlayer_SetCategory($_GET['cd_cat']);
	} else if ($id=="rc_mp_cdselectroot") {
		NetPlayer_SetCategory('');
	} else if ($id=="rc_mp_cdselectback") {
		NetPlayer_SwitchToMP3Player(false);
	} else if (substr($id,0,11)=="rc_mp_track") {
		NetPlayer_SetPlayListPosition((int)$_GET['track']);
		
	// Mediaplayer Set Webradio Station
	} else if (substr($id,0,11)=="rc_mp_radio") {
		NetPlayer_PlayRadio($_GET['radiourl'], $_GET['radiotitel']);
	} else if ($id=="rc_mp_radioselectprev") {
		NetPlayer_NavigateRadioBack(NP_COUNT_RADIOHTML);
	} else if ($id=="rc_mp_radioselectnext") {
		NetPlayer_NavigateRadioForward(NP_COUNT_RADIOHTML);

	} else {
		IPSLogger_Err(__file__, "Received Unknown Netplayer ObjID=".$id);
    }
    ;
	/** @}*/
?>