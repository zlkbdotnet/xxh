<?php
/**
 * File: M_Banner.php
 * Functionality: Banner model
 * Author: 资料空白
 * Date: 2019-07-09
 */

class M_Banner extends Model
{

	public function __construct()
	{
		$this->table = TB_PREFIX.'banner';
		parent::__construct();
	}

	public function getValue()
	{
		$result = $this->Order(array("sort_num"=>"DESC"))->Select();
		$value = array();
		if(!empty($result)){
			foreach($result AS $i){
				if(strlen($i['image'])>0){
					$value[$i['id']] = array("title"=>$i['title'],'image'=>$i['image'],'url'=>$i['url']);
				}
			}
		}
		return $value;
	}	
}