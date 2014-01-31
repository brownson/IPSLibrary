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

	/**@addtogroup ipscam
	 * @{
	 *
	 * @file          IPSCam_Manager.class.php
	 * @author        Andreas Brauneis
	 * @version
	 *  Version 2.50.1, 09.08.2012<br/>
	 *
	 * IPSCam Kamera Management
	 */

	/**
	 * @class IPSCam_Manager
	 *
	 * Definiert ein IPSCam_Manager Objekt
	 *
	 * @author Andreas Brauneis
	 * @version
	 *   Version 2.50.1, 26.07.2012<br/>
	 */
	class IPSCam_Manager {

		/**
		 * @private
		 * ID Kategorie für Kamera Daten
		 */
		private $categoryIdCams;

		/**
		 * @private
		 * ID Kategorie für allgemeine Steuerungs Daten
		 */
		private $categoryIdCommon;

		/**
		 * @private
		 * ID Kategorie WebFront Navigations Panel
		 */
		private $categoryIdNavPanel;

		/**
		 * @private
		 * ID Kategorie WebFront Camera Panel
		 */
		private $categoryIdCamPanel;

		/**
		 * @private
		 * Konfigurations Daten Array
		 */
		private $config;

		/**
		 * @public
		 *
		 * Initialisierung des IPSCam_Manager Objektes
		 *
		 */
		public function __construct() {
			$baseId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.data.modules.IPSCam');
			$displayCategoryId        = IPS_GetObjectIDByIdent('Display', $baseId);
			$this->categoryIdNavPanel = IPS_GetObjectIDByIdent('NavigationPanel', $displayCategoryId);
			$this->categoryIdCamPanel = IPS_GetObjectIDByIdent('CameraPanel', $displayCategoryId);
			$this->categoryIdCams     = IPS_GetObjectIDByIdent('Cams', $baseId);
			$this->categoryIdCommon   = IPS_GetObjectIDByIdent('Common', $baseId);
			$this->config             = IPSCam_GetConfiguration();
		}

		public function ActivateCamera($cameraIdx, $mode) {
			$this->SetCamera($cameraIdx);
			$this->SetMode($mode);
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
			switch ($variableIdent) {
				case IPSCAM_VAR_CAMSELECT:
					$this->SetCamera($value);
					break;
				case IPSCAM_VAR_CAMPOWER:
					$cameraIdx = (int)IPS_GetName(IPS_GetParent($variableId));
					$this->SetCamera($cameraIdx);
					break;
				case IPSCAM_VAR_MODE:
					$this->SetMode($value);
					break;
				case IPSCAM_VAR_SIZE:
					SetValue($variableId, $value);
					$mode = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MODE, $this->categoryIdCommon));
					$cameraIdx = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon));
					if ($mode==IPSCAM_MODE_PICTURE) {
						$this->PictureRefresh($cameraIdx);
					}
					$this->RefreshDisplay($cameraIdx);
					break;
				case IPSCAM_VAR_MODELIVE:
				case IPSCAM_VAR_MODEPICT:
				case IPSCAM_VAR_MODEHIST:
				case IPSCAM_VAR_MODESETT:
					if (!$value) {
						$this->SetMode(IPSCAM_MODE_PICTURE);
					} elseif ($variableIdent==IPSCAM_VAR_MODELIVE) {
						$this->SetMode(IPSCAM_MODE_LIVE);
					} elseif ($variableIdent==IPSCAM_VAR_MODEPICT) {
						$this->SetMode(IPSCAM_MODE_PICTURE);
					} elseif ($variableIdent==IPSCAM_VAR_MODEHIST) {
						$this->SetMode(IPSCAM_MODE_HISTORY);
					} elseif ($variableIdent==IPSCAM_VAR_MODESETT) {
						$this->SetMode(IPSCAM_MODE_SETTINGS);
					} else {
						trigger_error('Unknown Variable');
					}
					break;
				case IPSCAM_VAR_NAVPICT:
					SetValue($variableId, $value);
					$this->NavigatePictures($value, 1);
					IPS_Sleep(200);
					SetValue($variableId, -1);
					break;
				case IPSCAM_VAR_NAVDAYS:
					SetValue($variableId, $value);
					$this->NavigateDays($value, 1);
					IPS_Sleep(200);
					SetValue($variableId, -1);
					break;
				case IPSCAM_VAR_MOTMODE:
				case IPSCAM_VAR_MOTTIME:
					SetValue($variableId, $value);
					$categoryId = IPS_GetParent($variableId);
					$cameraIdx  = (int)IPS_GetName($categoryId);
					$this->SetTimer ($cameraIdx, 'PictureMotion', 
					                 GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTMODE, $categoryId)), 
					                 GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTTIME, $categoryId)));
					break;
				case IPSCAM_VAR_MOTHIST:
				case IPSCAM_VAR_MOTSIZE:
					SetValue($variableId, $value);
					break;
				case IPSCAM_VAR_PICTREF:
					SetValue($variableId, $value);
					$cameraIdx = (int)IPS_GetName(IPS_GetParent($variableId));
					$this->SetTimer ($cameraIdx, 'PictureRefresh', $value);
					break;
				case IPSCAM_VAR_PICTSTORE:
					SetValue($variableId, $value);
					$cameraIdx = (int)IPS_GetName(IPS_GetParent($variableId));
					$this->SetTimer ($cameraIdx, 'PictureStore', $value);
					break;
				case IPSCAM_VAR_PICTRESET:
					SetValue($variableId, $value);
					$cameraIdx = (int)IPS_GetName(IPS_GetParent($variableId));
					$this->SetTimer ($cameraIdx, 'PictureReset', $value);
					break;
				case IPSCAM_VAR_PICTHIST:
				case IPSCAM_VAR_PICTSIZE:
					SetValue($variableId, $value);
					break;
				default:
					trigger_error('Unknown VariableID'.$variableId);
			}
		}

		private function SetCamera ($cameraIdx) {
			$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
			if (GetValue($variableIdCamSelect)<>$cameraIdx) {
				SetValue($variableIdCamSelect, $cameraIdx);
				foreach ($this->config as $idx=>$data) {
					$categoryIdCam      = IPS_GetObjectIDByIdent($idx, $this->categoryIdCams);
					$variableIdCamPower = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMPOWER, $categoryIdCam);
					$valueCamPower      = ($idx==$cameraIdx);
					SetValue($variableIdCamPower, $valueCamPower);
				}
				$mode = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MODE, $this->categoryIdCommon));
				$this->RefreshDisplayByMode($mode);
				if ($mode==IPSCAM_MODE_HISTORY) {
					$this->NavigatePictures(IPSCAM_DAY_FORWARD, 1);
				} elseif ($mode==IPSCAM_MODE_PICTURE) {
					$this->PictureRefresh($cameraIdx);
				} else {
				}
				$this->RefreshDisplay($cameraIdx);
			}
		}

		private function SetMode ($mode) {
			$variableIdMode = IPS_GetObjectIDByIdent(IPSCAM_VAR_MODE, $this->categoryIdCommon);
			if (GetValue($variableIdMode)<>$mode) {
				SetValue($variableIdMode, $mode);
				$variableIdMode = IPS_GetObjectIDByIdent(IPSCAM_VAR_MODELIVE, $this->categoryIdCommon);
				SetValue($variableIdMode, ($mode==IPSCAM_MODE_LIVE));
				$variableIdMode = IPS_GetObjectIDByIdent(IPSCAM_VAR_MODEPICT, $this->categoryIdCommon);
				SetValue($variableIdMode, ($mode==IPSCAM_MODE_PICTURE));
				$variableIdMode = IPS_GetObjectIDByIdent(IPSCAM_VAR_MODEHIST, $this->categoryIdCommon);
				SetValue($variableIdMode, ($mode==IPSCAM_MODE_HISTORY));
				$variableIdMode = IPS_GetObjectIDByIdent(IPSCAM_VAR_MODESETT, $this->categoryIdCommon);
				SetValue($variableIdMode, ($mode==IPSCAM_MODE_SETTINGS));

				//Reset Timers
				$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
				$cameraIdx           = GetValue($variableIdCamSelect);
				$categoryIdCam       = IPS_GetObjectIDByIdent($cameraIdx, $this->categoryIdCams);
				$this->SetTimer ($cameraIdx, 'PictureReset', IPSCAM_VAL_DISABLED);
				$this->SetTimer ($cameraIdx, 'PictureReset', GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTRESET, $categoryIdCam)), 'Once');

				// Refresh 
				$this->RefreshDisplayByMode($mode);
				$this->RefreshDisplay($cameraIdx);
			}
		}

		
		private function RefreshDisplay($cameraIdx) {
			$mode            = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MODE, $this->categoryIdCommon));
			$variableIdHtml  = IPS_GetObjectIDByIdent(IPSCAM_VAR_HTML, $this->categoryIdCommon);
			$variableIdHtml2 = IPS_GetObjectIDByIdent(IPSCAM_VAR_IHTML, $this->categoryIdCommon);

			$urlStream  = $this->GetURL($cameraIdx, IPSCAM_URL_LIVE);
			$urlImage   = $this->GetURL($cameraIdx, IPSCAM_URL_PICTURE);
			
			
			$styleImage     = 'text-align:center; border:3px solid rgba(255,255,255,0.5);';
			$styleContainer = 'text-align:center; width:100%; display:block; cursor:default;';
			$styleImage2     = 'text-align:center; width:95%; border:3px solid rgba(255,255,255,0.5);';
			$styleContainer2 = 'text-align:center; display:block; cursor:default;';

			switch ($mode) {
				case IPSCAM_MODE_LIVE:
					$variableIdCam = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMHTML, $this->categoryIdCommon);
					SetValue($variableIdCam,  '<iframe frameborder="0" width="100%" height="'.$this->GetStreamHeight($cameraIdx).'px"  src="../user/IPSCam/IPSCam_Camera.php"</iframe>');
					SetValue($variableIdHtml, '<div style="'.$styleContainer.'"><img style="'.$styleImage.'" src="'.$urlStream.'"></div>');
					SetValue($variableIdHtml2,'<div style="'.$styleContainer2.'"><img style="'.$styleImage2.'" src="'.$urlStream.'"></div>');
					break;
				case IPSCAM_MODE_PICTURE:
					SetValue($variableIdHtml, '<div style="'.$styleContainer.'"><img style="'.$styleImage.'" src="/user/IPSCam/ImageCurrent.jpg" timestamp="'.date('His').'"></div>');
					SetValue($variableIdHtml2,'<div style="'.$styleContainer2.'"><img style="'.$styleImage2.'" src="/user/IPSCam/ImageCurrent.jpg" timestamp="'.date('His').'"></div>');
					break;
				case IPSCAM_MODE_HISTORY:
					SetValue($variableIdHtml, '<div style="'.$styleContainer.'"><img style="'.$styleImage.'" src="/user/IPSCam/ImageHistory.jpg" timestamp="'.date('His').'"></div>');
					SetValue($variableIdHtml2,'<div style="'.$styleContainer2.'"><img style="'.$styleImage2.'" src="/user/IPSCam/ImageHistory.jpg" timestamp="'.date('His').'"></div>');
					break;
				case IPSCAM_MODE_SETTINGS:
					break;
				default:
					trigger_error('Unknown Mode '.$mode);
			}

		}

		private function RefreshDisplayByMode($mode) {
			$mode      = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MODE, $this->categoryIdCommon));
			$cameraIdx = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon));

			$instanceIdMode       = IPS_GetObjectIDByIdent('Modus', $this->categoryIdNavPanel);
			$instanceIdNavigation = IPS_GetObjectIDByIdent('Navigation', $this->categoryIdNavPanel);
			$instanceIdCamSettings= IPS_GetObjectIDByIdent('KameraEinstellungen', $this->categoryIdNavPanel);
			$instanceIdPicture    = IPS_GetObjectIDByIdent('Bild', $this->categoryIdNavPanel);
			$instanceIdPower      = IPS_GetObjectIDByIdent('Power', $this->categoryIdNavPanel);

			$instanceIdCameraHtml = IPS_GetObjectIDByName('Kamera', $this->categoryIdCamPanel);
			$instanceIdCameraPict = IPS_GetObjectIDByName('Bild', $this->categoryIdCamPanel);
			$instanceIdCameraHist = IPS_GetObjectIDByName('History', $this->categoryIdCamPanel);
			$instanceIdSettings   = IPS_GetObjectIDByIdent('Einstellungen', $this->categoryIdCamPanel);

			$linkIdPower     = IPS_GetObjectIDByName('Power', $instanceIdPower);
			$variableIdPower = '';
			if (array_key_exists(IPSCAM_PROPERTY_SWITCHPOWER, $this->config[$cameraIdx])) { 
				$variableIdPower = $this->config[$cameraIdx][IPSCAM_PROPERTY_SWITCHPOWER];
				IPS_SetHidden($linkIdPower, ($variableIdPower==''));
				if ($variableIdPower<>'') {
					$variableIdPower = IPSUtil_ObjectIDByPath($variableIdPower);
					IPS_SetLinkTargetID($linkIdPower, $variableIdPower);
				}
			}
			$linkIdWLAN = IPS_GetObjectIDByName('WLAN', $instanceIdPower);
			$variableIdWLAN = '';
			if (array_key_exists(IPSCAM_PROPERTY_SWITCHWLAN, $this->config[$cameraIdx])) { 
				$variableIdWLAN = $this->config[$cameraIdx][IPSCAM_PROPERTY_SWITCHWLAN];
				IPS_SetHidden($linkIdWLAN, ($variableIdWLAN==''));
				if ($variableIdWLAN<>'') {
					$variableIdWLAN = IPSUtil_ObjectIDByPath($variableIdWLAN);
					IPS_SetLinkTargetID($linkIdWLAN, $variableIdWLAN);
				}
			}

			switch ($mode) {
				case IPSCAM_MODE_LIVE:
					IPS_SetHidden($instanceIdNavigation, true);
					IPS_SetHidden($instanceIdPicture,    false);
					IPS_SetHidden($instanceIdPower,      ($variableIdWLAN=='' and $variableIdPower==''));
					IPS_SetHidden($instanceIdCamSettings,false);
					IPS_SetHidden($instanceIdCameraHtml, false);
					IPS_SetHidden($instanceIdCameraPict, true);
					IPS_SetHidden($instanceIdCameraHist, true);
					IPS_SetHidden($instanceIdSettings,   true);
					break;
				case IPSCAM_MODE_PICTURE:
					IPS_SetHidden($instanceIdNavigation, true);
					IPS_SetHidden($instanceIdPicture,    false);
					IPS_SetHidden($instanceIdPower,      ($variableIdWLAN=='' and $variableIdPower==''));
					IPS_SetHidden($instanceIdCamSettings,false);
					IPS_SetHidden($instanceIdCameraHtml, true);
					IPS_SetHidden($instanceIdCameraPict, false);
					IPS_SetHidden($instanceIdCameraHist, true);
					IPS_SetHidden($instanceIdSettings,   true);
					break;
				case IPSCAM_MODE_HISTORY:
					IPS_SetHidden($instanceIdNavigation, false);
					IPS_SetHidden($instanceIdPicture,    false);
					IPS_SetHidden($instanceIdPower,      true);
					IPS_SetHidden($instanceIdCamSettings,true);
					IPS_SetHidden($instanceIdCameraHtml, true);
					IPS_SetHidden($instanceIdCameraPict, true);
					IPS_SetHidden($instanceIdCameraHist, false);
					IPS_SetHidden($instanceIdSettings,   true);
					break;
				case IPSCAM_MODE_SETTINGS:
					IPS_SetHidden($instanceIdNavigation, true);
					IPS_SetHidden($instanceIdPicture,    true);
					IPS_SetHidden($instanceIdPower,      true);
					IPS_SetHidden($instanceIdCamSettings,true);
					IPS_SetHidden($instanceIdCameraHtml, true);
					IPS_SetHidden($instanceIdCameraPict, true);
					IPS_SetHidden($instanceIdCameraHist, true);
					IPS_SetHidden($instanceIdSettings,   false);
					break;
				default:
					trigger_error('Unknown Mode '.$mode);
			}
			
			IPSUtils_Include ('IPSInstaller.inc.php', 'IPSLibrary::install::IPSInstaller');
			$categoryIdCam      = IPS_GetObjectIDByIdent($cameraIdx, $this->categoryIdCams);
			CreateLink('Bild Aktualisierung',    IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTREF, $categoryIdCam), $instanceIdSettings, 10);
			CreateLink('Autom. Bild Speicherung',IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTSTORE, $categoryIdCam), $instanceIdSettings, 20);
			CreateLink('Aktivierung Bild Modus', IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTRESET, $categoryIdCam), $instanceIdSettings, 30);
			CreateLink('Bild Historisierung',    IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTHIST, $categoryIdCam), $instanceIdSettings, 40);
			CreateLink('Bildgröße',              IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTSIZE, $categoryIdCam), $instanceIdSettings, 50);
			CreateLink('Zeitraffer Modus',       IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTMODE, $categoryIdCam), $instanceIdSettings, 60);
			CreateLink('Zeitraffer Abstand',     IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTTIME, $categoryIdCam), $instanceIdSettings, 70);
			CreateLink('Zeitraffer Zeitraum',    IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTHIST, $categoryIdCam), $instanceIdSettings, 80);
			CreateLink('Zeitraffer Bildgröße',   IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTSIZE, $categoryIdCam), $instanceIdSettings, 90);
		}


		private function SetTimer ($cameraIdx, $timerPrefix, $timerValue, $startTime=null) {
			$timerName = $timerPrefix.'_'.$cameraIdx;
			$scriptId = IPSUtil_ObjectIDByPath('Program.IPSLibrary.app.modules.IPSCam.IPSCam_ActionScript');
			$timerId = @IPS_GetObjectIDByIdent($timerName, $scriptId);
			if ($timerValue==IPSCAM_VAL_DISABLED) {
				if ($timerId!==false) {
					IPS_DeleteEvent($timerId);
				}
			} else {
				if ($timerId === false) {
					$timerId = IPS_CreateEvent(1 /*Cyclic Event*/);
					IPS_SetParent($timerId, $scriptId);
					IPS_SetName($timerId, $timerName);
					IPS_SetIdent($timerId, $timerName);
				}
				$hours = (int)($timerValue / (60*60));
				$mins  = (int)($timerValue / 60);
				$secs  = $timerValue;
				if ($startTime=='Once') {
					$nextTime = strtotime('+'.$timerValue.' sec');
					IPS_SetEventCyclic($timerId, 2 /**Daily*/, 1 /*Unused*/,0 /*Unused*/,0 /*Unused*/,0 /*Einmalig*/,0 /*Unused*/);
					IPS_SetEventCyclicTimeBounds($timerId, mktime(date('H',$nextTime), date('i',$nextTime), date('s',$nextTime)), 0);
				} elseif ($hours==24) {
					$startTime = explode(':', $startTime);
					IPS_SetEventCyclic($timerId, 2 /**Daily*/, 1 /*Unused*/,0 /*Unused*/,0 /*Unused*/,0 /*Einmalig*/,0 /*Unused*/);
					IPS_SetEventCyclicTimeBounds($timerId, mktime($startTime[0], $startTime[1], 0), 0);
				} elseif ($hours > 0) {
					IPS_SetEventCyclic($timerId, 2 /*Daily*/, 1 /*Unused*/,0 /*Unused*/,0 /*Unused*/,3 /*TimeType Hours*/,$hours /*Stunden*/);
				} elseif ($mins > 0) {
					IPS_SetEventCyclic($timerId, 2 /*Daily*/, 1 /*Unused*/,0 /*Unused*/,0 /*Unused*/,2 /*TimeType Minutes*/,$mins /*Minutes*/);
				} else {
					IPS_SetEventCyclic($timerId, 2 /*Daily*/, 1 /*Unused*/,0 /*Unused*/,0 /*Unused*/,1 /*TimeType Sec*/, $secs /*Sec*/);
				}
				IPS_SetEventActive($timerId, true);
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
			$timerNames = explode('_', $timerName);
			if (count($timerNames) < 2 ) { trigger_error('Unknown Timer '.$timerName.'(ID='.$timerId.')'); }
			$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
			$variableIdMode      = IPS_GetObjectIDByIdent(IPSCAM_VAR_MODE, $this->categoryIdCommon);
			$cameraIdx = $timerNames[1];
			switch($timerNames[0]) {
				case 'PictureRefresh';
					if (GetValue($variableIdMode)==IPSCAM_MODE_PICTURE and GetValue($variableIdCamSelect)==$cameraIdx) {
						$this->PictureRefresh($cameraIdx);
					}
					break;
				case 'PictureStore';
					$this->PictureStore($cameraIdx);
					break;
				case 'PictureReset';
					if (GetValue($variableIdMode)<>IPSCAM_MODE_PICTURE and GetValue($variableIdCamSelect)==$cameraIdx) {
						$this->PictureReset($cameraIdx);
					}
					break;
				case 'PictureMotion';
					$this->PictureMotion($cameraIdx);
					break;
				default:
					trigger_error('Unknown Timer '.$timerName.'(ID='.$timerId.')');
			}
		}

		/**
		 * @public
		 *
		 * Aktuallisiert das angezeigte Bild
		 *
		 * @param integer $cameraIdx Index der Kamera
		 */
		public function PictureRefresh($cameraIdx=null) {
			$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
			// If CameraIndex is NULL use selected
			if ($cameraIdx===null) {
				$cameraIdx = GetValue($variableIdCamSelect);
			}
			// if Camera is NOT selected - Set Selected
			if (GetValue($variableIdCamSelect) <> $cameraIdx) {
				$this->SetCamera($cameraIdx);
			}
			// Switch to Image Mode
			$variableIdMode      = IPS_GetObjectIDByIdent(IPSCAM_VAR_MODE, $this->categoryIdCommon);
			if (GetValue($variableIdMode) == IPSCAM_MODE_PICTURE) {
				$size = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_SIZE, $this->categoryIdCommon));
				$this->StorePicture($cameraIdx, 'Picture', $size, 'Current', 'Common');
			}

			// Set Media File for Common View
			$variableIdMedia = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMPICT, $this->categoryIdCommon);
			IPS_SetMediaFile($variableIdMedia, IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\Picture\\CommonDummy.jpg', false);
			IPS_SetMediaFile($variableIdMedia, IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\Picture\\Common.jpg', false);

			// Copy Image to webfront
			Copy (IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\Picture\\Common.jpg',
			      IPS_GetKernelDir().'\\webfront\\user\\IPSCam\\ImageCurrent.jpg');

			// Set Media File for Camera View
			$categoryIdCam   = IPS_GetObjectIDByIdent($cameraIdx, $this->categoryIdCams);
			$variableIdMedia = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMPICT, $categoryIdCam);
			IPS_SetMediaFile($variableIdMedia, IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\Picture\\CurrentDummy.jpg', false);
			IPS_SetMediaFile($variableIdMedia, IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\Picture\\Current.jpg', false);

			$this->RefreshDisplay($cameraIdx);
		}

		/**
		 * @public
		 *
		 * Speichert das aktuelle Bild in die History
		 *
		 * @param integer $cameraIdx Index der Kamera
		 */
		public function PictureStore($cameraIdx=null) {
			$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
			if ($cameraIdx==null) {
				$cameraIdx = GetValue($variableIdCamSelect);
			}
			//if (GetValue($variableIdCamSelect) <> $cameraIdx) {
			//	$this->SetCamera($cameraIdx);
			//}
			$categoryIdCam = IPS_GetObjectIDByIdent($cameraIdx, $this->categoryIdCams);
			$size          = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTSIZE, $categoryIdCam));
			if (IPSCam_BeforeStorePicture($cameraIdx)) {
				$this->StorePicture($cameraIdx, 'History', $size);
				IPSCam_AfterStorePicture($cameraIdx);
			}
		}

		private function IsCameraAvailable($cameraIdx=null) {
			if ($cameraIdx==0) {
				$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
				$cameraIdx = GetValue($variableIdCamSelect);
			}
			$cameraAvailable = true;
			if (array_key_exists(IPSCAM_PROPERTY_SWITCHPOWER, $this->config[$cameraIdx])) { 
				$variableIdPower = $this->config[$cameraIdx][IPSCAM_PROPERTY_SWITCHPOWER];
				if ($variableIdPower<>'') {
					$variableIdPower = IPSUtil_ObjectIDByPath($variableIdPower);
					$cameraAvailable = ($cameraAvailable and GetValue($variableIdPower));
				}
			}
			if (array_key_exists(IPSCAM_PROPERTY_SWITCHWLAN, $this->config[$cameraIdx])) { 
				$variableIdWLAN = $this->config[$cameraIdx][IPSCAM_PROPERTY_SWITCHWLAN];
				if ($variableIdWLAN<>'') {
					$variableIdWLAN = IPSUtil_ObjectIDByPath($variableIdWLAN);
					$cameraAvailable = ($cameraAvailable and GetValue($variableIdWLAN));
				}
			}
			return $cameraAvailable;
		}
		
		private function PictureReset($cameraIdx) {
			$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
			if ($cameraIdx==0) {
				$cameraIdx = GetValue($variableIdCamSelect);
			}
			if (GetValue($variableIdCamSelect) <> $cameraIdx) {
				$this->SetCamera($cameraIdx);
			}
			$this->SetMode (IPSCAM_MODE_PICTURE);
		}

		private function PictureMotion($cameraIdx) {
			$categoryIdCam = IPS_GetObjectIDByIdent($cameraIdx, $this->categoryIdCams);
			$size          = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTSIZE, $categoryIdCam));
			if (IPSCam_BeforeStoreMotion($cameraIdx)) {
				$this->StorePicture($cameraIdx, 'MotionCapture', $size);
				IPSCam_AfterStoreMotion($cameraIdx);
			}
		}

		private function StorePicture($cameraIdx, $directoryName, $size, $fileName=null, $fileName2=null) {
			if (!$this->IsCameraAvailable($cameraIdx)) {
				return;
			}
			$componentParams = $this->config[$cameraIdx][IPSCAM_PROPERTY_COMPONENT];
			$component       = IPSComponent::CreateObjectByParams($componentParams);
			$urlPicture      = $component->Get_URLPicture($size);
			if ($fileName == null) {
				$fileName = date(IPSCAM_NAV_DATEFORMATFILE);
			}
			$localFile        = IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\'.$directoryName.'\\'.$fileName.'.jpg';
			$localFile2       = IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\'.$directoryName.'\\'.$fileName2.'.jpg';
			IPSLogger_Trc(__file__, "Copy $urlPicture --> $localFile");

			$curl_handle=curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $urlPicture);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_handle, CURLOPT_FAILONERROR, true);
			$fileContent = curl_exec($curl_handle);
			if ($fileContent===false) {
				IPSLogger_Dbg (__file__, 'File '.$urlPicture.' could NOT be found on the Server !!!');
				return;
			}
			curl_close($curl_handle);

			$result = file_put_contents($localFile, $fileContent);
			if ($result===false) {
				trigger_error('Error writing File Content to '.$localFile);
			}
			if ($fileName2!==null) {
				$result = file_put_contents($localFile2, $fileContent);
				if ($result===false) {
					trigger_error('Error writing File Content to '.$localFile2);
				}
			}
		}

		private function NavigatePictures($direction, $count) {
			$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
			$cameraIdx  = GetValue($variableIdCamSelect);
			$directory  = IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\History\\';
			
			$variableIdNavFile = IPS_GetObjectIDByIdent(IPSCAM_VAR_NAVFILE, $this->categoryIdCommon);
			$variableIdNavTime = IPS_GetObjectIDByIdent(IPSCAM_VAR_NAVTIME, $this->categoryIdCommon);
			$navTime    = GetValue($variableIdNavFile);
			$navPos     = -1;

			$fileList2   = scandir($directory, 0);
			$fileList2   = array_diff($fileList2, Array('.','..'));
			$fileList2   = explode('|',implode('|',$fileList2));

			$fileList    = array();
			foreach($fileList2 as $idx=>$file) {
				$fileExt  = pathinfo($file, PATHINFO_EXTENSION);
				if ($fileExt=='jpg') {
					$fileList[] = $file;
				}
			}
			
			foreach($fileList as $idx=>$file) {
				$filename = basename($file);
				if ($filename==$navTime.'.jpg') {
					$navPos=$idx;
				} elseif ($filename>=$navTime.'.jpg' and $navPos==-1) {
					$navPos=$idx;
				} else {
				}
			}
			
			if ($navPos==-1) {
				$navPos=count($fileList)-1;
			} elseif ($direction==IPSCAM_NAV_BACK and $navPos > 0) {
				$navPos=$navPos-1;
			} elseif ($direction==IPSCAM_NAV_FORWARD and ($navPos+1)<count($fileList)) {
				$navPos=$navPos+1;
			} else {
			}

			if ($navPos<>-1) {
				$navFile = $fileList[$navPos];
				$navFile = str_replace('.jpg', '', $navFile);
				SetValue($variableIdNavFile, $navFile);
				$navTime = mktime(substr($navFile,9,2),  substr($navFile,11,2), substr($navFile,13,2), substr($navFile,4,2), substr($navFile,6,2), substr($navFile,0,4));
				SetValue($variableIdNavTime, date(IPSCAM_NAV_DATEFORMATDISP, $navTime));

			   $variableIdMedia   = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMHIST, $this->categoryIdCommon);
			   $mediaFileName     = IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\History\\'.$navFile.'.jpg';
			   $userFileName      = IPS_GetKernelDir().'\\WebFront\\User\\IPSCam\\ImageHistory.jpg';
			   IPS_SetMediaFile($variableIdMedia, $mediaFileName, false);
			   copy ($mediaFileName, $userFileName);
			}

			$this->RefreshDisplay($cameraIdx);
		}

		private function NavigateDays($direction, $count) {
			$variableIdNavFile   = IPS_GetObjectIDByIdent(IPSCAM_VAR_NAVFILE, $this->categoryIdCommon);
			$variableIdNavTime   = IPS_GetObjectIDByIdent(IPSCAM_VAR_NAVTIME, $this->categoryIdCommon);
			//012345678901234
			//YYYYMMDD_HHMISS
			$navTime = GetValue($variableIdNavFile);
			$navTime = mktime(substr($navTime,9,2),  substr($navTime,11,2), substr($navTime,13,2), substr($navTime,4,2), substr($navTime,6,2), substr($navTime,0,4));
			if ($direction==IPSCAM_DAY_BACK) {
				$navTime = strtotime('-'.$count.' day', $navTime);
			} elseif ($direction==IPSCAM_DAY_FORWARD) {
				$navTime = strtotime('+'.$count.' day', $navTime);
			} else {
				trigger_error('Unknown Direction='.$direction);
			}
			
			SetValue($variableIdNavTime, date(IPSCAM_NAV_DATEFORMATDISP, $navTime));
			SetValue($variableIdNavFile, date(IPSCAM_NAV_DATEFORMATFILE, $navTime));
			$this->NavigatePictures(IPSCAM_NAV_FORWARD, 1);
		}

		private function PurgeFilesByDirectory($directory, $days) {
			$refDate = strtotime('-'.$days.' day', time());
			$refDate = date(IPSCAM_NAV_DATEFORMATFILE, $refDate);
			$refDate = substr($refDate, 0 ,8);

			IPSLogger_Dbg(__file__, 'Purge Files with RefDate='.substr($refDate,0,4).'-'.substr($refDate,4,2).'-'.substr($refDate,6,2)
			                        .', Days='.str_pad("$days",3,' ').', Directory='.$directory);
			$fileList = scandir($directory, 0);
			$fileList = array_diff($fileList, Array('.','..'));
			foreach($fileList as $idx=>$file) {
				$filename = basename($file);
        	   $fileExt  = pathinfo($file, PATHINFO_EXTENSION);
				$filenameFull = $directory.$filename;
				$fileDate = substr($filename, 0, 8);
            if ($fileExt=='jpg') {
					if (($fileDate < $refDate) && (@IPS_GetMediaIDByFile(str_replace(IPS_GetKernelDir()."\\","",$filenameFull))== 0) ) {
						IPSLogger_Trc(__file__, 'Delete Camera File: '.$filenameFull);
						unlink($filenameFull);
					}
				}
			}
		}
		
		/**
		 * @public
		 *
		 * Löschen der nicht mehr benötigten Bilder in der History
		 *
		 */
		public function PurgeFiles() {
			set_time_limit(600); // Set PHP Time Limit of 10 Minutes for Purging of Files
			IPSLogger_Inf(__file__, 'Purge History and MotionCapture Camera Files');

			foreach ($this->config as $cameraIdx=>$data) {
				$categoryIdCam      = IPS_GetObjectIDByIdent($cameraIdx, $this->categoryIdCams);
				$directoryHist      = IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\History\\';
				$directoryMot       = IPS_GetKernelDir().'\\Cams\\'.$cameraIdx.'\\MotionCapture\\';
				$daysHist           = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_PICTHIST, $categoryIdCam));
				$daysMot            = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MOTHIST, $categoryIdCam));
				$this->PurgeFilesByDirectory($directoryHist, $daysHist);
				$this->PurgeFilesByDirectory($directoryMot,  $daysMot);
			}
		}

		public function GenerateMovies() {
		}

		
		/**
		 * @public
		 *
		 * Liefert eine URL um auf den LiveStream oder ein Bild einer Kamera zuzugreifen
		 *
		 * @param integer $cameraIdx Index der Kamera
		 * @param integer $urlType Type der URL die geliefert werden soll:
		 *    IPSCAM_URL_LIVE      ... URL des aktuellen Livestreams
		 *    IPSCAM_URL_PICTURE   ... URL des aktuellen Bildes
		 */
		public function GetURL($cameraIdx, $urlType) {
			if ($cameraIdx===null) {
				$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
				$cameraIdx = GetValue($variableIdCamSelect);
			}
			$variableIdSize = IPS_GetObjectIDByIdent(IPSCAM_VAR_SIZE, $this->categoryIdCommon);
			$size = GetValue($variableIdSize);
			$componentParams = $this->config[$cameraIdx][IPSCAM_PROPERTY_COMPONENT];
			$component       = IPSComponent::CreateObjectByParams($componentParams);
			switch($urlType) {
				case IPSCAM_URL_LIVE:
					$url = $component->Get_URLLivestream($size);
					break;
				case IPSCAM_URL_PICTURE:
					$url = $component->Get_URLPicture($size);
					break;
				case IPSCAM_URL_DISPLAY:
					$mode = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_MODE, $this->categoryIdCommon));
					if ($mode==IPSCAM_MODE_LIVE) {
						$url = $component->Get_URLLivestream($size);
					} else {
						$url = $component->Get_URLPicture($size);
					}
					break;
				case IPSCAM_URL_MOVEHOME:   $url = $component->Get_URL(IPSCOMPONENTCAM_URL_MOVEHOME); break;
				case IPSCAM_URL_MOVEUP:     $url = $component->Get_URL(IPSCOMPONENTCAM_URL_MOVEUP); break;
				case IPSCAM_URL_MOVEDOWN:   $url = $component->Get_URL(IPSCOMPONENTCAM_URL_MOVEDOWN); break;
				case IPSCAM_URL_MOVERIGHT:  $url = $component->Get_URL(IPSCOMPONENTCAM_URL_MOVERIGHT); break;
				case IPSCAM_URL_MOVELEFT:   $url = $component->Get_URL(IPSCOMPONENTCAM_URL_MOVELEFT); break;
				case IPSCAM_URL_PREDEFPOS1: $url = $component->Get_URL(IPSCOMPONENTCAM_URL_PREDEFPOS1); break;
				case IPSCAM_URL_PREDEFPOS2: $url = $component->Get_URL(IPSCOMPONENTCAM_URL_PREDEFPOS2); break;
				case IPSCAM_URL_PREDEFPOS3: $url = $component->Get_URL(IPSCOMPONENTCAM_URL_PREDEFPOS3); break;
				case IPSCAM_URL_PREDEFPOS4: $url = $component->Get_URL(IPSCOMPONENTCAM_URL_PREDEFPOS4); break;
				case IPSCAM_URL_PREDEFPOS5: $url = $component->Get_URL(IPSCOMPONENTCAM_URL_PREDEFPOS5); break;
				default:
					$url='';
					trigger_error('Unknown UrlType "'.$urlType.'"');
			}
			return $url;
		}

		private function GetCameraProperty($cameraIdx, $property, $mandatory=false) {
			if (array_key_exists($property, $this->config[$cameraIdx])) {
				$value = $this->config[$cameraIdx][$property];
				$value = htmlentities($value, ENT_COMPAT, 'ISO-8859-1');
			} elseif ($mandatory) {
				trigger_error('Property "'.$property.'" could NOT be found for CameraIdx='.$cameraIdx);
			} else {
				$value = '';
			}
			return $value;
		}
		
		/**
		 * @public
		 *
		 * Führt einen Command Befehl aus der GUI aus
		 *
		 * @param integer $cameraIdx Index der Kamera
		 * @param string $actionProperty Action Property
		 */
		public function ProcessCommand($cameraIdx, $actionProperty) {
			$action    = $this->GetCameraProperty($cameraIdx, $actionProperty);

			if ($action!=='') {
				if (is_numeric($action)) {
					IPSLogger_Dbg(__file__, 'Execute Camera Action Script "'.$action.'"');
					$id = IPSUtil_ObjectIDByPath($action);
					IPS_RunScript($id);
				} else {
					IPSLogger_Dbg(__file__, 'Execute Camera Action "'.$action.'"');
					$module    = IPSModule::CreateObjectByParams($action);
					$module->ExecuteButton();
				}
			}
		}
		
		/**
		 * @public
		 *
		 * Bewegt eine Kamera in eine bestimmte Richtung
		 *
		 * @param integer $cameraIdx Index der Kamera
		 * @param string $urlType Type der URL zur Richtungsangabe
		 */
		public function Move($cameraIdx, $urlType) {
			$curl_handle=curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $this->GetURL($cameraIdx, $urlType));
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
			//curl_setopt($curl_handle, CURLOPT_USERPWD, "User:pwd");
			//curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl_handle, CURLOPT_FAILONERROR, true);
			$fileContent = curl_exec($curl_handle);
		}
		
		private function GetHTMLCameraMap($cameraIdx, $width, $height) {
			$return = '';
			$camType    = $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_TYPE);
			if ($camType==IPSCAM_TYPE_MOVABLECAM) {
				$urlMoveUp    = $this->GetURL($cameraIdx, IPSCAM_URL_MOVEUP);
				$urlMoveDown  = $this->GetURL($cameraIdx, IPSCAM_URL_MOVEDOWN);
				$urlMoveLeft  = $this->GetURL($cameraIdx, IPSCAM_URL_MOVELEFT);
				$urlMoveRight = $this->GetURL($cameraIdx, IPSCAM_URL_MOVERIGHT);
				
				//   0  1  2  3  4  5
				//0  x  x        x  x
				//1  x  x        x  x
				//2        x  x 
				//3        x  x
				//4  x  x        x  x
				//5  x  x        x  x

				$w=round($width/5);
				$h=round($height/5);
				$cordsUp    = ''.($w*1).','.($h*0).','.($w*4).','.($h*0).','.($w*4).','.($h*1).','.($w*3).','.($h*2).','.($w*2).','.($h*2).','.($w*1).','.($h*1);
				$cordsDown  = ''.($w*1).','.($h*5).','.($w*4).','.($h*5).','.($w*4).','.($h*4).','.($w*3).','.($h*3).','.($w*2).','.($h*3).','.($w*1).','.($h*4);
				$cordsLeft  = ''.($w*0).','.($h*1).','.($w*1).','.($h*1).','.($w*2).','.($h*2).','.($w*2).','.($h*3).','.($w*1).','.($h*4).','.($w*0).','.($h*4);
				$cordsRight = ''.($w*5).','.($h*1).','.($w*4).','.($h*1).','.($w*3).','.($h*2).','.($w*3).','.($h*3).','.($w*4).','.($h*4).','.($w*5).','.($h*4);

				$return .= '         <area shape="poly" coords="'.$cordsUp.'" href="#" onClick="var img=new Image(); img.src=\''.$urlMoveUp.'\'" alt="" />'.PHP_EOL;
				$return .= '         <area shape="poly" coords="'.$cordsDown.'" href="#" onClick="var img=new Image(); img.src=\''.$urlMoveDown.'\'" alt="" />'.PHP_EOL;
				$return .= '         <area shape="poly" coords="'.$cordsLeft.'" href="#" onClick="var img=new Image(); img.src=\''.$urlMoveLeft.'\'" alt="" />'.PHP_EOL;
				$return .= '         <area shape="poly" coords="'.$cordsRight.'" href="#" onClick="var img=new Image(); img.src=\''.$urlMoveRight.'\'" alt="" />'.PHP_EOL;
			}
			return $return;
		}
		
		private function GetHTMLMobileButtonNavi($cameraIdx, $idButton, $iconName) {
			$return  = '        <td class="camData">'.PHP_EOL;
			$return .= '            <div id="'.$idButton.'" cameraidx="'.$cameraIdx.'" class="camButton">'.PHP_EOL;
			$return .= '              <table width=100% height=100% border=1 style="font-size:28px;"><tr><td height=140px align="center">'.PHP_EOL;
			$return .= '                <img style="vertical-align:middle" src="/user/IPSCam/icons/'.$iconName.'.png">'.PHP_EOL;
			$return .= '               </td></tr></table>'.PHP_EOL;
			$return .= '            </div>'.PHP_EOL;
			$return .= '        </td>'.PHP_EOL;

			return $return;
		}

		private function GetHTMLMobileButton($cameraIdx, $property, $idButton) {
			$return = '';
			$return .= '        <td class="camData">'.PHP_EOL;
			$return .= '          <div id="'.$idButton.'" cameraidx="'.$cameraIdx.'" class="camButton" >'.PHP_EOL;
			$return .= '            <table width=100% height=100% border=1 style="font-size:28px;"><tr><td align="center">'.
			                            $this->GetCameraProperty($cameraIdx, $property).
			                         '</td></tr></table>'.PHP_EOL;
			$return .= '          </div>'.PHP_EOL;
			$return .= '        </td>'.PHP_EOL;

			return $return;
		}

		/**
		 * @public
		 *
		 * Liefert den HTML Code für die WebFront Anzeige
		 *
		 * @param integer $cameraIdx Index der Kamera
		 * @param integer $size Größe des Streams
		 * @param boolean $showPreDefPosButtons definiert ob Buttons zum Anfahren der vordefinierten Positionen bei beweglichen Kameras erzeugt werden sollen 
		 * @param boolean $showCommandButtons definiert ob Buttons zum Schalten von vordefinierten Lichtern etc. erzeugt werden soll 
		 * @param boolean $showNavigationButtons definiert ob Buttons zur Steuerung beweglicher Kameras erzeugt werden sollen 
		 * @return string HTML Code zur Anzeige
		 */
		public function GetHTMLMobile($cameraIdx, $size, $showPreDefPosButtons, $showCommandButtons, $showNavigationButtons) {
			if ($cameraIdx===null) {
				$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
				$cameraIdx = GetValue($variableIdCamSelect);
			}
			if ($size===null) {
				$size = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_SIZE, $this->categoryIdCommon));
			}
			$camType         = $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_TYPE, true);
			$componentParams = $this->config[$cameraIdx][IPSCAM_PROPERTY_COMPONENT];
			$component       = IPSComponent::CreateObjectByParams($componentParams);
			$urlStream       = $component->Get_URLLivestream($size);
			$streamWidth     = $component->Get_Width($size); 
			$height          = $component->Get_Height($size);

			$width  = IPSCAM_WIDTH_MOBILE;
			$height =  $height * (($width)/$streamWidth);  

			
			$return = '';
			if ($showNavigationButtons and $camType==IPSCAM_TYPE_MOVABLECAM) {
				$return .= '    <table width = 100% border=0>'.PHP_EOL;
				$return .= '      <tr>'.PHP_EOL;
				$return .=          $this->GetHTMLMobileButtonNavi($cameraIdx, 'camButtonNavLeft', 'arrow_left');
				$return .=          $this->GetHTMLMobileButtonNavi($cameraIdx, 'camButtonNavUp',   'arrow_up');
				$return .=          $this->GetHTMLMobileButtonNavi($cameraIdx, 'camButtonNavDown', 'arrow_down');
				$return .=          $this->GetHTMLMobileButtonNavi($cameraIdx, 'camButtonNavRight','arrow_right');
				$return .= '        <td style="height:150px; text-align:center;" >'.PHP_EOL;
				$return .= '        </td>'.PHP_EOL;
				$return .= '      </tr>'.PHP_EOL;
				$return .= '    </table> '.PHP_EOL;
			}
			$return .= '    <div class="camContainer">'.PHP_EOL;
			$return .= '      <img width=100% class="camContentStream" src="'.$urlStream.'" usemap="#cam">'.PHP_EOL;
			$return .= '      <map id="cam" name="cam">'.PHP_EOL;
			$return .=           $this->GetHTMLCameraMap($cameraIdx, $width, $height);
			$return .= '      </map>'.PHP_EOL;
			$return .= '    </div>'.PHP_EOL;
			if ($showPreDefPosButtons and $camType==IPSCAM_TYPE_MOVABLECAM and $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS1)<>'') {
				$return .= '    <table width = 100% border=0>'.PHP_EOL;
				$return .= '      <tr>'.PHP_EOL;
				$return .=           $this->GetHTMLMobileButton($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS1, 'camButtonPreDef1');
				$return .=           $this->GetHTMLMobileButton($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS2, 'camButtonPreDef2');
				$return .=           $this->GetHTMLMobileButton($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS3, 'camButtonPreDef3');
				$return .=           $this->GetHTMLMobileButton($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS4, 'camButtonPreDef4');
				$return .= '        <td style="height:150px; text-align:center;" >'.PHP_EOL;
				$return .= '        </td>'.PHP_EOL;
				$return .= '      </tr>'.PHP_EOL;
				$return .= '    </table> '.PHP_EOL;
			}
			if ($showCommandButtons  and $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND1)<>'') {
				$return .= '    <table width = 100% border=0>'.PHP_EOL;
				$return .= '      <tr>'.PHP_EOL;
				$return .=           $this->GetHTMLMobileButton($cameraIdx, IPSCAM_PROPERTY_COMMAND1, 'camButtonCommand1');
				$return .=           $this->GetHTMLMobileButton($cameraIdx, IPSCAM_PROPERTY_COMMAND2, 'camButtonCommand2');
				$return .=           $this->GetHTMLMobileButton($cameraIdx, IPSCAM_PROPERTY_COMMAND3, 'camButtonCommand3');
				$return .=           $this->GetHTMLMobileButton($cameraIdx, IPSCAM_PROPERTY_COMMAND4, 'camButtonCommand4');
				$return .= '        <td style="height:150px; text-align:center;" >'.PHP_EOL;
				$return .= '        </td>'.PHP_EOL;
				$return .= '      </tr>'.PHP_EOL;
				$return .= '    </table>'.PHP_EOL;
			}
			
			return $return;
		}

		private function GetStreamHeight($cameraIdx, $size=null, $calcWindowHeight=true, $showPreDefPosButtons=true, $showCommandButtons=true, $showNavigationButtons=false) {
			if ($size===null) {
				$size = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_SIZE, $this->categoryIdCommon));
			}
			switch($size) {
				case IPSCAM_SIZE_SMALL:
					$maxHeight = IPSCAM_HEIGHT_SMALL;
					break;
				case IPSCAM_SIZE_MIDDLE:
					$maxHeight = IPSCAM_HEIGHT_MIDDLE;
					break;
				case IPSCAM_SIZE_LARGE:
					$maxHeight = IPSCAM_HEIGHT_LARGE;
					break;
				default:
					trigger_error('Unknown Size '.$size);
			}
			
			$camType         = $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_TYPE, true);
			$componentParams = $this->config[$cameraIdx][IPSCAM_PROPERTY_COMPONENT];
			$component       = IPSComponent::CreateObjectByParams($componentParams);
			$streamHeight    = $component->Get_Height($size);
			$htmlHeight      = 0;
			if ($showNavigationButtons and $camType==IPSCAM_TYPE_MOVABLECAM) {$htmlHeight = $htmlHeight + 50;}
			if ($showPreDefPosButtons and $camType==IPSCAM_TYPE_MOVABLECAM and $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS1)<>'') {$htmlHeight = $htmlHeight + 50;}
			if ($showCommandButtons  and $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND1)<>'') { $htmlHeight = $htmlHeight + 50;}
			if ($calcWindowHeight) {
				if (($htmlHeight+$streamHeight) <= $maxHeight) {
					$height = $htmlHeight + $streamHeight + 12;
				} else {
					$height = $maxHeight; 
				}
			} else {
				if (($htmlHeight+$streamHeight) <= $maxHeight) {
					$height = $streamHeight;
				} else {
					$height = $streamHeight - ($htmlHeight + $streamHeight - $maxHeight) - 10; // StreamHeight - Diff
				}
			}
			return $height;
		}

		/**
		 * @public
		 *
		 * Liefert den HTML Code für die WebFront Anzeige
		 *
		 * @param integer $cameraIdx Index der Kamera
		 * @param integer $size Größe des Streams
		 * @param boolean $showPreDefPosButtons definiert ob Buttons zum Anfahren der vordefinierten Positionen bei beweglichen Kameras erzeugt werden sollen 
		 * @param boolean $showCommandButtons definiert ob Buttons zum Schalten von vordefinierten Lichtern etc. erzeugt werden soll 
		 * @param boolean $showNavigationButtons definiert ob Buttons zur Steuerung beweglicher Kameras erzeugt werden sollen 
		 * @return string HTML Code zur Anzeige
		 */
		public function GetHTMLWebFront($cameraIdx, $size, $showPreDefPosButtons, $showCommandButtons, $showNavigationButtons) {
			if ($cameraIdx===null) {
				$variableIdCamSelect = IPS_GetObjectIDByIdent(IPSCAM_VAR_CAMSELECT, $this->categoryIdCommon);
				$cameraIdx = GetValue($variableIdCamSelect);
			}
			if ($size===null) {
				$size = GetValue(IPS_GetObjectIDByIdent(IPSCAM_VAR_SIZE, $this->categoryIdCommon));
			}
			$camType         = $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_TYPE, true);
			$componentParams = $this->config[$cameraIdx][IPSCAM_PROPERTY_COMPONENT];
			$component       = IPSComponent::CreateObjectByParams($componentParams);
			$urlStream       = $component->Get_URLLivestream($size);
			$origHeight      = $component->Get_Height($size); 
			$origWidth       = $component->Get_Width($size); 
			$streamHeight    = $this->GetStreamHeight($cameraIdx, $size, false, $showPreDefPosButtons, $showCommandButtons, $showNavigationButtons);
			$streamWidth     = $origWidth; 

			$return = '';
			if ($showNavigationButtons and $camType==IPSCAM_TYPE_MOVABLECAM) {
				$return .= '<div class="camContainer">'.PHP_EOL;
				$return .= '  <div class="camContent">'.PHP_EOL;
				$return .= '    <div class="camIcon">'.PHP_EOL;
				$return .= '      <div class="camIconWindDirection">&nbsp;</div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '    <div class="camTitle">'.PHP_EOL;
				$return .= '      <div class="camText"></div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '    <div class="camData">'.PHP_EOL;
				$return .= '      <div class="camDataEnum">'.PHP_EOL;
				$return .= '        <div class="camDataEnumBlock">'.PHP_EOL;
				$return .= '          <div id="camButtonNavLeft"  class="camButton camIconArrowLeft"  cameraidx="'.$cameraIdx.'" style="margin-left: 0px;"></div>'.PHP_EOL;
				$return .= '          <div id="camButtonNavUp"    class="camButton camIconArrowUp"    cameraidx="'.$cameraIdx.'" style="margin-left: 0px;"></div>'.PHP_EOL;
				$return .= '          <div id="camButtonNavDown"  class="camButton camIconArrowDown"  cameraidx="'.$cameraIdx.'" style="margin-left: 0px;"></div>'.PHP_EOL;
				$return .= '          <div id="camButtonNavRight" class="camButton camIconArrowRight" cameraidx="'.$cameraIdx.'" style="margin-left: 0px;"></div>'.PHP_EOL;
				$return .= '        </div>'.PHP_EOL;
				$return .= '      </div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '  </div>'.PHP_EOL;
				$return .= '</div>'.PHP_EOL;
			}
			$return .= '<div class="camContainer">'.PHP_EOL;
			if ($streamHeight===null) {
				$return .= '  <img class="camContentStream" src="'.$urlStream.'" usemap="#cam">'.PHP_EOL;
			} else {
				$return .= '  <img height='.$streamHeight.'px class="camContentStream" src="'.$urlStream.'" usemap="#cam">'.PHP_EOL;
				$streamWidth = $origWidth * (($streamHeight)/$origHeight);  
			}
			$return .= '  <map id="cam" name="cam">'.PHP_EOL;
			$return .=           $this->GetHTMLCameraMap($cameraIdx, $streamWidth, $streamHeight);
			$return .= '  </map>'.PHP_EOL;
			$return .= '</div>'.PHP_EOL;
			if ($showPreDefPosButtons and $camType==IPSCAM_TYPE_MOVABLECAM and $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS1)<>'') {
				$return .= '<div class="camContainer">'.PHP_EOL;
				$return .= '  <div class="camContent">'.PHP_EOL;
				$return .= '    <div class="camIcon td">'.PHP_EOL;
				$return .= '      <div class="camIconImage">&nbsp;</div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '    <div class="camTitle">'.PHP_EOL;
				$return .= '      <div class="camText">Vordefinierte Positionen</div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '    <div class="camData">'.PHP_EOL;
				$return .= '      <div class="camDataEnum">'.PHP_EOL;
				$return .= '        <div class="camDataEnumBlock">'.PHP_EOL;
				$return .= '          <div id="camButtonPreDef1" class="camButton" cameraidx="'.$cameraIdx.'">'.$this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS1).'</div>'.PHP_EOL;
				if ($this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS2)<>'') {
					$return .= '          <div id="camButtonPreDef2" class="camButton" cameraidx="'.$cameraIdx.'">'.$this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS2).'</div>'.PHP_EOL;
				}
				if ($this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS3)<>'') {
					$return .= '          <div id="camButtonPreDef3" class="camButton" cameraidx="'.$cameraIdx.'">'.$this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS3).'</div>'.PHP_EOL;
				}
				if ($this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS4)<>'') {
					$return .= '          <div id="camButtonPreDef4" class="camButton" cameraidx="'.$cameraIdx.'">'.$this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_PREDEFPOS4).'</div>'.PHP_EOL;
				}
				$return .= '        </div>'.PHP_EOL;
				$return .= '      </div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '  </div>'.PHP_EOL;
				$return .= '</div>'.PHP_EOL;
			}
			if ($showCommandButtons  and $this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND1)<>'') {
				$return .= '<div class="camContainer">'.PHP_EOL;
				$return .= '  <div class="camContent">'.PHP_EOL;
				$return .= '    <div class="camIcon td">'.PHP_EOL;
				$return .= '      <div class="camIconImage">&nbsp;</div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '    <div class="camTitle">'.PHP_EOL;
				$return .= '      <div class="camText">Licht</div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '    <div class="camData">'.PHP_EOL;
				$return .= '      <div class="camDataEnum">'.PHP_EOL;
				$return .= '        <div class="camDataEnumBlock">'.PHP_EOL;
				$return .= '          <div id="camButtonCommand1" class="camButton" cameraidx="'.$cameraIdx.'">'.$this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND1).'</div>'.PHP_EOL;
				if ($this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND2)<>'') {
					$return .= '          <div id="camButtonCommand2" class="camButton" cameraidx="'.$cameraIdx.'">'.$this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND2).'</div>'.PHP_EOL;
				}
				if ($this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND3)<>'') {
					$return .= '          <div id="camButtonCommand3" class="camButton" cameraidx="'.$cameraIdx.'">'.$this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND3).'</div>'.PHP_EOL;
				}
				if ($this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND4)<>'') {
					$return .= '          <div id="camButtonCommand4" class="camButton" cameraidx="'.$cameraIdx.'">'.$this->GetCameraProperty($cameraIdx, IPSCAM_PROPERTY_COMMAND4).'</div>'.PHP_EOL;
				}
				$return .= '        </div>'.PHP_EOL;
				$return .= '      </div>'.PHP_EOL;
				$return .= '    </div>'.PHP_EOL;
				$return .= '  </div>'.PHP_EOL;
				$return .= '</div>'.PHP_EOL;
			}

			return $return;
		}


	}


	/** @}*/
?>