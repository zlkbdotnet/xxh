UPDATE `t_config` SET `name` = 'registerswitch' WHERE `id` = 1;
UPDATE `t_config` SET `name` = 'limitiporder' WHERE `id` = 2;
UPDATE `t_config` SET `name` = 'limitemailorder' WHERE `id` = 3;
UPDATE `t_config` SET `name` = 'weburl' WHERE `id` = 4;
UPDATE `t_config` SET `name` = 'adminemail' WHERE `id` = 5;
UPDATE `t_config` SET `name` = 'webname' WHERE `id` = 6;
UPDATE `t_config` SET `name` = 'webdescription' WHERE `id` = 7;
UPDATE `t_config` SET `name` = 'yzmswitch' WHERE `id` = 10;
UPDATE `t_config` SET `name` = 'orderinputtype' WHERE `id` = 11;
UPDATE `t_config` SET `name` = 'tplindex' WHERE `id` = 12;
UPDATE `t_config` SET `name` = 'mprodcutdescriptionswitch' WHERE `id` = 15;
UPDATE `t_config` SET `name` = 'orderprefix' WHERE `id` = 16;
UPDATE `t_payment` SET `payment` = '收款宝' WHERE `id` = 8;
INSERT INTO `t_config` (`id`, `catid`, `name`, `value`, `tag`, `lock`, `updatetime`) VALUES
(21, 1, 'loginswitch', '1', '登录开关', 1, 1453452674),
(22, 1, 'forgetpwdswitch', '0', '找回密码开关', 1, 1453452674);