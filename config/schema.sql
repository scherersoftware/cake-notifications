CREATE TABLE `notification_contents` (
  `id` char(36) NOT NULL,
  `notification_identifier` varchar(255) NOT NULL,
  `notes` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `notification_queue` (
  `id` char(36) NOT NULL,
  `locale` varchar(3) NOT NULL,
  `recipient_user_id` char(36) NOT NULL,
  `notification_identifier` varchar(255) DEFAULT NULL,
  `config` mediumtext NOT NULL,
  `transport` varchar(45) NOT NULL,
  `locked` tinyint(1) DEFAULT NULL,
  `sent` tinyint(1) DEFAULT NULL,
  `send_after` datetime DEFAULT NULL,
  `send_tries` int(2) unsigned NOT NULL DEFAULT '0',
  `debug` mediumtext,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_notifications` (
  `id` char(36) NOT NULL,
  `locale` varchar(3) NOT NULL,
  `recipient_user_id` char(36) NOT NULL,
  `model` varchar(145) DEFAULT NULL,
  `foreign_key` char(36) DEFAULT NULL,
  `notification_identifier` varchar(255) DEFAULT NULL,
  `config` mediumtext NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB DEFAULT CHARSET=utf8;
