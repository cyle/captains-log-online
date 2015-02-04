CREATE DATABASE IF NOT EXISTS `captains-log-db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `captains-log-db`;

CREATE TABLE IF NOT EXISTS `rawlog` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `thedate` date NOT NULL,
  `activities` text,
  `notes` text,
  `activities_chksum` varchar(128) DEFAULT NULL,
  `notes_chksum` varchar(128) DEFAULT NULL,
  `tsu` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `userlevel` tinyint(3) unsigned NOT NULL,
  `tsc` int(10) unsigned NOT NULL,
  `tsu` int(10) unsigned NOT NULL,
  `lastactivity` int(10) unsigned NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

ALTER TABLE `rawlog` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `thedate` (`thedate`);
ALTER TABLE `users` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`);