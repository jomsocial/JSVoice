
-- --------------------------------------------------------

--
-- Table structure for table `#__jav_actions_log`
--

CREATE TABLE IF NOT EXISTS `#__jav_actions_log` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  `details` varchar(525) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `remote_addr` varchar(25) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_admin_responses`
--

CREATE TABLE IF NOT EXISTS `#__jav_admin_responses` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text,
  `type` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_configs`
--

CREATE TABLE IF NOT EXISTS `#__jav_configs` (
  `id` int(11) NOT NULL auto_increment,
  `group` varchar(100) default NULL,
  `data` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_email_templates`
--

CREATE TABLE IF NOT EXISTS `#__jav_email_templates` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `title` varchar(525) default NULL,
  `subject` varchar(525) default NULL,
  `emailcontent` text,
  `email_from_address` varchar(255) default NULL,
  `email_from_name` varchar(255) default NULL,
  `published` tinyint(1) default '0',
  `group` int(11) default '0',
  `language` varchar(20) default NULL,
  `system` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_forums`
--

CREATE TABLE IF NOT EXISTS `#__jav_forums` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(256) default NULL,
  `alias` varchar(256) default NULL,
  `description` text,
  `ordering` int(11) default '0',
  `published` tinyint(1) default '0',
  `gids_post` varchar(127) default '0',
  `gids_view` varchar(127) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_forums_has_voice_types`
--

CREATE TABLE IF NOT EXISTS `#__jav_forums_has_voice_types` (
  `forums_id` int(11) NOT NULL,
  `voice_types_id` int(11) NOT NULL,
  `total_items` int(11) default '0',
  `vote_counting` tinyint(1) default '0',
  `vote_option` text,
  `voice_type_status_id` int(11) default '0',
  PRIMARY KEY  (`forums_id`,`voice_types_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_items`
--

CREATE TABLE IF NOT EXISTS `#__jav_items` (
  `id` int(11) NOT NULL auto_increment,
  `voice_type_status_id` int(11) NOT NULL,
  `forums_id` int(11) NOT NULL,
  `voice_types_id` int(11) NOT NULL,
  `title` text default NULL,
  `content` text,
  `user_id` int(11) default NULL,
  `create_date` int(11) default NULL,
  `update_date` int(11) default NULL,
  `number_vote_up` int(11) default '0',
  `total_vote_up` int(11) default '0',
  `number_vote_down` int(11) default '0',
  `total_vote_down` int(11) default '0',
  `number_vote_neutral` int(11) NOT NULL default '0',
  `number_spam` int(11) default '0',
  `number_duplicate` int(11) default '0',
  `number_inapproprivate` int(11) default '0',
  `published` tinyint(4) NOT NULL default '0',
  `data` text,
  `guest_name` varchar(255) NOT NULL,
  `guest_email` varchar(255) NOT NULL,
  `guest_website` varchar(255) NOT NULL,
  `guest_file` varchar(255) NOT NULL,
  `use_anonymous` tinyint(1) DEFAULT '0',
  `is_private` tinyint(1) DEFAULT '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `content_search` (`title`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_logs`
--

CREATE TABLE IF NOT EXISTS `#__jav_logs` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `item_id` int(11) default NULL,
  `votes` int(4) NOT NULL default '0',
  `time_expired` int(11) default NULL,
  `remote_addr` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_temp_data`
--

CREATE TABLE IF NOT EXISTS `#__jav_temp_data` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(525) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_voice_types`
--

CREATE TABLE IF NOT EXISTS `#__jav_voice_types` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(256) default NULL,
  `alias` varchar(256) default NULL,
  `total_votes` int(4) default '0',
  `vote_option` text,
  `ordering` int(11) default NULL,
  `published` tinyint(1) default NULL,
  `class_css` varchar(20) default NULL,
  `description` text NOT NULL,
  `search_title` varchar(255) NOT NULL,
  `search_description` text NOT NULL,
  `search_button` varchar(255) default NULL,
  `display_voting` tinyint(2) NOT NULL COMMENT '0:dung text, 1: dung value',
  `language_response` varchar(525) default NULL,
  `has_answer` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__jav_voice_type_status`
--

CREATE TABLE IF NOT EXISTS `#__jav_voice_type_status` (
  `id` int(11) NOT NULL auto_increment,
  `voice_types_id` int(11) NOT NULL,
  `parent_id` int(11) default '0',
  `title` varchar(256) default NULL,
  `alias` varchar(256) default NULL,
  `class_css` varchar(125) default NULL,
  `name` varchar(125) default NULL,
  `show_on_tab` tinyint(1) default '0',
  `published` tinyint(4) NOT NULL default '0',
  `allow_voting` tinyint(2) NOT NULL COMMENT '0: No 1:Yes null:use global',
  `allow_show` tinyint(2) NOT NULL default '1',
  `ordering` int(11) NOT NULL default '0',
  `return_vote` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
-- --------------------------------------------------------

--
-- Table structure for table `#__jav_tags`
--
CREATE TABLE IF NOT EXISTS `#__jav_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `published` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
-- --------------------------------------------------------

--
-- Table structure for table `#__jav_tags_voice`
--
CREATE TABLE IF NOT EXISTS `#__jav_tags_voice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagID` int(11) NOT NULL,
  `voiceID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tagID` (`tagID`),
  KEY `voiceID` (`voiceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
-- --------------------------------------------------------

--
-- Table structure for table `#__jav_feeds`
--
CREATE TABLE IF NOT EXISTS `#__jav_feeds` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `user_id` int(13) NOT NULL,
  `filter_forums_id` varchar(255) NOT NULL,
  `filter_voicetypes_id` varchar(255) NOT NULL,
  `filter_date` int(11) NOT NULL,
  `filter_item_ids` varchar(255) NOT NULL,
  `filter_creator_ids` varchar(255) NOT NULL,
  `filter_status` tinyint(2) NOT NULL DEFAULT '2',
  `feed_name` varchar(30) DEFAULT NULL,
  `feed_alias` varchar(255) DEFAULT NULL,
  `feed_description` text,
  `feed_type` varchar(10) DEFAULT NULL,
  `feed_cache` smallint(9) DEFAULT NULL,
  `feed_imgUrl` varchar(100) DEFAULT NULL,
  `feed_button` varchar(100) DEFAULT NULL,
  `feed_renderAuthorFormat` varchar(10) DEFAULT 'NAME',
  `feed_renderHTML` tinyint(1) DEFAULT '0',
  `feed_renderImages` int(1) NOT NULL,
  `feed_system` tinyint(1) NOT NULL DEFAULT '0',
  `feed_last_update` int(13) NOT NULL,
  `msg_count` varchar(4) DEFAULT NULL,
  `msg_numWords` tinyint(4) unsigned DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;