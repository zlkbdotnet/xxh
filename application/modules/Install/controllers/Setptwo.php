<?php

/*
 * 功能：安装模块
 * Author:资料空白
 * Date:20180626
 */
use WriteiniFile\WriteiniFile;
class SetptwoController extends BasicController
{
	private $install_sql = INSTALL_PATH.'faka.sql';
	private $install_config = APP_PATH.'/conf/application.ini';
	
	public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
		if(file_exists(INSTALL_LOCK)){
			$this->redirect("/product/");
			return FALSE;
		}else{
			$data = array();
			$this->getView()->assign($data);
		}
    }
	
	public function ajaxAction()
	{
		if(file_exists(INSTALL_LOCK)){
			$data = array('code' => 1001, 'msg' => '已经安装过了');
			Helper::response($data);
		}
		$host = $this->getPost('host');
		$port = $this->getPost('port');
		$user = $this->getPost('user');
		$password = $this->getPost('password');
		$dbname = $this->getPost('dbname');
		
		$data = array();
		
		if($host AND $port AND $user AND $password AND $dbname){
            try {
				if(!preg_match("/^[A-Za-z\\0-9\\_\\\-]+$/",$dbname)){
					$data = array('code' => 1002, 'msg' =>"数据库名只能包含英文字母、中划线以及下划线");
					Helper::response($data);
				}
				if(file_exists($this->install_sql) AND is_readable($this->install_sql)){
					$sql = @file_get_contents($this->install_sql);
					if(!$sql){
						$data = array('code' => 1003, 'msg' =>"无法读取".$this->install_sql."文件,请检查文件是否存在且有读权限");
						Helper::response($data);
					}
				}else{
					$data = array('code' => 1004, 'msg' =>"无法读取".$this->install_sql."文件,请检查文件是否存在且有读权限");
					Helper::response($data);
				}
				
				if (!is_writable($this->install_config)){
					$data = array('code' => 1005, 'msg' =>"无法写入".$this->install_config."文件,请检查是否有写权限");
					Helper::response($data);
				}
				
				if (!is_writable(INSTALL_PATH)){
					$data = array('code' => 1006, 'msg' =>"无法写入目录".INSTALL_PATH.",请检查是否有写权限");
					Helper::response($data);
				}
				
                $pdo = new PDO("mysql:host=".$host.";port=".$port.";charset=utf8;",$user, $password, array(PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
				
				//是否判断数据库存在，默认不判断
				$isexistsRadio = false;
				if($isexistsRadio){
					$isexists = $pdo->query("show databases like '{$dbname}'");
					if($isexists->rowCount()>0){
						$data = array('code' => 1007, 'msg' =>"该数据库已存在");
						Helper::response($data);
					}
				}
				
				$pdo->query("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8 COLLATE utf8_general_ci;");
				$pdo->query("USE `{$dbname}`");
				$pdo->exec($sql);
				//20190319，这里添加一个延时，避免sql操作时间过长导致异常
				sleep(3);	
				$ini = new WriteiniFile($this->install_config);
				$ini->update([
					'product : common' => ['READ_HOST' => $host,'WRITE_HOST' => $host,'READ_PORT' => $port,'WRITE_PORT' => $port,'READ_USER' => $user,'WRITE_USER' => $user,'READ_PSWD' => $password,'WRITE_PSWD' => $password,'Default' => $dbname]
				]);
				$ini->write();
					
				$result = @file_put_contents(INSTALL_LOCK,VERSION,LOCK_EX);
				if (!$result){
					$data = array('code' => 1004, 'msg' =>"无法写入安装锁定到".INSTALL_LOCK."文件，请检查是否有写权限");
				}
				$data = array('code' => 1, 'msg' =>"SUCCESS");
            } catch (PDOException $e) {
				$data = array('code' => 1001, 'msg' =>"失败:".$e->getMessage());
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
}