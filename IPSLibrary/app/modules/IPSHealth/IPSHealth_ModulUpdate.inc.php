<?
		include_once "IPSHealth.inc.php";

		$Circle0Id     		= IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSHealth');
		$ips_uebersicht_id	= get_ControlId(c_Control_Uebersicht, $Circle0Id);

		$html1 = "";
		$html1 = $html1 . "<table border='0' bgcolor=#ff6611 width='100%' height='300' cellspacing='0'  >";

		$html1 = $html1 . "<tr>";
		$html1 = $html1 . "<td style='text-align:left;'>";
		$html1 = $html1 . "<span style='font-family:arial;color:white;font-size:15px;'><br></span>";
		$html1 = $html1 . "<span style='font-family:arial;color:white;font-size:15px;'></span></td>";
		$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:white;font-size:50px;'>Update</span></td>";
		$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:20px;'></span></td>";
		$html1 = $html1 . "</tr>";

		$html1 = $html1 . "<tr>";
		$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px'></span></td>";
		$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:yellow;font-size:50px'>IPSHealth</span></td>";
		$html1 = $html1 . "</tr>";

		$html1 = $html1 . "<tr>";
		$html1 = $html1 . "<td align=left><span style='font-family:arial;color:white;font-size:15px;'></span></td>";
		$html1 = $html1 . "<td align=center><span style='font-family:arial;font-weight:bold;color:yellow;font-size:50px;'>wurde gestartet</span></td>";
		$html1 = $html1 . "</tr>";

		$html1 = $html1 . "</table>";

		SetValueString($ips_uebersicht_id,$html1);


		$remoteRepository = 'https://raw.github.com/MCS-51/IPSLibrary/Development/';
		$component = 'IPSHealth';

		IPSUtils_Include ("IPSModuleManager.class.php", "IPSLibrary::install::IPSModuleManager");
		$moduleManager = new IPSModuleManager($component,$remoteRepository);
		$moduleManager->LoadModule($remoteRepository);

		$moduleManager->InstallModule($remoteRepository);


?>