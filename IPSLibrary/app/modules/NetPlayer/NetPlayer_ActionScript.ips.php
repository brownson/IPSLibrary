<?
	/**@addtogroup netplayer
	 * @{
	 *
	 * @file          NetPlayer_ActionScript.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Action Script, um die Änderung diverser NetPlayer Variablen über das WebFront zu
	 * ermöglichen.
	 */

	include_once "NetPlayer.inc.php";

	if ($_IPS['SENDER'] == 'WebFront') {
	   $variable = $_IPS['VARIABLE'];
	   $value    = $_IPS['VALUE'];
		switch ($variable) {
		   case NP_ID_POWER:
				NetPlayer_Power($value);
		      break;
		   case NP_ID_CONTROL:
		      if ($value==NP_IDX_CONTROLPLAY) {
					NetPlayer_Power(true);
		      } elseif ($value==NP_IDX_CONTROLPAUSE) {
					NetPlayer_Pause();
		      } elseif ($value==NP_IDX_CONTROLSTOP) {
					NetPlayer_Power(false);
		      } elseif ($value==NP_IDX_CONTROLPREV) {
					NetPlayer_Prev();
		      } elseif ($value==NP_IDX_CONTROLNEXT) {
					NetPlayer_Next();
		      } else {
			   	IPSLogger_Err(__file__, 'Unknown ControlValue '.$value);
		      }
		      break;
		   case NP_ID_SOURCE:
		      NetPlayer_SetSource($value);
		      break;
		   case NP_ID_CATEGORYLIST:
		      if ($value==NP_IDX_CATEGORYROOT) {
					NetPlayer_SetCategory('');
		      } else {
					NetPlayer_SetCategory('_'.NetPlayer_GetNameFromProfile($value,'NetPlayer_Category'));
		      }
		      break;
		   case NP_ID_CDTRACKNAV:
				SetValue($variable, $value);
		      if ($value==NP_IDX_CDPREV) {
					NetPlayer_NavigateTrackBack(NP_COUNT_TRACKVARIABLE);
		      } else {
					NetPlayer_NavigateTrackForward(NP_COUNT_TRACKVARIABLE);
		      }
				IPS_SLEEP(200);
				SetValue($variable, -1);
		      break;
		   case NP_ID_CDTRACKLIST:
				NetPlayer_SetPlayListPosition($value+1);
		      break;
		   case NP_ID_CDALBUMNAV:
				SetValue($variable, $value);
		      if ($value==NP_IDX_CDPREV) {
					NetPlayer_NavigateCDBack(NP_COUNT_CDVARIABLE);
		      } else {
					NetPlayer_NavigateCDForward(NP_COUNT_CDVARIABLE);
		      }
				IPS_SLEEP(200);
				SetValue($variable, -1);
		      break;
		   case NP_ID_CDALBUMLIST:
				$directory     = NetPlayer_GetNameFromProfile($value,'NetPlayer_CDAlbumList');
				$basePath      = NETPLAYER_DIRECTORY;
				$categoryPath  = GetValue(NP_ID_CDCATEGORYNAME);
				if ($categoryPath<>"") $basePath = $basePath."/".$categoryPath;
				$directory = $basePath . "/" . $directory;
				NetPlayer_PlayDirectory($directory);
				break;
		   case NP_ID_RADIONAV:
				SetValue($variable, $value);
				if ($value==NP_IDX_RADIOPREV) {
					NetPlayer_NavigateRadioBack(NP_COUNT_RADIOVARIABLE);
				} else {
					NetPlayer_NavigateRadioForward(NP_COUNT_RADIOVARIABLE);
				}
				IPS_SLEEP(200);
				SetValue($variable, -1);
				break;
		   case NP_ID_RADIOLIST:
				$radioName = NetPlayer_GetNameFromProfile($value,'NetPlayer_RadioList');
				$radioList = NetPlayer_GetRadioList();
				$radioUrl  = $radioList[$radioName];
				NetPlayer_PlayRadio($radioUrl, $radioName);
				break;
			default:
			   IPSLogger_Err(__file__, 'Unknown ControlID '.$variable);
		}
	
	} elseif ($_IPS['SENDER'] == 'Execute') {
	}

	function NetPlayer_GetNameFromProfile($value, $name) {
      $profileData   = IPS_GetVariableProfile($name);
		$associations  = $profileData['Associations'];
		foreach ($associations as $idx=>$association) {
		   if ($association['Value']==$value) {
		      $name = $association['Name'];
		   }
		}
		return $name;
	}
	

  /** @}*/
?>