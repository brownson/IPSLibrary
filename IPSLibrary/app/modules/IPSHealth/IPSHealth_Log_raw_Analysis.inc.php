<?
//Lofile-heute laden
$logFile 					= IPS_GetKernelDir() . "logs\logfile.log";  	// für jede ScriptID wird eine eigene Tmp-Datei erzeugt
$logKeyWord             = 'KernelMT';
$logKeyWord2            = 'Message';
$Message_Type1        	= 'Queue';
$Message_Type2        	= 'VM_UPDATE';

$file = file ($logFile);
//print_r($file);


// Array auf "KernelMT" -Messages reduzieren
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

// Listenausgabe für Konsole
	for($i=0;$i<count($temp);$i++)
	{
		//echo $temp[$i][0]."|".$temp[$i][1]."|".$temp[$i][1]."|".$temp[$i][3]."|".$temp[$i][4];
	}
	//print_r($temp);

// Array für HighCharts
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