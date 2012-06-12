<?
	/**@addtogroup ipslogger 
	 * @{
	 *
	 * @file          IPSLogger_Output.inc.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Dieses Script enthält die Funktionen, die die Messages zu den diversen Outputs schicken.
	 *
	 */
	include_once "IPSLogger_Constants.inc.php";

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_Out($LogLevel, $LogType, $Context, $Msg, $Priority=0) {
		$StackTxt   = '';
		$StackHtml  = '';
		if ($LogType==c_LogType_Error) {
			$DebugTrace = debug_backtrace();
			foreach ($DebugTrace as $Idx=>$Stack) {
				if (array_key_exists('line', $Stack) and array_key_exists('function', $Stack) and array_key_exists('file', $Stack)) {
					$File     = str_replace('scripts\\', '', str_replace(IPS_GetKernelDir(), '', $Stack['file']));
					$Function = $Stack['function'];
					$Line     = str_pad($Stack['line'],3,' ', STR_PAD_LEFT);
					$StackTxt  .= c_lf."  $Line in $File (call $Function)";
				} elseif (array_key_exists('function', $Stack)) {
					$StackTxt  .= c_lf.'      in '.$Stack['function'];
				} else {
					$StackTxt  .= c_lf.'      Unknown Stack ...';
				}
			}
		}
        $Context = IPSLogger_normalizeContext($Context);
		if (!IPS_VariableExists(c_ID_HtmlOutEnabled)) {
			echo $Context.'-'.$LogType.'-'.$Msg;
			return;
		}
        IPSLogger_invokeLoggers($LogLevel, $LogType, $Context, $Msg, $StackTxt, $Priority);
    }

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_WriteFile($Directory, $File, $Text, $ID_OutEnabled) {
		if ($Directory == "") {
			$Directory = IPS_GetKernelDir().'logs\\';
		}
		if(($FileHandle = fopen($Directory.$File, "a")) === false) {
			SetValue($ID_OutEnabled, false);
			Exit;
		}
		fwrite($FileHandle, $Text.c_lf);
		fclose($FileHandle);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutFile($LogLevel, $LogType, $Context, $Msg) {
			switch ($LogType) {
				case c_LogType_Test:          $prefix='        '; break;
				case c_LogType_Trace:         $prefix='      '; break;
				case c_LogType_Debug:         $prefix='    '; break;
				case c_LogType_Communication: $prefix='    '; break;
				default:                      $prefix='  ';
			}

			$Msg          = str_replace("\n", "  ", $Msg);

			$Out  = 'IPSymcon';
			$Out .= '-'.IPSLogger_LogTypeShort($LogType).'-';
			$Out .= substr(str_pad($Context,15,' '),0,15);
			$Out .= date(c_Format_LogOutDate).substr(microtime(),1,c_Format_LogOutMicroLen);
			$Out .= $prefix.$Msg;

			$File = 'IPSLogger_'.date('Ymd').'.'.c_File_Extension;
			IPSLogger_WriteFile(c_File_Directory, $File, $Out, c_ID_FileOutEnabled);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutLog4IPS($LogLevel, $LogType, $Context, $Msg) {
			$Out  = '<event';
			$Out .=   ' logger="'.$Context.'"';
			$Out .=   ' timestamp="'.date('Y-m-d\TH:i:s.u').'+01:00"';
			$Out .=   ' level="'.IPSLogger_LogTypeXml($LogType).'"';
			$Out .=   ' domain="IPS.exe"';
			$Out .=   ' username="IPS">';
			$Out .=   '<message>'.$Msg.'</message>';
			$Out .= '</event>';

			$File = 'IPSLogger_'.date('Ymd').'.'.c_Log4IPS_Extension;
			IPSLogger_WriteFile(c_Log4IPS_Directory, $File, $Out, c_ID_Log4IPSOutEnabled);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutEMail($LogLevel, $LogType, $Context, $Msg, $Priority) {
			$Out  =    'IPS-'.IPSLogger_LogTypeShort($LogType).'-'.substr($Context,0,c_Format_LogOutContextLen);
			$Out .=    '  '.date(c_Format_LogOutDate).substr(microtime(),1,c_Format_LogOutMicroLen);
			$Out .=    '  '.$Msg.c_lf;

			$MsgList = GetValue(c_ID_EMailOutMsgList);
			SetValue(c_ID_EMailOutMsgList, $MsgList.$Out);

			if (IPS_GetScriptTimer(c_ID_ScriptSendMail) == 0) { //Timer not active --> Start
				if (GetValue(c_ID_EMailOutDelay) == 0) {
					IPS_SetScriptTimer(c_ID_ScriptSendMail, 1);
				} else {
					IPS_SetScriptTimer(c_ID_ScriptSendMail, GetValue(c_ID_EMailOutDelay));
				}
			}
	}
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutIPS($LogLevel, $LogType, $Context, $Msg) {
			$Out = $LogType.': '.$Msg;
			IPS_LogMessage($Context, $Out);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutSingle($LogLevel, $LogType, $Context, $Msg) {
			$Out = '<div style="'.IPSLogger_LogTypeStyle($LogType).'">'.$LogType.': '.$Msg.'</div>';
			SetValue(c_ID_SingleOutMsg, $Out);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutHtml($LogLevel, $LogType, $Context, $Msg) {
			$Msg  = htmlentities($Msg, ENT_COMPAT, 'ISO-8859-1');
			$Msg  = str_replace("\n", "<BR> ", $Msg);
			switch ($LogType) {
				case c_LogType_Test: 			$Msg = '<DIV style="padding-left:45px;">'.$Msg.'</DIV>'; break;
				case c_LogType_Trace: 			$Msg = '<DIV style="padding-left:30px;">'.$Msg.'</DIV>'; break;
				case c_LogType_Debug: 			$Msg = '<DIV style="padding-left:15px;">'.$Msg.'</DIV>'; break;
				case c_LogType_Communication: $Msg = '<DIV style="padding-left:30px;">'.$Msg.'</DIV>'; break;
				default: 							$Msg = '<DIV>'.$Msg.'</DIV>';
			}

			$CurrentMsgId = GetValue(c_ID_HtmlOutMsgId)+1;
			$MsgList      = GetValue(c_ID_HtmlOutMsgList);
			$MsgCount     = GetValue(c_ID_HtmlOutMsgCount);

			$TablePrefix   = '<table width="100%" style="'.c_Style_HtmlOutTable.'">';
			$TablePrefix  .= c_Style_HtmlOutColGroup;

			//IPSymcon-Inf-WinLIRC 2010-12-03 22:09:13.000 Msg ...
			$Out =  '<tr id="'.$CurrentMsgId.'" style="'.IPSLogger_LogTypeStyle($LogType).'">';
			$Out .=    '<td>IPS</td>';
			$Out .=    '<td>-'.IPSLogger_LogTypeShort($LogType).'-</td>';
			$Out .=    '<td>'.IPSLogger_HtmlEncode(substr($Context,0,c_Format_LogOutContextLen)).'</td>';
			$Out .=    '<td>'.date(c_Format_LogOutDate).substr(microtime(),1,c_Format_LogOutMicroLen).'</td>';
			$Out .=    '<td>'.$Msg.'</td>';
			$Out .= '</tr>';

			//<table><tr id="1"><td>....</tr></table>
			if (IPSLOGGER_HTML_NEWMESSAGETOP) {
				if (strpos($MsgList, '</table>') === false) {
					$MsgList = "";
				} else {
					$StrPos1     = strlen($TablePrefix);
					$StrTmp      = '<tr id="'.($CurrentMsgId-$MsgCount).'"';
					if (strpos($MsgList, $StrTmp)===false) {
					   $StrPos2 = strpos($MsgList, '</table>');
					} else {
						$StrPos2 = strpos($MsgList, $StrTmp);
					}
					$StrLen      = $StrPos2 - $StrPos1;
					$MsgList     = substr($MsgList, $StrPos1, $StrLen);
				}
				SetValue(c_ID_HtmlOutMsgList, $TablePrefix.$Out.$MsgList.'</table>');
			} else {
				if (strpos($MsgList, '</table>') === false) {
					$MsgList = "";
				} else {
					$StrTmp      = '<tr id="'.($CurrentMsgId-$MsgCount+1).'"';
					if (strpos($MsgList, $StrTmp)===false) {
						$StrPos = strlen($TablePrefix);
					} else {
						$StrPos      = strpos($MsgList, $StrTmp);
					}
					$StrLen      = strlen($MsgList) - strlen('</table>') - $StrPos;
					$MsgList     = substr($MsgList, $StrPos, $StrLen);
				}
				SetValue(c_ID_HtmlOutMsgList, $TablePrefix.$MsgList.$Out.'</table>');
			}
			SetValue(c_ID_HtmlOutMsgId, $CurrentMsgId);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutEcho($LogLevel, $LogType, $Context, $Msg) {
			switch ($LogType) {
				case c_LogType_Test:          $prefix='        '; break;
				case c_LogType_Trace:         $prefix='      '; break;
				case c_LogType_Debug:         $prefix='    '; break;
				case c_LogType_Communication: $prefix='    '; break;
				default:                      $prefix='  ';
			}

			$Msg          = str_replace("\n", "  ", $Msg);
			$Msg          = str_replace(chr(13), "  ", $Msg);

			$Out  = 'IPS';
			$Out .= '-'.IPSLogger_LogTypeShort($LogType).'-';
			$Out .= substr(str_pad($Context,20,' '),0,20).' ';
			$Out .= date(c_Format_LogOutDate).substr(microtime(),1,c_Format_LogOutMicroLen);
			$Out .= $prefix.$Msg.c_lf;

			echo $Out;
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_SetOutEcho($OutEnabled, $LogLevel=null) {
		SetValue(c_ID_EchoOutEnabled, $OutEnabled);
		if ($LogLevel !== null) {
			SetValue(c_ID_EchoOutLevel, $LogLevel);
		}
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutProwl($LogLevel, $LogType, $Context, $Msg, $Priority) {
			include_once('ProwlPHP.php');

			$prowl = new Prowl(c_Key_ProwlService); 
			$prowl->push(array(	'application'	=> 'IP-Symcon',
 										'event'			=> $Context,
										'description'	=> utf8_encode($Msg),
										'priority'		=> 0));
	}
	
	// ---------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_SendProwlMessage($Event, $Description, $Priority) {
		include_once('ProwlPHP.php');

		$prowl = new Prowl(c_Key_ProwlService);
		$prowl->push(array(	'application'	=> 'IP-Symcon',
									'event'			=> $Event,
									'description'	=> utf8_encode($Description),
									'priority'		=> $Priority));

	}
	
	// ----------------------------------------------------------------------------------------------------------------------------
	function IPSLogger_OutProgram($Msg, $HtmlId, $LogId, $MsgCount, $FontSize=10) {
			$Msg          = htmlentities($Msg, ENT_COMPAT, 'ISO-8859-1');
			$Msg          = str_replace("\n", "<BR>", $Msg);
			$MsgList      = GetValue($HtmlId);
			$TablePrefix  = '<table width="100%" style="font-family:courier; font-size:'.$FontSize.'px;">';
			$CurrentMsgId = GetValue($LogId)+1;
			SetValue($LogId, $CurrentMsgId);

			//Msg ...
			$Out =  '<tr id="'.$CurrentMsgId.'" style="color:#FFFFFF;">';
			$Out .=    '<td>'.date('Y-m-d H:i').'</td>';
			$Out .=    '<td>'.$Msg.'</td>';
			$Out .= '</tr>';

			//<table><tr><td>....</tr></table>
			if (strpos($MsgList, '</table>') === false) {
				$MsgList = "";
			} else {
				$StrTmp      = '<tr id="'.($CurrentMsgId-$MsgCount+1).'"';
				if (strpos($MsgList, $StrTmp)===false) {
					$StrPos = strlen($TablePrefix);
				} else {
					$StrPos      = strpos($MsgList, $StrTmp);
				}
				$StrLen      = strlen($MsgList) - strlen('</table>') - $StrPos;
				$MsgList     = substr($MsgList, $StrPos, $StrLen);
			}
			SetValue($HtmlId, $TablePrefix.$MsgList.$Out.'</table>');
	}
    
    $IPSLogger_contextLoggingLevel = array();
    
    function IPSLogger_SetContextLoggingLevel($Context, $LogLevel) {
        global $IPSLogger_contextLoggingLevel;
        
        $Context = IPSLogger_normalizeContext($Context);
        $IPSLogger_contextLoggingLevel[$Context] = $LogLevel;
    }
    
    function IPSLogger_HasContextCustomLogLevel($Context) {
        global $IPSLogger_contextLoggingLevel;
        
        return isset($IPSLogger_contextLoggingLevel[$Context]);
    }
    
    function IPSLogger_ShouldLogContext($Context, $LogLevel) {
        global $IPSLogger_contextLoggingLevel;
        
        if(!IPSLogger_HasContextCustomLogLevel($Context)) {
            return true;
        }
        
        return $LogLevel <= $IPSLogger_contextLoggingLevel[$Context];
    }
    
    function IPSLogger_invokeLoggers($LogLevel, $LogType, $Context, $Msg, $StackTxt, $Priority) {
        $loggers = array(
            "Single"    => array(
                "enabled"   => c_ID_SingleOutEnabled,
                "level"     => c_ID_SingleOutLevel,
                "appendStack"   => false
            ),
            "Html"      => array(
                "enabled"   => c_ID_HtmlOutEnabled,
                "level"     => c_ID_HtmlOutLevel,
            ),
            "IPS"      => array(
                "enabled"   => c_ID_IPSOutEnabled,
                "level"     => c_ID_IPSOutLevel,
            ),
            "EMail"      => array(
                "enabled"   => c_ID_EMailOutEnabled,
                "level"     => c_ID_EMailOutLevel,
                "priority"  => c_ID_EMailOutPriority
            ),
            "File"      => array(
                "enabled"   => c_ID_FileOutEnabled,
                "level"     => c_ID_FileOutLevel,
            ),
            "Log4IPS"      => array(
                "enabled"   => c_ID_Log4IPSOutEnabled,
                "level"     => c_ID_Log4IPSOutLevel,
            ),
            "Echo"      => array(
                "enabled"   => c_ID_EchoOutEnabled,
                "level"     => c_ID_EchoOutLevel,
            ),
            "Prowl"      => array(
                "enabled"   => c_ID_ProwlOutEnabled,
                "level"     => c_ID_ProwlOutLevel,
                "priority"  => c_ID_ProwlOutPriority
            ),
        );
        
        foreach($loggers as $name => $logger) {
            // check if logger is enabled at all
            $shouldLog = GetValue($logger["enabled"]);
            if(!$shouldLog) {
                continue;
            }
            
            // evaluate log level. If no custom level is defined, use the global log level.
            if(IPSLogger_HasContextCustomLogLevel($Context)) {
                $shouldLog &= IPSLogger_ShouldLogContext($Context, $LogLevel);
            } else {
                $shouldLog &= GetValue($logger["level"]) >= $LogLevel;
            }
            
            $appendStack = !isset($logger["appendStack"]) || (isset($logger["appendStack"]) && $logger["appendStack"] === true);
            
            $hasPriority = isset($logger["priority"]);
            $shouldLog &= !$hasPriority || ($hasPriority && GetValue($logger["priority"]) >= $Priority);
            
            if($shouldLog) {
                $parameters = array($LogLevel, $LogType, $Context);
                $parameters[] = $Msg.($appendStack ? $StackTxt : "");
                if($hasPriority) $parameters[] = $Priority;
                
                call_user_func_array("IPSLogger_Out".$name, $parameters);
            }
        }
    }
    
    function IPSLogger_normalizeContext($Context) {
        if (strrpos($Context, '\\') !== false) {
            if (strpos($Context, '.') !== false) {
                $Context = substr($Context, strrpos($Context, '\\')+1, strpos($Context, '.')-strrpos($Context, '\\')-1);
            }
        }
        return $Context;
    }
   /** @}*/
?>