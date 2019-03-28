<?php
header('content-Type:text/html;charset=utf-8;');
define('APP_PATH',  dirname(dirname(__FILE__)));

if(!file_exists(APP_PATH.'/install/install.lock')){
	include_once( dirname(__FILE__)."/init.php");
}
\Yaf\Loader::import(APP_PATH.'/application/init.php');
$app = new \Yaf\Application(APP_PATH.'/conf/application.ini');
$app->getDispatcher()->throwException(TRUE);
$app->bootstrap()->run();