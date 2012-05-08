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


	/**@defgroup ipstwilight IPSTwilight 
	 * @ingroup modules_weather
	 * @{
	 *
	 * Script zur Berechnung der aktuellen Dämmerungs Zeiten
	 *
	 * IPSTwilight ist ein IPS Modul, um die täglichen Dämmerungszeiten zu berechnen und grafisch darzustellen.
	 * Ein Timer berechnet jeden Tag automatisch die aktuellen Dämmerungszeiten.
	 *
	 * Es gibt die Möglichkeit durch Angabe einer Minimal bzw. Maximal Range die jeweilige Dämmerungszeit zu begrenzen.
	 *
	 * So ist es z.B. möglich mit einer Beschattungssteuerung das Schließen der Jalousien an die Dämmerungszeit zu binden, aber gleichzeitig 
	 * zu definieren, dass das Schließen frühestens um 18:00 aber spätestens um 20:00 zu geschehen hat.
	 *
	 * Es werden folgende Dämmerungszeiten berechnet:
	 * - Sonnenaufgang/Sonnenuntergang
	 * - bürgerliche (zivile/ civil) Dämmerung - ist der Zeitpunkt, an dem die Sonne 6 Grad unter dem Horizont ist.
	 * - nautische Dämmerung - ist der Zeitpunkt, an dem die Sonne 12 Grad unter dem Horizont ist.
	 * - astronomische Dämmerung - ist der Zeitpunkt, an dem die Sonne 18 Grad unter dem Horizont ist.
	 * 
	 * Das Einsetzen der Dämmerung hängt vom Längengrand und somit vom jeweiligen Ort ab. Dazu muß im File "IPSTwilight_Configuraiton.inc.php" 
	 * der jeweilige Breiten und Längengrad gesetzt werden.'''
	 * 
	 * @file          IPSTwilight.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 13.02.2012<br/>
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                  "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSInstaller.inc.php",               "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSTwilight_Configuration.inc.php", "IPSLibrary::config::modules::Weather::IPSTwilight");
	IPSUtils_Include ("IPSTwilight_Custom.inc.php",        "IPSLibrary::config::modules::Weather::IPSTwilight");

	// ----------------------------------------------------------------------------------------------------------------------------
	// Settings for Twilight Calculation
	// ----------------------------------------------------------------------------------------------------------------------------
	$categoryId_Twilight = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.Weather.IPSTwilight');
	$categoryId_Scripts   = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.Weather.IPSTwilight');
	$categoryId_Values    = IPS_GetObjectIDByIdent('Values',$categoryId_Twilight);
	$categoryId_Graphics  = IPS_GetObjectIDByIdent('Graphics',$categoryId_Twilight);
	$variableId_Display   = IPS_GetObjectIDByIdent('Display',$categoryId_Values);
	$scriptId_Refresh     = IPS_GetObjectIDByIdent('IPSTwilight',$categoryId_Scripts);

	switch ($_IPS['SENDER']) {
		case 'WebFront':
			SetValue($_IPS['VARIABLE'], $_IPS['VALUE']);
			if ($IPS_VARIABLE==$variableId_Display) {
				CopyGraphics($variableId_Display);
			} else {
				IPS_RunScript ($_IPS['SELF']);
			}
			break;
		case 'TimerEvent':
			$eventName = IPS_GetName($_IPS['EVENT']);
			if (function_exists($eventName)) {
				$eventName(); 
			} else {
				CalculateCurrentValues($categoryId_Values, $scriptId_Refresh);
				GenerateGraphics($variableId_Display);
			}
			break;
		case 'Execute':
		case 'RunScript':
			CalculateCurrentValues($categoryId_Values, $scriptId_Refresh);
			GenerateGraphics($variableId_Display);
			break;
		default:
			IPSLogger_Err(__file__, 'Unknown Sender '.$_IPS['SENDER']);
			break;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function CopyGraphics($variableId_Display) {
		if (GetValue($variableId_Display)) {
			$SourceYear = IPS_GetKernelDir().'media\\IPSTwilight_YearLimited.gif';
			$SourceDay  = IPS_GetKernelDir().'media\\IPSTwilight_DayLimited.gif';
		} else {
			$SourceYear = IPS_GetKernelDir().'media\\IPSTwilight_YearUnlimited.gif';
			$SourceDay  = IPS_GetKernelDir().'media\\IPSTwilight_DayUnlimited.gif';
		}
	   
		if (!copy($SourceYear, IPS_GetKernelDir().'media\\IPSTwilight_Year.gif')) {
			IPSLogger_Err(__file__, "Error while coping $SourceYear to Destination File 'IPSTwilight_Year.gif'");
		}
		if (!copy($SourceDay,  IPS_GetKernelDir().'media\\IPSTwilight_Day.gif')) {
			IPSLogger_Err(__file__, "Error while coping $SourceDay to Destination File 'IPSTwilight_Day.gif'");
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function GenerateGraphics($variableId_Display) {
		GenerateTwilightGraphic('IPSTwilight_YearUnlimited', false, 4.4, 1.8);
		GenerateTwilightGraphic('IPSTwilight_YearLimited',   true,  4.4, 1.8);
		GenerateClockGraphic('IPSTwilight_DayUnlimited',      false);
		GenerateClockGraphic('IPSTwilight_DayLimited',        true);
		GenerateMondphase('IPSTwilight_Mond');

		CopyGraphics($variableId_Display);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function CreateTimer($date, $function, $scriptId_Refresh) {
		if (function_exists($function)) {
			IPSLogger_Dbg(__file__, 'Create Callback Timer '.$function.' for with ScriptId='.$scriptId_Refresh);
			if (@IPS_GetObjectIDByIdent($function, $scriptId_Refresh)!==false) {
			   IPS_DeleteEvent(IPS_GetObjectIDByIdent($function, $scriptId_Refresh));
			}
			CreateTimer_OnceADay ($function, $scriptId_Refresh, (int)date("H", $date), (int)date("i", $date));
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function SetLimitedValues($NameLimits, $NameBegin, $NameEnd, $TimeStart, $TimeEnd, $categoryId_Values, $scriptId_Refresh) {
		CreateTimer($TimeStart, 'IPSTwilight_'.$NameBegin, $scriptId_Refresh);
		CreateTimer($TimeEnd,   'IPSTwilight_'.$NameEnd,   $scriptId_Refresh);

		$Limits = GetValue(IPS_GetVariableIDByName($NameLimits, $categoryId_Values));
		//                   01234567890123456789012
		// Format or Limits: xx:xx-xx:xx/yy:yy-yy:yy
		$TimeStartMin =  mktime(substr($Limits,0,2), substr($Limits,3,2), 0);
		$TimeStartMax =  mktime(substr($Limits,6,2), substr($Limits,9,2), 0);
		$TimeEndMin   =  mktime(substr($Limits,12,2), substr($Limits,15,2), 0);
		$TimeEndMax   =  mktime(substr($Limits,18,2), substr($Limits,21,2), 0);
		if ($TimeStart > $TimeStartMax) { $TimeStart= $TimeStartMax;}
		if ($TimeStart < $TimeStartMin) { $TimeStart= $TimeStartMin;}
		if ($TimeEnd > $TimeEndMax) { $TimeEnd= $TimeEndMax;}
		if ($TimeEnd < $TimeEndMin and date('H', $TimeEnd)=='00') { $TimeEnd= $TimeEndMax;}
		if ($TimeEnd < $TimeEndMin) { $TimeEnd= $TimeEndMin;}
		SetValue(IPS_GetVariableIDByName($NameBegin, $categoryId_Values), date("H:i",$TimeStart));
		SetValue(IPS_GetVariableIDByName($NameEnd,   $categoryId_Values), date("H:i",$TimeEnd));

		CreateTimer($TimeStart, 'IPSTwilight_'.str_replace('Limited', '', $NameBegin), $scriptId_Refresh);
		CreateTimer($TimeEnd,   'IPSTwilight_'.str_replace('Limited', '', $NameEnd),   $scriptId_Refresh);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function CalculateCurrentValues($categoryId_Values, $scriptId_Refresh) {
		IPSLogger_Trc(__file__, 'Calculate Sunrise for current Day');

		$timestamp               = time();
		$sunrise                 = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 90+50/60, date("O")/100);
		$sunset                  = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 90+50/60, date("O")/100);
		$civilTwilightStart      = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 96, date("O")/100);
		$civilTwilightEnd        = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 96, date("O")/100);
		$nauticTwilightStart     = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 102, date("O")/100);
		$nauticTwilightEnd       = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 102, date("O")/100);
		$astronomicTwilightStart = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 108, date("O")/100);
		$astronomicTwilightEnd   = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 108, date("O")/100);

		IPSLogger_Dbg (__file__, "Calculation of Sunrise: ".date("H:i", $sunrise).'  -  '.date("H:i", $sunset));
		IPSLogger_Dbg (__file__, "Calculation of CivilTwilight: ".date("H:i", $civilTwilightStart).' - '.date("H:i", $civilTwilightEnd));
		IPSLogger_Dbg (__file__, "Calculation of NauticTwilight: ".date("H:i", $nauticTwilightStart).' - '.date("H:i", $nauticTwilightEnd));
		IPSLogger_Dbg (__file__, "Calculation of AstronomicTwilight: ".date("H:i", $astronomicTwilightStart).' - '.date("H:i", $astronomicTwilightEnd));

		SetValue( IPS_GetVariableIDByName('SunriseBegin',   $categoryId_Values), date("H:i", $sunrise));
		SetValue( IPS_GetVariableIDByName('SunriseEnd',     $categoryId_Values), date("H:i", $sunset));
		SetValue( IPS_GetVariableIDByName('SunriseDisplay', $categoryId_Values), date("H:i", $sunrise).' - '.date("H:i",$sunset));
		SetLimitedValues('SunriseLimits', 'SunriseBeginLimited', 'SunriseEndLimited', $sunrise, $sunset, $categoryId_Values, $scriptId_Refresh);

		SetValue( IPS_GetVariableIDByName('CivilBegin',   $categoryId_Values), date("H:i", $civilTwilightStart));
		SetValue( IPS_GetVariableIDByName('CivilEnd',     $categoryId_Values), date("H:i", $civilTwilightEnd));
		SetValue( IPS_GetVariableIDByName('CivilDisplay', $categoryId_Values), date("H:i", $civilTwilightStart).' - '.date("H:i",$civilTwilightEnd));
		SetLimitedValues('CivilLimits', 'CivilBeginLimited', 'CivilEndLimited', $civilTwilightStart, $civilTwilightEnd, $categoryId_Values, $scriptId_Refresh);

		SetValue( IPS_GetVariableIDByName('NauticBegin',   $categoryId_Values), date("H:i", $nauticTwilightStart));
		SetValue( IPS_GetVariableIDByName('NauticEnd',     $categoryId_Values), date("H:i", $nauticTwilightEnd));
		SetValue( IPS_GetVariableIDByName('NauticDisplay', $categoryId_Values), date("H:i", $nauticTwilightStart).' - '.date("H:i",$nauticTwilightEnd));
		SetLimitedValues('NauticLimits', 'NauticBeginLimited', 'NauticEndLimited', $nauticTwilightStart, $nauticTwilightEnd, $categoryId_Values, $scriptId_Refresh);

		SetValue( IPS_GetVariableIDByName('AstronomicBegin',   $categoryId_Values), date("H:i", $astronomicTwilightStart));
		SetValue( IPS_GetVariableIDByName('AstronomicEnd',     $categoryId_Values), date("H:i", $astronomicTwilightEnd));
		SetValue( IPS_GetVariableIDByName('AstronomicDisplay', $categoryId_Values), date("H:i", $astronomicTwilightStart).' - '.date("H:i",$astronomicTwilightEnd));
		SetLimitedValues('AstronomicLimits', 'AstronomicBeginLimited', 'AstronomicEndLimited', $astronomicTwilightStart, $astronomicTwilightEnd, $categoryId_Values, $scriptId_Refresh);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function GenerateMondphase($fileName) {

	$file1 = "http://www.astronomie.info/observer/s_moon.jpg";
	$file2 = IPS_GetKernelDir()."\\media\\".$fileName.".jpg";
	$img = imagecreatefromjpeg($file1);
	$imagex=imagesx($img); // Lesen von X-Kordinaten
	$imagey=imagesy($img); // Lesen von Y-Kordinaten
	for($y=0;$y < $imagey;$y++){
		for($x=0; $x < $imagex; $x++){
		$rgb = imagecolorat($img, $x, $y);
		print $rgb."\n";
			if($rgb<0x020202){ // RGB-Wert von dem ROT
			imagesetpixel ($img, $x, $y, 0x14202b); // 0 = schwarz
			}
		}
	}
	//	header('Content-Type: image/jpeg');
	imagejpeg($img, $file2); // Bild erstellen
	imagedestroy($img);

	}
	// ----------------------------------------------------------------------------------------------------------------------------
	function GenerateClockGraphic($fileName, $useLimited=false, $Width=180) {
		$clockWidth   = $Width;
		$clockHeight  = $clockWidth;
		$marginLeft   = 20;
		$marginRight  = 20;
		$marginTop    = 15;
		$marginMiddle = 45;
		$marginBottom = 30;
		$imageWidth   = $clockWidth + $marginLeft + $marginRight;
		$imageHeight  = $clockHeight*2 + $marginBottom + $marginTop+ $marginMiddle;

		$image  = imagecreate($imageWidth,$imageHeight);

		$white         = imagecolorallocate($image,255,255,255);
		$textColor     = imagecolorallocate($image,250,250,250);
		$transparent   = imagecolortransparent($image,$white);
		$black         = imagecolorallocate($image,0,0,0);
		$red           = imagecolorallocate($image,255,0,0);
		$green         = imagecolorallocate($image,0,255,0);
		$blue          = imagecolorallocate($image,0,0,255);
		$grey_back     = imagecolorallocate($image, 100, 100, 100);
		$grey_line     = imagecolorallocate($image, 120, 120, 120);
		$grey_sunrise1 = imagecolorallocate($image, 200, 200, 200);
		$grey_sunrise2 = imagecolorallocate($image, 170, 170, 170);
		$grey_sunrise3 = imagecolorallocate($image, 140, 140, 140);
		$grey          = imagecolorallocate($image, 100, 100, 100);
		$yellow        = imagecolorallocate($image, 255, 255, 0);

		imagefilledrectangle($image,1,1,$imageWidth,$imageHeight,$transparent);

		$timestamp  = time();
		$sunrise    = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 90+50/60, date("O")/100);
		$sunset     = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 90+50/60, date("O")/100);
		$sunrise1   = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 96, date("O")/100);
		$sunset1    = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 96, date("O")/100);
		$sunrise2   = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 102, date("O")/100);
		$sunset2    = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 102, date("O")/100);
		$sunrise3   = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 108, date("O")/100);
		$sunset3    = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 108, date("O")/100);

		if ($useLimited ) {
			LimitValues('SunriseLimits', $sunrise, $sunset);
			LimitValues('CivilLimits', $sunrise1, $sunset1);
			LimitValues('NauticLimits', $sunrise2, $sunset2);
			LimitValues('AstronomicLimits', $sunrise3, $sunset3);
		}

/*		$sunriseMins  = (270+(date("H",$sunrise)*60  + date("i",$sunrise))*360/1440)%360;
		$sunsetMins   = (270+(date("H",$sunset)*60   + date("i",$sunset))*360/1440)%360;
		$sunrise1Mins = (270+(date("H",$sunrise1)*60 + date("i",$sunrise1))*360/1440)%360;
		$sunset1Mins  = (270+(date("H",$sunset1)*60  + date("i",$sunset1))*360/1440)%360;
		$sunrise2Mins = (270+(date("H",$sunrise2)*60 + date("i",$sunrise2))*360/1440)%360;
		$sunset2Mins  = (270+(date("H",$sunset2)*60  + date("i",$sunset2))*360/1440)%360;
		$sunrise3Mins = (270+(date("H",$sunrise3)*60 + date("i",$sunrise3))*360/1440)%360;
		$sunset3Mins  = (270+(date("H",$sunset3)*60  + date("i",$sunset3))*360/1440)%360;

		// 0h - 24h
      imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, 0, 360, $grey, IMG_ARC_PIE);
      imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise3Mins, $sunset3Mins, $grey_sunrise3, IMG_ARC_PIE);
      imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise2Mins, $sunset2Mins, $grey_sunrise2, IMG_ARC_PIE);
      imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise1Mins, $sunset1Mins, $grey_sunrise1, IMG_ARC_PIE);
      imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunriseMins,  $sunsetMins,  $yellow,        IMG_ARC_PIE);
*/

		$sunriseMins  = (270+(date("H",$sunrise)*60  + date("i",$sunrise))*360/720)%360;
		$sunsetMins   = (270+(date("H",$sunset)*60   + date("i",$sunset))*360/720)%360;
		$sunrise1Mins = (270+(date("H",$sunrise1)*60 + date("i",$sunrise1))*360/720)%360;
		$sunset1Mins  = (270+(date("H",$sunset1)*60  + date("i",$sunset1))*360/720)%360;
		$sunrise2Mins = (270+(date("H",$sunrise2)*60 + date("i",$sunrise2))*360/720)%360;
		$sunset2Mins  = (270+(date("H",$sunset2)*60  + date("i",$sunset2))*360/720)%360;
		$sunrise3Mins = (270+(date("H",$sunrise3)*60 + date("i",$sunrise3))*360/720)%360;
		$sunset3Mins  = (270+(date("H",$sunset3)*60  + date("i",$sunset3))*360/720)%360;
		$middayMins  = (12*60);

		// 0h - 12h
		imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth+2, $clockHeight+2, 0, 360, $grey_line, IMG_ARC_PIE);
		imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, 0, 360, $grey, IMG_ARC_PIE);

		if ((date("H",$sunset3)*60+date("i",$sunset3))<(date("H",$sunrise3)*60+date("i",$sunrise3)) or (date("H",$sunset3)*60+date("i",$sunset3))<$middayMins) {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise3Mins, 270, $grey_sunrise3, IMG_ARC_PIE);
		} else {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise3Mins, 270, $grey_sunrise3, IMG_ARC_PIE);
		}
		if ((date("H",$sunset2)*60+date("i",$sunset2))<(date("H",$sunrise2)*60+date("i",$sunrise2)) or (date("H",$sunset2)*60+date("i",$sunset2))<$middayMins) {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise2Mins, 270, $grey_sunrise2, IMG_ARC_PIE);
		} else {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise2Mins, 270, $grey_sunrise2, IMG_ARC_PIE);
		}
		if ((date("H",$sunset1)*60+date("i",$sunset1))<(date("H",$sunrise1)*60+date("i",$sunrise1)) or (date("H",$sunset1)*60+date("i",$sunset1))<$middayMins) {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise1Mins, 270, $grey_sunrise1, IMG_ARC_PIE);
		} else {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunrise1Mins, 270, $grey_sunrise1, IMG_ARC_PIE);
		}
		imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunriseMins,  270,  $yellow,        IMG_ARC_PIE);
		//imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$clockHeight/2, $clockWidth, $clockHeight, $sunriseMins,  $sunriseMins+1,  $red,        IMG_ARC_PIE);

		// 12h - 24h
		imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth+2, $clockHeight+2, 0, 360, $grey_line, IMG_ARC_PIE);
		imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, 0, 360, $grey, IMG_ARC_PIE);
		if ((date("H",$sunset3)*60+date("i",$sunset3))<(date("H",$sunrise3)*60+date("i",$sunrise3))) {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, $sunset3Mins,270, $grey_sunrise3, IMG_ARC_PIE);
		} else {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, 270, $sunset3Mins, $grey_sunrise3, IMG_ARC_PIE);
		}
		if ((date("H",$sunset2)*60+date("i",$sunset2))<(date("H",$sunrise2)*60+date("i",$sunrise2))) {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, $sunset2Mins,270, $grey_sunrise2, IMG_ARC_PIE);
		} else {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, 270, $sunset2Mins, $grey_sunrise2, IMG_ARC_PIE);
		}
		if ((date("H",$sunset1)*60+date("i",$sunset1))<(date("H",$sunrise1)*60+date("i",$sunrise1))) {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, $sunset1Mins,270, $grey_sunrise1, IMG_ARC_PIE);
		} else {
			imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, 270, $sunset1Mins, $grey_sunrise1, IMG_ARC_PIE);
		}
		imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, 270,  $sunsetMins,  $yellow,        IMG_ARC_PIE);
		//imagefilledarc($image, $marginLeft+$clockWidth/2, $marginTop+$marginMiddle+$clockHeight+$clockHeight/2, $clockWidth, $clockHeight, $sunsetMins,  $sunsetMins+1,  $red,        IMG_ARC_PIE);


		imagestring($image,2,$marginLeft+$clockWidth/2-3,$marginTop-14,"00",$textColor);
		imagestring($image,2,$marginLeft+$clockWidth/2-3,$marginTop+$clockHeight+2,"06",$textColor);
		imagestring($image,2,$marginLeft-14,             $marginTop+$clockHeight/2-6,"09",$textColor);
		imagestring($image,2,$marginLeft+$clockWidth+4,  $marginTop+$clockHeight/2-6,"03",$textColor);

		imagestring($image,2,$marginLeft+$clockWidth/2-3,$marginTop+$clockHeight+$marginMiddle-14,"24",$textColor);
		imagestring($image,2,$marginLeft+$clockWidth/2-3,$marginTop+$clockHeight*2+2+$marginMiddle,"18",$textColor);
		imagestring($image,2,$marginLeft-14,             $marginTop+$clockHeight+$marginMiddle+$clockHeight/2-6,"21",$textColor);
		imagestring($image,2,$marginLeft+$clockWidth+4,  $marginTop+$clockHeight+$marginMiddle+$clockHeight/2-6,"15",$textColor);


		imagesetthickness($image, 1);
		for ($alpha=0; $alpha<360; $alpha=$alpha+30) {
			imageline($image, $marginLeft+$clockWidth/2*(1+cos(deg2rad($alpha))),
			                  $marginTop+$clockWidth/2*(1+sin(deg2rad($alpha))),
									$marginLeft+10+($clockWidth-20)/2*(1+cos(deg2rad($alpha))),
									$marginTop+10+($clockWidth-20)/2*(1+sin(deg2rad($alpha))), $grey_line );

			imageline($image, $marginLeft+$clockWidth/2*(1+cos(deg2rad($alpha))),
			                  $marginTop+$clockHeight+$marginMiddle+$clockWidth/2*(1+sin(deg2rad($alpha))),
									$marginLeft+10+($clockWidth-20)/2*(1+cos(deg2rad($alpha))),
									$marginTop+$clockHeight+$marginMiddle+10+($clockWidth-20)/2*(1+sin(deg2rad($alpha))), $grey_line );

		}

		imagestring($image,1,10,$imageHeight-7,"Generated at ".date('d-M-Y H:i:s')."",$textColor);

		imagegif ($image, IPS_GetKernelDir().'media\\'.$fileName.'.gif', 90);
		imagedestroy($image);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function GenerateTwilightGraphic($fileName, $useLimited=false, $dayDivisor = 4.4, $dayWidth = 1.8) {
		$dayHeight    = 1440/$dayDivisor;     //24h*60Min=1440Min, 1440/4=360
		$marginLeft   = 20;
		$marginTop    = 5;
		$marginBottom = 30;
		$marginRight  = 5;
		$imageWidth   = (365+30)*$dayWidth+$marginLeft+$marginRight; // 365days, 2*365=730
		$imageHeight  = $dayHeight + $marginBottom + $marginTop;

		$image  = imagecreate($imageWidth,$imageHeight);

		$white         = imagecolorallocate($image,255,255,255);
		$textColor     = imagecolorallocate($image,250,250,250);
		$transparent   = imagecolortransparent($image,$white);
		$black         = imagecolorallocate($image,0,0,0);
		$red           = imagecolorallocate($image,255,0,0);
		$green         = imagecolorallocate($image,0,255,0);
		$blue          = imagecolorallocate($image,0,0,255);
		$grey_back     = imagecolorallocate($image, 100, 100, 100);
		$grey_line     = imagecolorallocate($image, 120, 120, 120);
		$grey_sunrise1 = imagecolorallocate($image, 200, 200, 200);
		$grey_sunrise2 = imagecolorallocate($image, 170, 170, 170);
		$grey_sunrise3 = imagecolorallocate($image, 140, 140, 140);
		$grey          = imagecolorallocate($image, 100, 100, 100);
		$yellow        = imagecolorallocate($image, 255, 255, 0);

		imagefilledrectangle($image,1,1,$imageWidth,$imageHeight,$transparent);
		imagefilledrectangle($image,$marginLeft-2,$marginTop-2,$marginLeft+(365+30)*$dayWidth+1,$marginTop+$dayHeight+2,$black);

		$timestamp  = mktime(12, 0, 0, 1, 1, date("Y"))-15*3600*24;
		for ($day=0; $day<365+30; $day++) {
			$sunrise     = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 90+50/60, date("O")/100);
			$sunset      = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 90+50/60, date("O")/100);
			$sunrise1    = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 96, date("O")/100);
			$sunset1     = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 96, date("O")/100);
			$sunrise2    = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 102, date("O")/100);
			$sunset2     = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 102, date("O")/100);
			$sunrise3    = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 108, date("O")/100);
			$sunset3     = date_sunset ($timestamp, SUNFUNCS_RET_TIMESTAMP, IPSTWILIGHT_LATITUDE, IPSTWILIGHT_LONGITUDE, 108, date("O")/100);

			if ($useLimited ) {
				LimitValues('SunriseLimits', $sunrise, $sunset);
				LimitValues('CivilLimits', $sunrise1, $sunset1);
				LimitValues('NauticLimits', $sunrise2, $sunset2);
				LimitValues('AstronomicLimits', $sunrise3, $sunset3);
			}

			$sunriseMins = (date("H",$sunrise)*60 + date("i",$sunrise)) / $dayDivisor;
			$sunsetMins  = (date("H",$sunset)*60 +  date("i",$sunset))  / $dayDivisor;
			$sunrise1Mins = (date("H",$sunrise1)*60 + date("i",$sunrise1)) / $dayDivisor;
			$sunset1Mins  = (date("H",$sunset1)*60 +  date("i",$sunset1))  / $dayDivisor;
			$sunrise2Mins = (date("H",$sunrise2)*60 + date("i",$sunrise2)) / $dayDivisor;
			$sunset2Mins  = (date("H",$sunset2)*60 +  date("i",$sunset2))  / $dayDivisor;
			$sunrise3Mins = (date("H",$sunrise3)*60 + date("i",$sunrise3)) / $dayDivisor;
			$sunset3Mins  = (date("H",$sunset3)*60 +  date("i",$sunset3))  / $dayDivisor;
			$middayMins  = (12*60) / $dayDivisor;

			$dayBeg = $marginLeft+$day*$dayWidth-$dayWidth+1;
			$dayEnd = $marginLeft+$day*$dayWidth;


			imagefilledrectangle($image, $dayBeg, $marginTop, $marginLeft+$day*$dayWidth, $marginTop+$dayHeight, $grey );
			if ($sunset3Mins<$sunrise3Mins or $sunset3Mins<$middayMins) {
				imagefilledrectangle($image, $dayBeg, $marginTop,                         $dayEnd, $marginTop+$dayHeight-$sunrise3Mins,  $grey_sunrise3);
				imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunset3Mins, $dayEnd, $marginTop+$dayHeight,                $grey_sunrise3);
			} else {
				imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunrise3Mins, $dayEnd, $marginTop+$dayHeight-$sunset3Mins,  $grey_sunrise3);
			}
			if ($sunset2Mins<$sunrise2Mins or $sunset2Mins<$middayMins) {
				imagefilledrectangle($image, $dayBeg, $marginTop,                         $dayEnd, $marginTop+$dayHeight-$sunrise2Mins,  $grey_sunrise2);
				imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunset2Mins, $dayEnd, $marginTop+$dayHeight,                $grey_sunrise2);
			} else {
				imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunrise2Mins, $dayEnd, $marginTop+$dayHeight-$sunset2Mins,  $grey_sunrise2);
			}
			if ($sunset1Mins<$sunrise1Mins or $sunset1Mins<$middayMins) {
				imagefilledrectangle($image, $dayBeg, $marginTop,                         $dayEnd, $marginTop+$dayHeight-$sunrise1Mins,  $grey_sunrise1);
				imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunset1Mins, $dayEnd, $marginTop+$dayHeight,                $grey_sunrise1);
			} else {
				imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunrise1Mins, $dayEnd, $marginTop+$dayHeight-$sunset1Mins,  $grey_sunrise1);
			}
			imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunriseMins,  $dayEnd, $marginTop+$dayHeight-$sunsetMins,  $yellow );
			imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunriseMins,  $dayEnd, $marginTop+$dayHeight-$sunriseMins, $red );
			imagefilledrectangle($image, $dayBeg, $marginTop+$dayHeight-$sunsetMins,   $dayEnd, $marginTop+$dayHeight-$sunsetMins,  $red );

			// Line for new Month
			if (date("d",$timestamp)==1) {
				imagefilledrectangle($image, $dayEnd, $marginTop, $dayEnd, $marginTop+$dayHeight, $grey_line );
				if ($day<365) {
					imagestring($image,2,$dayBeg+30*$dayWidth/2-8,$marginTop+$dayHeight+5,date('M',$timestamp),$textColor);
				}
			}
			// Line for current Day
			if (date("d",$timestamp)==date("d",time()) and date("m",$timestamp)==date("m",time())) {
				imagefilledrectangle($image, $dayBeg,   $marginTop, $dayEnd,   $marginTop+$dayHeight, $blue );
				imagefilledrectangle($image, $dayBeg-1, $marginTop, $dayEnd-1, $marginTop+$dayHeight, $blue );
			}
			$timestamp = $timestamp+60*60*24;
		}

		// Hour Lines/Text
		for ($hour=0; $hour<=24; $hour=$hour+2) {
			imageline($image, $marginLeft, $marginTop+$dayHeight/24*$hour, $marginLeft+(365+30)*$dayWidth-2, $marginTop+$dayHeight/24*$hour, $grey_line );
			imagestring($image,2,2,$marginTop+$dayHeight-8-($dayHeight/24*$hour),str_pad($hour,2,'0', STR_PAD_LEFT),$textColor);
		}

		//imagestring($image,3,$imageWidth/2-100,15,"Tag- und Nachtstunden in Korneuburg",$textColor);
		imagestring($image,1,10,$marginTop+$dayHeight+$marginBottom-7,"Generated at ".date('d-M-Y H:i:s')." by Brownson",$textColor);
		imagegif ($image, IPS_GetKernelDir().'media\\'.$fileName.'.gif', 90);
		imagedestroy($image);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function LimitValues($NameLimits, &$TimeStart, &$TimeEnd) {
		$categoryId_Values = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.Weather.IPSTwilight.Values');
		$Limits = GetValue(IPS_GetVariableIDByName($NameLimits, $categoryId_Values));

		//                   01234567890123456789012
		// Format or Limits: xx:xx-xx:xx/yy:yy-yy:yy
		$TimeStartMin =  mktime(substr($Limits,0,2),  substr($Limits,3,2), 0, date('m',$TimeStart), date('d',$TimeStart), date('Y',$TimeStart));
		$TimeStartMax =  mktime(substr($Limits,6,2),  substr($Limits,9,2), 0, date('m',$TimeStart), date('d',$TimeStart), date('Y',$TimeStart));
		$TimeEndMin   =  mktime(substr($Limits,12,2), substr($Limits,15,2),0, date('m',$TimeEnd),   date('d',$TimeEnd),   date('Y',$TimeEnd));
		$TimeEndMax   =  mktime(substr($Limits,18,2), substr($Limits,21,2),0, date('m',$TimeEnd),   date('d',$TimeEnd),   date('Y',$TimeEnd));
		if ($TimeStart > $TimeStartMax) { $TimeStart= $TimeStartMax;}
		if ($TimeStart < $TimeStartMin) { $TimeStart= $TimeStartMin;}
		if ($TimeEnd > $TimeEndMax) { $TimeEnd= $TimeEndMax;}
		if ($TimeEnd < $TimeEndMin and date('H', $TimeEnd)=='00') { $TimeEnd= $TimeEndMax;}
		if ($TimeEnd < $TimeEndMin and $TimeEnd > $TimeStart) { $TimeEnd= $TimeEndMin;}
	}

	/** @}*/
?>