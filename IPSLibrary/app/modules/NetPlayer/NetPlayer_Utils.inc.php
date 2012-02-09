<?
	/**@addtogroup netplayer 
	 * @{
	 *
	 * @file          NetPlayer_Utils.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * NetPlayer Utility Funktionen
	 */

	// ---------------------------------------------------------------------------------------------------------------------------
	function convert($s) {
	   $source = array("&", "ä", "ö", "ü", "Ä", "Ö", "Ü", "ß", "<", ">", "€", "", "¹", "²", "³");
	   $dest = array("&amp;", "&auml;", "&ouml;", "&uuml;", "&Auml;", "&Ouml;", "&Uuml;", "&szlig;", "&lt;", "&gt;", "&euro;", "¹", "&#178", "³");
	   $s = str_replace($source, $dest, $s);
	   return $s;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function decode($s) {
	   $dest = array("&", "ä", "ö", "ü", "Ä", "Ö", "Ü", "ß", "<", ">", "€", "", "¹", "²", "³");
	   $source = array("&amp;", "&auml;", "&ouml;", "&uuml;", "&Auml;", "&Ouml;", "&Uuml;", "&szlig;", "&lt;", "&gt;", "&euro;", "¹", "&#178", "³");
	   $s = str_replace($source, $dest, $s);
	   return $s;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_NavigateRadio ($delta) {
		$maxIdx     = count(NetPlayer_GetRadioList());
		$currentIdx = GetValue(NP_ID_RADIOIDX);
		if ($currentIdx > $maxIdx - $delta) $currentIdx = $maxIdx - $delta;
		$currentIdx = $currentIdx + $delta;
		if ($currentIdx < 0) $currentIdx = 0;
		SetValue(NP_ID_RADIOIDX, $currentIdx);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_FilterRadioList ($radioList, $count) {
	   $resultList = array();
	   $maxIdx     = count($radioList);
	   $currentIdx = GetValue(NP_ID_RADIOIDX);
	   if ($currentIdx > $maxIdx - $count) $currentIdx = $maxIdx - $count;
	   if ($currentIdx < 0) $currentIdx = 0;
		for ($idx=0 ; $idx<$count; $idx++) {
		   if (array_key_exists($currentIdx + $idx, $radioList))
		     $resultList[] = $radioList[$currentIdx + $idx];
		}
		$idx      = 0;
		$radioIdx = 0;
		foreach ($radioList as $radioName=>$radioUrl) {
		   if ($idx>=$currentIdx and $idx<=$currentIdx+$count) {
		   	$resultList[$radioIdx] = $radioName;
		      $radioIdx++;
		   }
		   $idx++;
		}
		
		return $resultList;
	}
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_RefreshCategoryProfile() {
		$categoryList    = NetPlayer_GetCategoryList();
		$categoryCurr    = GetValue(NP_ID_CDCATEGORYNAME);
		$categoryCurrIdx = 0; /*Root*/
		$profileData     = IPS_GetVariableProfile('NetPlayer_Category');
		$associations    = $profileData['Associations'];
		foreach ($categoryList as $idx=>$category) {
			$profileIdx = $idx + 2;
			if ($categoryCurr==$category) $categoryCurrIdx = $profileIdx;
			$category = substr($category, 1);
			if ((array_key_exists($profileIdx, $associations) and $associations[$profileIdx]<>$category) or
			     !array_key_exists($profileIdx, $associations)) {
				IPS_SetVariableProfileAssociation('NetPlayer_Category', $profileIdx, $category, '', -1);
			}
		}
		foreach ($associations as $idx=>$name) {
			$categoryIdx = $idx - 2;
			if ($categoryIdx >= 0 and !array_key_exists($categoryIdx, $categoryList)) {
				IPS_SetVariableProfileAssociation('NetPlayer_Category', $idx, '', '', -1);
			}
		}
		if (GetValue(NP_ID_CATEGORYLIST) <> $categoryCurrIdx) {
			SetValue(NP_ID_CATEGORYLIST, $categoryCurrIdx);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_RefreshRadioListProfile() {
		$currentName   = GetValue(NP_ID_RADIONAME);
		$currentIdx    = GetValue(NP_ID_RADIOIDX);
		$radioList     = NetPlayer_FilterRadioList(NetPlayer_GetRadioList(), NP_COUNT_RADIOVARIABLE);
		$profileData   = IPS_GetVariableProfile('NetPlayer_RadioList');
		$associations  = $profileData['Associations'];
		foreach ($associations as $idx=>$data) {
			IPS_SetVariableProfileAssociation('NetPlayer_RadioList', $idx, '', '', -1);
		}
		foreach ($radioList as $idx=>$radio) {
			IPS_SetVariableProfileAssociation('NetPlayer_RadioList', $idx, $radio, '', -1);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_RefreshRadioListValue() {
		$currentIdx	   = -1;
		$currentName   = GetValue(NP_ID_RADIONAME);
		$profileData   = IPS_GetVariableProfile('NetPlayer_RadioList');
		$associations  = $profileData['Associations'];
		foreach ($associations as $idx=>$data) {
			if ($currentName == $data['Name']) {
			   $currentIdx = $idx;
			}
		}
		if (GetValue(NP_ID_RADIOLIST)<>$currentIdx ) {
		   SetValue(NP_ID_RADIOLIST,$currentIdx);
		}
	}

	
	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_RefreshCDListProfile() {
		$currentIdx    = GetValue(NP_ID_CDDIRECTORYIDX);
		$directoryList = NetPlayer_GetDirectoryList();
		$directoryList = NetPlayer_FilterDirectoryList($directoryList, NP_COUNT_CDVARIABLE);
		$profileData   = IPS_GetVariableProfile('NetPlayer_CDAlbumList');
		$associations  = $profileData['Associations'];
		foreach ($associations as $idx=>$data) {
			IPS_SetVariableProfileAssociation('NetPlayer_CDAlbumList', $idx, '', '', -1);
		}
		foreach ($directoryList as $idx=>$directory) {
			IPS_SetVariableProfileAssociation('NetPlayer_CDAlbumList', $idx, $directory, '', -1);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_RefreshCDListValue() {
		$currentIdx    = -1;
		$currentName   = GetValue(NP_ID_CDDIRECTORYNAME);
		$profileData   = IPS_GetVariableProfile('NetPlayer_CDAlbumList');
		$associations  = $profileData['Associations'];
		foreach ($associations as $idx=>$data) {
			if ($currentName == $data['Name']) {
			   $currentIdx = $idx;
			}
		}
		if (GetValue(NP_ID_CDALBUMLIST)<>$currentIdx ) {
		   SetValue(NP_ID_CDALBUMLIST,$currentIdx);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_GetTrackName($file) {
		$trackName   = pathinfo($file, PATHINFO_FILENAME);
		$trackName   = str_replace('.mp3', '', $trackName);
		$trackName   = str_replace('.wma', '', $trackName);
		if (substr($trackName,3,1)=='-' and substr($trackName,4,1)==' ') {
		   $trackName = substr($trackName,5);
		}

		return $trackName;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_GetTrackList() {
		$directory = GetValue(NP_ID_CDDIRECTORYPATH);
		$fileList = array();
		NetPlayer_GetPlayList($directory, $fileList);
		$trackList = array();
		foreach ($fileList as $idx=>$file) {
			$trackList[] = NetPlayer_GetTrackName($file);
		}

		return $trackList;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_FilterTrackList ($trackList, $count) {
	   $maxIdx     = count($trackList);
	   $currentIdx = GetValue(NP_ID_CDTRACKIDX);
	   if ($currentIdx > $maxIdx - $count) $currentIdx = $maxIdx - $count;
	   if ($currentIdx < 0) $currentIdx = 0;

//		for ($idx=0 ; $idx<$count; $idx++) {
//		   if (array_key_exists($currentIdx + $idx, $trackList))
//		     $trackList[] = $trackList[$currentIdx + $idx];
//		}

	   $resultList = array();
		$idx       = 0;
		foreach ($trackList as $trackIdx=>$trackName) {
		   if ($idx>=$currentIdx and $idx<=$currentIdx+$count) {
		   	$resultList[$trackIdx] = $trackName;
		   }
		   $idx++;
		}

		return $resultList;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_NavigateTrack($delta) {
		$maxIdx     = count(NetPlayer_GetTrackList());
		$currentIdx = GetValue(NP_ID_CDTRACKIDX);
		if ($currentIdx > $maxIdx - $delta) $currentIdx = $maxIdx - $delta;
		$currentIdx = $currentIdx + $delta;
		if ($currentIdx < 0) $currentIdx = 0;
		SetValue(NP_ID_CDTRACKIDX, $currentIdx);
	}
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_RefreshTrackListProfile() {
		$currentIdx    = GetValue(NP_ID_CDTRACKIDX);
		$trackList     = NetPlayer_FilterTrackList(NetPlayer_GetTrackList(), NP_COUNT_TRACKVARIABLE);
		$profileData   = IPS_GetVariableProfile('NetPlayer_CDTrackList');
		$associations  = $profileData['Associations'];
		foreach ($associations as $idx=>$data) {
			IPS_SetVariableProfileAssociation('NetPlayer_CDTrackList', $data['Value'], '', '', -1);
		}
		foreach ($trackList as $idx=>$track) {
			IPS_SetVariableProfileAssociation('NetPlayer_CDTrackList', $idx, $track, '', -1);
		}
	}
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_RefreshTrackListValue() {
		$currentIdx    = -1;
		$player = NetPlayer_GetIPSComponentPlayer();
		$currentName   = $player->GetTrackName();
		$currentName   = NetPlayer_GetTrackName($currentName);
		$currentName   = str_replace(GetValue(NP_ID_CDINTERPRET).' - ', '', $currentName);

		$profileData   = IPS_GetVariableProfile('NetPlayer_CDTrackList');
		$associations  = $profileData['Associations'];
		foreach ($associations as $idx=>$data) {
			IPSLogger_Trc(__file__, "Idx=$idx, '$currentName' --> '".$data['Name']."'");
			if ($currentName == $data['Name']) {
			   $currentIdx = $data['Value'];
			}
		}
		if (GetValue(NP_ID_CDTRACKLIST)<>$currentIdx ) {
		   SetValue(NP_ID_CDTRACKLIST, $currentIdx);
		}
	}


  /** @}*/
?>