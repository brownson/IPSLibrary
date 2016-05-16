<?
	/**@defgroup ipsedip_visu EDIP Visualisierung
	 * @ingroup ipsedip
	 * @{
	 *
	 * Zur Zeit wird nur die Ansteuerung von Displays des Types eDIPTFT43A unterstützt (horizontale Montage). Die Anzeige
	 * ist aufgeteilt in einen Navigations Teil (links) und in einen Variablen  Teil (rechts). Angezeit werden alle Variablen
	 * außer jene mit Profil "HTMLBox", editieren kann man zur Zeit folgende (Voraussetzung ActionScript ist definiert):
	 *  - Boolean
	 *  - Integer mit Prefix "%"
	 *  - Integer mit Assoziationen
	 *
	 * Zusätzlich kann die Anzeige auch noch durch die Angabe von speziellen "Tags" in der Descritpion von Variablen/Links
	 * beeinflusst werden. Dadurch ist es möglich:
	 *  - Darstellung von Texten mit doppelter Schriftgrösse
	 *  - Darstelung von Assoziationen über die volle Breite
	 *  - Darstellung von Assoziationen mit variabler Breite
	 *
	 *  Montage Beispiel eines Displays in der Praxis
	 *  @image html IPSEDIP_Example.jpg
	 *
	 *  Einbindung des NetPlayers und der Entertainment Steuerung
	 *  @image html IPSEDIP_Player.jpg
	 *
	 *  Edit einer Variable mit Assoziationen
	 *  @image html IPSEDIP_Selection.jpg
	 *
	 *  Beispiel von Assoziationen über die volle Breite
	 *  @image html IPSEDIP_FullWidth.jpg
	 *
	 */
	/** @}*/

	/**@defgroup ipsedip IPSEDIP 
	 * @{
	 *
	 * Es handelt sich bei IPSEDIP um Scripts, mit denen es möglich ist IPS Strukturen auf einem EDIP43 Display
	 * zu visualisieren. Das hat den Vorteil, dass man die Visualisierung komplett aus IPS steuern kann. Eine Änderung
	 * der EDIP Programmierung entfällt somit komplett und eine Anbindung neuer EDIP Displays kann praktisch per
	 * Plug and Play erledigt werden !
	 *
	 * Update der Displays erfolgt entweder über einen internen Timer oder über Events (d.h es wird autom. für jede visualisierte
	 * Variable ein Event angelegt, das bei Änderung sofort ein Refresh des Displays auslöst).
	 *
	 * @file          IPSEDIP.class.php
	 * @author        Andreas Brauneis
	 *
	 * EDIP Klasse
	 *
	 */

	include_once "IPSEDIP_Constants.inc.php";
	IPSUtils_Include ("IPSLogger.inc.php", "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSEDIP_Configuration.inc.php", "IPSLibrary::config::hardware::IPSEDIP");

	$_IPS['ABORT_ON_ERROR']    = true;

   /**
    * @class IPSEDIP
    *
    * Definiert ein allgemeines EDIP Display
    *
    * @author Andreas Brauneis
    * @version
    * Version 2.50.1, 31.01.2012<br/>
    */
	abstract class IPSEDIP{
		private $sendDelay=5;

		protected $instanceId=0;
		protected $rootId=0;
		protected $currentId=0;
		private   $registerId=0;
		private   $errorcounter=0;

		protected $objectIdsId=0;
		protected $objectValuesId=0;
		protected $objectCmdsId=0;
		protected $objectEditId=0;

		protected $messageArray=array();
		protected $objectList=array();//Types,Names,...

		/**
       * @public
		 *
		 * Initialisierung eines EDIP Display Objects
		 *
	    * @param integer $instanceId - ID des EDIP Displays.
		 */
		public function __construct($instanceId){
			$this->instanceId     = $instanceId;
			$this->rootId         = GetValue(IPS_GetObjectIDbyIdent(EDIP_VAR_ROOT, $instanceId));
			$this->currentId      = GetValue(IPS_GetObjectIDbyIdent(EDIP_VAR_CURRENT, $instanceId));
			$this->registerId     = GetValue(IPS_GetObjectIDbyIdent(EDIP_VAR_REGISTER, $instanceId));
			$this->objectIdsId    = IPS_GetObjectIDbyIdent(EDIP_VAR_OBJECTIDS, $instanceId);
			$this->objectValuesId = IPS_GetObjectIDbyIdent(EDIP_VAR_OBJECTVALUES, $instanceId);
			$this->objectCmdsId   = IPS_GetObjectIDbyIdent(EDIP_VAR_OBJECTCMDS, $instanceId);
			$this->objectEditId   = IPS_GetObjectIDbyIdent(EDIP_VAR_OBJECTEDIT, $instanceId);
		}

		abstract protected function AddMessageHeader();
		abstract protected function AddMessageCategories();
		abstract protected function AddMessageVariables();
		abstract protected function AddMessageValueEdit();

		private function GenerateEvents() {
			$objectIds = explode(',',GetValue($this->objectIdsId));
			$edipName  = IPS_GetName($this->instanceId);
			foreach ($objectIds as $objectId) {
			   $objectId   = (int)$objectId;
				$objectData = IPS_GetObject($objectId);
				if ($objectData['ObjectType']==2) {
				   $eventName = $edipName.'_'.IPS_GetName($objectId);
					$eventId   = @IPS_GetEventIdByName($eventName ,EDIP_ID_EVENTSCRIPT);
					if ($eventId===false) {
						$eventId = IPS_CreateEvent(0);
						IPSLogger_Trc(__file__, "Create Event=$eventName, ID=$eventId, Parent=".EDIP_ID_EVENTSCRIPT);
				  		IPS_SetName($eventId, $eventName);
						IPS_SetEventTrigger($eventId, 1, $objectId);
						IPS_SetParent($eventId, EDIP_ID_EVENTSCRIPT);
						IPS_SetEventActive($eventId, true);
					}
				}
			}
		}

		private function DropEvents() {
			$objectIds = explode(',',GetValue($this->objectIdsId));
			$edipName  = IPS_GetName($this->instanceId);
			foreach ($objectIds as $objectId) {
			   $objectId = (int)$objectId;
			   $eventName = $edipName.'_'.IPS_GetName($objectId);
				$eventId   = @IPS_GetEventIdByName($eventName ,EDIP_ID_EVENTSCRIPT);
				if ($eventId!==false) {
				   IPSLogger_Trc(__file__, "Drop Event=$eventName, ID=$eventId");
				   IPS_DeleteEvent($eventId);
				}
			}
		}

		private function StoreObjectData() {
			$objectIds    = array();
			$objectValues = array();
			$objectCmds   = array();
			foreach ($this->objectList as $object) {
			   if ($object['ObjectType']=='Category') {
					$objectIds[]    = $object['Link'];
				} else {
					$objectIds[]    = $object['Id'];
				}
				if ($object['DisplayType']=='Text') {
					$objectValues[] = "";
				} elseif (array_key_exists('Value', $object)) {
					$objectValues[] = $object['Value'];
				} else {
					$objectValues[] = "";
				}
				$objectCmds[]   = $object['Cmd'];
			}
			SetValue($this->objectIdsId,    implode(',',$objectIds));
			SetValue($this->objectValuesId, implode(',',$objectValues));
			SetValue($this->objectCmdsId,   implode(',',$objectCmds));
		}
		
		private function GetObjectDataByCmd($cmd) {
			$objectIds    = explode(',',GetValue($this->objectIdsId));
			$objectValues = explode(',',GetValue($this->objectValuesId));
			$objectCmds   = explode(',',GetValue($this->objectCmdsId));
			foreach ($objectCmds as $idx=>$objectCmd) {
				if ($objectCmd==$cmd) {
					$object['Cmd']   = $cmd;
					$object['Id']    = $objectIds[$idx];
					$object['Value'] = $objectValues[$idx];
					return $object;
				}
			}
			return false;
		}
		
		private function GetDisplayAttributte($id, $attribute, $default) {
		   $result     = $default;
			$object     = IPS_GetObject($id);
			$objectInfo = $object['ObjectInfo'];
			if (substr($objectInfo,0,2)=='##') {
				$attributeList = explode(',',substr($objectInfo,2));
				foreach ($attributeList as $keyValue) {
				   $keyValue = explode('=', $keyValue);
				   if ($keyValue[0]==$attribute) {
				      $result = $keyValue[1];
				   }
				}
			}
			return $result;
		}
		
		private function AddObjectCategory($link, $id, $name, $position) {
			$object = array();
			$object['Id']             = $id;
			$object['Link']           = $link;
			$object['Name']           = $name;
			$object['Position']       = $position;
			$object['DisplayType']    = 'Category';
			$object['ObjectType']     = 'Category';
			$this->objectList[] = $object;
		}

		private function AddObjectScript($link, $id, $name, $position) {
			$object = array();
			$object['Id']             = $id;
			$object['Link']           = $link;
			$object['Name']           = $name;
			$object['BlockBegin']     = true;
			$object['BlockEnd']       = true;
			$object['Position']       = $position;
			$object['DisplayType']    = 'Button';
			$object['ObjectType']     = 'Script';
			$object['ValueFormatted'] = 'Execute';
			$object['Width']          = 100;
			$object['LineIdx']        = 0;
			$color = 150*256*256+150*256+150;
			$red    = floor($color/256/256);
			$green  = floor(($color-$red*256*256)/256);
			$blue   = floor(($color-$red*256*256-$green*256));
			$object['Color']  =  $color;
			$object['Red']    =  $red;
			$object['Green']  =  $green;
			$object['Blue']   =  $blue;
			$this->objectList[] = $object;
		}
		
		private function AddObjectVariableValues($object, $associations, $valueCurrent, $objectType) {
			$lineIdx    = 0;
			$widthTotal = 0;
		   foreach ($associations as $idx=>$association) {
		      $object['BlockBegin']     = false;
				$object['BlockEnd']       = false;
		      if ($idx==0)                        $object['BlockBegin']     = true;
				if ($idx == count($associations)-1) $object['BlockEnd']       = true;
				$object['Idx']            = $idx;
				$object['Value']          = $association['Value'];
				$object['ValueFormatted'] = $object['Prefix'].$association['Name'].$object['Suffix'];
				$object['ObjectType']     = $objectType;

            $width = $this->GetDisplayAttributte($object['Link'], 'Width'.$association['Value'], '100');
				$object['Width']    = $width;
				$object['LineIdx']  = $lineIdx;
				$widthTotal = $widthTotal + $width;
				if ($widthTotal >= 100) {
				   $widthTotal = 0;
				   $lineIdx    = 0;
				} else {
				   $lineIdx = $lineIdx + 1;
				}

				if ($object['Value']==$valueCurrent) {
					$color = $association['Color'];
					if ($color==-1) {
					   $color = 150*256*256+150*256+150;
					}
				} else {
					$color = 60*256*256+60*256+60;
				}
				$red    = floor($color/256/256);
				$green  = floor(($color-$red*256*256)/256);
				$blue   = floor(($color-$red*256*256-$green*256));
				$object['Color']  =  $color;
				$object['Red']    =  $red;
				$object['Green']  =  $green;
				$object['Blue']   =  $blue;

				$this->objectList[] = $object;

		   }
		}
		
		private function AddObjectVariable($link, $id, $name, $position) {
			$value        = GetValue($id);
			$variable     = IPS_GetVariable($id);
			$action       = $variable['VariableCustomAction'] ;
			$type         = $variable['VariableValue']['ValueType'];
			$profile      = $variable['VariableCustomProfile'];
			if ($profile=='') return;
			$profileData  = IPS_GetVariableProfile($profile);
			$associations = $profileData['Associations'];
			$color        = -1;

			$object = array();
			$object['Id']             = $id;
			$object['Link']           = $link;
			$object['Name']           = $name;
			$object['Position']       = $position;
			$object['ValueFormatted'] = GetValueFormatted($id);
			$object['ObjectType']     = 'Variable';
			$object['DisplayType']    = 'Text';
			$object['BlockBegin']     = true;
			$object['BlockEnd']       = true;
			$object['Width']          = 100;
			$object['LineIdx']        = 0;
			$object['Suffix']         = $profileData['Suffix'];
			$object['Prefix']         = $profileData['Prefix'];
			$object['MaxValue']       = $profileData['MaxValue'];
			$object['MinValue']       = $profileData['MinValue'];
			$object['StepSize']       = $profileData['StepSize'];

			switch($type) {
			   case 0: // Boolean
					$object['DisplayType'] = 'Switch';
				   $value = $value ? 1 : 0;
				   $object['Value'] = $value;
					if (array_key_exists($value, $associations)) $color = $associations[$value]['Color'];
			      break;
			   case 1: // Integer
					$object['Value']          = $value;
			      if ($object['Suffix'] == '%') {
						$object['DisplayType']    =  'BarGraph';
				   } else if (count($profileData['Associations']) > 0) {
						$object['DisplayType'] = $this->GetDisplayAttributte($link, 'DisplayType', 'Switch');
						$object['Value']       = ""; // Call Edit Mode
						if ($object['DisplayType']=='Inline' or $object['DisplayType']=='Block') {
						   $this->AddObjectVariableValues($object, $associations, $value, 'Variable');
						   return;
						}
				   } else {
					}
					if (array_key_exists($value, $associations)) $color = $associations[$value]['Color'];
			      break;
			   case 2: // Float
					$object['Value']          = floatval($value);
			      break;
			   case 3: // String
					$object['Value']          = $value;
					if ($profile=='~HTMLBox') return; // Text
			      break;
			   default: // Unsupported Datatype
			}
			
			$object['DisplayType'] = $this->GetDisplayAttributte($link, 'DisplayType', $object['DisplayType']);
			if ($action==0 and $object['DisplayType']<>'Text' and $object['DisplayType']<>'BigText') {
				$object['DisplayType'] = 'Text';
			}

			if ($color==-1) $color = 150*256*256+150*256+150;
			$red    = floor($color/256/256);
			$green  = floor(($color-$red*256*256)/256);
			$blue   = floor(($color-$red*256*256-$green*256));
			$object['Color']  =  $color;
			$object['Red']    =  $red;
			$object['Green']  =  $green;
			$object['Blue']   =  $blue;
			$this->objectList[] = $object;

			if (GetValue($this->objectEditId)==$id) {
				$this->AddObjectVariableValues($object, $associations, $value, 'Edit');
			}
		}

		private function AddObjects() {
			$childrenIds = IPS_GetChildrenIDs($this->currentId);
			foreach ($childrenIds as $idx=>$childrenId) {
				$object     = IPS_GetObject($childrenId);
				$name       = IPS_GetName($childrenId);
				$position   = $object['ObjectPosition'];
				$linkId     = $childrenId;
				if ($object['ObjectType']==6) { // Link
					$link = IPS_GetLink($childrenId);
					$childrenId = $link['TargetID'];
					$object     = IPS_GetObject($childrenId);
				}
				switch($object['ObjectType']) {
					case 0: // Category
					case 1: // Instance
						//echo 'Found Category '.$name."\n";
						$this->AddObjectCategory($linkId, $childrenId, $name, $position);
						break;
					case 2: // Variable
						//echo 'Found Variable '.$name."\n";
						$this->AddObjectVariable($linkId, $childrenId, $name, $position);
						break;
					case 3: // Script
						//echo 'Found Script '.$name."\n";
						$this->AddObjectScript($linkId, $childrenId, $name, $position);
						break;
					default:
					   // Unsupported Object ...
				}
			}
		}

		private function OrderObjects() {
			usort($this->objectList, 'IPSEDIP_CompareObjects');
			$cmd   = 30;
			$graph = 2;
			foreach ($this->objectList as $idx=>$object) {
			   if ($object['DisplayType'] == 'BarGraph') {
					$this->objectList[$idx]['Cmd'] = $graph;
					$graph++;
			   } else {
					$this->objectList[$idx]['Cmd'] = $cmd;
					$cmd++;
			   }
			}
		}

		/**
       * @public
		 *
		 * Refresh der Anzeige
		 *
		 */
		public function RefreshDisplay() {
		   IPSLogger_Trc(__file__, 'Refresh EDIP Display '.IPS_GetName($this->instanceId));
		   $this->AddObjects();
		   $this->OrderObjects();
			$this->StoreObjectData();

			$this->messageArray = array();
			$this->AddMessageHeader();
			$this->AddMessageCategories();
			$this->AddMessageVariables();
			$this->AddMessageValueEdit();
			$this->sendArray($this->messageArray);
		}


		private function ReceiveCodeSpecial($code) {
			IPSLogger_Trc(__file__, 'Received SpecialCode='.$code.' from EDIP');
			switch ($code) {
				case 1: // Navigate Back
					if (GetValue($this->objectEditId)<>0) {
						SetValue($this->objectEditId,0);
					} elseif ($this->currentId <> $this->rootId) {
						$this->currentId = IPS_GetParent($this->currentId);
						SetValue(IPS_GetObjectIDbyIdent(EDIP_VAR_CURRENT, $this->instanceId), $this->currentId);
					} else {
					}
					break;
			}
		}

		private function ReceiveCodeCategory($object) {
			IPSLogger_Trc(__file__, 'Received CategoryCode='.$object['Cmd'].' for CategoryId='.$object['Id'].' from EDIP');
			$this->currentId = (int)$object['Id'];
			SetValue(IPS_GetObjectIDbyIdent(EDIP_VAR_CURRENT, $this->instanceId), $this->currentId);
		}

		private function ReceiveCodeScript($object) {
			IPSLogger_Trc(__file__, 'Received CategoryCode='.$object['Cmd'].' for ScriptId='.$object['Id'].' from EDIP');
			IPS_RunScriptWaitEx((int)$object['Id'], array( 'IPS_SENDER'=>'WebFront', 'IPS_VALUE'=>null, 'IPS_VARIABLE'=>null, 'REMOTE_ADDR'=>null));
		}

		private function ReceiveCodeVariable($object) {
			$variableId   = (int)$object['Id'];
			$variable     = IPS_GetVariable($variableId);
			$value        = GetValue($variableId);
			$action       = $variable['VariableCustomAction'] ;
			$type         = $variable['VariableValue']['ValueType'];
			$profile      = $variable['VariableCustomProfile'];
			$profileData  = IPS_GetVariableProfile($profile);
			$associations = $profileData['Associations'];

			if ($profile=='' or $action==0) return;

			if (GetValue($this->objectEditId)<>0) {
				SetValue($this->objectEditId, 0);
			}

			switch($type) {
				case 0: // Boolean
					IPSLogger_Dbg(__file__, 'Execute Action '.$action);
					IPS_RunScriptWaitEx($action, array( 'SENDER'=>'WebFront', 'VALUE'=>!$value, 'VARIABLE'=>$variableId, 'REMOTE_ADDR'=>'localhost'));
					break;
				case 1: // Integer
					if ($object['Value']=="") {
						SetValue($this->objectEditId, $variableId);
					} else {
						IPS_RunScriptWaitEx($action, array( 'SENDER'=>'WebFront', 'VALUE'=>(int)$object['Value'], 'VARIABLE'=>$variableId, 'REMOTE_ADDR'=>'localhost'));
					}
					break;
				case 2: // Float
					break;
				case 3: // String
					break;
				default: // Unsupported Datatype
			}
		}

		/**
       * @public
		 *
		 * Empfang von Daten
		 *
	    * @param string $string - Empfangene Daten vom Display
	    * @param boolean $useEvents - Anlegen von Events für jede visualisierte Variable
		 */
		public function ReceiveText($string, $useEvents=true) {
		   if ($useEvents) {
				$this->DropEvents();
			}
			if (substr($string,0,1)==chr(27)) {
				switch(substr($string,1,1)) {
					case 'A': // Button Code received
						$cmd = ord(substr($string,3));
						$object = $this->GetObjectDataByCmd($cmd);
						if ($object===false) {
							$this->ReceiveCodeSpecial($cmd);
							break;
						}

						$objectData = IPS_GetObject((int)$object['Id']);
						switch($objectData['ObjectType']) {
							case 0: // Category
							case 1: // Instance
								$this->ReceiveCodeCategory($object);
								break;
							case 2: // Variable
								$this->ReceiveCodeVariable($object);
								break;
							case 3: // Script
								$this->ReceiveCodeScript($object);
								break;
							default:
								// Unsupported Object ...
						}
						break;

					case 'B': // Bargraph Value received
						$graph = ord(substr($string,3,1));
						$value = ord(substr($string,4));
						$object = $this->GetObjectDataByCmd($graph);
						$object['Value'] = (int)$value-10; // Correct Value (BarGraph 0..100 doesnt work, 10..110 works???)
						$this->ReceiveCodeVariable($object);
						break;
					default:
						// Unsupported Message Type
				}
			}

			$this->RefreshDisplay();

		   if ($useEvents) {
				$this->GenerateEvents();
			}
		}
		
		
		private function SendArray($messageArray) {
			$messagePackage = '';
			foreach ($messageArray as $idx=>$message) {
				$messagePackage .='#'.$message.chr(13);
				if (strlen($messagePackage) >= 100) {
					$this->SendText($messagePackage);
					$messagePackage = '';
				}
			}
			if (strlen($messagePackage) > 0) {
				$this->SendText($messagePackage);
			}
		}
		
		/**
       * @public
		 *
		 * Senden von Daten
		 *
	    * @param string $string - Daten, die gesendet werden sollen
		 */
	   public function SendText($string){

			// Translate special Characters
			$string = str_replace("Ä", "\x8E", $string);
			$string = str_replace("ä", "\x84", $string);
			$string = str_replace("Ö", "\x99", $string);
			$string = str_replace("ö", "\x94", $string);
			$string = str_replace("ü", "\x81", $string);
			$string = str_replace("Ü", "\x9A", $string);
			$string = str_replace("ß", "\xE1", $string);
			//$string = str_replace(",", "\xFB", $string);
			$string = str_replace("°", "\xF8", $string);

			// Build Message
			$string = chr(17).chr(strlen($string)).$string; //Build Message <DC1><Len><DataBytes> 
			$checkSum = 0; // Calc Checksum
			for($i = 0; $i < strlen($string); $i++) {
				$checkSum = $checkSum + ord(substr($string, $i, 1));
			}
			$string .= chr($checkSum % 256);

			//IPSLogger_Com(__file__,'Send Msg to EDIP: '.$string);
			$result = @RegVar_SendText($this->registerId, $string);
			if ($result===false) {
			   $instanceId = IPS_GetInstance($this->registerId)['ConnectionID'];
      		IPS_SetProperty($instanceId, 'Open', false);
      		IPS_SetProperty($instanceId, 'Open', true);
				IPS_ApplyChanges($instanceId);
				$this->errorCounter++;
				if ($this->errorCounter==2) {
					IPSLogger_Wrn(__file__, 'Error sending EDIP Message to "'.IPS_GetName($this->instanceId).'"');
				}
			}
			ips_sleep($this->sendDelay);
		}
	}


	function IPSEDIP_CompareObjects($object1, $object2) {
		if ($object1['Position'] == $object2['Position']) {
		   if (array_key_exists('Idx',$object1) and array_key_exists('Idx',$object2)) {
				if ($object1['Idx'] == $object2['Idx']) {
					return 0;
				} else {
					return ($object1['Idx'] < $object2['Idx']) ? -1 : 1;
				}
		   }
			return 0;
		} else {
			return ($object1['Position'] < $object2['Position']) ? -1 : 1;
		}
	}

	/** @}*/
?>