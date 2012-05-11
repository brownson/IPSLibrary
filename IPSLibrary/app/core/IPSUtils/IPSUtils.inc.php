<?
	/**@addtogroup ipsmodulemanager
	 * @{
	 *
	 * @file          IPSUtils.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Diverse Hilfs Funktionen für den Betrieb der IPSLibrary
	 *
	 */


  /**
    * @class IPSUtilException
    *
    * Definiert eine IPSUtilException Exception
    *
    */
	class IPSUtilException extends Exception {
	}

	/**
	 * Function zum Include ander Scripte
	 *
	 * Usage:
	 * <pre>IPSUtils_Include('IPSLogger.inc.php', 'IPSLibrary::app::core::IPSLogger');</pre>
	 *
 	 * @param string $file File das inkludiert werden soll
	 * @param string $namespace namespace des Files, dass inkludiert werden soll (gibt den relativen Pfad vom IPS scripts Verzeichnis an)
	 */
	function IPSUtils_Include($file, $namespace="") {
	   if ($namespace=="") {
	      include_once $file;
	   } else {
	      $file = IPS_GetKernelDir().'\\scripts\\'.str_replace('::','\\',$namespace).'\\'.$file;
	      
	      if (!file_exists($file)) {
				throw new Exception('script '.$file.' could NOT be found!', E_USER_ERROR);
	      }
	      include_once $file;
	   }
	}

	/** ObjektId aus Pfad ermittlen
	 *
	 * Der Befehl ermittelt aus einer Pfadangabe (zB. "IPSLibrary.IPSUtils.IPSUtils.inc.php") die ID des Scriptes
	 *
	 * @param string $path Pfadangabe
	 * @param string $returnFalse wenn true, retouniert die Funktion false wenn das übergebene Object nicht gefunden wurde
	 * @return integer ID des Objektes
	 *
	 */
	function IPSUtil_ObjectIDByPath($path, $returnFalse=false) {
		$categoryList = explode('.',$path);
		if (count($categoryList)==1 and is_numeric($categoryList[0])) {
		   return (int)$categoryList[0];
		}

		$objId    = 0;
		$parentId = 0;
		foreach ($categoryList as $idx=>$category) {
			$objId = @IPS_GetObjectIDByIdent($category, $parentId);
			if ($objId===false) {
				$objId=@IPS_GetObjectIDByName($category, $parentId);
			}
			if ($objId===false) {
				if ($returnFalse) {
					return false;
				} else {
					throw new IPSUtilException('"'.$category.'" could NOT be found while searching for Path '.$path);
				}
			}
			$parentId = $objId;
		}
		return $objId;
	}

	/** Ident aus Object ID ermittlen
	 *
	 * @param integer $objId ID des Objektes
	 * @param string Ident des Objektes
	 *
	 */
	if (!function_exists('IPS_GetIdent')) {
		function IPS_GetIdent($objId) {
			$object = IPS_GetObject($objId);
			$ident  = $object['ObjectIdent'];
			return $ident;
		}
	}


   /** @}*/
?>