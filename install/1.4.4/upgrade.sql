CREATE TABLE IF NOT EXISTS `t_ad` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL COMMENT '广告标题',
  `label` varchar(60) NOT NULL COMMENT '广告标签',
  `content` text NOT NULL COMMENT '广告内容',
  `isactive` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未激活 1激活',
  `locked` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0不锁,1锁，锁定后不能修改',
  `addtime` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `t_ad` ADD PRIMARY KEY (`id`);
 
ALTER TABLE `t_ad` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;  


INSERT INTO `t_ad` (`id`, `name`, `label`, `content`, `isactive`, `locked`, `addtime`) VALUES
(1, '产品首页广告', 'product_index_ad', '', 1, 1, 1562641909),
(2, '产品内容页广告', 'product_content_ad', '', 1, 1, 1562641909);
