<?php

/*
 * 功能：安装升级模块
 * Author:资料空白
 * Date:20180702
 */

class UpgradeController extends AdminBasicController
{
	private $all_version;
	public function init()
    {
        parent::init();
		$this->all_version = ['1.0.0','1.0.2','1.0.3','1.0.4','1.0.5','1.0.6','1.0.7','1.0.8','1.0.9','1.1.0','1.1.1','1.1.2','1.1.3','1.1.4','1.1.5','1.1.6','1.1.7','1.1.8','1.1.9','1.2.0','1.2.1','1.2.2','1.2.3','1.2.4','1.2.5','1.2.6','1.2.7','1.2.8','1.2.9','1.3.0','1.3.1','1.3.3','1.3.4','1.3.5','1.3.6','1.3.7','1.3.8','1.3.9','1.4.0'];
    }

    public function indexAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		if(file_exists(INSTALL_LOCK)){
			//安装版本version,<= 当前待更新版本VERSION <=远程最新版本update_version
			$version = @file_get_contents(INSTALL_LOCK);
			$version = str_replace(array("\r","\n","\t"), "", $version);
			$version = strlen(trim($version))>0?$version:'1.0.0';

			if(version_compare(trim($version), trim(VERSION), '<' )){
				$data = array();
				$update_version = $this->_getUpdateVersion($version);
				if($update_version==''){
					$data['update_version'] = '未知的版本';
					$data['upgrade_desc'] = "抱歉,我表示很难理解你为什么能看到这条信息";
					$data['upgrade_sql'] = '';
					$data['button'] = false;
				}else{
					if(version_compare(trim($update_version),trim(VERSION),  '<=' )){
						$data['update_version'] = $update_version;
						$desc = @file_get_contents(INSTALL_PATH.'/'.$update_version.'/upgrade.txt');
						$data['upgrade_desc'] = $desc;
						if(file_exists(INSTALL_PATH.'/'.$update_version.'/upgrade.sql')){
							$data['upgrade_sql'] = INSTALL_PATH.'/'.$update_version.'/upgrade.sql';
						}else{
							$data['upgrade_sql'] = '';
						}
						$data['button'] = true;
					}else{
						$data['update_version'] = VERSION;
						$data['button'] = false;
						$data['upgrade_desc'] = "抱歉,我表示很难理解你为什么能看到这条信息";
					}
				}
				$data['version'] = $version;
				$this->getView()->assign($data);
			}else{
				$this->redirect('/'.ADMIN_DIR);
				return FALSE;
			}
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
    }
	
	public function ajaxAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$method = $this->getPost('method',false);
		$data = array();
		
		if($method AND $method=='upgrade'){
            try {
				$version = @file_get_contents(INSTALL_LOCK);
				$version = str_replace(array("\r","\n","\t"), "", $version);
				$version = strlen(trim($version))>0?$version:'1.0.0';
				if(version_compare(trim($version), trim(VERSION), '<' )){
					$update_version = $this->_getUpdateVersion($version);
					if($update_version==''){
						$data = array('code' => 1, 'msg' =>"版本信息异常");
						Helper::response($data);
					}
				}else{
					$data = array('code' => 1, 'msg' =>"请勿重复升级");
					Helper::response($data);
				}
				
				$upgrade_sql = INSTALL_PATH.'/'.$update_version.'/upgrade.sql';
				
				if(file_exists($upgrade_sql) AND is_readable($upgrade_sql)){
					$sql = @file_get_contents($upgrade_sql);
					if(!$sql){
						$data = array('code' => 1003, 'msg' =>"无法读取".$upgrade_sql."文件,请检查文件是否存在且有读权限");
						Helper::response($data);
					}
				}else{
					$data = array('code' => 1004, 'msg' =>"无法读取".$upgrade_sql."文件,请检查文件是否存在且有读权限");
					Helper::response($data);
				}
				
				if (!is_writable(INSTALL_LOCK)){
					$data = array('code' => 1006, 'msg' =>"无法写入文件".INSTALL_LOCK.",请检查是否有写权限");
					Helper::response($data);
				}
				
				$m_config = $this->load('config');
                $m_config->Query($sql);
				
				$result = @file_put_contents(INSTALL_LOCK,$update_version,LOCK_EX);
				if (!$result){
					$data = array('code' => 1004, 'msg' =>"无法写入安装锁定到".INSTALL_LOCK."文件，请检查是否有写权限");
				}
				//20190319，这里添加一个延时，避免sql操作时间过长导致异常
				sleep(10);
				//更新配置缓存 
				$m_config->getConfig(1);
				$data = array('code' => 1, 'msg' =>"SUCCESS");
            } catch (\Exception $e) {
				$data = array('code' => 1001, 'msg' =>"失败:".$e->getMessage());
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
	//获取下一版本号
	private function _getUpdateVersion($version)
	{
		$offset = array_search($version,$this->all_version);
		if($offset>=0){
			$k = $offset+1;
			if(isset($this->all_version[$k])){
				return $this->all_version[$k];
			}
		}
		return end($this->all_version);
	}
}