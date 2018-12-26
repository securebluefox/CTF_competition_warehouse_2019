DROP TABLE IF EXISTS `phpcms_comment_relation`;
CREATE TABLE IF NOT EXISTS `phpcms_comment_relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `module` char(20) NOT NULL,
  `modelid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `contentid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`,`modelid`,`contentid`)
) TYPE=MyISAM ;