ALTER TABLE `t_products_type` ADD `password` VARCHAR(60) NOT NULL COMMENT '分类密码' AFTER `name`;
ALTER TABLE `t_products` ADD `password` VARCHAR(60) NOT NULL COMMENT '密码' AFTER `sort_num`;