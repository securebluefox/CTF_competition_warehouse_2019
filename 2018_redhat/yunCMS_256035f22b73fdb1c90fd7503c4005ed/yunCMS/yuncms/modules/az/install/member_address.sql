DROP TABLE IF EXISTS `phpcms_member_address`;
CREATE TABLE IF NOT EXISTS `phpcms_member_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `truename` char(40) NOT NULL,
  `province` char(40) NOT NULL DEFAULT '0',
  `address` char(255) NOT NULL,
  `mobile` char(13) NOT NULL,
  `telephone` char(15) NOT NULL,
  `email` char(32) NOT NULL,
  `code` mediumint(6) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) TYPE=MyISAM  ;