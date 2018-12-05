<?php
/**
 * File: M_Products_pifa.php
 * Functionality: 产品pifa model
 * Author: 资料空白
 * Date: 2015-9-4
 */

class M_Products_pifa extends Model
{

	public function __construct()
	{
		$this->table = TB_PREFIX.'products_pifa';
		parent::__construct();
	}
	
	public function getPifa($pid)
	{
		$result = array();
		$result = $this->Field(array('qty','discount'))->Where(array('pid'=>$pid,'isdelete'=>0))->Order(array('qty'=>'DESC'))->Select();
		return $result;
	}  
}