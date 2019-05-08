<?
	/**@addtogroup netplayer 
	 * @{
	 *
	 * @file          NetPlayer_LoadFiles.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Laden eines Verzeichnisses in den Player
	 */
	 
	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_CopyCoverFile ($path) {
		$files = scandir($path);
		$cover_jpg = "";
		$cover_front = "";
		foreach ($files as $file) {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			$filename = pathinfo($file, PATHINFO_FILENAME);
			if (strtolower($extension)=='jpg') {
				$cover_jpg = $path."/".$file;
				if (strpos(strtolower($filename),'front')!==false) {
					$cover_front = $path."/".$file; 
				}
			}
		}

		$FileOutput = IPS_GetKernelDir()."/webfront/user/NetPlayer/NetPlayer_Cover.jpg";
		if ($cover_front != "") {
			$FileInput = $cover_front;
		} else if ($cover_jpg != "") {
			$FileInput = $cover_jpg;
		} else {
			$FileInput = IPS_GetKernelDir()."/webfront/user/NetPlayer/NetPlayer_CoverNotFound.jpg";
		}
		IPSLogger_Dbg(__file__, "Copy Cover File '$FileInput' to '$FileOutput'");
		copy ($FileInput, $FileOutput);

	}


	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_GetPlayList($path, &$playlist) {
		$content = scandir($path);
		$maindata = array_diff($content, Array(".",".."));
		$allowed = Array("mp3", "wma");

		foreach($maindata as $d)  {
			$actpath = $path . "/" . $d;
			if (is_dir($actpath)) {
			} else {
				$ext = pathinfo($actpath, PATHINFO_EXTENSION);
				if (in_array(strtolower($ext), $allowed)) {
					$playlist[] = $actpath;
				}
			}
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_LoadFiles ($cd_path) {
		//Get Directory to Load
		SetValue(NP_ID_CDDIRECTORYPATH, $cd_path);

		// Get PlayList
		$playlist = array();
		NetPlayer_GetPlayList ($cd_path, $playlist);
		NetPlayer_CopyCoverFile ($cd_path);

		// Leeren der vorher bestehenden Playlist:
		$player = NetPlayer_GetIPSComponentPlayer();
		$player->ClearPlaylist();
		$tracklist = "";

		// Durchlaufen des Playlist-Arrays und anhängen an die Mediaplayer-Instanz-Playlist
		$idx = 1;
		foreach($playlist as $data)
		{
			IPSLogger_Dbg(__file__, "Add File=".$data);
			$player->AddPlaylist($data);
			$tracklist.='<tr><td><div id="rc_mp_track'.$idx.'" track="'.$idx.'" class="containerControlTrack">'.convert(basename($data))."</div></td></tr>";
			$idx++;
		}
		SetValue(NP_ID_CDTRACKLISTHTML, $tracklist);
		$player->Play();

		$Directory = basename($cd_path);
		SetValue(NP_ID_CDINTERPRET, substr($Directory,0,strpos($Directory, "[")));
		SetValue(NP_ID_CDALBUM, substr($Directory,strpos($Directory, "[")+1, strpos($Directory, "]")-strpos($Directory, "[")-1));
   }

  /** @}*/
 ?>