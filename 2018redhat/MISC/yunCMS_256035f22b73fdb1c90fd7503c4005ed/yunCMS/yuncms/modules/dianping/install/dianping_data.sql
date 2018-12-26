DROP TABLE IF EXISTS `phpcms_dianping_data`;
CREATE TABLE `phpcms_dianping_data` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dianpingid` char(40) NOT NULL,
  `userid` mediumint(8) NOT NULL,
  `username` char(20) NOT NULL,
  `module` char(20) NOT NULL,
  `modelid` smallint(5) NOT NULL,
  `catid` smallint(5) NOT NULL,
  `dianping_typeid` tinyint(3) NOT NULL,
  `siteid` smallint(8) NOT NULL,
  `content` varchar(200) NOT NULL,
  `data` char(255) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `is_useful` char(10) NOT NULL,
  `addtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) TYPE=MyISAM;