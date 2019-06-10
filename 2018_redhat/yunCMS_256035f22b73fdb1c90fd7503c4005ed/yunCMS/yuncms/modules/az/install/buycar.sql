DROP TABLE IF EXISTS `phpcms_buycar`;
CREATE TABLE IF NOT EXISTS `phpcms_buycar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `prov_userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `productid` int(10) unsigned NOT NULL DEFAULT '0',
  `modelid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `price` float(8,2) unsigned NOT NULL DEFAULT '0.00',
  `quantity` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` char(120) NOT NULL,
  `thumb` char(120) NOT NULL,
  `url` char(120) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `prov_userid` (`prov_userid`,`addtime`)
) TYPE=MyISAM ;