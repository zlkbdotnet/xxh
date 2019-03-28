<?php
/**
 * File: M_Config.php
 * Functionality: 配置 model
 * Author: 资料空白
 * Date: 2015-9-4
 */

class M_Config extends Model
{

	public function __construct()
	{
		$this->table = TB_PREFIX.'config';
		parent::__construct();
	}

	/**
	 * 获取配置文件
	 * @param string $roleid
	 * @param string $password
	 * @return params on success or 0 or failure
	 */
	public function getConfig($new=0)
	{
		$data = $config = array();
		$file_path=TEMP_PATH ."/config.json";
		if(file_exists($file_path) AND !$new){
			$data = json_decode(file_get_contents($file_path),true);
		}
	
		//取旧值
		if(!empty($data) AND isset($data['config']) AND (isset($data['expire_time']) AND $data['expire_time'] > time())){
			$config =$data['config'];
		}
		if (empty($config) OR $new){
    		$data['config'] = $config = $this->_getData();
    		$data['expire_time'] = time() + 600;
			file_put_contents($file_path,json_encode($data));
    	}
		
		return $config;
	} 

	private function _getData()
	{
		$result = $this->Select();
		$config = array();
		if(!empty($result)){
			foreach($result AS $i){
				$config[$i['name']]=htmlspecialchars_decode($i['value'],ENT_QUOTES);
			}
		}

		return $config;
	}

	//批量修改配置－暂未使用，保留
	public function setConfig($params)
	{
		if(is_array($params) AND !empty($params)){
			$sql='UPDATE `xbsr_config` SET value = CASE name';
			$keys='';
			foreach($params AS $key=>$value){
				$value=htmlspecialchars($value , ENT_QUOTES);
				$sql.=" WHEN '{$key}' THEN '{$value}'";
				$keys.="'{$key}',";
			}
			$keys=rtrim($keys, ",");
			$sql.=" END WHERE name IN ({$keys})";
			$this->Query($sql);
			$this->getConfig(1);
			return true;
		}else{
			return false;
		}
	}
}