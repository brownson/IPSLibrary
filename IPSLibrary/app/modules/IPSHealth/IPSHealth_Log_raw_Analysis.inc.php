<?
################ IPS Log Queue Analysis by Raketenschnecke 12.06.2012 #####################
/*
	Ab IPS 2.60 #2388
	bereitet bestimmte IPS-Logdaten des aktuellen Tages für eine grafische Darstellung via HighCharts auf.
	Aufbereitet werden 3 Kategorieen:

	   1. Message Queue Items
	   2. Message Queue Delay
	   3. Single Message Delay

	Log-Einträge für Message-Queue:
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	0.06.2012 22:18:09.097  | 0 | 0 | KernelMT | Message Queue: 2 items, Delay 156 ms, Last Message: VM_UPDATE
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	werden aufgesplittet in "Message Queue Items": 2 items
	und
	"Message Queue Delay": Delay 156 ms

	Log-Einträge, die in der "Single Message Queue' zusammen gefasst werden, sind z.B. solche hier:
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	09.06.2012 11:30:07.111 | 0 | 0 | KernelMT | Message VM_UPDATE for ID 49626 took 54 ms
	0.06.2012 20:26:57.941  | 0 | 0 | KernelMT | Message TM_RUNNING for ID 11 took 104 ms
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	die Log-Einträge der 3 Kategorieen werden in dedizierten Arrays aufbereitet
	an das dazugehörige HighChart-Config Script übergeben.

	Diese Auswertung lässt Rückschlüsse auf die Stabilität/Performance des IPS-Systems zu.
	Dieses Script wird im Normalfall vom Highchart-Scipt aufgerufen. Wird das Script manuell aus der Konsole gestartet,
	werden die gefilterten Log-Einträge zusätzlich im Meldungsfenster ausgeworfen
*/
################ IPS Log Queue Analysis by Raketenschnecke #####################

// 1. Konfig-Parameter

	$logFile 					= IPS_GetKernelDir() . "logs\logfile.log";  	// Pfad- und File-Angabe (Standard: logfile.log)
	$logKeyWord             = 'KernelMT';
	$logKeyWord2            = 'Message';
	$logKeyWord3				= 'IPS_KERNEL';
	$logKeyWord4            = 'ms (';
	$Message_Type1        	= 'Queue';
	$Message_Type2        	= 'Message Queue';
   $temp                   = array();

// 2. Logfile auslesen und nach $logKeyWord gefilterte Zeilen in Array einlesen

	$f 							= fopen($logFile, "r");
	$ln							= 0;
	while ($line= fgets ($f))
	{
	   ++$ln;
		if ($line===FALSE)
		{
			print ("FALSE\n");
		}
	   else
		{
			$PrimaryKey    = @str_replace(' ', '',explode('|', $line)[3]);
			//Logfile-Datensätze nach $logKeyWord scannen und extrahieren
		   if(($PrimaryKey ==  $logKeyWord) && (strpos($line, $logKeyWord2) > 0) && (!strpos($line, $logKeyWord3))&& (!strpos($line, $logKeyWord4)))
		   {
			   $temp[]    = explode("|", $line);
		   }
		}
	}
	fclose ($f);

// 3. Listenausgabe für Konsole (nur beim manuellen Scriptaufruf)
	if ($_IPS['SENDER'] == 'Execute')
	{
		for($i=0;$i<count($temp);$i++)
		{
			echo $temp[$i][0]."|".$temp[$i][1]."|".$temp[$i][1]."|".$temp[$i][3]."|".$temp[$i][4];
		}
	}

// 4. 3 Arrays für HighCharts aufbauen
   $HC_Data_MQ_Items    = array();
   $HC_Data_MQ_Delay    = array();
   $HC_Data_SM_Delay    = array();

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

		// Ausgabe-Arrays für Single-Messages -Logeinträge
	   if((!strpos($temp[$i][4], $Message_Type2)) && (!strpos($temp[$i][4], '(InstanceForward)')))
	   {
	      $cut_range                       = strpos($temp[$i][4], 'ID');
	      $string									= substr ( $temp[$i][4], $cut_range);
			$Detail_Values                   = explode(" ", $string);

			// Ausgabe-Array "Delay"
			$HC_Data_SM_Delay[$i]['Value'] 		= $Detail_Values[3];
			$HC_Data_SM_Delay[$i]['TimeStamp'] 	= mktime(substr($temp[$i][0], 11, 2), substr($temp[$i][0], 14, 2), substr($temp[$i][0], 17, 2), substr($temp[$i][0], 3, 2), substr($temp[$i][0], 0, 2), substr($temp[$i][0], 6, 4));
			$HC_Data_SM_Delay[$i]['humanDate'] 	= date("d.m.Y. H:i:s", $HC_Data_SM_Delay[$i]['TimeStamp']);
		}

	}
   $HC_Data_SM_Delay	= array_merge($HC_Data_SM_Delay);

	//print_r($HC_Data_Items);
	//print_r($HC_Data_Delay);
	//print_r($HC_Data_SM_Delay);

?>