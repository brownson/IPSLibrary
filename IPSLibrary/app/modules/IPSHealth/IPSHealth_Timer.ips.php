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

	/**@addtogroup IPSHealth
	 * @{
	 *
	 * @file          IPSHealth_Timer.ips.php
	 * @author        André Czwalina
	 * @version
	* Version 1.00.0, 28.04.2012<br/>
	 *
	 *
	 */

	include_once "IPSHealth.inc.php";

	switch ($_IPS['SENDER']) {
		case 'TimerEvent':
			$eventId 	=  $_IPS['EVENT'];
			$strpos  	= strrpos(IPS_GetName($eventId), '-', 0);
			$CircleName = substr(IPS_GetName($eventId),0, $strpos);
			$EventMode 	= substr(IPS_GetName($eventId), $strpos+1, strlen(IPS_GetName($eventId))-$strpos-1);
			$Properts   = get_HealthConfiguration()[$CircleName];

			if (function_exists($CircleName)) {
				IPSLogger_Dbg(__file__, 'Health CallBack Funktion '.$CircleName.' Existiert in IPSHealth_Custom.');
				IPSHealth_Log($CircleName.' HealthCheck gestartet');

				$i=0;
				$r=0;
				$timeout = $Properts[c_HealthTimeout];
				foreach ($Properts[c_HealthVariables] as $ObjectID) {
					$Object = IPS_GetVariable($ObjectID);
					$lasttime = $Object['VariableUpdated'];
					$diff = (int)round(time() - $lasttime);
					$mld = 'OK';
					if ($diff > $timeout) {
						$mld = "Zeit ($diff Sek.) überschritten";
						IPSHealth_Log($CircleName." Variable: ".IPS_GetName($ObjectID)."($ObjectID),  Ergebnis: $mld");
						//IPSLogger_Err(__file__, $CircleName.",  Variable: ".IPS_GetName($ObjectID)."($ObjectID),  Zeit: $diff,  Ergebnis: $mld");
						$r++;
					}
					$i++;
				}
				if ($r == 0 and $i > 0) IPSHealth_Log($CircleName.' HealthCheck Fehlerfrei beendet');
				if ($i == 0) IPSHealth_Log($CircleName.' Keine Variablen zur Überwachung!');

			} else {

					IPSLogger_Err(__file__, "HealthCheck CallBack Funktion $CircleName in IPSHealth_Custom existiert nicht. Health: ".$Name);
			}
			break;
		case 'WebFront':
			break;
		case 'Execute':
			break;
		case 'RunScript':
			break;
		default:
			IPSLogger_Err(__file__, 'Unknown Sender '.$_IPS['SENDER']);
			break;
	}


	/** @}*/
?>