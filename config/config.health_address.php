<?php
include_once dirname(__FILE__)."/../vendor/autoload.php";
require_once dirname(__FILE__)."/config.medixcel.php";
require_once dirname(__FILE__)."/config.service.php";
require_once dirname(__FILE__)."/config.database.php";
require_once dirname(__FILE__)."/config.Email_SMS.php";
require_once dirname(__FILE__)."/config.SMSTemplate.php";

include_once dirname(__FILE__)."/../classes/class.ContainerService.php";
include_once dirname(__FILE__)."/../classes/class.DBConnManager.php";

global $oContainer;
$oContainer = ContainerService::getInstance();