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

	/**@defgroup ipswatering IPSWatering
	 * @ingroup modules
	 * @{
	 *
	 * IPSWatering ist ein IPS Modul, das die automatische Gartenbewässerung ermöglicht. 
	 * 
	 * In den Einstellungen gibt es die Möglichkeit die Automatik für jeden Kreis einzeln ein- oder auszuschalten. Eine manuelle Aktivierung 
	 * der Bewässerung ist jedoch jederzeit möglich. 
	 * 
	 * Die Beregnungsdauer und der Startzeitpunkt können für jeden Kreis getrennt eingestellt werden.  Die Frequenz der Beregnung wird über 
	 * das jeweilige Programm eingestellt. 
	 * 
	 * Zur Zeit werden folgende Programme unterstützt:
	 * - Manuell - Die Beregnung wird immer manuell gestartet
	 * - Jeden Tag
	 * - Jeden 2. Tag
	 * - Jeden 3. Tag
	 * - Montag, Mitwoch und Freitag
	 * - Montag und Donnerstag
	 * - Sonntags
	 * 
	 * Mit einem Regensensor ist es möglich, die regelmässige Bewässerung für Tage mit Regen zu unterbinden. Sollte der in der Konfiguration 
	 * angegebene Sensor eine Menge grösser/gleich der spezifizierten Menge melden, wird die Beregung ausgesetzt und autom. der nächste Zeitpunkt 
	 * laut Intervall berechnet.
	 *
	 * @file          IPSWatering.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 11.03.2012<br/>
	 *
	 */

	IPSUtils_Include ("IPSLogger.inc.php",                  "IPSLibrary::app::core::IPSLogger");
	IPSUtils_Include ("IPSInstaller.inc.php",               "IPSLibrary::install::IPSInstaller");
	IPSUtils_Include ("IPSComponent.class.php",             "IPSLibrary::app::core::IPSComponent");
	IPSUtils_Include ("IPSWatering_Constants.inc.php",      "IPSLibrary::app::modules::IPSWatering");
	IPSUtils_Include ("IPSWatering_Configuration.inc.php",  "IPSLibrary::config::modules::IPSWatering");
	IPSUtils_Include ("IPSWatering_Custom.inc.php",         "IPSLibrary::config::modules::IPSWatering");
	IPSUtils_Include ("IPSWatering_Logging.inc.php",        "IPSLibrary::app::modules::IPSWatering");

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWatering_ActivateWatering($CycleId, $Value, $Mode) {
		$WaterConfig      = get_WateringConfiguration();
		$CircleIdent      = IPS_GetName($CycleId);
		$ComponentParams  = $WaterConfig[$CircleIdent][c_Property_Component];

		if (!IPSWatering_BeforeActivateWatering($CycleId, $Value, $Mode)) {
			return false;
		}

		$DurationMin      = GetValue(get_ControlId(c_Control_Duration, $CycleId));
		$DurationSec      = 60*$DurationMin+5;
 
		$component = IPSComponent::CreateObjectByParams($ComponentParams);
		$component->SetState($Value, $DurationSec);

		IPSWatering_AfterActivateWatering($CycleId, $Value, $Mode);

		return true;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWatering_SetActive($ControlId, $Value, $Mode) {
	   $CircleId = get_CirclyIdByControlId($ControlId);
	   if (GetValue($ControlId) <> $Value) {
			if (IPSWatering_ActivateWatering($CircleId, $Value, $Mode)) {
				SetValue($ControlId, $Value);
				if ($Value) {
					IPSWatering_SetMode($CircleId, $Mode);
					SetValue(get_ControlId(c_Control_LastDate, $CircleId), date(c_Format_LastDate));
					SetValue(get_ControlId(c_Control_LastTime, $CircleId), date(c_Format_LastTime));
				} else {
				   IPSWatering_CalcNextScheduleDateTime($CircleId);
				}
				IPSWatering_LogActivate($CircleId, $Value, $Mode);
			} else {
			   IPSWatering_CalcNextScheduleDateTime($CircleId);
			}
			IPSWatering_ActivateRefreshTimer($Value);
		}
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWatering_CalcNextScheduleDateTime($CircleId) {
		$Automatic = GetValue(get_ControlId(c_Control_Automatic, $CircleId));
		SetValue(get_ControlId(c_Control_NextTime, $CircleId), GetValue(get_ControlId(c_Control_StartTime, $CircleId)));
		SetValue(get_ControlId(c_Control_NextDate, $CircleId), get_NextScheduledDate($CircleId));
		
		if (get_NextScheduledDate($CircleId)===false) {
			IPSWatering_SetMode($CircleId, c_Mode_AutomaticManual);
			IPSWatering_ActivateStartTimer($CircleId, false);
		} else if ($Automatic) {
			IPSWatering_SetMode($CircleId, c_Mode_AutomaticEnabled);
			IPSWatering_ActivateStartTimer($CircleId, true);
		} else {
			IPSWatering_SetMode($CircleId, c_Mode_AutomaticDisabled);
			IPSWatering_ActivateStartTimer($CircleId, false);
		}
		//IPSWatering_ActivateStartTimer($CircleId, $Automatic);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function	IPSWatering_ActivateStartTimer($CircleId, $Value=true) {
		$Name           = IPS_GetName($CircleId);
		$scriptId_Timer = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSWatering.IPSWatering_ActivationTimer');
		$TimerId        = @IPS_GetEventIDByName($Name, $scriptId_Timer);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetName($TimerId, $Name);
			IPS_SetParent($TimerId, $scriptId_Timer);
			if (!IPS_SetEventCyclic($TimerId, 1 /*Once*/, 1,0,0,0,0)) {
				IPSLogger_Err(__file__, "IPS_SetEventCyclic failed for WateringCircle '$Name' Timer !!!");
				exit;
			}
		}
		if ($Value) {
			$Time =get_DateTime(get_ControlId(c_Control_NextDate, $CircleId), c_Format_NextDate);
			if (!IPS_SetEventCyclicDateFrom ($TimerId, (int)date('d',$Time), (int)date('m',$Time), (int)date('Y',$Time))) {
				IPSLogger_Err(__file__, "IPS_SetEventCyclicDateFrom failed for WateringCircle '$Name' Timer !!!");
				exit;
			}
			$Time = GetValue(get_ControlId(c_Control_NextTime, $CircleId));
			if (!IPS_SetEventCyclicTimeFrom($TimerId, (int)substr($Time,0,2), (int)substr($Time,3,2), 0)) {
				IPSLogger_Err(__file__, "IPS_SetEventCyclicTimeFrom failed for WateringCircle '$Name' Timer !!!");
				exit;
			}
		}
		IPS_SetEventActive($TimerId, $Value);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function	IPSWatering_ActivateRefreshTimer($Value) {
		$Name    = 'Refresh';
		$scriptId_Timer = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSWatering.IPSWatering_RefreshTimer');
		$TimerId = @IPS_GetEventIDByName($Name, $scriptId_Timer);
		if ($TimerId === false) {
 			$TimerId = IPS_CreateEvent(1 /*Cyclic Event*/);
			IPS_SetName($TimerId, $Name);
			IPS_SetParent($TimerId, $scriptId_Timer);
			if (!IPS_SetEventCyclic($TimerId, 2 /*Daily*/, 1 /*Int*/,0 /*Days*/,0/*DayInt*/,1/*TimeType Sec*/,1/*Sec*/)) {
				IPSLogger_Err(__file__, "IPS_SetEventCyclic failed for Refresh Timer!!!");
				exit;
			}
		}

		if ($Value) {
			IPS_SetEventActive($TimerId, true);
		} else {
			$OneOrMoreCirclesActive = false;
			$categoryId_Circles = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWatering.WaterCircles');
			$CircleIds          = IPS_GetChildrenIds($categoryId_Circles);
				foreach($CircleIds as $CircleId) {
				$OneOrMoreCirclesActive = ($OneOrMoreCirclesActive or GetValue(get_ControlId(c_Control_Active, $CircleId)));
			}
			if (!$OneOrMoreCirclesActive) {
				IPS_SetEventActive($TimerId, false);
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWatering_SetMode($CircleId, $Mode) {
	   $ControlIdNextDisplay = get_ControlId(c_Control_NextDisplay, $CircleId);
	   $ControlIdDuration    = get_ControlId(c_Control_Duration, $CircleId);
	   $ControlIdToBeDone    = get_ControlId(c_Control_ToBeDone, $CircleId);

		switch ($Mode) {
		   case c_Mode_StartAutomatic:
		      SetValue($ControlIdNextDisplay, $Mode);
				SetValue($ControlIdToBeDone, $Mode.', '.GetValue($ControlIdDuration).' Minuten');
		      break;
		   case c_Mode_StartManual:
		      SetValue($ControlIdNextDisplay, $Mode);
				SetValue($ControlIdToBeDone, $Mode.', '.GetValue($ControlIdDuration).' Minuten');
		      break;
		   case c_Mode_AutomaticManual:
		   case c_Mode_AutomaticDisabled:
		      SetValue($ControlIdNextDisplay, $Mode);
				SetValue($ControlIdToBeDone, $Mode);
		      break;
		   case c_Mode_AutomaticEnabled:
		      $Date = get_DateTime(get_ControlId(c_Control_NextDate, $CircleId), c_Format_NextDate);
		      $Time = GetValue(get_ControlId(c_Control_NextTime, $CircleId));
		      SetValue($ControlIdNextDisplay, get_DateTranslation(date('l', $Date)).', '.$Time);
				SetValue($ControlIdToBeDone, get_DateTranslation(date('l', $Date)).', '.$Time.', '.GetValue($ControlIdDuration).' Minuten');
		      break;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
   function IPSWatering_SetAutomaticForAllCircles($Value) {
		$categoryId_Circles = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWatering.WaterCircles');
		$CircleIds          = IPS_GetChildrenIds($categoryId_Circles);
		foreach($CircleIds as $CircleId) {
			IPSWatering_SetValue(get_ControlId(c_Control_Automatic, $CircleId), $Value);
		}
   }

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWatering_SetValue($ControlId, $Value) {
		$CircleId = get_CirclyIdByControlId($ControlId);
		if (GetValue($ControlId)<>$Value) {
			IPSWatering_SetActive(get_ControlId(c_Control_Active, $CircleId), false, c_Mode_StartManual);

			SetValue($ControlId, $Value);
			IPSWatering_CalcNextScheduleDateTime($CircleId);
			IPSWatering_LogChange($CircleId, $Value, $ControlId);
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSWatering_Refresh() {
		$categoryId_Circles = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWatering.WaterCircles');
		$CircleIds          = IPS_GetChildrenIds($categoryId_Circles);
		foreach($CircleIds as $CircleId) {
			if (GetValue(get_ControlId(c_Control_Active, $CircleId))) {
				$Duration        = GetValue(get_ControlId(c_Control_Duration, $CircleId));
				$TimeCurrent     = time();
				$TimeStart       = get_DateTime(get_ControlId(c_Control_LastDate, $CircleId), c_Format_LastDate,
				                                get_ControlId(c_Control_LastTime, $CircleId), c_Format_LastTime);
				$TimeDiff        = $TimeCurrent-$TimeStart;
				$TimeDiffMinutes = floor($TimeDiff/60);
				$TimeDiffSeconds = $TimeDiff % 60;

				SetValue(get_ControlId(c_Control_ToBeDone, $CircleId),
				         GetValue(get_ControlId(c_Control_NextDisplay, $CircleId)).", $TimeDiffMinutes von $Duration Min ($TimeDiffSeconds Sek)");

				if (function_exists('IPSWatering_AfterRefresh')) {
					IPSWatering_AfterRefresh($CircleId, $Duration, $TimeDiffMinutes, $TimeDiffSeconds);
				}

				if ($TimeDiffMinutes >= $Duration) {
					IPSWatering_SetActive(get_ControlId(c_Control_Active, $CircleId),
												 false,
												 c_Mode_StartAutomatic);
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_DateTime($DateControlId, $DateFormat='Y.m.d', $TimeControlId=null, $TimeFormat='H:i') {
		$Value  = GetValue($DateControlId);
		$Format = $DateFormat;
		if ($TimeControlId<>null) {
			$Value  .= GetValue($TimeControlId);
			$Format .= $TimeFormat;
			}
	
		try {
			$datetime = DateTime::createFromFormat($Format, $Value);
			if ($datetime <> null) {
				return $datetime->getTimestamp();
			} else {
				IPSLogger_Err(__file__, "'$Value' could NOT be converted to DateTime using '$Format'");
				Exit;
			}
		} catch (Exception $exception) {
			IPSLogger_Err(__file__, "'$exception': '$Value' could NOT be converted to DateTime using '$Format'");
			Exit;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_NextScheduledDate($CircleId) {
		$ProgramId = GetValue(get_ControlId(c_Control_Program, $CircleId));
		$ControlIdLastDate = get_ControlId(c_Control_LastDate, $CircleId);
		$ControlIdNextTime = get_ControlId(c_Control_NextTime, $CircleId);
		switch ($ProgramId) {
			case c_ProgramId_EveryDay:
				$date = get_DateTime($ControlIdLastDate, c_Format_LastDate,
				                     $ControlIdNextTime, c_Format_NextTime)+1*24*60*60;
				break;
			case c_ProgramId_Every2Day:
				$date = get_DateTime($ControlIdLastDate, c_Format_LastDate,
				                     $ControlIdNextTime, c_Format_NextTime)+2*24*60*60;
				break;
			case c_ProgramId_Every3Day:
				$date = get_DateTime($ControlIdLastDate, c_Format_LastDate,
				                     $ControlIdNextTime, c_Format_NextTime)+3*24*60*60;
				break;
			case c_ProgramId_Every4Day:
				$date = get_DateTime($ControlIdLastDate, c_Format_LastDate,
				                     $ControlIdNextTime, c_Format_NextTime)+4*24*60*60;
				break;
			case c_ProgramId_MonWedFri:
				$date = strtotime('next monday');
				if (strtotime('next wednesday')<$date) { $date=strtotime('next wednesday');}
				if (strtotime('next friday')<$date) { $date=strtotime('next friday');}
				break;
			case c_ProgramId_MonTur:
				$date = strtotime('next monday');
				if (strtotime('next thursday')<$date) { $date=strtotime('next thursday');}
				break;
			case c_ProgramId_Sunday:
				$date = strtotime('next sunday');
				break;
			case c_ProgramId_Monday:
				$date = strtotime('next monday');
				break;
			case c_ProgramId_Tuesday:
				$date = strtotime('next tuesday');
				break;
			case c_ProgramId_Wednesday:
				$date = strtotime('next wednesday');
				break;
			case c_ProgramId_Thursday:
				$date = strtotime('next thursday');
				break;
			case c_ProgramId_Friday:
				$date = strtotime('next friday');
				break;
			case c_ProgramId_Saturday:
				$date = strtotime('next saturday');
				break;
			case c_ProgramId_Manual:
				return false;
			default:
			   IPSLogger_Err(__file__, "Unknown ProgramId $ProgramId");
			   Exit;
		}
		if ($date<=time()) {
			$date = strtotime('next day');
		}
		return Date(c_Format_NextDate, $date);
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_ControlType($ControlId) {
		$ControlName = IPS_GetName($ControlId);
		return $ControlName;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyIdByCircleIdent($CircleIdent, $ParentId=null) {
		if ($ParentId==null) {
			$ParentId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSWatering');
		}
		$CirclyId = IPS_GetCategoryIDByName($CircleIdent, $ParentId);

		return $CirclyId;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyNameByID($CircleId) {
	   $CircleItem       = IPS_GetName($CircleId);
		$WaterConfig      = get_WateringConfiguration();
		return $WaterConfig[$CircleItem][c_Property_Name];
	}


	// ----------------------------------------------------------------------------------------------------------------------------
	function get_CirclyIdByControlId($ControlId) {
		$CirclyId = IPS_GetParent($ControlId);
		if ($CirclyId === false) {
			IPSLogger_Err(__file__, "CircleId could NOT be found for ControlId=$ControlId");
			exit;
		}

		return $CirclyId;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_ControlId($ControlName, $CircleId) {
		$VariableId = IPS_GetVariableIDByName($ControlName, $CircleId);
		if ($VariableId === false) {
			IPSLogger_Err(__file__, "Control '$ControlName' could NOT be found for CircleId=$CircleId");
			exit;
		}
	   
		return $VariableId;
	}

	// ----------------------------------------------------------------------------------------------------------------------------
	function get_DateTranslation($DateItem) {
		$Translation = array(
		    'Monday'    => 'Montag',
		    'Tuesday'   => 'Dienstag',
		    'Wednesday' => 'Mittwoch',
		    'Thursday'  => 'Donnerstag',
		    'Friday'    => 'Freitag',
		    'Saturday'  => 'Samstag',
		    'Sunday'    => 'Sonntag',
		    'Mon'       => 'Mo',
		    'Tue'       => 'Di',
		    'Wed'       => 'Mi',
		    'Thu'       => 'Do',
		    'Fri'       => 'Fr',
		    'Sat'       => 'Sa',
		    'Sun'       => 'So',
		    'January'   => 'Januar',
		    'February'  => 'Februar',
		    'March'     => 'März',
		    'May'       => 'Mai',
		    'June'      => 'Juni',
		    'July'      => 'Juli',
		    'October'   => 'Oktober',
		    'December'  => 'Dezember',
		);
		return $Translation[$DateItem];
	}

	/** @}*/
?>