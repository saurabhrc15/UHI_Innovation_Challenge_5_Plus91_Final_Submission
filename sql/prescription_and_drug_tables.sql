/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 5.7.33 : Database - app_prescription_speech_api
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`app_prescription_speech_api` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `app_prescription_speech_api`;

/*Table structure for table `mxcel_drg_code` */

DROP TABLE IF EXISTS `mxcel_drg_code`;

CREATE TABLE `mxcel_drg_code` (
  `drg_code` varchar(36) DEFAULT NULL,
  `rate` varchar(100) DEFAULT NULL,
  `description` text,
  `class_sub` varchar(15) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `inclusive` text,
  `exclusive` text,
  `notes` text,
  `std_code` char(3) DEFAULT NULL,
  `sub_level` tinyint(4) DEFAULT NULL,
  `remarks` text,
  `extra_codes` text,
  `extra_subclass` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  KEY `drg_code` (`drg_code`),
  KEY `rate` (`rate`),
  KEY `sub_level` (`sub_level`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mxcel_drugs` */

DROP TABLE IF EXISTS `mxcel_drugs`;

CREATE TABLE `mxcel_drugs` (
  `drug_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `drug_code` varchar(50) DEFAULT NULL,
  `drug_name` varchar(100) DEFAULT NULL,
  `generic_name` varchar(100) DEFAULT NULL,
  `formulation` varchar(50) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `drug_for` varchar(20) DEFAULT NULL,
  `instructions` varchar(500) DEFAULT NULL,
  `usefull_in_diseases` varchar(300) DEFAULT NULL,
  `side_effects` varchar(200) DEFAULT NULL,
  `category` varchar(10) DEFAULT NULL,
  `drug_interaction` varchar(250) DEFAULT NULL,
  `contra_indication` varchar(250) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `in_stock` varchar(50) DEFAULT NULL,
  `product_cost` decimal(6,2) DEFAULT NULL,
  `product_price` decimal(6,2) DEFAULT NULL,
  `r_o_a` varchar(100) DEFAULT NULL,
  `specialisation` varchar(150) DEFAULT NULL,
  `creation_date` date NOT NULL DEFAULT '0000-00-00',
  `creation_time` time NOT NULL DEFAULT '00:00:00',
  `creation_user_id` varchar(20) NOT NULL,
  `last_update_date` date NOT NULL DEFAULT '0000-00-00',
  `last_update_time` time NOT NULL DEFAULT '00:00:00',
  `last_update_user_id` varchar(20) NOT NULL,
  `share_with_pharmacies` tinyint(1) DEFAULT '0',
  `create_product` tinyint(1) NOT NULL DEFAULT '0',
  `is_injectable` tinyint(2) DEFAULT '0',
  `is_antibiotic` tinyint(2) DEFAULT '0',
  `allow_to_be_billed_as_cash` tinyint(1) NOT NULL DEFAULT '0',
  `is_anesthesia` tinyint(1) NOT NULL DEFAULT '0',
  `clinic_id` int(11) DEFAULT NULL,
  `is_pain_killer` tinyint(1) NOT NULL DEFAULT '0',
  `is_anti_hypertensive` tinyint(1) NOT NULL DEFAULT '0',
  `is_ivs` tinyint(1) NOT NULL DEFAULT '0',
  `is_diabetic` tinyint(1) NOT NULL DEFAULT '0',
  `is_anti_coagulants` tinyint(1) NOT NULL DEFAULT '0',
  `drug_batch_no` varchar(100) DEFAULT NULL,
  `drug_expiry_date` date DEFAULT NULL,
  `drug_hsn_code` int(50) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  `medicine_type` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`drug_id`),
  KEY `drug_name` (`drug_name`),
  KEY `generic_name` (`generic_name`),
  KEY `drug_name_2` (`drug_name`,`generic_name`),
  KEY `creation_date` (`creation_date`,`creation_user_id`,`last_update_date`,`last_update_user_id`),
  KEY `drug_name_3` (`drug_name`,`generic_name`,`formulation`,`strength`,`creation_date`,`creation_user_id`,`last_update_date`,`last_update_user_id`),
  KEY `create_product` (`create_product`),
  KEY `strength` (`strength`),
  KEY `dosage` (`dosage`),
  KEY `product_cost` (`product_cost`),
  KEY `product_price` (`product_price`),
  KEY `specialisation` (`specialisation`),
  KEY `formulation` (`formulation`),
  KEY `share_with_pharmacies` (`share_with_pharmacies`),
  KEY `creation_date_2` (`creation_date`),
  KEY `creation_time` (`creation_time`),
  KEY `last_update_date` (`last_update_date`),
  KEY `last_update_time` (`last_update_time`),
  KEY `is_injectable` (`is_injectable`),
  KEY `is_antibiotic` (`is_antibiotic`),
  KEY `drug_id` (`drug_id`,`share_with_pharmacies`,`create_product`,`is_injectable`,`is_antibiotic`,`status`),
  KEY `status` (`status`),
  KEY `drug_id_2` (`drug_id`,`share_with_pharmacies`,`create_product`,`is_injectable`,`is_antibiotic`,`status`),
  KEY `is_anesthesia` (`is_anesthesia`),
  KEY `allow_to_be_billed_as_cash` (`allow_to_be_billed_as_cash`),
  KEY `clinic_id` (`clinic_id`),
  KEY `is_pain_killer` (`is_pain_killer`),
  KEY `is_anti_hypertensive` (`is_anti_hypertensive`),
  KEY `is_ivs` (`is_ivs`),
  KEY `is_diabetic` (`is_diabetic`),
  KEY `is_anti_coagulants` (`is_anti_coagulants`)
) ENGINE=InnoDB AUTO_INCREMENT=528 DEFAULT CHARSET=latin1;

/*Table structure for table `mxcel_drugs_frequency_master` */

DROP TABLE IF EXISTS `mxcel_drugs_frequency_master`;

CREATE TABLE `mxcel_drugs_frequency_master` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `frequency_name` varchar(400) DEFAULT NULL,
  `actual_frequency` varchar(200) DEFAULT '1',
  `actual_dosage` varchar(50) DEFAULT NULL,
  `frequency_category` varchar(50) DEFAULT '',
  `time_between_dosage` int(11) NOT NULL DEFAULT '0',
  `dosage_count_in_day` int(11) NOT NULL DEFAULT '0',
  `added_by` int(1) DEFAULT NULL,
  `added_on` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `added_by` (`added_by`),
  KEY `deleted` (`deleted`),
  KEY `id` (`id`,`deleted`),
  KEY `id_2` (`id`,`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8;

/*Table structure for table `mxcel_durg_formulation` */

DROP TABLE IF EXISTS `mxcel_durg_formulation`;

CREATE TABLE `mxcel_durg_formulation` (
  `formulation_id` int(11) NOT NULL AUTO_INCREMENT,
  `formulation` varchar(100) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  PRIMARY KEY (`formulation_id`),
  KEY `status` (`status`),
  KEY `formulation_id` (`formulation_id`,`status`),
  KEY `formulation_id_2` (`formulation_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

/*Table structure for table `mxcel_prescription` */

DROP TABLE IF EXISTS `mxcel_prescription`;

CREATE TABLE `mxcel_prescription` (
  `prescription_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `clinic_id` int(20) DEFAULT NULL,
  `consultation_id` int(20) DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  `creation_time` time NOT NULL DEFAULT '00:00:00',
  `creation_user_id` varchar(20) NOT NULL,
  `last_update_date` date DEFAULT NULL,
  `last_update_time` time NOT NULL DEFAULT '00:00:00',
  `last_update_user_id` varchar(20) NOT NULL,
  `dispensed` tinyint(1) NOT NULL DEFAULT '0',
  `set_as_emergency` tinyint(1) DEFAULT '0' COMMENT '1 means emergency prescription',
  `is_ipd_prescription` tinyint(1) DEFAULT '0' COMMENT '0 - OPD / 1 - IPD',
  `admit_id` int(11) DEFAULT '0',
  `schedule_id` int(11) DEFAULT '0',
  `status` int(1) DEFAULT '1',
  PRIMARY KEY (`prescription_id`),
  KEY `dispensed` (`dispensed`),
  KEY `staff_id` (`staff_id`),
  KEY `clinic_id` (`clinic_id`),
  KEY `set_as_emergency` (`set_as_emergency`),
  KEY `dispensed_2` (`dispensed`),
  KEY `set_as_emergency_2` (`set_as_emergency`),
  KEY `is_ipd_prescription` (`is_ipd_prescription`),
  KEY `admit_id` (`admit_id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `prescription_id` (`prescription_id`,`patient_id`,`staff_id`,`clinic_id`,`consultation_id`,`dispensed`,`set_as_emergency`,`is_ipd_prescription`,`admit_id`,`schedule_id`,`status`),
  KEY `consultation_id` (`consultation_id`),
  KEY `status` (`status`),
  KEY `is_ipd_prescription_2` (`is_ipd_prescription`),
  KEY `prescription_id_2` (`prescription_id`,`patient_id`,`staff_id`,`clinic_id`,`consultation_id`,`dispensed`,`set_as_emergency`,`is_ipd_prescription`,`admit_id`,`schedule_id`,`status`),
  KEY `patient_id` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13822 DEFAULT CHARSET=latin1;

/*Table structure for table `mxcel_prescription_item` */

DROP TABLE IF EXISTS `mxcel_prescription_item`;

CREATE TABLE `mxcel_prescription_item` (
  `prescription_item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prescription_id` int(11) DEFAULT NULL,
  `drug_id` varchar(20) DEFAULT NULL,
  `drug_name` varchar(100) DEFAULT NULL,
  `generic_name` varchar(100) DEFAULT NULL,
  `formulation` varchar(50) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `days` varchar(20) DEFAULT NULL,
  `alert_id` varchar(20) DEFAULT NULL,
  `drug_for` varchar(20) DEFAULT NULL,
  `intake_block` varchar(100) DEFAULT NULL,
  `intake_time` varchar(100) DEFAULT NULL,
  `instructions` varchar(100) DEFAULT NULL,
  `quantity` varchar(10) DEFAULT NULL,
  `prescription_dose` varchar(100) DEFAULT NULL,
  `bill_status` varchar(30) DEFAULT NULL,
  `drug_availability_status` varchar(100) DEFAULT NULL,
  `creation_date` date NOT NULL DEFAULT '0000-00-00',
  `creation_time` time NOT NULL DEFAULT '00:00:00',
  `creation_user_id` varchar(20) NOT NULL,
  `last_update_date` date NOT NULL DEFAULT '0000-00-00',
  `last_update_time` time NOT NULL DEFAULT '00:00:00',
  `last_update_user_id` varchar(20) NOT NULL,
  `no_of_tablets` int(3) DEFAULT NULL COMMENT 'it contains no of tablets for the prescription item',
  `isDispensed` tinyint(1) DEFAULT '0' COMMENT '0 - Not Dispense Yet / 1 - Fully Dispense / 2 - Partial Dispense',
  `isStopped` tinyint(1) DEFAULT '0' COMMENT '1 - Stopped / 0 - Valid',
  `prescription_stopped_by` int(11) DEFAULT NULL,
  `prescription_stopped_on` datetime DEFAULT NULL,
  `reason_for_stopped` text,
  `status` int(1) DEFAULT '1',
  PRIMARY KEY (`prescription_item_id`),
  KEY `drug_id` (`drug_id`),
  KEY `drug_name` (`drug_name`),
  KEY `dosage` (`dosage`),
  KEY `strength` (`strength`),
  KEY `generic_name` (`generic_name`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `intake_block` (`intake_block`),
  KEY `intake_time` (`intake_time`),
  KEY `instructions` (`instructions`),
  KEY `quantity` (`quantity`),
  KEY `prescription_dose` (`prescription_dose`),
  KEY `no_of_tablets` (`no_of_tablets`),
  KEY `isDispensed` (`isDispensed`),
  KEY `isStopped` (`isStopped`),
  KEY `prescription_stopped_by` (`prescription_stopped_by`),
  KEY `prescription_stopped_on` (`prescription_stopped_on`),
  KEY `prescription_item_id` (`prescription_item_id`,`prescription_id`,`drug_id`,`start_date`,`end_date`,`no_of_tablets`,`isDispensed`,`isStopped`,`prescription_stopped_by`,`prescription_stopped_on`,`status`),
  KEY `prescription_id` (`prescription_id`),
  KEY `prescription_id_2` (`prescription_id`),
  KEY `status` (`status`),
  KEY `prescription_item_id_2` (`prescription_item_id`,`prescription_id`,`drug_id`,`start_date`,`end_date`,`no_of_tablets`,`isDispensed`,`isStopped`,`prescription_stopped_by`,`prescription_stopped_on`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=74140 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

DROP TABLE IF EXISTS `mxcel_who_icd_10`;

CREATE TABLE `mxcel_who_icd_10` (
  `diagnosis_code` varchar(12) NOT NULL,
  `description` text NOT NULL,
  `class_sub` varchar(12) NOT NULL,
  `type` varchar(10) NOT NULL,
  `inclusive` text NOT NULL,
  `exclusive` text NOT NULL,
  `notes` text NOT NULL,
  `std_code` char(1) NOT NULL,
  `sub_level` tinyint(4) NOT NULL DEFAULT '0',
  `remarks` text NOT NULL,
  `extra_codes` text NOT NULL,
  `extra_subclass` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`diagnosis_code`),
  KEY `diagnosis_code` (`diagnosis_code`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;