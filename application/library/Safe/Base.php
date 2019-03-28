<?php
/**
 * @date 2017-11-23
 * @author zlkb.net
 * @desc 安全模块－基础类
 */

namespace Safe;
class Base
{
    public $value;
	
    public function __construct($str=null)
    {
        $this->value = $str;
    }

	/**
	* 通用函数
	* 
	* return object
	*/
    public function __call($name, $args)
    {
        array_unshift($args, $this->value);
        $this->value = call_user_func_array($name, $args);
        return $this;
    }

	/**
	* 获取内容
	* 
	* return int
	*/
    public function getValue()
    {
        return $this->value;
    }
	
	/**
	* 获取长度
	* 
	* return int
	*/
    public function strlen()
    {
        return strlen($this->value);
    }
	
	/**
	* 去除二边的空格
	* 
	* return object
	*/	
	public function trim()
	{
		$this->value = trim($this->value);
		return $this;
	}
	
	/**
	* 去除所有的空格
	* 
	* return object
	*/		
	public function trimall()
	{
		$oldchar = array(" ","　","\t","\n","\r");
		$newchar = array("","","","","");
		$this->value = str_replace($oldchar,$newchar,$this->value);
		return $this;
	}
	
	
}