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

	/**@addtogroup ipspowercontrol
	 * @{
	 *
	 * @file          IPSPowerControl_Manager.class.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 29.09.2012<br/>
	 *
	 * IPSPowerControl Manager
	 */

	/**
	 * @class IPSPowerControl_Manager
	 *
	 * Definiert ein IPSPowerControl_Manager Objekt
	 *
	 * @author Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 26.07.2012<br/>
	 */
	class IPSPowerControl_Manager {

		/**
		 * @private
		 * ID Kategorie für die berechneten Werte
		 */
		private $categoryIdValues;

		/**
		 * @private
		 * ID Kategorie für allgemeine Steuerungs Daten
		 */
		private $categoryIdCommon;


		/**
		 * @private
		 * Konfigurations Daten Array der Sensoren
		 */
		private $sensorConfig;

		/**
		 * @private
		 * Konfigurations Daten Array der berechneten Werte
		 */
		private $valueConfig;

		/**
		 * @public
		 *
		 * Initialisierung des IPSPowerControl_Manager Objektes
		 *
		 */
		public function __construct() {
			$baseId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSPowerControl');
			$this->categoryIdValues   = IPS_GetObjectIDByIdent('Values', $baseId);
			$this->categoryIdCommon   = IPS_GetObjectIDByIdent('Common', $baseId);
			$this->sensorConfig       = IPSPowerControl_GetSensorConfiguration();
			$this->valueConfig        = IPSPowerControl_GetValueConfiguration();
		}

		/**
		 * @public
		 *
		 * Modifiziert einen Variablen Wert der Kamera Steuerung
		 *
		 * @param integer $variableId ID der Variable die geändert werden soll
		 * @param variant $value Neuer Wert der Variable
		 */
		public function ChangeSetting($variableId, $value) {
			$variableIdent = IPS_GetIdent($variableId);
			if (substr($variableIdent,0,-1)==IPSPC_VAR_SELECTVALUE) {
				$powerIdx      = substr($variableIdent,-1,-1);
				$variableIdent = substr($variableIdent,0,-1);
			}
			if (substr($variableIdent,0,-2)==IPSPC_VAR_SELECTVALUE) {
				$powerIdx      = substr($variableIdent,-1,-2);
				$variableIdent = substr($variableIdent,0,-2);
			}
			switch ($variableIdent) {
				case IPSPC_VAR_SELECTVALUE:
					SetValue($variableId, $value);
					$this->CheckValueSelection();
					$this->RebuildGraph();
					break;
				case IPSPC_VAR_TYPEOFFSET:
				case IPSPC_VAR_PERIODCOUNT:
					$this->Navigation($variableId, $value);
					$this->RebuildGraph();
					break;
				case IPSPC_VAR_VALUEKWH:
				case IPSPC_VAR_VALUEWATT:
				case IPSPC_VAR_CHARTHTML:
				case IPSPC_VAR_TIMEOFFSET:
				case IPSPC_VAR_TIMECOUNT:
					trigger_error('Variable'.$variableIdent.' could NOT be modified!!!');
					break;
				default:
					trigger_error('Unknown VariableID'.$variableId);
			}
		}

		private function CheckValueSelection() {
			$valueSelected = false;
			foreach ($this->valueConfig as $valueIdx=>$valueData) {
				if ($valueData[IPSPC_PROPERTY_DISPLAY]) {
					$variableIdValueDisplay = IPS_GetVariableIDByName(IPSPC_VAR_SELECTVALUE.$valueIdx, $this->categoryIdCommon);
					$valueSelected = ($valueSelected or GetValue($variableIdValueDisplay));
				}
			}
			if (!$valueSelected) {
				SetValue(IPS_GetVariableIDByName(IPSPC_VAR_SELECTVALUE.'0', $this->categoryIdCommon), true);

			}

		}

		private function Navigation($variableId, $value) {
			$lastValue = GetValue($variableId);
			$variableIdOffset = IPS_GetObjectIDByIdent(IPSPC_VAR_TIMEOFFSET, $this->categoryIdCommon);
			$variableIdCount  = IPS_GetObjectIDByIdent(IPSPC_VAR_TIMECOUNT,  $this->categoryIdCommon);
			SetValue($variableId, $value);
			$restoreOldValue = false;
			Switch($value) {
				case IPSPC_COUNT_MINUS:
					if (GetValue($variableIdCount) > 1) {
						SetValue($variableIdCount, GetValue($variableIdCount) - 1);
					}
					IPS_SetVariableProfileAssociation('IPSPowerControl_PeriodAndCount', IPSPC_COUNT_VALUE, GetValue($variableIdCount), "", -1);
					$restoreOldValue = true;
					break;
				case IPSPC_COUNT_PLUS:
					SetValue($variableIdCount, GetValue($variableIdCount) + 1);
					IPS_SetVariableProfileAssociation('IPSPowerControl_PeriodAndCount', IPSPC_COUNT_VALUE, GetValue($variableIdCount), "", -1);
					$restoreOldValue = true;
					break;
				case IPSPC_OFFSET_PREV:
					SetValue($variableIdOffset, GetValue($variableIdOffset) - 1);
					IPS_SetVariableProfileAssociation('IPSPowerControl_TypeAndOffset', IPSPC_OFFSET_VALUE, GetValue($variableIdOffset), "", -1);
					$restoreOldValue = true;
					break;
				case IPSPC_OFFSET_NEXT:
					if (GetValue($variableIdOffset) < 0) {
						SetValue($variableIdOffset, GetValue($variableIdOffset) + 1);
					}
					IPS_SetVariableProfileAssociation('IPSPowerControl_TypeAndOffset', IPSPC_OFFSET_VALUE, GetValue($variableIdOffset), "", -1);
					$restoreOldValue = true;
					break;
				case IPSPC_OFFSET_VALUE:
				case IPSPC_OFFSET_SEPARATOR:
				case IPSPC_COUNT_VALUE:
				case IPSPC_COUNT_SEPARATOR:
					SetValue($variableId, $lastValue);
					break;
				default:
					// other Values
			}
			if ($restoreOldValue)  {
				IPS_Sleep(200);
				SetValue($variableId, $lastValue);
			}
		}
		
		/**
		 * @public
		 *
		 * Diese Funktion wird beim Auslösen eines Timers aufgerufen
		 *
		 * @param integer $timerId ID des Timers
		 */
		public function ActivateTimer($timerId) {
			$timerName = IPS_GetName($timerId);
			switch($timerName) {
				case 'CalculateWattValues';
					$this->CalculateWattValues();
					break;
				case 'CalculateKWHValues';
					$this->CalculateKWHValues();
					break;
				default:
					trigger_error('Unknown Timer '.$timerName.'(ID='.$timerId.')');
			}
		}

		private function GetGraphStartTime() {
			$variableIdOffset  = IPS_GetObjectIDByIdent(IPSPC_VAR_TIMEOFFSET,  $this->categoryIdCommon);
			$variableIdCount   = IPS_GetObjectIDByIdent(IPSPC_VAR_TIMECOUNT,   $this->categoryIdCommon);
			$variableIdPeriod  = IPS_GetObjectIDByIdent(IPSPC_VAR_PERIODCOUNT, $this->categoryIdCommon);

			$offset = abs(GetValue($variableIdOffset));
			$count  = GetValue($variableIdCount);
			$return = mktime(0,0,0, date("m", time()), date("d",time()), date("Y",time())); 
			switch(GetValue($variableIdPeriod)) {
				case IPSPC_PERIOD_DAY:
					$return = strtotime('-'.($offset+$count-1).' day', $return);
					break;
				case IPSPC_PERIOD_WEEK:
					$return = strtotime('-'.($offset+$count).' week', $return);
					break;
				case IPSPC_PERIOD_MONTH:
					$return = strtotime('-'.($offset+$count).' month', $return);
					break;
				case IPSPC_PERIOD_YEAR:
					$return = strtotime('-'.($offset+$count).' year', $return);
					break;
				default:
					trigger_error('Unknown Period '.GetValue($variableIdPeriod));
			}
			return $return;
		}

		private function GetGraphEndTime() {
			$variableIdOffset  = IPS_GetObjectIDByIdent(IPSPC_VAR_TIMEOFFSET,  $this->categoryIdCommon);
			$variableIdCount   = IPS_GetObjectIDByIdent(IPSPC_VAR_TIMECOUNT,   $this->categoryIdCommon);
			$variableIdPeriod  = IPS_GetObjectIDByIdent(IPSPC_VAR_PERIODCOUNT, $this->categoryIdCommon);

			$offset=abs(GetValue($variableIdOffset));
			$return = mktime(23,59,59, date("m", time()), date("d",time()), date("Y",time())); 
			switch(GetValue($variableIdPeriod)) {
				case IPSPC_PERIOD_DAY:
					$return = strtotime('-'.($offset).' day', $return);
					break;
				case IPSPC_PERIOD_WEEK:
					$return = strtotime('-'.($offset).' week', $return);
					break;
				case IPSPC_PERIOD_MONTH:
					$return = strtotime('-'.($offset).' month', $return);
					break;
				case IPSPC_PERIOD_YEAR:
					$return = strtotime('-'.($offset).' year', $return);
					break;
				default:
					trigger_error('Unknown Period '.$GetValue($variableIdPeriod));
			}
			return $return;
		}

		private function RebuildGraph () {
			$variableIdChartType = IPS_GetObjectIDByIdent(IPSPC_VAR_TYPEOFFSET, $this->categoryIdCommon);
			$variableIdPeriod    = IPS_GetObjectIDByIdent(IPSPC_VAR_PERIODCOUNT, $this->categoryIdCommon);
			$variableIdChartHTML = IPS_GetObjectIDByIdent(IPSPC_VAR_CHARTHTML,  $this->categoryIdCommon);

			$periodList = array (IPSPC_PERIOD_DAY          => 'Tag',
			                     IPSPC_PERIOD_WEEK         => 'Woche',
			                     IPSPC_PERIOD_MONTH        => 'Monat',
			                     IPSPC_PERIOD_YEAR         => 'Jahr');

			$valueTypeList = array (IPSPC_TYPE_KWH         => 'kWh',
			                        IPSPC_TYPE_EURO        => 'Euro',
			                        IPSPC_TYPE_WATT        => 'Watt',
			                        IPSPC_TYPE_STACK       => 'Details',
			                        IPSPC_TYPE_STACK2      => 'Total',
			                        IPSPC_TYPE_OFF         => 'Off',
			                        IPSPC_TYPE_PIE         => 'Pie');

			if (!array_key_exists(GetValue($variableIdChartType), $valueTypeList)) {
				SetValue($variableIdChartType, IPSPC_TYPE_KWH);
			}
			if (!array_key_exists(GetValue($variableIdPeriod), $periodList)) {
				SetValue($variableIdPeriod, IPSPC_PERIOD_DAY);
			}

			$archiveHandlerList = IPS_GetInstanceListByModuleID ('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
			$archiveHandlerId   = $archiveHandlerList[0];
			$chartType          = GetValue($variableIdChartType);

			$CfgDaten['ContentVarableId'] = $variableIdChartHTML ;
			$CfgDaten['Ips']['ChartType'] = 'Highcharts'; // Highcharts oder Highstock (default = Highcharts)
			$CfgDaten['StartTime']        = $this->GetGraphStartTime();
			$CfgDaten['EndTime']          = $this->GetGraphEndTime();
			$CfgDaten['RunMode']          = "file"; 	// file, script, popup

			// Serienübergreifende Einstellung für das Laden von Werten
			$CfgDaten['AggregatedValues']['HourValues']     = -1;      // ist der Zeitraum größer als X Tage werden Stundenwerte geladen
			$CfgDaten['AggregatedValues']['DayValues']      = -1;      // ist der Zeitraum größer als X Tage werden Tageswerte geladen
			$CfgDaten['AggregatedValues']['WeekValues']     = -1;      // ist der Zeitraum größer als X Tage werden Wochenwerte geladen
			$CfgDaten['AggregatedValues']['MonthValues']    = -1;      // ist der Zeitraum größer als X Tage werden Monatswerte geladen
			$CfgDaten['AggregatedValues']['YearValues']     = -1;      // ist der Zeitraum größer als X Tage werden Jahreswerte geladen
			$CfgDaten['AggregatedValues']['NoLoggedValues'] = 1000;    // ist der Zeitraum größer als X Tage werden keine Boolean Werte mehr geladen, diese werden zuvor immer als Einzelwerte geladen	$CfgDaten['AggregatedValues']['MixedMode'] = false;     // alle Zeitraumbedingungen werden kombiniert
			$CfgDaten['AggregatedValues']['MixedMode']      = false;
			$CfgDaten['title']['text']    = "Energieverbrauch";
			$CfgDaten['subtitle']['text'] = "Zeitraum: %STARTTIME% - %ENDTIME%";
			$CfgDaten['subtitle']['Ips']['DateTimeFormat'] = "(D) d.m.Y H:i";
			$CfgDaten['HighChart']['Theme']  = "ips.js";
			$CfgDaten['HighChart']['Width']  = 0; 			// in px,  0 = 100%
			$CfgDaten['HighChart']['Height'] = 400; 		// in px

			switch (GetValue($variableIdPeriod)) {
				case IPSPC_PERIOD_DAY:   $aggType = 0; break;
				case IPSPC_PERIOD_WEEK:  $aggType = 1; break;
				case IPSPC_PERIOD_MONTH: $aggType = 1; break;
				case IPSPC_PERIOD_YEAR:  $aggType = 3; break;
				default:
				   trigger_error('Unknown Period '.GetValue($variableIdPeriod));
			}

			foreach ($this->valueConfig as $valueIdx=>$valueData) {
				$valueType = $valueData[IPSPC_PROPERTY_VALUETYPE];
				if ($valueData[IPSPC_PROPERTY_DISPLAY]) {
					$variableIdValueDisplay   = IPS_GetVariableIDByName(IPSPC_VAR_SELECTVALUE.$valueIdx, $this->categoryIdCommon);
					$variableIdValueKWH       = @IPS_GetVariableIDByName(IPSPC_VAR_VALUEKWH.$valueIdx,  $this->categoryIdValues);
					$variableIdValueWatt      = @IPS_GetVariableIDByName(IPSPC_VAR_VALUEWATT.$valueIdx, $this->categoryIdValues);
					$variableIdValueM3        = @IPS_GetVariableIDByName(IPSPC_VAR_VALUEM3.$valueIdx, $this->categoryIdValues);

					$serie = array();
					$serie['type']          = 'column';
					$serie['ReplaceValues'] = false;
					$serie['step']          = false;
					$serie['shadow']        = true;
					$serie['AggType']       = $aggType;
					$serie['AggValue']      = 'Avg';
					$serie['yAxis']         = 0;
					$serie['zIndex']        = 110;
					$serie['step']          = false;
					$serie['visible']       = true;
					$serie['showInLegend']  = true;
					$serie['allowDecimals'] = false;
					$serie['enableMouseTracking'] = true;
					$serie['states']['hover']['lineWidth'] = 2;
					$serie['marker']['enabled'] = false;
					$serie['marker']['states']['hover']['enabled']   = true;
					$serie['marker']['states']['hover']['symbol']    = 'circle';
					$serie['marker']['states']['hover']['radius']    = 4;
					$serie['marker']['states']['hover']['lineWidth'] = 1;

					switch ($chartType) {
						case IPSPC_TYPE_OFF:
							SetValue($variableIdChartHTML, '');
							return;
						case IPSPC_TYPE_STACK:
						case IPSPC_TYPE_STACK2:
							$serie['Unit']        = "kWh";
							$serie['ScaleFactor'] = 1;
							$serie['name']        = $valueData[IPSPC_PROPERTY_NAME];
							$serie['Id']          = $variableIdValueKWH;
							if ($variableIdValueKWH!==false and $valueData[IPSPC_PROPERTY_VALUETYPE]==IPSPC_VALUETYPE_TOTAL and $chartType==IPSPC_TYPE_STACK2) {
								$serie['zIndex']  = 100;
								$serie['stack']  = 'Total';
								$CfgDaten['series'][] = $serie;
							} elseif ($variableIdValueKWH!==false and $valueData[IPSPC_PROPERTY_VALUETYPE]==IPSPC_VALUETYPE_DETAIL) {
								$serie['zIndex']  = 110;
								$serie['stack']  = 'Detail';
								$CfgDaten['series'][] = $serie;
							} else {
							}
							$CfgDaten['yAxis'][0]['title']['text'] = "Verbrauch";
							$CfgDaten['yAxis'][0]['stackLabels']['enabled']    = true;
							$CfgDaten['yAxis'][0]['stackLabels']['formatter']  = "@function() { return this.total.toFixed(1) }@";
							$CfgDaten['yAxis'][0]['Unit'] = "kWh";
							$CfgDaten['plotOptions']['column']['stacking']     = "normal";
							$CfgDaten['plotOptions']['column']['borderColor']  = "#666666";
							$CfgDaten['plotOptions']['column']['borderWidth']  = 0;
							$CfgDaten['plotOptions']['column']['shadow']       = true;
 							break;
						case IPSPC_TYPE_PIE:
							$serie['type'] = 'pie';
							if ($variableIdValueKWH!==false and $valueData[IPSPC_PROPERTY_VALUETYPE]==IPSPC_VALUETYPE_DETAIL) {
								$data_array	= AC_GetAggregatedValues($archiveHandlerId, $variableIdValueKWH, $aggType, $CfgDaten["StartTime"],$CfgDaten["EndTime"], 100);
								$value=0;
								for($i=0;$i<count($data_array)-1;$i++) {
									$value = $value + round($data_array[$i]['Avg'], 1);
								}
								$CfgDaten['series'][0]['ScaleFactor'] = 1;
								$CfgDaten['series'][0]['name']        = 'Aufteilung';
								$CfgDaten['series'][0]['Unit'] = '';
								$CfgDaten['series'][0]['type'] = 'pie';
								$CfgDaten['series'][0]['data'][] = [$valueData[IPSPC_PROPERTY_NAME],   $value];
								$CfgDaten['series'][0]['allowPointSelect'] = true;
								$CfgDaten['series'][0]['cursor'] = 'pointer';
								$CfgDaten['series'][0]['size'] = 200;
								$CfgDaten['series'][0]['dataLabels']['enabled'] = true;
							}
							break;
						case IPSPC_TYPE_WATT:
							if ($variableIdValueWatt!==false and GetValue($variableIdValueDisplay)) {
								$serie['Unit']        = "Watt";
								$serie['ScaleFactor'] = 1;
								$serie['name']        = $valueData[IPSPC_PROPERTY_NAME];
								$serie['Id']          = $variableIdValueWatt;
								$CfgDaten['series'][] = $serie;
								$CfgDaten['yAxis'][0]['title']['text'] = "Verbrauch";
								$CfgDaten['yAxis'][0]['Unit'] = "Watt";
							} elseif ($variableIdValueWatt===false and GetValue($variableIdValueDisplay)) {
								SetValue($variableIdValueDisplay, false);
							}
							break;
						case IPSPC_TYPE_EURO:
						case IPSPC_TYPE_KWH:
							if (GetValue($variableIdValueDisplay)) {
								$serie['name']        = $valueData[IPSPC_PROPERTY_NAME];
								if ($valueType==IPSPC_VALUETYPE_GAS) {
									$yAxisText = ($chartType==IPSPC_TYPE_EURO)?"Euro":"Gas / Wasser";
									$yAxisIdx  = $this->GetYAxisIdx($CfgDaten, $yAxisText);
									$serie['Unit']        = ($chartType==IPSPC_TYPE_EURO)?"Euro":"m³";
									$serie['Id']          = $variableIdValueM3;
									$serie['ScaleFactor'] = ($chartType==IPSPC_TYPE_EURO)?(IPSPC_GASRATE_KWH*IPSPC_GASRATE_EURO/100):1;
									$serie['yAxis']       = $yAxisIdx;
									$CfgDaten['series'][] = $serie;
									$CfgDaten['yAxis'][$yAxisIdx]['title']['text'] = $yAxisText;
									$CfgDaten['yAxis'][$yAxisIdx]['Unit']          = $serie['Unit'];
									$CfgDaten['yAxis'][$yAxisIdx]['stackLabels']['enabled']    = true;
									$CfgDaten['yAxis'][$yAxisIdx]['stackLabels']['formatter']  = "@function() { return this.total.toFixed(1) }@";
								} elseif ($valueType==IPSPC_VALUETYPE_WATER) {
									$yAxisText = ($chartType==IPSPC_TYPE_EURO)?"Euro":"Gas / Wasser";
									$yAxisIdx  = $this->GetYAxisIdx($CfgDaten, $yAxisText);
									$serie['Unit']        = ($chartType==IPSPC_TYPE_EURO)?"Euro":"m³";
									$serie['Id']          = $variableIdValueM3;
									$serie['ScaleFactor'] = ($chartType==IPSPC_TYPE_EURO)?(IPSPC_WATERRATE/100):1;
									$serie['yAxis']       = $yAxisIdx;
									$CfgDaten['series'][] = $serie;
									$CfgDaten['yAxis'][$yAxisIdx]['title']['text'] = $yAxisText;
									$CfgDaten['yAxis'][$yAxisIdx]['Unit']          = $serie['Unit'];
									$CfgDaten['yAxis'][$yAxisIdx]['stackLabels']['enabled']    = true;
									$CfgDaten['yAxis'][$yAxisIdx]['stackLabels']['formatter']  = "@function() { return this.total.toFixed(1) }@";
								} else {
									$yAxisText = ($chartType==IPSPC_TYPE_EURO)?"Euro":"Strom";
									$yAxisIdx  = $this->GetYAxisIdx($CfgDaten, $yAxisText);
									$serie['Unit']        = ($chartType==IPSPC_TYPE_EURO)?"Euro":"kWh";
									$serie['Id']          = $variableIdValueKWH;
									$serie['ScaleFactor'] = ($chartType==IPSPC_TYPE_EURO)?(IPSPC_ELECTRICITYRATE/100):1;
									$serie['yAxis']       = $yAxisIdx;
									$CfgDaten['series'][] = $serie;
									$CfgDaten['yAxis'][$yAxisIdx]['title']['text'] = $yAxisText;
									$CfgDaten['yAxis'][$yAxisIdx]['Unit']          = $serie['Unit'];
									$CfgDaten['yAxis'][$yAxisIdx]['stackLabels']['enabled']    = true;
									$CfgDaten['yAxis'][$yAxisIdx]['stackLabels']['formatter']  = "@function() { return this.total.toFixed(1) }@";
								}
							}
							break;
						default:
					}
				}
			}
			if (!array_key_exists('series', $CfgDaten)) {
				SetValue($variableIdChartHTML, '');
				return;
			}

			// Create Chart with Config File
			IPSUtils_Include ("IPSHighcharts.inc.php", "IPSLibrary::app::modules::Charts::IPSHighcharts");
			$CfgDaten    = CheckCfgDaten($CfgDaten);
			$sConfig     = CreateConfigString($CfgDaten);            
			$tmpFilename = CreateConfigFile($sConfig, 'IPSPowerControl');    
			WriteContentWithFilename ($CfgDaten, $tmpFilename);      
		}

		private function GetYAxisIdx($CfgDaten, $text) {
			$maxIdx = -1;
			if (array_key_exists('yAxis', $CfgDaten)) {
				foreach ($CfgDaten['yAxis'] as $idx=>$data) {
					$maxIdx = $idx;
					if ($data['title']['text'] == $text) {
						return $idx;
					}
				}
			}
			return ($maxIdx+1);
		}
		
		private function CalculateKWHValues () {
			// Prepare Value Lists for Callback
			$sensorValuesKWH = array();
			$calcValuesKWH   = array();
			foreach ($this->sensorConfig as $sensorIdx=>$sensorData) {
				$sensorValue = 0;
				if (array_key_exists(IPSPC_PROPERTY_VARKWH, $sensorData) and $sensorData[IPSPC_PROPERTY_VARKWH] <> null) {
					$variableIdKWH = IPSUtil_ObjectIDByPath($sensorData[IPSPC_PROPERTY_VARKWH]);
					$sensorValue    = GetValue($variableIdKWH);
					//echo 'SensorValue'.$sensorIdx.' '.$variableIdKWH.'='.$sensorValue.PHP_EOL;
				} elseif (array_key_exists(IPSPC_PROPERTY_VARM3, $sensorData) and $sensorData[IPSPC_PROPERTY_VARM3] <> null) {
					$variableIdm3 = IPSUtil_ObjectIDByPath($sensorData[IPSPC_PROPERTY_VARM3]);
					$sensorValue    = GetValue($variableIdm3);
				}
				$sensorValuesKWH[$sensorIdx] = $sensorValue;
			}
			foreach ($this->valueConfig as $valueIdx=>$valueData) {
				$calcValuesKWH2[$valueIdx] = 0;
				$variableId = @IPS_GetObjectIDByIdent(IPSPC_VAR_VALUEKWH.$valueIdx, $this->categoryIdValues);
				if ($variableId!==false) {
					$calcValuesKWH2[$valueIdx] = GetValue($variableId);
				} else {
					$variableId = @IPS_GetObjectIDByIdent(IPSPC_VAR_VALUEM3.$valueIdx, $this->categoryIdValues);
					if ($variableId!==false) {
						$calcValuesKWH2[$valueIdx] = GetValue($variableId);
					}
				}
			}

			// Calculate Value
			$calcValuesKWH = IPSPowerControl_CalculateValuesKWH($sensorValuesKWH, $calcValuesKWH2);
			
			// Write Values
			foreach ($this->valueConfig as $valueIdx=>$valueData) {
				echo 'Write '.$valueData[IPSPC_PROPERTY_NAME].'='.$calcValuesKWH[$valueIdx].', Old='.$calcValuesKWH2[$valueIdx].', Diff='.($calcValuesKWH[$valueIdx]-$calcValuesKWH2[$valueIdx]).PHP_EOL;
				$variableId = @IPS_GetObjectIDByIdent(IPSPC_VAR_VALUEKWH.$valueIdx, $this->categoryIdValues);
				if ($variableId!==false) {
					SetValue($variableId, $calcValuesKWH[$valueIdx]);
				} else {
					$variableId = @IPS_GetObjectIDByIdent(IPSPC_VAR_VALUEM3.$valueIdx, $this->categoryIdValues);
					if ($variableId!==false) {
						SetValue($variableId, $calcValuesKWH[$valueIdx]);
					}
				}
			}
		}
		
		private function CalculateWattValues () {
			// Prepare Value Lists for Callback
			$sensorValuesWatt = array();
			$calcValuesWatt   = array();
			foreach ($this->sensorConfig as $sensorIdx=>$sensorData) {
				$sensorValue = 0;
				if (array_key_exists(IPSPC_PROPERTY_VARWATT, $sensorData) and $sensorData[IPSPC_PROPERTY_VARWATT] <> null) {
					$variableIdWatt = IPSUtil_ObjectIDByPath($sensorData[IPSPC_PROPERTY_VARWATT]);
					$sensorValue    = GetValue($variableIdWatt);
				}
				$sensorValuesWatt[$sensorIdx] = $sensorValue;
			}
			foreach ($this->valueConfig as $valueIdx=>$valueData) {
				$calcValuesWatt[$sensorIdx] = 0;
			}
			// Calculate Value
			$calcValuesWatt = IPSPowerControl_CalculateValuesWatt($sensorValuesWatt, $calcValuesWatt);
			// Write Values
			foreach ($this->valueConfig as $valueIdx=>$valueData) {
				$variableId = @IPS_GetObjectIDByIdent(IPSPC_VAR_VALUEWATT.$valueIdx, $this->categoryIdValues);
				if ($variableId!==false) {
					echo 'Write '.$variableId.'='.$calcValuesWatt[$valueIdx].', Name='.$valueData[IPSPC_PROPERTY_NAME].PHP_EOL;
					SetValue($variableId, $calcValuesWatt[$valueIdx]);
				}
			}
		}

	}

	/** @}*/
?>