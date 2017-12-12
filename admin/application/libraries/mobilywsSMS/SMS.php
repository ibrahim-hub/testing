<?php
// Turn off all error reporting
error_reporting(0);

include("includeSettings.php");
class SMS {
 
   	/*Send SMS to Customer with Specefic Message*/
	public function sendSMSMsg($mobileno, $msg)
	{
		$MsgID = rand(1,99999);					
		$timeSend = 0;							
		$dateSend = 0;							
		$deleteKey = 152485;					
		$resultType = 0;	
		
		return sendSMS(SMS_UserAccount, SMS_PassAccount, $mobileno, SMS_Sender_Name, $msg, $MsgID, $timeSend, $dateSend, $deleteKey, $resultType);
	}

	public function checkbalance()
	{
		echo balanceSMS(SMS_UserAccount, SMS_PassAccount);
	}
}
?>