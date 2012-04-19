<?
	/**@addtogroup ipsedip
	 * @{
	 *
	 * Implementierung eines EDIP Displays vom Type eDipTFT43A
	 *
	 * @file          IPSEDIP_TFT43A.class.php
	 * @author        Andreas Brauneis
	 * @author        André Czwalina
	 * @version
	 * Version 2.50.2, 16.04.2012<br/>
	 *
	 */

   /**
    * @class IPSEDIP_TFT43A
    *
    * Implementierung einer IPSEDIP Klasse vom Type TFT43A
    *
    * @author Andreas Brauneis
	 * @author André Czwalina
    * @version
    * Version 2.50.2, 16.04.2012<br/>
    */
	class IPSEDIP_TFT43A extends IPSEDIP {
		protected function AddMessageHeader() {
			// Common Settings
			$this->messageArray[] = 'TA';     // Terminal aus
			$this->messageArray[] = 'AL,0,1'; // Touch löschen
			$this->messageArray[] = 'DL';     // Display leeren
			$this->messageArray[] = 'AS,0';   // Summer aus
			$this->messageArray[] = 'YH,'.GetValue($this->objectBacklightId);  // [AC] Helligkeit auf Parawert
//			$this->messageArray[] = 'YH,30';  // Helligkeit auf 30
			// Top Line
			$this->messageArray[] = "FP,19,130,130,130"; // Define Color
			$this->messageArray[] = "FE,8,1,19,8,1,7"; // Button Color
			$this->messageArray[] = "AT,404,1,478,25,2,0,CRefresh";  //[AC] Refresh Button rechts oben
			if ($this->rootId <> $this->currentId) $this->messageArray[] = 'AT,1,1,75,25,1,0,C<<';   // Touch Button
			$this->messageArray[] = 'ZF,6'; // Schriftart //[AC]
			$this->messageArray[] = 'ZZ,1,1'; // SchriftZoom
			$this->messageArray[] = "FZ,8,1"; // Textfarbe //[AC]
			$this->messageArray[] = 'ZC,220,5,'.IPS_GetName($this->currentId); // Current Category
			$this->messageArray[] = "FG,8,1,1"; // Line Color
			$this->messageArray[] = 'GR,1,30,480,30'; // Line
			$this->messageArray[] = 'GR,160,30,160,272'; // Line
		}

		protected function AddMessageCategories() {
			$categoryList = array();
			foreach ($this->objectList as $idx=>$object) {
				if ($object['ObjectType']=='Category') {
					$categoryList[] = $object;
				}
			}
			$this->GetObjectDisplayAttributes(count($categoryList), $buttonHeight, $buttonSpace, 30, 10);
			$this->messageArray[] = "FP,18,150,150,150"; // Define Color
			$this->messageArray[] = "FE,8,1,18,8,1,7"; // Button Color
			$yPos1 = 40;
			foreach ($categoryList as $idx=>$category) {
				$yPos2 = $yPos1+$buttonHeight;
				$cmd   = $category['Cmd'];
				$name  = $category['Name'];
				$this->messageArray[] = "AT(1,$yPos1,150,$yPos2,$cmd,0,C$name"; // Touch Button
				$yPos1 = $yPos1 + $buttonHeight + $buttonSpace;
			}
		}

		protected function AddMessageVariables() {
			$varList = array();
			$count     = 0;
			foreach ($this->objectList as $idx=>$object) {
				if ($object['ObjectType']=='Variable' or $object['ObjectType']=='Script') {
					$varList[] = $object;
					if ($object['LineIdx']==0) $count++;
				}
			}

			$this->GetObjectDisplayAttributes($count, $height, $space, 40, 0);
			$yPosR1  = 40;    // Start of Variable Section
			foreach ($varList as $idx=>$variable) {
				$cmd         = $variable['Cmd'];
				$name        = $variable['Name'];
				$displayType = $variable['DisplayType'];
				$txtFarbe 	 = $variable['Farbe'];//[AC]
				$yPosR2      = $yPosR1+$height;
				$this->GetObjectButtonAttributes($count, $displayType, $yPosR1, $yPosR2, $yPosG1, $yPosG2, $yPosB1, $yPosB2, $yPosT);

				if ($variable['BlockBegin']) {
					$yPosBR1              = $yPosR1;
					$this->messageArray[] = 'ZF,5'; // Schriftart
					$this->messageArray[] = 'ZZ,1,1'; // SchriftZoom
					$this->messageArray[] = "FR,16,1,1"; // Frame Color
					$this->messageArray[] = "FZ,8,1"; // Textfarbe //[AC]
					$this->messageArray[] = "ZL,180,$yPosT,$name"; // Text
				}

				$this->messageArray[] = "FZ,".$txtFarbe.",1"; // Textfarbe //[AC]
				switch($displayType) {
					case 'Text':
					case 'BigText':
						$valueFormatted  = $variable['ValueFormatted'];
						if ($displayType=='BigText') {
							$this->messageArray[] = 'ZF,5'; // Schriftart
							$this->messageArray[] = 'ZZ,2,2'; // SchriftZoom
							$yPosT=$yPosT-5;
						}
						$this->messageArray[] = "ZR,470,$yPosT,$valueFormatted"; // Text
						break;

					case 'Switch':
					case 'Button':
					case 'Select':
					case 'Inline':
					case 'Block':
						$valueFormatted  = $variable['ValueFormatted'];
						$red             = $variable['Red'];
						$green           = $variable['Green'];
						$blue            = $variable['Blue'];
						$this->messageArray[] = "FP,17,$red,$green,$blue"; // Define Color
						$this->messageArray[] = "FE,8,1,17,8,1,7"; // Button Color

						if ($displayType=='Inline') {
						 	//echo "Width=".$variable['Width'].", LineIdx=".$variable['LineIdx']."\n";
							if ($variable['Width']==50 and ($variable['LineIdx']==0)) {
							  $this->messageArray[] = "AT,320,$yPosB1,392,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							} elseif ($variable['Width']==50 and ($variable['LineIdx']==1)) {
							  $this->messageArray[] = "AT,398,$yPosB1,470,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							} else {
							  $this->messageArray[] = "AT,320,$yPosB1,470,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							}

						} elseif ($displayType=='Block') {
							if ($variable['Width']==50 and ($variable['LineIdx']==0)) {
								$valueFormatted = substr($valueFormatted,0,17);
							  	$this->messageArray[] = "AT,180,$yPosB1,320,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							} elseif ($variable['Width']==50 and ($variable['LineIdx']==1)) {
								$valueFormatted = substr($valueFormatted,0,17);
							  	$this->messageArray[] = "AT,330,$yPosB1,470,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							} else {
							   $valueFormatted = substr($valueFormatted,0,35);
							  	$this->messageArray[] = "AT,180,$yPosB1,470,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							}
						} elseif ($displayType=='Select') {
							if       ($variable['Width']==30 and ($variable['LineIdx']==0)) {
								$valueFormatted = substr($valueFormatted,0,17);
							   $this->messageArray[] = "AT,320,$yPosB1,345,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							} elseif ($variable['Width']==30 and ($variable['LineIdx']==1)) {
								$valueFormatted = substr($valueFormatted,0,17);
							   $this->messageArray[] = "AT,350,$yPosB1,440,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							} elseif ($variable['Width']==30 and ($variable['LineIdx']==2)) {
								$valueFormatted = substr($valueFormatted,0,17);
							   $this->messageArray[] = "AT,445,$yPosB1,470,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							} else {
								$valueFormatted = substr($valueFormatted,0,17);
							   $this->messageArray[] = "AT,320,$yPosB1,470,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
							}

						} else {
							$this->messageArray[] = "AT,320,$yPosB1,470,$yPosB2,$cmd,0,C$valueFormatted"; // Button Text
						}
						break;
					case 'Script':
						$this->messageArray[] = "FE,8,1,18,8,1,7"; // Button Color
						$this->messageArray[] = "AT,320,$yPosB1,470,$yPosB2,$cmd,0,C>> "; // Button Text
						break;
					case 'BarGraph':
						$maxValue        = $variable['MaxValue']+10; // Correct +10 (0..100 doesnt work, 10..110 works ???)
						$minValue        = $variable['MinValue']+10;
						$stepSize        = $variable['StepSize'];
						$value           = $variable['Value'];
						$this->messageArray[] = "BR,$cmd,280,$yPosG1,470,$yPosG2,$minValue,$maxValue,$stepSize";
						$this->messageArray[] = "BA,$cmd,".($value+10);
						$this->messageArray[] = "AB,$cmd";
						break;
					default: // Unsupported Datatype
				}
				if ($variable['BlockEnd']) {
				   $yPosBR2              = $yPosR2;
					$this->messageArray[] = "FG,16,1,1"; // Frame Color
					$this->messageArray[] = "GR,170,$yPosBR1,480,$yPosBR1"; // Line
					$this->messageArray[] = "GR,170,$yPosBR2,480,$yPosBR2"; // Line
					$this->messageArray[] = "GR,170,$yPosBR1,170,$yPosBR2"; // Line
					$this->messageArray[] = "GR,480,$yPosBR1,480,$yPosBR2"; // Line
				}
				// NewLine or EndOfList
				if ($idx==count($varList)-1 or ($idx<count($varList)-1) and $varList[$idx+1]['LineIdx']==0) {
					$yPosR1  = $yPosR1 + $height + $space; // Calc Next Position
				}
			}
		}

		protected function AddMessageValueEdit() {
			if (GetValue($this->objectEditId)==0) return;

			$editList = array();
			foreach ($this->objectList as $idx=>$object) {
				if ($object['ObjectType']=='Edit') {
					$editList[] = $object;
				}
			}
			$yPos1 = max(80 - count($editList)*4, 45);
			$value = GetValue(GetValue($this->objectEditId));
			$this->messageArray[] = "FP,19,130,130,130"; // Define Color
			$this->messageArray[] = "FP,20,130,130,180"; // Define Color
			$this->GetObjectDisplayAttributes(count($editList), $height, $space, 35, 0);
			foreach ($editList as $idx=>$object) {
				$valueFormatted = $object['ValueFormatted'];
				$cmd            = $object['Cmd'];
				$yPos2          = $yPos1 + $height;
				if ($object['Value']==$value) {
					$this->messageArray[] = "FE,8,1,20,8,1,7"; // Button Color
					$this->messageArray[] = "AK,250,$yPos1,465,$yPos2,$cmd,0,L * $valueFormatted"; // Button Text
				} else {
					$this->messageArray[] = "FE,8,1,19,8,1,7"; // Button Color
					$this->messageArray[] = "AK,250,$yPos1,465,$yPos2,$cmd,0,L   $valueFormatted"; // Button Text
				}
				$yPos1 = $yPos1 + $height + $space;
			}
		}

		private function GetObjectDisplayAttributes($objectCount, &$height, &$space, $defaultHeight=30, $defaultSpace=10, $startPos=40, $endPos=272) {
			if ($objectCount==0) return;
		   $height = $defaultHeight;
		   $space  = $defaultSpace;

			$totalHeight  = $endPos - $startPos;
			$segment      = floor($totalHeight / $objectCount);
			//echo "Total=$totalHeight, Segment=$segment, Count=$objectCount\n";
			$segmentSpace = 0;
			if ($space > 0) {
				$segmentSpace = $space + 9 - $objectCount*2;
				if ($space < 0) $space = 3;
			}
			$segmentHeight = $segment - $segmentSpace;
			//echo "$segmentHeight < $height\n";
			if ($segmentHeight < $height) {
			   $height = $segmentHeight;
			   $space  = $segmentSpace;
			}
			
		}

		private function GetObjectButtonAttributes($count, $displayType, $yPosR1, $yPosR2, &$yPosG1, &$yPosG2, &$yPosB1, &$yPosB2, &$yPosT) {
			$yPosT  = $yPosR1 + round(($yPosR2-$yPosR1)/2) - 6;
			$yPosG1 = $yPosR1+14-$count;
			$yPosG2 = $yPosR2-14+$count;
			$yPosB1 = $yPosR1+7-max(round($count/3),2);
			$yPosB2 = $yPosR2-7+max(round($count/3),2);
		}

	} 

	/** @}*/
?>