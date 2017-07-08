-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-03-24 13:30:35
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `myauth`
--
CREATE DATABASE IF NOT EXISTS `myauth` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `myauth`;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_info`
--

CREATE TABLE IF NOT EXISTS `oauth_info` (
  `appname` varchar(50) NOT NULL COMMENT '应用名称',
  `appid` varchar(50) NOT NULL COMMENT '应用ID',
  `appsecret` varchar(50) NOT NULL COMMENT '应用Secret',
  UNIQUE KEY `appname` (`appname`,`appid`,`appsecret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='OAuth的应用信息';

-- --------------------------------------------------------

--
-- 表的结构 `oauth_tokens`
--

CREATE TABLE IF NOT EXISTS `oauth_tokens` (
  `token_text` varchar(200) NOT NULL,
  `token_appid` varchar(50) NOT NULL,
  `token_uid` int(11) NOT NULL,
  `token_expire` bigint(20) unsigned NOT NULL COMMENT '令牌的过期时间',
  PRIMARY KEY (`token_text`),
  UNIQUE KEY `token_text` (`token_text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='OAuth的令牌表';

-- --------------------------------------------------------

--
-- 表的结构 `sso`
--

CREATE TABLE IF NOT EXISTS `sso` (
  `auth_id` int(11) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `auth_ded` varchar(20) NOT NULL,
  `auth_wechat` varchar(100) DEFAULT NULL,
  `auth_logincode` varchar(100) DEFAULT NULL COMMENT '登录用的凭证',
  UNIQUE KEY `auth_id` (`auth_id`),
  KEY `auth_ded` (`auth_ded`,`auth_wechat`),
  KEY `auth_logincode` (`auth_logincode`),
  KEY `auth_wechat` (`auth_wechat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='全部用户的验证信息';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
