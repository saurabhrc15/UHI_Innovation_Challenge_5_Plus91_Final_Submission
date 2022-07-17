<?php
include_once dirname(__FILE__)."/../config/config.Email_SMS.php";
include_once dirname(__FILE__)."/../processSMS.php";
include_once dirname(__FILE__).'/class.DBConnManager.php';

//! @brief - Class SMS

class SMS{

	public $iSenderNo;
	public $sSenderName;

	public $aReceiverNo = array();
	public $aReceiverName = array();
	private $iReceiverCount = 0;

	public $sMessage;

	public $iPriority = 1;

	public $iInsertSmsId;
	private $iSmsStatus;

	public $dSmsTimestamp;
	private $sApiMessageId;

	public $bIsMarketingSMS = FALSE; //! For set the marketing sms value (It may be true or false)..

	
	//! @brief - constructor,used to create SMS Basic Structure
	function __construct($aReceiverNo,$aReceiverName,$sMessage,$iSenderNo,$sSenderName){

			if($iSenderNo!='')
			{
				$this->iSenderNo = $iSenderNo;
			}
			else
			{
				$this->iSenderNo = SMS_SENDER_NO;
			}

			if($sSenderName != '')
			{
				$this->sSenderName = $sSenderName;
			}
			else
			{
				$this->sSenderName = SMS_SENDER_NAME;
			}
		
			if($aReceiverNo != null )
			{
				$this->sMessage = $sMessage;
			}
		

		$this->iInsertSmsId = 0;
		$this->iSmsStatus = 0;
		$this->dSmsTimestamp = date("Y-m-d H:i:s");

		$this->iInsertSmsId = $this->addSms(); //! call the function to get sms master id
		$iRecipientId = $this->addRecipient($aReceiverNo,$aReceiverName); //! call the function to get recipient id

	}

	//! brief function to get database connection.
	private function getSMSSysConn(){

		$DBMan = new DBConnManager(); // Initialzing DBConnManager() Class; 
		$oConn =  $DBMan->getConnInstance(); // Requiring connection insatnce
		
		return $oConn;
	}


	//! @brief - function to add SMS Basic Info  
	//! @return type integer,sns master id
	private function addSms(){

		$iInsertId = 0;
		$sExtra = 1; //! SMS valid status

		$sInsertQuery = "INSERT INTO `sms_master`(`id`, `sms_from_no`, `sms_form_name`, `message`, `created_on`, `extra`) 
						VALUES (NULL,'{$this->iSenderNo}','{$this->sSenderName}','{$this->sMessage}','{$this->dSmsTimestamp}','{$sExtra}')";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){
			$sInserQueryResult = $oMysqli->query($sInsertQuery);
			if($sInserQueryResult != false){

				$iInsertId = $oMysqli->insert_id;
			}
		}
		
		//$oMysqli->close();
		return $iInsertId;
	}

	//! @brief - function to delete SMS Basic Info  
	//! @return type integer 1 or 0
	private function deleteSms($iSMSId){

		$iInsertId = 0;

		$sUpdateQuery = "UPDATE `sms_master` SET `extra` = 0 WHERE id = {$iSMSId}";
		
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){
			$sUpdateQueryResult = $oMysqli->query($sUpdateQuery);
			
		}
		
		//$oMysqli->close();
		return $sUpdateQueryResult;
	}


	//! @brief- Function to Add Recipient for SMS
	//! @params - Receiver No,Reciver Name
	//1 @return type int, it return recipient id
	public function addRecipient($sRMobNo,$sRName){
		
		$iInsertId = 0;
		$sExtra = '';

		$sInsertQuery = "INSERT INTO `sms_recipient`(`id`, `sms_id`, `to_sms_no`, `to_name`, `status`, `extra`) 
						VALUES (NULL,'{$this->iInsertSmsId}','{$sRMobNo}','{$sRName}', 1,'{$sExtra}')";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){
			$sInserQueryResult = $oMysqli->query($sInsertQuery);
			if($sInserQueryResult != false){

				$iInsertId = $oMysqli->insert_id;
			}
		}
		

		$this->aReceiverNo[] = $sRMobNo;
		$this->aReceiverName[] = $sRName;

		$this->iReceiverCount++;

		//$oMysqli->close();
		return $iInsertId;
		
	}


	//! function to delete recipient
	//! @param :recepient id
	//! @return type integer
	public function deleteRecipient($iRecipientId){
		
		$iResult = 0;

		$sUpdateQuery = "UPDATE `sms_recipient` SET `status` = '0' WHERE `id` = '{$iRecipientId}'";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){
			$iResult = $oMysqli->query($sUpdateQuery);
		}

		return $iResult;
	}

	

	//! @brief- Function to Update  Message for SMS
	//! @param - sms updated msg ,sms master id
	//! @return type integer
	public function updateMessage($sUpdatedMessage,$iSmsId){

		$iResult = 0;

		$sUpdateQuery = "UPDATE `sms_master` SET `message` = '{$sUpdatedMessage}' WHERE `id` = '{$iSmsId}'";
		$oMysqli = $this->getEmailSysConn();
		if($oMysqli != false){
			$iResult = $oMysqli->query($sUpdateQuery);
		}

		return $iResult;
	}


	//! @brief- Function to Send SMS
	//! @return type integer
	public function sendSMSNow(){

		$iResult = 0;
		$aRecipientMobNo = implode(",",$this->aReceiverNo);
		$aRecipientName = implode(",",$this->aReceiverName);

		//! Sends the SMS using web2sms API
		$aSmsApiResponce = cURLProcessSMS($aRecipientMobNo,$this->sMessage);
		
		//Decode json response
		$aResult = json_decode($aSmsApiResponce, true);
		$sSmsMessage = $aResult['message'];

		if($aResult['status'] !== 'OK') {
			$this->iExceptionId = 2;
			$this->sExceptionMsg = 'Not Send';
			$this->iSmsStatus = 0;
			$this->sApiMessageId = $sSmsMessage;
			$this->addToQueue(); //! add the sms in queue
			$this->addSmsAttempts(); //! add to sms attempts
		}else{
			$this->iExceptionId = 1;
			$this->sExceptionMsg = 'Send Successfully';
			$this->iSmsStatus = 1;
			$this->sApiMessageId = $sSmsMessage;
			$this->addToQueue(); //! add the sms in queue
			$this->addSmsAttempts(); //! add to sms attempts
			$iResult = 1;
		}

		return $iResult;
	}

	//! brief function to add sms attempts
	//! @return attempt id
	private function addSmsAttempts(){

		$sInsertQuery = "INSERT INTO `sms_attempts`(`id`, `sms_id`, `exception_id`, `exception_msg`, `attempt_on`, `extra`) 
						VALUES (NULL,'{$this->iInsertSmsId}','{$this->iExceptionId}','{$this->sExceptionMsg}','{$this->dSmsTimestamp}','{$this->sApiMessageId}')";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){
			$sInserQueryResult = $oMysqli->query($sInsertQuery);
			if($sInserQueryResult != false){

				$iInsertId = $oMysqli->insert_id;
			}
		}
		
		//$oMysqli->close();

	}

	//! brief function to add Sms data to the queue
	//! @return queue id
	public function addToQueue(){
		
		$iInsertId = 0;
		$sExtra = '';

		$sInsertQuery = "INSERT INTO `sms_queue`(`id`, `sms_id`, `status`, `priority`, `added_on`, `extra`) 
						VALUES (NULL,'{$this->iInsertSmsId}', {$this->iSmsStatus},'{$this->iPriority}','{$this->dSmsTimestamp}','{$sExtra}')";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){
			$sInserQueryResult = $oMysqli->query($sInsertQuery);
			if($sInserQueryResult != false){

				$iInsertId = $oMysqli->insert_id;
			}
		}
		
		//$oMysqli->close();
		return $iInsertId;

	}

	//! @brief- Function to Process SMS Queue
	private function processSmsQueue(){

		$sSelectQuery = "SELECT `sms_id` FROM `sms_queue` WHERE `status` = 0 AND `priority` = 1";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){

			$sSelectQueryResult = $oMysqli->query($sSelectQuery);
			if($sSelectQueryResult != false){

				while($aRow = $sSelectQueryResult->fetch_array()){

					$iSMSId = $aRow['sms_id'];
					if($iSMSId != ''){

						$this->sendPendingSMS($iSMSId);
					}
				}
			}
		}
		//$oMysqli->close();

	}

	//! brief function to create sms
	//! @params sms master id
	private function sendPendingSMS($iSMSId){

		$this->sSenderName = '';
		$this->aReceiverName = array();
		$this->aReceiverNo = array();

		$this->getSmsBasic($iSMSId);
		$this->getSMSRecipient($iSMSId);
		$iResult = $this->sendSMSNow();
		if($iResult == 0){

			$this->updateSmsQueue($iSMSId);
		}
	}

	//! brief function to get sms basic info from sms master
	//! @param sms master id
	private function getSmsBasic($iSMSId){

		$sSelectQuery = "SELECT `sms_master`.`sms_from_no`, `sms_master`.`sms_form_name`, `sms_master`.`message`
						FROM `sms_master` WHERE `sms_master`.`id` = '{$iSMSId}'";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){

			$sSelectQueryResult = $oMysqli->query($sSelectQuery);
			if($sSelectQueryResult != false){

				while($aRow = $sSelectQueryResult->fetch_array()){
					
					$this->sMessage = $aRow['message'];
					
				}
			}
		}
	}

	//! brief function to get valid recipient of sms
	//! @param : sms master id
	private function getSMSRecipient($iSMSId){

		$aRecipients = array();

		$sSelectQuery = "SELECT `sms_recipient`.`to_sms_no`, `sms_recipient`.`to_name`
						FROM `sms_recipient` WHERE `sms_recipient`.`sms_id` = '{$iSMSId}' AND `status` = 1";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){

			$sSelectQueryResult = $oMysqli->query($sSelectQuery);
			if($sSelectQueryResult != false){

				while($aRow = $sSelectQueryResult->fetch_array()){
					
					$aReceiverNo[] = $aRow['to_sms_no'];
					$aReceiverName[]= $aRow['to_name'];
				}
				
			}
		}
	}

	//! brief function to update sms Queue status
	//! @return type integer
	private function updateSmsQueue(){

		$sUpdateQueryResult = '';
		$sUpdateQuery = "UPDATE `sms_queue` SET `status`= 1 WHERE `sms_id`= '{$this->iInsertSmsId}'";
		$oMysqli = $this->getSMSSysConn();
		if($oMysqli != false){

			$sUpdateQueryResult = $oMysqli->query($sUpdateQuery);
		}

		return $sUpdateQueryResult;
	}

}

?>