<?
	/**@addtogroup ipslogger 
	 * @{
	 *
	 * @file          IPSLogger_SendMail.ips.php
	 * @author        Andreas Brauneis
	 * @version
	 * Version 2.50.1, 31.01.2012<br/>
	 *
	 * Versenden von Mails
	 *
	 * Dieses Script wird vom Script IPSLogger_Output.ips.php durch einen Timer aufgerufen.
	 *
	 */
	include_once "IPSLogger.inc.php";
	define ("c_LogId", "IPSLogger_SendMail");

	IPSLogger_Trc(c_LogId, 'Execute SendEmail for Messages of IPSLogger');

	$MsgList = GetValue(c_ID_EMailOutMsgList);
	if ($MsgList <> "") {
	   if (c_EMail_Address1 <> "") {
			IPSLogger_Dbg(c_LogId, 'Send ErrorMail to '.c_EMail_Address1);
			SMTP_SendMailEx(c_ID_SmtpDevice, c_EMail_Address1,
			                c_EMail_Subject, $MsgList."\n\n".c_EMail_Signature);
		} else {
			IPSLogger_Dbg(c_LogId, 'Send ErrorMail to default SMTP EMail-Address: ');
		   SMTP_SendMail(c_ID_SmtpDevice, "IP-Symcon Error(s)",  $MsgList."\n\n".c_EMail_Signature);
		}
	   if (c_EMail_Address2 <> "") {
			IPSLogger_Dbg(c_LogId, 'Send ErrorMail to '.c_EMail_Address2);
		   SMTP_SendMailEx(c_ID_SmtpDevice, c_EMail_Address2,
			                c_EMail_Subject, $MsgList."\n\n".c_EMail_Signature);
		}

	   if (c_EMail_Address3 <> "") {
			IPSLogger_Dbg(c_LogId, 'Send ErrorMail to '.c_EMail_Address3);
		   SMTP_SendMailEx(c_ID_SmtpDevice, c_EMail_Address3,
			                c_EMail_Subject, $MsgList."\n\n".c_EMail_Signature);
		}
	}
	IPS_SetScriptTimer(c_ID_ScriptSendMail, 0);		// Clear Timer
	SetValue(c_ID_EMailOutMsgList, '');             // Clear MsgList
	/** @}*/
?>