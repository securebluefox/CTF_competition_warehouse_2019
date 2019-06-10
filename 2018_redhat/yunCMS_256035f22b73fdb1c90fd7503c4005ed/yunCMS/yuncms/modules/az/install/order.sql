DROP TABLE IF EXISTS `phpcms_order`;
CREATE TABLE IF NOT EXISTS `phpcms_order` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `order_sn` char(50) NOT NULL ,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0' ,
  `username` char(20) NOT NULL ,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' ,
  `buycarid` text NOT NULL ,
  `contactname` char(50) NOT NULL ,
  `email` char(40) NOT NULL COMMENT 'email',
  `telephone` char(20) NOT NULL ,
  `discount` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `money` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `quantity` int(8) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) NOT NULL DEFAULT '0' ,
  `usernote` char(255) NOT NULL ,
  `ip` char(15) NOT NULL DEFAULT '0.0.0.0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `postal` char(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) TYPE=MyISAM  ;