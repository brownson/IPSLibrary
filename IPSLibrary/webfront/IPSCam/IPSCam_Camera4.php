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
	 * @file          IPSCam_Camera4.php
	 * @author        Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 10.09.2012<br/>
	 *
	 * File kann in das WebFront bzw. MobileFront eingebunden und ermöglicht den Zugriff auf Kameras
	 *
	 */

	/** @}*/
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">

		<style type="text/css">html, body { margin: 0; padding: 0; }</style>
		<? include_once 'IPSCam_CameraUtils.php'; ?>
	</head>
	<body >
		<?php
			if ($mobileGUI) {
				echo $camManager->GetHTMLMobile(4 /*cameraIdx*/, null /*Size*/, true /*ShowPreDefPosButtons*/, true /*ShowCommandButtons*/, true /*ShowNavigationButtons*/);
			} else {
				echo $camManager->GetHTMLWebFront(4 /*cameraIdx*/, null /*Size*/, true /*ShowPreDefPosButtons*/, true /*ShowCommandButtons*/, false /*ShowNavigationButtons*/);
			}
		?>
   </body>
</html>


