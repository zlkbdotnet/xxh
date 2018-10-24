ALTER TABLE `t_payment` ADD `configure3` TEXT NOT NULL COMMENT '配置3';
ALTER TABLE `t_payment` CHANGE `sign_type` `sign_type` ENUM('RSA','RSA2','MD5') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'RSA2';
INSERT INTO `t_payment` (`id`, `payment`, `payname`, `payimage`, `alias`, `sign_type`, `app_id`, `app_secret`, `ali_public_key`, `rsa_private_key`, `configure3`, `overtime`, `active`) VALUES (6, '微信扫码支付', '微信', '/res/images/pay/weixin.jpg', 'wxf2f', 'MD5', '', '', '', '', '', 0, 0);
ALTER TABLE `t_products` ADD `addons` TEXT NOT NULL COMMENT '附加输入内容' AFTER `auto`;
ALTER TABLE `t_order` ADD `addons` TEXT NOT NULL COMMENT '备注' AFTER `kami`;
