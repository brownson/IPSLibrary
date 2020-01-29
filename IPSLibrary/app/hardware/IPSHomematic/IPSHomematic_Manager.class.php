<?
    /*
     * This file is part of the IPSLibrary.
     *
     * The IPSLibrary is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published
     * by the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * The IPSLibrary is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with the IPSLibrary. If not, see http://www.gnu.org/licenses/gpl.txt.
     */

	/**@ingroup ipshomematic
	 * @{
	 *
	 * @file          IPSHomematic_Manager.class.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 14.07.2012<br/>
	 *
	 */

   /**
    * @class IPSHomematic_Manager
    *
    * Definiert ein IPSHomematic_Manager Objekt
    *
    * @author Andreas Brauneis
    * @version
    *   Version 2.50.1, 15.07.2012<br/>
    */
	class IPSHomematic_Manager {

		/** 
		 * @public
		 *
		 * Initializes the IPSHomematic_Manager
		 */
		public function __construct() {
		}

		/** 
		 * @public
		 *
		 * Refreshed alle RSSI Variablen von der CCU
		 */
		public function RefreshRSSIValues() {
			$instanceIdList = $this->GetMaintainanceInstanceList();
			foreach ($instanceIdList as $instanceId) {
				$variableId = @IPS_GetVariableIDByName('RSSI_DEVICE', $instanceId);
				if ($variableId!==false) {
					usleep(100000);
					set_time_limit(HM_TIMEOUT_REFRESH);
					HM_RequestStatus($instanceId, 'RSSI_DEVICE');
				}
				$variableId = @IPS_GetVariableIDByName('RSSI_PEER', $instanceId);
				if ($variableId!==false) {
					usleep(100000);
					set_time_limit(HM_TIMEOUT_REFRESH);
					HM_RequestStatus($instanceId, 'RSSI_PEER');
				}
			}
		}
		
		/** 
		 * @public
		 *
		 * Refreshed alle RSSI HTML Listen
		 */
		public function RefreshRSSIHtml() {
			$instanceIdList = $this->GetMaintainanceInstanceList();
			$rssiDeviceList = array();
			$rssiPeerList   = array();
			foreach ($instanceIdList as $instanceId) {
				$variableId = @IPS_GetVariableIDByName('RSSI_DEVICE', $instanceId);
				if ($variableId!==false) {
					$rssiValue = GetValue($variableId);
					if ($rssiValue<>-65535) {
						$rssiDeviceList[$instanceId] = $rssiValue;
					}
				}
			}
			arsort($rssiDeviceList, SORT_NATURAL);

			foreach ($instanceIdList as $instanceId) {
				$variableId = @IPS_GetVariableIDByName('RSSI_PEER', $instanceId);
				if ($variableId!==false) {
					$rssiValue = GetValue($variableId);
					if ($rssiValue<>-65535) {
						$rssiPeerList[$instanceId] = $rssiValue;
					}
				}
			}
			arsort($rssiPeerList, SORT_NATURAL);
		  
			$categoryIdHtml     = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.IPSHomematic.StatusMessages');
			$categoryIdSettings = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.IPSHomematic.Settings');
			
			$variableIdRssi       = IPS_GetObjectIDByIdent(HM_CONTROL_RSSI, $categoryIdHtml);
			$variableIdRssiDevice = IPS_GetObjectIDByIdent(HM_CONTROL_RSSIDEVICE, $categoryIdHtml);
			$variableIdRssiPeer   = IPS_GetObjectIDByIdent(HM_CONTROL_RSSIPEER, $categoryIdHtml);

			$str = "<table width='90%' align='center'>"; 
			$str .= "<tr><td><b>Gerätname</b></td><td><b>GeräteID</b></td><td><b>Empfangsstärke</b></td></tr>";
			foreach($rssiDeviceList as $instanceId=>$value) {
				$str .= "<tr><td>".IPS_GetName($instanceId)."</td><td>".IPS_GetProperty($instanceId, 'Address')."</td><td>".$value."</td></tr>";
			}
			$str .= "</table>";
			SetValue($variableIdRssiDevice, $str);

			$str = "<table width='90%' align='center'>"; 
			$str .= "<tr><td><b>Gerätname</b></td><td><b>GeräteID</b></td><td><b>Empfangsstärke</b></td></tr>";
			foreach($rssiPeerList as $instanceId=>$value) {
				$str .= "<tr><td>".IPS_GetName($instanceId)."</td><td>".IPS_GetProperty($instanceId, 'Address')."</td><td>".$value."</td></tr>";
			}
			$str .= "</table>";
			SetValue($variableIdRssiPeer, $str);

			$str = "<table width='90%' align='center'>"; 
			$str .= "<tr><td><b>Gerätname</b></td><td><b>GeräteID</b></td><td><b>Empfangsstärke</b></td></tr>";
			$idx = 0;
			foreach($rssiDeviceList as $instanceId=>$value) {
				$idx++;
				if ($idx<=10) {
					$str .= "<tr><td>".IPS_GetName($instanceId)."</td><td>".IPS_GetProperty($instanceId, 'Address')."</td><td>".$value."</td></tr>";
				}
			}
			$str .= "</table>";
			SetValue($variableIdRssi, $str);
		}
		
		/** 
		 * @public
		 *
		 * Refresh Variablen und HTML der Empfangsstärken
		 */
		public function RefreshRSSI() {
			$this->RefreshRSSIValues();
			$this->RefreshRSSIHtml();
		}
		
		/** 
		 * @public
		 *
		 * Refresh aller Homematic Status Variablen (STATE und LEVEL)
		 */
		public function RefreshStatusVariables() {
			$instanceIdList     = IPS_GetInstanceListByModuleID("{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");
			foreach ($instanceIdList as $instanceId) {
				$variableId = @IPS_GetVariableIDByName('STATE', $instanceId);
				if ($variableId!==false) {
					set_time_limit(HM_TIMEOUT_REFRESH);
					HM_RequestStatus($instanceId, 'STATE');
				}
				//$variableId = @IPS_GetVariableIDByName('LEVEL', $instanceId);
				//if ($variableId!==false) {
				//	HM_RequestStatus($instanceId, 'LEVEL');
				//}
			}
		}
		
		/** 
		 * @public
		 *
		 * Refresh der Homematic Service Messages
		 */
		public function RefreshServiceMessages() {
		    $texte = Array("CONFIG_PENDING"  =>"Konfigurationsdaten stehen zur Übertragung an",
		                   "LOWBAT"          =>"Batterieladezustand gering",
		                   "STICKY_UNREACH"  =>"Gerätekommunikation war gestört",
		                   "UNREACH"         =>"Gerätekommunikation aktuell gestört");

		    $str = "<table width='90%' align='center'>"; // Farbe anpassen oder style entfernen
		    $str .= "<tr><td><b>Gerätname</b></td><td><b>GeräteID</b></td><td><b>Meldung</b></td></tr>";
		    $str_log = "";
		    $ids = IPS_GetInstanceListByModuleID("{A151ECE9-D733-4FB9-AA15-7F7DD10C58AF}");
		    if(sizeof($ids) == 0) die("Keine HomeMatic Socket Instanz gefunden!");

		    $msgs = HM_ReadServiceMessages($ids[0]);
		    if($msgs === false) die("Verbindung zur CCU fehlgeschlagen");

		    if(sizeof($msgs) == 0) {
		        $str .= "<tr><td colspan=3><br/>Keine Servicemeldungen!</td></tr>";
		        $str_log .= "Keine Servicemeldungen!";
		    }
		    foreach($msgs as $msg) {
		       if(array_key_exists($msg['Message'], $texte)) {
		            $text = $texte[$msg['Message']];
		        } else {
		            $text = $msg['Message'];
		        }
		        $id = HM_GetInstanceIDFromHMAddress($msg['Address']);
		        if(IPS_InstanceExists($id)) {
					$name = IPS_GetLocation($id);
		        } else {
		            $name = "Gerät nicht in IP-Symcon eingerichtet";
		        }

		        $str .= "<tr><td>".$name."</td><td>".$msg['Address']."</td><td>".$text."</td></tr>";
		        $str_log .= $name." - ".$msg['Address']." - ".$text."\n";
		    }
		    $str .= "</table>";

		    $categoryIdHtml       = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.IPSHomematic.StatusMessages');
		    $categoryIdSettings   = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.hardware.IPSHomematic.Settings');
		    $variableIdMessages   = IPS_GetObjectIDByIdent(HM_CONTROL_MESSAGES, $categoryIdHtml);
		    $variableIdPriority   = IPS_GetObjectIDByIdent(HM_CONTROL_PRIORITY, $categoryIdSettings);
		    if (GetValue($variableIdMessages) <> $str) {
		        SetValue($variableIdMessages, $str);
		        IPSLogger_Not(__file__, 'New Homematic Service Messages:'.PHP_EOL.$str_log, GetValue($variableIdPriority));
		    }
		}

		/** 
		 * @public
		 *
		 * Reset der Homematic Service Messages
		 */
		public function ResetServiceMessages() {
			$homematicIntanceIdList = IPS_GetInstanceListByModuleID("{A151ECE9-D733-4FB9-AA15-7F7DD10C58AF}");
			if(sizeof($homematicIntanceIdList) == 0) die("Keine HomeMatic Socket Instanz gefunden!");

			$CCUIPAddress = IPS_GetProperty($homematicIntanceIdList[0], 'Host');

			$HM_Script = "
				string itemID;
				string address;
				object aldp_obj;

				foreach(itemID, dom.GetObject(ID_DEVICES).EnumUsedIDs())
				{
					address = dom.GetObject(itemID).Address();
					aldp_obj = dom.GetObject('AL-' # address # ':0.STICKY_UNREACH');
					if (aldp_obj)
					{
					   if (aldp_obj.Value())
						{
						  aldp_obj.AlReceipt();
							! dom.GetObject('Kommunikationsstörung').State(dom.GetObject(itemID).Name());
						}
					  }
				}";

			// Initialisieren der Socket-Verbindung
			$fp = fsockopen ($CCUIPAddress, 8181, $errno, $errstr, 2);
			$res = "";

			if (!$fp) {
				$res = "$errstr ($errno)<br />\n";
			} else {
				// Zusammenstellen des Header für HTTP-Post
				fputs($fp, "POST /Test.exe HTTP/1.1\r\n");
				fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
				fputs($fp, "Content-length: ". strlen($HM_Script) ."\r\n");
				fputs($fp, "Connection: close\r\n\r\n");
				fputs($fp, $HM_Script);
				while(!feof($fp)) {
				$res .= fgets($fp, 500);
				}
				fclose($fp);
			}
			return $res;
		}
		
		/** 
		 * @public
		 *
		 * Liefert alle Homematic Maintainmance Instance IDs, die in der Konfiguration eingetragen sind
		 *
		 * @return array[int] Homematic Instance IDs
		 */
		public function GetMaintainanceInstanceList() {
			$homematicInstanceList     = IPS_GetInstanceListByModuleID("{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}");
			$homematicAddressList      = array();
			$homematicMaintainanceList = array();

			foreach ($homematicInstanceList as $homematicInstanceId ) {
				$homematicAddress = IPS_GetProperty($homematicInstanceId,'Address');
				$homematicAddressList[$homematicAddress] = $homematicInstanceId;
				
				$pos = strpos($homematicAddress, ':0');
				if ($pos !== false) {
					$homematicMaintainanceList[$homematicInstanceId] = $homematicInstanceId;
				}
			}

			return $homematicMaintainanceList;
		}
	}

	/** @}*/
?>
