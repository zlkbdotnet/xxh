INSERT INTO `t_payment` (`id`, `payment`, `payname`, `payimage`, `alias`, `sign_type`, `app_id`, `app_secret`, `ali_public_key`, `rsa_private_key`, `configure3`, `overtime`, `active`) VALUES(9, '收款宝(支付宝)', '支付宝', '/res/images/pay/alipay.jpg', 'zlkbcodepayalipay', 'RSA2', '', '', '', '', '', 300, 0);
UPDATE `t_payment` SET `payment` = '收款宝(微信)' WHERE `id` = 8;
INSERT INTO `t_config` (`id`, `catid`, `name`, `value`, `tag`, `lock`, `updatetime`) VALUES (24, 1, 'shortcuticon', '', 'ICO图标,格式必须是png或者ico或者gif', 1, 1453452674);
