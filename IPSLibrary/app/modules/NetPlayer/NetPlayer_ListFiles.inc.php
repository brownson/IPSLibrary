<?
	/**@addtogroup netplayer 
	 * @{
	 *
	 * @file          NetPlayer_ListFiles.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Anzeige von Verzeichnissen mit MP3 Files
	 */

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_GetCategoryList () {
		$basePath      = NETPLAYER_DIRECTORY;
		$categoryList  = array();
		$directoryList = scandir($basePath);
		$directoryList = array_diff($directoryList, Array(".",".."));
		$idx = 1;
		foreach($directoryList as $directory) {
			$fullDirName = $basePath . "/" . $directory;
			if (is_dir($fullDirName) and strpos($directory, '_')===0) {
				$categoryList[] = $directory;
			}
		}
		return  $categoryList;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_GetDirectoryList () {
		$basePath      = NETPLAYER_DIRECTORY;
		$categoryPath  = GetValue(NP_ID_CDCATEGORYNAME);
		if ($categoryPath<>"") $basePath = $basePath."/".$categoryPath;

		$resultList    = array();
		$directoryList = scandir($basePath);
		$directoryList = array_diff($directoryList, Array(".",".."));

		foreach($directoryList as $directory) {
			$fullDirName = $basePath . "/" . $directory;
			if (strpos($directory, '_')===0) {
				continue;
			} elseif (is_dir($fullDirName)) {
				$resultList[] = $directory;
			} else {
				//ignore
			}
		}
		return $resultList;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_FilterDirectoryList ($directoryList, $count) {
		$resultList = array();
		$maxIdx     = count($directoryList);
		$currentIdx = GetValue(NP_ID_CDDIRECTORYIDX);
		if ($currentIdx > $maxIdx - $count) $currentIdx = $maxIdx - $count;
		if ($currentIdx < 0) $currentIdx = 0;
		for ($idx=0 ; $idx<$count; $idx++) {
			if (array_key_exists($currentIdx + $idx, $directoryList))
			$resultList[] = $directoryList[$currentIdx + $idx];
		}
		return $resultList;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_GetCDList ($count, &$fullDirNameList, &$interpretList, &$albumList) {
		$directoryList = NetPlayer_GetDirectoryList();
		$directoryList = NetPlayer_FilterDirectoryList($directoryList, $count);
		$basePath      = NETPLAYER_DIRECTORY;
		$categoryPath  = GetValue(NP_ID_CDCATEGORYNAME);
		if ($categoryPath<>"") $basePath = $basePath."/".$categoryPath;
		foreach ($directoryList as $idx=>$directory) {
			$fullDirNameList[] = $basePath."/".$directory;
			$interpretList[]   = substr($directory, 0, strpos($directory, "["));
			$albumList[]       = substr($directory, strpos($directory, "["));
	   }
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function NetPlayer_NavigateCD ($delta) {
		$directoryList = NetPlayer_GetDirectoryList();
		$maxIdx        = count($directoryList);
		$currentIdx    = GetValue(NP_ID_CDDIRECTORYIDX);
		if ($currentIdx > $maxIdx - $delta) $currentIdx = $maxIdx - $delta;
		$currentIdx = $currentIdx + $delta;
		if ($currentIdx < 0) $currentIdx = 0;
		SetValue(NP_ID_CDDIRECTORYIDX, $currentIdx);
	}

  /** @}*/
?>