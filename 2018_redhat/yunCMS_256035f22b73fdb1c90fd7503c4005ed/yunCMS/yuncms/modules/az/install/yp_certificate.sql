DROP TABLE IF EXISTS `phpcms_yp_certificate`;
CREATE TABLE IF NOT EXISTS `phpcms_yp_certificate` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `organization` varchar(100) DEFAULT NULL,
  `thumb` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `addtime` int(10) DEFAULT NULL,
  `endtime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) TYPE=MyISAM 