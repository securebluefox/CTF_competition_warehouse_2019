DROP TABLE IF EXISTS `phpcms_dianping`;
CREATE TABLE `phpcms_dianping` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `dianpingid` char(40) NOT NULL,
  `dianping_typeid` int(3) NOT NULL,
  `siteid` smallint(8) NOT NULL,
  `dianping_nums` int(10) NOT NULL,
  `data1` tinyint(1) DEFAULT NULL,
  `data2` tinyint(1) DEFAULT NULL,
  `data3` tinyint(1) DEFAULT NULL,
  `data4` tinyint(1) DEFAULT NULL,
  `data5` tinyint(1) DEFAULT NULL,
  `data6` tinyint(1) DEFAULT NULL,
  `addtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) TYPE=MyISAM;