ALTER TABLE `t_payment` CHANGE `sign_type` `sign_type` ENUM('RSA','RSA2','MD5','HMAC-SHA256') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'RSA2';
UPDATE `t_email_queue` SET `status` = '2' WHERE `status` = '-1';
INSERT INTO `t_config` (`id`, `catid`, `name`, `value`, `tag`, `lock`, `updatetime`) VALUES (30, '1', 'emailswitch', '1', '发送用户邮件开关', '1', '1546063186');