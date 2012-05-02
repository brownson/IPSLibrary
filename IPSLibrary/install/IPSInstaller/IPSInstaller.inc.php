<?
	/**@defgroup ipsinstaller IPSInstaller
	 * @ingroup ipsmodulemanager
	 * @{
	 *
	 * Der IPSInstaller bietet diverse Funktionen um Objekte (Kategorien, Variablen, Links, usw.) in IPS anzulegen.
	 * Die Funktionen überprüfen ob das Objekt bereits existiert, wenn nein wird es neu angelegt ansonsten werden die
	 * Object Parameter angepasst.
	 *
	 * Dadurch ist es möglich Installations Skripts zu schreiben, die man einerseits verwenden kann Entwicklungen vom
	 * Test-System auf das Echt-System zu bringen oder andererseits Entwicklungen auch anderen Usern zur Verfügung zu
	 * stellen.
	 *
	 * @file          IPSInstaller.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 31.01.2012<br/>
	 *
	 */

	/** Anlegen einer Kategorie.
	 *
	 * Die Funktion legt eine Kategory, als übergeordnete ID dient dabei $ParentId. Sollte
	 * dort bereits eine Kategorie mit dem angegebenen Namen existieren, werden die Parameter
	 * (Position und Icon) der existierenden Kategorie auf den neuen Wert gesetzt.
	 *
	 * @param string $Name Name der Kategorie
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Position Positionswert des Objekts
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @return integer ID der Kategorie
	 *
	 */
	function CreateCategory ($Name, $ParentId, $Position, $Icon=null) {
		$CategoryId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($CategoryId === false) $CategoryId = @IPS_GetCategoryIDByName($Name, $ParentId);
		if ($CategoryId === false) {
			$CategoryId = IPS_CreateCategory();
			IPS_SetParent($CategoryId, $ParentId);
			IPS_SetName($CategoryId, $Name);
			IPS_SetIdent($CategoryId, Get_IdentByName($Name));
			if ($Position!==false) {
				IPS_SetPosition($CategoryId, $Position);
			}
			if ($Icon!==false) {
				IPS_SetIcon($CategoryId, $Icon);
			}
			Debug ('Created Category '.$Name.'='.$CategoryId."");
		}
		UpdateObjectData($CategoryId, $Position, $Icon);
		return $CategoryId;
	}

	/** Löschen einer Kategory inklusve Inhalt
	 *
	 * Die Funktion löscht eine Kategory, vor dem Löschen werden noch alle beinhaltenden Objekte
	 * aus der Kategory gelöscht.
	 *
	 * @param integer $CategoryId ID der Kategory
	 *
	 */
	function DeleteCategory($CategoryId) {
		EmptyCategory($CategoryId);
		Debug ("Delete Category ID=$CategoryId");
		IPS_DeleteCategory($CategoryId);
	}

	/** Löschen des Inhalts einer Kategorie inklusve Inhalt
	 *
	 * Die Funktion löscht den gesamtem Inhalt einer Kategorie
	 *
	 * @param integer $CategoryId ID der Kategory
	 *
	 */
	function EmptyCategory($CategoryId) {
		if ($CategoryId==0) {
			Error ("Root Category could NOT ne deleted!!!");
		}

		$ChildrenIds = IPS_GetChildrenIDs($CategoryId);
		foreach ($ChildrenIds as $ObjectId) {
			$Object     = IPS_GetObject($ObjectId);
			$ObjectType = $Object['ObjectType'];
			switch ($ObjectType) {
				case 0: // Category
					DeleteCategory($ObjectId);
					break;
				case 1: // Instance
					EmptyCategory($ObjectId);
					IPS_DeleteInstance($ObjectId);
					break;
				case 2: // Variable
					IPS_DeleteVariable($ObjectId);
					break;
				case 3: // Script
					IPS_DeleteScript($ObjectId, false);
					break;
				case 4: // Event
					IPS_DeleteEvent($ObjectId);
					break;
				case 5: // Media
					IPS_DeleteMedia($ObjectId, true);
					break;
				case 6: // Link
					IPS_DeleteLink($ObjectId);
					break;
				default:
					Error ("Found unknown ObjectType $ObjectType");
			}
		}
		Debug ("Empty Category ID=$CategoryId");
	}

	/** Anlegen eines Kategorie Pfades.
	 *
	 * Eine Liste von Kategorien, die durch einen '.' voneinander separiert sind, können als String übergeben
	 * werden.
	 *
	 * @param string $Path Kategorie Pfad (zB. 'Program.IPSInstaller')
	 * @param integer $LastPosition Positionswert der obersten Kategorie (im Falle von 'Programm.IPSInstaller' kann man den
	 *                              Positionswert von IPSInstaller angeben).
	 * @param string $LastIcon Icon der obersten Kategorie (Dateiname des Icons ohne Pfad/Erweiterung)
	 * @return integer ID der Kategorie
	 *
	 */
	function CreateCategoryPath($Path, $LastPosition=0, $LastIcon="") {
		$CategoryList = explode('.',$Path);
		$ParentId = 0;
		foreach ($CategoryList as $Idx=>$Category) {
			if ($Idx == count($CategoryList)-1) {
				$ParentId = CreateCategory ($Category, $ParentId, $LastPosition, $LastIcon);
			} else {
				$ParentId = CreateCategory ($Category, $ParentId, false, false);
			}
		}
		return $ParentId;
	}

	/** ObjektId aus Pfad ermittlen
	 *
	 * Der Befehl ermittelt aus einer Pfadangabe (zB. "Program.IPSInstaller") die ID der Kategorie
	 *
	 * @param string $Path Pfadangabe
	 * @param string $ReturnFalse wenn true, retouniert die Funktion false wenn das übergebene Object nicht gefunden wurde
	 *
	 */
	function get_ObjectIDByPath($Path, $ReturnFalse=false) {
		$CategoryList = explode('.',$Path);
		$ObjId    = 0;
		$ParentId = 0;
		foreach ($CategoryList as $Idx=>$Category) {
			$ObjId = @IPS_GetObjectIDByIdent(Get_IdentByName($Category), $ParentId);
			if ($ObjId===false) {
				$ObjId=@IPS_GetObjectIDByName($Category, $ParentId);
			}
			if ($ObjId===false) {
				if ($ReturnFalse) {
					return false;
				} else {
					Error("'$Category' could NOT be found for in Path '$Path'!!!");
				}
			}
			$ParentId = $ObjId;
		}
		return $ObjId;
	}

	/** Ident aus Namen generieren
	 *
	 * Die Funktion wandelt einen Namen auf einen gültigen  IPS Ident um.
	 *
	 * @param string $name Name
	 * @return string Identifier 
	 *
	 */
	function Get_IdentByName($name) {
		$ident = str_replace(' ', '', $name);
		$ident = str_replace(array('ö','ä','ü','Ö','Ä','Ü'), array('oe', 'ae','ue','Oe', 'Ae','Ue' ), $ident);
		$ident = str_replace(array('"','\'','%','&','(',')','=','#','<','>','|','\\'), '', $ident);
		$ident = str_replace(array(',','.',':',';','!','?'), '', $ident);
		$ident = str_replace(array('+','-','/','*'), '', $ident);
		$ident = str_replace(array('ß'), 'ss', $ident);
		return $ident;
	}



	/** Anlegen einer Instanz.
	 *
	 * Die Funktion legt eine unkonfigurierte Instanz an, als übergeordnete ID dient dabei $ParentId. Sollte
	 * dort bereits eine Kategorie mit dem angegebenen Namen existieren, werden die Parameter
	 * (Position und Icon) der existierenden Kategorie auf den neuen Wert gesetzt.
	 *
	 * @param string $Name Name der Instance
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param string $ModulId ModulID des zu erstellenden Objekts. Die ModulID ist eine 32Bit GUID im Format
	 *                        {00000000-0000-0000-0000-000000000000}.
	 * @param integer $Position Positionswert des Objekts
	 * @return integer ID der Instanz
	 *
	 */
	function CreateInstance ($Name, $ParentId, $ModulId, $Position=0) {
		$InstanceId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($InstanceId === false) $InstanceId = @IPS_GetInstanceIDByName($Name, $ParentId);
		if ($InstanceId === false) {
			$InstanceId	= IPS_CreateInstance($ModulId);
			IPS_SetParent($InstanceId, $ParentId);
			IPS_SetName($InstanceId, $Name);
			IPS_SetIdent($InstanceId, Get_IdentByName($Name));
			IPS_SetPosition($InstanceId, $Position);
			Debug ("Created Instance $Name=$InstanceId, ModuleID=$ModulId");
		}
		UpdateObjectData($InstanceId, $Position);
		return $InstanceId;
	}

	/** Anlegen einer "MediaPlayer" Instanz.
	 *
	 * Die Funktion legt eine "MediaPlayer" Instanz an und liefert die ID des generierten Objekts als Return Wert zurück
	 * Es wird die erste Soundkarte verwendet, die im System gefunden wird.
	 *
	 * @param string $Name Name der MediaPlayer Instance
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Position Positionswert des Objekts
	 * @return integer ID der Instanz
	 *
	 */
	function CreateMediaPlayer($Name, $ParentId, $Position=0) {
		$MediaPlayerInstanceId = CreateInstance($Name, $ParentId, "{2999EBBB-5D36-407E-A52B-E9142A45F19C}",$Position);
		$SoundCards = WAC_GetDevices($MediaPlayerInstanceId);
		foreach ($SoundCards as $Idx=>$SoundCard) {
			if ($SoundCard <> "No sound") {
				Debug ("Set Soundcard $SoundCard");
			   WAC_SetDeviceID($MediaPlayerInstanceId, $Idx);
			}
		}
		WAC_SetUpdateInterval($MediaPlayerInstanceId, 1);
		IPS_ApplyChanges($MediaPlayerInstanceId);

		return $MediaPlayerInstanceId;
	}

	/** Anlegen einer "Media" Instanz.
	 *
	 * Die Funktion legt eine "Media" Instanz an und liefert die ID des generierten Objekts als Return Wert zurück
	 *
	 * @param string $Name Name der Media Instance
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param string $FileName Dateiname der Mediendatei.
	 * @param boolean $FileExists TRUE, wenn Existenz geprüft werden soll, sonst FALSE
	 * @param boolean $MediaType MedienType (0=Designer Formular, 1=Image Obejct, 2=Sound Objekt)
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @param integer $Position Positionswert des Objekts
	 * @return integer ID der Instanz
	 *
	 */
	function CreateMedia ($Name, $ParentId, $FileName, $FileExists=false, $MediaType=1/*1=Image*/, $Icon="", $Position=0) {
		$MediaId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($MediaId === false) $MediaId = @IPS_GetMediaIDByName($Name, $ParentId);
		if ($MediaId === false) {
			$MediaId	= IPS_CreateMedia($MediaType);
			IPS_SetParent($MediaId, $ParentId);
			IPS_SetName($MediaId, $Name);
			IPS_SetIdent($MediaId, Get_IdentByName($Name));
			IPS_SetPosition($MediaId, $Position);
			Debug ("Created Media $Name=$MediaId, File=$FileName");
		}
		IPS_SetMediaFile($MediaId, $FileName, $FileExists);
		UpdateObjectData($MediaId, $Position, $Icon);
		return $MediaId;
	}

	/** Anlegen einer Instanz.
	 *
	 * Die Funktion legt eine "Dummy" Instanz an und liefert die ID des generierten Objects als Return Wert zurück
	 *
	 * @param string $Name Name der Dummy Instance
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Position Positionswert des Objekts
	 * @return integer ID der Instanz
	 *
	 */
	function CreateDummyInstance ($Name, $ParentId, $Position=0) {
	   return CreateInstance ($Name, $ParentId, "{485D0419-BE97-4548-AA9C-C083EB82E61E}", $Position);
	}

	/** Anlegen einer IO Instanze mit seriellem Port
	 *
	 * Die Funktion legt eine Serielle IO Instanze an. Wenn unter der ParentId bereits eine Instanze mit selbem
	 * Namen existiert, werden alle Parameter auf den aktuellen Wert gesetzt.
	 *
	 * @param string $Name Name der IO Instanze
	 * @param string $ComPort Name des Com Ports
	 * @param integer $Baud Baud Rate des seriellen Ports
	 * @param integer $StopBits Einstellung Stop Bits des seriellen Ports
	 * @param integer $DataBits Parity Data Bits des seriellen Ports
	 * @param string $Parity Parity Einstellung des seriellen Ports
	 * @param integer $Position Positions Wert des Objekts
	 * @param boolean $IgnoreError Ignoriren von Fehlern bei der Generierung der Instanz, andernfalls wird das Script abgebrochen
	 * @return integer ID der seriellen Port Instanze
	 *
	 */
	function CreateSerialPort($Name, $ComPort, $Baud=9600, $StopBits=1, $DataBits=8, $Parity='None', $Position=0, $IgnoreError=true) {
		$InstanceId = CreateInstance($Name, 0, "{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}",$Position);
		COMPort_SetBaudRate($InstanceId, $Baud);
		COMPort_SetStopBits($InstanceId, $StopBits);
		COMPort_SetDataBits($InstanceId, $DataBits);
		COMPort_SetParity($InstanceId, $Parity);
		COMPort_SetPort($InstanceId, $ComPort);
		COMPort_SetOpen($InstanceId, true);

		if (!@IPS_ApplyChanges($InstanceId) and !$IgnoreError) {
			Error ("Error applying Changes to ComPort Instance --> Abort Script");
		};
		return $InstanceId;
	}

	/** Anlegen einer IO Instanze mit seriellem Port
	 *
	 * Die Funktion legt eine Serielle IO Instanze an. Es werden alle IO Instanze gelesen, sollte keine
	 * mit dem selben ComPort Namen gefunden werden, wird eine neue Instanze angelegt.
	 *
	 * @param string $Name Name der IO Instanze
	 * @param string $ComPort Name des Com Ports
	 * @param integer $Baud Baud Rate des seriellen Ports
	 * @param integer $StopBits Einstellung Stop Bits des seriellen Ports
	 * @param integer $DataBits Parity Data Bits des seriellen Ports
	 * @param string $Parity Parity Einstellung des seriellen Ports
	 * @param integer $Position Positions Wert des Objekts
	 * @param boolean $IgnoreError Ignoriren von Fehlern bei der Generierung der Instanz, andernfalls wird das Script abgebrochen
	 * @return integer ID der seriellen Port Instanze
	 *
	 */

	function CreateSerialPortByComPort($Name, $ComPort, $Baud=9600, $StopBits=1, $DataBits=8, $Parity='None', $Position=0,$IgnoreError) {
		$InstanceList = IPS_GetInstanceListByModuleID ("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
		$InstanceId   = false;
		foreach ($InstanceList as $id) {
			$Port = COMPort_GetPort($id);
			Debug ("Found Port >>$Port<<");
			if ($Port==$ComPort or $Port=="") {
				$InstanceId = $id;
			}
		}
		if ($InstanceId !== false) {
			IPS_SetName($InstanceId, $Name);
		}

		return CreateSerialPort($Name, $ComPort, $Baud, $StopBits, $DataBits, $Parity, $Position);
	}

	/** Anlegen eines Splitter mit linkem und rechtem Trennzeichen
	 *
	 * Die Funktion legt eine Splitter Instanz an, die mit linkem und rechtem Trennzeichen arbeitet.
	 *
	 * @param string $name Name der Cutter Instanz
	 * @param integer $ioInstanceId ID der übergeordneten IO Instanz
	 * @param string $leftCutChar Linkes Trennzeichen
	 * @param string $rightCutChar Rechtes Trennzeichen
	 * @param string $timeout Timeout, wenn die Verzögerung zweier Pakete größer als Timeout ist, wird der Puffer gelöscht und as zweite Paket als neuer Inhalt übernommen
	 * @return integer ID der Cutter Instanz
	 *
	 */
	function CreateVariableCutter ($name, $ioInstanceId, $leftCutChar, $rightCutChar, $timeout=0) {
		$instanceId = CreateInstance($name, 0, '{AC6C6E74-C797-40B3-BA82-F135D941D1A2}',0);
      Cutter_SetParseType($instanceId, 0);
      Cutter_SetLeftCutChar($instanceId, $leftCutChar);
      Cutter_SetRightCutChar($instanceId, $rightCutChar);
      Cutter_SetTimeout($instanceId, $timeout);
		IPS_ConnectInstance($instanceId, $ioInstanceId);

		if (!@IPS_ApplyChanges($instanceId)) {
			Error ("Error applying Changes to Cutter Instance --> Abort Script");
		};

      return $instanceId;
	}


	/** Anlegen eines Splitter mit fester Länge
	 *
	 * Die Funktion legt eine Splitter Instanz an, die eine feste Länge verwendet.
	 *
	 * @param string $name Name der Cutter Instanz
	 * @param integer $ioInstanceId ID der übergeordneten IO Instanz
	 * @param string $inputLength Eingabelänge
	 * @param string $syncChar Sync Zeichen
	 * @param string $timeout Timeout, wenn die Verzögerung zweier Pakete größer als Timeout ist, wird der Puffer gelöscht und as zweite Paket als neuer Inhalt übernommen
	 * @return integer ID der Cutter Instanz
	 *
	 */
	function CreateFixedCutter ($name, $ioInstanceId, $inputLength, $syncChar, $timeout=0) {
		$instanceId = CreateInstance($name, 0, '{AC6C6E74-C797-40B3-BA82-F135D941D1A2}',0);
      Cutter_SetParseType($instanceId, 1);
      Cutter_SetInputLength($instanceId, $inputLength);
      Cutter_SetSyncChar($instanceId, $syncChar);
      Cutter_SetTimeout($instanceId, $timeout);
		IPS_ConnectInstance($instanceId, $ioInstanceId);

		if (!@IPS_ApplyChanges($instanceId)) {
			Error ("Error applying Changes to Cutter Instance --> Abort Script");
		};

      return $instanceId;
	}


	/** Anlegen einer Register-Variable
	 *
	 * Die Funktion legt eine Register Variable, als übergeordnete ID dient dabei $ParentId. Sollte
	 * dort bereits eine Register Variable mit dem spezifizierten Namen existieren, werden alle Parameter
	 * auf den aktuellen Wert gesetzt.
	 *
	 * @param string $Name Name der Register Variable
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $ScriptId ID des Scriptes
	 * @param integer $PortId ID der zu verbindenden Instanz
	 * @param integer $Position Positionswert des Objekts
	 * @return integer ID der Register Variable
	 *
	 */
	function CreateRegisterVariable($Name, $ParentId, $ScriptId, $PortId, $Position=0) {
		$InstanceId = CreateInstance($Name, $ParentId, "{F3855B3C-7CD6-47CA-97AB-E66D346C037F}",$Position);
		RegVar_SetRXObjectID($InstanceId, $ScriptId);
		IPS_ConnectInstance($InstanceId, $PortId);
		IPS_ApplyChanges($InstanceId);

		return $InstanceId;
	}

	/** Anlegen eines Scriptes.
	 *
	 * Der Befehl legt ein neues Script an und bindet den Dateinamen $File an das erzeugte Skript.
	 *
	 * @param string $Name Name des Scripts im logischen Objektbaum
	 * @param string $File Dateiname des PHP Skripts (relativ zum "/scripts" Ordner)
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Position Positionswert des Objekts
	 * @return integer ID des Scriptes
	 *
	 */
	function CreateScript ($Name, $File, $ParentId, $Position=0) {
		$ScriptId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($ScriptId === false) $ScriptId = @IPS_GetScriptIDByName($Name, $ParentId);
		if ($ScriptId === false) {
			$File = str_replace(IPS_GetKernelDir().'scripts\\', '', $File);
			if (!file_exists(IPS_GetKernelDir().'scripts\\'.$File)) {
				Error ("Script File $File could NOT be found !!!");
			}
			$ScriptId = IPS_CreateScript(0);
			IPS_SetParent($ScriptId, $ParentId);
			IPS_SetName($ScriptId, $Name);
			IPS_SetPosition($ScriptId, $Position);
 			IPS_SetScriptFile($ScriptId, $File);
			IPS_SetIdent($ScriptId, Get_IdentByName($Name));
			Debug ('Created Script '.$Name.'='.$ScriptId."");
		}
		UpdateObjectData($ScriptId, $Position);
		return $ScriptId;
	}

	/** Anlegen einer Variable.
	 *
	 * Der Befehl legt eine neue IPS-Variable vom Typ $Type an.
	 * Die Funktion liefert eine ID, mit deren Hilfe die erzeugte Variable eindeutig identifiziert werden kann.
	 *
	 * @param string $Name Name der Variable im logischen Objektbaum
	 * @param integer $Type VariablenType 0=Boolean, 1=Integer, 2=Float, 3=String
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Position Positionswert des Objekts
	 * @param string $Profile Name des Profils. Verfügbare Profile können über IPS_GetVariableProfileList abgefragt werden. Wenn ein Leerstring übergeben wird, dann wird das benutzerdefinierte Profil deaktiviert.
	 * @param integer $Action ID eines Skriptes das als Aktions aufgeführt werden soll
	 * @param string $ValueDefault Wert den die Variable nach dem Anlegen haben soll
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @return integer ID der Variable
	 *
	 */
	function CreateVariable ($Name, $Type, $ParentId, $Position=0, $Profile="", $Action=null, $ValueDefault='', $Icon='') {
		$VariableId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($VariableId === false) $VariableId = @IPS_GetVariableIDByName($Name, $ParentId);
		if ($VariableId === false) {
 			$VariableId = IPS_CreateVariable($Type);
			IPS_SetParent($VariableId, $ParentId);
			IPS_SetName($VariableId, $Name);
			IPS_SetIdent($VariableId, Get_IdentByName($Name));
			IPS_SetPosition($VariableId, $Position);
  			IPS_SetVariableCustomProfile($VariableId, $Profile);
 			IPS_SetVariableCustomAction($VariableId, $Action);
			IPS_SetIcon($VariableId, $Icon);
			if ($ValueDefault===null) {
			   switch($Type) {
			      case 0: SetValue($VariableId, false); break; /*Boolean*/
			      case 1: SetValue($VariableId, 0); break; /*Integer*/
			      case 2: SetValue($VariableId, 0.0); break; /*Float*/
			      case 3: SetValue($VariableId, ""); break; /*String*/
			      default: 
			   }
			} else {
				SetValue($VariableId, $ValueDefault); 
			}
			
			Debug ('Created VariableId '.$Name.'='.$VariableId."");
		}
		$VariableData = IPS_GetVariable ($VariableId);
		if ($VariableData['VariableCustomProfile'] <> $Profile) {
			Debug ("Set VariableProfile='$Profile' for Variable='$Name' ");
			IPS_SetVariableCustomProfile($VariableId, $Profile);
		}
		if ($VariableData['VariableCustomAction'] <> $Action) {
			Debug ("Set VariableCustomAction='$Action' for Variable='$Name' ");
			IPS_SetVariableCustomAction($VariableId, $Action);
		}
		UpdateObjectData($VariableId, $Position, $Icon);
		return $VariableId;
	}

	/** Anlegen eines Links
	 *
	 * Die Funktion legt einen Link an, als übergeordnete ID dient dabei $ParentId. Sollte
	 * dort bereits ein Link mit dem angegebenen Namen existieren, werden die Parameter
	 * (Position und Icon) des existierenden Links auf den neuen Wert gesetzt.
	 *
	 * @param string $Name Name des Links im logischen Objektbaum
	 * @param integer $Link ID des zu verknüpfenden Objekts
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Position Positionswert des Objekts
	 * @param string $ident Identifikator für das Objekt
	 * @return integer ID der Variable
	 *
	 */
	function CreateLink ($Name, $Link, $ParentId, $Position, $ident="") {
		$LinkId = false;
		if ($ident<>"") {
			$LinkId = IPS_GetObjectIDByIdent($ident, $ParentId);
		}
		if ($LinkId === false) $LinkId = @IPS_GetLinkIDByName($Name, $ParentId);
		if ($LinkId === false) {
 			$LinkId = IPS_CreateLink();
			IPS_SetParent($LinkId, $ParentId);
			IPS_SetName($LinkId, $Name);
			if ($ident<>"") {
				IPS_SetIdent($LinkId, Get_IdentByName($Name));
			}
			IPS_SetLinkChildID($LinkId, $Link);
			IPS_SetPosition($LinkId, $Position);
			Debug ('Created Link '.$Name.'='.$LinkId."");
		}
		UpdateObjectData($LinkId, $Position);
		IPS_SetLinkChildID($LinkId, $Link);
		return $LinkId;
	}

	/** Anlegen eines Links
	 *
	 * Die Funktion sucht in der spezifizierten Parent Kategorie alle vorhandenen Links und überprüft ob einer der
	 * Links bereits auf das zu verknüpfende Objekt verweist. Wenn kein Link gefunden wurde wird ein neuer angelegt,
	 * anderenfalls wird Position und Name existierenden Links auf den neuen Wert gesetzt.
	 *
	 * @param string $Name Name des Links im logischen Objektbaum
	 * @param integer $LinkChildId ID des zu verknüpfenden Objekts
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Position Positionswert des Objekts
	 * @param string $ident Identifikator für das Objekt
	 * @return integer ID der Variable
	 *
	 */
	function CreateLinkByDestination ($Name, $LinkChildId, $ParentId, $Position, $ident="") {
		$LinkId    = false;
		$ObjectIds = IPS_GetChildrenIDs ($ParentId);
		foreach ($ObjectIds as $ObjectId) {
			$Object = IPS_GetObject ($ObjectId);
			if ($Object['ObjectType']==6 /*Link*/) {
				$Link = IPS_GetLink($ObjectId);
				if ($Link['LinkChildID']==$LinkChildId) {
					$LinkId = $ObjectId;
					break;
			   }
			}
		}

	   if ($LinkId === false) {
 			$LinkId = IPS_CreateLink();
			IPS_SetParent($LinkId, $ParentId);
			IPS_SetPosition($LinkId, $Position);
		}
		IPS_SetLinkChildID($LinkId, $LinkChildId);
		if ($ident<>"") {
			IPS_SetIdent($LinkId, $ident);
		}
		IPS_SetName($LinkId, $Name);
		UpdateObjectData($LinkId, $Position);
		return $LinkId;
	}

   // ------------------------------------------------------------------------------------------------
	function UpdateObjectData($ObjectId, $Position, $Icon="") {
		$ObjectData = IPS_GetObject ($ObjectId);
		$ObjectPath = IPS_GetLocation($ObjectId);
		if ($ObjectData['ObjectPosition'] <> $Position and $Position!==false) {
			Debug ("Set ObjectPosition='$Position' for Object='$ObjectPath' ");
			IPS_SetPosition($ObjectId, $Position);
		}
		if ($ObjectData['ObjectIcon'] <> $Icon and $Icon!==false) {
			Debug ("Set ObjectIcon='$Icon' for Object='$ObjectPath' ");
			IPS_SetIcon($ObjectId, $Icon);
		}

	}

	/** Definieren einer ID Konstante
	 *
	 * Mit der Funktion ist es möglich, eine Konstane in einem PHP Script File auf einen beliebigen Wert zu setzen.
	 *
	 * @param string $Name Name dee Konstante
	 * @param integer $ID Wert der Konstante
	 * @param string $FileName FileName des PHP Scriptes in dem die Konstate gesetzt werden soll
	 * @param string $Namespace Namespace des Files (Angabe der Verzeichnisse durch :: getrennt)
	 *
	 */
	function SetVariableConstant ($Name, $ID, $FileName, $Namespace='') {
		if ($Namespace<>'') {
		   $Namespace = str_replace('::','\\',$Namespace).'\\';
		}
		$FileNameFull = IPS_GetKernelDir().'scripts\\'.$Namespace.$FileName;
		if (!file_exists($FileNameFull)) {
			throw new Exception($FileNameFull.' could NOT be found!', E_USER_ERROR);
		}
		$FileContent = file_get_contents($FileNameFull, true);

		$pos = strpos($FileContent, $Name);
		if ($pos === false) {
			throw new Exception('Error - '.$Name.' could NOT be found in FileContent!', E_USER_ERROR);
		}
		$pos = $pos + strlen($Name);
		while (substr($FileContent, $pos, 1) < "0" or substr($FileContent, $pos, 1) > "9") {
			$pos = $pos+1;
		}
		$FileContentNew = substr($FileContent, 0, $pos).$ID.substr($FileContent, $pos+5);

		file_put_contents($FileNameFull, $FileContentNew);
	}

	/** Definieren "Tages" Timers
	 *
	 * Anlegen eines Timers, der einmal pro Tag zu einer bestimmten Uhrzeit ausgeführt wird
	 *
	 * @param string $Name Name des Timers
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Hour Stunde zu der der Timer aktiviert werden soll
	 * @param integer $Minute Minute zu der der Timer aktiviert werden soll
	 * @return integer ID des Timers
	 *
	 */
	function CreateTimer_OnceADay ($Name, $ParentId, $Hour, $Minute=0) {
		$TimerId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($TimerId === false) $TimerId = @IPS_GetEventIDByName($Name, $ParentId);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetParent($TimerId, $ParentId);
			IPS_SetName($TimerId, $Name);
			IPS_SetIdent($TimerId, Get_IdentByName($Name));
			if (!IPS_SetEventCyclic($TimerId, 2 /**Daily*/, 1,0,0,0,0)) {
				Error ("IPS_SetEventCyclic failed !!!");
			}
			if (!IPS_SetEventCyclicTimeBounds($TimerId, mktime($Hour, $Minute, 0), 0)) {
				Error ("IPS_SetEventCyclicTimeBounds failed !!!");
			}
			IPS_SetEventActive($TimerId, true);
			Debug ('Created Timer '.$Name.'='.$TimerId."");
		}
		return $TimerId;
	}

	/** Definieren "Sekunden" Timers
	 *
	 * Anlegen eines Timers, der alle $Seconds Sekunden ausgeführt wird
	 *
	 * @param string $Name Name des Timers
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Seconds Intervall in Sekunden
	 * @param boolean $Active Timer aktiv setzen
	 * @return integer ID des Timers
	 *
	 */
	function CreateTimer_CyclicBySeconds ($Name, $ParentId, $Seconds, $Active=true) {
		$TimerId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($TimerId === false) $TimerId = @IPS_GetEventIDByName($Name, $ParentId);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetParent($TimerId, $ParentId);
			IPS_SetName($TimerId, $Name);
			IPS_SetIdent($TimerId, Get_IdentByName($Name));
			if (!IPS_SetEventCyclic($TimerId, 2 /*Daily*/, 1 /*Int*/,0 /*Days*/,0/*DayInt*/,1/*TimeType Sec*/,$Seconds/*Sec*/)) {
				Error ("IPS_SetEventCyclic failed !!!");
			}
			IPS_SetEventActive($TimerId, $Active);
			Debug ('Created Timer '.$Name.'='.$TimerId."");
		}
		return $TimerId;
	}

	/** Definieren "Minuten" Timers
	 *
	 * Anlegen eines Timers, der alle $Minutes Minuten ausgeführt wird
	 *
	 * @param string $Name Name des Timers
	 * @param integer $ParentId ID des übergeordneten Objekts im logischen Objektbaum
	 * @param integer $Minutes Intervall in Minuten
	 * @param boolean $Active Timer aktiv setzen
	 * @return integer ID des Timers
	 *
	 */
	function CreateTimer_CyclicByMinutes ($Name, $ParentId, $Minutes, $Active=true) {
		$TimerId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ParentId);
		if ($TimerId === false) $TimerId = @IPS_GetEventIDByName($Name, $ParentId);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetParent($TimerId, $ParentId);
			IPS_SetName($TimerId, $Name);
			IPS_SetIdent($TimerId, Get_IdentByName($Name));
			if (!IPS_SetEventCyclic($TimerId, 2 /*Daily*/, 1 /*Unused*/,0 /*Unused*/,0/*Unused*/,2/*TimeType Minutes*/,$Minutes/*Minutes*/)) {
				Error ("IPS_SetEventCyclic failed !!!");
			}
			IPS_SetEventActive($TimerId, $Active);
			Debug ('Created Timer '.$Name.'='.$TimerId."");
		}
		return $TimerId;
	}

	/** Anlegen eines Events
	 *
	 * Der Befehl erzeugt ein Event, das bei Änderungen der Variable $VariableId das Script
	 * mit der ID $ScriptId aufruft.
	 *
	 * @param string $Name Name des Events
	 * @param integer $VariableId ID der Variable auf der das Event definiert ist
	 * @param integer $ScriptId ID des Scripts das ausgeführt werden soll
	 * @param integer $TriggerType Type des Triggers 0=Bei Variablenaktualisierung, 1=Bei Variablenänderung, 2=Bei Grenzüberschreitung, 3=Bei Grenzunterschreitung
	 * @return integer ID des Events
	 *
	 */
	function CreateEvent ($Name, $VariableId, $ScriptId, $TriggerType=1/*ByChange*/) {
		$EventId = @IPS_GetObjectIDByIdent(Get_IdentByName($Name), $ScriptId);
		if ($EventId === false) $EventId = @IPS_GetEventIDByName ($Name,$ScriptId);
		if ($EventId === false) {
			$EventId = IPS_CreateEvent(0);
			IPS_SetParent($EventId, $ScriptId);
			IPS_SetName($EventId, $Name);
			IPS_SetIdent($EventId, Get_IdentByName($Name));
			IPS_SetEventTrigger($EventId, $TriggerType, $VariableId);
			IPS_SetEventActive($EventId, true);
			Debug ("Created Event $Name=$EventId, trigger ScriptId=$ScriptId by Variable=$VariableId");
  		}
		return $EventId;
	}

	/** Anlegen eines Profils mit Associations
	 *
	 * der Befehl legt ein Profile an und erzeugt für die übergebenen Werte Assoziationen
	 *
	 * @param string $Name Name des Profiles
	 * @param string $Associations[] Array mit Wert und Namens Zuordnungen
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @param integer $Color[] Array mit Farbwerten im HTML Farbcode (z.b. 0x0000FF für Blau). Sonderfall: -1 für Transparent
	 * @param boolean $DeleteProfile Profile löschen und neu generieren
	 *
	 */
	function CreateProfile_Associations ($Name, $Associations, $Icon="", $Color=-1, $DeleteProfile=true) {
		if ($DeleteProfile) {
			@IPS_DeleteVariableProfile($Name);
		}
		@IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, "", "");
		IPS_SetVariableProfileValues($Name, 0, 0, 0);
		IPS_SetVariableProfileDigits($Name, 0);
		IPS_SetVariableProfileIcon($Name, $Icon);
		foreach($Associations as $Idx => $IdxName) {
			if ($IdxName == "") {
			  // Ignore
			} elseif (is_array($Color)) {
				IPS_SetVariableProfileAssociation($Name, $Idx, $IdxName, "", $Color[$Idx]);
		   } else {
				IPS_SetVariableProfileAssociation($Name, $Idx, $IdxName, "", $Color);
			}
		}
	}

	/** Anlegen eines Profils für boolsche Varaiblen
	 *
	 * Der Befehl legt ein Switch Profil an. Es können für den Zustand Ein bzw. Aus jeweils ein eigener
	 * Text, eine eigene Farbe und ein eigenes Icon gesetzt werden. Angabe von Icons und Farbe ist optional
	 *
	 * @param string $Name Name des Profiles
	 * @param string $DisplayFalse Text der für FALSE verwendet werden soll
	 * @param string $DisplayTrue Text der für TRUE verwendet werden soll
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @param integer $ColorOff Farbwert der für FALSE verwendet werden soll im HTML Farbcode (z.b. 0x0000FF für Blau). Sonderfall: -1 für Transparent
	 * @param integer $ColorOn Farbwert der für TRUE verwendet werden soll im HTML Farbcode (z.b. 0x0000FF für Blau). Sonderfall: -1 für Transparent
	 * @param string $IconOff Dateiname des Icons das für FALSE verwendet werden soll
	 * @param string $IconOn Dateiname des Icons das für TRUE verwendet werden soll
	 *
	 */
	function CreateProfile_Switch ($Name, $DisplayFalse, $DisplayTrue, $Icon="", $ColorOff=-1, $ColorOn=0x00ff00, $IconOff="", $IconOn="") {
		@IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, "", "");
		IPS_SetVariableProfileValues($Name, 0, 1, 1);
		IPS_SetVariableProfileDigits($Name, 0);
		IPS_SetVariableProfileIcon($Name, $Icon);
		IPS_SetVariableProfileAssociation($Name, 0, $DisplayFalse, $IconOff, $ColorOff);
		IPS_SetVariableProfileAssociation($Name, 1, $DisplayTrue,  $IconOn,  $ColorOn);
	}

	/** Anlegen eines Integer Profils
	 *
	 * Der Befehl legt ein Integer Profil an. Es kann ein Minimalwert, ein Maximalwert und eine Schrittweite
	 * angegeben werden.
	 *
	 * @param string $Name Name des Profiles
	 * @param integer $Start Der für die Visualisierung genutzte Minimalwert. Diese Soft-Limitation beeinflusst nicht den Variablenwert.
	 * @param integer $Step Die für die Visualisierung genutzte Schrittweite zur Erstellung der Sollwert-Veränderngsleiste. Eine Schrittweite von 0 aktiviert die Assoziationsliste.
	 * @param integer $Stop Der für die Visualisierung genutzte Maximalwert. Diese Soft-Limitation beeinflusst nicht den Variablenwert.
	 * @param string $Prefix Prefix für den Wert
	 * @param string $Suffix Suffix für den Wert
	 * @param string $Icon Dateiname des Icons
	 *
	 */
	function CreateProfile_Count ($Name, $Start=0, $Step=0, $Stop=0, $Prefix="", $Suffix="", $Icon="") {
		@IPS_CreateVariableProfile($Name, 1);
		IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
		IPS_SetVariableProfileValues($Name, $Start, $Stop, $Step);
		IPS_SetVariableProfileDigits($Name, 0);
		IPS_SetVariableProfileIcon($Name, $Icon);
	}

	/** Liefert die ID des ersten gefundenen WebFront Konfigurators
	 *
	 * Die Funktion durchsucht den Konfigurations Baum von IP-Symcon und liefert die ID des erst besten
	 * WebFront Konfigurators zurück.
	 *
	 */
	function ReloadAllWebFronts() {
		$childrenIds = IPS_GetChildrenIDs(0);
		foreach ($childrenIds as $childrenId) {
		   $object     = IPS_GetObject($childrenId);
		   $objectType = $object['ObjectType'];
		   if ($objectType==1 /*Instance*/) {
		      $instance= IPS_GetInstance($childrenId);
		      if ($instance['ModuleInfo']['ModuleID'] == '{3565B1F2-8F7B-4311-A4B6-1BF1D868F39E}') {
		         WFC_Reload($childrenId);
		      }
		   }
		}
	}

	/** Liefert die ID des ersten gefundenen WebFront Konfigurators
	 *
	 * Die Funktion durchsucht den Konfigurations Baum von IP-Symcon und liefert die ID des erst besten
	 * WebFront Konfigurators zurück.
	 *
	 */
	function GetWFCIdDefault() {
	   $wfcId=false;
		$childrenIds = IPS_GetChildrenIDs(0);
		foreach ($childrenIds as $childrenId) {
		   $object     = IPS_GetObject($childrenId);
		   $objectType = $object['ObjectType'];
		   if ($objectType==1 /*Instance*/) {
		      $instance= IPS_GetInstance($childrenId);
		      if ($instance['ModuleInfo']['ModuleID'] == '{3565B1F2-8F7B-4311-A4B6-1BF1D868F39E}') {
		         $wfcId = $childrenId;
		         return $wfcId;
		      }
		   }
		}
	   return $wfcId;
	}

	/** Existenz eines WebFront Konfigurator Items überprüfen
	 *
	 * Der Befehl überprüft ob ein bestimmtes Item im WebFront Konfigurator existiert
	 *
	 * @param integer $WFCId ID des WebFront Konfigurators
	 * @param string $ItemId Element Name im Konfigurator Objekt Baum
	 * @return boolean TRUE wenn das Item existiert anderenfalls FALSE
	 *
	 */
	function exists_WFCItem($WFCId, $ItemId) {
	   $ItemList = WFC_GetItems($WFCId);
	   foreach ($ItemList as $Item) {
	      if ($Item['ID']==$ItemId) {
				return true;
	      }
	   }
	   return false;
	}


	function PrepareWFCItemData (&$ItemId, &$ParentId, &$Title) {
		$ItemId   = str_replace(' ','_',$ItemId);
		$ParentId = str_replace(' ','_',$ParentId);
		//$ItemId   = str_replace('_','',$ItemId);
		//$ParentId = str_replace('_','',$ParentId);
		$Title    = utf8_encode($Title);
		$ItemId   = utf8_encode($ItemId);
		$ParentId = utf8_encode($ParentId);
	}

	function CreateWFCItem ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon, $ClassName, $Configuration) {
	   if (!exists_WFCItem($WFCId, $ItemId)) {
		   Debug ("Add WFCItem='$ItemId', Class=$ClassName, Config=$Configuration");
			WFC_AddItem($WFCId, $ItemId, $ClassName, $Configuration, $ParentId);
		}
		WFC_UpdateConfiguration($WFCId, $ItemId, $Configuration);
		WFC_UpdateParentID($WFCId, $ItemId, $ParentId);
		WFC_UpdatePosition($WFCId,$ItemId, $Position);
		IPS_ApplyChanges($WFCId);
	}

	/** Anlegen eines TabPanes im WebFront Konfigurator
	 *
	 * Der Befehl legt im WebFront Konfigurator ein TabPane mit dem Element Namen $ItemId an
	 *
	 * @param integer $WFCId ID des WebFront Konfigurators
	 * @param string $ItemId Element Name im Konfigurator Objekt Baum
	 * @param string $ParentId Übergeordneter Element Name im Konfigurator Objekt Baum
	 * @param integer $Position Positionswert im Objekt Baum
	 * @param string $Title Title
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 *
	 */
	function CreateWFCItemTabPane ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon) {
		PrepareWFCItemData ($ItemId, $ParentId, $Title);
		$Configuration = "{\"title\":\"$Title\",\"name\":\"$ItemId\",\"icon\":\"$Icon\"}";
		CreateWFCItem ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon, 'TabPane', $Configuration);
	}

	/** Anlegen eines SplitPanes im WebFront Konfigurator
	 *
	 * Der Befehl legt im WebFront Konfigurator ein SplitPane mit dem Element Namen $ItemId an
	 *
	 * @param integer $WFCId ID des WebFront Konfigurators
	 * @param string $ItemId Element Name im Konfigurator Objekt Baum
	 * @param string $ParentId Übergeordneter Element Name im Konfigurator Objekt Baum
	 * @param integer $Position Positionswert im Objekt Baum
	 * @param string $Title Title
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @param integer $Alignment Aufteilung der Container (0=horizontal, 1=vertical)
	 * @param integer $Ratio Größe der Container
	 * @param integer $RatioTarget Zuordnung der Größenangabe (0=erster Container, 1=zweiter Container)
	 * @param integer $RatioType Einheit der Größenangabe (0=Percentage, 1=Pixel)
	 * @param string $ShowBorder Zeige Begrenzungs Linie
	 *
	 */
	function CreateWFCItemSplitPane ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon="", $Alignment=0 /*0=horizontal, 1=vertical*/, $Ratio=50, $RatioTarget=0 /*0 or 1*/, $RatioType /*0=Percentage, 1=Pixel*/, $ShowBorder='true' /*'true' or 'false'*/) {
		PrepareWFCItemData ($ItemId, $ParentId, $Title);
		$Configuration = "{\"title\":\"$Title\",\"name\":\"$ItemId\",\"icon\":\"$Icon\",\"alignmentType\":$Alignment,\"ratio\":$Ratio,\"ratioTarget\":$RatioTarget,\"ratioType\":$RatioType,\"showBorder\":$ShowBorder}";
		CreateWFCItem ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon, 'SplitPane', $Configuration);
	}

	/** Anlegen einer Kategorie im WebFront Konfigurator
	 *
	 * Der Befehl legt im WebFront Konfigurator eine Kategorie mit dem Element Namen $ItemId an
	 *
	 * @param integer $WFCId ID des WebFront Konfigurators
	 * @param string $ItemId Element Name im Konfigurator Objekt Baum
	 * @param string $ParentId Übergeordneter Element Name im Konfigurator Objekt Baum
	 * @param integer $Position Positionswert im Objekt Baum
	 * @param string $Title Title
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @param integer $BaseId Kategorie ID im logischen Objektbaum
	 * @param string $BarBottomVisible Sichtbarkeit der Navigations Leiste
	 * @param integer $BarColums
	 * @param integer $BarSteps
	 * @param integer $PercentageSlider
	 *
	 */
	function CreateWFCItemCategory ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon="", $BaseId /*ID of Category*/, $BarBottomVisible='true' /*'true' or 'false'*/, $BarColums=9, $BarSteps=5, $PercentageSlider='true' /*'true' or 'false'*/ ) {
		PrepareWFCItemData ($ItemId, $ParentId, $Title);
		$Configuration = "{\"title\":\"$Title\",\"name\":\"$ItemId\",\"icon\":\"$Icon\",\"baseID\":$BaseId,\"enumBarColumns\":$BarColums,\"selectorBarSteps\":$BarSteps,\"isBarBottomVisible\":$BarBottomVisible,\"enablePercentageSlider\":$PercentageSlider}";
		CreateWFCItem ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon, 'Category', $Configuration);
	}

	/** Anlegen einer ExternalPage im WebFront Konfigurator
	 *
	 * Der Befehl legt im WebFront Konfigurator eine ExternalPage mit dem Element Namen $ItemId an
	 *
	 * @param integer $WFCId ID des WebFront Konfigurators
	 * @param string $ItemId Element Name im Konfigurator Objekt Baum
	 * @param string $ParentId Übergeordneter Element Name im Konfigurator Objekt Baum
	 * @param integer $Position Positionswert im Objekt Baum
	 * @param string $Title Title
	 * @param string $Icon Dateiname des Icons ohne Pfad/Erweiterung
	 * @param string $PageUri URL der externen Seite
	 * @param string $BarBottomVisible Sichtbarkeit der Navigations Leiste
	 *
	 */
	function CreateWFCItemExternalPage ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon="", $PageUri, $BarBottomVisible='true' /*'true' or 'false'*/) {
		PrepareWFCItemData ($ItemId, $ParentId, $Title);
		$Configuration = "{\"title\":\"$Title\",\"name\":\"$ItemId\",\"icon\":\"$Icon\",\"pageUri\":\"$PageUri\",\"isBarBottomVisible\":$BarBottomVisible}";
		CreateWFCItem ($WFCId, $ItemId, $ParentId, $Position, $Title, $Icon, 'ExternalPage', $Configuration);
	}

	/** Anlegen eines Widget im WebFront Konfigurator
	 *
	 * Der Befehl legt im WebFront Konfigurator ein Widget mit dem Element Namen $ItemId an
	 *
	 * @param integer $WFCId ID des WebFront Konfigurators
	 * @param string $ItemId Element Name im Konfigurator Objekt Baum
	 * @param string $ParentId Übergeordneter Element Name im Konfigurator Objekt Baum
	 * @param integer $Position Positionswert im Objekt Baum
	 * @param string $variableId VariableId, die zur Anzeige im Widget verwendet werden soll
	 * @param string $scriptId ScriptId, Script das ausgeführt werden soll
	 *
	 */
	function CreateWFCItemWidget ($WFCId, $ItemId, $ParentId, $Position, $variableId, $scriptId) {
		PrepareWFCItemData ($ItemId, $ParentId, $Title);
      $Configuration = '{"variableID":'.$variableId.',"scriptID":'.$scriptId.',"name":"'.$ItemId.'"}';
		CreateWFCItem ($WFCId, $ItemId, $ParentId, $Position, '', '', 'InfoWidget', $Configuration);
	}

	/** Löschen eines kompletten Objektbaumes aus dem WebFront Konfigurator
	 *
	 * Der Befehl löscht im WebFront Konfigurator einen Teilbaum durch Angabe des Root Element Namens $ItemId
	 *
	 * @param integer $WFCId ID des WebFront Konfigurators
	 * @param string $ItemId Root Element Name im Konfigurator Objekt Baum
	 *
	 */
	function DeleteWFCItems($WFCId, $ItemId) {
		$ItemList = WFC_GetItems($WFCId);
		foreach ($ItemList as $Item) {
			if (strpos($Item['ID'], $ItemId)===0) {
				DeleteWFCItem($WFCId, $Item['ID']);
			}
		}
	}

	/** Löschen ein Element aus dem WebFront Konfigurator
	 *
	 * Der Befehl löscht im WebFront Konfigurator ein Element durch Angabe des Element Namens $ItemId
	 *
	 * @param integer $WFCId ID des WebFront Konfigurators
	 * @param string $ItemId Element Name im Konfigurator Objekt Baum
	 *
	 */
	function DeleteWFCItem($WFCId, $ItemId) {
		Debug ("Delete WFC Item='$ItemId'");
		WFC_DeleteItem($WFCId, $ItemId);
	}

	function Debug($msg) {
		if (isset($_IPS['MODULEMANAGER'])) {
		   $moduleManager = $_IPS['MODULEMANAGER'];
		   $moduleManager->LogHandler()->Debug($msg);
		} elseif (isset($_IPS['SENDER']) and $_IPS['SENDER']=='WebFront') {
		} else {
		   echo $msg.PHP_EOL;
		}
	}

	function Error($msg) {
		if (isset($_IPS['MODULEMANAGER'])) {
		   $moduleManager = $_IPS['MODULEMANAGER'];
		   $moduleManager->LogHandler()->Error($msg);
		} else {
		   echo $msg.PHP_EOL;
		}
		throw new Exception($msg);
	}

	/** @}*/
?>
