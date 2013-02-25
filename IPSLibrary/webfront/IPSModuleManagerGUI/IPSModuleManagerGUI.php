<?php 
	/**
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

	/**@addtogroup ipscam
	 * @{
	 *
	 * @file          IPSCam_Camera.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 10.09.2012<br/>
	 *
	 * File kann in das WebFront bzw. MobileFront eingebunden und ermöglicht den Zugriff auf Kameras
	 *location.reload();
	 */

	/** @}*/
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
			
		<style type="text/css">html, body { margin: 0; padding: 0; }</style>
		<link href="/user/default.css" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="jquery.min.js"></script>

		<script type="text/javascript">
			function trigger_button(action, module, info) {
				var serverAddr = "<?echo $_SERVER["HTTP_HOST"];?>";
				var id         = $(this).attr("id");

				$.ajax({type: "POST",
						url: location.protocol+"//"+serverAddr+"/user/IPSModuleManagerGUI/IPSModuleManagerGUI_Receiver.php",
						data: "id="+id+"&action="+action+"&module="+module+"&info="+info});
			}

			function trigger_button2(action, module, info) {
				var serverAddr = "<?echo $_SERVER["HTTP_HOST"];?>";
				var id                    = $(this).attr("id");
				var WFC10Enabled          = $("#WFC10Enabled").is(':checked');
				var WFC10TabPaneExclusive = $("#WFC10TabPaneExclusive").is(':checked');
				var WFC10Path             = $("#WFC10Path").val();
				var WFC10ID               = $("#WFC10ID").val();
				var WFC10TabPaneParent    = $("#WFC10TabPaneParent").val();
				var WFC10TabPaneItem      = $("#WFC10TabPaneItem").val();
				var WFC10TabPaneIcon      = $("#WFC10TabPaneIcon").val();
				var WFC10TabPaneName      = $("#WFC10TabPaneName").val();
				var WFC10TabPaneOrder     = $("#WFC10TabPaneOrder").val();
				var WFC10TabItem          = $("#WFC10TabItem").val();
				var WFC10TabIcon          = $("#WFC10TabIcon").val();
				var WFC10TabName          = $("#WFC10TabName").val();
				var WFC10TabOrder         = $("#WFC10TabOrder").val();
	
				var MobileEnabled         = $("#MobileEnabled").is(':checked');
				var MobilePath            = $("#MobilePath").val();
				var MobilePathIcon        = $("#MobilePathIcon").val();
				var MobilePathOrder       = $("#MobilePathOrder").val();
				var MobileName            = $("#MobileName").val();
				var MobileIcon            = $("#MobileIcon").val();
				var MobileOrder           = $("#MobileOrder").val();

				$.ajax({type: "POST",
						url: location.protocol+"//"+serverAddr+"/user/IPSModuleManagerGUI/IPSModuleManagerGUI_Receiver.php",
						data: encodeURIComponent("id="+id+"&action="+action+"&module="+module+"&info="+info
						       +"&WFC10Enabled="+WFC10Enabled
						       +"&WFC10TabPaneExclusive="+WFC10TabPaneExclusive
						       +"&WFC10Path="+WFC10Path
						       +"&WFC10ID="+WFC10ID
						       +"&WFC10TabPaneParent="+WFC10TabPaneParent
						       +"&WFC10TabPaneItem="+WFC10TabPaneItem
						       +"&WFC10TabPaneIcon="+WFC10TabPaneIcon
						       +"&WFC10TabPaneName="+WFC10TabPaneName
						       +"&WFC10TabPaneOrder="+WFC10TabPaneOrder
						       +"&WFC10TabItem="+WFC10TabItem
						       +"&WFC10TabIcon="+WFC10TabIcon
						       +"&WFC10TabName="+WFC10TabName
						       +"&WFC10TabOrder="+WFC10TabOrder
						       +"&MobileEnabled="+MobileEnabled
						       +"&MobilePath="+MobilePath
						       +"&MobilePathIcon="+MobilePathIcon
						       +"&MobilePathOrder="+MobilePathOrder
						       +"&MobileName="+MobileName
						       +"&MobileIcon="+MobileIcon
						       +"&MobileOrder="+MobileOrder)
						});
						
			}


		</script>

	</head>
	<body >
		<a href="#" onClick=trigger_button('Refresh','','')>Refresh</a> |
		<a href="#" onClick=trigger_button('Overview','','')>&Uuml;bersicht</a> |
		<a href="#" onClick=trigger_button('Logs','','')>Log File's</a> |
		<a href="#" onClick=trigger_button('Updates','','')>Update's</a> |
		<a href="#" onClick=trigger_button('NewModule','','')>Neues Modul</a>
		<?php
			IPSUtils_Include ("IPSModuleManagerGUI.inc.php", "IPSLibrary::app::modules::IPSModuleManagerGUI");

			$baseId  = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSModuleManagerGUI');
			$action  = GetValue(IPS_GetObjectIDByIdent(IPSMMG_VAR_ACTION, $baseId));
			$module  = GetValue(IPS_GetObjectIDByIdent(IPSMMG_VAR_MODULE, $baseId));
			$info    = GetValue(IPS_GetObjectIDByIdent(IPSMMG_VAR_INFO,   $baseId));

			$processing = !IPSModuleManagerGUI_GetLock();
			if (!$processing) {
				IPSModuleManagerGUI_ReleaseLock();
			} else {
				echo '| Processing ...';
			}
		?>
		<BR>
		<BR>
		<?php
			switch($action) {
				case IPSMMG_ACTION_OVERVIEW:
					include 'IPSModuleManagerGUI_Overview.php';
					break;
				case IPSMMG_ACTION_UPDATES:
					include 'IPSModuleManagerGUI_Updates.php';
					break;
				case IPSMMG_ACTION_MODULE:
					include 'IPSModuleManagerGUI_Module.php';
					break;
				case IPSMMG_ACTION_WIZARD:
					include 'IPSModuleManagerGUI_Wizard.php';
					break;
				case IPSMMG_ACTION_LOGS:
					include 'IPSModuleManagerGUI_Logs.php';
					break;
				case IPSMMG_ACTION_LOGFILE:
					include 'IPSModuleManagerGUI_LogFile.php';
					break;
				case IPSMMG_ACTION_NEWMODULE:
					include 'IPSModuleManagerGUI_NewModule.php';
					break;
				default:
					trigger_error('Unknown Action '.$action);
			}
		?>

	</body>
</html>


