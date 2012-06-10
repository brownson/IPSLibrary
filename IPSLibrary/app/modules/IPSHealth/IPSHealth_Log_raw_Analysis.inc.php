<?
################ IPS Log Queue Analysis by Raketenschnecke #####################
/*
	bereitet die IPS-Logdaten des aktuellen tages für eine grafische Darstellung via HighCharts auf.
	Ausgelesen und aufbereitet werden werden Log-Einträge:

	09.06.2012 11:29:51.129 | 0 | 0 | KernelMT | Message Queue: 35 items, Delay 125 ms
	09.06.2012 11:30:07.111 | 0 | 0 | KernelMT | Message VM_UPDATE for ID 49626 took 54 ms

	diese werden als Array aufbereitet an das dazugehörige HighChart-Config Script übergeben.
	Im HC-Diagramm sind dann die Anzahl der Queue-Einträge (items), das Delay der Queue (Delay)
	und das Delay des VM-Updates zu sehen.
	Dies lässt Rückschlüsse auf die Stabilität/Performance des IPS-Systems zu.
	Dieses Script wird im Normalfall vom Highchart-Scipt aufgerufen. Wird das Script manuell aus der Konsole gestartet,
	werden die gefilterten Log-Einträge zusätzlich im Meldungsfenster ausgeworfen
*/
################ IPS Log Queue Analysis by Raketenschnecke #####################

// 1. Lofile-heute laden
	$logFile 					= IPS_GetKernelDir() . "logs\logfile.log";  	// für jede ScriptID wird eine eigene Tmp-Datei erzeugt
	$logKeyWord             = 'KernelMT';
	$logKeyWord2            = 'Message';
	$Message_Type1        	= 'Queue';
	$Message_Type2        	= 'VM_UPDATE';

	$file = file ($logFile);
	//print_r($file);


// 2. Array auf "KernelMT" -Messages reduzieren
   $temp       = array();
	for($i=0;$i<count($file);$i++)
	{
	   if((strpos($file[$i], $logKeyWord) > 0) && (strpos($file[$i], $logKeyWord2) > 0))
	   {
		   $temp[]    = explode("|", $file[$i]);
	   }
		//$LogArray[$i]['TimeStamp']      = $temp[0];
	}
	// array "$file" löschen
	$file       = array();

// 3. Listenausgabe für Konsole (nur beim manuellen Scriptaufruf)
	if ($_IPS['SENDER'] == 'Execute')
	{
		for($i=0;$i<count($temp);$i++)
		{
			echo $temp[$i][0]."|".$temp[$i][1]."|".$temp[$i][1]."|".$temp[$i][3]."|".$temp[$i][4];
		}
		//print_r($temp);
	}

// 4. 3 Arrays für HighCharts aufbauen
   $HC_Data_MQ_Items    = array();
   $HC_Data_MQ_Delay    = array();
   $HC_Data_VM_Delay    = array();

	for($i=0;$i<count($temp);$i++)
	{
	   // Ausgabe-Arrays für Message Queue -Logeinträge
	   if(strpos($temp[$i][4], $Message_Type1) > 0)
	   {
			$search                          = array('Message', 'Queue:', 'items', "Delay", "ms", ' ', "\n", "\r");
			$string 									= str_replace($search, "", $temp[$i][4]);
			$Detail_Values                   = explode(",", $string);

			// Ausgabe-Array "Items"
			$HC_Data_MQ_Items[$i]['Value'] 		= $Detail_Values[0];
			$HC_Data_MQ_Items[$i]['TimeStamp'] 	= mktime(substr($temp[$i][0], 11, 2), substr($temp[$i][0], 14, 2), substr($temp[$i][0], 17, 2), substr($temp[$i][0], 3, 2), substr($temp[$i][0], 0, 2), substr($temp[$i][0], 6, 4));
			$HC_Data_MQ_Items[$i]['humanDate'] 	= date("d.m.Y. H:i:s", $HC_Data_MQ_Items[$i]['TimeStamp']);

			// Ausgabe-Array "Delay"
			$HC_Data_MQ_Delay[$i]['Value'] 		= $Detail_Values[1];
			$HC_Data_MQ_Delay[$i]['TimeStamp'] 	= mktime(substr($temp[$i][0], 11, 2), substr($temp[$i][0], 14, 2), substr($temp[$i][0], 17, 2), substr($temp[$i][0], 3, 2), substr($temp[$i][0], 0, 2), substr($temp[$i][0], 6, 4));
			$HC_Data_MQ_Delay[$i]['humanDate'] 	= date("d.m.Y. H:i:s", $HC_Data_MQ_Delay[$i]['TimeStamp']);
		}

		// Ausgabe-Arrays für Message VM-Updates -Logeinträge
	   if((strpos($temp[$i][4], $Message_Type2) > 0) && (strpos($temp[$i][4], '(InstanceForward)') == false))
	   {
			$search                          = array('Message', 'VM_UPDATE', 'for', 'ID', "Delay", "ms", ' ', "\n", "\r");
			$string 									= str_replace($search, "", $temp[$i][4]);
			$Detail_Values                   = explode("took", $string);

			// Ausgabe-Array "Delay"
			$HC_Data_VM_Delay[$i]['Value'] 		= $Detail_Values[1];
			$HC_Data_VM_Delay[$i]['TimeStamp'] 	= mktime(substr($temp[$i][0], 11, 2), substr($temp[$i][0], 14, 2), substr($temp[$i][0], 17, 2), substr($temp[$i][0], 3, 2), substr($temp[$i][0], 0, 2), substr($temp[$i][0], 6, 4));
			$HC_Data_VM_Delay[$i]['humanDate'] 	= date("d.m.Y. H:i:s", $HC_Data_VM_Delay[$i]['TimeStamp']);
		}

	}

	//print_r($HC_Data_Items);
	//print_r($HC_Data_Delay);
	// print_r($HC_Data_VMDelay);
?>