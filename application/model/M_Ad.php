<?php
/**
 * File: M_Ad.php
 * Functionality: 广告 model
 * Author: 资料空白
 * Date: 2019-07-09
 */

class M_Ad extends Model
{

	public function __construct()
	{
		$this->table = TB_PREFIX.'ad';
		parent::__construct();
	}

	private function getValue()
	{
		$result = $this->Select();
		$value = array();
		if(!empty($result)){
			foreach($result AS $i){
				$value[$i['label']] = htmlspecialchars_decode($i['content'],ENT_QUOTES);
			}
		}
		return $value;
	}	
}