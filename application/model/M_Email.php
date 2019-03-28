<?php
/**
 * File: M_Email.php
 * Functionality: 邮箱
 * Author: 资料空白
 * Date: 2016-03-21
 */

class M_Email extends Model
{

	public function __construct()
	{
		$this->table = TB_PREFIX.'email';
		parent::__construct();
	}

	public function getConfig($new=0)
	{
		$data = $emailConfig = array();

		$file_path=TEMP_PATH ."/email.json";
			if(file_exists($file_path) AND !$new){
			$data = json_decode(file_get_contents($file_path),true);
		}
		
		//取旧值
		if(!empty($data) AND isset($data['email']) AND (isset($data['expire_time']) AND $data['expire_time'] > time())){
			//做了随机发送的处理，随机选择账号发送
			$key = array_rand($data['email']);
			$emailConfig = $data['email'][$key];
		}
		if (empty($emailConfig) OR $new){
    		$email = $this->_getData();
    		$data['email'] = $email;
    		$data['expire_time'] = time() + 600;

			file_put_contents($file_path,json_encode($data));
			
			$key = array_rand($data['email']);
			$emailConfig = $data['email'][$key];
    	}
		
		return $emailConfig;
	} 

	private function _getData()
	{
		$where = array('isdelete'=>0);
		$email = $this->Where($where)->Select();
		return $email;
	}

}