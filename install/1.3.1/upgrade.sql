CREATE TABLE IF NOT EXISTS `t_products_pifa` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '商品d',
  `qty` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠价格',
  `tag` varchar(255) NOT NULL COMMENT '简单说明',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `t_products_pifa`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `t_products_pifa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
UPDATE `t_payment` SET `payimage` = '/res/images/pay/qqpay.jpg' WHERE `id` = 10;