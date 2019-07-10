<?php
/*
 * 功能：关于我们
 * Author:资料空白
 * Date:20190529
 */
class AboutController extends IndexBasicController
{

	public function init()
	{
        parent::init();
	}

	public function indexAction()
	{
		$data = array();
		$data['title'] = "关于我们";
		$this->getView()->assign($data);
	}
}