<?php
/*
 * 功能：消息处理
 * Author:资料空白
 * Date:20180604
 */
class ShowmsgController extends BasicController
{

 	public function init()
	{
        parent::init();
	}
	
	public function indexAction()
	{
		$data = array();
		$data['code']=$this->getParam('code');
		$data['msg']=$this->getParam('msg');
		$url=$this->getParam('url',false);
		$url=isset($url)?$url:'/';
		$data['url']=$url;
		$data['title'] = "操作提示";
		$this->getView()->assign($data);
	}
}