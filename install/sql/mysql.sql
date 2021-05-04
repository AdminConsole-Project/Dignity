SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";


CREATE TABLE `#__articles` (
  `ID` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `alias` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `content` longtext CHARACTER SET utf8mb4,
  `category` int(11) DEFAULT NULL,
  `settings` varchar(191) CHARACTER SET utf8mb4 DEFAULT NULL,
  `author` int(11) NOT NULL,
  `hits` bigint(11) DEFAULT '0',
  `date` datetime NOT NULL,
  `date_gmt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `#__categories` (
  `ID` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `level` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `path` text CHARACTER SET utf8mb4 NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `alias` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `description` varchar(128) CHARACTER SET utf8mb4 DEFAULT NULL,
  `date` datetime NOT NULL,
  `date_gmt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `#__languages` (
  `ID` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `title_native` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `lang_code` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `version` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `lang_update` tinyint(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `date_gmt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `#__media` (
  `ID` int(11) NOT NULL,
  `name` text CHARACTER SET utf8mb4 NOT NULL,
  `basename` text CHARACTER SET utf8mb4 NOT NULL,
  `location` text CHARACTER SET utf8mb4 NOT NULL,
  `size` bigint(11) NOT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 DEFAULT NULL,
  `mime_type` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `date` datetime NOT NULL,
  `date_gmt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `#__navigation` (
  `ID` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `alias` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `structure` longtext CHARACTER SET utf8mb4,
  `date` datetime NOT NULL,
  `date_gmt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `#__pages` (
  `ID` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `main_page` int(11) NOT NULL DEFAULT '0',
  `title` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `alias` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `content` longtext CHARACTER SET utf8mb4,
  `settings` varchar(191) CHARACTER SET utf8mb4 DEFAULT NULL,
  `date` datetime NOT NULL,
  `date_gmt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `#__settings` (
  `ID` int(11) NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `value` text CHARACTER SET utf8mb4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `#__settings` (`ID`, `name`, `value`) VALUES
(1, 'sitename', 'Dignity'),
(2, 'description', NULL),
(3, 'site_language', 'en-US'),
(4, 'timezone', 'Europe/Bratislava'),
(5, 'robots', 'index, follow'),
(6, 'author', NULL),
(7, 'show_author', 'false'),
(8, 'sitename_title_location', 'after'),
(9, 'maintenance', 'false'),
(10, 'maintenance_message', NULL),
(11, 'media_date_sort', 'true'),
(12, 'media_type_sort', 'false'),
(13, 'session_length', '15');

CREATE TABLE `#__themes` (
  `ID` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_520_ci,
  `settings` varchar(191) CHARACTER SET utf8mb4 DEFAULT NULL,
  `date` datetime NOT NULL,
  `date_gmt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `#__users` (
  `ID` int(11) NOT NULL,
  `status` varchar(32) CHARACTER SET utf8mb4 NOT NULL,
  `role` varchar(32) CHARACTER SET utf8mb4 NOT NULL,
  `username` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `firstname` varchar(191) CHARACTER SET utf8mb4 DEFAULT NULL,
  `lastname` varchar(191) CHARACTER SET utf8mb4 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `preferences` varchar(191) CHARACTER SET utf8mb4 DEFAULT NULL,
  `activation` varchar(191) CHARACTER SET utf8mb4 DEFAULT NULL,
  `session` longtext CHARACTER SET utf8mb4,
  `last_login_date` datetime DEFAULT NULL,
  `date` datetime NOT NULL,
  `date_gmt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


ALTER TABLE `#__articles`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `alias` (`alias`);

ALTER TABLE `#__categories`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `#__languages`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `title` (`title`,`title_native`,`lang_code`);

ALTER TABLE `#__media`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `#__navigation`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `alias` (`alias`);

ALTER TABLE `#__pages`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `alias` (`alias`);

ALTER TABLE `#__settings`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `#__themes`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `alias` (`name`);

ALTER TABLE `#__users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `username` (`username`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `#__articles`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__categories`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__languages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__media`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__navigation`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__pages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__settings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `#__themes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
