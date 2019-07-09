<?php
/*
 * 功能：会员首页
 * Author:资料空白
 * Date:20180604
 */
class IndexController extends IndexBasicController
{

	public function init()
	{
        parent::init();
	}

	public function indexAction()
	{
		if(file_exists(INSTALL_LOCK)){
			$data = array();
			$data['title'] = "首页";
			$this->getView()->assign($data);
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
	}
}