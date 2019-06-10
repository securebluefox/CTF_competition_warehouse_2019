DROP TABLE IF EXISTS `phpcms_yp_guestbook`;
CREATE TABLE IF NOT EXISTS `phpcms_yp_guestbook` (
  `gid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL ,
  `telephone` varchar(18) NOT NULL,
  `qq` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' ,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`gid`),
  KEY `userid` (`userid`)
) TYPE=MyISAM;