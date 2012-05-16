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

	/**@addtogroup ipsshadowing
	 * @{
	 *
	 * @file          IPSShadowing_Azimuth.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 21.03.2012<br/>
	 *
	 * Berechnung/Grafik von Azimuth und Elevation 
	 *
	 */
	 
	IPSUtils_Include ("IPSLogger.inc.php",                      "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSComponent.class.php",                 "IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSShadowing_Constants.inc.php",         "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Configuration.inc.php",     "IPSLibrary::config::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Custom.inc.php",            "IPSLibrary::config::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Logging.inc.php",           "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Device.class.php",          "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_ProfileTemp.class.php",     "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_ProfileTime.class.php",     "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_ProfileManager.class.php",  "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_Scenario.class.php",        "IPSLibrary::app::modules::IPSShadowing");
	IPSUtils_Include ("IPSShadowing_ScenarioManager.class.php", "IPSLibrary::app::modules::IPSShadowing");

	//IPSShadowing_GenerateSunGraphic(time(), 150, 190,30);

	function IPSShadowing_GenerateSunGraphic($graphDate, $azimuthBgn=110, $azimuthEnd=220, $elevationLvl=20, $orientationSouth=false) {
		$longitude      = IPSSHADOWING_LONGITUDE;
		$latitude       = IPSSHADOWING_LATITUDE;
		$orientationDeg = IPSSHADOWING_BUILDINGORIENTATION;
		$relationDeg    = IPSSHADOWING_BUILDINGRELATION;

		$imageWidth   = 400;
		$imageHeight  = 450;

		$image         = imagecreate($imageWidth,$imageHeight);
		$white         = imagecolorallocate($image,255,255,255);
		$textColor     = imagecolorallocate($image,250,250,250);
		$transparent   = imagecolortransparent($image,$white);

		$elevationVOffset   = 110;
		$elevationSunRadio  = 10;
		$elevationFactor    = 1.5;
		$elevationColorAct  = imagecolorallocate($image, 255, 255, 0); // Yellow
		$elevationColorNor  = imagecolorallocate($image,  55,  55, 0);
		$elevationColorLine = imagecolorallocate($image, 100, 100, 100);
		$elevationColorText = imagecolorallocate($image, 200, 200, 200);

		$sunDegree      = $orientationSouth ? 0 : $orientationDeg;
		$sunOffsetH     = $imageWidth/2;
		$sunOffsetV     = $imageHeight/2+100;
		$sunRadius1     = $imageWidth/2-70;
		$sunRadius2     = $imageWidth/5;
		$sunColorAct    = imagecolorallocate($image, 255, 255, 0); // Yellow
		$sunColorEle    = imagecolorallocate($image, 200, 200, 200);
		$sunColorNor    = imagecolorallocate($image,  55,  55, 0);

		$buildingDeg1   = $orientationSouth ? 45 - $orientationDeg  : 45;
		$buildingDeg2   = $relationDeg;
		$buildingVOffset= $sunOffsetV;
		$buildingHOffset= $imageWidth/2;
		$buildingRadius = $imageWidth/7;
		$buildingColor  = imagecolorallocate($image, 100, 100, 100); // Grey

		// Prepare Elevation Display
		for ($idx=0;$idx<=60;$idx=$idx+10) {
			imageline($image, 20, $elevationVOffset-$idx*$elevationFactor, $imageWidth-10, $elevationVOffset-$idx*$elevationFactor, $elevationColorLine);
			imagestring($image,2,0,$elevationVOffset-$idx*$elevationFactor-7,$idx."°",$elevationColorText);
		}
		for ($hour=2;$hour<=22;$hour=$hour+2) {
			imageline($image, $imageWidth/24*$hour, $elevationVOffset+5, $imageWidth/24*$hour, $elevationVOffset-60*$elevationFactor-7, $elevationColorLine);
			imagestring($image,2,$imageWidth/24*$hour-5,$elevationVOffset+10,str_pad($hour, 2, "0", STR_PAD_LEFT),$elevationColorText);
		}

		for ($hour=0; $hour<24; $hour++) {
			$data      = Get_AnzimuatAndElevation(mktime($hour, 0, 0, date('n',$graphDate), date('d', $graphDate), date("Y",$graphDate)), $longitude, $latitude);
			$azimuth   = round($data['Azimuth']);
			$elevation = round($data['Elevation']);

			// Elevation
			// --------------------------------------------------------------------------------------------------------------------------
			if ($elevation >= -5) {
				$elevationColor = $elevationColorNor;
				if ($elevation>=$elevationLvl) {$elevationColor = $elevationColorAct;}
				imagefilledarc($image, $imageWidth/24*$hour, $elevationVOffset-$elevation*$elevationFactor, $elevationSunRadio, $elevationSunRadio, 0, 360, $elevationColor, IMG_ARC_PIE);
			}

			// Print Sun
			$sunColor = $sunColorNor;
			if ($azimuth>=$azimuthBgn and $azimuth<=$azimuthEnd) {
			   $sunColor = $sunColorAct;
			   if ($elevation < $elevationLvl) $sunColor = $sunColorEle;
			}

			// Azimut
			// --------------------------------------------------------------------------------------------------------------------------
			if ($elevation >=0) {
				$deg = 270-$azimuth-$sunDegree;
				$x1 = round(cos(($deg) * M_PI / 180) * $sunRadius1)+$sunOffsetH;
    			$y1 = round(sin(($deg) * M_PI / 180) * $sunRadius1);
				$x2 = round(cos(($deg+1) * M_PI / 180) * $sunRadius1)+$sunOffsetH;
    			$y2 = round(sin(($deg+1) * M_PI / 180) * $sunRadius1);
				$x3 = round(cos(($deg+1) * M_PI / 180) * $sunRadius2)+$sunOffsetH;
    			$y3 = round(sin(($deg+1) * M_PI / 180) * $sunRadius2);
				$x4 = round(cos(($deg) * M_PI / 180) * $sunRadius2)+$sunOffsetH;
    			$y4 = round(sin(($deg) * M_PI / 180) * $sunRadius2);
				$x5 = round(cos(($deg-1.5) * M_PI / 180) * ($sunRadius2+10))+$sunOffsetH;
    			$y5 = round(sin(($deg-1.5) * M_PI / 180) * ($sunRadius2+10));
				$x6 = round(cos(($deg+2.5) * M_PI / 180) * ($sunRadius2+10))+$sunOffsetH;
    			$y6 = round(sin(($deg+2.5) * M_PI / 180) * ($sunRadius2+10));

				imagefilledpolygon ($image ,array($x1,$sunOffsetV-$y1,$x2,$sunOffsetV-$y2,$x3,$sunOffsetV-$y3,$x4,$sunOffsetV-$y4), 4, $sunColor);
				imagefilledpolygon ($image ,array($x3,$sunOffsetV-$y3,$x4,$sunOffsetV-$y4,$x5,$sunOffsetV-$y5,$x6,$sunOffsetV-$y6), 4, $sunColor);

				$x3 = round(cos($deg * M_PI / 180) * $sunRadius1+9)+$sunOffsetH;
				$y3 = round(sin($deg * M_PI / 180) * $sunRadius1+9);
				if ($x3<=$imageWidth/2) {$x3=$x3-25;}
				imagestring($image,2,$x3,$sunOffsetV-$y3,str_pad($hour, 2, "0", STR_PAD_LEFT),$elevationColorText);
			}
		}

		for ($hour=9; $hour<17; $hour++) {
			$data      = Get_AnzimuatAndElevation(mktime($hour, 30, 0, date('n',$graphDate), date('d', $graphDate), date("Y",$graphDate)), $longitude, $latitude);
			$azimuth   = round($data['Azimuth']);
			$elevation = round($data['Elevation']);

			$sunColor = $sunColorNor;
			if ($azimuth>=$azimuthBgn and $azimuth<=$azimuthEnd) {
			   $sunColor = $sunColorAct;
			   if ($elevation < $elevationLvl) $sunColor = $sunColorEle;
			}
			if ($elevation >=10) {
				$deg = 270-$azimuth-$sunDegree;
				$x1 = round(cos(($deg) * M_PI / 180) * ($sunRadius1-10))+$sunOffsetH;
    			$y1 = round(sin(($deg) * M_PI / 180) * ($sunRadius1-10));
				$x2 = round(cos(($deg) * M_PI / 180) * ($sunRadius1-10))+$sunOffsetH;
    			$y2 = round(sin(($deg) * M_PI / 180) * ($sunRadius1-10));
				$x3 = round(cos(($deg) * M_PI / 180) * ($sunRadius2+10))+$sunOffsetH;
    			$y3 = round(sin(($deg) * M_PI / 180) * ($sunRadius2+10));
				$x4 = round(cos(($deg) * M_PI / 180) * ($sunRadius2+10))+$sunOffsetH;
    			$y4 = round(sin(($deg) * M_PI / 180) * ($sunRadius2+10));

				imageline($image, $x1,$sunOffsetV-$y1, $x3,$sunOffsetV-$y3, $sunColor);
			}
		}

		$x3 = round(cos((270-90-$sunDegree) * M_PI / 180) * ($sunRadius1+50))+$sunOffsetH;
		$y3 = round(sin((270-90-$sunDegree) * M_PI / 180) * ($sunRadius1+50));
		imagestring($image,4,$x3,$sunOffsetV-$y3,'Ost',$elevationColorText);

		$x3 = round(cos((270-180-$sunDegree) * M_PI / 180) * ($sunRadius1+25))+$sunOffsetH-15;
		$y3 = round(sin((270-180-$sunDegree) * M_PI / 180) * ($sunRadius1+25));
		imagestring($image,4,$x3,$sunOffsetV-$y3,'Süd',$elevationColorText);

		$x3 = round(cos((270-270-$sunDegree) * M_PI / 180) * ($sunRadius1+25))+$sunOffsetH;
		$y3 = round(sin((270-270-$sunDegree) * M_PI / 180) * ($sunRadius1+25));
		imagestring($image,4,$x3,$sunOffsetV-$y3,'West',$elevationColorText);


		// Print Building
		// --------------------------------------------------------------------------------------------------------------------------
		$points = array(
			round(cos(($buildingDeg1-$buildingDeg2) * M_PI / 180)                     * $buildingRadius)+$buildingHOffset,
			round(sin(($buildingDeg1-$buildingDeg2) * M_PI / 180)                     * $buildingRadius)+$buildingVOffset,
			round(cos(($buildingDeg1+90+$buildingDeg2) * M_PI / 180)  * $buildingRadius)+$buildingHOffset,
			round(sin(($buildingDeg1+90+$buildingDeg2) * M_PI / 180)  * $buildingRadius)+$buildingVOffset,
			round(cos(($buildingDeg1+180-$buildingDeg2) * M_PI / 180)               * $buildingRadius)+$buildingHOffset,
			round(sin(($buildingDeg1+180-$buildingDeg2) * M_PI / 180)               * $buildingRadius)+$buildingVOffset,
			round(cos(($buildingDeg1+270+$buildingDeg2) * M_PI / 180) * $buildingRadius)+$buildingHOffset,
			round(sin(($buildingDeg1+270+$buildingDeg2) * M_PI / 180) * $buildingRadius)+$buildingVOffset ,
			);
		imagefilledpolygon ($image ,$points, 4, $buildingColor);

		imagestring($image,4,5,$elevationVOffset+35, 'Datum: '.date('Y-m-d',$graphDate),$buildingColor);

		imagestring($image,2,5,$imageHeight-15, 'Latitude: '.round($latitude,2),$buildingColor);
		imagestring($image,2,5,$imageHeight-30, 'Longitude: '.round($longitude,2),$buildingColor);
		imagestring($image,2,$imageWidth/2+10,$imageHeight-15, 'Elevation aktuell: '.$elevation,$buildingColor);
		imagestring($image,2,$imageWidth/2+10,$imageHeight-30, 'Azimuth aktuell: '.$azimuth,$buildingColor);

		// Write File
		imagegif ($image, IPS_GetKernelDir().'media\\IPSShadowing_Azimuth.gif', 90);
		imagedestroy($image);
	}

	function IPSShadowing_GenerateSunGraphicOld($graphDate, $azimuthBgn=110, $azimuthEnd=220, $elevationLvl=20) {
		$longitude    = IPSSHADOWING_LONGITUDE;
		$latitude     = IPSSHADOWING_LATITUDE;

		$imageWidth   = 300;
		$imageHeight  = 400;
		
		$image         = imagecreate($imageWidth,$imageHeight);
		$white         = imagecolorallocate($image,255,255,255);
		$textColor     = imagecolorallocate($image,250,250,250);
		$transparent   = imagecolortransparent($image,$white);

		$elevationVOffset   = 110;
		$elevationSunRadio  = 10;
		$elevationFactor    = 1.5;
		$elevationColorAct  = imagecolorallocate($image, 255, 255, 0); // Yellow
		$elevationColorNor  = imagecolorallocate($image,  55,  55, 0); 
		$elevationColorLine = imagecolorallocate($image, 100, 100, 100);
		$elevationColorText = imagecolorallocate($image, 200, 200, 200);

		$sunOffsetH  = $imageWidth/2;
		$sunOffsetV  = $imageHeight/2+80;
		$sunRadius1 = $imageWidth/2-20;
		$sunRadius2 = $imageWidth/4;
		$sunColorAct = imagecolorallocate($image, 255, 255, 0); // Yellow
		$sunColorEle = imagecolorallocate($image, 200, 200, 200);
		$sunColorNor = imagecolorallocate($image,  55,  55, 0);

		$buildingDeg1=15;
		$buildingDeg2=20;
		$buildingVOffset=$sunOffsetV;
		$buildingHOffset=$imageWidth/2;
		$buildingRadius=$imageWidth/5;
		$buildingColor = imagecolorallocate($image, 100, 100, 100); // Grey

		// Prepare Elevation Display
		for ($idx=0;$idx<=60;$idx=$idx+10) {
			imageline($image, 20, $elevationVOffset-$idx*$elevationFactor, $imageWidth-10, $elevationVOffset-$idx*$elevationFactor, $elevationColorLine);
			imagestring($image,2,0,$elevationVOffset-$idx*$elevationFactor-7,$idx."°",$elevationColorText);
		}
		for ($hour=2;$hour<=22;$hour=$hour+2) {
			imageline($image, $imageWidth/24*$hour, $elevationVOffset+5, $imageWidth/24*$hour, $elevationVOffset-60*$elevationFactor-7, $elevationColorLine);
			imagestring($image,2,$imageWidth/24*$hour-5,$elevationVOffset+10,str_pad($hour, 2, "0", STR_PAD_LEFT),$elevationColorText);
		}

		for ($hour=0; $hour<24; $hour++) {
			$data      = Get_AnzimuatAndElevation(mktime($hour, 0, 0, date('n',$graphDate), date('d', $graphDate), date("Y",$graphDate)), $longitude, $latitude);
			$azimuth   = round($data['Azimuth']);
			$elevation = round($data['Elevation']);

			// Elevation
			// --------------------------------------------------------------------------------------------------------------------------
			if ($elevation >= -5) {
				$elevationColor = $elevationColorNor;
				if ($elevation>=$elevationLvl) {$elevationColor = $elevationColorAct;}
				imagefilledarc($image, $imageWidth/24*$hour, $elevationVOffset-$elevation*$elevationFactor, $elevationSunRadio, $elevationSunRadio, 0, 360, $elevationColor, IMG_ARC_PIE);
			}

			// Print Sun
			$sunColor = $sunColorNor;
			if ($azimuth>=$azimuthBgn and $azimuth<=$azimuthEnd) {
			   $sunColor = $sunColorAct;
			   if ($elevation < $elevationLvl) $sunColor = $sunColorEle;
			}

			// Azimut
			// --------------------------------------------------------------------------------------------------------------------------
			if ($elevation >=0) {
				$deg = 270-$azimuth;
				$x1 = round(cos(($deg) * M_PI / 180) * $sunRadius1)+$sunOffsetH;
    			$y1 = round(sin(($deg) * M_PI / 180) * $sunRadius1);
				$x2 = round(cos(($deg+1) * M_PI / 180) * $sunRadius1)+$sunOffsetH;
    			$y2 = round(sin(($deg+1) * M_PI / 180) * $sunRadius1);
				$x3 = round(cos(($deg+1) * M_PI / 180) * $sunRadius2)+$sunOffsetH;
    			$y3 = round(sin(($deg+1) * M_PI / 180) * $sunRadius2);
				$x4 = round(cos(($deg) * M_PI / 180) * $sunRadius2)+$sunOffsetH;
    			$y4 = round(sin(($deg) * M_PI / 180) * $sunRadius2);
				$x5 = round(cos(($deg-1.5) * M_PI / 180) * ($sunRadius2+10))+$sunOffsetH;
    			$y5 = round(sin(($deg-1.5) * M_PI / 180) * ($sunRadius2+10));
				$x6 = round(cos(($deg+2.5) * M_PI / 180) * ($sunRadius2+10))+$sunOffsetH;
    			$y6 = round(sin(($deg+2.5) * M_PI / 180) * ($sunRadius2+10));

				imagefilledpolygon ($image ,array($x1,$sunOffsetV-$y1,$x2,$sunOffsetV-$y2,$x3,$sunOffsetV-$y3,$x4,$sunOffsetV-$y4), 4, $sunColor);
				imagefilledpolygon ($image ,array($x3,$sunOffsetV-$y3,$x4,$sunOffsetV-$y4,$x5,$sunOffsetV-$y5,$x6,$sunOffsetV-$y6), 4, $sunColor);

				$x3 = round(cos($deg * M_PI / 180) * $sunRadius1+9)+$sunOffsetH;
				$y3 = round(sin($deg * M_PI / 180) * $sunRadius1+9);
				if ($x3<=$imageWidth/2) {$x3=$x3-25;}
				imagestring($image,2,$x3,$sunOffsetV-$y3,str_pad($hour, 2, "0", STR_PAD_LEFT),$elevationColorText);
			}
		}

		for ($hour=9; $hour<17; $hour++) {
			$data      = Get_AnzimuatAndElevation(mktime($hour, 30, 0, date('n',$graphDate), date('d', $graphDate), date("Y",$graphDate)), $longitude, $latitude);
			$azimuth   = round($data['Azimuth']);
			$elevation = round($data['Elevation']);

			$sunColor = $sunColorNor;
			if ($azimuth>=$azimuthBgn and $azimuth<=$azimuthEnd) {
			   $sunColor = $sunColorAct;
			   if ($elevation < $elevationLvl) $sunColor = $sunColorEle;
			}
			if ($elevation >=10) {
				$deg = 270-$azimuth;
				$x1 = round(cos(($deg) * M_PI / 180) * ($sunRadius1-10))+$sunOffsetH;
    			$y1 = round(sin(($deg) * M_PI / 180) * ($sunRadius1-10));
				$x2 = round(cos(($deg) * M_PI / 180) * ($sunRadius1-10))+$sunOffsetH;
    			$y2 = round(sin(($deg) * M_PI / 180) * ($sunRadius1-10));
				$x3 = round(cos(($deg) * M_PI / 180) * ($sunRadius2+10))+$sunOffsetH;
    			$y3 = round(sin(($deg) * M_PI / 180) * ($sunRadius2+10));
				$x4 = round(cos(($deg) * M_PI / 180) * ($sunRadius2+10))+$sunOffsetH;
    			$y4 = round(sin(($deg) * M_PI / 180) * ($sunRadius2+10));

				imageline($image, $x1,$sunOffsetV-$y1, $x3,$sunOffsetV-$y3, $sunColor);
			}
		}

			
		// Print Building
		// --------------------------------------------------------------------------------------------------------------------------
		$points = array(
			round(cos($buildingDeg1 * M_PI / 180)                     * $buildingRadius)+$buildingHOffset,
			round(sin($buildingDeg1 * M_PI / 180)                     * $buildingRadius)+$buildingVOffset,
			round(cos(($buildingDeg1+90+$buildingDeg2) * M_PI / 180)  * $buildingRadius)+$buildingHOffset,
			round(sin(($buildingDeg1+90+$buildingDeg2) * M_PI / 180)  * $buildingRadius)+$buildingVOffset,
			round(cos(($buildingDeg1+180) * M_PI / 180)               * $buildingRadius)+$buildingHOffset,
			round(sin(($buildingDeg1+180) * M_PI / 180)               * $buildingRadius)+$buildingVOffset,
			round(cos(($buildingDeg1+270+$buildingDeg2) * M_PI / 180) * $buildingRadius)+$buildingHOffset,
			round(sin(($buildingDeg1+270+$buildingDeg2) * M_PI / 180) * $buildingRadius)+$buildingVOffset ,
			);
		imagefilledpolygon ($image ,$points, 4, $buildingColor);

		imagestring($image,2,5,$imageHeight-15, 'Latitude: '.round($latitude,2),$buildingColor);
		imagestring($image,2,5,$imageHeight-30, 'Longitude: '.round($longitude,2),$buildingColor);
		imagestring($image,2,5,$elevationVOffset+20, 'Datum: '.date('Y-m-d',$graphDate),$buildingColor);

		imagestring($image,2,$imageWidth/2+10,$imageHeight-15, 'Elevation aktuell: '.$elevation,$buildingColor);
		imagestring($image,2,$imageWidth/2+10,$imageHeight-30, 'Azimuth aktuell: '.$azimuth,$buildingColor);

		// Write File
		imagegif ($image, IPS_GetKernelDir().'media\\IPSShadowing_Azimuth.gif', 90);
		imagedestroy($image);
	}

	function Get_AnzimuatAndElevation($time, $dLongitude, $dLatitude) {
		$dHours = gmdate('H', $time);
		$dMinutes = gmdate('i', $time);
		$dSeconds = gmdate('s', $time);
		$iYear = gmdate('Y', $time);
		$iMonth = gmdate('m', $time);
		$iDay = gmdate('d', $time);

		$pi = 3.14159265358979323846;
		$twopi = (2*$pi);
		$rad = ($pi/180);
		$dEarthMeanRadius = 6371.01;    // In km
		$dAstronomicalUnit = 149597890; // In km

		// Calculate difference in days between the current Julian Day
		// and JD 2451545.0, which is noon 1 January 2000 Universal Time
		// Calculate time of the day in UT decimal hours
		$dDecimalHours = floatval($dHours) + (floatval($dMinutes) + floatval($dSeconds) / 60.0 ) / 60.0;
		// Calculate current Julian Day
		$iYfrom2000 = $iYear;//expects now as YY ;
		$iA= (14 - ($iMonth)) / 12;
		$iM= ($iMonth) + 12 * $iA -3;
		$liAux3=(153 * $iM + 2)/5;
		$liAux4= 365 * ($iYfrom2000 - $iA);
		$liAux5= ( $iYfrom2000 - $iA)/4;
		$dElapsedJulianDays= floatval(($iDay + $liAux3 + $liAux4 + $liAux5 + 59)+ -0.5 + $dDecimalHours/24.0);

		// Calculate ecliptic coordinates (ecliptic longitude and obliquity of the
		// ecliptic in radians but without limiting the angle to be less than 2*Pi
		// (i.e., the result may be greater than 2*Pi)
		$dOmega= 2.1429 - 0.0010394594 * $dElapsedJulianDays;
		$dMeanLongitude = 4.8950630 + 0.017202791698 * $dElapsedJulianDays; // Radians
		$dMeanAnomaly = 6.2400600 + 0.0172019699 * $dElapsedJulianDays;
		$dEclipticLongitude = $dMeanLongitude + 0.03341607 * sin( $dMeanAnomaly ) + 0.00034894 * sin( 2 * $dMeanAnomaly ) -0.0001134 -0.0000203 * sin($dOmega);
		$dEclipticObliquity = 0.4090928 - 6.2140e-9 * $dElapsedJulianDays +0.0000396 * cos($dOmega);

		// Calculate celestial coordinates ( right ascension and declination ) in radians
		// but without limiting the angle to be less than 2*Pi (i.e., the result may be
		// greater than 2*Pi)
		$dSin_EclipticLongitude = sin( $dEclipticLongitude );
		$dY1 = cos( $dEclipticObliquity ) * $dSin_EclipticLongitude;
		$dX1 = cos( $dEclipticLongitude );
		$dRightAscension = atan2( $dY1,$dX1 );
		if( $dRightAscension < 0.0 ) $dRightAscension = $dRightAscension + $twopi;
		$dDeclination = asin( sin( $dEclipticObliquity )* $dSin_EclipticLongitude );

		// Calculate local coordinates ( azimuth and zenith angle ) in degrees
		$dGreenwichMeanSiderealTime = 6.6974243242 +    0.0657098283 * $dElapsedJulianDays + $dDecimalHours;
		$dLocalMeanSiderealTime = ($dGreenwichMeanSiderealTime*15 + $dLongitude)* $rad;
		$dHourAngle = $dLocalMeanSiderealTime - $dRightAscension;
		$dLatitudeInRadians = $dLatitude * $rad;
		$dCos_Latitude = cos( $dLatitudeInRadians );
		$dSin_Latitude = sin( $dLatitudeInRadians );
		$dCos_HourAngle= cos( $dHourAngle );
		$dZenithAngle = (acos( $dCos_Latitude * $dCos_HourAngle * cos($dDeclination) + sin( $dDeclination )* $dSin_Latitude));
		$dY = -sin( $dHourAngle );
		$dX = tan( $dDeclination )* $dCos_Latitude - $dSin_Latitude * $dCos_HourAngle;
		$dAzimuth = atan2( $dY, $dX );
		if ( $dAzimuth < 0.0 )
			$dAzimuth = $dAzimuth + $twopi;
		$dAzimuth = $dAzimuth / $rad;
		// Parallax Correction
		$dParallax = ($dEarthMeanRadius / $dAstronomicalUnit) * sin( $dZenithAngle);
		$dZenithAngle = ($dZenithAngle + $dParallax) / $rad;
		$dElevation = 90 - $dZenithAngle;
			
		$data=array();
		$data['Azimuth']   = $dAzimuth;
		$data['Elevation'] = $dElevation;
		return $data;
	}

	/** @}*/
?>