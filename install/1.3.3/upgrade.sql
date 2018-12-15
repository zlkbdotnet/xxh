UPDATE `t_payment` SET `payimage` = '/res/images/pay/qqpay.jpg' WHERE `id` = 10;
ALTER TABLE `t_products_pifa` CHANGE `money` `discount` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠价格';