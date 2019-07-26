<?php
/**
 * File: M_Links.php
 * Functionality: 友情链接 model
 * Author: 资料空白
 * Date: 2019-07-26
 */

class M_Links extends Model
{

	public function __construct()
	{
		$this->table = TB_PREFIX.'links';
		parent::__construct();
	}
}