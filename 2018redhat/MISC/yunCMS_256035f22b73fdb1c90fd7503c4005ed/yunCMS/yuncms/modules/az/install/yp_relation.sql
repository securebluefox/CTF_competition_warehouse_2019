DROP TABLE IF EXISTS `phpcms_yp_relation`;
CREATE TABLE IF NOT EXISTS `phpcms_yp_relation` (
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `catid` mediumint(8) unsigned NOT NULL DEFAULT '0'
) TYPE=MyISAM;