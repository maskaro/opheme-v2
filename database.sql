-- phpMyAdmin SQL Dump
-- version 4.4.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 02, 2015 at 01:48 PM
-- Server version: 5.5.43-0+deb7u1
-- PHP Version: 5.6.7-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `opheme21`
--
CREATE DATABASE IF NOT EXISTS `opheme21` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `opheme21`;

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` bigint(20) unsigned NOT NULL,
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `company_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `hourly_limit` tinyint(1) NOT NULL DEFAULT '6',
  `category` text CHARACTER SET utf8 NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  `response_text` text CHARACTER SET utf16 NOT NULL,
  `banner` longtext CHARACTER SET utf8 NOT NULL,
  `banner_type` text CHARACTER SET utf8 NOT NULL,
  `filter` varchar(116) CHARACTER SET utf8 NOT NULL,
  `filter_ex` text CHARACTER SET utf16 NOT NULL,
  `centre_lat` double NOT NULL,
  `centre_lng` double NOT NULL,
  `radius` double NOT NULL,
  `weekdays` text CHARACTER SET utf8 NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `since_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `message_count_last_notification` bigint(20) unsigned NOT NULL DEFAULT '0',
  `messages_limit` bigint(20) unsigned NOT NULL,
  `time_limit` varchar(10) CHARACTER SET utf8 NOT NULL,
  `shared` tinyint(1) NOT NULL DEFAULT '0',
  `last_check` bigint(20) NOT NULL DEFAULT '0',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='campaign details';

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(11) NOT NULL,
  `company_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `modules` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discovers`
--

CREATE TABLE IF NOT EXISTS `discovers` (
  `id` bigint(20) unsigned NOT NULL,
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `company_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `hourly_limit` int(11) NOT NULL DEFAULT '0',
  `messageLifeSpanLimit` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `filter` varchar(116) CHARACTER SET utf8 NOT NULL,
  `filter_ex` text CHARACTER SET utf16 NOT NULL,
  `centre_lat` double NOT NULL,
  `centre_lng` double NOT NULL,
  `radius` double NOT NULL,
  `weekdays` text CHARACTER SET utf8 NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `message_count_last_notification` bigint(20) unsigned NOT NULL DEFAULT '0',
  `since_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `shared` tinyint(1) NOT NULL DEFAULT '0',
  `messages_limit` bigint(20) unsigned NOT NULL,
  `time_limit` varchar(10) CHARACTER SET utf8 NOT NULL,
  `last_check` bigint(20) NOT NULL DEFAULT '0',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='campaign details';

-- --------------------------------------------------------

--
-- Table structure for table `email_notification_history`
--

CREATE TABLE IF NOT EXISTS `email_notification_history` (
  `user_id` bigint(20) NOT NULL,
  `discover_names` varchar(1024) NOT NULL,
  `discover_messages` varchar(1014) NOT NULL,
  `campaign_names` varchar(1024) NOT NULL,
  `campaign_messages` varchar(1024) NOT NULL,
  `sent_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `instagram_keys`
--

CREATE TABLE IF NOT EXISTS `instagram_keys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `screen_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `last_checked` bigint(20) NOT NULL DEFAULT '0',
  `last_checked_messages` bigint(20) NOT NULL DEFAULT '0',
  `last_checked_interaction` bigint(20) NOT NULL DEFAULT '0',
  `average_message_time_of_followers` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='twitter user tokens';

-- --------------------------------------------------------

--
-- Table structure for table `jobs_messages`
--

CREATE TABLE IF NOT EXISTS `jobs_messages` (
  `id` bigint(32) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `job_type` varchar(32) NOT NULL,
  `message_id` varchar(255) NOT NULL,
  `created_mongo` bigint(20) NOT NULL,
  `token_type` varchar(32) NOT NULL,
  `user_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jobs_share_zip`
--

CREATE TABLE IF NOT EXISTS `jobs_share_zip` (
  `id` bigint(20) NOT NULL,
  `jobType` varchar(32) NOT NULL,
  `jobId` bigint(20) NOT NULL DEFAULT '-1',
  `messagesCount` bigint(20) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8;

-- --------------------------------------------------------

--
-- Table structure for table `jobs_tokens`
--

CREATE TABLE IF NOT EXISTS `jobs_tokens` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '-1',
  `job_type` varchar(32) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `token_type` varchar(32) NOT NULL,
  `token_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `time` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs_form_submits`
--

CREATE TABLE IF NOT EXISTS `logs_form_submits` (
  `submit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_ip` varchar(16) NOT NULL,
  `form_type` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs_operations`
--

CREATE TABLE IF NOT EXISTS `logs_operations` (
  `id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `error` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `registration_tokens`
--

CREATE TABLE IF NOT EXISTS `registration_tokens` (
  `id` int(11) NOT NULL,
  `email` text NOT NULL,
  `token` text NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company_id` int(11) NOT NULL COMMENT 'company id'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session_data`
--

CREATE TABLE IF NOT EXISTS `session_data` (
  `id` char(128) NOT NULL,
  `set_time` char(10) NOT NULL,
  `data` text NOT NULL,
  `session_key` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `short_urls`
--

CREATE TABLE IF NOT EXISTS `short_urls` (
  `id` bigint(20) unsigned NOT NULL,
  `long_url` varchar(255) NOT NULL,
  `short_code` varbinary(12) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `counter` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sm_interaction`
--

CREATE TABLE IF NOT EXISTS `sm_interaction` (
  `id` bigint(32) NOT NULL,
  `opheme_user_id` int(11) NOT NULL,
  `sm_user_id` varchar(64) CHARACTER SET utf8 NOT NULL,
  `sm_user_screen_name` varchar(256) CHARACTER SET utf8 NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 NOT NULL COMMENT 'follow_out, follow_in, message_out, message_in, favourite_out, favourite_in',
  `original_message` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `original_message_id` varchar(96) CHARACTER SET utf8 DEFAULT NULL,
  `original_message_added_at` bigint(20) NOT NULL,
  `message` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `message_id` varchar(96) CHARACTER SET utf8 DEFAULT NULL,
  `message_added_at` bigint(20) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `favourited` tinyint(1) NOT NULL DEFAULT '0',
  `authKeyId` int(11) NOT NULL DEFAULT '-1',
  `authKeyType` varchar(24) CHARACTER SET utf8 NOT NULL COMMENT 'twitter/...',
  `added_at` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_limits`
--

CREATE TABLE IF NOT EXISTS `subscription_limits` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `messages_limit` bigint(20) unsigned NOT NULL,
  `time_limit` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `account_time_limit` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `discover_job_limit` int(11) NOT NULL,
  `campaign_job_limit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_campaign_marketing_blacklist`
--

CREATE TABLE IF NOT EXISTS `twitter_campaign_marketing_blacklist` (
  `id` bigint(20) unsigned NOT NULL,
  `screen_name` text CHARACTER SET utf16 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_campaign_marketing_preferences`
--

CREATE TABLE IF NOT EXISTS `twitter_campaign_marketing_preferences` (
  `id` int(11) NOT NULL,
  `screen_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(128) COLLATE utf8_unicode_ci NOT NULL,
  `salt` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `preferences` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_keys`
--

CREATE TABLE IF NOT EXISTS `twitter_keys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `screen_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `token_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `last_checked` bigint(20) NOT NULL DEFAULT '0',
  `last_checked_messages` bigint(20) NOT NULL DEFAULT '0',
  `last_checked_interaction` bigint(20) NOT NULL DEFAULT '0',
  `average_message_time_of_followers` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='twitter user tokens';

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `password` char(128) NOT NULL,
  `salt` char(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(64) NOT NULL DEFAULT '1',
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `phone` text NOT NULL,
  `business_type` text NOT NULL,
  `business_www` text NOT NULL,
  `home_location` varchar(255) NOT NULL,
  `email_notification_frequency` varchar(8) NOT NULL DEFAULT '1 Day',
  `email_notification_last_timestamp` bigint(32) NOT NULL DEFAULT '0',
  `subscription` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - free account, 1 - discover lite, 2 - discover pro, 3 - campaign lite, 4 - campaign pro, 5 - campaign unlimited',
  `last_login` datetime NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `from_company_id` int(11) NOT NULL DEFAULT '-1' COMMENT 'userID of company that invited user',
  `registered_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_modules`
--

CREATE TABLE IF NOT EXISTS `user_modules` (
  `user_id` int(11) NOT NULL,
  `modules` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discovers`
--
ALTER TABLE `discovers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_notification_history`
--
ALTER TABLE `email_notification_history`
  ADD UNIQUE KEY `user_id` (`user_id`,`sent_date`);

--
-- Indexes for table `instagram_keys`
--
ALTER TABLE `instagram_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs_messages`
--
ALTER TABLE `jobs_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `per_job_unique` (`message_id`,`job_type`,`job_id`);

--
-- Indexes for table `jobs_share_zip`
--
ALTER TABLE `jobs_share_zip`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs_tokens`
--
ALTER TABLE `jobs_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `per_user_unique` (`job_type`,`job_id`,`token_type`,`token_id`,`user_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`email`,`time`);

--
-- Indexes for table `logs_form_submits`
--
ALTER TABLE `logs_form_submits`
  ADD UNIQUE KEY `submit_time` (`submit_time`);

--
-- Indexes for table `logs_operations`
--
ALTER TABLE `logs_operations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registration_tokens`
--
ALTER TABLE `registration_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session_data`
--
ALTER TABLE `session_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `short_urls`
--
ALTER TABLE `short_urls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `short_code` (`short_code`);

--
-- Indexes for table `sm_interaction`
--
ALTER TABLE `sm_interaction`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `opheme_user_id` (`opheme_user_id`,`sm_user_id`,`type`,`original_message_id`,`message_id`,`authKeyType`,`authKeyId`);

--
-- Indexes for table `subscription_limits`
--
ALTER TABLE `subscription_limits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `twitter_campaign_marketing_blacklist`
--
ALTER TABLE `twitter_campaign_marketing_blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `twitter_campaign_marketing_preferences`
--
ALTER TABLE `twitter_campaign_marketing_preferences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `twitter_keys`
--
ALTER TABLE `twitter_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_modules`
--
ALTER TABLE `user_modules`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `discovers`
--
ALTER TABLE `discovers`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `instagram_keys`
--
ALTER TABLE `instagram_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jobs_messages`
--
ALTER TABLE `jobs_messages`
  MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jobs_share_zip`
--
ALTER TABLE `jobs_share_zip`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jobs_tokens`
--
ALTER TABLE `jobs_tokens`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_operations`
--
ALTER TABLE `logs_operations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `registration_tokens`
--
ALTER TABLE `registration_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `short_urls`
--
ALTER TABLE `short_urls`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sm_interaction`
--
ALTER TABLE `sm_interaction`
  MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `twitter_campaign_marketing_blacklist`
--
ALTER TABLE `twitter_campaign_marketing_blacklist`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `twitter_campaign_marketing_preferences`
--
ALTER TABLE `twitter_campaign_marketing_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `twitter_keys`
--
ALTER TABLE `twitter_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
