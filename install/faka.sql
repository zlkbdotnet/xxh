-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-06-30 12:03:22
-- 服务器版本： 10.1.33-MariaDB
-- PHP Version: 7.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `faka`
--

-- --------------------------------------------------------

--
-- 表的结构 `t_admin_login_log`
--

CREATE TABLE IF NOT EXISTS `t_admin_login_log` (
  `id` int(11) NOT NULL,
  `adminid` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '登录ip',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员登录日志';

-- --------------------------------------------------------

--
-- 表的结构 `t_admin_user`
--

CREATE TABLE IF NOT EXISTS `t_admin_user` (
  `id` int(11) NOT NULL,
  `email` varchar(55) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `secret` varchar(55) NOT NULL DEFAULT '',
  `updatetime` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `t_admin_user`
--

INSERT INTO `t_admin_user` (`id`, `email`, `password`, `secret`, `updatetime`) VALUES
(1, '43036456@qq.com', '76b1807fc1c914f15588520b0833fbc3', '78e055', 0);

-- --------------------------------------------------------

--
-- 表的结构 `t_config`
--

CREATE TABLE IF NOT EXISTS `t_config` (
  `id` int(11) NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '1' COMMENT '分类ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '配置名',
  `value` text NOT NULL COMMENT '配置内容',
  `tag` text NOT NULL COMMENT '备注',
  `lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '锁',
  `updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '最后修改时间'
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COMMENT='基础配置';

--
-- 转存表中的数据 `t_config`
--

INSERT INTO `t_config` (`id`, `catid`, `name`, `value`, `tag`, `lock`, `updatetime`) VALUES
(1, 1, 'registerswitch', '0', '是否开放注册功能,1是开放,0是关闭', 1, 1453452674),
(2, 1, 'limitiporder', '3', '同一ip当日下单限制（针对未付款订单）,不限制请设置为0', 1, 1453452674),
(3, 1, 'limitemailorder', '3', '同一email当日下单限制（针对未付款订单）,不限制请设置为0', 1, 1453452674),
(4, 1, 'weburl', 'http://faka.zlkb.net', '当前网站地址,用于支付站点异步返回，务必修改正确', 1, 1453452674),
(5, 1, 'adminemail', 'demo@demo.com', '管理员邮箱,用于接收邮件提醒用', 1, 1453452674),
(6, 1, 'webname', 'ZFAKA平台', '当前站点名称', 1, 1453452674),
(7, 1, 'webdescription', '本系统由资料空白开发', '当前站点描述', 1, 1453452674),
(8, 1, 'notice', '本系统商品均可正常购买。开源下载地址：github地址:&lt;a href=&quot;https://github.com/zlkbdotnet/zfaka/&quot; target=&quot;_blank&quot;&gt;https://github.com/zlkbdotnet/zfaka/&lt;/a&gt;', '首页公告', 1, 1453452674),
(9, 1, 'ad', '&lt;image src=&quot;/res/images/pay/supportme.jpg&quot;&gt;', '购买页默认内容', 1, 1453452674),
(10, 1, 'yzmswitch', '1', '验证码开关(1开，0关)', 1, 1453452674),
(11, 1, 'orderinputtype', '2', '订单必填输入框选择: 1邮箱 2QQ', 1, 1453452674),
(12, 1, 'tplindex', 'default', '首页显示模式，筛选(default)|列表(list)', 1, 1453452674),
(13, 1, 'logo', '/res/images/logo.png', 'LOGO地址,默认：/res/images/logo.png', 1, 1453452674),
(14, 1, 'tongji', '<!--统计js-->', '统计脚本', 1, 1453452674),
(15, 1, 'mprodcutdescriptionswitch', '0', '移动端商品详情，隐藏(0)|显示(1)', 1, 1453452674),
(16, 1, 'orderprefix', 'zlkb', '订单前缀，只能是英文和数字,且长度不要超过5个字符串建议不要超过5个字符串', 1, 1453452674),
(17, 1, 'backgroundimage', 'https://gss0.baidu.com/-fo3dSag_xI4khGko9WTAnF6hhy/zhidao/pic/item/6a600c338744ebf894c9e667dff9d72a6059a72a.jpg', '前台背景图片地址', 1, 1453452674),
(18, 1, 'headermenucolor', 'layui-bg-black', '前台顶部菜单配色方案', 1, 1453452674),
(19, 1, 'mousejqtx', '', '鼠标特效', 1, 1453452674),
(20, 1, 'layerad', '', '弹窗广告', 1, 1453452674),
(21, 1, 'loginswitch', '1', '登录开关', 1, 1453452674),
(22, 1, 'forgetpwdswitch', '0', '找回密码开关', 1, 1453452674),
(23, 1, 'adminyzmswitch', '1', '后台登录验证码开关', 1, 1453452674),
(24, 1, 'shortcuticon', '', 'ICO图标,格式必须是png或者ico或者gif', 1, 1453452674),
(25, 1, 'limitorderqty', '5', '单笔订单数量限制', 1, 1453452674),
(26, 1, 'discountswitch', '0', '折扣开关', 1, 1453452674),
(27, 1, 'qrserver', '/product/order/showqr/?url=', '生成二维码的服务地址,默认请填写:/product/order/showqr/?url=', 1, 1453452674),
(28, 1, 'paysubjectswitch', '0', '订单说明显示:0商品名,1订单号', 1, 1453452674),
(29, 1, 'tplproduct', 'default', '商品详情页模版', '1', 1546063186),
(30, 1, 'emailswitch', '1', '发送用户邮件开关', '1', 1546063186),
(31, 1, 'emailsendtypeswitch', '1', '发送用户邮件方式筛选开关', '1', '1546063186');
-- --------------------------------------------------------

--
-- 表的结构 `t_config_cat`
--

CREATE TABLE IF NOT EXISTS `t_config_cat` (
  `id` int(11) NOT NULL,
  `catname` varchar(32) NOT NULL DEFAULT '' COMMENT '配置分类名',
  `catkey` varchar(32) NOT NULL DEFAULT '' COMMENT '配置分类KEY'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='基础配置分类';

--
-- 转存表中的数据 `t_config_cat`
--

INSERT INTO `t_config_cat` (`id`, `catname`, `catkey`) VALUES
(1, '基础设置', 'basic'),
(2, '其他设置', 'other');

-- --------------------------------------------------------

--
-- 表的结构 `t_email`
--

CREATE TABLE IF NOT EXISTS `t_email` (
  `id` int(11) NOT NULL,
  `mailaddress` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `mailpassword` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱密码',
  `sendmail` varchar(255) NOT NULL DEFAULT '' COMMENT '	发件人email',
  `sendname` varchar(255) NOT NULL DEFAULT '' COMMENT '发送人昵称',
  `port` varchar(55) NOT NULL DEFAULT '' COMMENT '端口号',
  `host` varchar(255) NOT NULL DEFAULT '' COMMENT '发送邮件服务端',
  `isssl` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0关，1开',
  `isactive` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0未激活 1激活',
  `isdelete` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `t_email_code`
--

CREATE TABLE IF NOT EXISTS `t_email_code` (
  `id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作类型',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '内容',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
  `result` text COMMENT '结果',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '结果0未发送 1已发送',
  `checkedStatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未校验，1已校验'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `t_email_queue`
--

CREATE TABLE IF NOT EXISTS `t_email_queue` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT ' 收件人',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `sendtime` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `sendresult` text COMMENT '发送错误',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0,未发送 ，1已发送，-1,失败',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `t_help`
--

CREATE TABLE IF NOT EXISTS `t_help` (
  `id` int(11) NOT NULL,
  `typeid` int(11) NOT NULL DEFAULT '1' COMMENT '类型',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `isactive` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1是激活，0是不激活',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `t_help`
--

INSERT INTO `t_help` (`id`, `typeid`, `title`, `content`, `isactive`, `addtime`) VALUES
(1, 1, '这是什么系统', '这就是一个伟大的系统', 1, 1527775425);

-- --------------------------------------------------------

--
-- 表的结构 `t_order`
--

CREATE TABLE IF NOT EXISTS `t_order` (
  `id` int(11) NOT NULL,
  `orderid` varchar(55) NOT NULL DEFAULT '0' COMMENT '订单号',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `qq` varchar(50) NOT NULL COMMENT 'QQ号码',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '产品id',
  `productname` varchar(255) NOT NULL DEFAULT '' COMMENT '订单名称',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `chapwd` varchar(55) NOT NULL DEFAULT '' COMMENT '查询密码',
  `ip` varchar(55) NOT NULL DEFAULT '' COMMENT 'ip',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态0待支付,1待处理,2已完成,3处理失败,-1删除',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `paytime` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `tradeid` varchar(255) NOT NULL DEFAULT '' COMMENT '外部订单id',
  `paymethod` varchar(255) NOT NULL DEFAULT '' COMMENT '支付渠道',
  `paymoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付总金额',
  `kami` text COMMENT '卡密',
  `addons` text NOT NULL COMMENT '备注',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `t_payment`
--

CREATE TABLE IF NOT EXISTS `t_payment` (
  `id` int(11) NOT NULL,
  `payment` varchar(55) DEFAULT '' COMMENT '支付名',
  `payname` varchar(55) NOT NULL DEFAULT '' COMMENT '显示名称',
  `payimage` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
  `alias` varchar(55) NOT NULL DEFAULT '' COMMENT '别名',
  `sign_type` enum('RSA','RSA2','MD5','HMAC-SHA256') NOT NULL DEFAULT 'RSA2',
  `app_id` varchar(255) NOT NULL DEFAULT '',
  `app_secret` varchar(255) NOT NULL DEFAULT '',
  `ali_public_key` text,
  `rsa_private_key` text,
  `configure3` text NOT NULL COMMENT '配置3',
  `configure4` text NOT NULL COMMENT '配置4',
  `overtime` int(11) NOT NULL DEFAULT '0' COMMENT '支付超时,0是不限制',
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未激活,1已激活'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `t_payment`
--

INSERT INTO `t_payment` (`id`, `payment`, `payname`, `payimage`, `alias`, `sign_type`, `app_id`, `app_secret`, `ali_public_key`, `rsa_private_key`, `configure3`,`configure4`,`overtime`, `active`) VALUES
(1, '支付宝当面付', '支付宝', '/res/images/pay/alipay.jpg', 'zfbf2f', 'RSA2', '', '', '', '','','',0, 0),
(2, '码支付-支付宝扫码支付', '支付宝', '/res/images/pay/alipay.jpg', 'codepayalipay', 'RSA2', '', '', '', '', '','',300, 0),
(3, '码支付-QQ扫码支付', '手机QQ', '/res/images/pay/qqpay.jpg', 'codepayqq', 'RSA2', '', '', '', '','','',300, 0),
(4, '码支付-微信扫码支付', '微信', '/res/images/pay/weixin.jpg', 'codepaywx', 'RSA2', '', '', '', '','','',300, 0),
(5, '支付宝电脑网站支付(WEB)', '支付宝', '/res/images/pay/alipay.jpg', 'zfbweb', 'RSA2', '2018********', '', '', '', '','',0, 0),
(6, '微信扫码支付', '微信', '/res/images/pay/weixin.jpg', 'wxf2f', 'MD5', '', '', '', '', '', '',0, 0),
(7, '有赞接口', '微信', '/res/images/pay/yzpay.jpg', 'yzpay', 'RSA2', '', '', '', '', '', '',0, 0),
(8, '收款宝(微信)', '微信', '/res/images/pay/weixin.jpg', 'zlkbcodepaywx', 'RSA2', '', '', '', '', 'https://codepay.zlkb.net/api/order', '0',300, 0),
(9, '收款宝(支付宝)', '支付宝', '/res/images/pay/alipay.jpg', 'zlkbcodepayalipay', 'RSA2', '', '', '', '', 'https://codepay.zlkb.net/api/order', '0',300, 0),
(10, '收款宝(QQ)', 'QQ', '/res/images/pay/qqpay.jpg', 'zlkbcodepayqq', 'RSA2', '', '', '', '', 'https://codepay.zlkb.net/api/order', '0',300, 0),
(11, '微信H5支付', '微信', '/res/images/pay/weixin.jpg', 'wxh5', 'MD5', '', '', '', '', '','', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `t_products`
--

CREATE TABLE IF NOT EXISTS `t_products` (
  `id` int(11) NOT NULL,
  `typeid` int(11) NOT NULL DEFAULT '1' COMMENT '类型id',
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未激活 1激活',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '产品名',
  `password` varchar(60) NOT NULL DEFAULT '' COMMENT '密码',
  `description` text COMMENT '描述',
  `stockcontrol` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0不控制,1控制',
  `qty` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `auto` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0手动,1自动',
  `addons` text NOT NULL COMMENT '备注',
  `sort_num` int(11) NOT NULL DEFAULT '1' COMMENT '排序',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除',
  `imgurl` text NOT NULL DEFAULT '' COMMENT '产品图片'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `t_products`
--

INSERT INTO `t_products` (`id`, `typeid`, `active`, `name`, `password`,`description`, `stockcontrol`, `qty`, `price`, `auto`, `sort_num`, `addtime`,`isdelete`) VALUES
(1, 1, 1, '测试商品', '','测试使用', 0, 0, '0.10', 1, 99, 1528962221,0);

-- --------------------------------------------------------

--
-- 表的结构 `t_products_card`
--

CREATE TABLE IF NOT EXISTS `t_products_card` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '商品id',
  `card` text COMMENT '卡密',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0可用 1已使用',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `t_products_card`
--

INSERT INTO `t_products_card` (`id`, `pid`, `card`, `addtime`, `active`,`isdelete`) VALUES
(1, 1, '资料空白是大帅锅', 1530082076, 0,0);


CREATE TABLE IF NOT EXISTS `t_products_pifa` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '商品d',
  `qty` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠价格',
  `tag` varchar(255) NOT NULL COMMENT '简单说明',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `t_products_type`
--

CREATE TABLE IF NOT EXISTS `t_products_type` (
  `id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL DEFAULT '' COMMENT '类型命名',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '分类描述',
  `password` varchar(60) NOT NULL DEFAULT '' COMMENT '分类密码',
  `sort_num` int(11) NOT NULL DEFAULT '1' COMMENT '排序',
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未激活,1已激活',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `t_products_type`
--

INSERT INTO `t_products_type` (`id`, `name`, `description`,`password`,`sort_num`, `active`,`isdelete`) VALUES
(1, '测试商品', '测试商品','',1, 1,0);

-- --------------------------------------------------------

--
-- 表的结构 `t_seo`
--

CREATE TABLE IF NOT EXISTS `t_seo` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `t_ticket`
--

CREATE TABLE IF NOT EXISTS `t_ticket` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `typeid` int(11) NOT NULL DEFAULT '1' COMMENT '类型',
  `priority` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0不重要 1重要',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '主题',
  `content` text COMMENT '内容',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0,刚创建;1,已回复;5已完结',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `t_user`
--

CREATE TABLE IF NOT EXISTS `t_user` (
  `id` int(11) NOT NULL,
  `groupid` int(11) NOT NULL DEFAULT '1' COMMENT '分组ID',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `qq` varchar(20) NOT NULL DEFAULT '' COMMENT 'qq',
  `mobilephone` varchar(15) NOT NULL DEFAULT '' COMMENT '手机',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `tag` varchar(255) NOT NULL DEFAULT '' COMMENT '用户自己的备注',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `t_user`
--

INSERT INTO `t_user` (`id`, `groupid`, `nickname`, `password`, `email`, `qq`, `mobilephone`, `money`, `integral`, `tag`, `createtime`) VALUES
(1, 1, '测试账户', 'e10adc3949ba59abbe56e057f20f883e', '43036456@qq.com', '43036456', '13717335559', '0.00', 0, '资料空白是大帅锅', 1525857488);

-- --------------------------------------------------------

--
-- 表的结构 `t_user_group`
--

CREATE TABLE IF NOT EXISTS `t_user_group` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户组名',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '折扣'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `t_user_group`
--

INSERT INTO `t_user_group` (`id`, `name`, `remark`, `discount`) VALUES
(1, '普通', '普通用户', '0.00'),
(2, 'VIP1', 'VIP1用户', '0.00'),
(3, 'VIP2', 'VIP2用户', '0.00'),
(4, 'VIP3', 'VIP3用户', '0.00');

-- --------------------------------------------------------

--
-- 表的结构 `t_user_login_logs`
--

CREATE TABLE IF NOT EXISTS `t_user_login_logs` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `ip` varchar(25) NOT NULL DEFAULT '' COMMENT '登录ip',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--
ALTER TABLE `t_products_pifa`
  ADD PRIMARY KEY (`id`);
--
-- Indexes for table `t_admin_login_log`
--
ALTER TABLE `t_admin_login_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_admin_user`
--
ALTER TABLE `t_admin_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_config`
--
ALTER TABLE `t_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_config_cat`
--
ALTER TABLE `t_config_cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_email`
--
ALTER TABLE `t_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_email_code`
--
ALTER TABLE `t_email_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_email_queue`
--
ALTER TABLE `t_email_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_help`
--
ALTER TABLE `t_help`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_order`
--
ALTER TABLE `t_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_payment`
--
ALTER TABLE `t_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_products`
--
ALTER TABLE `t_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_products_card`
--
ALTER TABLE `t_products_card`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_products_type`
--
ALTER TABLE `t_products_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_seo`
--
ALTER TABLE `t_seo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_ticket`
--
ALTER TABLE `t_ticket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_user`
--
ALTER TABLE `t_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_user_group`
--
ALTER TABLE `t_user_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_user_login_logs`
--
ALTER TABLE `t_user_login_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_admin_login_log`
--
ALTER TABLE `t_admin_login_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_admin_user`
--
ALTER TABLE `t_admin_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_config`
--
ALTER TABLE `t_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `t_config_cat`
--
ALTER TABLE `t_config_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `t_email`
--
ALTER TABLE `t_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_email_code`
--
ALTER TABLE `t_email_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_email_queue`
--
ALTER TABLE `t_email_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_help`
--
ALTER TABLE `t_help`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_order`
--
ALTER TABLE `t_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_payment`
--
ALTER TABLE `t_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `t_products`
--
ALTER TABLE `t_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_products_card`
--
ALTER TABLE `t_products_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_products_type`
--
ALTER TABLE `t_products_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_seo`
--
ALTER TABLE `t_seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_ticket`
--
ALTER TABLE `t_ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_user`
--
ALTER TABLE `t_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_user_group`
--
ALTER TABLE `t_user_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `t_user_login_logs`
--

ALTER TABLE `t_products_pifa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE `t_user_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
