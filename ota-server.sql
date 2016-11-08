-- MySQL dump 10.13  Distrib 5.7.9, for osx10.11 (x86_64)
--
-- Host: localhost    Database: ota-server
-- ------------------------------------------------------
-- Server version	5.7.9

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户',
  `name` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `thumb` varchar(200) DEFAULT NULL COMMENT '头像地址',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `pwd` varchar(32) DEFAULT NULL,
  `token` varchar(32) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL COMMENT '登录名',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (3,'未填写',NULL,1467365977,'96e79218965eb72c92a549dd5a330112','123','admin');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ota`
--

DROP TABLE IF EXISTS `ota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ota` (
  `ota_id` int(11) NOT NULL AUTO_INCREMENT,
  `ota_platform_id` int(255) NOT NULL COMMENT '所属平台',
  `ota_identification_id` int(11) NOT NULL COMMENT '标识',
  `version_name` varchar(200) NOT NULL COMMENT '版本名',
  `version_code` bigint(20) NOT NULL COMMENT '版本号',
  `is_force` enum('1','0') NOT NULL DEFAULT '0' COMMENT '是否强制升级 0:不强制  1:强制',
  `package_size` bigint(20) DEFAULT NULL COMMENT '包大小，单位Byte',
  `package_md5` varchar(32) DEFAULT NULL COMMENT '包校验码',
  `url` varchar(200) DEFAULT NULL COMMENT '下载地址',
  `description` text COMMENT '描述',
  `ota_channel_id` int(200) DEFAULT NULL COMMENT '渠道',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` enum('1','0') NOT NULL DEFAULT '1' COMMENT '1正常  0删除',
  PRIMARY KEY (`ota_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ota_channel`
--

DROP TABLE IF EXISTS `ota_channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ota_channel` (
  `ota_channel_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL COMMENT '渠道名称',
  `description` varchar(255) NOT NULL COMMENT '描述',
  PRIMARY KEY (`ota_channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ota_identification`
--

DROP TABLE IF EXISTS `ota_identification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ota_identification` (
  `ota_identification_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL COMMENT '标识。android: package name, iOS: app name',
  `ota_platform_id` int(255) NOT NULL COMMENT '所属平台: Android, iOS, WindowsPhone',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`ota_identification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ota_platform`
--

DROP TABLE IF EXISTS `ota_platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ota_platform` (
  `ota_platform_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '平台名称',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`ota_platform_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;
