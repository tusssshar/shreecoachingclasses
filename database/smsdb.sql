-- Shree Coaching Classes database (MariaDB 10.4), exported 2026-10-02 13:12
-- Demo data. Secret settings (Twilio, SMTP, Clickatell, cron key) are blank: set them in the app.
-- Session, email and WhatsApp logs: structure only.
-- Import into an empty database, e.g.: mysql -u root smsDB < database/smsdb.sql

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: smsDB
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_syllabus`
--

DROP TABLE IF EXISTS `academic_syllabus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_syllabus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `academic_syllabus_code` longtext NOT NULL,
  `title` longtext NOT NULL,
  `description` longtext NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `uploader_type` longtext NOT NULL,
  `uploader_id` int(11) NOT NULL,
  `session` longtext NOT NULL,
  `timestamp` longtext NOT NULL,
  `file_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_syllabus`
--

LOCK TABLES `academic_syllabus` WRITE;
/*!40000 ALTER TABLE `academic_syllabus` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_syllabus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accountant`
--

DROP TABLE IF EXISTS `accountant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accountant` (
  `accountant_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `birthday` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sex` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `religion` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `blood_group` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `authentication_key` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`accountant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accountant`
--

LOCK TABLES `accountant` WRITE;
/*!40000 ALTER TABLE `accountant` DISABLE KEYS */;
/*!40000 ALTER TABLE `accountant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL,
  `level` longtext NOT NULL,
  `authentication_key` longtext NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (3,'Shree Manager','admin@admin.com','admin','1','');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alumni`
--

DROP TABLE IF EXISTS `alumni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumni` (
  `alumni_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sex` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `profession` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `marital_status` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `g_year` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `club` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `interest` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`alumni_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumni`
--

LOCK TABLES `alumni` WRITE;
/*!40000 ALTER TABLE `alumni` DISABLE KEYS */;
/*!40000 ALTER TABLE `alumni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `answer`
--

DROP TABLE IF EXISTS `answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answer` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `label` varchar(10) NOT NULL DEFAULT 'A',
  `content` text NOT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1041 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answer`
--

LOCK TABLES `answer` WRITE;
/*!40000 ALTER TABLE `answer` DISABLE KEYS */;
INSERT INTO `answer` VALUES (829,208,'A','Run'),(830,208,'B','Happy'),(831,208,'C','Elephant'),(832,208,'D','Quickly'),(833,209,'A','Childs'),(834,209,'B','Children'),(835,209,'C','Childes'),(836,209,'D','Childrens'),(837,210,'A','Warm'),(838,210,'B','Cold'),(839,210,'C','Big'),(840,210,'D','Soft'),(841,211,'A','Recieve'),(842,211,'B','Receive'),(843,211,'C','Receeve'),(844,211,'D','Riceive'),(845,212,'A','go'),(846,212,'B','goes'),(847,212,'C','going'),(848,212,'D','gone'),(849,213,'A','45'),(850,213,'B','60'),(851,213,'C','55'),(852,213,'D','65'),(853,214,'A','21'),(854,214,'B','27'),(855,214,'C','29'),(856,214,'D','33'),(857,215,'A','34%'),(858,215,'B','75%'),(859,215,'C','43%'),(860,215,'D','70%'),(861,216,'A','10 cm'),(862,216,'B','25 cm'),(863,216,'C','20 cm'),(864,216,'D','15 cm'),(865,217,'A','14'),(866,217,'B','49'),(867,217,'C','77'),(868,217,'D','42'),(869,218,'A','Venus'),(870,218,'B','Mars'),(871,218,'C','Jupiter'),(872,218,'D','Saturn'),(873,219,'A','90 C'),(874,219,'B','100 C'),(875,219,'C','110 C'),(876,219,'D','50 C'),(877,220,'A','Respiration'),(878,220,'B','Photosynthesis'),(879,220,'C','Digestion'),(880,220,'D','Evaporation'),(881,221,'A','Carbon dioxide'),(882,221,'B','Oxygen'),(883,221,'C','Nitrogen'),(884,221,'D','Helium'),(885,222,'A','Gold'),(886,222,'B','Iron'),(887,222,'C','Diamond'),(888,222,'D','Glass'),(889,223,'A','Mumbai'),(890,223,'B','New Delhi'),(891,223,'C','Kolkata'),(892,223,'D','Chennai'),(893,224,'A','Gandhi'),(894,224,'B','Nehru'),(895,224,'C','Patel'),(896,224,'D','Bose'),(897,225,'A','Yamuna'),(898,225,'B','Ganga'),(899,225,'C','Godavari'),(900,225,'D','Narmada'),(901,226,'A','1942'),(902,226,'B','1947'),(903,226,'C','1950'),(904,226,'D','1930'),(905,227,'A','Joule'),(906,227,'B','Newton'),(907,227,'C','Watt'),(908,227,'D','Pascal'),(909,228,'A','3 x 10^5 m/s'),(910,228,'B','3 x 10^8 m/s'),(911,228,'C','3 x 10^6 m/s'),(912,228,'D','3 x 10^10 m/s'),(913,229,'A','I/R'),(914,229,'B','IR'),(915,229,'C','R/I'),(916,229,'D','I+R'),(917,230,'A','Joule'),(918,230,'B','Watt'),(919,230,'C','Newton'),(920,230,'D','Volt'),(921,231,'A','CO2'),(922,231,'B','H2O'),(923,231,'C','O2'),(924,231,'D','NaCl'),(925,232,'A','5'),(926,232,'B','7'),(927,232,'C','9'),(928,232,'D','1'),(929,233,'A','12'),(930,233,'B','6'),(931,233,'C','8'),(932,233,'D','14'),(933,234,'A','Sugar'),(934,234,'B','Common salt'),(935,234,'C','Baking soda'),(936,234,'D','Lime'),(937,235,'A','45'),(938,235,'B','60'),(939,235,'C','55'),(940,235,'D','65'),(941,236,'A','21'),(942,236,'B','27'),(943,236,'C','29'),(944,236,'D','33'),(945,237,'A','34%'),(946,237,'B','75%'),(947,237,'C','43%'),(948,237,'D','70%'),(949,238,'A','10 cm'),(950,238,'B','25 cm'),(951,238,'C','20 cm'),(952,238,'D','15 cm'),(953,239,'A','14'),(954,239,'B','49'),(955,239,'C','77'),(956,239,'D','42'),(957,240,'A','Nucleus'),(958,240,'B','Mitochondria'),(959,240,'C','Ribosome'),(960,240,'D','Golgi body'),(961,241,'A','2'),(962,241,'B','4'),(963,241,'C','3'),(964,241,'D','6'),(965,242,'A','Di Nitro Acid'),(966,242,'B','Deoxyribonucleic Acid'),(967,242,'C','Dual Nucleic Acid'),(968,242,'D','None'),(969,243,'A','Liver'),(970,243,'B','Skin'),(971,243,'C','Heart'),(972,243,'D','Brain');
/*!40000 ALTER TABLE `answer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignment`
--

DROP TABLE IF EXISTS `assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignment` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_type` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `class_id` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `timestamp` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`assignment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignment`
--

LOCK TABLES `assignment` WRITE;
/*!40000 ALTER TABLE `assignment` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL COMMENT '0 undefined , 1 present , 2  absent',
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`attendance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=451 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (20,1,19,'2026-09-11'),(22,1,21,'2026-09-11'),(45,1,19,'2026-09-12'),(47,1,21,'2026-09-12'),(70,1,19,'2026-09-14'),(72,1,21,'2026-09-14'),(95,1,19,'2026-09-15'),(97,1,21,'2026-09-15'),(120,1,19,'2026-09-16'),(122,1,21,'2026-09-16'),(145,1,19,'2026-09-17'),(147,1,21,'2026-09-17'),(170,2,19,'2026-09-18'),(172,1,21,'2026-09-18'),(195,1,19,'2026-09-19'),(197,1,21,'2026-09-19'),(220,1,19,'2026-09-21'),(222,1,21,'2026-09-21'),(245,1,19,'2026-09-22'),(247,1,21,'2026-09-22'),(270,1,19,'2026-09-23'),(272,1,21,'2026-09-23'),(295,1,19,'2026-09-24'),(297,1,21,'2026-09-24'),(320,1,19,'2026-09-25'),(322,1,21,'2026-09-25'),(345,1,19,'2026-09-26'),(347,1,21,'2026-09-26'),(370,1,19,'2026-09-28'),(372,1,21,'2026-09-28'),(395,1,19,'2026-09-29'),(397,1,21,'2026-09-29'),(420,1,19,'2026-09-30'),(422,1,21,'2026-09-30'),(445,1,19,'2026-10-01'),(447,1,21,'2026-10-01');
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banner` (
  `banner_id` int(11) NOT NULL AUTO_INCREMENT,
  `b_namea` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `b_nameb` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner`
--

LOCK TABLES `banner` WRITE;
/*!40000 ALTER TABLE `banner` DISABLE KEYS */;
INSERT INTO `banner` VALUES (7,'WE ARE THE BEST IN ALL SITUATIONS','CONGRATULATIONS FOR THE SUCCESS FO THIS PROGRAMME');
/*!40000 ALTER TABLE `banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `board`
--

DROP TABLE IF EXISTS `board`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board` (
  `board_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`board_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board`
--

LOCK TABLES `board` WRITE;
/*!40000 ALTER TABLE `board` DISABLE KEYS */;
INSERT INTO `board` VALUES (1,'CBSE',1),(2,'ICSE',2),(3,'State Board',3),(4,'Mumbai University',4),(5,'SPPU',5),(6,'IB',6),(7,'IGCSE',7),(8,'test',8);
/*!40000 ALTER TABLE `board` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `description` longtext NOT NULL,
  `author` longtext NOT NULL,
  `class_id` longtext NOT NULL,
  `status` longtext NOT NULL,
  `price` longtext NOT NULL,
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book`
--

LOCK TABLES `book` WRITE;
/*!40000 ALTER TABLE `book` DISABLE KEYS */;
INSERT INTO `book` VALUES (2,'PHP','COMPLETE PHP REFERENCE','OPTIMUM LINKUP COMPUTERS','3','available','40000');
/*!40000 ALTER TABLE `book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cbt_exam`
--

DROP TABLE IF EXISTS `cbt_exam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cbt_exam` (
  `exam_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `session` varchar(50) NOT NULL DEFAULT '',
  `exam_date` date NOT NULL,
  `start_time` time NOT NULL DEFAULT '10:00:00',
  `end_time` time DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 30,
  `pass_percent` int(11) NOT NULL DEFAULT 35,
  `instructions` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `results_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` int(11) DEFAULT NULL,
  `results_published_at` int(11) DEFAULT NULL,
  `reminder_sent_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`exam_id`),
  KEY `class_date` (`class_id`,`exam_date`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cbt_exam`
--

LOCK TABLES `cbt_exam` WRITE;
/*!40000 ALTER TABLE `cbt_exam` DISABLE KEYS */;
INSERT INTO `cbt_exam` VALUES (45,'Weekly Quiz 1 - English',12,52,'2026-2027','2026-09-25','10:00:00','20:00:00',15,40,'Read each question carefully. Each question carries the marks shown. No negative marking.','published',1,1790101800,1790447400,NULL,1790015400),(46,'Weekly Quiz 2 - Mathematics',12,53,'2026-2027','2026-09-29','10:00:00','20:00:00',15,40,'Read each question carefully. Each question carries the marks shown. No negative marking.','published',0,1790447400,NULL,NULL,1790361000),(47,'Practice Test - Science',12,54,'2026-2027','2026-10-02','00:05:00','23:55:00',15,40,'Read each question carefully. Each question carries the marks shown. No negative marking.','published',0,1790706600,NULL,NULL,1790620200),(48,'Chapter Test - Social Studies',12,55,'2026-2027','2026-10-05','17:00:00','18:00:00',15,40,'Read each question carefully. Each question carries the marks shown. No negative marking.','published',0,1790965800,NULL,NULL,1790879400),(49,'Weekly Quiz 1 - Physics',13,57,'2026-2027','2026-09-25','10:00:00','20:00:00',15,40,'Read each question carefully. Each question carries the marks shown. No negative marking.','published',1,1790101800,1790447400,NULL,1790015400),(50,'Weekly Quiz 2 - Chemistry',13,58,'2026-2027','2026-09-29','10:00:00','20:00:00',15,40,'Read each question carefully. Each question carries the marks shown. No negative marking.','published',0,1790447400,NULL,NULL,1790361000),(51,'Practice Test - Mathematics',13,59,'2026-2027','2026-10-02','00:05:00','23:55:00',15,40,'Read each question carefully. Each question carries the marks shown. No negative marking.','published',0,1790706600,NULL,NULL,1790620200),(52,'Chapter Test - Biology',13,60,'2026-2027','2026-10-05','17:00:00','18:00:00',15,40,'Read each question carefully. Each question carries the marks shown. No negative marking.','published',0,1790965800,NULL,NULL,1790879400);
/*!40000 ALTER TABLE `cbt_exam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `circular`
--

DROP TABLE IF EXISTS `circular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `circular` (
  `circular_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ref` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`circular_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `circular`
--

LOCK TABLES `circular` WRITE;
/*!40000 ALTER TABLE `circular` DISABLE KEYS */;
INSERT INTO `circular` VALUES (2,'PARENT MEETINGS ASSOCIATION TODAY','PTAMEETINGS','THERE IS GOING TO BE MEETINGS TODAY','Thu, 13 July 2017');
/*!40000 ALTER TABLE `circular` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `name_numeric` longtext NOT NULL,
  `teacher_id` int(11) NOT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class`
--

LOCK TABLES `class` WRITE;
/*!40000 ALTER TABLE `class` DISABLE KEYS */;
INSERT INTO `class` VALUES (1,'Jr KG','-1',2),(2,'Sr KG','0',2),(3,'1st','1',2),(4,'2nd','2',2),(5,'3rd','3',2),(6,'4th','4',2),(7,'5th','5',2),(8,'6th','6',2),(9,'7th','7',2),(10,'8th','8',2),(11,'9th','9',2),(12,'10th','10',2),(13,'11th','11',3),(14,'12th','12',3);
/*!40000 ALTER TABLE `class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_routine`
--

DROP TABLE IF EXISTS `class_routine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_routine` (
  `class_routine_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `time_start` int(11) NOT NULL,
  `time_end` int(11) NOT NULL,
  `time_start_min` int(11) NOT NULL,
  `time_end_min` int(11) NOT NULL,
  `day` longtext NOT NULL,
  PRIMARY KEY (`class_routine_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_routine`
--

LOCK TABLES `class_routine` WRITE;
/*!40000 ALTER TABLE `class_routine` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_routine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `club`
--

DROP TABLE IF EXISTS `club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `club` (
  `club_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `desc` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`club_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `club`
--

LOCK TABLES `club` WRITE;
/*!40000 ALTER TABLE `club` DISABLE KEYS */;
INSERT INTO `club` VALUES (1,'Science','This is just the most important aspect of clubs'),(3,'Jet Club','This is for those that want to go into engineering fields');
/*!40000 ALTER TABLE `club` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `session_name` varchar(20) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `standard_name` varchar(50) DEFAULT NULL,
  `total_fees` decimal(10,2) DEFAULT 0.00,
  `installments` int(11) DEFAULT 1,
  `description` longtext DEFAULT NULL,
  `created_by` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course`
--

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_installment`
--

DROP TABLE IF EXISTS `course_installment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_installment` (
  `installment_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`installment_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_installment`
--

LOCK TABLES `course_installment` WRITE;
/*!40000 ALTER TABLE `course_installment` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_installment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_subject`
--

DROP TABLE IF EXISTS `course_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_subject` (
  `csubject_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `subject_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`csubject_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_subject`
--

LOCK TABLES `course_subject` WRITE;
/*!40000 ALTER TABLE `course_subject` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `description` longtext NOT NULL,
  `file_name` longtext NOT NULL,
  `file_type` longtext NOT NULL,
  `class_id` longtext NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `timestamp` longtext NOT NULL,
  PRIMARY KEY (`document_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document`
--

LOCK TABLES `document` WRITE;
/*!40000 ALTER TABLE `document` DISABLE KEYS */;
INSERT INTO `document` VALUES (5,'assignment','assignment','SIMULATION COMPLETE SOFTWARE.doc','doc','1',0,'1499731200'),(4,'Investment| Management System','Investment| Management System. This is the information am talking about2','OLAWUYI.docx','doc','2',0,'1500495420');
/*!40000 ALTER TABLE `document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dormitory`
--

DROP TABLE IF EXISTS `dormitory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dormitory` (
  `dormitory_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `number_of_room` longtext NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`dormitory_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dormitory`
--

LOCK TABLES `dormitory` WRITE;
/*!40000 ALTER TABLE `dormitory` DISABLE KEYS */;
INSERT INTO `dormitory` VALUES (1,'Main Hall','402','Very Clean Apartment'),(3,'New building','1000','For all students');
/*!40000 ALTER TABLE `dormitory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enquiry`
--

DROP TABLE IF EXISTS `enquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enquiry` (
  `enquiry_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mobile` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `purpose` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `whom_to_meet` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `session_name` varchar(20) DEFAULT NULL,
  `enquiry_no` varchar(50) DEFAULT NULL,
  `enquiry_date` date DEFAULT NULL,
  `enquiry_for` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `source_student` varchar(255) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `assign_to` int(11) DEFAULT NULL,
  `handled_by` int(11) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `remark` longtext DEFAULT NULL,
  `created_by` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`enquiry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enquiry`
--

LOCK TABLES `enquiry` WRITE;
/*!40000 ALTER TABLE `enquiry` DISABLE KEYS */;
/*!40000 ALTER TABLE `enquiry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enquiry_activity`
--

DROP TABLE IF EXISTS `enquiry_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enquiry_activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `enquiry_id` int(11) NOT NULL,
  `status` varchar(30) DEFAULT NULL,
  `note` longtext DEFAULT NULL,
  `created_by` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `enquiry_id` (`enquiry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enquiry_activity`
--

LOCK TABLES `enquiry_activity` WRITE;
/*!40000 ALTER TABLE `enquiry_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `enquiry_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enquiry_category`
--

DROP TABLE IF EXISTS `enquiry_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enquiry_category` (
  `enquirycat_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `purpose` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `whom` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`enquirycat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enquiry_category`
--

LOCK TABLES `enquiry_category` WRITE;
/*!40000 ALTER TABLE `enquiry_category` DISABLE KEYS */;
INSERT INTO `enquiry_category` VALUES (1,'Parent','For Admission','Teacher'),(2,'Vendors','Student Performance','Principal'),(3,'School Staff','Bill Submit','Administrative Office'),(4,'Service Man','Payment Collection','Director'),(5,'Others','Complain by Parent','Reception'),(6,'Visitors','Student Leave Early','Others'),(8,'Guardian','PTA','Student');
/*!40000 ALTER TABLE `enquiry_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam`
--

DROP TABLE IF EXISTS `exam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam` (
  `exam_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `date` longtext NOT NULL,
  `comment` longtext NOT NULL,
  `exam_date` date DEFAULT NULL,
  `class_ids` varchar(255) NOT NULL DEFAULT '',
  `total_marks` int(11) NOT NULL DEFAULT 100,
  `pass_percent` int(11) NOT NULL DEFAULT 35,
  `results_published` tinyint(1) NOT NULL DEFAULT 0,
  `results_published_at` int(11) DEFAULT NULL,
  `notified_at` int(11) DEFAULT NULL,
  `reminder_sent_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam`
--

LOCK TABLES `exam` WRITE;
/*!40000 ALTER TABLE `exam` DISABLE KEYS */;
INSERT INTO `exam` VALUES (1,'Unit Test 1','09/12/2026','Chapters 1-3 of each subject.','2026-09-12','1,2,3,4,5,6,7,8,9,10,11,12,13,14',25,35,1,1789716926,1788593726,NULL),(2,'Mid Term Exam','10/14/2026','Full syllabus till September. Bring your hall ticket.','2026-10-14','1,2,3,4,5,6,7,8,9,10,11,12,13,14',100,35,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `exam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_assignment`
--

DROP TABLE IF EXISTS `exam_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_assignment` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `session` varchar(255) NOT NULL DEFAULT '',
  `student_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'assigned',
  `assigned_at` int(11) DEFAULT NULL,
  `completed_at` int(11) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `started_at` int(11) DEFAULT NULL,
  `submitted_at` int(11) DEFAULT NULL,
  `score` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `notified_at` int(11) DEFAULT NULL,
  `result_notified_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `exam_key` (`class_id`,`subject_id`,`date`,`duration`,`session`),
  KEY `student_id` (`student_id`),
  KEY `exam_id` (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_assignment`
--

LOCK TABLES `exam_assignment` WRITE;
/*!40000 ALTER TABLE `exam_assignment` DISABLE KEYS */;
INSERT INTO `exam_assignment` VALUES (77,12,52,'2026-09-25',15,'2026-2027',19,'submitted',1790101800,1790317560,45,1790317080,1790317560,6.00,6.00,1790101800,NULL),(79,12,53,'2026-09-29',15,'2026-2027',19,'submitted',1790447400,1790658660,46,1790658000,1790658660,4.00,6.00,1790447400,NULL),(81,12,54,'2026-10-02',15,'2026-2027',19,'assigned',1790706600,NULL,47,NULL,NULL,NULL,NULL,1790706600,NULL),(83,12,55,'2026-10-05',15,'2026-2027',19,'assigned',1790965800,NULL,48,NULL,NULL,NULL,NULL,1790965800,NULL),(85,13,57,'2026-09-25',15,'2026-2027',21,'submitted',1790101800,1790318220,49,1790317440,1790318220,3.00,5.00,1790101800,NULL),(87,13,58,'2026-09-29',15,'2026-2027',21,'submitted',1790447400,1790659380,50,1790658660,1790659380,4.00,5.00,1790447400,NULL),(89,13,59,'2026-10-02',15,'2026-2027',21,'assigned',1790706600,NULL,51,NULL,NULL,NULL,NULL,1790706600,NULL),(91,13,60,'2026-10-05',15,'2026-2027',21,'assigned',1790965800,NULL,52,NULL,NULL,NULL,NULL,1790965800,NULL);
/*!40000 ALTER TABLE `exam_assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_result`
--

DROP TABLE IF EXISTS `exam_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_result` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer` varchar(5) DEFAULT NULL,
  `marks_awarded` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'submitted',
  `submitted_at` int(11) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`result_id`),
  KEY `exam_student` (`exam_id`,`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_result`
--

LOCK TABLES `exam_result` WRITE;
/*!40000 ALTER TABLE `exam_result` DISABLE KEYS */;
INSERT INTO `exam_result` VALUES (187,19,208,'C',2.00,'checked',1790317560,45),(188,19,209,'B',1.00,'checked',1790317560,45),(189,19,210,'B',1.00,'checked',1790317560,45),(190,19,211,'B',1.00,'checked',1790317560,45),(191,19,212,'B',1.00,'checked',1790317560,45),(197,19,213,'B',2.00,'checked',1790658660,46),(198,19,214,'C',1.00,'checked',1790658660,46),(199,19,215,'A',0.00,'checked',1790658660,46),(200,19,216,'C',1.00,'checked',1790658660,46),(201,19,217,'C',0.00,'checked',1790658660,46),(207,21,227,'D',0.00,'checked',1790318220,49),(208,21,228,'B',1.00,'checked',1790318220,49),(209,21,229,'B',1.00,'checked',1790318220,49),(210,21,230,'B',1.00,'checked',1790318220,49),(215,21,231,'B',2.00,'checked',1790659380,50),(216,21,232,'B',1.00,'checked',1790659380,50),(217,21,233,'B',1.00,'checked',1790659380,50),(218,21,234,'A',0.00,'checked',1790659380,50);
/*!40000 ALTER TABLE `exam_result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examquestion`
--

DROP TABLE IF EXISTS `examquestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `examquestion` (
  `examquestion_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_type` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `class_id` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` longtext NOT NULL,
  PRIMARY KEY (`examquestion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examquestion`
--

LOCK TABLES `examquestion` WRITE;
/*!40000 ALTER TABLE `examquestion` DISABLE KEYS */;
/*!40000 ALTER TABLE `examquestion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_category`
--

DROP TABLE IF EXISTS `expense_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_category` (
  `expense_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `year` int(4) DEFAULT NULL,
  PRIMARY KEY (`expense_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_category`
--

LOCK TABLES `expense_category` WRITE;
/*!40000 ALTER TABLE `expense_category` DISABLE KEYS */;
INSERT INTO `expense_category` VALUES (1,'Teacher Salary',50000.00,2026),(2,'Classroom Equipments',3000.00,2026),(3,'Classroom Decorations',0.00,NULL),(4,'Inventory Purchase',0.00,NULL),(5,'Exam Accessories',0.00,NULL),(6,'Teacher Salary',30000.00,2025);
/*!40000 ALTER TABLE `expense_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `front_end`
--

DROP TABLE IF EXISTS `front_end`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `front_end` (
  `front_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`front_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `front_end`
--

LOCK TABLES `front_end` WRITE;
/*!40000 ALTER TABLE `front_end` DISABLE KEYS */;
INSERT INTO `front_end` VALUES (3,'about_us',' Welcome to the bank of the 22nd Century. Bank Emerald is a bank that provides innovative solutions for all. We provide 36% interest per annum on all your deposits or 1% monthly on all your deposits. Fund your account instantly and withdraw instantly. Invest in your future today. '),(4,'vision','VISSION The first stage is to have you fund your wallet instantly and invest in Bank Emerald1'),(5,'mission','MISSION The first stage is to have you fund your wallet instantly and invest in Bank Emerald1'),(6,'goal','GOAL The first stage is to have you fund your wallet instantly and invest in Bank Emerald1'),(7,'services','SERVICES At Bank Emerald, our range of services are as below with description. Services we render are reliable and profitable.');
/*!40000 ALTER TABLE `front_end` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade`
--

DROP TABLE IF EXISTS `grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grade` (
  `grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `grade_point` longtext NOT NULL,
  `mark_from` int(11) NOT NULL,
  `mark_upto` int(11) NOT NULL,
  `comment` longtext NOT NULL,
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade`
--

LOCK TABLES `grade` WRITE;
/*!40000 ALTER TABLE `grade` DISABLE KEYS */;
INSERT INTO `grade` VALUES (1,'A1','10',91,100,'Outstanding'),(2,'A2','9',81,90,'Excellent'),(3,'B1','8',71,80,'Very Good'),(4,'B2','7',61,70,'Good'),(5,'C1','6',51,60,'Above Average'),(6,'C2','5',41,50,'Average'),(7,'D','4',33,40,'Pass'),(8,'E','0',0,32,'Needs Improvement');
/*!40000 ALTER TABLE `grade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_desk`
--

DROP TABLE IF EXISTS `help_desk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_desk` (
  `helpdesk_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `purpose` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`helpdesk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_desk`
--

LOCK TABLES `help_desk` WRITE;
/*!40000 ALTER TABLE `help_desk` DISABLE KEYS */;
INSERT INTO `help_desk` VALUES (3,'Omololu Esther','Payment Collection','We not able to pay school fees all the time please find solution to this problem as soon as possible'),(4,'TUNDE ALOWO','SCHOOL FEES PAYMENT','I WANT TO MAKE NOTICE TO THE MANAGEMENT THAT I WAS UNABLE TO MAKE PAYMENT OF MY SCHOOL FEES. THANKS FOR YOUR UNDERSTANDNG');
/*!40000 ALTER TABLE `help_desk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_link`
--

DROP TABLE IF EXISTS `help_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_link` (
  `helplink_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `link` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`helplink_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_link`
--

LOCK TABLES `help_link` WRITE;
/*!40000 ALTER TABLE `help_link` DISABLE KEYS */;
INSERT INTO `help_link` VALUES (3,'THIS IS THE TITLE OF THE ASSIGNMENT','https://www.optimumlinkup.com.ng'),(4,'Introduction to Java Programming','https://www.optimumlinkup.com.ng'),(6,'JAVA VIDEO TUTORIAL','https://www.optimumlinkup.com.ng');
/*!40000 ALTER TABLE `help_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holiday`
--

DROP TABLE IF EXISTS `holiday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holiday` (
  `holiday_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `holiday` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`holiday_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holiday`
--

LOCK TABLES `holiday` WRITE;
/*!40000 ALTER TABLE `holiday` DISABLE KEYS */;
INSERT INTO `holiday` VALUES (1,'Dussehra','Dussehra holiday','Tue, 20 Oct 2026'),(2,'Diwali','Diwali break (Lakshmi Pujan)','Sun, 08 Nov 2026'),(3,'Christmas','Christmas holiday','Fri, 25 Dec 2026'),(4,'Republic Day','Republic Day - flag hoisting at 8 AM','Tue, 26 Jan 2027'),(5,'Holi','Holi holiday','Mon, 22 Mar 2027');
/*!40000 ALTER TABLE `holiday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hostel`
--

DROP TABLE IF EXISTS `hostel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hostel` (
  `hostel_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `birthday` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sex` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `religion` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `blood_group` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `authentication_key` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`hostel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hostel`
--

LOCK TABLES `hostel` WRITE;
/*!40000 ALTER TABLE `hostel` DISABLE KEYS */;
/*!40000 ALTER TABLE `hostel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `description` longtext NOT NULL,
  `amount` int(11) NOT NULL,
  `amount_paid` longtext NOT NULL,
  `due` longtext NOT NULL,
  `creation_timestamp` int(11) NOT NULL,
  `payment_timestamp` longtext NOT NULL,
  `payment_method` longtext NOT NULL,
  `payment_details` longtext NOT NULL,
  `status` longtext NOT NULL COMMENT 'paid or unpaid',
  PRIMARY KEY (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice`
--

LOCK TABLES `invoice` WRITE;
/*!40000 ALTER TABLE `invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `phrase_id` int(11) NOT NULL AUTO_INCREMENT,
  `phrase` longtext NOT NULL,
  `english` longtext NOT NULL DEFAULT '',
  `bengali` longtext NOT NULL DEFAULT '',
  `hindi` longtext NOT NULL DEFAULT '',
  `marathi` longtext NOT NULL DEFAULT '',
  `kannada` longtext NOT NULL DEFAULT '',
  `gujarati` longtext NOT NULL DEFAULT '',
  `tamil` longtext NOT NULL DEFAULT '',
  PRIMARY KEY (`phrase_id`),
  UNIQUE KEY `uniq_phrase` (`phrase`(191))
) ENGINE=MyISAM AUTO_INCREMENT=8106 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language`
--

LOCK TABLES `language` WRITE;
/*!40000 ALTER TABLE `language` DISABLE KEYS */;
INSERT INTO `language` VALUES (1,'login','login','লগইন','लॉगिन','लॉगिन','ಲಾಗಿನ್','લૉગિન','உள்நுழை'),(4,'teacher','Teacher','শিক্ষক','शिक्षक','शिक्षक','ಶಿಕ್ಷಕ','શિક્ષક','ஆசிரியர்'),(5,'student','Student','ছাত্র','छात्र','विद्यार्थी','ವಿದ್ಯಾರ್ಥಿ','વિદ્યાર્થી','மாணவர்'),(6,'parent','Parent','অভিভাবক','अभिभावक','पालक','ಪೋಷಕ','વાલી','பெற்றோர்'),(7,'email','email','ইমেল','ईमेल','ईमेल','ಇಮೇಲ್','ઈમેલ','மின்னஞ்சல்'),(8,'password','password','পাসওয়ার্ড','पासवर्ड','पासवर्ड','ಪಾಸ್‌ವರ್ಡ್','પાસવર્ડ','கடவுச்சொல்'),(10,'reset_password','reset password','পাসওয়ার্ড রিসেট','पासवर्ड रीसेट','पासवर्ड रीसेट करा','ಪಾಸ್‌ವರ್ಡ್ ಮರುಹೊಂದಿಸಿ','પાસવર્ડ રીસેટ','கடவுச்சொல்லை மீட்டமை'),(12,'admin_dashboard','Admin Dashboard','অ্যাডমিন ড্যাশবোর্ড','एडमिन डैशबोर्ड','प्रशासक डॅशबोर्ड','ಅಡ್ಮಿನ್ ಡ್ಯಾಶ್‌ಬೋರ್ಡ್','એડમિન ડેશબોર્ડ','நிர்வாகி டாஷ்போர்டு'),(15,'change_password','change password','পাসওয়ার্ড পরিবর্তন','पासवर्ड बदलें','पासवर्ड बदला','ಪಾಸ್‌ವರ್ಡ್ ಬದಲಿಸಿ','પાસવર્ડ બદલો','கடவுச்சொல்லை மாற்று'),(19,'dashboard','Dashboard','ড্যাশবোর্ড','डैशबोर्ड','डॅशबोर्ड','ಡ್ಯಾಶ್‌ಬೋರ್ಡ್','ડેશબોર્ડ','டாஷ்போர்டு'),(23,'subject','subject','বিষয়','विषय','विषय','ವಿಷಯ','વિષય','பாடம்'),(25,'class','class','ক্লাস','कक्षा','वर्ग','ತರಗತಿ','વર્ગ','வகுப்பு'),(35,'payment','payment','পেমেন্ট','भुगतान','पेमेंट','ಪಾವತಿ','ચુકવણી','கட்டணம்'),(45,'settings','settings','সেটিংস','सेटिंग्स','सेटिंग्ज','ಸೆಟ್ಟಿಂಗ್‌ಗಳು','સેટિંગ્સ','அமைப்புகள்'),(46,'system_settings','system settings','সিস্টেম সেটিংস','सिस्टम सेटिंग्स','सिस्टम सेटिंग्ज','ಸಿಸ್ಟಂ ಸೆಟ್ಟಿಂಗ್‌ಗಳು','સિસ્ટમ સેટિંગ્સ','கணினி அமைப்புகள்'),(47,'manage_language','manage language','ভাষা পরিচালনা','भाषा प्रबंधन','भाषा व्यवस्थापन','ಭಾಷೆ ನಿರ್ವಹಿಸಿ','ભાષા સંચાલન','மொழி நிர்வாகம்'),(51,'manage_teacher','manage teacher','শিক্ষক পরিচালনা','शिक्षक प्रबंधन','शिक्षक व्यवस्थापन','ಶಿಕ್ಷಕ ನಿರ್ವಹಣೆ','શિક્ષક સંચાલન','ஆசிரியர் நிர்வாகம்'),(53,'language','language','ভাষা','भाषा','भाषा','ಭಾಷೆ','ભાષા','மொழி'),(58,'add_student','add student','ছাত্র যোগ করুন','छात्र जोड़ें','विद्यार्थी जोडा','ವಿದ್ಯಾರ್ಥಿ ಸೇರಿಸಿ','વિદ્યાર્થી ઉમેરો','மாணவரைச் சேர்'),(59,'roll','roll','রোল','रोल नंबर','हजेरी क्रमांक','ರೋಲ್ ನಂ.','રોલ નંબર','வரிசை எண்'),(60,'photo','photo','ছবি','फ़ोटो','फोटो','ಫೋಟೋ','ફોટો','புகைப்படம்'),(62,'address','address','ঠিকানা','पता','पत्ता','ವಿಳಾಸ','સરનામું','முகவரி'),(63,'options','options','বিকল্প','विकल्प','पर्याय','ಆಯ್ಕೆಗಳು','વિકલ્પો','விருப்பங்கள்'),(66,'edit','edit','সম্পাদনা','संपादित करें','संपादन','ಸಂಪಾದಿಸಿ','ફેરફાર કરો','திருத்து'),(67,'delete','delete','মুছুন','हटाएं','हटवा','ಅಳಿಸಿ','કાઢી નાખો','நீக்கு'),(70,'name','name','নাম','नाम','नाव','ಹೆಸರು','નામ','பெயர்'),(71,'birthday','birthday','জন্মদিন','जन्मदिन','जन्मतारीख','ಜನ್ಮದಿನಾಂಕ','જન્મ તારીખ','பிறந்தநாள்'),(72,'sex','sex','লিঙ্গ','लिंग','लिंग','ಲಿಂಗ','જાતિ','பாலினம்'),(73,'male','male','পুরুষ','पुरुष','पुरुष','ಪುರುಷ','પુરુષ','ஆண்'),(74,'female','female','মহিলা','महिला','स्त्री','ಮಹಿಳೆ','સ્ત્રી','பெண்'),(77,'phone','phone','ফোন','फ़ोन','फोन','ಫೋನ್','ફોન','தொலைபேசி'),(80,'edit_student','edit student','ছাত্র সম্পাদনা','छात्र संपादित करें','विद्यार्थी संपादन','ವಿದ್ಯಾರ್ಥಿ ಸಂಪಾದಿಸಿ','વિદ્યાર્થી ફેરફાર','மாணவரைத் திருத்து'),(82,'add_teacher','add teacher','শিক্ষক যোগ করুন','शिक्षक जोड़ें','शिक्षक जोडा','ಶಿಕ್ಷಕ ಸೇರಿಸಿ','શિક્ષક ઉમેરો','ஆசிரியரைச் சேர்'),(84,'edit_teacher','edit teacher','শিক্ষক সম্পাদনা','शिक्षक संपादित करें','शिक्षक संपादन','ಶಿಕ್ಷಕ ಸಂಪಾದಿಸಿ','શિક્ષક ફેરફાર','ஆசிரியரைத் திருத்து'),(93,'add','add','যোগ করুন','जोड़ें','जोडा','ಸೇರಿಸಿ','ઉમેરો','சேர்'),(95,'profession','profession','পেশা','पेशा','व्यवसाय','ವೃತ್ತಿ','વ્યવસાય','தொழில்'),(97,'add_parent','add parent','অভিভাবক যোগ করুন','अभिभावक जोड़ें','पालक जोडा','ಪೋಷಕ ಸೇರಿಸಿ','વાલી ઉમેરો','பெற்றோரைச் சேர்'),(98,'manage_subject','manage subject','বিষয় পরিচালনা','विषय प्रबंधन','विषय व्यवस्थापन','ವಿಷಯ ನಿರ್ವಹಣೆ','વિષય સંચાલન','பாட நிர்வாகம்'),(99,'subject_list','subject list','বিষয়ের তালিকা','विषय सूची','विषय यादी','ವಿಷಯ ಪಟ್ಟಿ','વિષય યાદી','பாடப் பட்டியல்'),(100,'add_subject','add subject','বিষয় যোগ করুন','विषय जोड़ें','विषय जोडा','ವಿಷಯ ಸೇರಿಸಿ','વિષય ઉમેરો','பாடத்தைச் சேர்'),(101,'subject_name','subject name','বিষয়ের নাম','विषय का नाम','विषयाचे नाव','ವಿಷಯದ ಹೆಸರು','વિષયનું નામ','பாடத்தின் பெயர்'),(102,'edit_subject','edit subject','বিষয় সম্পাদনা','विषय संपादित करें','विषय संपादन','ವಿಷಯ ಸಂಪಾದಿಸಿ','વિષય ફેરફાર','பாடத்தைத் திருத்து'),(105,'add_class','add class','ক্লাস যোগ করুন','कक्षा जोड़ें','वर्ग जोडा','ತರಗತಿ ಸೇರಿಸಿ','વર્ગ ઉમેરો','வகுப்பைச் சேர்'),(106,'class_name','class name','ক্লাসের নাম','कक्षा का नाम','वर्गाचे नाव','ತರಗತಿಯ ಹೆಸರು','વર્ગનું નામ','வகுப்பின் பெயர்'),(107,'numeric_name','numeric name','সংখ্যাসূচক নাম','संख्यात्मक नाम','अंकी नाव','ಸಂಖ್ಯಾ ಹೆಸರು','આંકડાકીય નામ','எண் பெயர்'),(108,'name_numeric','name numeric','সংখ্যাসূচক নাম দিন','नाम संख्या','नाव अंकी','ಹೆಸರು ಸಂಖ್ಯೆ','નામ આંકડા','பெயர் எண்'),(109,'edit_class','edit class','ক্লাস সম্পাদনা','कक्षा संपादित करें','वर्ग संपादन','ತರಗತಿ ಸಂಪಾದಿಸಿ','વર્ગ ફેરફાર','வகுப்பைத் திருத்து'),(110,'manage_exam','manage exam','পরীক্ষা পরিচালনা','परीक्षा प्रबंधन','परीक्षा व्यवस्थापन','ಪರೀಕ್ಷೆ ನಿರ್ವಹಣೆ','પરીક્ષા સંચાલન','தேர்வு நிர்வாகம்'),(111,'exam_list','exam list','পরীক্ষার তালিকা','परीक्षा सूची','परीक्षा यादी','ಪರೀಕ್ಷೆ ಪಟ್ಟಿ','પરીક્ષા યાદી','தேர்வுப் பட்டியல்'),(112,'add_exam','add exam','পরীক্ষা যোগ করুন','परीक्षा जोड़ें','परीक्षा जोडा','ಪರೀಕ್ಷೆ ಸೇರಿಸಿ','પરીક્ષા ઉમેરો','தேர்வைச் சேர்'),(113,'exam_name','exam name','পরীক্ষার নাম','परीक्षा का नाम','परीक्षेचे नाव','ಪರೀಕ್ಷೆಯ ಹೆಸರು','પરીક્ષાનું નામ','தேர்வின் பெயர்'),(114,'date','date','তারিখ','तारीख','तारीख','ದಿನಾಂಕ','તારીખ','தேதி'),(115,'comment','comment','মন্তব্য','टिप्पणी','टिप्पणी','ಕಾಮೆಂಟ್','ટિપ્પણી','கருத்து'),(116,'edit_exam','edit exam','পরীক্ষা সম্পাদনা','परीक्षा संपादित करें','परीक्षा संपादन','ಪರೀಕ್ಷೆ ಸಂಪಾದಿಸಿ','પરીક્ષા ફેરફાર','தேர்வைத் திருத்து'),(117,'manage_exam_marks','manage exam marks','পরীক্ষার নম্বর পরিচালনা','परीक्षा अंक प्रबंधन','परीक्षा गुण व्यवस्थापन','ಪರೀಕ್ಷಾ ಅಂಕ ನಿರ್ವಹಣೆ','પરીક્ષા ગુણ સંચાલન','தேர்வு மதிப்பெண் நிர்வாகம்'),(118,'manage_marks','manage marks','নম্বর পরিচালনা','अंक प्रबंधन','गुण व्यवस्थापन','ಅಂಕ ನಿರ್ವಹಣೆ','ગુણ સંચાલન','மதிப்பெண் நிர்வாகம்'),(120,'select_class','select class','ক্লাস নির্বাচন করুন','कक्षा चुनें','वर्ग निवडा','ತರಗತಿ ಆಯ್ಕೆಮಾಡಿ','વર્ગ પસંદ કરો','வகுப்பைத் தேர்ந்தெடுக்கவும்'),(121,'select_subject','select subject','বিষয় নির্বাচন করুন','विषय चुनें','विषय निवडा','ವಿಷಯ ಆಯ್ಕೆಮಾಡಿ','વિષય પસંદ કરો','பாடத்தைத் தேர்ந்தெடுக்கவும்'),(124,'attendance','Attendance','উপস্থিতি','उपस्थिति','उपस्थिती','ಹಾಜರಾತಿ','હાજરી','வருகை'),(125,'manage_grade','manage grade','গ্রেড পরিচালনা','ग्रेड प्रबंधन','श्रेणी व्यवस्थापन','ಗ್ರೇಡ್ ನಿರ್ವಹಣೆ','ગ્રેડ સંચાલન','கிரேடு நிர்வாகம்'),(126,'grade_list','grade list','গ্রেডের তালিকা','ग्रेड सूची','श्रेणी यादी','ಗ್ರೇಡ್ ಪಟ್ಟಿ','ગ્રેડ યાદી','கிரேடு பட்டியல்'),(127,'add_grade','add grade','গ্রেড যোগ করুন','ग्रेड जोड़ें','श्रेणी जोडा','ಗ್ರೇಡ್ ಸೇರಿಸಿ','ગ્રેડ ઉમેરો','கிரேடைச் சேர்'),(128,'grade_name','grade name','গ্রেডের নাম','ग्रेड का नाम','श्रेणीचे नाव','ಗ್ರೇಡ್ ಹೆಸರು','ગ્રેડનું નામ','கிரேடு பெயர்'),(129,'grade_point','grade point','গ্রেড পয়েন্ট','ग्रेड पॉइंट','श्रेणी गुण','ಗ್ರೇಡ್ ಪಾಯಿಂಟ್','ગ્રેડ પોઇન્ટ','கிரேடு புள்ளி'),(130,'mark_from','mark from','নম্বর থেকে','अंक से','गुण पासून','ಅಂಕ ಇಂದ','ગુણ થી','மதிப்பெண் முதல்'),(131,'mark_upto','mark upto','নম্বর পর্যন্ত','अंक तक','गुण पर्यंत','ಅಂಕ ವರೆಗೆ','ગુણ સુધી','மதிப்பெண் வரை'),(133,'manage_class_routine','manage class routine','ক্লাস রুটিন পরিচালনা','कक्षा समय-सारणी प्रबंधन','वर्ग वेळापत्रक व्यवस्थापन','ತರಗತಿ ವೇಳಾಪಟ್ಟಿ ನಿರ್ವಹಣೆ','વર્ગ સમયપત્રક સંચાલન','வகுப்பு அட்டவணை நிர்வாகம்'),(140,'manage_invoice/payment','manage invoice/payment','চালান/পেমেন্ট পরিচালনা','इनवॉइस/भुगतान प्रबंधन','बिल/पेमेंट व्यवस्थापन','ಇನ್‌ವಾಯ್ಸ್/ಪಾವತಿ ನಿರ್ವಹಣೆ','ઇન્વોઇસ/ચુકવણી સંચાલન','விலைப்பட்டியல்/கட்டண நிர்வாகம்'),(141,'invoice/payment_list','invoice/payment list','চালান/পেমেন্টের তালিকা','इनवॉइस/भुगतान सूची','बिल/पेमेंट यादी','ಇನ್‌ವಾಯ್ಸ್/ಪಾವತಿ ಪಟ್ಟಿ','ઇન્વોઇસ/ચુકવણી યાદી','விலைப்பட்டியல்/கட்டணப் பட்டியல்'),(142,'add_invoice/payment','add invoice/payment','চালান/পেমেন্ট যোগ করুন','इनवॉइस/भुगतान जोड़ें','बिल/पेमेंट जोडा','ಇನ್‌ವಾಯ್ಸ್/ಪಾವತಿ ಸೇರಿಸಿ','ઇન્વોઇસ/ચુકવણી ઉમેરો','விலைப்பட்டியல்/கட்டணத்தைச் சேர்'),(143,'title','title','শিরোনাম','शीर्षक','शीर्षक','ಶೀರ್ಷಿಕೆ','શીર્ષક','தலைப்பு'),(144,'description','description','বিবরণ','विवरण','वर्णन','ವಿವರಣೆ','વર્ણન','விளக்கம்'),(145,'amount','amount','পরিমাণ','राशि','रक्कम','ಮೊತ್ತ','રકમ','தொகை'),(146,'status','status','অবস্থা','स्थिति','स्थिती','ಸ್ಥಿತಿ','સ્થિતિ','நிலை'),(147,'view_invoice','view invoice','চালান দেখুন','इनवॉइस देखें','बिल पहा','ಇನ್‌ವಾಯ್ಸ್ ವೀಕ್ಷಿಸಿ','ઇન્વોઇસ જુઓ','விலைப்பட்டியலைப் பார்'),(148,'paid','paid','পরিশোধিত','भुगतान किया','भरलेले','ಪಾವತಿಸಲಾಗಿದೆ','ચૂકવેલ','செலுத்தப்பட்டது'),(149,'unpaid','unpaid','অপরিশোধিত','अवैतनिक','न भरलेले','ಪಾವತಿಸಿಲ್ಲ','બાકી','செலுத்தப்படாதது'),(150,'add_invoice','add invoice','চালান যোগ করুন','इनवॉइस जोड़ें','बिल जोडा','ಇನ್‌ವಾಯ್ಸ್ ಸೇರಿಸಿ','ઇન્વોઇસ ઉમેરો','விலைப்பட்டியலைச் சேர்'),(151,'payment_to','payment to','পেমেন্ট প্রাপক','को भुगतान','यांना पेमेंट','ಪಾವತಿ ಸ್ವೀಕರಿಸುವವರು','ચુકવણી કોને','கட்டணம் பெறுபவர்'),(152,'bill_to','bill to','বিল প্রাপক','बिल प्राप्तकर्ता','यांना बिल','ಬಿಲ್ ಪಡೆಯುವವರು','બિલ કોને','பில் பெறுபவர்'),(156,'manage_library_books','manage library books','লাইব্রেরির বই পরিচালনা','पुस्तकालय की पुस्तकों का प्रबंधन','ग्रंथालय पुस्तके व्यवस्थापन','ಗ್ರಂಥಾಲಯ ಪುಸ್ತಕ ನಿರ್ವಹಣೆ','પુસ્તકાલય પુસ્તક સંચાલન','நூலகப் புத்தக நிர்வாகம்'),(160,'author','author','লেখক','लेखक','लेखक','ಲೇಖಕ','લેખક','ஆசிரியர் (நூலாசிரியர்)'),(161,'price','price','মূল্য','कीमत','किंमत','ಬೆಲೆ','કિંમત','விலை'),(162,'available','available','উপলব্ধ','उपलब्ध','उपलब्ध','ಲಭ್ಯವಿದೆ','ઉપલબ્ધ','கிடைக்கிறது'),(163,'unavailable','unavailable','অনুপলব্ধ','अनुपलब्ध','अनुपलब्ध','ಲಭ್ಯವಿಲ್ಲ','અનુપલબ્ધ','கிடைக்கவில்லை'),(164,'edit_book','edit book','বই সম্পাদনা','पुस्तक संपादित करें','पुस्तक संपादन','ಪುಸ್ತಕ ಸಂಪಾದಿಸಿ','પુસ્તક ફેરફાર','புத்தகத்தைத் திருத்து'),(172,'manage_dormitory','manage dormitory','ছাত্রাবাস পরিচালনা','छात्रावास प्रबंधन','वसतिगृह व्यवस्थापन','ವಸತಿ ನಿಲಯ ನಿರ್ವಹಣೆ','છાત્રાલય સંચાલન','விடுதி நிர்வாகம்'),(173,'dormitory_list','dormitory list','ছাত্রাবাসের তালিকা','छात्रावास सूची','वसतिगृह यादी','ವಸತಿ ನಿಲಯ ಪಟ್ಟಿ','છાત્રાલય યાદી','விடுதிப் பட்டியல்'),(174,'add_dormitory','add dormitory','ছাত্রাবাস যোগ করুন','छात्रावास जोड़ें','वसतिगृह जोडा','ವಸತಿ ನಿಲಯ ಸೇರಿಸಿ','છાત્રાલય ઉમેરો','விடுதியைச் சேர்'),(175,'dormitory_name','dormitory name','ছাত্রাবাসের নাম','छात्रावास का नाम','वसतिगृहाचे नाव','ವಸತಿ ನಿಲಯದ ಹೆಸರು','છાત્રાલયનું નામ','விடுதியின் பெயர்'),(176,'number_of_room','number of room','ঘরের সংখ্যা','कमरों की संख्या','खोल्यांची संख्या','ಕೊಠಡಿಗಳ ಸಂಖ್ಯೆ','રૂમની સંખ્યા','அறைகளின் எண்ணிக்கை'),(177,'manage_noticeboard','manage noticeboard','নোটিশবোর্ড পরিচালনা','सूचना पट्ट प्रबंधन','सूचनाफलक व्यवस्थापन','ಸೂಚನಾ ಫಲಕ ನಿರ್ವಹಣೆ','નોટિસબોર્ડ સંચાલન','அறிவிப்புப் பலகை நிர்வாகம்'),(178,'noticeboard_list','noticeboard list','নোটিশবোর্ডের তালিকা','सूचना पट्ट सूची','सूचनाफलक यादी','ಸೂಚನಾ ಫಲಕ ಪಟ್ಟಿ','નોટિસબોર્ડ યાદી','அறிவிப்புப் பலகைப் பட்டியல்'),(179,'add_noticeboard','add noticeboard','নোটিশবোর্ড যোগ করুন','सूचना पट्ट जोड़ें','सूचनाफलक जोडा','ಸೂಚನಾ ಫಲಕ ಸೇರಿಸಿ','નોટિસબોર્ડ ઉમેરો','அறிவிப்புப் பலகையைச் சேர்'),(180,'notice','notice','বিজ্ঞপ্তি','सूचना','सूचना','ಸೂಚನೆ','નોટિસ','அறிவிப்பு'),(181,'add_notice','add notice','নোটিশ যোগ করুন','सूचना जोड़ें','सूचना जोडा','ಸೂಚನೆ ಸೇರಿಸಿ','નોટિસ ઉમેરો','அறிவிப்பைச் சேர்'),(183,'system_name','system name','সিস্টেমের নাম','सिस्टम का नाम','सिस्टमचे नाव','ಸಿಸ್ಟಂ ಹೆಸರು','સિસ્ટમનું નામ','கணினியின் பெயர்'),(184,'save','save','সংরক্ষণ','सहेजें','जतन करा','ಉಳಿಸಿ','સાચવો','சேமி'),(185,'system_title','system title','সিস্টেমের শিরোনাম','सिस्टम शीर्षक','सिस्टम शीर्षक','ಸಿಸ್ಟಂ ಶೀರ್ಷಿಕೆ','સિસ્ટમ શીર્ષક','கணினித் தலைப்பு'),(186,'paypal_email','paypal email','পেপাল ইমেল','पेपैल ईमेल','पेपाल ईमेल','ಪೇಪಾಲ್ ಇಮೇಲ್','પેપાલ ઈમેલ','பேபால் மின்னஞ்சல்'),(187,'currency','currency','মুদ্রা','मुद्रा','चलन','ಕರೆನ್ಸಿ','ચલણ','நாணயம்'),(189,'add_phrase','add phrase','বাক্যাংশ যোগ করুন','वाक्यांश जोड़ें','वाक्यांश जोडा','ಪದಗುಚ್ಛ ಸೇರಿಸಿ','શબ્દસમૂહ ઉમેરો','சொற்றொடரைச் சேர்'),(190,'add_language','add language','ভাষা যোগ করুন','भाषा जोड़ें','भाषा जोडा','ಭಾಷೆ ಸೇರಿಸಿ','ભાષા ઉમેરો','மொழியைச் சேர்'),(191,'phrase','phrase','বাক্যাংশ','वाक्यांश','वाक्यांश','ಪದಗುಚ್ಛ','શબ્દસમૂહ','சொற்றொடர்'),(192,'manage_backup_restore','manage backup restore','ব্যাকআপ ও রিস্টোর পরিচালনা','बैकअप रिस्टोर प्रबंधन','बॅकअप रिस्टोअर व्यवस्थापन','ಬ್ಯಾಕಪ್ ಮರುಸ್ಥಾಪನೆ ನಿರ್ವಹಣೆ','બેકઅપ રીસ્ટોર સંચાલન','காப்பு மற்றும் மீட்டெடுப்பு நிர்வாகம்'),(200,'manage_profile','manage profile','প্রোফাইল পরিচালনা','प्रोफ़ाइल प्रबंधन','प्रोफाइल व्यवस्थापन','ಪ್ರೊಫೈಲ್ ನಿರ್ವಹಣೆ','પ્રોફાઇલ સંચાલન','சுயவிவர நிர்வாகம்'),(201,'update_profile','update profile','প্রোফাইল আপডেট','प्रोफ़ाइल अपडेट करें','प्रोफाइल अपडेट करा','ಪ್ರೊಫೈಲ್ ನವೀಕರಿಸಿ','પ્રોફાઇલ અપડેટ કરો','சுயவிவரத்தைப் புதுப்பி'),(202,'new_password','new password','নতুন পাসওয়ার্ড','नया पासवर्ड','नवीन पासवर्ड','ಹೊಸ ಪಾಸ್‌ವರ್ಡ್','નવો પાસવર્ડ','புதிய கடவுச்சொல்'),(203,'confirm_new_password','confirm new password','নতুন পাসওয়ার্ড নিশ্চিত করুন','नए पासवर्ड की पुष्टि करें','नवीन पासवर्डची खात्री करा','ಹೊಸ ಪಾಸ್‌ವರ್ಡ್ ದೃಢೀಕರಿಸಿ','નવો પાસવર્ડ ફરી લખો','புதிய கடவுச்சொல்லை உறுதிசெய்'),(210,'delete_language','delete language','ভাষা মুছুন','भाषा हटाएं','भाषा हटवा','ಭಾಷೆ ಅಳಿಸಿ','ભાષા કાઢી નાખો','மொழியை நீக்கு'),(211,'settings_updated','settings updated','সেটিংস আপডেট হয়েছে','सेटिंग्स अपडेट हुईं','सेटिंग्ज अपडेट झाल्या','ಸೆಟ್ಟಿಂಗ್‌ಗಳು ನವೀಕರಿಸಲಾಗಿದೆ','સેટિંગ્સ અપડેટ થઈ','அமைப்புகள் புதுப்பிக்கப்பட்டன'),(218,'system_email','system email','সিস্টেম ইমেল','सिस्टम ईमेल','सिस्टम ईमेल','ಸಿಸ್ಟಂ ಇಮೇಲ್','સિસ્ટમ ઈમેલ','கணினி மின்னஞ்சல்'),(219,'option','option','বিকল্প','विकल्प','पर्याय','ಆಯ್ಕೆ','વિકલ્પ','விருப்பம்'),(220,'edit_phrase','edit phrase','বাক্যাংশ সম্পাদনা','वाक्यांश संपादित करें','वाक्यांश संपादन','ಪದಗುಚ್ಛ ಸಂಪಾದಿಸಿ','શબ્દસમૂહ ફેરફાર','சொற்றொடரைத் திருத்து'),(221,'forgot_your_password','Forgot Your Password','আপনার পাসওয়ার্ড ভুলে গেছেন','पासवर्ड भूल गए','पासवर्ड विसरलात','ನಿಮ್ಮ ಪಾಸ್‌ವರ್ಡ್ ಮರೆತಿರಾ','તમારો પાસવર્ડ ભૂલી ગયા','கடவுச்சொல்லை மறந்துவிட்டீர்களா'),(224,'return_to_login_page','Return to Login Page','লগইন পৃষ্ঠায় ফিরে যান','लॉगिन पेज पर लौटें','लॉगिन पृष्ठावर परत जा','ಲಾಗಿನ್ ಪುಟಕ್ಕೆ ಮರಳಿ','લૉગિન પેજ પર પાછા જાઓ','உள்நுழைவுப் பக்கத்திற்குத் திரும்பு'),(225,'admit_student','Admit Student','ছাত্র ভর্তি','छात्र प्रवेश','विद्यार्थी प्रवेश','ವಿದ್ಯಾರ್ಥಿ ಪ್ರವೇಶ','વિદ્યાર્થી પ્રવેશ','மாணவர் சேர்க்கை'),(226,'admit_bulk_student','Admit Bulk Student','একসাথে অনেক ছাত্র ভর্তি','थोक छात्र प्रवेश','सामूहिक विद्यार्थी प्रवेश','ಸಾಮೂಹಿಕ ವಿದ್ಯಾರ್ಥಿ ಪ್ರವೇಶ','સામૂહિક વિદ્યાર્થી પ્રવેશ','மாணவர்களை மொத்தமாகச் சேர்'),(227,'student_information','Student Information','ছাত্রের তথ্য','छात्र जानकारी','विद्यार्थी माहिती','ವಿದ್ಯಾರ್ಥಿ ಮಾಹಿತಿ','વિદ્યાર્થી માહિતી','மாணவர் தகவல்'),(228,'student_marksheet','Student Mark Sheet','ছাত্রের মার্কশিট','छात्र अंकपत्र','विद्यार्थी गुणपत्रिका','ವಿದ್ಯಾರ್ಥಿ ಅಂಕಪಟ್ಟಿ','વિદ્યાર્થી માર્કશીટ','மாணவர் மதிப்பெண் பட்டியல்'),(230,'exam_grades','Exam Grades','পরীক্ষার গ্রেড','परीक्षा ग्रेड','परीक्षा श्रेणी','ಪರೀಕ್ಷಾ ಗ್ರೇಡ್‌ಗಳು','પરીક્ષા ગ્રેડ','தேர்வு கிரேடுகள்'),(232,'general_settings','General Settings','সাধারণ সেটিংস','सामान्य सेटिंग्स','सामान्य सेटिंग्ज','ಸಾಮಾನ್ಯ ಸೆಟ್ಟಿಂಗ್‌ಗಳು','સામાન્ય સેટિંગ્સ','பொது அமைப்புகள்'),(233,'language_settings','Language Settings','ভাষা সেটিংস','भाषा सेटिंग्स','भाषा सेटिंग्ज','ಭಾಷಾ ಸೆಟ್ಟಿಂಗ್‌ಗಳು','ભાષા સેટિંગ્સ','மொழி அமைப்புகள்'),(234,'edit_profile','Edit Profile','প্রোফাইল সম্পাদনা','प्रोफ़ाइल संपादित करें','प्रोफाइल संपादन','ಪ್ರೊಫೈಲ್ ಸಂಪಾದಿಸಿ','પ્રોફાઇલ ફેરફાર','சுயவிவரத்தைத் திருத்து'),(235,'event_schedule','Event Schedule','ইভেন্টের সময়সূচি','कार्यक्रम सूची','कार्यक्रम वेळापत्रक','ಕಾರ್ಯಕ್ರಮ ವೇಳಾಪಟ್ಟಿ','કાર્યક્રમ સમયપત્રક','நிகழ்வு அட்டவணை'),(236,'cancel','Cancel','বাতিল','रद्द करें','रद्द करा','ರದ್ದುಮಾಡಿ','રદ કરો','ரத்து செய்'),(238,'value_required','Value Required','মান আবশ্যক','मान आवश्यक है','मूल्य आवश्यक','ಮೌಲ್ಯ ಅಗತ್ಯವಿದೆ','મૂલ્ય જરૂરી છે','மதிப்பு தேவை'),(239,'select','Select','নির্বাচন করুন','चुनें','निवडा','ಆಯ್ಕೆಮಾಡಿ','પસંદ કરો','தேர்ந்தெடு'),(240,'gender','Gender','লিঙ্গ','लिंग','लिंग','ಲಿಂಗ','જાતિ','பாலினம்'),(241,'add_bulk_student','Add Bulk Student','একসাথে অনেক ছাত্র যোগ করুন','थोक छात्र जोड़ें','सामूहिक विद्यार्थी जोडा','ಸಾಮೂಹಿಕ ವಿದ್ಯಾರ್ಥಿ ಸೇರಿಸಿ','સામૂહિક વિદ્યાર્થી ઉમેરો','மாணவர்களை மொத்தமாகச் சேர்'),(242,'student_bulk_add_form','Student Bulk Add Form','ছাত্র বাল্ক যোগ ফর্ম','थोक छात्र जोड़ने का फॉर्म','सामूहिक विद्यार्थी जोडण्याचा अर्ज','ವಿದ್ಯಾರ್ಥಿ ಸಾಮೂಹಿಕ ಸೇರ್ಪಡೆ ಫಾರ್ಮ್','સામૂહિક વિદ્યાર્થી ઉમેરવાનું ફોર્મ','மாணவர் மொத்த சேர்க்கைப் படிவம்'),(244,'upload_and_import','Upload And Import','আপলোড ও ইমপোর্ট','अपलोड और इम्पोर्ट करें','अपलोड व इम्पोर्ट करा','ಅಪ್‌ಲೋಡ್ ಮತ್ತು ಆಮದು','અપલોડ અને ઇમ્પોર્ટ કરો','பதிவேற்றி இறக்குமதி செய்'),(247,'add_new_teacher','Add New Teacher','নতুন শিক্ষক যোগ করুন','नया शिक्षक जोड़ें','नवीन शिक्षक जोडा','ಹೊಸ ಶಿಕ್ಷಕ ಸೇರಿಸಿ','નવા શિક્ષક ઉમેરો','புதிய ஆசிரியரைச் சேர்'),(252,'update','Update','আপডেট','अपडेट करें','अपडेट करा','ನವೀಕರಿಸಿ','અપડેટ કરો','புதுப்பி'),(253,'section','Section','সেকশন','सेक्शन','तुकडी','ವಿಭಾಗ','વિભાગ','பிரிவு'),(257,'add_form','Add Form','ফর্ম যোগ করুন','फॉर्म जोड़ें','अर्ज जोडा','ಫಾರ್ಮ್ ಸೇರಿಸಿ','ફોર્મ ઉમેરો','படிவத்தைச் சேர்'),(258,'all_parents','All Parents','সব অভিভাবক','सभी अभिभावक','सर्व पालक','ಎಲ್ಲಾ ಪೋಷಕರು','બધા વાલીઓ','அனைத்துப் பெற்றோர்'),(259,'parents','Parents','অভিভাবকগণ','अभिभावक','पालक','ಪೋಷಕರು','વાલીઓ','பெற்றோர்'),(260,'add_new_parent','Add New Parent','নতুন অভিভাবক যোগ করুন','नया अभिभावक जोड़ें','नवीन पालक जोडा','ಹೊಸ ಪೋಷಕ ಸೇರಿಸಿ','નવા વાલી ઉમેરો','புதிய பெற்றோரைச் சேர்'),(261,'add_new_student','Add New Student','নতুন ছাত্র যোগ করুন','नया छात्र जोड़ें','नवीन विद्यार्थी जोडा','ಹೊಸ ವಿದ್ಯಾರ್ಥಿ ಸೇರಿಸಿ','નવા વિદ્યાર્થી ઉમેરો','புதிய மாணவரைச் சேர்'),(262,'all_students','All Students','সব ছাত্র','सभी छात्र','सर्व विद्यार्थी','ಎಲ್ಲಾ ವಿದ್ಯಾರ್ಥಿಗಳು','બધા વિદ્યાર્થીઓ','அனைத்து மாணவர்கள்'),(264,'text_align','Text Align','টেক্সট অ্যালাইন','टेक्स्ट संरेखण','मजकूर संरेखन','ಪಠ್ಯ ಜೋಡಣೆ','ટેક્સ્ટ ગોઠવણી','உரை சீரமைப்பு'),(265,'clickatell_username','Clickatell Username','ক্লিকাটেল ইউজারনেম','क्लिकैटेल यूज़रनेम','क्लिकॅटेल वापरकर्तानाव','ಕ್ಲಿಕಾಟೆಲ್ ಬಳಕೆದಾರ ಹೆಸರು','ક્લિકેટેલ યુઝરનેમ','கிளிக்டெல் பயனர்பெயர்'),(266,'clickatell_password','Clickatell Password','ক্লিকাটেল পাসওয়ার্ড','क्लिकैटेल पासवर्ड','क्लिकॅटेल पासवर्ड','ಕ್ಲಿಕಾಟೆಲ್ ಪಾಸ್‌ವರ್ಡ್','ક્લિકેટેલ પાસવર્ડ','கிளிக்டெல் கடவுச்சொல்'),(267,'clickatell_api_id','Clickatell Api Id','ক্লিকাটেল এপিআই আইডি','क्लिकैटेल एपीआई आईडी','क्लिकॅटेल एपीआय आयडी','ಕ್ಲಿಕಾಟೆಲ್ API ಐಡಿ','ક્લિકેટેલ API આઈડી','கிளிக்டெல் API ஐடி'),(268,'sms_settings','Sms Settings','এসএমএস সেটিংস','एसएमएस सेटिंग्स','एसएमएस सेटिंग्ज','SMS ಸೆಟ್ಟಿಂಗ್‌ಗಳು','SMS સેટિંગ્સ','SMS அமைப்புகள்'),(269,'data_updated','Data Updated','তথ্য আপডেট হয়েছে','डेटा अपडेट हुआ','डेटा अपडेट झाला','ಡೇಟಾ ನವೀಕರಿಸಲಾಗಿದೆ','ડેટા અપડેટ થયો','தரவு புதுப்பிக்கப்பட்டது'),(270,'data_added_successfully','Data Added Successfully','তথ্য সফলভাবে যোগ হয়েছে','डेटा सफलतापूर्वक जोड़ा गया','डेटा यशस्वीरित्या जोडला','ಡೇಟಾ ಯಶಸ್ವಿಯಾಗಿ ಸೇರಿಸಲಾಗಿದೆ','ડેટા સફળતાપૂર્વક ઉમેરાયો','தரவு வெற்றிகரமாகச் சேர்க்கப்பட்டது'),(271,'edit_notice','Edit Notice','নোটিশ সম্পাদনা','सूचना संपादित करें','सूचना संपादन','ಸೂಚನೆ ಸಂಪಾದಿಸಿ','નોટિસ ફેરફાર','அறிவிப்பைத் திருத்து'),(272,'private_messaging','Private Messaging','ব্যক্তিগত বার্তা','निजी संदेश','खाजगी संदेशन','ಖಾಸಗಿ ಸಂದೇಶ','ખાનગી સંદેશાવ્યવહાર','தனிப்பட்ட செய்தியிடல்'),(273,'messages','Messages','বার্তাসমূহ','संदेश','संदेश','ಸಂದೇಶಗಳು','સંદેશાઓ','செய்திகள்'),(277,'select_a_user','Select A User','একজন ব্যবহারকারী নির্বাচন করুন','उपयोगकर्ता चुनें','वापरकर्ता निवडा','ಬಳಕೆದಾರರನ್ನು ಆಯ್ಕೆಮಾಡಿ','વપરાશકર્તા પસંદ કરો','பயனரைத் தேர்ந்தெடுக்கவும்'),(280,'current_password','Current Password','বর্তমান পাসওয়ার্ড','वर्तमान पासवर्ड','सध्याचा पासवर्ड','ಪ್ರಸ್ತುತ ಪಾಸ್‌ವರ್ಡ್','હાલનો પાસવર્ડ','தற்போதைய கடவுச்சொல்'),(283,'total_marks','Total Marks','মোট নম্বর','कुल अंक','एकूण गुण','ಒಟ್ಟು ಅಂಕಗಳು','કુલ ગુણ','மொத்த மதிப்பெண்கள்'),(285,'theme_settings','Theme Settings','থিম সেটিংস','थीम सेटिंग्स','थीम सेटिंग्ज','ಥೀಮ್ ಸೆಟ್ಟಿಂಗ್‌ಗಳು','થીમ સેટિંગ્સ','தீம் அமைப்புகள்'),(286,'select_theme','Select Theme','থিম নির্বাচন করুন','थीम चुनें','थीम निवडा','ಥೀಮ್ ಆಯ್ಕೆಮಾಡಿ','થીમ પસંદ કરો','தீமைத் தேர்ந்தெடு'),(287,'theme_selected','Theme Selected','থিম নির্বাচিত হয়েছে','थीम चुनी गई','थीम निवडली','ಥೀಮ್ ಆಯ್ಕೆಮಾಡಲಾಗಿದೆ','થીમ પસંદ થઈ','தீம் தேர்ந்தெடுக்கப்பட்டது'),(288,'language_list','Language List','ভাষার তালিকা','भाषा सूची','भाषा यादी','ಭಾಷಾ ಪಟ್ಟಿ','ભાષા યાદી','மொழிப் பட்டியல்'),(290,'study_material','Study Material','পাঠ্য সামগ্রী','अध्ययन सामग्री','अभ्यास साहित्य','ಅಧ್ಯಯನ ಸಾಮಗ್ರಿ','અભ્યાસ સામગ્રી','கற்றல் பொருட்கள்'),(292,'select_a_theme_to_make_changes','Select A Theme To Make Changes','পরিবর্তনের জন্য একটি থিম নির্বাচন করুন','बदलाव के लिए थीम चुनें','बदल करण्यासाठी थीम निवडा','ಬದಲಾವಣೆಗೆ ಥೀಮ್ ಆಯ್ಕೆಮಾಡಿ','ફેરફાર કરવા થીમ પસંદ કરો','மாற்றங்கள் செய்ய ஒரு தீமைத் தேர்ந்தெடுக்கவும்'),(293,'manage_daily_attendance','Manage Daily Attendance','দৈনিক উপস্থিতি পরিচালনা','दैनिक उपस्थिति प्रबंधन','दैनिक उपस्थिती व्यवस्थापन','ದೈನಂದಿನ ಹಾಜರಾತಿ ನಿರ್ವಹಣೆ','દૈનિક હાજરી સંચાલન','தினசரி வருகை நிர்வாகம்'),(298,'twilio_account','Twilio Account','টুইলিও অ্যাকাউন্ট','ट्विलियो खाता','ट्विलिओ खाते','ಟ್ವಿಲಿಯೊ ಖಾತೆ','ટ્વિલિયો ખાતું','ட்விலியோ கணக்கு'),(299,'authentication_token','Authentication Token','অথেন্টিকেশন টোকেন','प्रमाणीकरण टोकन','प्रमाणीकरण टोकन','ದೃಢೀಕರಣ ಟೋಕನ್','ઓથેન્ટિકેશન ટોકન','அங்கீகார டோக்கன்'),(300,'registered_phone_number','Registered Phone Number','নিবন্ধিত ফোন নম্বর','पंजीकृत फ़ोन नंबर','नोंदणीकृत फोन नंबर','ನೋಂದಾಯಿತ ಫೋನ್ ಸಂಖ್ಯೆ','નોંધાયેલ ફોન નંબર','பதிவு செய்யப்பட்ட தொலைபேசி எண்'),(301,'select_a_service','Select A Service','একটি সেবা নির্বাচন করুন','सेवा चुनें','सेवा निवडा','ಸೇವೆಯನ್ನು ಆಯ್ಕೆಮಾಡಿ','સેવા પસંદ કરો','சேவையைத் தேர்ந்தெடுக்கவும்'),(302,'active','Active','সক্রিয়','सक्रिय','सक्रिय','ಸಕ್ರಿಯ','સક્રિય','செயலில்'),(304,'not_selected','Not Selected','নির্বাচিত নয়','चयनित नहीं','निवडलेले नाही','ಆಯ್ಕೆಮಾಡಿಲ್ಲ','પસંદ કરેલ નથી','தேர்ந்தெடுக்கப்படவில்லை'),(305,'disabled','Disabled','নিষ্ক্রিয়','बंद','बंद','ನಿಷ್ಕ್ರಿಯಗೊಳಿಸಲಾಗಿದೆ','બંધ','முடக்கப்பட்டது'),(308,'accounting','Accounting','হিসাবরক্ষণ','लेखा','लेखा','ಲೆಕ್ಕಪತ್ರ','હિસાબી','கணக்கியல்'),(310,'expense','Expense','ব্যয়','व्यय','खर्च','ವೆಚ್ಚ','ખર્ચ','செலவு'),(312,'invoice_informations','Invoice Informations','চালানের তথ্য','इनवॉइस की जानकारी','बिल माहिती','ಇನ್‌ವಾಯ್ಸ್ ಮಾಹಿತಿ','ઇન્વોઇસ માહિતી','விலைப்பட்டியல் தகவல்கள்'),(313,'payment_informations','Payment Informations','পেমেন্টের তথ্য','भुगतान की जानकारी','पेमेंट माहिती','ಪಾವತಿ ಮಾಹಿತಿ','ચુકવણી માહિતી','கட்டணத் தகவல்கள்'),(314,'total','Total','মোট','कुल','एकूण','ಒಟ್ಟು','કુલ','மொத்தம்'),(315,'enter_total_amount','Enter Total Amount','মোট পরিমাণ লিখুন','कुल राशि दर्ज करें','एकूण रक्कम टाका','ಒಟ್ಟು ಮೊತ್ತ ನಮೂದಿಸಿ','કુલ રકમ લખો','மொத்தத் தொகையை உள்ளிடவும்'),(316,'enter_payment_amount','Enter Payment Amount','পেমেন্টের পরিমাণ লিখুন','भुगतान राशि दर्ज करें','भरणा रक्कम टाका','ಪಾವತಿ ಮೊತ್ತ ನಮೂದಿಸಿ','ચુકવણીની રકમ લખો','கட்டணத் தொகையை உள்ளிடவும்'),(318,'method','Method','পদ্ধতি','तरीका','पद्धत','ವಿಧಾನ','પદ્ધતિ','முறை'),(319,'cash','Cash','নগদ','नकद','रोख','ನಗದು','રોકડ','ரொக்கம்'),(320,'check','Check','চেক','चेक','धनादेश','ಚೆಕ್','ચેક','காசோலை'),(321,'card','Card','কার্ড','कार्ड','कार्ड','ಕಾರ್ಡ್','કાર્ડ','கார்டு'),(322,'data_deleted','Data Deleted','তথ্য মুছে ফেলা হয়েছে','डेटा हटाया गया','डेटा हटवला','ಡೇಟಾ ಅಳಿಸಲಾಗಿದೆ','ડેટા કાઢી નાખ્યો','தரவு நீக்கப்பட்டது'),(323,'total_amount','Total Amount','মোট পরিমাণ','कुल राशि','एकूण रक्कम','ಒಟ್ಟು ಮೊತ್ತ','કુલ રકમ','மொத்தத் தொகை'),(324,'take_payment','Take Payment','পেমেন্ট নিন','भुगतान लें','पेमेंट घ्या','ಪಾವತಿ ಸ್ವೀಕರಿಸಿ','ચુકવણી લો','கட்டணம் பெறு'),(325,'payment_history','Payment History','পেমেন্টের ইতিহাস','भुगतान इतिहास','पेमेंट इतिहास','ಪಾವತಿ ಇತಿಹಾಸ','ચુકવણી ઇતિહાસ','கட்டண வரலாறு'),(326,'amount_paid','Amount Paid','প্রদত্ত পরিমাণ','चुकाई गई राशि','भरलेली रक्कम','ಪಾವತಿಸಿದ ಮೊತ್ತ','ચૂકવેલ રકમ','செலுத்திய தொகை'),(327,'due','Due','বকেয়া','बकाया','थकबाकी','ಬಾಕಿ','બાકી રકમ','நிலுவை'),(329,'creation_date','Creation Date','তৈরির তারিখ','बनाने की तारीख','तयार केल्याची तारीख','ರಚನೆ ದಿನಾಂಕ','બનાવ્યાની તારીખ','உருவாக்கிய தேதி'),(331,'paid_amount','Paid Amount','প্রদত্ত পরিমাণ','चुकाई गई राशि','भरलेली रक्कम','ಪಾವತಿಸಿದ ಮೊತ್ತ','ચૂકવેલ રકમ','செலுத்திய தொகை'),(332,'send_sms_to_all','Send Sms To All','সবাইকে এসএমএস পাঠান','सभी को एसएमएस भेजें','सर्वांना एसएमएस पाठवा','ಎಲ್ಲರಿಗೂ SMS ಕಳುಹಿಸಿ','બધાને SMS મોકલો','அனைவருக்கும் SMS அனுப்பு'),(333,'yes','Yes','হ্যাঁ','हाँ','होय','ಹೌದು','હા','ஆம்'),(334,'no','No','না','नहीं','नाही','ಇಲ್ಲ','ના','இல்லை'),(335,'activated','Activated','সক্রিয় করা হয়েছে','सक्रिय किया गया','सक्रिय केले','ಸಕ್ರಿಯಗೊಳಿಸಲಾಗಿದೆ','સક્રિય કરેલ','செயல்படுத்தப்பட்டது'),(336,'sms_service_not_activated','Sms Service Not Activated','এসএমএস সেবা সক্রিয় নয়','एसएमएस सेवा सक्रिय नहीं है','एसएमएस सेवा सक्रिय नाही','SMS ಸೇವೆ ಸಕ್ರಿಯಗೊಂಡಿಲ್ಲ','SMS સેવા સક્રિય નથી','SMS சேவை செயல்படுத்தப்படவில்லை'),(338,'file','File','ফাইল','फ़ाइल','फाइल','ಫೈಲ್','ફાઇલ','கோப்பு'),(339,'file_type','File Type','ফাইলের ধরন','फ़ाइल प्रकार','फाइल प्रकार','ಫೈಲ್ ಪ್ರಕಾರ','ફાઇલનો પ્રકાર','கோப்பு வகை'),(340,'select_file_type','Select File Type','ফাইলের ধরন নির্বাচন করুন','फ़ाइल प्रकार चुनें','फाइल प्रकार निवडा','ಫೈಲ್ ಪ್ರಕಾರ ಆಯ್ಕೆಮಾಡಿ','ફાઇલનો પ્રકાર પસંદ કરો','கோப்பு வகையைத் தேர்ந்தெடுக்கவும்'),(341,'image','Image','ছবি','छवि','प्रतिमा','ಚಿತ್ರ','છબી','படம்'),(342,'doc','Doc','ডক','डॉक','दस्तऐवज','ಡಾಕ್','ડૉક','ஆவணம்'),(343,'pdf','Pdf','পিডিএফ','पीडीएफ','पीडीएफ','PDF','PDF','PDF'),(344,'excel','Excel','এক্সেল','एक्सेल','एक्सेल','ಎಕ್ಸೆಲ್','એક્સેલ','எக்செல்'),(345,'other','Other','অন্যান্য','अन्य','इतर','ಇತರೆ','અન્ય','மற்றவை'),(346,'expenses','Expenses','ব্যয়সমূহ','व्यय','खर्च','ವೆಚ್ಚಗಳು','ખર્ચાઓ','செலவுகள்'),(349,'edit_expense','Edit Expense','ব্যয় সম্পাদনা','व्यय संपादित करें','खर्च संपादन','ವೆಚ್ಚ ಸಂಪಾದಿಸಿ','ખર્ચ ફેરફાર','செலவைத் திருத்து'),(351,'send_marks_by_sms','Send Marks By Sms','এসএমএসে নম্বর পাঠান','एसएमएस से अंक भेजें','एसएमएसने गुण पाठवा','SMS ಮೂಲಕ ಅಂಕಗಳನ್ನು ಕಳುಹಿಸಿ','SMS દ્વારા ગુણ મોકલો','SMS மூலம் மதிப்பெண்களை அனுப்பு'),(354,'students','Students','ছাত্রগণ','छात्र','विद्यार्थी','ವಿದ್ಯಾರ್ಥಿಗಳು','વિદ્યાર્થીઓ','மாணவர்கள்'),(358,'expense_category','Expense Category','ব্যয়ের বিভাগ','व्यय श्रेणी','खर्च वर्ग','ವೆಚ್ಚ ವರ್ಗ','ખર્ચ શ્રેણી','செலவு வகை'),(359,'add_new_expense_category','Add New Expense Category','নতুন ব্যয়ের বিভাগ যোগ করুন','नई व्यय श्रेणी जोड़ें','नवीन खर्च वर्ग जोडा','ಹೊಸ ವೆಚ್ಚ ವರ್ಗ ಸೇರಿಸಿ','નવી ખર્ચ શ્રેણી ઉમેરો','புதிய செலவு வகையைச் சேர்'),(360,'add_expense_category','Add Expense Category','ব্যয়ের বিভাগ যোগ করুন','व्यय श्रेणी जोड़ें','खर्च वर्ग जोडा','ವೆಚ್ಚ ವರ್ಗ ಸೇರಿಸಿ','ખર્ચ શ્રેણી ઉમેરો','செலவு வகையைச் சேர்'),(361,'category','Category','বিভাগ','श्रेणी','वर्ग','ವರ್ಗ','શ્રેણી','வகை'),(362,'select_expense_category','Select Expense Category','ব্যয়ের বিভাগ নির্বাচন করুন','व्यय श्रेणी चुनें','खर्च वर्ग निवडा','ವೆಚ್ಚ ವರ್ಗ ಆಯ್ಕೆಮಾಡಿ','ખર્ચ શ્રેણી પસંદ કરો','செலவு வகையைத் தேர்ந்தெடுக்கவும்'),(365,'account_updated','Account Updated','অ্যাকাউন্ট আপডেট হয়েছে','खाता अपडेट हुआ','खाते अपडेट झाले','ಖಾತೆ ನವೀಕರಿಸಲಾಗಿದೆ','ખાતું અપડેટ થયું','கணக்கு புதுப்பிக்கப்பட்டது'),(366,'upload_logo','Upload Logo','লোগো আপলোড','लोगो अपलोड करें','लोगो अपलोड करा','ಲೋಗೋ ಅಪ್‌ಲೋಡ್ ಮಾಡಿ','લોગો અપલોડ કરો','லோகோவைப் பதிவேற்று'),(367,'upload','Upload','আপলোড','अपलोड करें','अपलोड','ಅಪ್‌ಲೋಡ್','અપલોડ','பதிவேற்று'),(371,'default','Default','ডিফল্ট','डिफ़ॉल्ट','डीफॉल्ट','ಡೀಫಾಲ್ಟ್','ડિફૉલ્ટ','இயல்புநிலை'),(372,'tabulation_sheet','Tabulation Sheet','ট্যাবুলেশন শিট','टैबुलेशन शीट','तक्ता पत्रक','ಕೋಷ್ಟಕ ಪಟ್ಟಿ','ટેબ્યુલેશન શીટ','அட்டவணைத் தாள்'),(373,'create_student_payment','Create Student Payment','ছাত্রের পেমেন্ট তৈরি করুন','छात्र भुगतान बनाएं','विद्यार्थी पेमेंट तयार करा','ವಿದ್ಯಾರ್ಥಿ ಪಾವತಿ ರಚಿಸಿ','વિદ્યાર્થી ચુકવણી બનાવો','மாணவர் கட்டணத்தை உருவாக்கு'),(374,'student_payments','Student Payments','ছাত্রের পেমেন্টসমূহ','छात्र भुगतान','विद्यार्थी पेमेंट्स','ವಿದ್ಯಾರ್ಥಿ ಪಾವತಿಗಳು','વિદ્યાર્થી ચુકવણીઓ','மாணவர் கட்டணங்கள்'),(375,'update_product','Update Product','প্রোডাক্ট আপডেট','उत्पाद अपडेट करें','उत्पादन अपडेट करा','ಉತ್ಪನ್ನ ನವೀಕರಿಸಿ','પ્રોડક્ટ અપડેટ કરો','தயாரிப்பைப் புதுப்பி'),(376,'install_update','Install Update','আপডেট ইনস্টল করুন','अपडेट इंस्टॉल करें','अपडेट इन्स्टॉल करा','ನವೀಕರಣ ಸ್ಥಾಪಿಸಿ','અપડેટ ઇન્સ્ટૉલ કરો','புதுப்பிப்பை நிறுவு'),(380,'password_updated','Password Updated','পাসওয়ার্ড আপডেট হয়েছে','पासवर्ड अपडेट हुआ','पासवर्ड अपडेट झाला','ಪಾಸ್‌ವರ್ಡ್ ನವೀಕರಿಸಲಾಗಿದೆ','પાસવર્ડ અપડેટ થયો','கடவுச்சொல் புதுப்பிக்கப்பட்டது'),(381,'librarian','Librarian','গ্রন্থাগারিক','पुस्तकालयाध्यक्ष','ग्रंथपाल','ಗ್ರಂಥಪಾಲಕ','ગ્રંથપાલ','நூலகர்'),(382,'librarians','Librarians','গ্রন্থাগারিকগণ','पुस्तकालयाध्यक्ष','ग्रंथपाल','ಗ್ರಂಥಪಾಲಕರು','ગ્રંથપાલો','நூலகர்கள்'),(384,'Accountants','Accountants','হিসাবরক্ষকগণ','लेखाकार','लेखापाल','ಲೆಕ್ಕಿಗರು','હિસાબનીશો','கணக்காளர்கள்'),(385,'manage_librarian','Manage Librarian','গ্রন্থাগারিক পরিচালনা','पुस्तकालयाध्यक्ष प्रबंधन','ग्रंथपाल व्यवस्थापन','ಗ್ರಂಥಪಾಲಕ ನಿರ್ವಹಣೆ','ગ્રંથપાલ સંચાલન','நூலகர் நிர்வாகம்'),(386,'add_new_librarian','Add New Librarian','নতুন গ্রন্থাগারিক যোগ করুন','नया पुस्तकालयाध्यक्ष जोड़ें','नवीन ग्रंथपाल जोडा','ಹೊಸ ಗ್ರಂಥಪಾಲಕ ಸೇರಿಸಿ','નવા ગ્રંથપાલ ઉમેરો','புதிய நூலகரைச் சேர்'),(387,'add_librarian','Add Librarian','গ্রন্থাগারিক যোগ করুন','पुस्तकालयाध्यक्ष जोड़ें','ग्रंथपाल जोडा','ಗ್ರಂಥಪಾಲಕ ಸೇರಿಸಿ','ગ્રંથપાલ ઉમેરો','நூலகரைச் சேர்'),(388,'edit_librarian','Edit Librarian','গ্রন্থাগারিক সম্পাদনা','पुस्तकालयाध्यक्ष संपादित करें','ग्रंथपाल संपादन','ಗ್ರಂಥಪಾಲಕ ಸಂಪಾದಿಸಿ','ગ્રંથપાલ ફેરફાર','நூலகரைத் திருத்து'),(397,'accountant','Accountant','হিসাবরক্ষক','लेखाकार','लेखापाल','ಲೆಕ್ಕಿಗ','હિસાબનીશ','கணக்காளர்'),(399,'hostel_manager','Hostel Manager','হোস্টেল ম্যানেজার','हॉस्टल प्रबंधक','वसतिगृह व्यवस्थापक','ಹಾಸ್ಟೆಲ್ ಮ್ಯಾನೇಜರ್','છાત્રાલય સંચાલક','விடுதி மேலாளர்'),(401,'manage_accountant','Manage Accountant','হিসাবরক্ষক পরিচালনা','लेखाकार प्रबंधन','लेखापाल व्यवस्थापन','ಲೆಕ್ಕಿಗ ನಿರ್ವಹಣೆ','હિસાબનીશ સંચાલન','கணக்காளர் நிர்வாகம்'),(402,'add_new_accountant','Add New Accountant','নতুন হিসাবরক্ষক যোগ করুন','नया लेखाकार जोड़ें','नवीन लेखापाल जोडा','ಹೊಸ ಲೆಕ್ಕಿಗ ಸೇರಿಸಿ','નવા હિસાબનીશ ઉમેરો','புதிய கணக்காளரைச் சேர்'),(404,'teachers','Teachers','শিক্ষকগণ','शिक्षक','शिक्षक','ಶಿಕ್ಷಕರು','શિક્ષકો','ஆசிரியர்கள்'),(417,'add_accountant','Add Accountant','হিসাবরক্ষক যোগ করুন','लेखाकार जोड़ें','लेखापाल जोडा','ಲೆಕ್ಕಿಗ ಸೇರಿಸಿ','હિસાબનીશ ઉમેરો','கணக்காளரைச் சேர்'),(422,'edit_accountant','Edit Accountant','হিসাবরক্ষক সম্পাদনা','लेखाकार संपादित करें','लेखापाल संपादन','ಲೆಕ್ಕಿಗ ಸಂಪಾದಿಸಿ','હિસાબનીશ ફેરફાર','கணக்காளரைத் திருத்து'),(447,'manage_hostel','Manage Hostel','হোস্টেল পরিচালনা','हॉस्टल प्रबंधन','वसतिगृह व्यवस्थापन','ಹಾಸ್ಟೆಲ್ ನಿರ್ವಹಣೆ','છાત્રાલય સંચાલન','விடுதி நிர்வாகம்'),(450,'add_new_hostel','Add New Hostel','নতুন হোস্টেল যোগ করুন','नया हॉस्टल जोड़ें','नवीन वसतिगृह जोडा','ಹೊಸ ಹಾಸ್ಟೆಲ್ ಸೇರಿಸಿ','નવું છાત્રાલય ઉમેરો','புதிய விடுதியைச் சேர்'),(452,'add_hostel','Add Hostel','হোস্টেল যোগ করুন','हॉस्टल जोड़ें','वसतिगृह जोडा','ಹಾಸ್ಟೆಲ್ ಸೇರಿಸಿ','છાત્રાલય ઉમેરો','விடுதியைச் சேர்'),(456,'edit_hostel','Edit Hostel','হোস্টেল সম্পাদনা','हॉस्टल संपादित करें','वसतिगृह संपादन','ಹಾಸ್ಟೆಲ್ ಸಂಪಾದಿಸಿ','છાત્રાલય ફેરફાર','விடுதியைத் திருத்து'),(470,'news_settings','News Settings','সংবাদ সেটিংস','समाचार सेटिंग्स','बातम्या सेटिंग्ज','ಸುದ್ದಿ ಸೆಟ್ಟಿಂಗ್‌ಗಳು','સમાચાર સેટિંગ્સ','செய்தி அமைப்புகள்'),(481,'enquiries','Enquiries','অনুসন্ধানসমূহ','पूछताछ','चौकशा','ವಿಚಾರಣೆಗಳು','પૂછપરછો','விசாரணைகள்'),(499,'Charts','Charts','চার্টসমূহ','चार्ट','तक्ते','ಚಾರ್ಟ್‌ಗಳು','ચાર્ટ','விளக்கப்படங்கள்'),(536,'manage_news','Manage News','সংবাদ পরিচালনা','समाचार प्रबंधन','बातम्या व्यवस्थापन','ಸುದ್ದಿ ನಿರ್ವಹಣೆ','સમાચાર સંચાલન','செய்தி நிர்வாகம்'),(540,'news_list','News List','সংবাদের তালিকা','समाचार सूची','बातम्या यादी','ಸುದ್ದಿ ಪಟ್ಟಿ','સમાચાર યાદી','செய்திப் பட்டியல்'),(541,'add_news','Add News','সংবাদ যোগ করুন','समाचार जोड़ें','बातमी जोडा','ಸುದ್ದಿ ಸೇರಿಸಿ','સમાચાર ઉમેરો','செய்தியைச் சேர்'),(576,'news_title','News Title','সংবাদের শিরোনাম','समाचार शीर्षक','बातमीचे शीर्षक','ಸುದ್ದಿ ಶೀರ್ಷಿಕೆ','સમાચાર શીર્ષક','செய்தித் தலைப்பு'),(577,'news_content','News Content','সংবাদের বিষয়বস্তু','समाचार सामग्री','बातमीचा मजकूर','ಸುದ್ದಿ ವಿಷಯ','સમાચાર સામગ્રી','செய்தி உள்ளடக்கம்'),(587,'edit_news','Edit News','সংবাদ সম্পাদনা','समाचार संपादित करें','बातमी संपादन','ಸುದ್ದಿ ಸಂಪಾದಿಸಿ','સમાચાર ફેરફાર','செய்தியைத் திருத்து'),(606,'about_us','About Us','আমাদের সম্পর্কে','हमारे बारे में','आमच्याबद्दल','ನಮ್ಮ ಬಗ್ಗೆ','અમારા વિશે','எங்களைப் பற்றி'),(607,'vision','Vision','দৃষ্টিভঙ্গি','दृष्टि','दृष्टी','ದೃಷ್ಟಿ','દૃષ્ટિ','தொலைநோக்கு'),(608,'mission','Mission','লক্ষ্য','मिशन','ध्येय','ಧ್ಯೇಯ','ધ્યેય','நோக்கம்'),(609,'goal','Goal','উদ্দেশ্য','लक्ष्य','उद्दिष्ट','ಗುರಿ','લક્ષ્ય','இலக்கு'),(610,'services','Services','সেবাসমূহ','सेवाएँ','सेवा','ಸೇವೆಗಳು','સેવાઓ','சேவைகள்'),(611,'save_settings','Save Settings','সেটিংস সংরক্ষণ','सेटिंग्स सहेजें','सेटिंग्ज जतन करा','ಸೆಟ್ಟಿಂಗ್‌ಗಳನ್ನು ಉಳಿಸಿ','સેટિંગ્સ સાચવો','அமைப்புகளைச் சேமி'),(689,'teacher_idcard','Teacher Idcard','শিক্ষকের আইডি কার্ড','शिक्षक आईडी कार्ड','शिक्षक आयडी कार्ड','ಶಿಕ್ಷಕರ ಐಡಿ ಕಾರ್ಡ್','શિક્ષક ઓળખ કાર્ડ','ஆசிரியர் அடையாள அட்டை'),(699,'noticeboards','Noticeboards','নোটিশবোর্ডসমূহ','सूचना पट्ट','सूचनाफलके','ಸೂಚನಾ ಫಲಕಗಳು','નોટિસબોર્ડ','அறிவிப்புப் பலகைகள்'),(702,'libraries','Libraries','লাইব্রেরিসমূহ','पुस्तकालय','ग्रंथालये','ಗ್ರಂಥಾಲಯಗಳು','પુસ્તકાલયો','நூலகங்கள்'),(704,'dormitories','Dormitories','ছাত্রাবাসসমূহ','छात्रावास','वसतिगृहे','ವಸತಿ ನಿಲಯಗಳು','છાત્રાલયો','விடுதிகள்'),(706,'accounts','Accounts','অ্যাকাউন্টসমূহ','लेखा','खाती','ಖಾತೆಗಳು','ખાતાઓ','கணக்குகள்'),(712,'subjects','Subjects','বিষয়সমূহ','विषय','विषय','ವಿಷಯಗಳು','વિષયો','பாடங்கள்'),(713,'classs','Classs','ক্লাসসমূহ','कक्षाएँ','वर्ग','ತರಗತಿಗಳು','વર્ગો','வகுப்புகள்'),(714,'class_routines','Class Routines','ক্লাস রুটিনসমূহ','कक्षा समय-सारणी','वर्ग वेळापत्रके','ತರಗತಿ ವೇಳಾಪಟ್ಟಿಗಳು','વર્ગ સમયપત્રકો','வகுப்பு அட்டவணைகள்'),(724,'supply_front_end_information','Supply Front End Information','ফ্রন্ট এন্ডের তথ্য দিন','फ्रंट एंड जानकारी भरें','फ्रंट एंड माहिती पुरवा','ಫ್ರಂಟ್ ಎಂಡ್ ಮಾಹಿತಿ ಒದಗಿಸಿ','ફ્રન્ટ એન્ડ માહિતી આપો','முகப்புப் பக்கத் தகவலை வழங்கு'),(841,'manage_banners','Manage Banners','ব্যানার পরিচালনা','बैनर प्रबंधन','बॅनर व्यवस्थापन','ಬ್ಯಾನರ್‌ಗಳ ನಿರ್ವಹಣೆ','બેનરોનું સંચાલન','பேனர்களை நிர்வகி'),(842,'front_ends','Front Ends','ফ্রন্ট এন্ডসমূহ','फ्रंट एंड','फ्रंट एंड','ಫ್ರಂಟ್ ಎಂಡ್‌ಗಳು','ફ્રન્ટ એન્ડ','முகப்புப் பக்கங்கள்'),(933,'study_materials','Study Materials','পাঠ্য সামগ্রীসমূহ','अध्ययन सामग्री','अभ्यास साहित्य','ಅಧ್ಯಯನ ಸಾಮಗ್ರಿಗಳು','અભ્યાસ સામગ્રીઓ','கற்றல் பொருட்கள்'),(939,'assignments','Assignments','অ্যাসাইনমেন্টসমূহ','असाइनमेंट','स्वाध्याय','ಅಸೈನ್‌ಮೆಂಟ್‌ಗಳು','અસાઇનમેન્ટ','பணிகள்'),(973,'manage_assignment','Manage Assignment','অ্যাসাইনমেন্ট পরিচালনা','असाइनमेंट प्रबंधन','स्वाध्याय व्यवस्थापन','ಅಸೈನ್‌ಮೆಂಟ್ ನಿರ್ವಹಣೆ','અસાઇનમેન્ટ સંચાલન','பணி நிர்வாகம்'),(1082,'manage_media','Manage Media','মিডিয়া পরিচালনা','मीडिया प्रबंधन','मीडिया व्यवस्थापन','ಮೀಡಿಯಾ ನಿರ್ವಹಣೆ','મીડિયા સંચાલન','ஊடக நிர்வாகம்'),(1180,'all_enquiries','All Enquiries','সব অনুসন্ধান','सभी पूछताछ','सर्व चौकशा','ಎಲ್ಲಾ ವಿಚಾರಣೆಗಳು','બધી પૂછપરછો','அனைத்து விசாரணைகள்'),(1191,'manage_enquiry_category','Manage Enquiry Category','অনুসন্ধানের বিভাগ পরিচালনা','पूछताछ श्रेणी प्रबंधन','चौकशी वर्ग व्यवस्थापन','ವಿಚಾರಣೆ ವರ್ಗ ನಿರ್ವಹಣೆ','પૂછપરછ શ્રેણી સંચાલન','விசாரணை வகை நிர்வாகம்'),(1193,'enquiry_category_settings','Enquiry Category Settings','অনুসন্ধানের বিভাগ সেটিংস','पूछताछ श्रेणी सेटिंग्स','चौकशी वर्ग सेटिंग्ज','ವಿಚಾರಣೆ ವರ್ಗ ಸೆಟ್ಟಿಂಗ್‌ಗಳು','પૂછપરછ શ્રેણી સેટિંગ્સ','விசாரணை வகை அமைப்புகள்'),(1194,'purpose','Purpose','উদ্দেশ্য','उद्देश्य','उद्देश','ಉದ್ದೇಶ','હેતુ','நோக்கம்'),(1195,'who_to_visit','Who To Visit','কার সঙ্গে দেখা করবেন','किससे मिलना है','कोणाला भेटायचे','ಭೇಟಿ ಮಾಡುವವರು','કોને મળવું છે','யாரைச் சந்திக்க'),(1196,'whom','Whom','কার সঙ্গে','किससे','कोणाला','ಯಾರನ್ನು','કોને','யாரை'),(1198,'add_enquiry_setting','Add Enquiry Setting','অনুসন্ধান সেটিং যোগ করুন','पूछताछ सेटिंग जोड़ें','चौकशी सेटिंग जोडा','ವಿಚಾರಣೆ ಸೆಟ್ಟಿಂಗ್ ಸೇರಿಸಿ','પૂછપરછ સેટિંગ ઉમેરો','விசாரணை அமைப்பைச் சேர்'),(1220,'manage_enquiries','Manage Enquiries','অনুসন্ধান পরিচালনা','पूछताछ प्रबंधन','चौकशा व्यवस्थापन','ವಿಚಾರಣೆಗಳ ನಿರ್ವಹಣೆ','પૂછપરછ સંચાલન','விசாரணைகளை நிர்வகி'),(1296,'manage_teacher_idcard','Manage Teacher Idcard','শিক্ষকের আইডি কার্ড পরিচালনা','शिक्षक आईडी कार्ड प्रबंधन','शिक्षक आयडी कार्ड व्यवस्थापन','ಶಿಕ್ಷಕರ ಐಡಿ ಕಾರ್ಡ್ ನಿರ್ವಹಣೆ','શિક્ષક ઓળખ કાર્ડ સંચાલન','ஆசிரியர் அடையாள அட்டை நிர்வாகம்'),(1471,'all_enquries','All Enquries','সব অনুসন্ধান','सभी पूछताछ','सर्व चौकशा','ಎಲ್ಲಾ ವಿಚಾರಣೆಗಳು','બધી પૂછપરછો','அனைத்து விசாரணைகள்'),(1472,'all_messages','All Messages','সব বার্তা','सभी संदेश','सर्व संदेश','ಎಲ್ಲಾ ಸಂದೇಶಗಳು','બધા સંદેશાઓ','அனைத்துச் செய்திகள்'),(1484,'generate_ID_cards','Generate ID Cards','আইডি কার্ড তৈরি করুন','आईडी कार्ड बनाएं','आयडी कार्ड्स तयार करा','ಐಡಿ ಕಾರ್ಡ್‌ಗಳನ್ನು ರಚಿಸಿ','ઓળખ કાર્ડ બનાવો','அடையாள அட்டைகளை உருவாக்கு'),(1512,'manage_hostel_id_card','Manage Hostel Id Card','হোস্টেল আইডি কার্ড পরিচালনা','हॉस्टल आईडी कार्ड प्रबंधन','वसतिगृह आयडी कार्ड व्यवस्थापन','ಹಾಸ್ಟೆಲ್ ಐಡಿ ಕಾರ್ಡ್ ನಿರ್ವಹಣೆ','છાત્રાલય ઓળખ કાર્ડ સંચાલન','விடுதி அடையாள அட்டை நிர்வாகம்'),(1547,'generate_hostel_id_card','Generate Hostel Id Card','হোস্টেল আইডি কার্ড তৈরি করুন','हॉस्टल आईडी कार्ड बनाएं','वसतिगृह आयडी कार्ड तयार करा','ಹಾಸ್ಟೆಲ್ ಐಡಿ ಕಾರ್ಡ್ ರಚಿಸಿ','છાત્રાલય ઓળખ કાર્ડ બનાવો','விடுதி அடையாள அட்டையை உருவாக்கு'),(1660,'manage_librarian_ID_card','Manage Librarian ID Card','গ্রন্থাগারিক আইডি কার্ড পরিচালনা','पुस्तकालयाध्यक्ष आईडी कार्ड प्रबंधन','ग्रंथपाल आयडी कार्ड व्यवस्थापन','ಗ್ರಂಥಪಾಲಕ ಐಡಿ ಕಾರ್ಡ್ ನಿರ್ವಹಣೆ','ગ્રંથપાલ ઓળખ કાર્ડ સંચાલન','நூலகர் அடையாள அட்டை நிர்வாகம்'),(1685,'manage_help_link','Manage Help Link','সহায়ক লিংক পরিচালনা','सहायता लिंक प्रबंधन','मदत दुवा व्यवस्थापन','ಸಹಾಯ ಲಿಂಕ್ ನಿರ್ವಹಣೆ','મદદ લિંક સંચાલન','உதவி இணைப்பு நிர்வாகம்'),(1688,'links','Links','লিংকসমূহ','लिंक','दुवे','ಲಿಂಕ್‌ಗಳು','લિંક્સ','இணைப்புகள்'),(1692,'help_link_list','Help Link List','সহায়ক লিংকের তালিকা','सहायता लिंक सूची','मदत दुवा यादी','ಸಹಾಯ ಲಿಂಕ್ ಪಟ್ಟಿ','મદદ લિંક યાદી','உதவி இணைப்புப் பட்டியல்'),(1693,'add_help_link','Add Help Link','সহায়ক লিংক যোগ করুন','सहायता लिंक जोड़ें','मदत दुवा जोडा','ಸಹಾಯ ಲಿಂಕ್ ಸೇರಿಸಿ','મદદ લિંક ઉમેરો','உதவி இணைப்பைச் சேர்'),(1694,'link','Link','লিংক','लिंक','दुवा','ಲಿಂಕ್','લિંક','இணைப்பு'),(1769,'edit_help_link','Edit Help Link','সহায়ক লিংক সম্পাদনা','सहायता लिंक संपादित करें','मदत दुवा संपादन','ಸಹಾಯ ಲಿಂಕ್ ಸಂಪಾದಿಸಿ','મદદ લિંક ફેરફાર','உதவி இணைப்பைத் திருத்து'),(1770,'edit_helpful_links','Edit Helpful Links','সহায়ক লিংকসমূহ সম্পাদনা','उपयोगी लिंक संपादित करें','उपयुक्त दुवे संपादन','ಉಪಯುಕ್ತ ಲಿಂಕ್‌ಗಳನ್ನು ಸಂಪಾದಿಸಿ','મદદરૂપ લિંક્સ ફેરફાર','பயனுள்ள இணைப்புகளைத் திருத்து'),(1779,'manage_help_desk','Manage Help Desk','হেল্প ডেস্ক পরিচালনা','हेल्प डेस्क प्रबंधन','हेल्प डेस्क व्यवस्थापन','ಸಹಾಯ ಕೇಂದ್ರ ನಿರ್ವಹಣೆ','હેલ્પ ડેસ્ક સંચાલન','உதவி மைய நிர்வாகம்'),(1782,'help_desk_list','Help Desk List','হেল্প ডেস্কের তালিকা','हेल्प डेस्क सूची','हेल्प डेस्क यादी','ಸಹಾಯ ಕೇಂದ್ರ ಪಟ್ಟಿ','હેલ્પ ડેસ્ક યાદી','உதவி மையப் பட்டியல்'),(1783,'add_help_desk','Add Help Desk','হেল্প ডেস্ক যোগ করুন','हेल्प डेस्क जोड़ें','हेल्प डेस्क जोडा','ಸಹಾಯ ಕೇಂದ್ರ ಸೇರಿಸಿ','હેલ્પ ડેસ્ક ઉમેરો','உதவி மையத்தைச் சேர்'),(1784,'content','Content','বিষয়বস্তু','सामग्री','मजकूर','ವಿಷಯ','સામગ્રી','உள்ளடக்கம்'),(1813,'edit_help_desk','Edit Help Desk','হেল্প ডেস্ক সম্পাদনা','हेल्प डेस्क संपादित करें','हेल्प डेस्क संपादन','ಸಹಾಯ ಕೇಂದ್ರ ಸಂಪಾದಿಸಿ','હેલ્પ ડેસ્ક ફેરફાર','உதவி மையத்தைத் திருத்து'),(1822,'manage_holiday','Manage Holiday','ছুটি পরিচালনা','अवकाश प्रबंधन','सुट्टी व्यवस्थापन','ರಜೆ ನಿರ್ವಹಣೆ','રજા સંચાલન','விடுமுறை நிர்வாகம்'),(1825,'holiday_list','Holiday List','ছুটির তালিকা','अवकाश सूची','सुट्टी यादी','ರಜೆ ಪಟ್ಟಿ','રજા યાદી','விடுமுறைப் பட்டியல்'),(1826,'add_holiday','Add Holiday','ছুটি যোগ করুন','अवकाश जोड़ें','सुट्टी जोडा','ರಜೆ ಸೇರಿಸಿ','રજા ઉમેરો','விடுமுறையைச் சேர்'),(1827,'holiday','Holiday','ছুটি','अवकाश','सुट्टी','ರಜೆ','રજા','விடுமுறை'),(1836,'edit_holiday','Edit Holiday','ছুটি সম্পাদনা','अवकाश संपादित करें','सुट्टी संपादन','ರಜೆ ಸಂಪಾದಿಸಿ','રજા ફેરફાર','விடுமுறையைத் திருத்து'),(1841,'manage_todays_thought','Manage Todays Thought','আজকের ভাবনা পরিচালনা','आज का विचार प्रबंधन','आजचा विचार व्यवस्थापन','ಇಂದಿನ ಚಿಂತನೆ ನಿರ್ವಹಣೆ','આજના વિચારનું સંચાલન','இன்றைய சிந்தனை நிர்வாகம்'),(1844,'todays_thought_list','Todays Thought List','আজকের ভাবনার তালিকা','आज के विचार की सूची','आजचा विचार यादी','ಇಂದಿನ ಚಿಂತನೆ ಪಟ್ಟಿ','આજના વિચારની યાદી','இன்றைய சிந்தனைப் பட்டியல்'),(1845,'add_todays_thought','Add Todays Thought','আজকের ভাবনা যোগ করুন','आज का विचार जोड़ें','आजचा विचार जोडा','ಇಂದಿನ ಚಿಂತನೆ ಸೇರಿಸಿ','આજનો વિચાર ઉમેરો','இன்றைய சிந்தனையைச் சேர்'),(1846,'thought','Thought','ভাবনা','विचार','विचार','ಚಿಂತನೆ','વિચાર','சிந்தனை'),(1857,'edit_todays_thought','Edit Todays Thought','আজকের ভাবনা সম্পাদনা','आज का विचार संपादित करें','आजचा विचार संपादन','ಇಂದಿನ ಚಿಂತನೆ ಸಂಪಾದಿಸಿ','આજનો વિચાર ફેરફાર','இன்றைய சிந்தனையைத் திருத்து'),(2059,'school_clubs','School Clubs','স্কুল ক্লাবসমূহ','स्कूल क्लब','शाळा क्लब','ಶಾಲಾ ಕ್ಲಬ್‌ಗಳು','શાળા ક્લબ','பள்ளி மன்றங்கள்'),(2071,'manage_club','Manage Club','ক্লাব পরিচালনা','क्लब प्रबंधन','क्लब व्यवस्थापन','ಕ್ಲಬ್ ನಿರ್ವಹಣೆ','ક્લબ સંચાલન','மன்ற நிர்வாகம்'),(2074,'club_list','Club List','ক্লাবের তালিকা','क्लब सूची','क्लब यादी','ಕ್ಲಬ್ ಪಟ್ಟಿ','ક્લબ યાદી','மன்றப் பட்டியல்'),(2075,'add_club','Add Club','ক্লাব যোগ করুন','क्लब जोड़ें','क्लब जोडा','ಕ್ಲಬ್ ಸೇರಿಸಿ','ક્લબ ઉમેરો','மன்றத்தைச் சேர்'),(2082,'edit_club','Edit Club','ক্লাব সম্পাদনা','क्लब संपादित करें','क्लब संपादन','ಕ್ಲಬ್ ಸಂಪಾದಿಸಿ','ક્લબ ફેરફાર','மன்றத்தைத் திருத்து'),(2087,'club_name','Club Name','ক্লাবের নাম','क्लब का नाम','क्लबचे नाव','ಕ್ಲಬ್ ಹೆಸರು','ક્લબનું નામ','மன்றத்தின் பெயர்'),(2104,'manage_alumni','Manage Alumni','প্রাক্তনী পরিচালনা','पूर्व छात्र प्रबंधन','माजी विद्यार्थी व्यवस्थापन','ಹಳೆಯ ವಿದ್ಯಾರ್ಥಿಗಳ ನಿರ್ವಹಣೆ','ભૂતપૂર્વ વિદ્યાર્થી સંચાલન','முன்னாள் மாணவர் நிர்வாகம்'),(2345,'manage_banner','Manage Banner','ব্যানার পরিচালনা','बैनर प्रबंधन','बॅनर व्यवस्थापन','ಬ್ಯಾನರ್ ನಿರ್ವಹಣೆ','બેનર સંચાલન','பேனர் நிர்வாகம்'),(2349,'banner_text_1','Banner Text 1','ব্যানারের লেখা ১','बैनर टेक्स्ट 1','बॅनर मजकूर 1','ಬ್ಯಾನರ್ ಪಠ್ಯ 1','બેનર લખાણ 1','பேனர் உரை 1'),(2350,'banner_text_2','Banner Text 2','ব্যানারের লেখা ২','बैनर टेक्स्ट 2','बॅनर मजकूर 2','ಬ್ಯಾನರ್ ಪಠ್ಯ 2','બેનર લખાણ 2','பேனர் உரை 2'),(2357,'add_banner','Add Banner','ব্যানার যোগ করুন','बैनर जोड़ें','बॅनर जोडा','ಬ್ಯಾನರ್ ಸೇರಿಸಿ','બેનર ઉમેરો','பேனரைச் சேர்'),(2371,'edit_banner','Edit Banner','ব্যানার সম্পাদনা','बैनर संपादित करें','बॅनर संपादन','ಬ್ಯಾನರ್ ಸಂಪಾದಿಸಿ','બેનર ફેરફાર','பேனரைத் திருத்து'),(2414,'front_end_banner','Front End Banner','ফ্রন্ট এন্ড ব্যানার','फ्रंट एंड बैनर','फ्रंट एंड बॅनर','ಫ್ರಂಟ್ ಎಂಡ್ ಬ್ಯಾನರ್','ફ્રન્ટ એન્ડ બેનર','முகப்புப் பக்க பேனர்'),(2415,'b_text_one','B Text One','ব্যানার লেখা এক','बैनर टेक्स्ट एक','बी मजकूर एक','ಬಿ ಪಠ್ಯ ಒಂದು','બેનર લખાણ એક','பேனர் உரை ஒன்று'),(2416,'b_text_two','B Text Two','ব্যানার লেখা দুই','बैनर टेक्स्ट दो','बी मजकूर दोन','ಬಿ ಪಠ್ಯ ಎರಡು','બેનર લખાણ બે','பேனர் உரை இரண்டு'),(2492,'all_news','All News','সব সংবাদ','सभी समाचार','सर्व बातम्या','ಎಲ್ಲಾ ಸುದ್ದಿ','બધા સમાચાર','அனைத்துச் செய்திகள்'),(2537,'manage_loan_applicants','Manage Loan Applicants','ঋণ আবেদনকারী পরিচালনা','ऋण आवेदकों का प्रबंधन','कर्ज अर्जदार व्यवस्थापन','ಸಾಲ ಅರ್ಜಿದಾರರ ನಿರ್ವಹಣೆ','લોન અરજદારોનું સંચાલન','கடன் விண்ணப்பதாரர்களை நிர்வகி'),(2538,'manage_loan_approvals','Manage Loan Approvals','ঋণ অনুমোদন পরিচালনা','ऋण स्वीकृति प्रबंधन','कर्ज मंजुरी व्यवस्थापन','ಸಾಲ ಅನುಮೋದನೆಗಳ ನಿರ್ವಹಣೆ','લોન મંજૂરીઓનું સંચાલન','கடன் ஒப்புதல்களை நிர்வகி'),(2556,'staff_name','Staff Name','কর্মীর নাম','कर्मचारी का नाम','कर्मचाऱ्याचे नाव','ಸಿಬ್ಬಂದಿ ಹೆಸರು','સ્ટાફનું નામ','பணியாளர் பெயர்'),(2600,'loan_application_submitted_successfully','Loan Application Submitted Successfully','ঋণের আবেদন সফলভাবে জমা হয়েছে','ऋण आवेदन सफलतापूर्वक जमा हुआ','कर्ज अर्ज यशस्वीरित्या सादर केला','ಸಾಲದ ಅರ್ಜಿ ಯಶಸ್ವಿಯಾಗಿ ಸಲ್ಲಿಸಲಾಗಿದೆ','લોન અરજી સફળતાપૂર્વક સબમિટ થઈ','கடன் விண்ணப்பம் வெற்றிகரமாகச் சமர்ப்பிக்கப்பட்டது'),(2622,'manage_loan_approval','Manage Loan Approval','ঋণ অনুমোদন পরিচালনা','ऋण स्वीकृति प्रबंधन','कर्ज मंजुरी व्यवस्थापन','ಸಾಲ ಅನುಮೋದನೆ ನಿರ್ವಹಣೆ','લોન મંજૂરી સંચાલન','கடன் ஒப்புதல் நிர்வாகம்'),(7939,'adjust_marks','Adjust Marks','নম্বর সমন্বয় করুন','अंक समायोजित करें','गुण समायोजित करा','ಅಂಕಗಳನ್ನು ಹೊಂದಿಸಿ','ગુણ ગોઠવો','மதிப்பெண்களை சரிசெய்'),(7940,'assign','Assign','নির্ধারণ করুন','असाइन करें','नेमून द्या','ನಿಯೋಜಿಸಿ','સોંપો','ஒதுக்கு'),(7941,'assign_email_hint','Each newly assigned student (and parent) gets an \'Exam scheduled\' email.','নতুন নির্ধারিত প্রতিটি শিক্ষার্থী (ও অভিভাবক) একটি \'পরীক্ষার সময়সূচি\' ইমেল পাবেন।','नए असाइन किए गए प्रत्येक छात्र (और अभिभावक) को \'परीक्षा निर्धारित\' ईमेल मिलता है।','नव्याने नेमलेल्या प्रत्येक विद्यार्थ्याला (व पालकाला) \'परीक्षा नियोजित\' ईमेल मिळतो.','ಹೊಸದಾಗಿ ನಿಯೋಜಿಸಿದ ಪ್ರತಿ ವಿದ್ಯಾರ್ಥಿಗೆ (ಮತ್ತು ಪೋಷಕರಿಗೆ) \'ಪರೀಕ್ಷೆ ನಿಗದಿಯಾಗಿದೆ\' ಇಮೇಲ್ ಹೋಗುತ್ತದೆ.','નવા સોંપાયેલા દરેક વિદ્યાર્થી (અને વાલી) ને \'પરીક્ષા નિર્ધારિત\' ઈમેલ મળશે.','புதிதாக ஒதுக்கப்பட்ட ஒவ்வொரு மாணவருக்கும் (மற்றும் பெற்றோருக்கும்) \'தேர்வு திட்டமிடப்பட்டது\' மின்னஞ்சல் அனுப்பப்படும்.'),(7942,'assign_selected_and_email','Assign Selected And Email','নির্বাচিতদের নির্ধারণ করুন ও ইমেল পাঠান','चयनित को असाइन करें और ईमेल भेजें','निवडलेल्यांना नेमा आणि ईमेल करा','ಆಯ್ದವರಿಗೆ ನಿಯೋಜಿಸಿ ಮತ್ತು ಇಮೇಲ್ ಮಾಡಿ','પસંદ કરેલાને સોંપો અને ઈમેલ કરો','தேர்ந்தெடுத்தவற்றை ஒதுக்கி மின்னஞ்சல் அனுப்பு'),(7943,'assign_students','Assign Students','শিক্ষার্থী নির্ধারণ করুন','छात्रों को असाइन करें','विद्यार्थी नेमा','ವಿದ್ಯಾರ್ಥಿಗಳನ್ನು ನಿಯೋಜಿಸಿ','વિદ્યાર્થીઓને સોંપો','மாணவர்களை ஒதுக்கு'),(7944,'cannot_remove_a_student_who_has_started','A student who has started the exam cannot be removed','যে শিক্ষার্থী পরীক্ষা শুরু করেছে তাকে সরানো যাবে না','जिस छात्र ने परीक्षा शुरू कर दी है उसे हटाया नहीं जा सकता','परीक्षा सुरू केलेल्या विद्यार्थ्याला काढता येत नाही','ಪರೀಕ್ಷೆ ಆರಂಭಿಸಿರುವ ವಿದ್ಯಾರ್ಥಿಯನ್ನು ತೆಗೆದುಹಾಕಲು ಸಾಧ್ಯವಿಲ್ಲ','જે વિદ્યાર્થીએ પરીક્ષા શરૂ કરી દીધી હોય તેને દૂર કરી શકાતો નથી','தேர்வைத் தொடங்கிய மாணவரை நீக்க முடியாது'),(2795,'hostel','Hostel','হোস্টেল','हॉस्टल','वसतिगृह','ಹಾಸ್ಟೆಲ್','છાત્રાલય','விடுதி'),(2798,'edit_dormitory','Edit Dormitory','ছাত্রাবাস সম্পাদনা','छात्रावास संपादित करें','वसतिगृह संपादन','ವಸತಿ ನಿಲಯ ಸಂಪಾದಿಸಿ','છાત્રાલય ફેરફાર','விடுதியைத் திருத்து'),(2900,'running_session','Running Session','চলমান সেশন','वर्तमान सत्र','चालू सत्र','ಪ್ರಸ್ತುತ ಸೆಷನ್','ચાલુ સત્ર','நடப்பு கல்வியாண்டு'),(3081,'system_footer','System Footer','সিস্টেম ফুটার','सिस्टम फ़ूटर','सिस्टम तळटीप','ಸಿಸ್ಟಂ ಅಡಿಟಿಪ್ಪಣಿ','સિસ્ટમ ફૂટર','கணினி அடிக்குறிப்பு'),(3146,'manage_exam_questions','Manage Exam Questions','পরীক্ষার প্রশ্ন পরিচালনা','परीक्षा प्रश्न प्रबंधन','परीक्षा प्रश्न व्यवस्थापन','ಪರೀಕ್ಷಾ ಪ್ರಶ್ನೆಗಳ ನಿರ್ವಹಣೆ','પરીક્ષા પ્રશ્નોનું સંચાલન','தேர்வு வினா நிர்வாகம்'),(3152,'add_examquestion','Add Examquestion','পরীক্ষার প্রশ্ন যোগ করুন','परीक्षा प्रश्न जोड़ें','परीक्षा प्रश्न जोडा','ಪರೀಕ್ಷಾ ಪ್ರಶ್ನೆ ಸೇರಿಸಿ','પરીક્ષા પ્રશ્ન ઉમેરો','தேர்வு வினாவைச் சேர்'),(3324,'marksheet_for','Marksheet For','মার্কশিট:','के लिए अंकपत्र','यांची गुणपत्रिका','ಇವರ ಅಂಕಪಟ್ಟಿ','માટે માર્કશીટ','மதிப்பெண் பட்டியல் -'),(3327,'average_grade_point','Average Grade Point','গড় গ্রেড পয়েন্ট','औसत ग्रेड पॉइंट','सरासरी श्रेणी गुण','ಸರಾಸರಿ ಗ್ರೇಡ್ ಪಾಯಿಂಟ್','સરેરાશ ગ્રેડ પોઇન્ટ','சராசரி கிரேடு புள்ளி'),(3328,'print_marksheet','Print Marksheet','মার্কশিট প্রিন্ট','अंकपत्र प्रिंट करें','गुणपत्रिका प्रिंट करा','ಅಂಕಪಟ್ಟಿ ಮುದ್ರಿಸಿ','માર્કશીટ પ્રિન્ટ કરો','மதிப்பெண் பட்டியலை அச்சிடு'),(3508,'student_promotion','Student Promotion','ছাত্র প্রমোশন','छात्र प्रोन्नति','विद्यार्थी बढती','ವಿದ್ಯಾರ್ಥಿ ಬಡ್ತಿ','વિદ્યાર્થી બઢતી','மாணவர் பதவி உயர்வு'),(3513,'current_session','Current Session','বর্তমান সেশন','वर्तमान सत्र','चालू सत्र','ಪ್ರಸ್ತುತ ಸೆಷನ್','ચાલુ સત્ર','நடப்பு கல்வியாண்டு'),(3514,'promote_to_session','Promote To Session','যে সেশনে প্রমোশন','सत्र में प्रोन्नत करें','या सत्रात बढती','ಬಡ್ತಿ ಪಡೆಯುವ ಸೆಷನ್','આ સત્રમાં બઢતી','பதவி உயர்வு பெறும் கல்வியாண்டு'),(3515,'promotion_from_class','Promotion From Class','যে ক্লাস থেকে প্রমোশন','प्रोन्नति कक्षा से','बढती देणारा वर्ग','ಬಡ್ತಿ ಇಂದ ತರಗತಿ','બઢતી માટેનો વર્ગ (થી)','பதவி உயர்வு பெறும் வகுப்பிலிருந்து'),(3516,'promotion_to_class','Promotion To Class','যে ক্লাসে প্রমোশন','प्रोन्नति कक्षा में','बढती मिळणारा वर्ग','ಬಡ್ತಿ ಪಡೆಯುವ ತರಗತಿ','બઢતી માટેનો વર્ગ (સુધી)','பதவி உயர்வு பெறும் வகுப்பு'),(3517,'manage_promotion','Manage Promotion','প্রমোশন পরিচালনা','प्रोन्नति प्रबंधन','बढती व्यवस्थापन','ಬಡ್ತಿ ನಿರ್ವಹಣೆ','બઢતી સંચાલન','பதவி உயர்வு நிர்வாகம்'),(3518,'select_class_for_promotion_to_and_from','Select Class For Promotion To And From','প্রমোশনের ক্লাস নির্বাচন করুন (থেকে ও পর্যন্ত)','प्रोन्नति की कक्षा से और में चुनें','बढतीसाठी आधीचा व नवीन वर्ग निवडा','ಬಡ್ತಿ ಇಂದ ಮತ್ತು ಗೆ ತರಗತಿ ಆಯ್ಕೆಮಾಡಿ','બઢતી માટે વર્ગ (થી અને સુધી) પસંદ કરો','பதவி உயர்வுக்கான வகுப்புகளைத் தேர்ந்தெடுக்கவும்'),(8005,'this_exam_has_already_closed','This Exam Has Already Closed','এই পরীক্ষা ইতিমধ্যে বন্ধ হয়ে গেছে','यह परीक्षा पहले ही बंद हो चुकी है','ही परीक्षा आधीच बंद झाली आहे','ಈ ಪರೀಕ್ಷೆ ಈಗಾಗಲೇ ಮುಗಿದಿದೆ','આ પરીક્ષા પહેલેથી બંધ થઈ ગઈ છે','இந்தத் தேர்வு ஏற்கனவே முடிந்துவிட்டது'),(7985,'published_exam_edit_hint','This exam is published. You can still edit questions until a student starts it.','এই পরীক্ষা প্রকাশিত হয়েছে। কোনো শিক্ষার্থী শুরু না করা পর্যন্ত আপনি প্রশ্ন সম্পাদনা করতে পারবেন।','यह परीक्षा प्रकाशित है। किसी छात्र के शुरू करने तक आप प्रश्न संपादित कर सकते हैं।','ही परीक्षा प्रकाशित झाली आहे. विद्यार्थी सुरू करेपर्यंत तुम्ही प्रश्न संपादित करू शकता.','ಈ ಪರೀಕ್ಷೆಯನ್ನು ಪ್ರಕಟಿಸಲಾಗಿದೆ. ವಿದ್ಯಾರ್ಥಿ ಆರಂಭಿಸುವವರೆಗೆ ನೀವು ಪ್ರಶ್ನೆಗಳನ್ನು ಸಂಪಾದಿಸಬಹುದು.','આ પરીક્ષા પ્રકાશિત થયેલ છે. કોઈ વિદ્યાર્થી શરૂ કરે ત્યાં સુધી તમે પ્રશ્નો સંપાદિત કરી શકો છો.','இந்தத் தேர்வு வெளியிடப்பட்டுள்ளது. ஒரு மாணவர் தொடங்கும் வரை கேள்விகளைத் திருத்தலாம்.'),(7978,'not_answered','Not Answered','উত্তর দেওয়া হয়নি','उत्तर नहीं दिया','उत्तर दिले नाही','ಉತ್ತರಿಸಿಲ್ಲ','જવાબ આપ્યો નથી','பதிலளிக்கப்படவில்லை'),(7972,'no_cbt_exams_yet','No Cbt Exams Yet','এখনও কোনো CBT পরীক্ষা নেই','अभी तक कोई CBT परीक्षा नहीं','अजून कोणतीही CBT परीक्षा नाही','ಇನ್ನೂ ಯಾವುದೇ CBT ಪರೀಕ್ಷೆಗಳಿಲ್ಲ','હજી કોઈ CBT પરીક્ષા નથી','இன்னும் CBT தேர்வுகள் இல்லை'),(4278,'manage_loan','Manage Loan','ঋণ পরিচালনা','ऋण प्रबंधन','कर्ज व्यवस्थापन','ಸಾಲ ನಿರ್ವಹಣೆ','લોન સંચાલન','கடன் நிர்வாகம்'),(4289,'manage_help_desks','Manage Help Desks','হেল্প ডেস্ক পরিচালনা','हेल्प डेस्क प्रबंधन','हेल्प डेस्क व्यवस्थापन','ಸಹಾಯ ಕೇಂದ್ರಗಳ ನಿರ್ವಹಣೆ','હેલ્પ ડેસ્કનું સંચાલન','உதவி மையங்களை நிர்வகி'),(4294,'enquiry_category','Enquiry Category','অনুসন্ধানের বিভাগ','पूछताछ श्रेणी','चौकशी वर्ग','ವಿಚಾರಣೆ ವರ್ಗ','પૂછપરછ શ્રેણી','விசாரணை வகை'),(7968,'hide_results_from_students','Hide Results From Students','শিক্ষার্থীদের থেকে ফলাফল লুকান','छात्रों से परिणाम छिपाएँ','विद्यार्थ्यांपासून निकाल लपवा','ವಿದ್ಯಾರ್ಥಿಗಳಿಂದ ಫಲಿತಾಂಶ ಮರೆಮಾಡಿ','વિદ્યાર્થીઓથી પરિણામ છુપાવો','மாணவர்களிடமிருந்து முடிவுகளை மறை'),(4306,'add_circular','Add Circular','সার্কুলার যোগ করুন','परिपत्र जोड़ें','परिपत्रक जोडा','ಸುತ್ತೋಲೆ ಸೇರಿಸಿ','પરિપત્ર ઉમેરો','சுற்றறிக்கையைச் சேர்'),(7961,'exam_moved_back_to_draft','Exam moved back to draft','পরীক্ষা খসড়ায় ফেরানো হয়েছে','परीक्षा ड्राफ्ट में वापस भेजी गई','परीक्षा परत मसुद्यात नेली','ಪರೀಕ್ಷೆಯನ್ನು ಡ್ರಾಫ್ಟ್‌ಗೆ ಹಿಂತಿರುಗಿಸಲಾಗಿದೆ','પરીક્ષા પાછી ડ્રાફ્ટમાં ખસેડી','தேர்வு வரைவுக்கு மாற்றப்பட்டது'),(7959,'exam_date_and_start_time_are_required','Exam date and start time are required','পরীক্ষার তারিখ ও শুরুর সময় আবশ্যক','परीक्षा की तारीख और शुरू होने का समय आवश्यक हैं','परीक्षेची तारीख आणि सुरू होण्याची वेळ आवश्यक आहे','ಪರೀಕ್ಷೆಯ ದಿನಾಂಕ ಮತ್ತು ಆರಂಭ ಸಮಯ ಅಗತ್ಯ','પરીક્ષાની તારીખ અને શરૂઆતનો સમય જરૂરી છે','தேர்வு தேதி மற்றும் தொடக்க நேரம் தேவை'),(4365,'task_manager','Task Manager','টাস্ক ম্যানেজার','कार्य प्रबंधक','कार्य व्यवस्थापक','ಕಾರ್ಯ ಮ್ಯಾನೇಜರ್','કાર્ય વ્યવસ્થાપક','பணி மேலாளர்'),(4378,'manage_circular','Manage Circular','সার্কুলার পরিচালনা','परिपत्र प्रबंधन','परिपत्रक व्यवस्थापन','ಸುತ್ತೋಲೆ ನಿರ್ವಹಣೆ','પરિપત્ર સંચાલન','சுற்றறிக்கை நிர்வாகம்'),(4381,'circular_list','Circular List','সার্কুলারের তালিকা','परिपत्र सूची','परिपत्रक यादी','ಸುತ್ತೋಲೆ ಪಟ್ಟಿ','પરિપત્ર યાદી','சுற்றறிக்கைப் பட்டியல்'),(4382,'reference_no','Reference No','রেফারেন্স নম্বর','संदर्भ संख्या','संदर्भ क्रमांक','ಉಲ್ಲೇಖ ಸಂಖ್ಯೆ','સંદર્ભ નંબર','குறிப்பு எண்'),(4387,'circular_title','Circular Title','সার্কুলারের শিরোনাম','परिपत्र शीर्षक','परिपत्रक शीर्षक','ಸುತ್ತೋಲೆ ಶೀರ್ಷಿಕೆ','પરિપત્ર શીર્ષક','சுற்றறிக்கைத் தலைப்பு'),(4388,'circular_date','Circular Date','সার্কুলারের তারিখ','परिपत्र की तारीख','परिपत्रक तारीख','ಸುತ್ತೋಲೆ ದಿನಾಂಕ','પરિપત્ર તારીખ','சுற்றறிக்கை தேதி'),(4411,'edit_circular','Edit Circular','সার্কুলার সম্পাদনা','परिपत्र संपादित करें','परिपत्रक संपादन','ಸುತ್ತೋಲೆ ಸಂಪಾದಿಸಿ','પરિપત્ર ફેરફાર','சுற்றறிக்கையைத் திருத்து'),(4436,'task_name','Task Name','কাজের নাম','कार्य का नाम','कार्याचे नाव','ಕಾರ್ಯದ ಹೆಸರು','કાર્યનું નામ','பணியின் பெயர்'),(7956,'end_time_must_be_after_start_time','Last entry time must be after the start time','শেষ প্রবেশের সময় শুরুর সময়ের পরে হতে হবে','अंतिम प्रवेश समय शुरू होने के समय के बाद का होना चाहिए','शेवटची प्रवेश वेळ सुरू होण्याच्या वेळेनंतरची असावी','ಕೊನೆಯ ಪ್ರವೇಶ ಸಮಯ ಆರಂಭ ಸಮಯದ ನಂತರ ಇರಬೇಕು','છેલ્લો પ્રવેશ સમય શરૂઆતના સમય પછીનો હોવો જોઈએ','கடைசி நுழைவு நேரம் தொடக்க நேரத்திற்குப் பிறகு இருக்க வேண்டும்'),(4439,'assign_to','Assign To','যাকে বরাদ্দ','सौंपा गया','यांना सोपवा','ಯಾರಿಗೆ ನಿಯೋಜಿಸಲಾಗಿದೆ','કોને સોંપ્યું','ஒதுக்கப்பட்டவர்'),(4440,'task_status','Task Status','কাজের অবস্থা','कार्य स्थिति','कार्य स्थिती','ಕಾರ್ಯದ ಸ್ಥಿತಿ','કાર્ય સ્થિતિ','பணி நிலை'),(4441,'manage_task_manager','Manage Task Manager','টাস্ক ম্যানেজার পরিচালনা','कार्य प्रबंधक का प्रबंधन','कार्य व्यवस्थापक व्यवस्थापन','ಕಾರ್ಯ ಮ್ಯಾನೇಜರ್ ನಿರ್ವಹಣೆ','કાર્ય વ્યવસ્થાપક સંચાલન','பணி மேலாளர் நிர்வாகம்'),(4456,'add_task_manager','Add Task Manager','টাস্ক ম্যানেজার যোগ করুন','कार्य प्रबंधक जोड़ें','कार्य व्यवस्थापक जोडा','ಕಾರ್ಯ ಮ್ಯಾನೇಜರ್ ಸೇರಿಸಿ','કાર્ય વ્યવસ્થાપક ઉમેરો','பணி மேலாளரைச் சேர்'),(4467,'task_date','Task Date','কাজের তারিখ','कार्य की तारीख','कार्य तारीख','ಕಾರ್ಯದ ದಿನಾಂಕ','કાર્ય તારીખ','பணி தேதி'),(4468,'task_for','Task For','যার জন্য কাজ','कार्य किसके लिए','कार्य कोणासाठी','ಕಾರ್ಯ ಯಾರಿಗೆ','કાર્ય કોના માટે','பணிக்கு உரியவர்'),(4483,'edit_task_manager','Edit Task Manager','টাস্ক ম্যানেজার সম্পাদনা','कार्य प्रबंधक संपादित करें','कार्य व्यवस्थापक संपादन','ಕಾರ್ಯ ಮ್ಯಾನೇಜರ್ ಸಂಪಾದಿಸಿ','કાર્ય વ્યવસ્થાપક ફેરફાર','பணி மேலாளரைத் திருத்து'),(4484,'task_priority','Task Priority','কাজের অগ্রাধিকার','कार्य प्राथमिकता','कार्य प्राधान्य','ಕಾರ್ಯದ ಆದ್ಯತೆ','કાર્ય પ્રાથમિકતા','பணி முன்னுரிமை'),(4548,'view_result','View Result','ফলাফল দেখুন','परिणाम देखें','निकाल पहा','ಫಲಿತಾಂಶ ವೀಕ್ಷಿಸಿ','પરિણામ જુઓ','முடிவைப் பார்'),(4555,'list_exams','List Exams','পরীক্ষার তালিকা','परीक्षा सूची','परीक्षा यादी','ಪರೀಕ್ಷೆಗಳ ಪಟ್ಟಿ','પરીક્ષાઓની યાદી','தேர்வுகள் பட்டியல்'),(4564,'add_exams','Add Exams','পরীক্ষা যোগ করুন','परीक्षाएँ जोड़ें','परीक्षा जोडा','ಪರೀಕ್ಷೆಗಳನ್ನು ಸೇರಿಸಿ','પરીક્ષાઓ ઉમેરો','தேர்வுகளைச் சேர்'),(4577,'exam_result','Exam Result','পরীক্ষার ফলাফল','परीक्षा परिणाम','परीक्षा निकाल','ಪರೀಕ್ಷಾ ಫಲಿತಾಂಶ','પરીક્ષા પરિણામ','தேர்வு முடிவு'),(4587,'exam_date','Exam Date','পরীক্ষার তারিখ','परीक्षा की तारीख','परीक्षेची तारीख','ಪರೀಕ್ಷಾ ದಿನಾಂಕ','પરીક્ષા તારીખ','தேர்வு தேதி'),(4588,'session','Session','সেশন','सत्र','सत्र','ಸೆಷನ್','સત્ર','கல்வியாண்டு'),(4615,'view_exam','View Exam','পরীক্ষা দেখুন','परीक्षा देखें','परीक्षा पहा','ಪರೀಕ್ಷೆ ವೀಕ್ಷಿಸಿ','પરીક્ષા જુઓ','தேர்வைப் பார்'),(4636,'question_count','Question Count','প্রশ্নের সংখ্যা','प्रश्नों की संख्या','प्रश्न संख्या','ಪ್ರಶ್ನೆಗಳ ಸಂಖ್ಯೆ','પ્રશ્નોની સંખ્યા','வினாக்களின் எண்ணிக்கை'),(4637,'exam_duration','Exam Duration','পরীক্ষার সময়কাল','परीक्षा की अवधि','परीक्षा कालावधी','ಪರೀಕ್ಷೆಯ ಅವಧಿ','પરીક્ષાનો સમયગાળો','தேர்வு கால அளவு'),(4638,'continue','Continue','চালিয়ে যান','जारी रखें','पुढे चला','ಮುಂದುವರಿಸಿ','ચાલુ રાખો','தொடரவும்'),(4667,'question','Question','প্রশ্ন','प्रश्न','प्रश्न','ಪ್ರಶ್ನೆ','પ્રશ્ન','வினா'),(4682,'question_list','Question List','প্রশ্নের তালিকা','प्रश्न सूची','प्रश्न यादी','ಪ್ರಶ್ನೆ ಪಟ್ಟಿ','પ્રશ્ન યાદી','வினாப் பட்டியல்'),(4683,'exam_setting','Exam Setting','পরীক্ষার সেটিং','परीक्षा सेटिंग','परीक्षा सेटिंग','ಪರೀಕ್ಷಾ ಸೆಟ್ಟಿಂಗ್','પરીક્ષા સેટિંગ','தேர்வு அமைப்பு'),(4684,'coreect_answer','Coreect Answer','সঠিক উত্তর','सही उत्तर','बरोबर उत्तर','ಸರಿಯಾದ ಉತ್ತರ','સાચો જવાબ','சரியான பதில்'),(4887,'exam_settings','Exam Settings','পরীক্ষার সেটিংস','परीक्षा सेटिंग्स','परीक्षा सेटिंग्ज','ಪರೀಕ್ಷಾ ಸೆಟ್ಟಿಂಗ್‌ಗಳು','પરીક્ષા સેટિંગ્સ','தேர்வு அமைப்புகள்'),(4918,'exam_class','Exam Class','পরীক্ষার ক্লাস','परीक्षा की कक्षा','परीक्षेचा वर्ग','ಪರೀಕ್ಷಾ ತರಗತಿ','પરીક્ષા વર્ગ','தேர்வு வகுப்பு'),(4919,'exam_subject','Exam Subject','পরীক্ষার বিষয়','परीक्षा का विषय','परीक्षेचा विषय','ಪರೀಕ್ಷಾ ವಿಷಯ','પરીક્ષા વિષય','தேர்வுப் பாடம்'),(4941,'save_now','Save Now','এখনই সংরক্ষণ করুন','अभी सहेजें','आता जतन करा','ಈಗ ಉಳಿಸಿ','હમણાં સાચવો','இப்போதே சேமி'),(5414,'all_invoice','All Invoice','সব চালান','सभी इनवॉइस','सर्व बिले','ಎಲ್ಲಾ ಇನ್‌ವಾಯ್ಸ್‌ಗಳು','બધા ઇન્વોઇસ','அனைத்து விலைப்பட்டியல்கள்'),(5468,'student_information_page','Student Information Page','ছাত্রের তথ্য পৃষ্ঠা','छात्र जानकारी पृष्ठ','विद्यार्थी माहिती पृष्ठ','ವಿದ್ಯಾರ್ಥಿ ಮಾಹಿತಿ ಪುಟ','વિદ્યાર્થી માહિતી પેજ','மாணவர் தகவல் பக்கம்'),(5523,'parent_information_page','Parent Information Page','অভিভাবকের তথ্য পৃষ্ঠা','अभिभावक जानकारी पृष्ठ','पालक माहिती पृष्ठ','ಪೋಷಕರ ಮಾಹಿತಿ ಪುಟ','વાલી માહિતી પેજ','பெற்றோர் தகவல் பக்கம்'),(5532,'librarian_information_page','Librarian Information Page','গ্রন্থাগারিকের তথ্য পৃষ্ঠা','पुस्तकालयाध्यक्ष जानकारी पृष्ठ','ग्रंथपाल माहिती पृष्ठ','ಗ್ರಂಥಪಾಲಕ ಮಾಹಿತಿ ಪುಟ','ગ્રંથપાલ માહિતી પેજ','நூலகர் தகவல் பக்கம்'),(5553,'teacher_information_page','Teacher Information Page','শিক্ষকের তথ্য পৃষ্ঠা','शिक्षक जानकारी पृष्ठ','शिक्षक माहिती पृष्ठ','ಶಿಕ್ಷಕ ಮಾಹಿತಿ ಪುಟ','શિક્ષક માહિતી પેજ','ஆசிரியர் தகவல் பக்கம்'),(5586,'accountant_information_page','Accountant Information Page','হিসাবরক্ষকের তথ্য পৃষ্ঠা','लेखाकार जानकारी पृष्ठ','लेखापाल माहिती पृष्ठ','ಲೆಕ್ಕಿಗ ಮಾಹಿತಿ ಪುಟ','હિસાબનીશ માહિતી પેજ','கணக்காளர் தகவல் பக்கம்'),(5591,'hestel_information_page','Hestel Information Page','হোস্টেলের তথ্য পৃষ্ঠা','हॉस्टल जानकारी पृष्ठ','वसतिगृह माहिती पृष्ठ','ಹಾಸ್ಟೆಲ್ ಮಾಹಿತಿ ಪುಟ','છાત્રાલય માહિતી પેજ','விடுதித் தகவல் பக்கம்'),(5611,'subject_information','Subject Information','বিষয়ের তথ্য','विषय जानकारी','विषय माहिती','ವಿಷಯ ಮಾಹಿತಿ','વિષય માહિતી','பாடத் தகவல்'),(5732,'exam_information_page','Exam Information Page','পরীক্ষার তথ্য পৃষ্ঠা','परीक्षा जानकारी पृष्ठ','परीक्षा माहिती पृष्ठ','ಪರೀಕ್ಷಾ ಮಾಹಿತಿ ಪುಟ','પરીક્ષા માહિતી પેજ','தேர்வுத் தகவல் பக்கம்'),(5749,'grade_information_page','Grade Information Page','গ্রেডের তথ্য পৃষ্ঠা','ग्रेड जानकारी पृष्ठ','श्रेणी माहिती पृष्ठ','ಗ್ರೇಡ್ ಮಾಹಿತಿ ಪುಟ','ગ્રેડ માહિતી પેજ','கிரேடு தகவல் பக்கம்'),(5790,'assignment_information_page','Assignment Information Page','অ্যাসাইনমেন্টের তথ্য পৃষ্ঠা','असाइनमेंट जानकारी पृष्ठ','स्वाध्याय माहिती पृष्ठ','ಅಸೈನ್‌ಮೆಂಟ್ ಮಾಹಿತಿ ಪುಟ','અસાઇનમેન્ટ માહિતી પેજ','பணித் தகவல் பக்கம்'),(5842,'expense_information_page','Expense Information Page','ব্যয়ের তথ্য পৃষ্ঠা','व्यय जानकारी पृष्ठ','खर्च माहिती पृष्ठ','ವೆಚ್ಚ ಮಾಹಿತಿ ಪುಟ','ખર્ચ માહિતી પેજ','செலவுத் தகவல் பக்கம்'),(5885,'noticeborad_information_page','Noticeborad Information Page','নোটিশবোর্ডের তথ্য পৃষ্ঠা','सूचना पट्ट जानकारी पृष्ठ','सूचनाफलक माहिती पृष्ठ','ಸೂಚನಾ ಫಲಕ ಮಾಹಿತಿ ಪುಟ','નોટિસબોર્ડ માહિતી પેજ','அறிவிப்புப் பலகைத் தகவல் பக்கம்'),(5892,'holiday_information_page','Holiday Information Page','ছুটির তথ্য পৃষ্ঠা','अवकाश जानकारी पृष्ठ','सुट्टी माहिती पृष्ठ','ರಜೆ ಮಾಹಿತಿ ಪುಟ','રજા માહિતી પેજ','விடுமுறைத் தகவல் பக்கம்'),(5901,'todays_thought_information_page','Todays Thought Information Page','আজকের ভাবনার তথ্য পৃষ্ঠা','आज का विचार जानकारी पृष्ठ','आजचा विचार माहिती पृष्ठ','ಇಂದಿನ ಚಿಂತನೆ ಮಾಹಿತಿ ಪುಟ','આજનો વિચાર માહિતી પેજ','இன்றைய சிந்தனைத் தகவல் பக்கம்'),(5902,'add_thought','Add Thought','ভাবনা যোগ করুন','विचार जोड़ें','विचार जोडा','ಚಿಂತನೆ ಸೇರಿಸಿ','વિચાર ઉમેરો','சிந்தனையைச் சேர்'),(5913,'sms_information_page','Sms Information Page','এসএমএসের তথ্য পৃষ্ঠা','एसएमएस जानकारी पृष्ठ','एसएमएस माहिती पृष्ठ','SMS ಮಾಹಿತಿ ಪುಟ','SMS માહિતી પેજ','SMS தகவல் பக்கம்'),(5930,'language_information_page','Language Information Page','ভাষার তথ্য পৃষ্ঠা','भाषा जानकारी पृष्ठ','भाषा माहिती पृष्ठ','ಭಾಷಾ ಮಾಹಿತಿ ಪುಟ','ભાષા માહિતી પેજ','மொழித் தகவல் பக்கம்'),(5980,'banner_information_page','Banner Information Page','ব্যানারের তথ্য পৃষ্ঠা','बैनर जानकारी पृष्ठ','बॅनर माहिती पृष्ठ','ಬ್ಯಾನರ್ ಮಾಹಿತಿ ಪುಟ','બેનર માહિતી પેજ','பேனர் தகவல் பக்கம்'),(5983,'add_new_banner','Add New Banner','নতুন ব্যানার যোগ করুন','नया बैनर जोड़ें','नवीन बॅनर जोडा','ಹೊಸ ಬ್ಯಾನರ್ ಸೇರಿಸಿ','નવું બેનર ઉમેરો','புதிய பேனரைச் சேர்'),(5990,'news_information_page','News Information Page','সংবাদের তথ্য পৃষ্ঠা','समाचार जानकारी पृष्ठ','बातमी माहिती पृष्ठ','ಸುದ್ದಿ ಮಾಹಿತಿ ಪುಟ','સમાચાર માહિતી પેજ','செய்தித் தகவல் பக்கம்'),(5997,'help_link_information_page','Help Link Information Page','সহায়ক লিংকের তথ্য পৃষ্ঠা','सहायता लिंक जानकारी पृष्ठ','मदत दुवा माहिती पृष्ठ','ಸಹಾಯ ಲಿಂಕ್ ಮಾಹಿತಿ ಪುಟ','મદદ લિંક માહિતી પેજ','உதவி இணைப்புத் தகவல் பக்கம்'),(6002,'add_help','Add Help','সহায়তা যোগ করুন','सहायता जोड़ें','मदत जोडा','ಸಹಾಯ ಸೇರಿಸಿ','મદદ ઉમેરો','உதவியைச் சேர்'),(6007,'help_information_page','Help Information Page','সহায়তার তথ্য পৃষ্ঠা','सहायता जानकारी पृष्ठ','मदत माहिती पृष्ठ','ಸಹಾಯ ಮಾಹಿತಿ ಪುಟ','મદદ માહિતી પેજ','உதவித் தகவல் பக்கம்'),(6093,'save_changes','Save Changes','পরিবর্তন সংরক্ষণ','बदलाव सहेजें','बदल जतन करा','ಬದಲಾವಣೆಗಳನ್ನು ಉಳಿಸಿ','ફેરફારો સાચવો','மாற்றங்களைச் சேமி'),(6487,'manage_thoughts','Manage Thoughts','ভাবনা পরিচালনা','विचार प्रबंधन','विचार व्यवस्थापन','ಚಿಂತನೆಗಳ ನಿರ್ವಹಣೆ','વિચારોનું સંચાલન','சிந்தனைகளை நிர்வகி'),(6492,'transportations','Transportations','পরিবহনসমূহ','परिवहन','वाहतूक','ಸಾರಿಗೆಗಳು','પરિવહન','போக்குவரத்துகள்'),(6493,'syllabus','Syllabus','সিলেবাস','पाठ्यक्रम','अभ्यासक्रम','ಪಠ್ಯಕ್ರಮ','અભ્યાસક્રમ','பாடத்திட்டம்'),(6494,'exam_question','Exam Question','পরীক্ষার প্রশ্ন','परीक्षा प्रश्न','परीक्षा प्रश्न','ಪರೀಕ್ಷಾ ಪ್ರಶ್ನೆ','પરીક્ષા પ્રશ્ન','தேர்வு வினா'),(6495,'thoughts','Thoughts','ভাবনাসমূহ','विचार','विचार','ಚಿಂತನೆಗಳು','વિચારો','சிந்தனைகள்'),(6496,'help_desk','Help Desk','হেল্প ডেস্ক','हेल्प डेस्क','हेल्प डेस्क','ಸಹಾಯ ಕೇಂದ್ರ','હેલ્પ ડેસ્ક','உதவி மையம்'),(6497,'results','Results','ফলাফলসমূহ','परिणाम','निकाल','ಫಲಿತಾಂಶಗಳು','પરિણામો','முடிவுகள்'),(6498,'enquiry','Enquiry','অনুসন্ধান','पूछताछ','चौकशी','ವಿಚಾರಣೆ','પૂછપરછ','விசாரணை'),(6499,'media','Media','মিডিয়া','मीडिया','मीडिया','ಮೀಡಿಯಾ','મીડિયા','ஊடகம்'),(6752,'on','On','চালু','चालू','चालू','ಆನ್','ચાલુ','இயக்கம்'),(6888,'standard','Standard','সাধারণ','सामान्य','सामान्य','ಸಾಮಾನ್ಯ','સામાન્ય','வழக்கமான'),(7769,'upload_xls_csv_instructions','Upload an .xls, .xlsx or .csv file. The first row must contain the column headings below.','একটি .xls, .xlsx বা .csv ফাইল আপলোড করুন। প্রথম সারিতে নিচের কলাম শিরোনামগুলি থাকতে হবে।','एक .xls, .xlsx या .csv फ़ाइल अपलोड करें। पहली पंक्ति में नीचे दिए गए कॉलम शीर्षक होने चाहिए।','.xls, .xlsx किंवा .csv फाइल अपलोड करा. पहिल्या ओळीत खालील कॉलम शीर्षके असणे आवश्यक आहे.','.xls, .xlsx ಅಥವಾ .csv ಫೈಲ್ ಅಪ್‌ಲೋಡ್ ಮಾಡಿ. ಮೊದಲ ಸಾಲಿನಲ್ಲಿ ಕೆಳಗಿನ ಕಾಲಮ್ ಶೀರ್ಷಿಕೆಗಳು ಇರಬೇಕು.','.xls, .xlsx અથવા .csv ફાઇલ અપલોડ કરો. પહેલી હરોળમાં નીચેના કૉલમ મથાળાં હોવા જોઈએ.','.xls, .xlsx அல்லது .csv கோப்பைப் பதிவேற்றவும். முதல் வரிசையில் கீழே உள்ள நெடுவரிசைத் தலைப்புகள் இருக்க வேண்டும்.'),(7761,'select_staff','Select Staff','কর্মী বেছে নিন','स्टाफ चुनें','कर्मचारी निवडा','ಸಿಬ್ಬಂದಿ ಆಯ್ಕೆಮಾಡಿ','સ્ટાફ પસંદ કરો','பணியாளரைத் தேர்ந்தெடு'),(7753,'photo_upload_failed','Photo upload failed (file may be too large)','ছবি আপলোড ব্যর্থ (ফাইল খুব বড় হতে পারে)','फ़ोटो अपलोड विफल (फ़ाइल बहुत बड़ी हो सकती है)','फोटो अपलोड अयशस्वी (फाइल खूप मोठी असू शकते)','ಫೋಟೋ ಅಪ್‌ಲೋಡ್ ವಿಫಲವಾಗಿದೆ (ಫೈಲ್ ತುಂಬಾ ದೊಡ್ಡದಿರಬಹುದು)','ફોટો અપલોડ નિષ્ફળ (ફાઇલ ખૂબ મોટી હોઈ શકે)','புகைப்படப் பதிவேற்றம் தோல்வி (கோப்பு மிகப் பெரியதாக இருக்கலாம்)'),(7749,'no_phrases_found','No phrases found','কোনো বাক্যাংশ পাওয়া যায়নি','कोई वाक्यांश नहीं मिला','कोणतीही वाक्ये सापडली नाहीत','ಯಾವುದೇ ಪದಗುಚ್ಛ ಕಂಡುಬಂದಿಲ್ಲ','કોઈ શબ્દસમૂહ મળ્યો નથી','சொற்றொடர்கள் எதுவும் கிடைக்கவில்லை'),(7070,'total_fees','Total Fees','মোট ফি','कुल फीस','एकूण शुल्क','ಒಟ್ಟು ಶುಲ್ಕ','કુલ ફી','மொத்தக் கட்டணம்'),(7745,'installments_hint','The course fee is split equally into this many installments. You can adjust each one after saving.','কোর্স ফি এতগুলি সমান কিস্তিতে ভাগ করা হবে। সংরক্ষণের পরে প্রতিটি কিস্তি পরিবর্তন করতে পারবেন।','कोर्स शुल्क इतनी बराबर किश्तों में बाँटा जाएगा। सहेजने के बाद आप हर किश्त बदल सकते हैं।','कोर्स शुल्क इतक्या समान हप्त्यांमध्ये विभागले जाईल. जतन केल्यानंतर तुम्ही प्रत्येक हप्ता बदलू शकता.','ಕೋರ್ಸ್ ಶುಲ್ಕವನ್ನು ಇಷ್ಟು ಸಮಾನ ಕಂತುಗಳಾಗಿ ವಿಂಗಡಿಸಲಾಗುತ್ತದೆ. ಉಳಿಸಿದ ನಂತರ ಪ್ರತಿ ಕಂತನ್ನು ಬದಲಾಯಿಸಬಹುದು.','કોર્સ ફી આટલા સરખા હપ્તામાં વહેંચાશે. સાચવ્યા પછી તમે દરેક હપ્તો બદલી શકો છો.','பாடநெறிக் கட்டணம் இத்தனை சம தவணைகளாகப் பிரிக்கப்படும். சேமித்த பிறகு ஒவ்வொன்றையும் மாற்றலாம்.'),(7739,'handled_by','Handled By','দায়িত্বপ্রাপ্ত','द्वारा संभाला गया','हाताळणारे','ನಿರ್ವಹಿಸಿದವರು','સંભાળનાર','கையாண்டவர்'),(7734,'enquiry_name','Enquiry Name','অনুসন্ধানকারীর নাম','पूछताछकर्ता का नाम','चौकशी करणाऱ्याचे नाव','ವಿಚಾರಣೆದಾರರ ಹೆಸರು','પૂછપરછ કરનારનું નામ','விசாரிப்பவர் பெயர்'),(7728,'edit_enquiry','Edit Enquiry','অনুসন্ধান সম্পাদনা করুন','पूछताछ संपादित करें','चौकशी संपादित करा','ವಿಚಾರಣೆ ಸಂಪಾದಿಸಿ','પૂછપરછ સંપાદિત કરો','விசாரணையைத் திருத்து'),(7724,'current_language_cannot_be_deleted','The current language cannot be deleted','বর্তমান ভাষা মোছা যাবে না','वर्तमान भाषा हटाई नहीं जा सकती','सध्याची भाषा हटवता येत नाही','ಪ್ರಸ್ತುತ ಭಾಷೆಯನ್ನು ಅಳಿಸಲಾಗುವುದಿಲ್ಲ','વર્તમાન ભાષા કાઢી શકાતી નથી','தற்போதைய மொழியை நீக்க முடியாது'),(7200,'select_section','Select Section','সেকশন নির্বাচন করুন','सेक्शन चुनें','तुकडी निवडा','ವಿಭಾಗ ಆಯ್ಕೆಮಾಡಿ','વિભાગ પસંદ કરો','பிரிவைத் தேர்ந்தெடுக்கவும்'),(7239,'select_student','Select Student','ছাত্র নির্বাচন করুন','छात्र चुनें','विद्यार्थी निवडा','ವಿದ್ಯಾರ್ಥಿ ಆಯ್ಕೆಮಾಡಿ','વિદ્યાર્થી પસંદ કરો','மாணவரைத் தேர்ந்தெடுக்கவும்'),(7240,'save_payment','Save Payment','পেমেন্ট সংরক্ষণ','भुगतान सहेजें','पेमेंट जतन करा','ಪಾವತಿ ಉಳಿಸಿ','ચુકવણી સાચવો','கட்டணத்தைச் சேமி'),(7571,'test_whatsapp','Test Whatsapp','হোয়াটসঅ্যাপ পরীক্ষা','व्हाट्सऐप टेस्ट करें','व्हॉट्सअॅप चाचणी','ವಾಟ್ಸಾಪ್ ಪರೀಕ್ಷಿಸಿ','વૉટ્સએપ ટેસ્ટ','வாட்ஸ்அப் சோதனை'),(7608,'add_youtube_media','Add YouTube Media','ইউটিউব মিডিয়া যোগ করুন','यूट्यूब मीडिया जोड़ें','यूट्यूब मीडिया जोडा','ಯೂಟ್ಯೂಬ್ ಮೀಡಿಯಾ ಸೇರಿಸಿ','YouTube મીડિયા ઉમેરો','YouTube ஊடகத்தைச் சேர்'),(7609,'media_list','Media List','মিডিয়ার তালিকা','मीडिया सूची','मीडिया यादी','ಮೀಡಿಯಾ ಪಟ್ಟಿ','મીડિયા યાદી','ஊடகப் பட்டியல்'),(7610,'media_added_successfully','Media added successfully','মিডিয়া সফলভাবে যোগ হয়েছে','मीडिया सफलतापूर्वक जोड़ा गया','मीडिया यशस्वीरित्या जोडला','ಮೀಡಿಯಾ ಯಶಸ್ವಿಯಾಗಿ ಸೇರಿಸಲಾಗಿದೆ','મીડિયા સફળતાપૂર્વક ઉમેરાયું','ஊடகம் வெற்றிகரமாகச் சேர்க்கப்பட்டது'),(7611,'media_deleted','Media deleted','মিডিয়া মুছে ফেলা হয়েছে','मीडिया हटाया गया','मीडिया हटवला','ಮೀಡಿಯಾ ಅಳಿಸಲಾಗಿದೆ','મીડિયા કાઢી નાખ્યું','ஊடகம் நீக்கப்பட்டது'),(7634,'manage_enquiry_category_information_page','Manage Enquiry Category Information Page','অনুসন্ধানের বিভাগ তথ্য পৃষ্ঠা পরিচালনা','पूछताछ श्रेणी जानकारी प्रबंधन पृष्ठ','चौकशी वर्ग माहिती व्यवस्थापन पृष्ठ','ವಿಚಾರಣೆ ವರ್ಗ ಮಾಹಿತಿ ಪುಟ ನಿರ್ವಹಣೆ','પૂછપરછ શ્રેણી સંચાલન માહિતી પેજ','விசாரணை வகைத் தகவல் பக்க நிர்வாகம்'),(7677,'edit_expense_category','Edit Expense Category','ব্যয়ের বিভাগ সম্পাদনা','व्यय श्रेणी संपादित करें','खर्च वर्ग संपादन','ವೆಚ್ಚ ವರ್ಗ ಸಂಪಾದಿಸಿ','ખર્ચ શ્રેણી ફેરફાર','செலவு வகையைத் திருத்து'),(7679,'add_enquiry','Add Enquiry','অনুসন্ধান যোগ করুন','पूछताछ जोड़ें','चौकशी जोडा','ವಿಚಾರಣೆ ಸೇರಿಸಿ','પૂછપરછ ઉમેરો','விசாரணையைச் சேர்'),(7680,'bulk_enquiry_import','Bulk Enquiry Import','একসাথে অনুসন্ধান ইমপোর্ট','थोक पूछताछ इम्पोर्ट','सामूहिक चौकशी इम्पोर्ट','ಸಾಮೂಹಿಕ ವಿಚಾರಣೆ ಆಮದು','સામૂહિક પૂછપરછ ઇમ્પોર્ટ','விசாரணைகள் மொத்த இறக்குமதி'),(7681,'courses','Courses','কোর্সসমূহ','पाठ्यक्रम','अभ्यासक्रम','ಕೋರ್ಸ್‌ಗಳು','અભ્યાસક્રમો','படிப்புகள்'),(7682,'manage_courses','Manage Courses','কোর্স পরিচালনা','पाठ्यक्रम प्रबंधन','अभ्यासक्रम व्यवस्थापन','ಕೋರ್ಸ್‌ಗಳ ನಿರ್ವಹಣೆ','અભ્યાસક્રમ સંચાલન','படிப்புகளை நிர்வகி'),(7683,'new_course','New Course','নতুন কোর্স','नया पाठ्यक्रम','नवीन अभ्यासक्रम','ಹೊಸ ಕೋರ್ಸ್','નવો અભ્યાસક્રમ','புதிய படிப்பு'),(7719,'contact_no','Contact No','যোগাযোগ নম্বর','संपर्क नंबर','संपर्क क्रमांक','ಸಂಪರ್ಕ ಸಂಖ್ಯೆ','સંપર્ક નંબર','தொடர்பு எண்'),(7685,'name_is_required','Name Is Required','নাম আবশ্যক','नाम आवश्यक है','नाव आवश्यक आहे','ಹೆಸರು ಅಗತ್ಯವಿದೆ','નામ જરૂરી છે','பெயர் தேவை'),(7686,'invalid_email_address','Invalid Email Address','ইমেল ঠিকানা সঠিক নয়','अमान्य ईमेल पता','अवैध ईमेल पत्ता','ಅಮಾನ್ಯ ಇಮೇಲ್ ವಿಳಾಸ','અમાન્ય ઈમેલ સરનામું','தவறான மின்னஞ்சல் முகவரி'),(7687,'photo_must_be_an_image','Photo Must Be An Image','ছবি অবশ্যই একটি ইমেজ হতে হবে','फ़ोटो छवि होनी चाहिए','फोटो प्रतिमा असणे आवश्यक आहे','ಫೋಟೋ ಚಿತ್ರವಾಗಿರಬೇಕು','ફોટો છબી હોવો જોઈએ','புகைப்படம் படக்கோப்பாக இருக்க வேண்டும்'),(7688,'current_password_is_incorrect','Current Password Is Incorrect','বর্তমান পাসওয়ার্ড ভুল','वर्तमान पासवर्ड गलत है','सध्याचा पासवर्ड चुकीचा आहे','ಪ್ರಸ್ತುತ ಪಾಸ್‌ವರ್ಡ್ ತಪ್ಪಾಗಿದೆ','હાલનો પાસવર્ડ ખોટો છે','தற்போதைய கடவுச்சொல் தவறானது'),(7689,'new_password_is_required','New Password Is Required','নতুন পাসওয়ার্ড আবশ্যক','नया पासवर्ड आवश्यक है','नवीन पासवर्ड आवश्यक आहे','ಹೊಸ ಪಾಸ್‌ವರ್ಡ್ ಅಗತ್ಯವಿದೆ','નવો પાસવર્ડ જરૂરી છે','புதிய கடவுச்சொல் தேவை'),(7690,'new_passwords_do_not_match','New Passwords Do Not Match','নতুন পাসওয়ার্ড মেলেনি','नए पासवर्ड मेल नहीं खाते','नवीन पासवर्ड जुळत नाहीत','ಹೊಸ ಪಾಸ್‌ವರ್ಡ್‌ಗಳು ಹೊಂದಿಕೆಯಾಗುತ್ತಿಲ್ಲ','નવા પાસવર્ડ મેળ ખાતા નથી','புதிய கடவுச்சொற்கள் பொருந்தவில்லை'),(7691,'translated','Translated','অনূদিত','अनुवादित','भाषांतरित','ಅನುವಾದಿತ','અનુવાદિત','மொழிபெயர்க்கப்பட்டவை'),(7692,'current','Current','বর্তমান','वर्तमान','सध्याची','ಪ್ರಸ್ತುತ','વર્તમાન','தற்போதைய'),(7693,'untranslated','Untranslated','অনুবাদ বাকি','अनुवाद बाकी','भाषांतर बाकी','ಅನುವಾದ ಬಾಕಿ','અનુવાદ બાકી','மொழிபெயர்க்கப்படாதவை'),(7694,'this_cannot_be_undone','This cannot be undone.','এটি আর ফেরানো যাবে না।','इसे पूर्ववत नहीं किया जा सकता।','हे पूर्ववत करता येणार नाही.','ಇದನ್ನು ರದ್ದುಗೊಳಿಸಲು ಸಾಧ್ಯವಿಲ್ಲ.','આ પાછું ફેરવી શકાશે નહીં.','இதைத் திரும்பப் பெற முடியாது.'),(7695,'choose_the_system_language_in','Choose the system language in','সিস্টেমের ভাষা এখানে বেছে নিন:','सिस्टम भाषा यहाँ चुनें:','सिस्टम भाषा येथे निवडा:','ಸಿಸ್ಟಮ್ ಭಾಷೆಯನ್ನು ಇಲ್ಲಿ ಆಯ್ಕೆಮಾಡಿ:','સિસ્ટમ ભાષા અહીં પસંદ કરો:','கணினி மொழியை இங்கே தேர்ந்தெடுக்கவும்:'),(7696,'language_name_letters_only','Use English letters only (2-30), e.g. telugu','শুধু ইংরেজি অক্ষর (2-30) ব্যবহার করুন, যেমন telugu','केवल अंग्रेज़ी अक्षर (2-30) लिखें, जैसे telugu','फक्त इंग्रजी अक्षरे (2-30) वापरा, उदा. telugu','ಇಂಗ್ಲಿಷ್ ಅಕ್ಷರಗಳನ್ನು ಮಾತ್ರ ಬಳಸಿ (2-30), ಉದಾ. telugu','ફક્ત અંગ્રેજી અક્ષરો (2-30) વાપરો, જેમ કે telugu','ஆங்கில எழுத்துகளை மட்டும் பயன்படுத்தவும் (2-30), எ.கா. telugu'),(7697,'search_phrase','Search phrase','বাক্যাংশ খুঁজুন','वाक्यांश खोजें','वाक्य शोधा','ಪದಗುಚ್ಛ ಹುಡುಕಿ','શબ્દસમૂહ શોધો','சொற்றொடரைத் தேடு'),(7698,'all_phrases','All Phrases','সব বাক্যাংশ','सभी वाक्यांश','सर्व वाक्ये','ಎಲ್ಲಾ ಪದಗುಚ್ಛಗಳು','બધા શબ્દસમૂહો','அனைத்து சொற்றொடர்கள்'),(7699,'untranslated_only','Untranslated only','শুধু অনুবাদ বাকি','केवल अनुवाद बाकी','फक्त भाषांतर बाकी','ಅನುವಾದ ಬಾಕಿ ಇರುವುದು ಮಾತ್ರ','ફક્ત અનુવાદ બાકી','மொழிபெயர்க்கப்படாதவை மட்டும்'),(7700,'search','Search','খুঁজুন','खोजें','शोधा','ಹುಡುಕಿ','શોધો','தேடு'),(7701,'phrases','Phrases','বাক্যাংশ','वाक्यांश','वाक्ये','ಪದಗುಚ್ಛಗಳು','શબ્દસમૂહો','சொற்றொடர்கள்'),(7702,'english','English','ইংরেজি','अंग्रेज़ी','इंग्रजी','ಇಂಗ್ಲಿಷ್','અંગ્રેજી','ஆங்கிலம்'),(7703,'page','Page','পৃষ্ঠা','पृष्ठ','पृष्ठ','ಪುಟ','પૃષ્ઠ','பக்கம்'),(7704,'unsaved_changes','unsaved changes','অসংরক্ষিত পরিবর্তন','बिना सहेजे बदलाव','जतन न केलेले बदल','ಉಳಿಸದ ಬದಲಾವಣೆಗಳು','સાચવ્યા વગરના ફેરફારો','சேமிக்கப்படாத மாற்றங்கள்'),(7705,'clear','Clear','মুছুন','साफ़ करें','साफ करा','ತೆರವುಗೊಳಿಸಿ','સાફ કરો','அழி'),(7706,'phrases_updated','Phrases updated','বাক্যাংশ হালনাগাদ হয়েছে','वाक्यांश अपडेट किए गए','वाक्ये अद्ययावत केली','ಪದಗುಚ್ಛಗಳನ್ನು ನವೀಕರಿಸಲಾಗಿದೆ','શબ્દસમૂહો અપડેટ થયા','சொற்றொடர்கள் புதுப்பிக்கப்பட்டன'),(7707,'language_already_exists','This language already exists','এই ভাষা ইতিমধ্যে আছে','यह भाषा पहले से मौजूद है','ही भाषा आधीच अस्तित्वात आहे','ಈ ಭಾಷೆ ಈಗಾಗಲೇ ಇದೆ','આ ભાષા પહેલેથી છે','இந்த மொழி ஏற்கனவே உள்ளது'),(7708,'language_added','Language added','ভাষা যোগ করা হয়েছে','भाषा जोड़ी गई','भाषा जोडली','ಭಾಷೆ ಸೇರಿಸಲಾಗಿದೆ','ભાષા ઉમેરાઈ','மொழி சேர்க்கப்பட்டது'),(7709,'english_cannot_be_deleted','English cannot be deleted','ইংরেজি মোছা যাবে না','अंग्रेज़ी हटाई नहीं जा सकती','इंग्रजी हटवता येत नाही','ಇಂಗ್ಲಿಷ್ ಅಳಿಸಲಾಗುವುದಿಲ್ಲ','અંગ્રેજી કાઢી શકાતી નથી','ஆங்கிலத்தை நீக்க முடியாது'),(7710,'language_not_found','Language not found','ভাষা পাওয়া যায়নি','भाषा नहीं मिली','भाषा सापडली नाही','ಭಾಷೆ ಕಂಡುಬಂದಿಲ್ಲ','ભાષા મળી નથી','மொழி கிடைக்கவில்லை'),(7711,'language_deleted','Language deleted','ভাষা মুছে ফেলা হয়েছে','भाषा हटाई गई','भाषा हटवली','ಭಾಷೆ ಅಳಿಸಲಾಗಿದೆ','ભાષા કાઢી નાખી','மொழி நீக்கப்பட்டது'),(7914,'publish_results_confirm','Publish the results and email every student (and parent)?','ফলাফল প্রকাশ করে প্রতিটি শিক্ষার্থীকে (ও অভিভাবককে) ইমেল পাঠাবেন?','परिणाम प्रकाशित करें और हर छात्र (और अभिभावक) को ईमेल भेजें?','निकाल प्रकाशित करून प्रत्येक विद्यार्थ्याला (व पालकाला) ईमेल करायचा?','ಫಲಿತಾಂಶಗಳನ್ನು ಪ್ರಕಟಿಸಿ ಪ್ರತಿ ವಿದ್ಯಾರ್ಥಿಗೆ (ಮತ್ತು ಪೋಷಕರಿಗೆ) ಇಮೇಲ್ ಮಾಡಬೇಕೇ?','પરિણામ પ્રકાશિત કરીને દરેક વિદ્યાર્થી (અને વાલી) ને ઈમેલ કરવો?','முடிவுகளை வெளியிட்டு ஒவ்வொரு மாணவருக்கும் (மற்றும் பெற்றோருக்கும்) மின்னஞ்சல் அனுப்பவா?'),(7915,'publish_results_and_email','Publish Results And Email','ফলাফল প্রকাশ করুন ও ইমেল পাঠান','परिणाम प्रकाशित करें और ईमेल भेजें','निकाल प्रकाशित करा आणि ईमेल करा','ಫಲಿತಾಂಶ ಪ್ರಕಟಿಸಿ ಮತ್ತು ಇಮೇಲ್ ಮಾಡಿ','પરિણામ પ્રકાશિત કરો અને ઈમેલ કરો','முடிவுகளை வெளியிட்டு மின்னஞ்சல் அனுப்பு'),(7916,'publish_results_hint','Students can see their result in the portal only after you publish.','আপনি প্রকাশ করার পরই শিক্ষার্থীরা পোর্টালে নিজেদের ফলাফল দেখতে পাবে।','आपके प्रकाशित करने के बाद ही छात्र पोर्टल में अपना परिणाम देख सकते हैं।','तुम्ही प्रकाशित केल्यानंतरच विद्यार्थ्यांना पोर्टलमध्ये त्यांचा निकाल दिसेल.','ನೀವು ಪ್ರಕಟಿಸಿದ ನಂತರವೇ ವಿದ್ಯಾರ್ಥಿಗಳು ಪೋರ್ಟಲ್‌ನಲ್ಲಿ ತಮ್ಮ ಫಲಿತಾಂಶ ನೋಡಬಹುದು.','તમે પ્રકાશિત કરો પછી જ વિદ્યાર્થીઓ પોર્ટલમાં પોતાનું પરિણામ જોઈ શકશે.','நீங்கள் வெளியிட்ட பிறகுதான் மாணவர்கள் தங்கள் முடிவை போர்ட்டலில் காண முடியும்.'),(7917,'pass','Pass','উত্তীর্ণ','उत्तीर्ण','उत्तीर्ण','ಉತ್ತೀರ್ಣ','પાસ','தேர்ச்சி'),(7918,'results_published','Results Published','ফলাফল প্রকাশিত হয়েছে','परिणाम प्रकाशित हो गए','निकाल प्रकाशित केले','ಫಲಿತಾಂಶ ಪ್ರಕಟಿಸಲಾಗಿದೆ','પરિણામ પ્રકાશિત થયા','முடிவுகள் வெளியிடப்பட்டன'),(7919,'correct','Correct','সঠিক','सही','बरोबर','ಸರಿ','સાચો','சரி'),(7920,'wrong','Wrong','ভুল','गलत','चूक','ತಪ್ಪು','ખોટો','தவறு'),(7921,'skipped','Skipped','বাদ দেওয়া হয়েছে','छोड़ा गया','वगळले','ಬಿಟ್ಟುಬಿಡಲಾಗಿದೆ','છોડી દીધા','தவிர்க்கப்பட்டது'),(7922,'answers','Answers','উত্তরসমূহ','उत्तर','उत्तरे','ಉತ್ತರಗಳು','જવાબો','பதில்கள்'),(7923,'your_answer','Your Answer','আপনার উত্তর','आपका उत्तर','तुमचे उत्तर','ನಿಮ್ಮ ಉತ್ತರ','તમારો જવાબ','உங்கள் பதில்'),(7924,'print','Print','প্রিন্ট','प्रिंट करें','प्रिंट','ಮುದ್ರಿಸಿ','પ્રિન્ટ','அச்சிடு'),(7925,'exam_has_submitted_attempts_and_cannot_be_deleted','This exam has submitted answers and cannot be deleted','এই পরীক্ষায় উত্তর জমা পড়েছে, তাই মুছে ফেলা যাবে না','इस परीक्षा के उत्तर सबमिट हो चुके हैं और इसे हटाया नहीं जा सकता','या परीक्षेची उत्तरे सबमिट झाली आहेत, त्यामुळे ती हटवता येत नाही','ಈ ಪರೀಕ್ಷೆಗೆ ಉತ್ತರಗಳನ್ನು ಸಲ್ಲಿಸಲಾಗಿದೆ, ಅಳಿಸಲು ಸಾಧ್ಯವಿಲ್ಲ','આ પરીક્ષામાં સબમિટ કરેલા જવાબો છે અને તેને કાઢી શકાતી નથી','இந்தத் தேர்வில் சமர்ப்பிக்கப்பட்ட பதில்கள் உள்ளன, எனவே நீக்க முடியாது'),(7926,'resend','Resend','আবার পাঠান','फिर से भेजें','पुन्हा पाठवा','ಮರುಕಳುಹಿಸಿ','ફરી મોકલો','மீண்டும் அனுப்பு'),(7927,'default_grades_added','Standard grades added','স্ট্যান্ডার্ড গ্রেড যোগ হয়েছে','मानक ग्रेड जोड़ दिए गए','प्रमाणित श्रेणी जोडल्या','ಸ್ಟ್ಯಾಂಡರ್ಡ್ ಗ್ರೇಡ್‌ಗಳನ್ನು ಸೇರಿಸಲಾಗಿದೆ','સ્ટાન્ડર્ડ ગ્રેડ ઉમેર્યા','நிலையான தரங்கள் சேர்க்கப்பட்டன'),(7928,'grade_range_overlaps_another_grade','This range overlaps another grade','এই সীমা অন্য গ্রেডের সঙ্গে মিলে যাচ্ছে','यह सीमा किसी अन्य ग्रेड से मेल खाती है','ही मर्यादा दुसऱ्या श्रेणीशी जुळते','ಈ ವ್ಯಾಪ್ತಿ ಮತ್ತೊಂದು ಗ್ರೇಡ್‌ನೊಂದಿಗೆ ಅತಿಕ್ರಮಿಸುತ್ತದೆ','આ શ્રેણી બીજા ગ્રેડ સાથે મળતી આવે છે','இந்த வரம்பு மற்றொரு தரத்துடன் மேலெழுகிறது'),(7929,'mark_from_must_not_exceed_mark_upto','\'From\' must not be more than \'To\'','\'থেকে\' মান \'পর্যন্ত\' মানের বেশি হতে পারবে না','\'से\' का मान \'तक\' से अधिक नहीं होना चाहिए','\'पासून\' हे \'पर्यंत\' पेक्षा जास्त नसावे','\'ಇಂದ\' ಮೌಲ್ಯ \'ವರೆಗೆ\' ಮೌಲ್ಯಕ್ಕಿಂತ ಹೆಚ್ಚಿರಬಾರದು','\'થી\' એ \'સુધી\' કરતાં વધારે ન હોવું જોઈએ','\'இருந்து\' என்பது \'வரை\' என்பதை விட அதிகமாக இருக்கக்கூடாது'),(7930,'select_at_least_one_class','Select At Least One Class','কমপক্ষে একটি শ্রেণি নির্বাচন করুন','कम से कम एक कक्षा चुनें','किमान एक वर्ग निवडा','ಕನಿಷ್ಠ ಒಂದು ತರಗತಿ ಆಯ್ಕೆಮಾಡಿ','ઓછામાં ઓછો એક વર્ગ પસંદ કરો','குறைந்தது ஒரு வகுப்பைத் தேர்ந்தெடுக்கவும்'),(7931,'exam_schedule_emailed','Exam schedule emailed','পরীক্ষার সময়সূচি ইমেল করা হয়েছে','परीक्षा कार्यक्रम ईमेल किया गया','परीक्षेचे वेळापत्रक ईमेल केले','ಪರೀಕ್ಷೆ ವೇಳಾಪಟ್ಟಿಯನ್ನು ಇಮೇಲ್ ಮಾಡಲಾಗಿದೆ','પરીક્ષાનું સમયપત્રક ઈમેલ કર્યું','தேர்வு அட்டவணை மின்னஞ்சலில் அனுப்பப்பட்டது'),(7932,'marks_must_be_between_0_and_total','Marks must be between 0 and the total','নম্বর ০ থেকে মোট নম্বরের মধ্যে হতে হবে','अंक 0 और कुल अंक के बीच होने चाहिए','गुण 0 ते एकूण गुणांदरम्यान असावेत','ಅಂಕಗಳು 0 ಮತ್ತು ಒಟ್ಟು ಅಂಕಗಳ ನಡುವೆ ಇರಬೇಕು','ગુણ 0 અને કુલ ગુણ વચ્ચે હોવા જોઈએ','மதிப்பெண்கள் 0 முதல் மொத்தம் வரை இருக்க வேண்டும்'),(7933,'marks_must_be_numbers','Marks Must Be Numbers','নম্বর অবশ্যই সংখ্যা হতে হবে','अंक संख्या में होने चाहिए','गुण संख्या असावेत','ಅಂಕಗಳು ಸಂಖ್ಯೆಗಳಾಗಿರಬೇಕು','ગુણ સંખ્યા હોવા જોઈએ','மதிப்பெண்கள் எண்களாக இருக்க வேண்டும்'),(7934,'need_correction','Need Correction','সংশোধন প্রয়োজন','सुधार आवश्यक','दुरुस्ती आवश्यक','ತಿದ್ದುಪಡಿ ಅಗತ್ಯ','સુધારાની જરૂર','திருத்தம் தேவை'),(7935,'fail','Fail','অনুত্তীর্ণ','अनुत्तीर्ण','अनुत्तीर्ण','ಅನುತ್ತೀರ್ಣ','નાપાસ','தோல்வி'),(7936,'no_upcoming_exams','No Upcoming Exams','কোনো আসন্ন পরীক্ষা নেই','कोई आगामी परीक्षा नहीं','आगामी परीक्षा नाहीत','ಮುಂಬರುವ ಪರೀಕ್ಷೆಗಳಿಲ್ಲ','આગામી કોઈ પરીક્ષા નથી','வரவிருக்கும் தேர்வுகள் இல்லை'),(7937,'written','Written','লিখিত','लिखित','लेखी','ಲಿಖಿತ','લેખિત','எழுத்துத்தேர்வு'),(7938,'exam_has_marks_and_cannot_be_deleted','This exam has marks entered and cannot be deleted','এই পরীক্ষায় নম্বর এন্ট্রি করা আছে, তাই মুছে ফেলা যাবে না','इस परीक्षा के अंक दर्ज हैं और इसे हटाया नहीं जा सकता','या परीक्षेचे गुण भरलेले आहेत, त्यामुळे ती हटवता येत नाही','ಈ ಪರೀಕ್ಷೆಗೆ ಅಂಕಗಳನ್ನು ನಮೂದಿಸಲಾಗಿದೆ, ಅಳಿಸಲು ಸಾಧ್ಯವಿಲ್ಲ','આ પરીક્ષામાં ગુણ દાખલ થયેલા છે અને તેને કાઢી શકાતી નથી','இந்தத் தேர்வில் மதிப்பெண்கள் உள்ளிடப்பட்டுள்ளன, எனவே நீக்க முடியாது'),(8009,'time_is_up_your_answers_were_submitted','Time is up. Your answers were submitted.','সময় শেষ। আপনার উত্তর জমা দেওয়া হয়েছে।','समय समाप्त। आपके उत्तर सबमिट कर दिए गए।','वेळ संपली. तुमची उत्तरे सबमिट केली गेली.','ಸಮಯ ಮುಗಿದಿದೆ. ನಿಮ್ಮ ಉತ್ತರಗಳನ್ನು ಸಲ್ಲಿಸಲಾಗಿದೆ.','સમય પૂરો થયો. તમારા જવાબો સબમિટ થઈ ગયા.','நேரம் முடிந்தது. உங்கள் பதில்கள் சமர்ப்பிக்கப்பட்டன.'),(7997,'review','Review','পর্যালোচনা','समीक्षा','तपासा','ಪರಿಶೀಲಿಸಿ','સમીક્ષા','மதிப்பாய்வு'),(7998,'saved_leave_blank_to_keep','Saved - leave blank to keep it','সংরক্ষিত - রাখতে চাইলে ফাঁকা রাখুন','सहेजा गया - रखने के लिए खाली छोड़ें','जतन केले - ठेवण्यासाठी रिकामे सोडा','ಉಳಿಸಲಾಗಿದೆ - ಹಾಗೆಯೇ ಇರಿಸಲು ಖಾಲಿ ಬಿಡಿ','સાચવ્યું - રાખવા માટે ખાલી છોડો','சேமிக்கப்பட்டது - வைத்திருக்க காலியாக விடவும்'),(7992,'remove','Remove','সরান','हटाएँ','काढा','ತೆಗೆದುಹಾಕಿ','દૂર કરો','நீக்கு'),(7993,'result_awaited','Result Awaited','ফলাফলের অপেক্ষায়','परिणाम की प्रतीक्षा','निकालाची प्रतीक्षा','ಫಲಿತಾಂಶ ನಿರೀಕ್ಷೆಯಲ್ಲಿದೆ','પરિણામની રાહ','முடிவு எதிர்பார்க்கப்படுகிறது'),(7990,'question_text_is_required','Question Text Is Required','প্রশ্নের লেখা আবশ্যক','प्रश्न का पाठ आवश्यक है','प्रश्नाचा मजकूर आवश्यक आहे','ಪ್ರಶ್ನೆಯ ಪಠ್ಯ ಅಗತ್ಯ','પ્રશ્નનું લખાણ જરૂરી છે','கேள்வி உரை தேவை'),(7984,'published','Published','প্রকাশিত','प्रकाशित','प्रकाशित','ಪ್ರಕಟಿಸಲಾಗಿದೆ','પ્રકાશિત','வெளியிடப்பட்டது'),(7982,'parent_email','Parent Email','অভিভাবকের ইমেল','अभिभावक ईमेल','पालकांचा ईमेल','ಪೋಷಕರ ಇಮೇಲ್','વાલીનો ઈમેલ','பெற்றோர் மின்னஞ்சல்'),(7768,'update_status','Update Status','অবস্থা হালনাগাদ করুন','स्थिति अपडेट करें','स्थिती अद्ययावत करा','ಸ್ಥಿತಿ ನವೀಕರಿಸಿ','સ્થિતિ અપડેટ કરો','நிலையைப் புதுப்பி'),(7760,'select_course','Select Course','কোর্স বেছে নিন','कोर्स चुनें','कोर्स निवडा','ಕೋರ್ಸ್ ಆಯ್ಕೆಮಾಡಿ','કોર્સ પસંદ કરો','பாடநெறியைத் தேர்ந்தெடு'),(7748,'no_activities_yet','No activities yet','এখনও কোনো কার্যকলাপ নেই','अभी कोई गतिविधि नहीं','अद्याप कोणताही क्रियाकलाप नाही','ಇನ್ನೂ ಯಾವುದೇ ಚಟುವಟಿಕೆ ಇಲ್ಲ','હજી કોઈ પ્રવૃત્તિ નથી','இதுவரை செயல்பாடுகள் இல்லை'),(7738,'fee_installments','Fee Installments','ফি-র কিস্তি','शुल्क किश्तें','शुल्क हप्ते','ಶುಲ್ಕ ಕಂತುಗಳು','ફી હપ્તા','கட்டணத் தவணைகள்'),(7733,'enquiry_for','Enquiry For','অনুসন্ধানের বিষয়','पूछताछ किसके लिए','चौकशी कशासाठी','ವಿಚಾರಣೆ ಯಾವುದಕ್ಕೆ','પૂછપરછ શેના માટે','விசாரணை எதற்காக'),(7727,'edit_course','Edit Course','কোর্স সম্পাদনা করুন','कोर्स संपादित करें','कोर्स संपादित करा','ಕೋರ್ಸ್ ಸಂಪಾದಿಸಿ','કોર્સ સંપાદિત કરો','பாடநெறியைத் திருத்து'),(7718,'contact','Contact','যোগাযোগ','संपर्क','संपर्क','ಸಂಪರ್ಕ','સંપર્ક','தொடர்பு'),(7890,'all_my_exams','All My Exams','আমার সব পরীক্ষা','मेरी सभी परीक्षाएँ','माझ्या सर्व परीक्षा','ನನ್ನ ಎಲ್ಲಾ ಪರೀಕ್ಷೆಗಳು','મારી બધી પરીક્ષાઓ','எனது அனைத்து தேர்வுகள்'),(7891,'recent_results','Recent Results','সাম্প্রতিক ফলাফল','हाल के परिणाम','अलीकडील निकाल','ಇತ್ತೀಚಿನ ಫಲಿತಾಂಶಗಳು','તાજેતરના પરિણામો','சமீபத்திய முடிவுகள்'),(7892,'no_results_published_yet','No Results Published Yet','এখনও কোনো ফলাফল প্রকাশিত হয়নি','अभी तक कोई परिणाम प्रकाशित नहीं','अजून कोणताही निकाल प्रकाशित नाही','ಇನ್ನೂ ಫಲಿತಾಂಶಗಳನ್ನು ಪ್ರಕಟಿಸಿಲ್ಲ','હજી કોઈ પરિણામ પ્રકાશિત થયું નથી','இன்னும் முடிவுகள் வெளியிடப்படவில்லை'),(7893,'start_exam_confirm','Start the exam now? The timer starts as soon as you begin.','এখনই পরীক্ষা শুরু করবেন? আপনি শুরু করার সঙ্গে সঙ্গেই টাইমার চালু হবে।','परीक्षा अभी शुरू करें? शुरू करते ही टाइमर चालू हो जाएगा।','परीक्षा आता सुरू करायची? तुम्ही सुरू करताच टायमर सुरू होईल.','ಈಗ ಪರೀಕ್ಷೆ ಆರಂಭಿಸಬೇಕೇ? ನೀವು ಆರಂಭಿಸಿದ ತಕ್ಷಣ ಟೈಮರ್ ಪ್ರಾರಂಭವಾಗುತ್ತದೆ.','પરીક્ષા હમણાં શરૂ કરવી? તમે શરૂ કરશો કે તરત ટાઇમર ચાલુ થશે.','தேர்வை இப்போது தொடங்கவா? நீங்கள் தொடங்கியவுடன் டைமர் ஓடத் தொடங்கும்.'),(7894,'mark','Mark','নম্বর','अंक','गुण','ಅಂಕ','ગુણ','மதிப்பெண்'),(7895,'clear_answer','Clear Answer','উত্তর মুছুন','उत्तर मिटाएँ','उत्तर पुसा','ಉತ್ತರ ಅಳಿಸಿ','જવાબ સાફ કરો','பதிலை அழி'),(7896,'submit_exam','Submit Exam','পরীক্ষা জমা দিন','परीक्षा सबमिट करें','परीक्षा सबमिट करा','ಪರೀಕ್ಷೆ ಸಲ್ಲಿಸಿ','પરીક્ષા સબમિટ કરો','தேர்வைச் சமர்ப்பி'),(7897,'time_left','Time Left','বাকি সময়','शेष समय','उरलेला वेळ','ಉಳಿದ ಸಮಯ','બાકી સમય','மீதமுள்ள நேரம்'),(7898,'answered','Answered','উত্তর দেওয়া হয়েছে','उत्तर दिया','उत्तर दिले','ಉತ್ತರಿಸಲಾಗಿದೆ','જવાબ આપ્યો','பதிலளிக்கப்பட்டது'),(7899,'connection_problem_answers_will_be_sent_on_submit','Connection problem. Your answers will be sent when you submit.','সংযোগে সমস্যা। জমা দেওয়ার সময় আপনার উত্তর পাঠানো হবে।','कनेक्शन में समस्या। सबमिट करने पर आपके उत्तर भेज दिए जाएँगे।','कनेक्शनमध्ये अडचण. तुम्ही सबमिट कराल तेव्हा तुमची उत्तरे पाठवली जातील.','ಸಂಪರ್ಕ ಸಮಸ್ಯೆ. ನೀವು ಸಲ್ಲಿಸಿದಾಗ ನಿಮ್ಮ ಉತ್ತರಗಳನ್ನು ಕಳುಹಿಸಲಾಗುತ್ತದೆ.','કનેક્શનની સમસ્યા. તમે સબમિટ કરશો ત્યારે તમારા જવાબો મોકલાશે.','இணைப்பு சிக்கல். நீங்கள் சமர்ப்பிக்கும்போது உங்கள் பதில்கள் அனுப்பப்படும்.'),(7900,'saved','Saved','সংরক্ষিত','सहेजा गया','जतन केले','ಉಳಿಸಲಾಗಿದೆ','સાચવ્યું','சேமிக்கப்பட்டது'),(7901,'saving','Saving','সংরক্ষণ হচ্ছে','सहेज रहे हैं','जतन करत आहे','ಉಳಿಸಲಾಗುತ್ತಿದೆ','સાચવી રહ્યા છીએ','சேமிக்கிறது'),(7902,'questions_are_unanswered','questions are unanswered','টি প্রশ্নের উত্তর দেওয়া হয়নি','प्रश्नों के उत्तर नहीं दिए गए','प्रश्नांची उत्तरे दिलेली नाहीत','ಪ್ರಶ್ನೆಗಳಿಗೆ ಉತ್ತರಿಸಿಲ್ಲ','પ્રશ્નોના જવાબ બાકી છે','கேள்விகளுக்கு பதிலளிக்கப்படவில்லை'),(7903,'submit_exam_confirm','Submit your answers? You cannot change them after submitting.','আপনার উত্তর জমা দেবেন? জমা দেওয়ার পর আর পরিবর্তন করতে পারবেন না।','अपने उत्तर सबमिट करें? सबमिट करने के बाद आप उन्हें बदल नहीं सकते।','तुमची उत्तरे सबमिट करायची? सबमिट केल्यानंतर तुम्ही ती बदलू शकत नाही.','ನಿಮ್ಮ ಉತ್ತರಗಳನ್ನು ಸಲ್ಲಿಸಬೇಕೇ? ಸಲ್ಲಿಸಿದ ನಂತರ ಬದಲಾಯಿಸಲು ಸಾಧ್ಯವಿಲ್ಲ.','તમારા જવાબો સબમિટ કરવા? સબમિટ કર્યા પછી બદલી શકાશે નહીં.','உங்கள் பதில்களைச் சமர்ப்பிக்கவா? சமர்ப்பித்த பிறகு மாற்ற முடியாது.'),(7904,'exam_submitted_successfully','Exam Submitted Successfully','পরীক্ষা সফলভাবে জমা হয়েছে','परीक्षा सफलतापूर्वक सबमिट हो गई','परीक्षा यशस्वीरित्या सबमिट झाली','ಪರೀಕ್ಷೆಯನ್ನು ಯಶಸ್ವಿಯಾಗಿ ಸಲ್ಲಿಸಲಾಗಿದೆ','પરીક્ષા સફળતાપૂર્વક સબમિટ થઈ','தேர்வு வெற்றிகரமாக சமர்ப்பிக்கப்பட்டது'),(7905,'your_result_will_be_shown_once_published','Your result will be shown once it is published','প্রকাশিত হলে আপনার ফলাফল দেখানো হবে','आपका परिणाम प्रकाशित होने के बाद दिखाया जाएगा','तुमचा निकाल प्रकाशित झाल्यावर दिसेल','ನಿಮ್ಮ ಫಲಿತಾಂಶ ಪ್ರಕಟವಾದ ನಂತರ ತೋರಿಸಲಾಗುತ್ತದೆ','પરિણામ પ્રકાશિત થયા પછી તમારું પરિણામ બતાવાશે','உங்கள் முடிவு வெளியிடப்பட்டவுடன் காட்டப்படும்'),(7906,'you_have_already_submitted_this_exam','You Have Already Submitted This Exam','আপনি ইতিমধ্যে এই পরীক্ষা জমা দিয়েছেন','आप यह परीक्षा पहले ही सबमिट कर चुके हैं','तुम्ही ही परीक्षा आधीच सबमिट केली आहे','ನೀವು ಈಗಾಗಲೇ ಈ ಪರೀಕ್ಷೆಯನ್ನು ಸಲ್ಲಿಸಿದ್ದೀರಿ','તમે આ પરીક્ષા પહેલેથી સબમિટ કરી દીધી છે','நீங்கள் ஏற்கனவே இந்தத் தேர்வைச் சமர்ப்பித்துவிட்டீர்கள்'),(7907,'result_not_published_yet','Result Not Published Yet','ফলাফল এখনও প্রকাশিত হয়নি','परिणाम अभी प्रकाशित नहीं हुआ','निकाल अजून प्रकाशित झालेला नाही','ಫಲಿತಾಂಶ ಇನ್ನೂ ಪ್ರಕಟವಾಗಿಲ್ಲ','પરિણામ હજી પ્રકાશિત થયું નથી','முடிவு இன்னும் வெளியிடப்படவில்லை'),(7908,'back','Back','ফিরে যান','वापस','मागे','ಹಿಂದೆ','પાછળ','பின்செல்'),(7909,'auto_marked_hint','Multiple-choice answers are marked automatically. Change the marks only if a question needs special treatment.','বহুনির্বাচনী উত্তর স্বয়ংক্রিয়ভাবে মূল্যায়ন হয়। কোনো প্রশ্নে বিশেষ বিবেচনা প্রয়োজন হলেই নম্বর পরিবর্তন করুন।','बहुविकल्पीय उत्तर स्वचालित रूप से जाँचे जाते हैं। अंक तभी बदलें जब किसी प्रश्न के लिए विशेष विचार आवश्यक हो।','बहुपर्यायी उत्तरांना आपोआप गुण दिले जातात. प्रश्नासाठी विशेष विचार आवश्यक असेल तरच गुण बदला.','ಬಹು-ಆಯ್ಕೆ ಉತ್ತರಗಳಿಗೆ ಸ್ವಯಂಚಾಲಿತವಾಗಿ ಅಂಕ ನೀಡಲಾಗುತ್ತದೆ. ಪ್ರಶ್ನೆಗೆ ವಿಶೇಷ ಪರಿಗಣನೆ ಬೇಕಾದರೆ ಮಾತ್ರ ಅಂಕಗಳನ್ನು ಬದಲಾಯಿಸಿ.','બહુવિકલ્પી જવાબો આપોઆપ ચકાસાય છે. પ્રશ્નને વિશેષ ગણતરીની જરૂર હોય તો જ ગુણ બદલો.','பல தேர்வு விடைகள் தானாக மதிப்பிடப்படும். ஒரு கேள்விக்கு சிறப்பு கவனம் தேவைப்பட்டால் மட்டும் மதிப்பெண்களை மாற்றவும்.'),(7910,'student_answer','Student Answer','শিক্ষার্থীর উত্তর','छात्र का उत्तर','विद्यार्थ्याचे उत्तर','ವಿದ್ಯಾರ್ಥಿಯ ಉತ್ತರ','વિદ્યાર્થીનો જવાબ','மாணவரின் பதில்'),(7911,'marks_awarded','Marks Awarded','প্রদত্ত নম্বর','दिए गए अंक','दिलेले गुण','ನೀಡಿದ ಅಂಕಗಳು','આપેલા ગુણ','வழங்கப்பட்ட மதிப்பெண்கள்'),(7912,'marks_saved','Marks Saved','নম্বর সংরক্ষিত হয়েছে','अंक सहेजे गए','गुण जतन केले','ಅಂಕಗಳನ್ನು ಉಳಿಸಲಾಗಿದೆ','ગુણ સાચવાયા','மதிப்பெண்கள் சேமிக்கப்பட்டன'),(7913,'preview_result_email','Preview Result Email','ফলাফল ইমেল প্রিভিউ','परिणाम ईमेल पूर्वावलोकन','निकाल ईमेल पूर्वावलोकन','ಫಲಿತಾಂಶ ಇಮೇಲ್ ಪೂರ್ವವೀಕ್ಷಣೆ','પરિણામ ઈમેલનું પૂર્વાવલોકન','முடிவு மின்னஞ்சல் முன்னோட்டம்'),(8011,'upcoming','Upcoming','আসন্ন','आगामी','आगामी','ಮುಂಬರುವ','આગામી','வரவிருக்கும்'),(8004,'test_email_sent_to','Test Email Sent To','টেস্ট ইমেল পাঠানো হয়েছে','टेस्ट ईमेल भेजा गया','चाचणी ईमेल पाठवला','ಪರೀಕ್ಷಾ ಇಮೇಲ್ ಕಳುಹಿಸಲಾಗಿದೆ:','ટેસ્ટ ઈમેલ મોકલાયો','சோதனை மின்னஞ்சல் அனுப்பப்பட்ட முகவரி'),(8000,'select_class_and_subject','Select Class And Subject','শ্রেণি ও বিষয় নির্বাচন করুন','कक्षा और विषय चुनें','वर्ग आणि विषय निवडा','ತರಗತಿ ಮತ್ತು ವಿಷಯ ಆಯ್ಕೆಮಾಡಿ','વર્ગ અને વિષય પસંદ કરો','வகுப்பு மற்றும் பாடத்தைத் தேர்ந்தெடுக்கவும்'),(7989,'question_not_found','Question Not Found','প্রশ্ন পাওয়া যায়নি','प्रश्न नहीं मिला','प्रश्न आढळला नाही','ಪ್ರಶ್ನೆ ಕಂಡುಬಂದಿಲ್ಲ','પ્રશ્ન મળ્યો નથી','கேள்வி கிடைக்கவில்லை'),(7975,'no_exams_yet','No Exams Yet','এখনও কোনো পরীক্ষা নেই','अभी तक कोई परीक्षा नहीं','अजून कोणतीही परीक्षा नाही','ಇನ್ನೂ ಪರೀಕ್ಷೆಗಳಿಲ್ಲ','હજી કોઈ પરીક્ષા નથી','இன்னும் தேர்வுகள் இல்லை'),(7971,'no_active_students_in_this_class','No Active Students In This Class','এই শ্রেণিতে কোনো সক্রিয় শিক্ষার্থী নেই','इस कक्षा में कोई सक्रिय छात्र नहीं','या वर्गात कोणतेही सक्रिय विद्यार्थी नाहीत','ಈ ತರಗತಿಯಲ್ಲಿ ಸಕ್ರಿಯ ವಿದ್ಯಾರ್ಥಿಗಳಿಲ್ಲ','આ વર્ગમાં કોઈ સક્રિય વિદ્યાર્થી નથી','இந்த வகுப்பில் செயலில் உள்ள மாணவர்கள் இல்லை'),(7964,'exam_not_in_progress','Exam Not In Progress','পরীক্ষা চলছে না','परीक्षा जारी नहीं है','परीक्षा सुरू नाही','ಪರೀಕ್ಷೆ ನಡೆಯುತ್ತಿಲ್ಲ','પરીક્ષા ચાલુ નથી','தேர்வு நடைபெறவில்லை'),(7951,'edit_grade','Edit Grade','গ্রেড সম্পাদনা','ग्रेड संपादित करें','श्रेणी संपादित करा','ಗ್ರೇಡ್ ಸಂಪಾದಿಸಿ','ગ્રેડ સંપાદિત કરો','தரத்தைத் திருத்து'),(7772,'year','Year','বছর','वर्ष','वर्ष','ವರ್ಷ','વર્ષ','ஆண்டு'),(7767,'unknown_export_format','Unknown export format','অজানা রপ্তানি ফরম্যাট','अज्ञात निर्यात प्रारूप','अज्ञात निर्यात स्वरूप','ಅಜ್ಞಾತ ರಫ್ತು ಸ್ವರೂಪ','અજ્ઞાત નિકાસ ફોર્મેટ','தெரியாத ஏற்றுமதி வடிவம்'),(7759,'save_status','Save Status','অবস্থা সংরক্ষণ করুন','स्थिति सहेजें','स्थिती जतन करा','ಸ್ಥಿತಿ ಉಳಿಸಿ','સ્થિતિ સાચવો','நிலையைச் சேமி'),(7752,'number_of_installments','Number Of Installments','কিস্তির সংখ্যা','किश्तों की संख्या','हप्त्यांची संख्या','ಕಂತುಗಳ ಸಂಖ್ಯೆ','હપ્તાની સંખ્યા','தவணைகளின் எண்ணிக்கை'),(7744,'installments_edit_hint','Edit each installment\'s amount and due date. The total should match the course fee.','প্রতিটি কিস্তির পরিমাণ ও নির্ধারিত তারিখ সম্পাদনা করুন। মোট পরিমাণ কোর্স ফি-র সমান হওয়া উচিত।','हर किश्त की राशि और देय तिथि बदलें। कुल राशि कोर्स शुल्क के बराबर होनी चाहिए।','प्रत्येक हप्त्याची रक्कम आणि देय तारीख बदला. एकूण रक्कम कोर्स शुल्काइतकी असावी.','ಪ್ರತಿ ಕಂತಿನ ಮೊತ್ತ ಮತ್ತು ಬಾಕಿ ದಿನಾಂಕವನ್ನು ಸಂಪಾದಿಸಿ. ಒಟ್ಟು ಮೊತ್ತ ಕೋರ್ಸ್ ಶುಲ್ಕಕ್ಕೆ ಸಮನಾಗಿರಬೇಕು.','દરેક હપ્તાની રકમ અને નિયત તારીખ બદલો. કુલ રકમ કોર્સ ફી જેટલી હોવી જોઈએ.','ஒவ்வொரு தவணையின் தொகையையும் செலுத்த வேண்டிய தேதியையும் திருத்தவும். மொத்தம் பாடநெறிக் கட்டணத்துக்குச் சமமாக இருக்க வேண்டும்.'),(7732,'enquiry_follow_up','Enquiry Follow Up','অনুসন্ধান ফলো-আপ','पूछताछ फॉलो-अप','चौकशी पाठपुरावा','ವಿಚಾರಣೆ ಅನುಸರಣೆ','પૂછપરછ ફોલો-અપ','விசாரணை பின்தொடர்தல்'),(7723,'created_by','Created By','তৈরি করেছেন','द्वारा बनाया गया','यांनी तयार केले','ರಚಿಸಿದವರು','બનાવનાર','உருவாக்கியவர்'),(7717,'choose_file','Choose File','ফাইল বেছে নিন','फ़ाइल चुनें','फाइल निवडा','ಫೈಲ್ ಆಯ್ಕೆಮಾಡಿ','ફાઇલ પસંદ કરો','கோப்பைத் தேர்ந்தெடு'),(7864,'students_with_marks','Students With Marks','নম্বরসহ শিক্ষার্থী','अंक वाले छात्र','गुण असलेले विद्यार्थी','ಅಂಕಗಳಿರುವ ವಿದ್ಯಾರ್ಥಿಗಳು','ગુણ સાથેના વિદ્યાર્થીઓ','மதிப்பெண் உள்ள மாணவர்கள்'),(7865,'grade','Grade','গ্রেড','ग्रेड','श्रेणी','ಗ್ರೇಡ್','ગ્રેડ','தரம்'),(7866,'tabulation_hint','Rank uses the overall percentage. Students with no marks entered are not ranked.','র‍্যাংক সামগ্রিক শতাংশের উপর ভিত্তি করে হয়। যাদের নম্বর এন্ট্রি হয়নি তাদের র‍্যাংক দেওয়া হয় না।','रैंक कुल प्रतिशत के आधार पर तय होती है। जिन छात्रों के अंक दर्ज नहीं हैं उन्हें रैंक नहीं दी जाती।','क्रमांक एकूण टक्केवारीवर आधारित असतो. ज्यांचे गुण भरलेले नाहीत त्यांना क्रमांक दिला जात नाही.','ಶ್ರೇಣಿಯನ್ನು ಒಟ್ಟಾರೆ ಶೇಕಡಾವಾರಿನಿಂದ ನಿರ್ಧರಿಸಲಾಗುತ್ತದೆ. ಅಂಕಗಳನ್ನು ನಮೂದಿಸದ ವಿದ್ಯಾರ್ಥಿಗಳಿಗೆ ಶ್ರೇಣಿ ನೀಡಲಾಗುವುದಿಲ್ಲ.','ક્રમ કુલ ટકાવારી પર આધારિત છે. જેના ગુણ દાખલ નથી તે વિદ્યાર્થીઓને ક્રમ અપાતો નથી.','தரவரிசை மொத்த சதவீதத்தைப் பயன்படுத்துகிறது. மதிப்பெண் உள்ளிடப்படாத மாணவர்களுக்கு தரவரிசை வழங்கப்படாது.'),(7867,'email_preview','Email Preview','ইমেল প্রিভিউ','ईमेल पूर्वावलोकन','ईमेल पूर्वावलोकन','ಇಮೇಲ್ ಪೂರ್ವವೀಕ್ಷಣೆ','ઈમેલ પૂર્વાવલોકન','மின்னஞ்சல் முன்னோட்டம்'),(7868,'preview_for','Preview For','যার জন্য প্রিভিউ','इसके लिए पूर्वावलोकन','यांच्यासाठी पूर्वावलोकन','ಇವರಿಗಾಗಿ ಪೂರ್ವವೀಕ್ಷಣೆ','માટે પૂર્વાવલોકન','முன்னோட்டம் யாருக்கு'),(7869,'each_student_gets_their_own_details','each student gets their own details','প্রতিটি শিক্ষার্থী নিজের তথ্য পাবে','प्रत्येक छात्र को उसका अपना विवरण मिलता है','प्रत्येक विद्यार्थ्याला त्याचा स्वतःचा तपशील मिळतो','ಪ್ರತಿ ವಿದ್ಯಾರ್ಥಿಗೆ ಅವರದೇ ವಿವರಗಳು ಸಿಗುತ್ತವೆ','દરેક વિદ્યાર્થીને તેની પોતાની વિગતો મળે છે','ஒவ்வொரு மாணவருக்கும் அவரவர் விவரங்கள் கிடைக்கும்'),(7870,'recipients','Recipients','প্রাপকগণ','प्राप्तकर्ता','प्राप्तकर्ते','ಸ್ವೀಕರಿಸುವವರು','પ્રાપ્તકર્તાઓ','பெறுநர்கள்'),(7871,'email_addresses','Email Addresses','ইমেল ঠিকানা','ईमेल पते','ईमेल पत्ते','ಇಮೇಲ್ ವಿಳಾಸಗಳು','ઈમેલ સરનામાં','மின்னஞ்சல் முகவரிகள்'),(7872,'no_recipients_yet_nothing_will_be_sent','There are no recipients yet, so nothing will be sent.','এখনও কোনো প্রাপক নেই, তাই কিছু পাঠানো হবে না।','अभी कोई प्राप्तकर्ता नहीं है, इसलिए कुछ नहीं भेजा जाएगा।','अजून कोणतेही प्राप्तकर्ते नाहीत, त्यामुळे काहीही पाठवले जाणार नाही.','ಇನ್ನೂ ಸ್ವೀಕರಿಸುವವರಿಲ್ಲ, ಆದ್ದರಿಂದ ಏನನ್ನೂ ಕಳುಹಿಸಲಾಗುವುದಿಲ್ಲ.','હજી કોઈ પ્રાપ્તકર્તા નથી, તેથી કંઈ મોકલાશે નહીં.','இன்னும் பெறுநர்கள் இல்லை, எனவே எதுவும் அனுப்பப்படாது.'),(7873,'exam_title_is_required','Exam Title Is Required','পরীক্ষার শিরোনাম আবশ্যক','परीक्षा शीर्षक आवश्यक है','परीक्षेचे शीर्षक आवश्यक आहे','ಪರೀಕ್ಷೆಯ ಶೀರ್ಷಿಕೆ ಅಗತ್ಯ','પરીક્ષાનું શીર્ષક જરૂરી છે','தேர்வு தலைப்பு தேவை'),(7874,'subject_does_not_belong_to_class','The subject does not belong to the selected class','বিষয়টি নির্বাচিত শ্রেণির অন্তর্গত নয়','विषय चयनित कक्षा का नहीं है','विषय निवडलेल्या वर्गाचा नाही','ವಿಷಯವು ಆಯ್ಕೆ ಮಾಡಿದ ತರಗತಿಗೆ ಸೇರಿಲ್ಲ','વિષય પસંદ કરેલા વર્ગનો નથી','இந்தப் பாடம் தேர்ந்தெடுக்கப்பட்ட வகுப்புக்கு உரியது அல்ல'),(7875,'exam_created_now_enter_the_questions','Exam created. Now enter the questions.','পরীক্ষা তৈরি হয়েছে। এখন প্রশ্নগুলি লিখুন।','परीक्षा बन गई। अब प्रश्न दर्ज करें।','परीक्षा तयार झाली. आता प्रश्न भरा.','ಪರೀಕ್ಷೆ ರಚಿಸಲಾಗಿದೆ. ಈಗ ಪ್ರಶ್ನೆಗಳನ್ನು ನಮೂದಿಸಿ.','પરીક્ષા બની ગઈ. હવે પ્રશ્નો દાખલ કરો.','தேர்வு உருவாக்கப்பட்டது. இப்போது கேள்விகளை உள்ளிடவும்.'),(7876,'exam_cannot_be_published_yet','The exam cannot be published yet','পরীক্ষাটি এখনও প্রকাশ করা যাবে না','परीक्षा अभी प्रकाशित नहीं की जा सकती','परीक्षा अजून प्रकाशित करता येत नाही','ಪರೀಕ್ಷೆಯನ್ನು ಇನ್ನೂ ಪ್ರಕಟಿಸಲು ಸಾಧ್ಯವಿಲ್ಲ','પરીક્ષા હજી પ્રકાશિત કરી શકાતી નથી','தேர்வை இன்னும் வெளியிட முடியாது'),(7877,'fix_these_before_publishing','Fix these before publishing','প্রকাশের আগে এগুলি ঠিক করুন','प्रकाशित करने से पहले इन्हें ठीक करें','प्रकाशित करण्यापूर्वी हे दुरुस्त करा','ಪ್ರಕಟಿಸುವ ಮೊದಲು ಇವುಗಳನ್ನು ಸರಿಪಡಿಸಿ','પ્રકાશિત કરતા પહેલા આ સુધારો','வெளியிடுவதற்கு முன் இவற்றைச் சரிசெய்யவும்'),(7878,'correct_answer_must_be_a_filled_option','The correct answer must be one of the filled options','সঠিক উত্তর অবশ্যই পূরণ করা বিকল্পগুলির একটি হতে হবে','सही उत्तर भरे हुए विकल्पों में से एक होना चाहिए','बरोबर उत्तर भरलेल्या पर्यायांपैकीच एक असले पाहिजे','ಸರಿಯಾದ ಉತ್ತರವು ಭರ್ತಿ ಮಾಡಿದ ಆಯ್ಕೆಗಳಲ್ಲಿ ಒಂದಾಗಿರಬೇಕು','સાચો જવાબ ભરેલા વિકલ્પોમાંથી જ એક હોવો જોઈએ','சரியான பதில் நிரப்பப்பட்ட விருப்பங்களில் ஒன்றாக இருக்க வேண்டும்'),(7879,'question_saved','Question Saved','প্রশ্ন সংরক্ষিত হয়েছে','प्रश्न सहेजा गया','प्रश्न जतन केला','ಪ್ರಶ್ನೆ ಉಳಿಸಲಾಗಿದೆ','પ્રશ્ન સાચવાયો','கேள்வி சேமிக்கப்பட்டது'),(7880,'exam_published','Exam Published','পরীক্ষা প্রকাশিত হয়েছে','परीक्षा प्रकाशित हो गई','परीक्षा प्रकाशित केली','ಪರೀಕ್ಷೆ ಪ್ರಕಟಿಸಲಾಗಿದೆ','પરીક્ષા પ્રકાશિત થઈ','தேர்வு வெளியிடப்பட்டது'),(7881,'you_can_now_assign_it_to_students','You Can Now Assign It To Students','আপনি এখন এটি শিক্ষার্থীদের নির্ধারণ করতে পারেন','अब आप इसे छात्रों को असाइन कर सकते हैं','आता तुम्ही ती विद्यार्थ्यांना नेमू शकता','ಈಗ ನೀವು ಇದನ್ನು ವಿದ್ಯಾರ್ಥಿಗಳಿಗೆ ನಿಯೋಜಿಸಬಹುದು','હવે તમે તેને વિદ્યાર્થીઓને સોંપી શકો છો','இப்போது இதை மாணவர்களுக்கு ஒதுக்கலாம்'),(7882,'students_assigned','Students Assigned','নির্ধারিত শিক্ষার্থী','असाइन किए गए छात्र','नेमलेले विद्यार्थी','ನಿಯೋಜಿತ ವಿದ್ಯಾರ್ಥಿಗಳು','સોંપાયેલા વિદ્યાર્થીઓ','ஒதுக்கப்பட்ட மாணவர்கள்'),(7883,'student_dashboard','Student Dashboard','শিক্ষার্থী ড্যাশবোর্ড','छात्र डैशबोर्ड','विद्यार्थी डॅशबोर्ड','ವಿದ್ಯಾರ್ಥಿ ಡ್ಯಾಶ್‌ಬೋರ್ಡ್','વિદ્યાર્થી ડૅશબોર્ડ','மாணவர் முகப்புப்பக்கம்'),(7884,'my_online_exams','My Online Exams','আমার অনলাইন পরীক্ষা','मेरी ऑनलाइन परीक्षाएँ','माझ्या ऑनलाइन परीक्षा','ನನ್ನ ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆಗಳು','મારી ઑનલાઇન પરીક્ષાઓ','எனது ஆன்லைன் தேர்வுகள்'),(7885,'my_marks','My Marks','আমার নম্বর','मेरे अंक','माझे गुण','ನನ್ನ ಅಂಕಗಳು','મારા ગુણ','எனது மதிப்பெண்கள்'),(7886,'my_profile','My Profile','আমার প্রোফাইল','मेरी प्रोफ़ाइल','माझे प्रोफाइल','ನನ್ನ ಪ್ರೊಫೈಲ್','મારી પ્રોફાઇલ','எனது சுயவிவரம்'),(7887,'welcome','Welcome','স্বাগতম','स्वागत है','स्वागत','ಸ್ವಾಗತ','સ્વાગત છે','வரவேற்கிறோம்'),(7888,'upcoming_online_exams','Upcoming Online Exams','আসন্ন অনলাইন পরীক্ষা','आगामी ऑनलाइन परीक्षाएँ','आगामी ऑनलाइन परीक्षा','ಮುಂಬರುವ ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆಗಳು','આગામી ઑનલાઇન પરીક્ષાઓ','வரவிருக்கும் ஆன்லைன் தேர்வுகள்'),(7889,'start_exam','Start Exam','পরীক্ষা শুরু করুন','परीक्षा शुरू करें','परीक्षा सुरू करा','ಪರೀಕ್ಷೆ ಆರಂಭಿಸಿ','પરીક્ષા શરૂ કરો','தேர்வைத் தொடங்கு'),(8008,'this_student_has_not_submitted_yet','This Student Has Not Submitted Yet','এই শিক্ষার্থী এখনও জমা দেয়নি','इस छात्र ने अभी सबमिट नहीं किया है','या विद्यार्थ्याने अजून सबमिट केलेले नाही','ಈ ವಿದ್ಯಾರ್ಥಿ ಇನ್ನೂ ಸಲ್ಲಿಸಿಲ್ಲ','આ વિદ્યાર્થીએ હજી સબમિટ કર્યું નથી','இந்த மாணவர் இன்னும் சமர்ப்பிக்கவில்லை'),(8003,'test_email_failed','Test Email Failed','টেস্ট ইমেল ব্যর্থ হয়েছে','टेस्ट ईमेल विफल','चाचणी ईमेल अयशस्वी','ಪರೀಕ್ಷಾ ಇಮೇಲ್ ವಿಫಲವಾಗಿದೆ','ટેસ્ટ ઈમેલ નિષ્ફળ','சோதனை மின்னஞ்சல் தோல்வி'),(8002,'student_removed_from_exam','Student Removed From Exam','শিক্ষার্থীকে পরীক্ষা থেকে সরানো হয়েছে','छात्र को परीक्षा से हटाया गया','विद्यार्थी परीक्षेतून काढला','ವಿದ್ಯಾರ್ಥಿಯನ್ನು ಪರೀಕ್ಷೆಯಿಂದ ತೆಗೆದುಹಾಕಲಾಗಿದೆ','વિદ્યાર્થીને પરીક્ષામાંથી દૂર કર્યો','மாணவர் தேர்விலிருந்து நீக்கப்பட்டார்'),(7999,'select_at_least_one_student','Select At Least One Student','কমপক্ষে একজন শিক্ষার্থী নির্বাচন করুন','कम से कम एक छात्र चुनें','किमान एक विद्यार्थी निवडा','ಕನಿಷ್ಠ ಒಬ್ಬ ವಿದ್ಯಾರ್ಥಿಯನ್ನು ಆಯ್ಕೆಮಾಡಿ','ઓછામાં ઓછો એક વિદ્યાર્થી પસંદ કરો','குறைந்தது ஒரு மாணவரைத் தேர்ந்தெடுக்கவும்'),(7996,'results_hidden_from_students','Results Hidden From Students','শিক্ষার্থীদের থেকে ফলাফল লুকানো আছে','छात्रों से परिणाम छिपे हैं','निकाल विद्यार्थ्यांपासून लपवले आहेत','ವಿದ್ಯಾರ್ಥಿಗಳಿಂದ ಫಲಿತಾಂಶ ಮರೆಮಾಡಲಾಗಿದೆ','વિદ્યાર્થીઓથી પરિણામ છુપાવ્યા','மாணவர்களிடமிருந்து முடிவுகள் மறைக்கப்பட்டுள்ளன'),(7988,'question_deleted','Question Deleted','প্রশ্ন মুছে ফেলা হয়েছে','प्रश्न हटाया गया','प्रश्न हटवला','ಪ್ರಶ್ನೆ ಅಳಿಸಲಾಗಿದೆ','પ્રશ્ન કાઢી નાખ્યો','கேள்வி நீக்கப்பட்டது'),(7977,'no_submitted_attempts_to_publish','No Submitted Attempts To Publish','প্রকাশ করার মতো কোনো জমা দেওয়া পরীক্ষা নেই','प्रकाशित करने के लिए कोई सबमिट प्रयास नहीं','प्रकाशित करण्यासाठी कोणतेही सबमिट केलेले प्रयत्न नाहीत','ಪ್ರಕಟಿಸಲು ಸಲ್ಲಿಸಿದ ಪ್ರಯತ್ನಗಳಿಲ್ಲ','પ્રકાશિત કરવા માટે કોઈ સબમિટ કરેલ પ્રયાસ નથી','வெளியிட சமர்ப்பிக்கப்பட்ட முயற்சிகள் இல்லை'),(7967,'grade_range_must_be_0_to_100','Grade range must be between 0 and 100','গ্রেডের সীমা ০ থেকে ১০০ এর মধ্যে হতে হবে','ग्रेड सीमा 0 से 100 के बीच होनी चाहिए','श्रेणीची मर्यादा 0 ते 100 दरम्यान असावी','ಗ್ರೇಡ್ ವ್ಯಾಪ್ತಿ 0 ಮತ್ತು 100 ರ ನಡುವೆ ಇರಬೇಕು','ગ્રેડની શ્રેણી 0 થી 100 વચ્ચે હોવી જોઈએ','தர வரம்பு 0 முதல் 100 வரை இருக்க வேண்டும்'),(7963,'exam_not_found','Exam Not Found','পরীক্ষা পাওয়া যায়নি','परीक्षा नहीं मिली','परीक्षा आढळली नाही','ಪರೀಕ್ಷೆ ಕಂಡುಬಂದಿಲ್ಲ','પરીક્ષા મળી નથી','தேர்வு கிடைக்கவில்லை'),(7771,'view_fees','View Fees','ফি দেখুন','शुल्क देखें','शुल्क पहा','ಶುಲ್ಕ ವೀಕ್ಷಿಸಿ','ફી જુઓ','கட்டணத்தைக் காண்க'),(7766,'total_course_fees','Total Course Fees','মোট কোর্স ফি','कुल कोर्स शुल्क','एकूण कोर्स शुल्क','ಒಟ್ಟು ಕೋರ್ಸ್ ಶುಲ್ಕ','કુલ કોર્સ ફી','மொத்த பாடநெறிக் கட்டணம்'),(7758,'remark','Remark','মন্তব্য','टिप्पणी','शेरा','ಟಿಪ್ಪಣಿ','ટિપ્પણી','குறிப்பு'),(7755,'phrase_already_exists','This phrase already exists','এই বাক্যাংশ ইতিমধ্যে আছে','यह वाक्यांश पहले से मौजूद है','हे वाक्य आधीच अस्तित्वात आहे','ಈ ಪದಗುಚ್ಛ ಈಗಾಗಲೇ ಇದೆ','આ શબ્દસમૂહ પહેલેથી છે','இந்தச் சொற்றொடர் ஏற்கனவே உள்ளது'),(7747,'mismatch_with_course_fee','Does not match course fee','কোর্স ফি-র সাথে মিলছে না','कोर्स शुल्क से मेल नहीं खाता','कोर्स शुल्काशी जुळत नाही','ಕೋರ್ಸ್ ಶುಲ್ಕಕ್ಕೆ ಹೊಂದುವುದಿಲ್ಲ','કોર્સ ફી સાથે મેળ ખાતું નથી','பாடநெறிக் கட்டணத்துடன் பொருந்தவில்லை'),(7743,'installments','Installments','কিস্তি','किश्तें','हप्ते','ಕಂತುಗಳು','હપ્તા','தவணைகள்'),(7737,'export_not_configured_for_this_list','Export is not set up for this list','এই তালিকার জন্য রপ্তানি সেট করা নেই','इस सूची के लिए निर्यात सेट नहीं है','या यादीसाठी निर्यात सेट केलेली नाही','ಈ ಪಟ್ಟಿಗೆ ರಫ್ತು ಹೊಂದಿಸಿಲ್ಲ','આ યાદી માટે નિકાસ સેટ નથી','இந்தப் பட்டியலுக்கு ஏற்றுமதி அமைக்கப்படவில்லை'),(7731,'enquiry_date','Enquiry Date','অনুসন্ধানের তারিখ','पूछताछ तिथि','चौकशी तारीख','ವಿಚಾರಣೆ ದಿನಾಂಕ','પૂછપરછ તારીખ','விசாரணை தேதி'),(7726,'due_date','Due Date','নির্ধারিত তারিখ','देय तिथि','देय तारीख','ಬಾಕಿ ದಿನಾಂಕ','નિયત તારીખ','செலுத்த வேண்டிய தேதி'),(7722,'course_name','Course Name','কোর্সের নাম','कोर्स का नाम','कोर्सचे नाव','ಕೋರ್ಸ್ ಹೆಸರು','કોર્સનું નામ','பாடநெறி பெயர்'),(7715,'back_to_list','Back To List','তালিকায় ফিরুন','सूची पर वापस','यादीकडे परत','ಪಟ್ಟಿಗೆ ಹಿಂತಿರುಗಿ','યાદી પર પાછા','பட்டியலுக்குத் திரும்பு'),(7716,'by','By','দ্বারা','द्वारा','द्वारे','ಮೂಲಕ','દ્વારા','மூலம்'),(7949,'delete_existing_grades_first','Delete the existing grades first','আগে বিদ্যমান গ্রেডগুলি মুছুন','पहले मौजूदा ग्रेड हटाएँ','आधी सध्याच्या श्रेणी हटवा','ಮೊದಲು ಈಗಿರುವ ಗ್ರೇಡ್‌ಗಳನ್ನು ಅಳಿಸಿ','પહેલાં હાલના ગ્રેડ કાઢી નાખો','முதலில் ஏற்கனவே உள்ள தரங்களை நீக்கவும்'),(7950,'duration_must_be_at_least_1_minute','Duration must be at least 1 minute','সময়কাল কমপক্ষে ১ মিনিট হতে হবে','अवधि कम से कम 1 मिनट होनी चाहिए','कालावधी किमान 1 मिनिट असावा','ಅವಧಿ ಕನಿಷ್ಠ 1 ನಿಮಿಷ ಇರಬೇಕು','સમયગાળો ઓછામાં ઓછો 1 મિનિટ હોવો જોઈએ','கால அளவு குறைந்தது 1 நிமிடம் இருக்க வேண்டும்'),(7948,'contact_the_office_to_change_your_details','Contact the office to change your details.','আপনার তথ্য পরিবর্তন করতে অফিসে যোগাযোগ করুন।','अपना विवरण बदलने के लिए कार्यालय से संपर्क करें।','तुमचा तपशील बदलण्यासाठी कार्यालयाशी संपर्क साधा.','ನಿಮ್ಮ ವಿವರಗಳನ್ನು ಬದಲಾಯಿಸಲು ಕಚೇರಿಯನ್ನು ಸಂಪರ್ಕಿಸಿ.','તમારી વિગતો બદલવા માટે ઑફિસનો સંપર્ક કરો.','உங்கள் விவரங்களை மாற்ற அலுவலகத்தைத் தொடர்பு கொள்ளவும்.'),(7945,'choose_exam_and_class','Choose Exam And Class','পরীক্ষা ও শ্রেণি বেছে নিন','परीक्षा और कक्षा चुनें','परीक्षा आणि वर्ग निवडा','ಪರೀಕ್ಷೆ ಮತ್ತು ತರಗತಿ ಆಯ್ಕೆಮಾಡಿ','પરીક્ષા અને વર્ગ પસંદ કરો','தேர்வு மற்றும் வகுப்பைத் தேர்ந்தெடு'),(7946,'choose_exam_class_and_subject','Choose Exam Class And Subject','পরীক্ষা, শ্রেণি ও বিষয় বেছে নিন','परीक्षा, कक्षा और विषय चुनें','परीक्षा, वर्ग आणि विषय निवडा','ಪರೀಕ್ಷೆ, ತರಗತಿ ಮತ್ತು ವಿಷಯ ಆಯ್ಕೆಮಾಡಿ','પરીક્ષા, વર્ગ અને વિષય પસંદ કરો','தேர்வு, வகுப்பு மற்றும் பாடத்தைத் தேர்ந்தெடு'),(7834,'last_100','last 100','শেষ ১০০টি','अंतिम 100','शेवटचे 100','ಕೊನೆಯ 100','છેલ્લા 100','கடைசி 100'),(7835,'type','Type','ধরন','प्रकार','प्रकार','ಪ್ರಕಾರ','પ્રકાર','வகை'),(7836,'no_emails_yet','No Emails Yet','এখনও কোনো ইমেল নেই','अभी तक कोई ईमेल नहीं','अजून कोणतेही ईमेल नाहीत','ಇನ್ನೂ ಇಮೇಲ್‌ಗಳಿಲ್ಲ','હજી કોઈ ઈમેલ નથી','இன்னும் மின்னஞ்சல்கள் இல்லை'),(7837,'cbt_exam','Online Exam','অনলাইন পরীক্ষা','ऑनलाइन परीक्षा','ऑनलाइन परीक्षा','ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆ','ઑનલાઇન પરીક્ષા','ஆன்லைன் தேர்வு'),(7838,'preview_exam_email','Preview Exam Email','পরীক্ষার ইমেল প্রিভিউ','परीक्षा ईमेल पूर्वावलोकन','परीक्षा ईमेल पूर्वावलोकन','ಪರೀಕ್ಷೆ ಇಮೇಲ್ ಪೂರ್ವವೀಕ್ಷಣೆ','પરીક્ષા ઈમેલનું પૂર્વાવલોકન','தேர்வு மின்னஞ்சல் முன்னோட்டம்'),(7839,'publish_exam','Publish Exam','পরীক্ষা প্রকাশ করুন','परीक्षा प्रकाशित करें','परीक्षा प्रकाशित करा','ಪರೀಕ್ಷೆ ಪ್ರಕಟಿಸಿ','પરીક્ષા પ્રકાશિત કરો','தேர்வை வெளியிடு'),(7840,'correct_answer','Correct Answer','সঠিক উত্তর','सही उत्तर','बरोबर उत्तर','ಸರಿಯಾದ ಉತ್ತರ','સાચો જવાબ','சரியான பதில்'),(7841,'select_the_radio_of_the_correct_option','Tick the circle next to the correct option','সঠিক বিকল্পের পাশের বৃত্তে টিক দিন','सही विकल्प के बगल के गोले पर टिक करें','बरोबर पर्यायासमोरील गोल निवडा','ಸರಿಯಾದ ಆಯ್ಕೆಯ ಪಕ್ಕದ ವೃತ್ತವನ್ನು ಗುರುತಿಸಿ','સાચા વિકલ્પની બાજુના ગોળા પર ટિક કરો','சரியான விருப்பத்தின் அருகிலுள்ள வட்டத்தைத் தேர்ந்தெடுக்கவும்'),(7842,'not_entered','Not Entered','এন্ট্রি হয়নি','दर्ज नहीं','भरलेले नाही','ನಮೂದಿಸಿಲ್ಲ','દાખલ કર્યા નથી','உள்ளிடப்படவில்லை'),(7843,'add_question','Add Question','প্রশ্ন যোগ করুন','प्रश्न जोड़ें','प्रश्न जोडा','ಪ್ರಶ್ನೆ ಸೇರಿಸಿ','પ્રશ્ન ઉમેરો','கேள்வியைச் சேர்'),(7844,'publish_the_exam_before_assigning_it','Publish The Exam Before Assigning It','নির্ধারণের আগে পরীক্ষাটি প্রকাশ করুন','असाइन करने से पहले परीक्षा प्रकाशित करें','नेमण्यापूर्वी परीक्षा प्रकाशित करा','ನಿಯೋಜಿಸುವ ಮೊದಲು ಪರೀಕ್ಷೆಯನ್ನು ಪ್ರಕಟಿಸಿ','સોંપતા પહેલા પરીક્ષા પ્રકાશિત કરો','ஒதுக்குவதற்கு முன் தேர்வை வெளியிடவும்'),(7845,'open_exam','Open Exam','পরীক্ষা খুলুন','परीक्षा खोलें','परीक्षा उघडा','ಪರೀಕ್ಷೆ ತೆರೆಯಿರಿ','પરીક્ષા ખોલો','தேர்வைத் திற'),(7846,'select_all','Select All','সব নির্বাচন করুন','सभी चुनें','सर्व निवडा','ಎಲ್ಲವನ್ನೂ ಆಯ್ಕೆಮಾಡಿ','બધા પસંદ કરો','அனைத்தையும் தேர்ந்தெடு'),(7847,'email_sent','Email Sent','ইমেল পাঠানো হয়েছে','ईमेल भेजा गया','ईमेल पाठवला','ಇಮೇಲ್ ಕಳುಹಿಸಲಾಗಿದೆ','ઈમેલ મોકલાયો','மின்னஞ்சல் அனுப்பப்பட்டது'),(7848,'not_assigned','Not Assigned','নির্ধারিত নয়','असाइन नहीं','नेमलेले नाही','ನಿಯೋಜಿಸಿಲ್ಲ','સોંપાયેલ નથી','ஒதுக்கப்படவில்லை'),(7849,'started','Started','শুরু হয়েছে','शुरू किया','सुरू केले','ಆರಂಭಿಸಿದೆ','શરૂ કર્યું','தொடங்கியது'),(7850,'score','Score','স্কোর','स्कोर','गुण','ಸ್ಕೋರ್','સ્કોર','மதிப்பெண்'),(7851,'no_students_assigned','No Students Assigned','কোনো শিক্ষার্থী নির্ধারিত নেই','कोई छात्र असाइन नहीं','कोणतेही विद्यार्थी नेमलेले नाहीत','ಯಾವುದೇ ವಿದ್ಯಾರ್ಥಿಗಳನ್ನು ನಿಯೋಜಿಸಿಲ್ಲ','કોઈ વિદ્યાર્થી સોંપાયો નથી','மாணவர்கள் ஒதுக்கப்படவில்லை'),(7852,'class_average','Class Average','শ্রেণির গড়','कक्षा औसत','वर्गाची सरासरी','ತರಗತಿಯ ಸರಾಸರಿ','વર્ગ સરેરાશ','வகுப்பு சராசரி'),(7853,'passed','Passed','উত্তীর্ণ হয়েছে','उत्तीर्ण हुए','उत्तीर्ण झाले','ಉತ್ತೀರ್ಣರಾದವರು','પાસ થયા','தேர்ச்சி பெற்றவர்கள்'),(7854,'result_emails_sent','Result Emails Sent','ফলাফলের ইমেল পাঠানো হয়েছে','परिणाम ईमेल भेजे गए','निकाल ईमेल पाठवले','ಫಲಿತಾಂಶ ಇಮೇಲ್‌ಗಳನ್ನು ಕಳುಹಿಸಲಾಗಿದೆ','પરિણામ ઈમેલ મોકલાયા','முடிவு மின்னஞ்சல்கள் அனுப்பப்பட்டன'),(7855,'rank','Rank','র‍্যাংক','रैंक','क्रमांक','ಶ್ರೇಣಿ','ક્રમ','தரவரிசை'),(7856,'result','Result','ফলাফল','परिणाम','निकाल','ಫಲಿತಾಂಶ','પરિણામ','முடிவு'),(7857,'marks_obtained','Marks Obtained','প্রাপ্ত নম্বর','प्राप्त अंक','मिळालेले गुण','ಗಳಿಸಿದ ಅಂಕಗಳು','મેળવેલા ગુણ','பெற்ற மதிப்பெண்கள்'),(7858,'out_of','Out Of','মোট','में से','पैकी','ಒಟ್ಟು','માંથી','மொத்தம்'),(7859,'absent_or_not_entered','Absent / not entered','অনুপস্থিত / এন্ট্রি হয়নি','अनुपस्थित / दर्ज नहीं','अनुपस्थित / नोंद नाही','ಗೈರು / ನಮೂದಿಸಿಲ್ಲ','ગેરહાજર / દાખલ કર્યા નથી','வருகையின்மை / உள்ளிடப்படவில்லை'),(7860,'save_marks','Save Marks','নম্বর সংরক্ষণ করুন','अंक सहेजें','गुण जतन करा','ಅಂಕಗಳನ್ನು ಉಳಿಸಿ','ગુણ સાચવો','மதிப்பெண்களைச் சேமி'),(7861,'marks_entry_hint','Leave a box blank if the student was absent or the mark is not ready yet.','শিক্ষার্থী অনুপস্থিত থাকলে বা নম্বর এখনও প্রস্তুত না হলে ঘরটি ফাঁকা রাখুন।','यदि छात्र अनुपस्थित था या अंक अभी तैयार नहीं हैं तो बॉक्स खाली छोड़ें।','विद्यार्थी अनुपस्थित असेल किंवा गुण अजून तयार नसतील तर रकाना रिकामा ठेवा.','ವಿದ್ಯಾರ್ಥಿ ಗೈರಾಗಿದ್ದರೆ ಅಥವಾ ಅಂಕ ಇನ್ನೂ ಸಿದ್ಧವಾಗಿಲ್ಲದಿದ್ದರೆ ಬಾಕ್ಸ್ ಖಾಲಿ ಬಿಡಿ.','વિદ્યાર્થી ગેરહાજર હોય અથવા ગુણ હજી તૈયાર ન હોય તો ખાનું ખાલી રાખો.','மாணவர் வராமல் இருந்தால் அல்லது மதிப்பெண் இன்னும் தயாராகவில்லை என்றால் கட்டத்தை காலியாக விடவும்.'),(7862,'view_tabulation_sheet','View Tabulation Sheet','ট্যাবুলেশন শিট দেখুন','टैबुलेशन शीट देखें','टॅब्युलेशन शीट पहा','ಟ್ಯಾಬುಲೇಶನ್ ಶೀಟ್ ವೀಕ್ಷಿಸಿ','ટેબ્યુલેશન શીટ જુઓ','அட்டவணைத் தாளைக் காண்க'),(7863,'set_up_grades','Set Up Grades','গ্রেড সেট আপ করুন','ग्रेड सेट करें','श्रेणी सेट करा','ಗ್ರೇಡ್‌ಗಳನ್ನು ಹೊಂದಿಸಿ','ગ્રેડ સેટ કરો','தரங்களை அமை'),(7987,'question_count_must_be_1_to_200','Number of questions must be between 1 and 200','প্রশ্নের সংখ্যা ১ থেকে ২০০ এর মধ্যে হতে হবে','प्रश्नों की संख्या 1 से 200 के बीच होनी चाहिए','प्रश्नांची संख्या 1 ते 200 दरम्यान असावी','ಪ್ರಶ್ನೆಗಳ ಸಂಖ್ಯೆ 1 ಮತ್ತು 200 ರ ನಡುವೆ ಇರಬೇಕು','પ્રશ્નોની સંખ્યા 1 થી 200 વચ્ચે હોવી જોઈએ','கேள்விகளின் எண்ணிக்கை 1 முதல் 200 வரை இருக்க வேண்டும்'),(7980,'open','Open now','এখন খোলা','अभी खुला है','आता सुरू','ಈಗ ತೆರೆದಿದೆ','હમણાં ખુલ્લી','இப்போது திறந்துள்ளது'),(7981,'opens_at','Opens at','খুলবে','खुलने का समय','सुरू होण्याची वेळ','ತೆರೆಯುವ ಸಮಯ','ખુલશે','திறக்கும் நேரம்'),(7974,'no_exams_assigned_to_you_yet','No Exams Assigned To You Yet','আপনাকে এখনও কোনো পরীক্ষা দেওয়া হয়নি','आपको अभी तक कोई परीक्षा असाइन नहीं हुई','तुम्हाला अजून कोणतीही परीक्षा नेमलेली नाही','ನಿಮಗೆ ಇನ್ನೂ ಯಾವುದೇ ಪರೀಕ್ಷೆ ನಿಯೋಜಿಸಿಲ್ಲ','હજી તમને કોઈ પરીક્ષા સોંપાઈ નથી','உங்களுக்கு இன்னும் தேர்வுகள் ஒதுக்கப்படவில்லை'),(7970,'move_to_draft','Move To Draft','খসড়ায় নিন','ड्राफ्ट में ले जाएँ','मसुद्यात न्या','ಡ್ರಾಫ್ಟ್‌ಗೆ ಸರಿಸಿ','ડ્રાફ્ટમાં ખસેડો','வரைவுக்கு மாற்று'),(7958,'exam_cannot_be_unpublished_after_students_started','The exam cannot be moved back to draft after students have started','শিক্ষার্থীরা শুরু করার পর পরীক্ষাকে খসড়ায় ফেরানো যাবে না','छात्रों के शुरू करने के बाद परीक्षा को ड्राफ्ट में वापस नहीं किया जा सकता','विद्यार्थ्यांनी सुरू केल्यानंतर परीक्षा परत मसुद्यात नेता येत नाही','ವಿದ್ಯಾರ್ಥಿಗಳು ಆರಂಭಿಸಿದ ನಂತರ ಪರೀಕ್ಷೆಯನ್ನು ಡ್ರಾಫ್ಟ್‌ಗೆ ಹಿಂತಿರುಗಿಸಲು ಸಾಧ್ಯವಿಲ್ಲ','વિદ્યાર્થીઓએ શરૂ કર્યા પછી પરીક્ષાને પાછી ડ્રાફ્ટમાં ખસેડી શકાતી નથી','மாணவர்கள் தொடங்கிய பிறகு தேர்வை வரைவுக்கு மாற்ற முடியாது'),(7955,'email_results_again','Email results to remaining students','বাকি শিক্ষার্থীদের ফলাফল ইমেল করুন','शेष छात्रों को परिणाम ईमेल करें','उर्वरित विद्यार्थ्यांना निकाल ईमेल करा','ಉಳಿದ ವಿದ್ಯಾರ್ಥಿಗಳಿಗೆ ಫಲಿತಾಂಶಗಳನ್ನು ಇಮೇಲ್ ಮಾಡಿ','બાકીના વિદ્યાર્થીઓને પરિણામ ફરીથી ઈમેલ કરો','மீதமுள்ள மாணவர்களுக்கு முடிவுகளை மீண்டும் மின்னஞ்சலில் அனுப்பு'),(7765,'subject_id','Subject ID','বিষয় আইডি','विषय आईडी','विषय आयडी','ವಿಷಯ ಐಡಿ','વિષય આઈડી','பாட ஐடி'),(7751,'nominate','Nominate','মনোনীত করুন','नामांकित करें','नामनिर्देशित करा','ನಾಮನಿರ್ದೇಶನ ಮಾಡಿ','નામાંકિત કરો','பரிந்துரை செய்'),(7742,'installment_total','Installment Total','কিস্তির মোট','किश्तों का योग','हप्त्यांची एकूण रक्कम','ಕಂತುಗಳ ಒಟ್ಟು','હપ્તાનો કુલ','தவணை மொத்தம்'),(7730,'email_is_required','Email is required','ইমেল আবশ্যক','ईमेल आवश्यक है','ईमेल आवश्यक आहे','ಇಮೇಲ್ ಅಗತ್ಯವಿದೆ','ઈમેલ જરૂરી છે','மின்னஞ்சல் தேவை'),(7725,'download_template','Download Template','টেমপ্লেট ডাউনলোড করুন','टेम्पलेट डाउनलोड करें','टेम्पलेट डाउनलोड करा','ಟೆಂಪ್ಲೇಟ್ ಡೌನ್‌ಲೋಡ್ ಮಾಡಿ','ટેમ્પલેટ ડાઉનલોડ કરો','வார்ப்புருவைப் பதிவிறக்கு'),(7714,'all_activities','All Activities','সব কার্যকলাপ','सभी गतिविधियाँ','सर्व क्रियाकलाप','ಎಲ್ಲಾ ಚಟುವಟಿಕೆಗಳು','બધી પ્રવૃત્તિઓ','அனைத்து செயல்பாடுகள்'),(7811,'marks_per_subject','Marks Per Subject','বিষয় অনুযায়ী নম্বর','प्रति विषय अंक','प्रति विषय गुण','ವಿಷಯವಾರು ಅಂಕಗಳು','વિષય દીઠ ગુણ','பாடவாரி மதிப்பெண்கள்'),(7812,'email_exam_schedule_to_students','Email the exam schedule to students','শিক্ষার্থীদের পরীক্ষার সময়সূচি ইমেল করুন','छात्रों को परीक्षा कार्यक्रम ईमेल करें','विद्यार्थ्यांना परीक्षेचे वेळापत्रक ईमेल करा','ವಿದ್ಯಾರ್ಥಿಗಳಿಗೆ ಪರೀಕ್ಷೆ ವೇಳಾಪಟ್ಟಿಯನ್ನು ಇಮೇಲ್ ಮಾಡಿ','વિદ્યાર્થીઓને પરીક્ષાનું સમયપત્રક ઈમેલ કરો','தேர்வு அட்டவணையை மாணவர்களுக்கு மின்னஞ்சலில் அனுப்பு'),(7813,'or_preview_first_then_send_from_the_list','Or preview it first and send it from the list.','অথবা আগে প্রিভিউ দেখে তালিকা থেকে পাঠান।','या पहले पूर्वावलोकन देखें और सूची से भेजें।','किंवा आधी पूर्वावलोकन करा आणि यादीतून पाठवा.','ಅಥವಾ ಮೊದಲು ಪೂರ್ವವೀಕ್ಷಣೆ ನೋಡಿ, ನಂತರ ಪಟ್ಟಿಯಿಂದ ಕಳುಹಿಸಿ.','અથવા પહેલા પૂર્વાવલોકન કરો અને યાદીમાંથી મોકલો.','அல்லது முதலில் முன்னோட்டம் பார்த்து, பட்டியலிலிருந்து அனுப்பவும்.'),(7814,'select_exam_class_and_subject_to_enter_marks','Select Exam Class And Subject To Enter Marks','নম্বর লিখতে পরীক্ষা, শ্রেণি ও বিষয় নির্বাচন করুন','अंक दर्ज करने के लिए परीक्षा, कक्षा और विषय चुनें','गुण भरण्यासाठी परीक्षा, वर्ग आणि विषय निवडा','ಅಂಕಗಳನ್ನು ನಮೂದಿಸಲು ಪರೀಕ್ಷೆ, ತರಗತಿ ಮತ್ತು ವಿಷಯ ಆಯ್ಕೆಮಾಡಿ','ગુણ દાખલ કરવા માટે પરીક્ષા, વર્ગ અને વિષય પસંદ કરો','மதிப்பெண்களை உள்ளிட தேர்வு, வகுப்பு மற்றும் பாடத்தைத் தேர்ந்தெடுக்கவும்'),(7815,'view','View','দেখুন','देखें','पहा','ವೀಕ್ಷಿಸಿ','જુઓ','காண்க'),(7816,'select_exam_and_class_to_see_the_tabulation_sheet','Select Exam And Class To See The Tabulation Sheet','ট্যাবুলেশন শিট দেখতে পরীক্ষা ও শ্রেণি নির্বাচন করুন','टैबुलेशन शीट देखने के लिए परीक्षा और कक्षा चुनें','टॅब्युलेशन शीट पाहण्यासाठी परीक्षा आणि वर्ग निवडा','ಟ್ಯಾಬುಲೇಶನ್ ಶೀಟ್ ನೋಡಲು ಪರೀಕ್ಷೆ ಮತ್ತು ತರಗತಿ ಆಯ್ಕೆಮಾಡಿ','ટેબ્યુલેશન શીટ જોવા માટે પરીક્ષા અને વર્ગ પસંદ કરો','அட்டவணைத் தாளைக் காண தேர்வு மற்றும் வகுப்பைத் தேர்ந்தெடுக்கவும்'),(7817,'percentage','Percentage','শতাংশ','प्रतिशत','टक्केवारी','ಶೇಕಡಾವಾರು','ટકાવારી','சதவீதம்'),(7818,'no_grades_configured','No Grades Configured','কোনো গ্রেড সেট করা নেই','कोई ग्रेड सेट नहीं है','कोणत्याही श्रेणी सेट केलेल्या नाहीत','ಗ್ರೇಡ್‌ಗಳನ್ನು ಹೊಂದಿಸಿಲ್ಲ','કોઈ ગ્રેડ ગોઠવેલા નથી','தரங்கள் அமைக்கப்படவில்லை'),(7819,'add_standard_grades','Add Standard Grades','স্ট্যান্ডার্ড গ্রেড যোগ করুন','मानक ग्रेड जोड़ें','प्रमाणित श्रेणी जोडा','ಸ್ಟ್ಯಾಂಡರ್ಡ್ ಗ್ರೇಡ್‌ಗಳನ್ನು ಸೇರಿಸಿ','સ્ટાન્ડર્ડ ગ્રેડ ઉમેરો','நிலையான தரங்களைச் சேர்'),(7820,'grade_hint','Grades use the percentage of marks obtained. Ranges must not overlap.','গ্রেড প্রাপ্ত নম্বরের শতাংশের উপর ভিত্তি করে হয়। সীমা যেন একে অপরের সঙ্গে না মেলে।','ग्रेड प्राप्त अंकों के प्रतिशत पर आधारित होते हैं। सीमाएँ एक-दूसरे से मेल नहीं खानी चाहिए।','श्रेणी मिळालेल्या गुणांच्या टक्केवारीवर आधारित असतात. श्रेणींच्या मर्यादा एकमेकांवर येऊ नयेत.','ಗ್ರೇಡ್‌ಗಳು ಗಳಿಸಿದ ಅಂಕಗಳ ಶೇಕಡಾವಾರನ್ನು ಬಳಸುತ್ತವೆ. ವ್ಯಾಪ್ತಿಗಳು ಪರಸ್ಪರ ಅತಿಕ್ರಮಿಸಬಾರದು.','ગ્રેડ મેળવેલા ગુણની ટકાવારી પર આધારિત છે. શ્રેણીઓ એકબીજા પર આવવી ન જોઈએ.','தரங்கள் பெற்ற மதிப்பெண்களின் சதவீதத்தைப் பயன்படுத்துகின்றன. வரம்புகள் ஒன்றோடொன்று மேலெழக்கூடாது.'),(7821,'from','From','থেকে','से','कडून','ಇಂದ','પ્રતિ','அனுப்புநர்'),(7822,'to','To','পর্যন্ত','तक','पर्यंत','ವರೆಗೆ','પ્રતિ','பெறுநர்'),(7823,'email_not_configured_hint','Email is not set up yet. Emails are only recorded in the log below until you enter a Gmail address and app password.','ইমেল এখনও সেট আপ করা হয়নি। Gmail ঠিকানা ও অ্যাপ পাসওয়ার্ড না দেওয়া পর্যন্ত ইমেল শুধু নিচের লগে রেকর্ড হবে।','ईमेल अभी सेट नहीं है। जब तक आप Gmail पता और ऐप पासवर्ड दर्ज नहीं करते, ईमेल केवल नीचे दिए लॉग में दर्ज होंगे।','ईमेल अजून सेट केलेला नाही. तुम्ही Gmail पत्ता आणि अ‍ॅप पासवर्ड टाकेपर्यंत ईमेल फक्त खालील लॉगमध्ये नोंदवले जातात.','ಇಮೇಲ್ ಇನ್ನೂ ಸಿದ್ಧಗೊಂಡಿಲ್ಲ. ನೀವು Gmail ವಿಳಾಸ ಮತ್ತು ಆ್ಯಪ್ ಪಾಸ್‌ವರ್ಡ್ ನಮೂದಿಸುವವರೆಗೆ ಇಮೇಲ್‌ಗಳನ್ನು ಕೆಳಗಿನ ಲಾಗ್‌ನಲ್ಲಿ ಮಾತ್ರ ದಾಖಲಿಸಲಾಗುತ್ತದೆ.','ઈમેલ હજી સેટ થયેલ નથી. તમે Gmail સરનામું અને એપ પાસવર્ડ દાખલ ન કરો ત્યાં સુધી ઈમેલ ફક્ત નીચેના લોગમાં નોંધાશે.','மின்னஞ்சல் இன்னும் அமைக்கப்படவில்லை. Gmail முகவரி மற்றும் ஆப் கடவுச்சொல்லை உள்ளிடும் வரை மின்னஞ்சல்கள் கீழே உள்ள பதிவில் மட்டுமே சேமிக்கப்படும்.'),(7824,'gmail_address','Gmail Address','Gmail ঠিকানা','Gmail पता','Gmail पत्ता','Gmail ವಿಳಾಸ','Gmail સરનામું','Gmail முகவரி'),(7825,'app_password','App Password','অ্যাপ পাসওয়ার্ড','ऐप पासवर्ड','अ‍ॅप पासवर्ड','ಆ್ಯಪ್ ಪಾಸ್‌ವರ್ಡ್','એપ પાસવર્ડ','ஆப் கடவுச்சொல்'),(7826,'smtp_server','SMTP server','SMTP সার্ভার','SMTP सर्वर','SMTP सर्व्हर','SMTP ಸರ್ವರ್','SMTP સર્વર','SMTP சர்வர்'),(7827,'send_emails','Send Emails','ইমেল পাঠান','ईमेल भेजें','ईमेल पाठवा','ಇಮೇಲ್‌ಗಳನ್ನು ಕಳುಹಿಸಿ','ઈમેલ મોકલો','மின்னஞ்சல்களை அனுப்பு'),(7828,'also_send_a_copy_to_the_parent','Also send a copy to the parent','অভিভাবককেও একটি কপি পাঠান','अभिभावक को भी एक प्रति भेजें','पालकांनाही प्रत पाठवा','ಪೋಷಕರಿಗೂ ಒಂದು ಪ್ರತಿ ಕಳುಹಿಸಿ','વાલીને પણ નકલ મોકલો','பெற்றோருக்கும் ஒரு நகல் அனுப்பு'),(7829,'send_a_test_email_to','Send a test email to','টেস্ট ইমেল পাঠান এখানে','टेस्ट ईमेल भेजें','यांना चाचणी ईमेल पाठवा','ಪರೀಕ್ಷಾ ಇಮೇಲ್ ಕಳುಹಿಸಿ:','ટેસ્ટ ઈમેલ મોકલો','சோதனை மின்னஞ்சலை அனுப்ப வேண்டிய முகவரி'),(7830,'send_test','Send Test','টেস্ট পাঠান','टेस्ट भेजें','चाचणी पाठवा','ಪರೀಕ್ಷೆ ಕಳುಹಿಸಿ','ટેસ્ટ મોકલો','சோதனை அனுப்பு'),(7831,'gmail_setup_steps','How to set up Gmail','Gmail কীভাবে সেট আপ করবেন','Gmail कैसे सेट करें','Gmail कसे सेट करावे','Gmail ಅನ್ನು ಹೇಗೆ ಹೊಂದಿಸುವುದು','Gmail કેવી રીતે સેટ કરવું','Gmail ஐ எவ்வாறு அமைப்பது'),(7832,'daily_exam_reminders','Daily exam reminders','দৈনিক পরীক্ষার অনুস্মারক','दैनिक परीक्षा अनुस्मारक','दैनंदिन परीक्षा स्मरणपत्रे','ದೈನಂದಿನ ಪರೀಕ್ಷೆ ಜ್ಞಾಪನೆಗಳು','દૈનિક પરીક્ષા રિમાઇન્ડર','தினசரி தேர்வு நினைவூட்டல்கள்'),(7833,'email_log','Email Log','ইমেল লগ','ईमेल लॉग','ईमेल लॉग','ಇಮೇಲ್ ಲಾಗ್','ઈમેલ લોગ','மின்னஞ்சல் பதிவு'),(8007,'this_exam_has_not_started_yet','This Exam Has Not Started Yet','এই পরীক্ষা এখনও শুরু হয়নি','यह परीक्षा अभी शुरू नहीं हुई है','ही परीक्षा अजून सुरू झालेली नाही','ಈ ಪರೀಕ್ಷೆ ಇನ್ನೂ ಆರಂಭವಾಗಿಲ್ಲ','આ પરીક્ષા હજી શરૂ થઈ નથી','இந்தத் தேர்வு இன்னும் தொடங்கவில்லை'),(7995,'results_already_published_student_sees_new_marks','Results are already published, so the student will see the new marks.','ফলাফল ইতিমধ্যে প্রকাশিত, তাই শিক্ষার্থী নতুন নম্বর দেখতে পাবে।','परिणाम पहले ही प्रकाशित हो चुके हैं, इसलिए छात्र नए अंक देखेगा।','निकाल आधीच प्रकाशित झाले आहेत, त्यामुळे विद्यार्थ्याला नवीन गुण दिसतील.','ಫಲಿತಾಂಶಗಳನ್ನು ಈಗಾಗಲೇ ಪ್ರಕಟಿಸಲಾಗಿದೆ, ಆದ್ದರಿಂದ ವಿದ್ಯಾರ್ಥಿಗೆ ಹೊಸ ಅಂಕಗಳು ಕಾಣಿಸುತ್ತವೆ.','પરિણામ પહેલેથી પ્રકાશિત થયેલ છે, તેથી વિદ્યાર્થી નવા ગુણ જોશે.','முடிவுகள் ஏற்கனவே வெளியிடப்பட்டுள்ளன, எனவே மாணவர் புதிய மதிப்பெண்களைக் காண்பார்.'),(7986,'question_added','Question Added','প্রশ্ন যোগ হয়েছে','प्रश्न जोड़ा गया','प्रश्न जोडला','ಪ್ರಶ್ನೆ ಸೇರಿಸಲಾಗಿದೆ','પ્રશ્ન ઉમેરાયો','கேள்வி சேர்க்கப்பட்டது'),(7979,'online','Online','অনলাইন','ऑनलाइन','ऑनलाइन','ಆನ್‌ಲೈನ್','ઑનલાઇન','ஆன்லைன்'),(7973,'no_email','No Email','ইমেল নেই','ईमेल नहीं','ईमेल नाही','ಇಮೇಲ್ ಇಲ್ಲ','ઈમેલ નથી','மின்னஞ்சல் இல்லை'),(7969,'missed','Missed','বাদ পড়েছে','छूटा','चुकले','ತಪ್ಪಿಸಿಕೊಂಡಿದೆ','ચૂકી ગયા','தவறவிட்டது'),(7966,'grade_name_is_required','Grade Name Is Required','গ্রেডের নাম আবশ্যক','ग्रेड का नाम आवश्यक है','श्रेणीचे नाव आवश्यक आहे','ಗ್ರೇಡ್ ಹೆಸರು ಅಗತ್ಯ','ગ્રેડનું નામ જરૂરી છે','தரத்தின் பெயர் தேவை'),(7965,'exam_settings_saved','Exam Settings Saved','পরীক্ষার সেটিংস সংরক্ষিত হয়েছে','परीक्षा सेटिंग्स सहेजी गईं','परीक्षा सेटिंग्ज जतन केली','ಪರೀಕ್ಷೆ ಸೆಟ್ಟಿಂಗ್‌ಗಳನ್ನು ಉಳಿಸಲಾಗಿದೆ','પરીક્ષા સેટિંગ્સ સાચવાઈ','தேர்வு அமைப்புகள் சேமிக்கப்பட்டன'),(7962,'exam_name_is_required','Exam Name Is Required','পরীক্ষার নাম আবশ্যক','परीक्षा का नाम आवश्यक है','परीक्षेचे नाव आवश्यक आहे','ಪರೀಕ್ಷೆಯ ಹೆಸರು ಅಗತ್ಯ','પરીક્ષાનું નામ જરૂરી છે','தேர்வு பெயர் தேவை'),(7960,'exam_date_is_required','Exam Date Is Required','পরীক্ষার তারিখ আবশ্যক','परीक्षा की तारीख आवश्यक है','परीक्षेची तारीख आवश्यक आहे','ಪರೀಕ್ಷೆಯ ದಿನಾಂಕ ಅಗತ್ಯ','પરીક્ષાની તારીખ જરૂરી છે','தேர்வு தேதி தேவை'),(7957,'error','Error','ত্রুটি','त्रुटि','त्रुटी','ದೋಷ','ભૂલ','பிழை'),(7953,'email_is_configured','Email is set up. Students and parents will receive emails.','ইমেল সেট আপ করা আছে। শিক্ষার্থী ও অভিভাবকরা ইমেল পাবেন।','ईमेल सेट है। छात्रों और अभिभावकों को ईमेल मिलेंगे।','ईमेल सेट केला आहे. विद्यार्थी आणि पालकांना ईमेल मिळतील.','ಇಮೇಲ್ ಸಿದ್ಧವಾಗಿದೆ. ವಿದ್ಯಾರ್ಥಿಗಳು ಮತ್ತು ಪೋಷಕರಿಗೆ ಇಮೇಲ್ ತಲುಪುತ್ತದೆ.','ઈમેલ સેટ થયેલ છે. વિદ્યાર્થીઓ અને વાલીઓને ઈમેલ મળશે.','மின்னஞ்சல் அமைக்கப்பட்டுள்ளது. மாணவர்களும் பெற்றோரும் மின்னஞ்சல்களைப் பெறுவர்.'),(7770,'valid_email_required','A valid email is required','সঠিক ইমেল আবশ্যক','मान्य ईमेल आवश्यक है','वैध ईमेल आवश्यक आहे','ಮಾನ್ಯ ಇಮೇಲ್ ಅಗತ್ಯವಿದೆ','માન્ય ઈમેલ જરૂરી છે','சரியான மின்னஞ்சல் தேவை'),(7764,'standard_hint','The class / standard this course is for','এই কোর্সটি কোন শ্রেণির জন্য','यह कोर्स किस कक्षा के लिए है','हा कोर्स कोणत्या इयत्तेसाठी आहे','ಈ ಕೋರ್ಸ್ ಯಾವ ತರಗತಿಗಾಗಿ','આ કોર્સ કયા ધોરણ માટે છે','இந்தப் பாடநெறி எந்த வகுப்புக்கானது'),(7757,'product_updated_successfully','Product updated successfully','পণ্য সফলভাবে হালনাগাদ হয়েছে','उत्पाद सफलतापूर्वक अपडेट हुआ','उत्पादन यशस्वीरित्या अद्ययावत झाले','ಉತ್ಪನ್ನವನ್ನು ಯಶಸ್ವಿಯಾಗಿ ನವೀಕರಿಸಲಾಗಿದೆ','પ્રોડક્ટ સફળતાપૂર્વક અપડેટ થયું','தயாரிப்பு வெற்றிகரமாகப் புதுப்பிக்கப்பட்டது'),(7754,'phrase_added','Phrase added','বাক্যাংশ যোগ করা হয়েছে','वाक्यांश जोड़ा गया','वाक्य जोडले','ಪದಗುಚ್ಛ ಸೇರಿಸಲಾಗಿದೆ','શબ્દસમૂહ ઉમેરાયો','சொற்றொடர் சேர்க்கப்பட்டது'),(7746,'language_name_is_required','Language name is required','ভাষার নাম আবশ্যক','भाषा का नाम आवश्यक है','भाषेचे नाव आवश्यक आहे','ಭಾಷೆಯ ಹೆಸರು ಅಗತ್ಯವಿದೆ','ભાષાનું નામ જરૂરી છે','மொழிப் பெயர் தேவை'),(7736,'existing_student_name','Existing Student Name','বর্তমান ছাত্রের নাম','मौजूदा छात्र का नाम','विद्यमान विद्यार्थ्याचे नाव','ಈಗಿರುವ ವಿದ್ಯಾರ್ಥಿಯ ಹೆಸರು','હાલના વિદ્યાર્થીનું નામ','தற்போதைய மாணவர் பெயர்'),(7721,'course_details','Course Details','কোর্সের বিবরণ','कोर्स विवरण','कोर्स तपशील','ಕೋರ್ಸ್ ವಿವರಗಳು','કોર્સ વિગતો','பாடநெறி விவரங்கள்'),(7713,'add_course','Add Course','কোর্স যোগ করুন','कोर्स जोड़ें','कोर्स जोडा','ಕೋರ್ಸ್ ಸೇರಿಸಿ','કોર્સ ઉમેરો','பாடநெறி சேர்'),(7947,'closed','Closed','বন্ধ','बंद','बंद','ಮುಚ್ಚಲಾಗಿದೆ','બંધ','மூடப்பட்டது'),(7776,'cbt_exams','Online Exams (CBT)','অনলাইন পরীক্ষা (CBT)','ऑनलाइन परीक्षाएँ (CBT)','ऑनलाइन परीक्षा (CBT)','ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆಗಳು (CBT)','ઑનલાઇન પરીક્ષાઓ (CBT)','ஆன்லைன் தேர்வுகள் (CBT)'),(7777,'written_exams','Written Exams','লিখিত পরীক্ষা','लिखित परीक्षाएँ','लेखी परीक्षा','ಲಿಖಿತ ಪರೀಕ್ಷೆಗಳು','લેખિત પરીક્ષાઓ','எழுத்துத் தேர்வுகள்'),(7778,'enter_marks','Enter Marks','নম্বর লিখুন','अंक दर्ज करें','गुण भरा','ಅಂಕಗಳನ್ನು ನಮೂದಿಸಿ','ગુણ દાખલ કરો','மதிப்பெண்களை உள்ளிடு'),(7779,'add_cbt_exam','Add Online Exam','অনলাইন পরীক্ষা যোগ করুন','ऑनलाइन परीक्षा जोड़ें','ऑनलाइन परीक्षा जोडा','ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆ ಸೇರಿಸಿ','ઑનલાઇન પરીક્ષા ઉમેરો','ஆன்லைன் தேர்வைச் சேர்'),(7780,'assign_exam_to_students','Assign Exam To Students','শিক্ষার্থীদের পরীক্ষা নির্ধারণ করুন','छात्रों को परीक्षा असाइन करें','विद्यार्थ्यांना परीक्षा नेमून द्या','ವಿದ್ಯಾರ್ಥಿಗಳಿಗೆ ಪರೀಕ್ಷೆ ನಿಯೋಜಿಸಿ','વિદ્યાર્થીઓને પરીક્ષા સોંપો','மாணவர்களுக்கு தேர்வை ஒதுக்கு'),(7781,'paper_checking','Paper Checking','খাতা দেখা','पेपर जाँच','पेपर तपासणी','ಪತ್ರಿಕೆ ಪರಿಶೀಲನೆ','પેપર તપાસણી','தாள் திருத்தம்'),(7782,'cbt_results','Online Exam Results','অনলাইন পরীক্ষার ফলাফল','ऑनलाइन परीक्षा परिणाम','ऑनलाइन परीक्षा निकाल','ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆ ಫಲಿತಾಂಶಗಳು','ઑનલાઇન પરીક્ષા પરિણામો','ஆன்லைன் தேர்வு முடிவுகள்'),(7783,'email_settings','Email Settings','ইমেল সেটিংস','ईमेल सेटिंग्स','ईमेल सेटिंग्ज','ಇಮೇಲ್ ಸೆಟ್ಟಿಂಗ್‌ಗಳು','ઈમેલ સેટિંગ્સ','மின்னஞ்சல் அமைப்புகள்'),(7784,'cbt_workflow_hint','Steps: Add exam, enter questions, Publish, Assign students (they get an email), students take the test online, check papers, Publish results (students get an email).','ধাপসমূহ: পরীক্ষা যোগ করুন, প্রশ্ন লিখুন, প্রকাশ করুন, শিক্ষার্থী নির্ধারণ করুন (তারা ইমেল পাবে), শিক্ষার্থীরা অনলাইনে পরীক্ষা দেবে, খাতা দেখুন, ফলাফল প্রকাশ করুন (শিক্ষার্থীরা ইমেল পাবে)।','चरण: परीक्षा जोड़ें, प्रश्न दर्ज करें, प्रकाशित करें, छात्रों को असाइन करें (उन्हें ईमेल मिलता है), छात्र ऑनलाइन टेस्ट दें, पेपर जाँचें, परिणाम प्रकाशित करें (छात्रों को ईमेल मिलता है)।','पायऱ्या: परीक्षा जोडा, प्रश्न भरा, प्रकाशित करा, विद्यार्थी नेमा (त्यांना ईमेल मिळतो), विद्यार्थी ऑनलाइन परीक्षा देतात, पेपर तपासा, निकाल प्रकाशित करा (विद्यार्थ्यांना ईमेल मिळतो).','ಹಂತಗಳು: ಪರೀಕ್ಷೆ ಸೇರಿಸಿ, ಪ್ರಶ್ನೆಗಳನ್ನು ನಮೂದಿಸಿ, ಪ್ರಕಟಿಸಿ, ವಿದ್ಯಾರ್ಥಿಗಳನ್ನು ನಿಯೋಜಿಸಿ (ಅವರಿಗೆ ಇಮೇಲ್ ಹೋಗುತ್ತದೆ), ವಿದ್ಯಾರ್ಥಿಗಳು ಆನ್‌ಲೈನ್‌ನಲ್ಲಿ ಪರೀಕ್ಷೆ ಬರೆಯುತ್ತಾರೆ, ಪತ್ರಿಕೆಗಳನ್ನು ಪರಿಶೀಲಿಸಿ, ಫಲಿತಾಂಶ ಪ್ರಕಟಿಸಿ (ವಿದ್ಯಾರ್ಥಿಗಳಿಗೆ ಇಮೇಲ್ ಹೋಗುತ್ತದೆ).','પગલાં: પરીક્ષા ઉમેરો, પ્રશ્નો દાખલ કરો, પ્રકાશિત કરો, વિદ્યાર્થીઓને સોંપો (તેમને ઈમેલ મળશે), વિદ્યાર્થીઓ ઑનલાઇન ટેસ્ટ આપે, પેપર તપાસો, પરિણામ પ્રકાશિત કરો (વિદ્યાર્થીઓને ઈમેલ મળશે).','படிகள்: தேர்வைச் சேர், கேள்விகளை உள்ளிடு, வெளியிடு, மாணவர்களை ஒதுக்கு (அவர்களுக்கு மின்னஞ்சல் செல்லும்), மாணவர்கள் ஆன்லைனில் தேர்வு எழுதுவர், தாள்களைச் சரிபார், முடிவுகளை வெளியிடு (மாணவர்களுக்கு மின்னஞ்சல் செல்லும்).'),(7785,'exam','Exam','পরীক্ষা','परीक्षा','परीक्षा','ಪರೀಕ್ಷೆ','પરીક્ષા','தேர்வு'),(7786,'time','Time','সময়','समय','वेळ','ಸಮಯ','સમય','நேரம்'),(7787,'questions','Questions','প্রশ্নসমূহ','प्रश्न','प्रश्न','ಪ್ರಶ್ನೆಗಳು','પ્રશ્નો','கேள்விகள்'),(7788,'marks','Marks','নম্বর','अंक','गुण','ಅಂಕಗಳು','ગુણ','மதிப்பெண்கள்'),(7789,'submitted','Submitted','জমা দেওয়া হয়েছে','सबमिट किया','सबमिट केले','ಸಲ್ಲಿಸಲಾಗಿದೆ','સબમિટ કર્યું','சமர்ப்பிக்கப்பட்டது'),(7790,'min','min','মিনিট','मिनट','मिनिटे','ನಿಮಿಷ','મિનિટ','நிமி'),(7791,'exam_title','Exam Title','পরীক্ষার শিরোনাম','परीक्षा शीर्षक','परीक्षेचे शीर्षक','ಪರೀಕ್ಷೆಯ ಶೀರ್ಷಿಕೆ','પરીક્ષાનું શીર્ષક','தேர்வு தலைப்பு'),(7792,'start_time','Start Time','শুরুর সময়','शुरू होने का समय','सुरू होण्याची वेळ','ಆರಂಭ ಸಮಯ','શરૂઆતનો સમય','தொடக்க நேரம்'),(7793,'last_entry_time','Last entry time','শেষ প্রবেশের সময়','अंतिम प्रवेश समय','शेवटची प्रवेश वेळ','ಕೊನೆಯ ಪ್ರವೇಶ ಸಮಯ','છેલ્લો પ્રવેશ સમય','கடைசி நுழைவு நேரம்'),(7794,'exam_window_hint','Students can start between the start time and the last entry time (default: start time + duration). Each student gets the full duration, but must finish by the last entry time.','শিক্ষার্থীরা শুরুর সময় থেকে শেষ প্রবেশের সময়ের মধ্যে শুরু করতে পারবে (ডিফল্ট: শুরুর সময় + সময়কাল)। প্রত্যেক শিক্ষার্থী পূর্ণ সময়কাল পাবে, কিন্তু শেষ প্রবেশের সময়ের মধ্যে শেষ করতে হবে।','छात्र शुरू होने के समय और अंतिम प्रवेश समय के बीच परीक्षा शुरू कर सकते हैं (डिफ़ॉल्ट: शुरू होने का समय + अवधि)। प्रत्येक छात्र को पूरी अवधि मिलती है, लेकिन उसे अंतिम प्रवेश समय तक समाप्त करना होगा।','विद्यार्थी सुरू होण्याची वेळ आणि शेवटची प्रवेश वेळ यादरम्यान परीक्षा सुरू करू शकतात (डीफॉल्ट: सुरू होण्याची वेळ + कालावधी). प्रत्येक विद्यार्थ्याला पूर्ण कालावधी मिळतो, पण त्याने शेवटच्या प्रवेश वेळेपर्यंत पूर्ण केले पाहिजे.','ವಿದ್ಯಾರ್ಥಿಗಳು ಆರಂಭ ಸಮಯ ಮತ್ತು ಕೊನೆಯ ಪ್ರವೇಶ ಸಮಯದ ನಡುವೆ ಪ್ರಾರಂಭಿಸಬಹುದು (ಡೀಫಾಲ್ಟ್: ಆರಂಭ ಸಮಯ + ಅವಧಿ). ಪ್ರತಿ ವಿದ್ಯಾರ್ಥಿಗೆ ಪೂರ್ಣ ಅವಧಿ ಸಿಗುತ್ತದೆ, ಆದರೆ ಕೊನೆಯ ಪ್ರವೇಶ ಸಮಯದೊಳಗೆ ಮುಗಿಸಬೇಕು.','વિદ્યાર્થીઓ શરૂઆતના સમય અને છેલ્લા પ્રવેશ સમય વચ્ચે શરૂ કરી શકે છે (ડિફૉલ્ટ: શરૂઆતનો સમય + સમયગાળો). દરેક વિદ્યાર્થીને પૂરો સમયગાળો મળે છે, પણ છેલ્લા પ્રવેશ સમય સુધીમાં પૂર્ણ કરવું પડશે.','மாணவர்கள் தொடக்க நேரத்திற்கும் கடைசி நுழைவு நேரத்திற்கும் இடையே தொடங்கலாம் (இயல்புநிலை: தொடக்க நேரம் + கால அளவு). ஒவ்வொரு மாணவருக்கும் முழு கால அளவு கிடைக்கும், ஆனால் கடைசி நுழைவு நேரத்திற்குள் முடிக்க வேண்டும்.'),(7795,'duration','Duration','সময়কাল','अवधि','कालावधी','ಅವಧಿ','સમયગાળો','கால அளவு'),(7796,'pass_percentage','Pass Percentage','পাসের শতাংশ','उत्तीर्ण प्रतिशत','उत्तीर्ण टक्केवारी','ಉತ್ತೀರ್ಣ ಶೇಕಡಾವಾರು','પાસ ટકાવારી','தேர்ச்சி சதவீதம்'),(7797,'number_of_questions','Number Of Questions','প্রশ্নের সংখ্যা','प्रश्नों की संख्या','प्रश्नांची संख्या','ಪ್ರಶ್ನೆಗಳ ಸಂಖ್ಯೆ','પ્રશ્નોની સંખ્યા','கேள்விகளின் எண்ணிக்கை'),(7798,'options_per_question','Options Per Question','প্রতি প্রশ্নে বিকল্প','प्रति प्रश्न विकल्प','प्रति प्रश्न पर्याय','ಪ್ರತಿ ಪ್ರಶ್ನೆಗೆ ಆಯ್ಕೆಗಳು','પ્રશ્ન દીઠ વિકલ્પો','ஒரு கேள்விக்கான விருப்பங்கள்'),(7799,'instructions','Instructions','নির্দেশাবলি','निर्देश','सूचना','ಸೂಚನೆಗಳು','સૂચનાઓ','அறிவுறுத்தல்கள்'),(7800,'continue_to_questions','Continue To Questions','প্রশ্নে এগিয়ে যান','प्रश्नों पर आगे बढ़ें','प्रश्नांकडे पुढे जा','ಪ್ರಶ್ನೆಗಳಿಗೆ ಮುಂದುವರಿಯಿರಿ','પ્રશ્નો તરફ આગળ વધો','கேள்விகளுக்குத் தொடர்'),(7801,'select_exam','Select Exam','পরীক্ষা নির্বাচন করুন','परीक्षा चुनें','परीक्षा निवडा','ಪರೀಕ್ಷೆ ಆಯ್ಕೆಮಾಡಿ','પરીક્ષા પસંદ કરો','தேர்வைத் தேர்ந்தெடு'),(7802,'draft','Draft','খসড়া','ड्राफ्ट','मसुदा','ಡ್ರಾಫ್ಟ್','ડ્રાફ્ટ','வரைவு'),(7803,'select_an_exam_to_assign_students','Select An Exam To Assign Students','শিক্ষার্থী নির্ধারণ করতে একটি পরীক্ষা বেছে নিন','छात्रों को असाइन करने के लिए परीक्षा चुनें','विद्यार्थी नेमण्यासाठी परीक्षा निवडा','ವಿದ್ಯಾರ್ಥಿಗಳನ್ನು ನಿಯೋಜಿಸಲು ಪರೀಕ್ಷೆ ಆಯ್ಕೆಮಾಡಿ','વિદ્યાર્થીઓને સોંપવા માટે પરીક્ષા પસંદ કરો','மாணவர்களை ஒதுக்க ஒரு தேர்வைத் தேர்ந்தெடுக்கவும்'),(7804,'paper_check_hint','Select an exam to see which students have submitted and to review their answers.','কোন শিক্ষার্থীরা জমা দিয়েছে তা দেখতে ও তাদের উত্তর যাচাই করতে একটি পরীক্ষা বেছে নিন।','यह देखने के लिए कि किन छात्रों ने सबमिट किया है और उनके उत्तर जाँचने के लिए परीक्षा चुनें।','कोणत्या विद्यार्थ्यांनी सबमिट केले ते पाहण्यासाठी आणि त्यांची उत्तरे तपासण्यासाठी परीक्षा निवडा.','ಯಾವ ವಿದ್ಯಾರ್ಥಿಗಳು ಸಲ್ಲಿಸಿದ್ದಾರೆ ಎಂದು ನೋಡಲು ಮತ್ತು ಅವರ ಉತ್ತರಗಳನ್ನು ಪರಿಶೀಲಿಸಲು ಪರೀಕ್ಷೆ ಆಯ್ಕೆಮಾಡಿ.','કયા વિદ્યાર્થીઓએ સબમિટ કર્યું છે તે જોવા અને તેમના જવાબો તપાસવા માટે પરીક્ષા પસંદ કરો.','எந்த மாணவர்கள் சமர்ப்பித்துள்ளனர் என்பதைக் காணவும் அவர்களின் பதில்களைப் பார்க்கவும் ஒரு தேர்வைத் தேர்ந்தெடுக்கவும்.'),(7805,'select_an_exam_to_see_results','Select An Exam To See Results','ফলাফল দেখতে একটি পরীক্ষা বেছে নিন','परिणाम देखने के लिए परीक्षा चुनें','निकाल पाहण्यासाठी परीक्षा निवडा','ಫಲಿತಾಂಶ ನೋಡಲು ಪರೀಕ್ಷೆ ಆಯ್ಕೆಮಾಡಿ','પરિણામ જોવા માટે પરીક્ષા પસંદ કરો','முடிவுகளைக் காண ஒரு தேர்வைத் தேர்ந்தெடுக்கவும்'),(7806,'classes','Classes','শ্রেণিসমূহ','कक्षाएँ','वर्ग','ತರಗತಿಗಳು','વર્ગો','வகுப்புகள்'),(7807,'no_class_selected','No Class Selected','কোনো শ্রেণি নির্বাচিত হয়নি','कोई कक्षा चयनित नहीं','वर्ग निवडलेला नाही','ತರಗತಿ ಆಯ್ಕೆ ಮಾಡಿಲ್ಲ','કોઈ વર્ગ પસંદ કર્યો નથી','வகுப்பு தேர்ந்தெடுக்கப்படவில்லை'),(7808,'marks_pending','Marks Pending','নম্বর বাকি','अंक लंबित','गुण प्रलंबित','ಅಂಕಗಳು ಬಾಕಿ','ગુણ બાકી','மதிப்பெண்கள் நிலுவையில்'),(7809,'preview','Preview','প্রিভিউ','पूर्वावलोकन','पूर्वावलोकन','ಪೂರ್ವವೀಕ್ಷಣೆ','પૂર્વાવલોકન','முன்னோட்டம்'),(7810,'written_exam_workflow_hint','Steps: Add exam (optionally email the schedule), enter marks after the exam, check the tabulation sheet, then publish the results (students and parents get an email).','ধাপসমূহ: পরীক্ষা যোগ করুন (ইচ্ছা হলে সময়সূচি ইমেল করুন), পরীক্ষার পর নম্বর লিখুন, ট্যাবুলেশন শিট দেখুন, তারপর ফলাফল প্রকাশ করুন (শিক্ষার্থী ও অভিভাবকরা ইমেল পাবেন)।','चरण: परीक्षा जोड़ें (चाहें तो कार्यक्रम ईमेल करें), परीक्षा के बाद अंक दर्ज करें, टैबुलेशन शीट जाँचें, फिर परिणाम प्रकाशित करें (छात्रों और अभिभावकों को ईमेल मिलता है)।','पायऱ्या: परीक्षा जोडा (हवे असल्यास वेळापत्रक ईमेल करा), परीक्षेनंतर गुण भरा, टॅब्युलेशन शीट तपासा, मग निकाल प्रकाशित करा (विद्यार्थी आणि पालकांना ईमेल मिळतो).','ಹಂತಗಳು: ಪರೀಕ್ಷೆ ಸೇರಿಸಿ (ಬೇಕಾದರೆ ವೇಳಾಪಟ್ಟಿ ಇಮೇಲ್ ಮಾಡಿ), ಪರೀಕ್ಷೆಯ ನಂತರ ಅಂಕಗಳನ್ನು ನಮೂದಿಸಿ, ಟ್ಯಾಬುಲೇಶನ್ ಶೀಟ್ ಪರಿಶೀಲಿಸಿ, ನಂತರ ಫಲಿತಾಂಶ ಪ್ರಕಟಿಸಿ (ವಿದ್ಯಾರ್ಥಿಗಳು ಮತ್ತು ಪೋಷಕರಿಗೆ ಇಮೇಲ್ ಹೋಗುತ್ತದೆ).','પગલાં: પરીક્ષા ઉમેરો (સમયપત્રક ઈમેલ કરવું વૈકલ્પિક), પરીક્ષા પછી ગુણ દાખલ કરો, ટેબ્યુલેશન શીટ તપાસો, પછી પરિણામ પ્રકાશિત કરો (વિદ્યાર્થીઓ અને વાલીઓને ઈમેલ મળશે).','படிகள்: தேர்வைச் சேர் (விருப்பமாக அட்டவணையை மின்னஞ்சலில் அனுப்பு), தேர்வுக்குப் பிறகு மதிப்பெண்களை உள்ளிடு, அட்டவணைத் தாளைச் சரிபார், பின்னர் முடிவுகளை வெளியிடு (மாணவர்களுக்கும் பெற்றோருக்கும் மின்னஞ்சல் செல்லும்).'),(8010,'total_marks_must_be_more_than_0','Total marks must be more than 0','মোট নম্বর ০ এর বেশি হতে হবে','कुल अंक 0 से अधिक होने चाहिए','एकूण गुण 0 पेक्षा जास्त असावेत','ಒಟ್ಟು ಅಂಕಗಳು 0 ಕ್ಕಿಂತ ಹೆಚ್ಚಿರಬೇಕು','કુલ ગુણ 0 કરતાં વધારે હોવા જોઈએ','மொத்த மதிப்பெண்கள் 0 ஐ விட அதிகமாக இருக்க வேண்டும்'),(8006,'this_exam_has_closed','This Exam Has Closed','এই পরীক্ষা বন্ধ হয়ে গেছে','यह परीक्षा बंद हो गई है','ही परीक्षा बंद झाली आहे','ಈ ಪರೀಕ್ಷೆ ಮುಗಿದಿದೆ','આ પરીક્ષા બંધ થઈ ગઈ છે','இந்தத் தேர்வு மூடப்பட்டுவிட்டது'),(8001,'student_not_assigned_to_this_exam','Student Not Assigned To This Exam','শিক্ষার্থীকে এই পরীক্ষায় নির্ধারণ করা হয়নি','छात्र को इस परीक्षा में असाइन नहीं किया गया','विद्यार्थ्याला ही परीक्षा नेमलेली नाही','ವಿದ್ಯಾರ್ಥಿಯನ್ನು ಈ ಪರೀಕ್ಷೆಗೆ ನಿಯೋಜಿಸಿಲ್ಲ','વિદ્યાર્થી આ પરીક્ષામાં સોંપાયેલ નથી','இந்தத் தேர்வுக்கு மாணவர் ஒதுக்கப்படவில்லை'),(7994,'result_not_available','Result Not Available','ফলাফল পাওয়া যায়নি','परिणाम उपलब्ध नहीं','निकाल उपलब्ध नाही','ಫಲಿತಾಂಶ ಲಭ್ಯವಿಲ್ಲ','પરિણામ ઉપલબ્ધ નથી','முடிவு கிடைக்கவில்லை'),(7991,'questions_are_locked_because_students_have_started','Questions are locked because students have started this exam.','শিক্ষার্থীরা এই পরীক্ষা শুরু করেছে, তাই প্রশ্নগুলি লক করা আছে।','प्रश्न लॉक हैं क्योंकि छात्रों ने यह परीक्षा शुरू कर दी है।','विद्यार्थ्यांनी ही परीक्षा सुरू केल्यामुळे प्रश्न लॉक आहेत.','ವಿದ್ಯಾರ್ಥಿಗಳು ಈ ಪರೀಕ್ಷೆಯನ್ನು ಆರಂಭಿಸಿರುವುದರಿಂದ ಪ್ರಶ್ನೆಗಳನ್ನು ಲಾಕ್ ಮಾಡಲಾಗಿದೆ.','વિદ્યાર્થીઓએ આ પરીક્ષા શરૂ કરી દીધી હોવાથી પ્રશ્નો લૉક છે.','மாணவர்கள் இந்தத் தேர்வைத் தொடங்கியதால் கேள்விகள் பூட்டப்பட்டுள்ளன.'),(7983,'preview_reminder','Preview Reminder','অনুস্মারক প্রিভিউ','अनुस्मारक पूर्वावलोकन','स्मरणपत्र पूर्वावलोकन','ಜ್ಞಾಪನೆ ಪೂರ್ವವೀಕ್ಷಣೆ','રિમાઇન્ડરનું પૂર્વાવલોકન','நினைவூட்டல் முன்னோட்டம்'),(7976,'no_marks_entered_yet','No Marks Entered Yet','এখনও কোনো নম্বর এন্ট্রি হয়নি','अभी तक कोई अंक दर्ज नहीं','अजून कोणतेही गुण भरलेले नाहीत','ಇನ್ನೂ ಅಂಕಗಳನ್ನು ನಮೂದಿಸಿಲ್ಲ','હજી કોઈ ગુણ દાખલ થયા નથી','இன்னும் மதிப்பெண்கள் உள்ளிடப்படவில்லை'),(7954,'email_remaining_results','Email remaining results','বাকি ফলাফল ইমেল করুন','शेष परिणाम ईमेल करें','उर्वरित निकाल ईमेल करा','ಉಳಿದ ಫಲಿತಾಂಶಗಳನ್ನು ಇಮೇಲ್ ಮಾಡಿ','બાકીના પરિણામો ઈમેલ કરો','மீதமுள்ள முடிவுகளை மின்னஞ்சலில் அனுப்பு'),(7952,'email_failed','Email Failed','ইমেল ব্যর্থ','ईमेल विफल','ईमेल अयशस्वी','ಇಮೇಲ್ ವಿಫಲವಾಗಿದೆ','ઈમેલ નિષ્ફળ','மின்னஞ்சல் தோல்வி'),(7762,'select_standard','Select Standard','শ্রেণি বেছে নিন','कक्षा चुनें','इयत्ता निवडा','ತರಗತಿ ಆಯ್ಕೆಮಾಡಿ','ધોરણ પસંદ કરો','வகுப்பைத் தேர்ந்தெடு'),(7763,'source','Source','উৎস','स्रोत','स्रोत','ಮೂಲ','સ્રોત','மூலம்'),(7756,'phrase_is_required','Phrase is required','বাক্যাংশ আবশ্যক','वाक्यांश आवश्यक है','वाक्य आवश्यक आहे','ಪದಗುಚ್ಛ ಅಗತ್ಯವಿದೆ','શબ્દસમૂહ જરૂરી છે','சொற்றொடர் தேவை'),(7750,'no_subjects_yet','No subjects yet','এখনও কোনো বিষয় নেই','अभी कोई विषय नहीं','अद्याप कोणतेही विषय नाहीत','ಇನ್ನೂ ಯಾವುದೇ ವಿಷಯಗಳಿಲ್ಲ','હજી કોઈ વિષય નથી','இதுவரை பாடங்கள் இல்லை'),(7740,'import','Import','আমদানি করুন','आयात करें','आयात करा','ಆಮದು ಮಾಡಿ','આયાત કરો','இறக்குமதி'),(7741,'installment','Installment','কিস্তি','किश्त','हप्ता','ಕಂತು','હપ્તો','தவணை'),(7735,'enquiry_no','Enquiry No','অনুসন্ধান নম্বর','पूछताछ संख्या','चौकशी क्रमांक','ವಿಚಾರಣೆ ಸಂಖ್ಯೆ','પૂછપરછ નંબર','விசாரணை எண்'),(7729,'email_already_in_use','This email is already in use','এই ইমেল ইতিমধ্যে ব্যবহৃত হচ্ছে','यह ईमेल पहले से उपयोग में है','हा ईमेल आधीच वापरात आहे','ಈ ಇಮೇಲ್ ಈಗಾಗಲೇ ಬಳಕೆಯಲ್ಲಿದೆ','આ ઈમેલ પહેલેથી વપરાશમાં છે','இந்த மின்னஞ்சல் ஏற்கனவே பயன்பாட்டில் உள்ளது'),(7720,'course','Course','কোর্স','कोर्स','कोर्स','ಕೋರ್ಸ್','કોર્સ','பாடநெறி'),(7712,'add_activity_note','Add Activity Note','কার্যকলাপ নোট যোগ করুন','गतिविधि नोट जोड़ें','क्रियाकलाप टीप जोडा','ಚಟುವಟಿಕೆ ಟಿಪ್ಪಣಿ ಸೇರಿಸಿ','પ્રવૃત્તિ નોંધ ઉમેરો','செயல்பாட்டுக் குறிப்பு சேர்'),(8012,'theme_and_colours','Theme & Colours','থিম ও রং','थीम और रंग','थीम आणि रंग','ಥೀಮ್ ಮತ್ತು ಬಣ್ಣಗಳು','થીમ અને રંગો','தீம் & வண்ணங்கள்'),(8013,'choose_a_colour_theme','Choose a colour theme','রঙের থিম বেছে নিন','रंग थीम चुनें','रंग थीम निवडा','ಬಣ್ಣದ ಥೀಮ್ ಆಯ್ಕೆಮಾಡಿ','રંગ થીમ પસંદ કરો','வண்ணத் தீமைத் தேர்ந்தெடு'),(8014,'original_look','original look','আগের রূপ','पुराना रूप','मूळ रूप','ಮೂಲ ನೋಟ','મૂળ દેખાવ','பழைய தோற்றம்'),(8015,'customise','Customise','নিজের মতো সাজান','अनुकूलित करें','सानुकूल करा','ಕಸ್ಟಮೈಸ್ ಮಾಡಿ','કસ્ટમાઇઝ કરો','தனிப்பயனாக்கு'),(8016,'use_my_own_colours','Use my own colours','নিজের রং ব্যবহার করুন','अपने रंग इस्तेमाल करें','माझे स्वतःचे रंग वापरा','ನನ್ನ ಸ್ವಂತ ಬಣ್ಣಗಳನ್ನು ಬಳಸಿ','મારા પોતાના રંગો વાપરો','என் சொந்த வண்ணங்களைப் பயன்படுத்து'),(8017,'main_colour','Main colour','প্রধান রং','मुख्य रंग','मुख्य रंग','ಮುಖ್ಯ ಬಣ್ಣ','મુખ્ય રંગ','முதன்மை வண்ணம்'),(8018,'second_colour','Second colour','দ্বিতীয় রং','दूसरा रंग','दुसरा रंग','ಎರಡನೇ ಬಣ್ಣ','બીજો રંગ','இரண்டாம் வண்ணம்'),(8019,'font','Font','ফন্ট','फ़ॉन्ट','फॉन्ट','ಫಾಂಟ್','ફૉન્ટ','எழுத்துரு'),(8020,'rounded_and_friendly','rounded and friendly','গোলাকার ও সুন্দর','गोल और सुंदर','गोलसर आणि सुंदर','ದುಂಡಾದ ಮತ್ತು ಸ್ನೇಹಪರ','ગોળ અને સુંદર','வட்டமான, இனிமையான'),(8021,'playful','playful','মজাদার','मज़ेदार','खेळकर','ಆಟವಾಡುವ ಶೈಲಿ','રમતિયાળ','விளையாட்டுத்தனமான'),(8022,'save_theme','Save theme','থিম সংরক্ষণ করুন','थीम सहेजें','थीम जतन करा','ಥೀಮ್ ಉಳಿಸಿ','થીમ સાચવો','தீமைச் சேமி'),(8023,'theme_saved','Theme saved','থিম সংরক্ষিত হয়েছে','थीम सहेज ली गई','थीम जतन झाली','ಥೀಮ್ ಉಳಿಸಲಾಗಿದೆ','થીમ સાચવી','தீம் சேமிக்கப்பட்டது'),(8024,'theme_applies_to_everyone_hint','The theme applies to every user, including the student portal and the login page.','এই থিম ছাত্র পোর্টাল ও লগইন পেজ সহ সব ব্যবহারকারীর জন্য প্রযোজ্য।','यह थीम सभी उपयोगकर्ताओं पर लागू होती है, छात्र पोर्टल और लॉगिन पेज सहित।','ही थीम सर्व वापरकर्त्यांना लागू होते, विद्यार्थी पोर्टल आणि लॉगिन पृष्ठासह.','ಈ ಥೀಮ್ ವಿದ್ಯಾರ್ಥಿ ಪೋರ್ಟಲ್ ಮತ್ತು ಲಾಗಿನ್ ಪುಟ ಸೇರಿದಂತೆ ಎಲ್ಲಾ ಬಳಕೆದಾರರಿಗೆ ಅನ್ವಯಿಸುತ್ತದೆ.','આ થીમ વિદ્યાર્થી પોર્ટલ અને લૉગિન પેજ સહિત બધા વપરાશકર્તાઓને લાગુ પડે છે.','இந்தத் தீம் மாணவர் போர்டல், உள்நுழைவுப் பக்கம் உட்பட அனைவருக்கும் பொருந்தும்.'),(8028,'teacher_dashboard','Teacher Dashboard','শিক্ষক ড্যাশবোর্ড','शिक्षक डैशबोर्ड','शिक्षक डॅशबोर्ड','ಶಿಕ್ಷಕರ ಡ್ಯಾಶ್‌ಬೋರ್ಡ್','શિક્ષક ડેશબોર્ડ','ஆசிரியர் டாஷ்போர்டு'),(8029,'my_timetable','My Timetable','আমার টাইমটেবিল','मेरा टाइमटेबल','माझे वेळापत्रक','ನನ್ನ ವೇಳಾಪಟ್ಟಿ','મારું ટાઇમટેબલ','என் கால அட்டவணை'),(8030,'my_students','My Students','আমার শিক্ষার্থী','मेरे छात्र','माझे विद्यार्थी','ನನ್ನ ವಿದ್ಯಾರ್ಥಿಗಳು','મારા વિદ્યાર્થીઓ','என் மாணவர்கள்'),(8031,'mark_attendance','Mark Attendance','উপস্থিতি চিহ্নিত করুন','उपस्थिति दर्ज करें','उपस्थिती नोंदवा','ಹಾಜರಾತಿ ದಾಖಲಿಸಿ','હાજરી નોંધો','வருகை பதிவு செய்'),(8032,'noticeboard','Noticeboard','নোটিসবোর্ড','सूचना पट','सूचना फलक','ಸೂಚನಾ ಫಲಕ','નોટિસ બોર્ડ','அறிவிப்புப் பலகை'),(8033,'papers_to_check','Papers to check','যাচাই করার খাতা','जाँचने के लिए पेपर','तपासायचे पेपर','ಪರಿಶೀಲಿಸಬೇಕಾದ ಉತ್ತರ ಪತ್ರಿಕೆಗಳು','તપાસવાના પેપર','திருத்த வேண்டிய தாள்கள்'),(8034,'todays_batches','Today\'s batches','আজকের ব্যাচ','आज के बैच','आजच्या बॅचेस','ಇಂದಿನ ಬ್ಯಾಚ್‌ಗಳು','આજના બેચ','இன்றைய பேட்ச்கள்'),(8035,'latest_notices','Latest Notices','সাম্প্রতিক নোটিস','नवीनतम सूचनाएँ','ताज्या सूचना','ಇತ್ತೀಚಿನ ಸೂಚನೆಗಳು','તાજેતરની સૂચનાઓ','சமீபத்திய அறிவிப்புகள்'),(8036,'my_weekly_timetable','My Weekly Timetable','আমার সাপ্তাহিক টাইমটেবিল','मेरा साप्ताहिक टाइमटेबल','माझे साप्ताहिक वेळापत्रक','ನನ್ನ ವಾರದ ವೇಳಾಪಟ್ಟಿ','મારું સાપ્તાહિક ટાઇમટેબલ','என் வாராந்திர கால அட்டவணை'),(8037,'day','Day','দিন','दिन','दिवस','ದಿನ','દિવસ','நாள்'),(8038,'batches','Batches','ব্যাচ','बैच','बॅचेस','ಬ್ಯಾಚ್‌ಗಳು','બેચ','பேட்ச்கள்'),(8039,'monday','','','','','','',''),(8040,'tuesday','','','','','','',''),(8041,'wednesday','','','','','','',''),(8042,'thursday','','','','','','',''),(8043,'friday','','','','','','',''),(8044,'saturday','','','','','','',''),(8045,'sunday','','','','','','',''),(8046,'subjects_i_teach','Subjects I Teach','আমি যে বিষয় পড়াই','मेरे पढ़ाए विषय','मी शिकवत असलेले विषय','ನಾನು ಬೋಧಿಸುವ ವಿಷಯಗಳು','હું ભણાવું છું તે વિષયો','நான் கற்பிக்கும் பாடங்கள்'),(8047,'all_classes','All Classes','সব ক্লাস','सभी कक्षाएँ','सर्व वर्ग','ಎಲ್ಲಾ ತರಗತಿಗಳು','બધા વર્ગો','அனைத்து வகுப்புகள்'),(8048,'parent_phone','Parent Phone','অভিভাবকের ফোন','अभिभावक का फ़ोन','पालकांचा फोन','ಪೋಷಕರ ಫೋನ್','વાલીનો ફોન','பெற்றோர் தொலைபேசி'),(8049,'girl','Girl','মেয়ে','लड़की','मुलगी','ಹುಡುಗಿ','છોકરી','பெண்'),(8050,'boy','Boy','ছেলে','लड़का','मुलगा','ಹುಡುಗ','છોકરો','ஆண்'),(8051,'attendance_edit_window_hint','Class teachers can mark or correct attendance for today and the previous 7 days.','ক্লাস টিচাররা আজ এবং গত ৭ দিনের উপস্থিতি চিহ্নিত বা সংশোধন করতে পারেন।','क्लास टीचर आज और पिछले 7 दिनों की उपस्थिति दर्ज या सुधार सकते हैं।','वर्गशिक्षक आज आणि मागील ७ दिवसांची उपस्थिती नोंदवू किंवा दुरुस्त करू शकतात.','ತರಗತಿ ಶಿಕ್ಷಕರು ಇಂದಿನ ಮತ್ತು ಹಿಂದಿನ 7 ದಿನಗಳ ಹಾಜರಾತಿಯನ್ನು ದಾಖಲಿಸಬಹುದು ಅಥವಾ ಸರಿಪಡಿಸಬಹುದು.','વર્ગ શિક્ષકો આજની અને પાછલા 7 દિવસની હાજરી નોંધી અથવા સુધારી શકે છે.','வகுப்பு ஆசிரியர்கள் இன்றைய மற்றும் முந்தைய 7 நாட்களுக்கான வருகையைப் பதிவு செய்யலாம் அல்லது திருத்தலாம்.'),(8052,'no_online_exams_for_your_subjects','No online exams for your subjects yet','আপনার বিষয়ের জন্য এখনও কোনো অনলাইন পরীক্ষা নেই','आपके विषयों की कोई ऑनलाइन परीक्षा अभी नहीं है','तुमच्या विषयांसाठी अद्याप ऑनलाइन परीक्षा नाहीत','ನಿಮ್ಮ ವಿಷಯಗಳಿಗೆ ಇನ್ನೂ ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆಗಳಿಲ್ಲ','તમારા વિષયો માટે હજી કોઈ ઓનલાઇન પરીક્ષા નથી','உங்கள் பாடங்களுக்கு இதுவரை ஆன்லைன் தேர்வுகள் இல்லை'),(8053,'online_exams','Online Exams','অনলাইন পরীক্ষা','ऑनलाइन परीक्षाएँ','ऑनलाइन परीक्षा','ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆಗಳು','ઓનલાઇન પરીક્ષાઓ','ஆன்லைன் தேர்வுகள்'),(8054,'holidays','Holidays','ছুটির দিন','छुट्टियाँ','सुट्ट्या','ರಜಾದಿನಗಳು','રજાઓ','விடுமுறைகள்'),(8055,'designation','Designation','পদবি','पदनाम','पदनाम','ಹುದ್ದೆ','હોદ્દો','பதவி'),(8056,'joining_date','Joining Date','যোগদানের তারিখ','जॉइनिंग तारीख','रुजू होण्याची तारीख','ಸೇರಿದ ದಿನಾಂಕ','જોડાવાની તારીખ','சேர்ந்த தேதி'),(8057,'parent_dashboard','Parent Dashboard','অভিভাবক ড্যাশবোর্ড','अभिभावक डैशबोर्ड','पालक डॅशबोर्ड','ಪೋಷಕರ ಡ್ಯಾಶ್‌ಬೋರ್ಡ್','વાલી ડેશબોર્ડ','பெற்றோர் டாஷ்போர்டு'),(8058,'fees','Fees','ফি','फीस','फी','ಶುಲ್ಕಗಳು','ફી','கட்டணங்கள்'),(8059,'showing','Showing','দেখানো হচ্ছে','दिखा रहे हैं','दाखवत आहे','ತೋರಿಸಲಾಗುತ್ತಿದೆ','બતાવી રહ્યા છે','காட்டப்படுவது'),(8060,'attendance_this_month','Attendance This Month','এই মাসের উপস্থিতি','इस माह की उपस्थिति','या महिन्याची उपस्थिती','ಈ ತಿಂಗಳ ಹಾಜರಾತಿ','આ મહિનાની હાજરી','இந்த மாத வருகை'),(8061,'fee_balance_due','Fee Balance Due','বকেয়া ফি','बकाया फीस','फी थकबाकी','ಶುಲ್ಕ ಬಾಕಿ','બાકી ફી','செலுத்த வேண்டிய கட்டண நிலுவை'),(8062,'online_exam_results','Online Exam Results','অনলাইন পরীক্ষার ফলাফল','ऑनलाइन परीक्षा परिणाम','ऑनलाइन परीक्षा निकाल','ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆಯ ಫಲಿತಾಂಶಗಳು','ઓનલાઇન પરીક્ષા પરિણામ','ஆன்லைன் தேர்வு முடிவுகள்'),(8063,'no_exams_assigned_yet','No Exams Assigned Yet','এখনও কোনো পরীক্ষা দেওয়া হয়নি','अभी तक कोई परीक्षा नहीं सौंपी गई','अद्याप कोणतीही परीक्षा दिलेली नाही','ಇನ್ನೂ ಯಾವುದೇ ಪರೀಕ್ಷೆ ನಿಯೋಜಿಸಿಲ್ಲ','હજી સુધી કોઈ પરીક્ષા સોંપાઈ નથી','தேர்வுகள் இதுவரை ஒதுக்கப்படவில்லை'),(8064,'your_child_takes_the_exam_from_the_student_login','Your child takes the online exam from the student login.','আপনার সন্তান শিক্ষার্থী লগইন থেকে অনলাইন পরীক্ষা দেবে।','आपका बच्चा ऑनलाइन परीक्षा छात्र लॉगिन से देता है।','तुमचे मूल ऑनलाइन परीक्षा विद्यार्थी लॉगिनमधून देते.','ನಿಮ್ಮ ಮಗು ವಿದ್ಯಾರ್ಥಿ ಲಾಗಿನ್‌ನಿಂದ ಆನ್‌ಲೈನ್ ಪರೀಕ್ಷೆ ಬರೆಯುತ್ತದೆ.','તમારું બાળક સ્ટુડન્ટ લોગિનથી ઓનલાઇન પરીક્ષા આપે છે.','உங்கள் குழந்தை மாணவர் உள்நுழைவிலிருந்து ஆன்லைன் தேர்வை எழுதுவார்.'),(8065,'present','Present','উপস্থিত','उपस्थित','उपस्थित','ಹಾಜರು','હાજર','வந்தவர்'),(8066,'absent','Absent','অনুপস্থিত','अनुपस्थित','अनुपस्थित','ಗೈರುಹಾಜರು','ગેરહાજર','வரவில்லை'),(8067,'no_attendance_marked_this_month','No Attendance Marked This Month','এই মাসে কোনো উপস্থিতি চিহ্নিত হয়নি','इस माह कोई उपस्थिति दर्ज नहीं','या महिन्यात उपस्थिती नोंदवलेली नाही','ಈ ತಿಂಗಳು ಹಾಜರಾತಿ ದಾಖಲಾಗಿಲ್ಲ','આ મહિને કોઈ હાજરી નોંધાઈ નથી','இந்த மாதம் வருகை பதிவு செய்யப்படவில்லை'),(8068,'balance_due','Balance Due','বকেয়া ব্যালেন্স','बकाया राशि','थकबाकी','ಬಾಕಿ ಮೊತ್ತ','બાકી રકમ','செலுத்த வேண்டிய நிலுவை'),(8069,'no_payments_recorded_yet','No Payments Recorded Yet','এখনও কোনো পেমেন্ট নথিভুক্ত হয়নি','अभी तक कोई भुगतान दर्ज नहीं','अद्याप कोणतेही पेमेंट नोंदवलेले नाही','ಇನ್ನೂ ಯಾವುದೇ ಪಾವತಿ ದಾಖಲಾಗಿಲ್ಲ','હજી સુધી કોઈ ચુકવણી નોંધાઈ નથી','கட்டணங்கள் இதுவரை பதிவு செய்யப்படவில்லை'),(8070,'contact_the_office_for_fee_receipts','Contact the office for fee receipts.','ফি রসিদের জন্য অফিসে যোগাযোগ করুন।','फीस रसीद के लिए कार्यालय से संपर्क करें।','फी पावत्यांसाठी कार्यालयाशी संपर्क साधा.','ಶುಲ್ಕ ರಸೀದಿಗಳಿಗಾಗಿ ಕಚೇರಿಯನ್ನು ಸಂಪರ್ಕಿಸಿ.','ફી રસીદ માટે ઓફિસનો સંપર્ક કરો.','கட்டண ரசீதுகளுக்கு அலுவலகத்தைத் தொடர்பு கொள்ளவும்.'),(8071,'children','Children','সন্তান','बच्चे','मुले','ಮಕ್ಕಳು','બાળકો','குழந்தைகள்'),(8072,'my_attendance','My Attendance','আমার উপস্থিতি','मेरी उपस्थिति','माझी उपस्थिती','ನನ್ನ ಹಾಜರಾತಿ','મારી હાજરી','என் வருகை'),(8073,'you_do_not_have_access_to_this_page','You do not have access to this page','এই পেজে আপনার প্রবেশাধিকার নেই','आपको इस पेज की अनुमति नहीं है','तुम्हाला या पेजवर प्रवेश नाही','ಈ ಪುಟಕ್ಕೆ ನಿಮಗೆ ಪ್ರವೇಶವಿಲ್ಲ','તમારી પાસે આ પેજનો એક્સેસ નથી','இந்தப் பக்கத்தை அணுக உங்களுக்கு அனுமதி இல்லை'),(8074,'menu_permissions','Menu Permissions','মেনু অনুমতি','मेनू अनुमतियाँ','मेनू परवानग्या','ಮೆನು ಅನುಮತಿಗಳು','મેનુ પરવાનગીઓ','மெனு அனுமதிகள்'),(8075,'menu_permissions_hint','Choose which menus teachers, parents and students can see. A switched-off menu is hidden and its page is blocked. Dashboard and profile are always available.','শিক্ষক, অভিভাবক ও শিক্ষার্থীরা কোন মেনু দেখতে পাবেন তা বেছে নিন। বন্ধ করা মেনু লুকানো থাকবে এবং তার পেজ ব্লক হবে। ড্যাশবোর্ড ও প্রোফাইল সবসময় পাওয়া যায়।','चुनें कि शिक्षक, अभिभावक और छात्र कौन-से मेनू देख सकते हैं। बंद किया गया मेनू छिप जाता है और उसका पेज बंद हो जाता है। डैशबोर्ड और प्रोफ़ाइल हमेशा उपलब्ध रहते हैं।','शिक्षक, पालक आणि विद्यार्थी कोणते मेनू पाहू शकतात ते निवडा. बंद केलेला मेनू लपवला जातो आणि त्याचे पेज ब्लॉक केले जाते. डॅशबोर्ड आणि प्रोफाइल नेहमी उपलब्ध असतात.','ಶಿಕ್ಷಕರು, ಪೋಷಕರು ಮತ್ತು ವಿದ್ಯಾರ್ಥಿಗಳು ಯಾವ ಮೆನುಗಳನ್ನು ನೋಡಬಹುದು ಎಂಬುದನ್ನು ಆಯ್ಕೆಮಾಡಿ. ಆಫ್ ಮಾಡಿದ ಮೆನು ಮರೆಯಾಗುತ್ತದೆ ಮತ್ತು ಅದರ ಪುಟವನ್ನು ನಿರ್ಬಂಧಿಸಲಾಗುತ್ತದೆ. ಡ್ಯಾಶ್‌ಬೋರ್ಡ್ ಮತ್ತು ಪ್ರೊಫೈಲ್ ಯಾವಾಗಲೂ ಲಭ್ಯವಿರುತ್ತವೆ.','શિક્ષકો, વાલીઓ અને વિદ્યાર્થીઓ કયા મેનુ જોઈ શકે તે પસંદ કરો. બંધ કરેલ મેનુ છુપાઈ જશે અને તેનું પેજ બ્લોક થશે. ડેશબોર્ડ અને પ્રોફાઇલ હંમેશા ઉપલબ્ધ રહેશે.','ஆசிரியர்கள், பெற்றோர்கள் மற்றும் மாணவர்கள் எந்த மெனுக்களைப் பார்க்கலாம் என்பதைத் தேர்வு செய்யவும். அணைக்கப்பட்ட மெனு மறைக்கப்படும், அதன் பக்கமும் தடுக்கப்படும். டாஷ்போர்டு மற்றும் சுயவிவரம் எப்போதும் கிடைக்கும்.'),(8076,'always_on','always on','সবসময় চালু','हमेशा चालू','नेहमी चालू','ಯಾವಾಗಲೂ ಆನ್','હંમેશા ચાલુ','எப்போதும் இயக்கத்தில்'),(8077,'save_permissions','Save Permissions','অনুমতি সংরক্ষণ করুন','अनुमतियाँ सहेजें','परवानग्या जतन करा','ಅನುಮತಿಗಳನ್ನು ಉಳಿಸಿ','પરવાનગીઓ સાચવો','அனுமதிகளைச் சேமி'),(8078,'reset_to_default','Reset To Default','ডিফল্টে রিসেট করুন','डिफ़ॉल्ट पर रीसेट करें','डीफॉल्टवर रीसेट करा','ಡೀಫಾಲ್ಟ್‌ಗೆ ಮರುಹೊಂದಿಸಿ','ડિફોલ્ટ પર રીસેટ કરો','இயல்புநிலைக்கு மீட்டமை'),(8079,'attendance_saved','Attendance Saved','উপস্থিতি সংরক্ষিত হয়েছে','उपस्थिति सहेजी गई','उपस्थिती जतन केली','ಹಾಜರಾತಿ ಉಳಿಸಲಾಗಿದೆ','હાજરી સાચવાઈ','வருகை சேமிக்கப்பட்டது'),(8080,'you_are_not_the_class_teacher_of_this_class','You are not the class teacher of this class','আপনি এই ক্লাসের ক্লাস টিচার নন','आप इस कक्षा के क्लास टीचर नहीं हैं','तुम्ही या वर्गाचे वर्गशिक्षक नाही','ನೀವು ಈ ತರಗತಿಯ ತರಗತಿ ಶಿಕ್ಷಕರಲ್ಲ','તમે આ વર્ગના વર્ગ શિક્ષક નથી','நீங்கள் இந்த வகுப்பின் வகுப்பு ஆசிரியர் அல்ல'),(8081,'attendance_cannot_be_marked_for_a_future_date','Attendance cannot be marked for a future date','ভবিষ্যতের তারিখের জন্য উপস্থিতি চিহ্নিত করা যাবে না','भविष्य की तारीख के लिए उपस्थिति दर्ज नहीं की जा सकती','भविष्यातील तारखेसाठी उपस्थिती नोंदवता येत नाही','ಭವಿಷ್ಯದ ದಿನಾಂಕಕ್ಕೆ ಹಾಜರಾತಿ ದಾಖಲಿಸಲು ಸಾಧ್ಯವಿಲ್ಲ','ભવિષ્યની તારીખ માટે હાજરી નોંધી શકાતી નથી','எதிர்கால தேதிக்கு வருகையைப் பதிவு செய்ய முடியாது'),(8082,'attendance_older_than_7_days_is_locked','Attendance older than 7 days is locked','৭ দিনের পুরনো উপস্থিতি লক করা আছে','7 दिन से पुरानी उपस्थिति लॉक है','७ दिवसांपेक्षा जुनी उपस्थिती लॉक केलेली आहे','7 ದಿನಗಳಿಗಿಂತ ಹಳೆಯ ಹಾಜರಾತಿಯನ್ನು ಲಾಕ್ ಮಾಡಲಾಗಿದೆ','7 દિવસથી જૂની હાજરી લોક થયેલ છે','7 நாட்களுக்கு முந்தைய வருகை பூட்டப்பட்டுள்ளது'),(8083,'you_do_not_teach_this_subject','You Do Not Teach This Subject','আপনি এই বিষয় পড়ান না','आप यह विषय नहीं पढ़ाते','तुम्ही हा विषय शिकवत नाही','ನೀವು ಈ ವಿಷಯವನ್ನು ಬೋಧಿಸುವುದಿಲ್ಲ','તમે આ વિષય ભણાવતા નથી','நீங்கள் இந்தப் பாடத்தைக் கற்பிப்பதில்லை'),(8084,'results_are_published_marks_are_locked','Results are published, so marks are locked. Ask the office to change them.','ফলাফল প্রকাশিত হয়েছে, তাই নম্বর লক করা আছে। পরিবর্তনের জন্য অফিসে বলুন।','परिणाम प्रकाशित हो चुके हैं, इसलिए अंक लॉक हैं। बदलने के लिए कार्यालय से कहें।','निकाल प्रसिद्ध झाला आहे, त्यामुळे गुण लॉक आहेत. बदलण्यासाठी कार्यालयाशी संपर्क साधा.','ಫಲಿತಾಂಶ ಪ್ರಕಟವಾಗಿದೆ, ಆದ್ದರಿಂದ ಅಂಕಗಳನ್ನು ಲಾಕ್ ಮಾಡಲಾಗಿದೆ. ಬದಲಾಯಿಸಲು ಕಚೇರಿಯನ್ನು ಕೇಳಿ.','પરિણામ પ્રકાશિત થયેલ છે, તેથી ગુણ લોક છે. બદલવા માટે ઓફિસને કહો.','முடிவுகள் வெளியிடப்பட்டதால் மதிப்பெண்கள் பூட்டப்பட்டுள்ளன. மாற்ற அலுவலகத்தைக் கேட்கவும்.'),(8085,'menu_permissions_saved','Menu Permissions Saved','মেনু অনুমতি সংরক্ষিত হয়েছে','मेनू अनुमतियाँ सहेजी गईं','मेनू परवानग्या जतन केल्या','ಮೆನು ಅನುಮತಿಗಳು ಉಳಿಸಲಾಗಿದೆ','મેનુ પરવાનગીઓ સાચવાઈ','மெனு அனுமதிகள் சேமிக்கப்பட்டன'),(8086,'menu_permissions_reset_to_default','Menu permissions reset to default','মেনু অনুমতি ডিফল্টে রিসেট হয়েছে','मेनू अनुमतियाँ डिफ़ॉल्ट पर रीसेट की गईं','मेनू परवानग्या डीफॉल्टवर रीसेट केल्या','ಮೆನು ಅನುಮತಿಗಳನ್ನು ಡೀಫಾಲ್ಟ್‌ಗೆ ಮರುಹೊಂದಿಸಲಾಗಿದೆ','મેનુ પરવાનગીઓ ડિફોલ્ટ પર રીસેટ થઈ','மெனு அனுமதிகள் இயல்புநிலைக்கு மீட்டமைக்கப்பட்டன'),(8087,'all_present','All Present','সবাই উপস্থিত','सभी उपस्थित','सर्व उपस्थित','ಎಲ್ಲರೂ ಹಾಜರು','બધા હાજર','அனைவரும் வந்தனர்'),(8088,'already_marked_you_can_correct_it','Already marked - you can correct it','ইতিমধ্যে চিহ্নিত - আপনি সংশোধন করতে পারেন','पहले से दर्ज - आप इसे सुधार सकते हैं','आधीच नोंदवले आहे - तुम्ही दुरुस्त करू शकता','ಈಗಾಗಲೇ ದಾಖಲಿಸಲಾಗಿದೆ - ನೀವು ಸರಿಪಡಿಸಬಹುದು','પહેલેથી નોંધાયેલ છે - તમે સુધારી શકો છો','ஏற்கனவே பதிவு செய்யப்பட்டது - திருத்தலாம்'),(8089,'invalid_date','Invalid Date','অবৈধ তারিখ','अमान्य तारीख','अवैध तारीख','ಅಮಾನ್ಯ ದಿನಾಂಕ','અમાન્ય તારીખ','தவறான தேதி'),(8090,'no_batches_assigned_to_you','No Batches Assigned To You','আপনাকে কোনো ব্যাচ দেওয়া হয়নি','आपको कोई बैच नहीं सौंपा गया','तुम्हाला कोणतीही बॅच दिलेली नाही','ನಿಮಗೆ ಯಾವುದೇ ಬ್ಯಾಚ್ ನಿಯೋಜಿಸಿಲ್ಲ','તમને કોઈ બેચ સોંપાયેલ નથી','உங்களுக்கு பேட்ச்கள் ஒதுக்கப்படவில்லை'),(8091,'no_batches_today','No Batches Today','আজ কোনো ব্যাচ নেই','आज कोई बैच नहीं','आज कोणतीही बॅच नाही','ಇಂದು ಬ್ಯಾಚ್‌ಗಳಿಲ್ಲ','આજે કોઈ બેચ નથી','இன்று பேட்ச்கள் இல்லை'),(8092,'no_children_linked_to_your_account_contact_the_office','No children are linked to your account. Please contact the office.','আপনার অ্যাকাউন্টে কোনো সন্তান যুক্ত নেই। অনুগ্রহ করে অফিসে যোগাযোগ করুন।','आपके खाते से कोई बच्चा जुड़ा नहीं है। कृपया कार्यालय से संपर्क करें।','तुमच्या खात्याशी कोणतीही मुले जोडलेली नाहीत. कृपया कार्यालयाशी संपर्क साधा.','ನಿಮ್ಮ ಖಾತೆಗೆ ಯಾವುದೇ ಮಕ್ಕಳನ್ನು ಲಿಂಕ್ ಮಾಡಿಲ್ಲ. ದಯವಿಟ್ಟು ಕಚೇರಿಯನ್ನು ಸಂಪರ್ಕಿಸಿ.','તમારા ખાતા સાથે કોઈ બાળક જોડાયેલ નથી. કૃપા કરીને ઓફિસનો સંપર્ક કરો.','உங்கள் கணக்குடன் குழந்தைகள் யாரும் இணைக்கப்படவில்லை. அலுவலகத்தைத் தொடர்பு கொள்ளவும்.'),(8093,'no_fee_details','No Fee Details','কোনো ফি বিবরণ নেই','कोई फीस विवरण नहीं','फी तपशील नाहीत','ಶುಲ್ಕ ವಿವರಗಳಿಲ್ಲ','કોઈ ફી વિગતો નથી','கட்டண விவரங்கள் இல்லை'),(8094,'no_holidays','No Holidays','কোনো ছুটি নেই','कोई छुट्टी नहीं','सुट्ट्या नाहीत','ರಜಾದಿನಗಳಿಲ್ಲ','કોઈ રજા નથી','விடுமுறைகள் இல்லை'),(8095,'no_notices','No Notices','কোনো নোটিস নেই','कोई सूचना नहीं','सूचना नाहीत','ಸೂಚನೆಗಳಿಲ್ಲ','કોઈ સૂચના નથી','அறிவிப்புகள் இல்லை'),(8096,'no_students_found','No Students Found','কোনো শিক্ষার্থী পাওয়া যায়নি','कोई छात्र नहीं मिला','विद्यार्थी आढळले नाहीत','ವಿದ್ಯಾರ್ಥಿಗಳು ಕಂಡುಬಂದಿಲ್ಲ','કોઈ વિદ્યાર્થી મળ્યો નથી','மாணவர்கள் இல்லை'),(8097,'no_subjects_assigned_to_you','No Subjects Assigned To You','আপনাকে কোনো বিষয় দেওয়া হয়নি','आपको कोई विषय नहीं सौंपा गया','तुम्हाला कोणताही विषय दिलेला नाही','ನಿಮಗೆ ಯಾವುದೇ ವಿಷಯ ನಿಯೋಜಿಸಿಲ್ಲ','તમને કોઈ વિષય સોંપાયેલ નથી','உங்களுக்கு பாடங்கள் ஒதுக்கப்படவில்லை'),(8098,'not_marked_yet_all_present_by_default','Not marked yet - everyone is present by default','এখনও চিহ্নিত হয়নি - ডিফল্টভাবে সবাই উপস্থিত','अभी दर्ज नहीं - डिफ़ॉल्ट रूप से सभी उपस्थित हैं','अद्याप नोंदवलेले नाही - सर्वजण डीफॉल्टनुसार उपस्थित आहेत','ಇನ್ನೂ ದಾಖಲಿಸಿಲ್ಲ - ಡೀಫಾಲ್ಟ್ ಆಗಿ ಎಲ್ಲರೂ ಹಾಜರು','હજી નોંધાયું નથી - ડિફોલ્ટ રૂપે બધા હાજર છે','இதுவரை பதிவு செய்யப்படவில்லை - இயல்பாக அனைவரும் வந்தவர்கள்'),(8099,'only_class_teachers_can_mark_attendance','Only class teachers can mark attendance. You are not the class teacher of any class.','শুধুমাত্র ক্লাস টিচাররা উপস্থিতি চিহ্নিত করতে পারেন। আপনি কোনো ক্লাসের ক্লাস টিচার নন।','केवल क्लास टीचर उपस्थिति दर्ज कर सकते हैं। आप किसी कक्षा के क्लास टीचर नहीं हैं।','फक्त वर्गशिक्षक उपस्थिती नोंदवू शकतात. तुम्ही कोणत्याही वर्गाचे वर्गशिक्षक नाही.','ತರಗತಿ ಶಿಕ್ಷಕರು ಮಾತ್ರ ಹಾಜರಾತಿ ದಾಖಲಿಸಬಹುದು. ನೀವು ಯಾವುದೇ ತರಗತಿಯ ತರಗತಿ ಶಿಕ್ಷಕರಲ್ಲ.','ફક્ત વર્ગ શિક્ષકો હાજરી નોંધી શકે છે. તમે કોઈ વર્ગના વર્ગ શિક્ષક નથી.','வகுப்பு ஆசிரியர்கள் மட்டுமே வருகையைப் பதிவு செய்யலாம். நீங்கள் எந்த வகுப்பிற்கும் வகுப்பு ஆசிரியர் அல்ல.'),(8100,'results_not_published_to_students_yet','Results are not published to students yet.','ফলাফল এখনও শিক্ষার্থীদের জন্য প্রকাশিত হয়নি।','परिणाम अभी छात्रों के लिए प्रकाशित नहीं हुए हैं।','निकाल अद्याप विद्यार्थ्यांसाठी प्रसिद्ध झालेला नाही.','ಫಲಿತಾಂಶಗಳನ್ನು ಇನ್ನೂ ವಿದ್ಯಾರ್ಥಿಗಳಿಗೆ ಪ್ರಕಟಿಸಲಾಗಿಲ್ಲ.','પરિણામ હજી વિદ્યાર્થીઓ માટે પ્રકાશિત થયું નથી.','முடிவுகள் இன்னும் மாணவர்களுக்கு வெளியிடப்படவில்லை.'),(8101,'save_attendance','Save Attendance','উপস্থিতি সংরক্ষণ করুন','उपस्थिति सहेजें','उपस्थिती जतन करा','ಹಾಜರಾತಿ ಉಳಿಸಿ','હાજરી સાચવો','வருகையைச் சேமி'),(8102,'this_exam_is_not_for_that_class','This exam is not for that class','এই পরীক্ষা ওই ক্লাসের জন্য নয়','यह परीक्षा उस कक्षा के लिए नहीं है','ही परीक्षा त्या वर्गासाठी नाही','ಈ ಪರೀಕ್ಷೆ ಆ ತರಗತಿಗೆ ಸಂಬಂಧಿಸಿದ್ದಲ್ಲ','આ પરીક્ષા તે વર્ગ માટે નથી','இந்தத் தேர்வு அந்த வகுப்பிற்கு உரியதல்ல'),(8103,'you_do_not_teach_this_class','You Do Not Teach This Class','আপনি এই ক্লাসে পড়ান না','आप यह कक्षा नहीं पढ़ाते','तुम्ही हा वर्ग शिकवत नाही','ನೀವು ಈ ತರಗತಿಗೆ ಬೋಧಿಸುವುದಿಲ್ಲ','તમે આ વર્ગ ભણાવતા નથી','நீங்கள் இந்த வகுப்பிற்குக் கற்பிப்பதில்லை');
/*!40000 ALTER TABLE `language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `librarian`
--

DROP TABLE IF EXISTS `librarian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `librarian` (
  `librarian_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `birthday` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sex` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `religion` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `blood_group` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` longtext CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `authentication_key` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`librarian_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `librarian`
--

LOCK TABLES `librarian` WRITE;
/*!40000 ALTER TABLE `librarian` DISABLE KEYS */;
/*!40000 ALTER TABLE `librarian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loan`
--

DROP TABLE IF EXISTS `loan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loan` (
  `loan_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `amount` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `purpose` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `l_duration` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mop` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `g_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `g_relationship` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `g_number` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `g_address` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `g_country` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `c_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `c_type` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `model` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `make` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `serial_number` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `condition` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`loan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loan`
--

LOCK TABLES `loan` WRITE;
/*!40000 ALTER TABLE `loan` DISABLE KEYS */;
INSERT INTO `loan` VALUES (3,'teacher 2','1000','I WANT TO USE TO PAY FOR MY SCHOOL CHILDREN SCHOOL FEES','Daily','MODE OF PAYMENT HERE','MR OPTIMUMLINKUP','FAMILY','NUMBER','G ADDRESS','G COUNTRY','COLLATERAL NAME','C TYPE','C MODEL','C MAKE','C SERIAL NYMEBNER','6','Daily','Fri, 07 July 2017','Pending',''),(5,'teacher','1000000','FOR WEDDING CEREMONY','Eight Months','Yearly','OMOLOLU','FAMILY','081789644','G ADDRESS','COUNTRY','c name','c type','C MOD','C MAKE','C SERIAL NYMEBNER','700000','Monthly','Wed, 12 July 2017','Pending',''),(6,'LIBRARIAN','5000','jghhgh','One Month','Daily','hkuhkjh','hjkhj','hkjh','kjhkjhj','hjkhjkhjh','khhhj','hkhhj','hj','ghi890-9ookk','hhjhj','35676543','Daily','Thu, 20 July 2017','Approved',''),(7,'Mr. Segun','50000','jkkj`','One Month','Daily','jghjgh','jh','jh','jhj','hkjh','jk','hjk','hjk','hkj','hjk','6789','Daily','Mon, 17 July 2017','Pending',''),(8,'hostel','4000','purpoae','One Month','Daily','khkj','hjk','jkhjh','hjkh','hjkh','kjhj','hjk','kj','ljhkl','hjk','67890','Daily','Tue, 18 July 2017','Pending','');
/*!40000 ALTER TABLE `loan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lookup_value`
--

DROP TABLE IF EXISTS `lookup_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lookup_value` (
  `lookup_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(64) NOT NULL,
  `value` varchar(128) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`lookup_id`),
  UNIQUE KEY `cat_val` (`category`,`value`),
  KEY `category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lookup_value`
--

LOCK TABLES `lookup_value` WRITE;
/*!40000 ALTER TABLE `lookup_value` DISABLE KEYS */;
INSERT INTO `lookup_value` VALUES (1,'medium','English',1,1),(2,'medium','Hindi',2,1),(3,'payment_type','Admission',1,1),(4,'payment_type','Installment',2,1),(5,'payment_mode','Cash',1,1),(6,'payment_mode','Online',2,1),(7,'payment_mode','Cheque',3,1);
/*!40000 ALTER TABLE `lookup_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mark`
--

DROP TABLE IF EXISTS `mark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mark` (
  `mark_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `mark_obtained` decimal(6,2) DEFAULT NULL,
  `mark_total` int(11) NOT NULL DEFAULT 100,
  `comment` longtext NOT NULL,
  `result_notified_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`mark_id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mark`
--

LOCK TABLES `mark` WRITE;
/*!40000 ALTER TABLE `mark` DISABLE KEYS */;
INSERT INTO `mark` VALUES (88,19,52,12,1,16.00,25,'',NULL),(89,19,53,12,1,11.00,25,'',NULL),(90,19,54,12,1,11.00,25,'',NULL),(91,19,55,12,1,13.00,25,'',NULL),(92,19,56,12,1,15.00,25,'',NULL),(98,21,57,13,1,20.00,25,'',NULL),(99,21,58,13,1,23.00,25,'Excellent',NULL),(100,21,59,13,1,22.00,25,'Excellent',NULL),(101,21,60,13,1,23.00,25,'Excellent',NULL),(102,21,61,13,1,21.00,25,'',NULL);
/*!40000 ALTER TABLE `mark` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `media_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `file_type` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mlink` longtext NOT NULL,
  `class_id` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teacher_id` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (1,'INTRODUCTION TO JAVA','THIS IS THE JAVA BEGINNING TUTORIAL, PLEASE WATCH AND LEARN MORE ABOUT JAVA.','module.zip','other','<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/ZK3O402wf1c\" frameborder=\"0\" allowfullscreen></iframe>','4','','Sat, 15 July 2017');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_thread_code` longtext NOT NULL,
  `message` longtext NOT NULL,
  `sender` longtext NOT NULL,
  `timestamp` longtext NOT NULL,
  `read_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 unread 1 read',
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_thread`
--

DROP TABLE IF EXISTS `message_thread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_thread` (
  `message_thread_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_thread_code` longtext NOT NULL,
  `sender` longtext NOT NULL,
  `reciever` longtext NOT NULL,
  `last_message_timestamp` longtext NOT NULL,
  PRIMARY KEY (`message_thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_thread`
--

LOCK TABLES `message_thread` WRITE;
/*!40000 ALTER TABLE `message_thread` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_thread` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `news_content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (3,'INFORMATION ABOUT OMOLOU ESTHER IS CORRECT THAT SHE LOVES SEGUN','23:12:12 PM','MARO RARA SHE IS JUST PRETENDING OOOOO');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noticeboard`
--

DROP TABLE IF EXISTS `noticeboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `noticeboard` (
  `notice_id` int(11) NOT NULL AUTO_INCREMENT,
  `notice_title` longtext NOT NULL,
  `notice` longtext NOT NULL,
  `create_timestamp` int(11) NOT NULL,
  PRIMARY KEY (`notice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticeboard`
--

LOCK TABLES `noticeboard` WRITE;
/*!40000 ALTER TABLE `noticeboard` DISABLE KEYS */;
INSERT INTO `noticeboard` VALUES (1,'Mid Term Exam timetable','The Mid Term Exam starts on 14 Oct 2026. The detailed subject-wise timetable is shared with class teachers. Please collect the hall ticket from the office.',1790829000),(2,'Parent-Teacher Meeting','PTM for all classes on Saturday 10 Oct 2026 from 10:00 AM to 1:00 PM. Please meet the class teacher to discuss your child\'s progress.',1790656200),(3,'Diwali break','Classes will remain closed for the Diwali break as per the holiday list. Online tests will continue as scheduled in the student portal.',1790483400),(4,'Fee installment reminder','The second fee installment is due by the 10th of this month. Parents can check the balance in the parent portal under Fees.',1790224200);
/*!40000 ALTER TABLE `noticeboard` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parent`
--

DROP TABLE IF EXISTS `parent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parent` (
  `parent_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `address` longtext NOT NULL,
  `profession` longtext NOT NULL,
  `authentication_key` longtext NOT NULL,
  PRIMARY KEY (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parent`
--

LOCK TABLES `parent` WRITE;
/*!40000 ALTER TABLE `parent` DISABLE KEYS */;
INSERT INTO `parent` VALUES (19,'Kiran Joshi','kiran.joshi.parent@yahoo.com','$2y$10$iwkvl7PJ22UlsbYEzQTGw.1Lqs.AqU/IAbBKd1pRm..3i61u.N0xa','9000000219','46, Hiranandani Estate, Thane West 400601','Teacher',''),(21,'Rajendra Kulkarni','rajendra.kulkarni.parent@yahoo.com','$2y$10$iwkvl7PJ22UlsbYEzQTGw.1Lqs.AqU/IAbBKd1pRm..3i61u.N0xa','9000000221','60, Naupada, Thane West 400603','Accountant','');
/*!40000 ALTER TABLE `parent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_category_id` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `payment_type` longtext NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `method` longtext NOT NULL,
  `description` longtext NOT NULL,
  `amount` longtext NOT NULL,
  `timestamp` longtext NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  `question_count` int(11) DEFAULT NULL,
  `duration` int(5) DEFAULT NULL,
  `question` text DEFAULT NULL,
  `correct_answers` varchar(255) DEFAULT NULL,
  `marks` int(11) NOT NULL DEFAULT 1,
  `exam_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`question_id`),
  KEY `exam_id` (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (208,12,52,'2026-09-25','2026-2027',5,15,'Choose the noun:','C',2,45),(209,12,52,'2026-09-25','2026-2027',5,15,'Plural of \"child\" is','B',1,45),(210,12,52,'2026-09-25','2026-2027',5,15,'Opposite of \"hot\" is','B',1,45),(211,12,52,'2026-09-25','2026-2027',5,15,'Pick the correct spelling:','B',1,45),(212,12,52,'2026-09-25','2026-2027',5,15,'\"She ___ to school daily.\"','B',1,45),(213,12,53,'2026-09-29','2026-2027',5,15,'What is 15 x 4?','B',2,46),(214,12,53,'2026-09-29','2026-2027',5,15,'Which is a prime number?','C',1,46),(215,12,53,'2026-09-29','2026-2027',5,15,'What is 3/4 as a percentage?','B',1,46),(216,12,53,'2026-09-29','2026-2027',5,15,'Perimeter of a square of side 5 cm?','C',1,46),(217,12,53,'2026-09-29','2026-2027',5,15,'Value of 7 squared?','B',1,46),(218,12,54,'2026-10-02','2026-2027',5,15,'Which planet is known as the Red Planet?','B',2,47),(219,12,54,'2026-10-02','2026-2027',5,15,'Water boils at','B',1,47),(220,12,54,'2026-10-02','2026-2027',5,15,'Plants make food by','B',1,47),(221,12,54,'2026-10-02','2026-2027',5,15,'Which gas do we breathe in?','B',1,47),(222,12,54,'2026-10-02','2026-2027',5,15,'The hardest natural substance is','C',1,47),(223,12,55,'2026-10-05','2026-2027',4,15,'Capital of India is','B',2,48),(224,12,55,'2026-10-05','2026-2027',4,15,'Who was the first Prime Minister of India?','B',1,48),(225,12,55,'2026-10-05','2026-2027',4,15,'The longest river in India is','B',1,48),(226,12,55,'2026-10-05','2026-2027',4,15,'India got independence in','B',1,48),(227,13,57,'2026-09-25','2026-2027',4,15,'SI unit of force is','B',2,49),(228,13,57,'2026-09-25','2026-2027',4,15,'Speed of light is about','B',1,49),(229,13,57,'2026-09-25','2026-2027',4,15,'Ohm\'s law: V =','B',1,49),(230,13,57,'2026-09-25','2026-2027',4,15,'Unit of power is','B',1,49),(231,13,58,'2026-09-29','2026-2027',4,15,'Chemical formula of water','B',2,50),(232,13,58,'2026-09-29','2026-2027',4,15,'pH of pure water is','B',1,50),(233,13,58,'2026-09-29','2026-2027',4,15,'Atomic number of Carbon','B',1,50),(234,13,58,'2026-09-29','2026-2027',4,15,'NaCl is commonly called','B',1,50),(235,13,59,'2026-10-02','2026-2027',5,15,'What is 15 x 4?','B',2,51),(236,13,59,'2026-10-02','2026-2027',5,15,'Which is a prime number?','C',1,51),(237,13,59,'2026-10-02','2026-2027',5,15,'What is 3/4 as a percentage?','B',1,51),(238,13,59,'2026-10-02','2026-2027',5,15,'Perimeter of a square of side 5 cm?','C',1,51),(239,13,59,'2026-10-02','2026-2027',5,15,'Value of 7 squared?','B',1,51),(240,13,60,'2026-10-05','2026-2027',4,15,'Powerhouse of the cell','B',2,52),(241,13,60,'2026-10-05','2026-2027',4,15,'Human heart has ___ chambers','B',1,52),(242,13,60,'2026-10-05','2026-2027',4,15,'DNA stands for','B',1,52),(243,13,60,'2026-10-05','2026-2027',4,15,'Largest organ of the human body','B',1,52);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `nick_name` longtext NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `days` varchar(255) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `session_name` varchar(20) DEFAULT NULL,
  `revision_section` varchar(255) DEFAULT NULL,
  `lecture_subject` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section`
--

LOCK TABLES `section` WRITE;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` VALUES (1,'A','Jr KG - A',1,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','09:00:00','11:00:00','2026-2027',NULL,NULL),(2,'A','Sr KG - A',2,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','09:00:00','11:00:00','2026-2027',NULL,NULL),(3,'A','1st - A',3,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','14:00:00','16:00:00','2026-2027',NULL,NULL),(4,'A','2nd - A',4,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','14:00:00','16:00:00','2026-2027',NULL,NULL),(5,'A','3rd - A',5,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','14:00:00','16:00:00','2026-2027',NULL,NULL),(6,'A','4th - A',6,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','14:00:00','16:00:00','2026-2027',NULL,NULL),(7,'A','5th - A',7,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','14:00:00','16:00:00','2026-2027',NULL,NULL),(8,'A','6th - A',8,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','14:00:00','16:00:00','2026-2027',NULL,NULL),(9,'A','7th - A',9,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','14:00:00','16:00:00','2026-2027',NULL,NULL),(10,'A','8th - A',10,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','17:00:00','19:30:00','2026-2027',NULL,NULL),(11,'A','9th - A',11,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','17:00:00','19:30:00','2026-2027',NULL,NULL),(12,'A','10th - A',12,2,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','17:00:00','19:30:00','2026-2027',NULL,NULL),(13,'A','11th - A',13,3,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','17:00:00','19:30:00','2026-2027',NULL,NULL),(14,'A','12th - A',14,3,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','17:00:00','19:30:00','2026-2027',NULL,NULL);
/*!40000 ALTER TABLE `section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` VALUES (3,'2016-2017'),(4,'2017-2018'),(5,'2018-2019'),(6,'2019-2020'),(7,'2020-2021'),(8,'2021-2022'),(9,'2022-2023'),(10,'2023-2024'),(11,'2024-2025'),(12,'2025-2026'),(13,'2026-2027'),(14,'2027-2028'),(15,'2028-2029'),(16,'2029-2030');
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` longtext DEFAULT NULL,
  `middle_name` longtext DEFAULT NULL,
  `last_name` longtext DEFAULT NULL,
  `name` longtext NOT NULL,
  `birthday` longtext NOT NULL,
  `sex` longtext NOT NULL,
  `religion` longtext NOT NULL,
  `blood_group` longtext NOT NULL,
  `address` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `fmobile` longtext DEFAULT NULL,
  `mmobile` longtext DEFAULT NULL,
  `emergency_contact` longtext DEFAULT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL,
  `father_name` longtext NOT NULL,
  `mother_name` longtext NOT NULL,
  `class_id` longtext NOT NULL,
  `standard` longtext DEFAULT NULL,
  `medium` longtext DEFAULT NULL,
  `board` longtext DEFAULT NULL,
  `birth_certificate` longtext DEFAULT NULL,
  `marksheet` longtext DEFAULT NULL,
  `aadhar_card` longtext DEFAULT NULL,
  `section_id` varchar(50) DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `roll` longtext NOT NULL,
  `transport_id` int(11) NOT NULL,
  `dormitory_id` int(11) NOT NULL,
  `dormitory_room_number` longtext NOT NULL,
  `authentication_key` longtext NOT NULL,
  `student_photo` varchar(255) DEFAULT NULL,
  `total_fees` varchar(255) DEFAULT NULL,
  `payment_done` varchar(255) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_alumni` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `academic_year` varchar(16) DEFAULT NULL,
  `previous_student_id` int(11) DEFAULT NULL,
  `student_mobile` varchar(15) DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `is_reregister` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (19,'Tanvi','Kiran','Joshi','Tanvi Kiran Joshi','2011-07-01','female','','B+','46, Hiranandani Estate, Thane West 400601','9000000219','9000000219','9000000319','9000000319','tanvi.joshi29@gmail.com','$2y$10$iwkvl7PJ22UlsbYEzQTGw.1Lqs.AqU/IAbBKd1pRm..3i61u.N0xa','Kiran Joshi','Smita Joshi','12','10th','English','CBSE',NULL,NULL,NULL,'12',19,'1',0,0,'','',NULL,'30000','0',1,0,'2026-10-02 12:33:42','2026-2027',NULL,'9000000419',NULL,0),(21,'Sneha','Rajendra','Kulkarni','Sneha Rajendra Kulkarni','2010-09-07','female','','O+','60, Naupada, Thane West 400603','9000000221','9000000221','9000000321','9000000321','sneha.kulkarni31@gmail.com','$2y$10$iwkvl7PJ22UlsbYEzQTGw.1Lqs.AqU/IAbBKd1pRm..3i61u.N0xa','Rajendra Kulkarni','Madhuri Kulkarni','13','11th','English','State Board',NULL,NULL,NULL,'13',21,'1',0,0,'','',NULL,'40000','28000',1,0,'2026-10-02 12:33:42','2026-2027',NULL,'9000000421',NULL,0);
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_payment_history`
--

DROP TABLE IF EXISTS `student_payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_payment_history` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `payment_type` varchar(32) NOT NULL,
  `method` varchar(32) NOT NULL,
  `description` longtext NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `transaction_id` varchar(64) DEFAULT NULL,
  `cheque_number` varchar(64) DEFAULT NULL,
  `cheque_bank` varchar(128) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_payment_history`
--

LOCK TABLES `student_payment_history` WRITE;
/*!40000 ALTER TABLE `student_payment_history` DISABLE KEYS */;
INSERT INTO `student_payment_history` VALUES (36,21,0,'Installment 1','fees','card','Fee installment 1 (2026-2027)',16000.00,1787031000,'TXN100035','','',NULL),(37,21,0,'Installment 2','fees','cash','Fee installment 2 (2026-2027)',12000.00,1789191000,'TXN100036','','',NULL);
/*!40000 ALTER TABLE `student_payment_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
INSERT INTO `subject` VALUES (1,'English',1,2),(2,'Numbers',1,2),(3,'Rhymes & Activities',1,2),(4,'English',2,2),(5,'Numbers',2,2),(6,'Rhymes & Activities',2,2),(7,'English',3,2),(8,'Mathematics',3,2),(9,'EVS',3,2),(10,'Hindi',3,2),(11,'Marathi',3,2),(12,'English',4,2),(13,'Mathematics',4,2),(14,'EVS',4,2),(15,'Hindi',4,2),(16,'Marathi',4,2),(17,'English',5,2),(18,'Mathematics',5,2),(19,'EVS',5,2),(20,'Hindi',5,2),(21,'Marathi',5,2),(22,'English',6,2),(23,'Mathematics',6,2),(24,'EVS',6,2),(25,'Hindi',6,2),(26,'Marathi',6,2),(27,'English',7,2),(28,'Mathematics',7,2),(29,'Science',7,3),(30,'Social Studies',7,2),(31,'Hindi',7,2),(32,'English',8,2),(33,'Mathematics',8,2),(34,'Science',8,3),(35,'Social Studies',8,2),(36,'Hindi',8,2),(37,'English',9,2),(38,'Mathematics',9,2),(39,'Science',9,3),(40,'Social Studies',9,2),(41,'Hindi',9,2),(42,'English',10,2),(43,'Mathematics',10,2),(44,'Science',10,3),(45,'Social Studies',10,2),(46,'Hindi',10,2),(47,'English',11,2),(48,'Mathematics',11,2),(49,'Science',11,3),(50,'Social Studies',11,2),(51,'Hindi',11,2),(52,'English',12,2),(53,'Mathematics',12,2),(54,'Science',12,3),(55,'Social Studies',12,2),(56,'Hindi',12,2),(57,'Physics',13,3),(58,'Chemistry',13,3),(59,'Mathematics',13,2),(60,'Biology',13,3),(61,'English',13,2),(62,'Physics',14,3),(63,'Chemistry',14,3),(64,'Mathematics',14,2),(65,'Biology',14,3),(66,'English',14,2);
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_manager`
--

DROP TABLE IF EXISTS `task_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_manager` (
  `task_manager_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `priority` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`task_manager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_manager`
--

LOCK TABLES `task_manager` WRITE;
/*!40000 ALTER TABLE `task_manager` DISABLE KEYS */;
INSERT INTO `task_manager` VALUES (1,'CLEANING OF TOILET TODAY','YOU HAVE BEEN ASSIGNMENT OF WATCH THE STAFF TOILET TODAY','Normal','Fri, 14 July 2017','parent-OMMOLOLU ESTHER ','Open'),(3,'THIS IS ANOTHER ONE','YOU ARE EXPECTED TO HAVE UNDERSTOOD THE INFOMATION I SENT EARLIER','Low','Thu, 13 July 2017','parent-AKINADE AYODEJI AYOTUNDE','Normal');
/*!40000 ALTER TABLE `task_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `birthday` longtext NOT NULL,
  `sex` longtext NOT NULL,
  `religion` longtext NOT NULL,
  `blood_group` longtext NOT NULL,
  `address` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL,
  `authentication_key` longtext NOT NULL,
  `teacher_photo` varchar(255) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `bank_account` varchar(40) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT 0.00,
  `hra` decimal(10,2) DEFAULT 0.00,
  `da` decimal(10,2) DEFAULT 0.00,
  `conveyance` decimal(10,2) DEFAULT 0.00,
  `medical_allowance` decimal(10,2) DEFAULT 0.00,
  `other_allowance` decimal(10,2) DEFAULT 0.00,
  `pf_deduction` decimal(10,2) DEFAULT 0.00,
  `tax_deduction` decimal(10,2) DEFAULT 0.00,
  `other_deduction` decimal(10,2) DEFAULT 0.00,
  `total_salary` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher`
--

LOCK TABLES `teacher` WRITE;
/*!40000 ALTER TABLE `teacher` DISABLE KEYS */;
INSERT INTO `teacher` VALUES (2,'Rajesh Vinayak Kulkarni','11/02/1979','male','','B+','Vartak Nagar, Thane','9000000102','rajesh.kulkarni.maths@yahoo.com','$2y$10$iwkvl7PJ22UlsbYEzQTGw.1Lqs.AqU/IAbBKd1pRm..3i61u.N0xa','',NULL,'Senior Teacher','2021-06-01','ABCDE1231F','000000001002',32500.00,10000.00,4000.00,2000.00,1500.00,1000.00,1800.00,200.00,0.00,49000.00),(3,'Priya Anil Nair','07/21/1988','female','','A+','Majiwada, Thane','9000000103','priya.nair.science@gmail.com','$2y$10$dyz2f3et3bv44.0TWMh8xO9NTrmV0GcT08FP0ivCuoxf4xerzo/n6','',NULL,'Senior Teacher','2022-06-01','ABCDE1232F','000000001003',30000.00,10000.00,4000.00,2000.00,1500.00,1000.00,1800.00,200.00,0.00,46500.00);
/*!40000 ALTER TABLE `teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_attendance`
--

DROP TABLE IF EXISTS `teacher_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0 undefined, 1 present, 2 absent',
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `teacher_date` (`teacher_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_attendance`
--

LOCK TABLES `teacher_attendance` WRITE;
/*!40000 ALTER TABLE `teacher_attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `teacher_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todays_thought`
--

DROP TABLE IF EXISTS `todays_thought`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todays_thought` (
  `tthought_id` int(11) NOT NULL AUTO_INCREMENT,
  `thought` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tthought_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `todays_thought`
--

LOCK TABLES `todays_thought` WRITE;
/*!40000 ALTER TABLE `todays_thought` DISABLE KEYS */;
INSERT INTO `todays_thought` VALUES (1,'be yourself alwatys','2017-07-19 00:19:35'),(2,'You will sure make it if your believ','2017-07-06 00:40:26');
/*!40000 ALTER TABLE `todays_thought` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transport`
--

DROP TABLE IF EXISTS `transport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transport` (
  `transport_id` int(11) NOT NULL AUTO_INCREMENT,
  `route_name` longtext NOT NULL,
  `number_of_vehicle` longtext NOT NULL,
  `picnic_date` date DEFAULT NULL,
  `location` longtext DEFAULT NULL,
  `description` longtext NOT NULL,
  `route_fare` longtext NOT NULL,
  `expenses` decimal(10,2) DEFAULT 0.00,
  `bill_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`transport_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transport`
--

LOCK TABLES `transport` WRITE;
/*!40000 ALTER TABLE `transport` DISABLE KEYS */;
INSERT INTO `transport` VALUES (1,'Lagos','400GL','2026-06-10','Essel World','Long Distance','500',12430.00,'bill_1790921220_4392f7f0.pdf');
/*!40000 ALTER TABLE `transport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'smsDB'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: smsDB
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_log`
--

DROP TABLE IF EXISTS `email_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` mediumtext DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `error` text DEFAULT NULL,
  `event` varchar(50) NOT NULL DEFAULT '',
  `ref_type` varchar(30) NOT NULL DEFAULT '',
  `ref_id` int(11) NOT NULL DEFAULT 0,
  `student_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`log_id`),
  KEY `ref` (`ref_type`,`ref_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_log`
--

DROP TABLE IF EXISTS `whatsapp_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `event_type` varchar(64) DEFAULT NULL,
  `phone_to` varchar(64) DEFAULT NULL,
  `phone_from` varchar(64) DEFAULT NULL,
  `message_body` text DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `http_code` int(11) DEFAULT NULL,
  `twilio_sid` varchar(64) DEFAULT NULL,
  `twilio_status` varchar(32) DEFAULT NULL,
  `error_code` varchar(32) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `raw_response` longtext DEFAULT NULL,
  `response_payload` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_twilio_sid` (`twilio_sid`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: smsDB
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `settings_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--
-- WHERE:  type NOT IN ('clickatell_user','clickatell_password','clickatell_api_id','twilio_sender_phone_number','twilio_account_sid','twilio_auth_token','smtp_user','smtp_pass','cron_key')

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'system_name','Shree Coaching Classes'),(2,'system_title','CRM'),(3,'address','Lokmanya Nagar, Thane'),(4,'phone','+919833963867'),(5,'paypal_email','info@shreecoachingclasses.com'),(6,'currency','₹250000'),(7,'system_email','info@shreecoachingclasses.com'),(20,'active_sms_service','twilio'),(11,'language','english'),(12,'text_align','left-to-right'),(16,'skin_colour','blue'),(27,'whatsapp_welcome_message','Dear Parent,\nThis is to confirm that your student {{studentname}} successfully registered with Shree Coaching Classes. We welcome you and wish you the very best for your learning journey with us.'),(21,'session','2026-2027'),(22,'footer','© 2026 CLASSES MANAGEMENT SYSTEM.'),(25,'active_whatsapp','enabled'),(26,'twilio_whatsapp_number','+14155238886'),(28,'smtp_host','smtp.gmail.com'),(29,'smtp_port','587'),(30,'smtp_crypto','tls'),(33,'email_enabled','1'),(34,'email_copy_parent','1'),(36,'exam_schema_version','2'),(37,'ui_theme','sunshine'),(38,'ui_primary',''),(39,'ui_accent',''),(40,'ui_font','nunito');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed

-- secret settings, intentionally blank
INSERT INTO `settings` (`type`, `description`) VALUES ('clickatell_user', '');
INSERT INTO `settings` (`type`, `description`) VALUES ('clickatell_password', '');
INSERT INTO `settings` (`type`, `description`) VALUES ('clickatell_api_id', '');
INSERT INTO `settings` (`type`, `description`) VALUES ('twilio_sender_phone_number', '');
INSERT INTO `settings` (`type`, `description`) VALUES ('twilio_account_sid', '');
INSERT INTO `settings` (`type`, `description`) VALUES ('twilio_auth_token', '');
INSERT INTO `settings` (`type`, `description`) VALUES ('smtp_user', '');
INSERT INTO `settings` (`type`, `description`) VALUES ('smtp_pass', '');
INSERT INTO `settings` (`type`, `description`) VALUES ('cron_key', '');
