<?php
/*
 * 功能：会员首页
 * Author:资料空白
 * Date:20180604
 */
class IndexController extends IndexBasicController
{
	private $m_help;
	private $m_banner;
	
	public function init()
	{
        parent::init();
	}

	public function indexAction()
	{
		if(file_exists(INSTALL_LOCK)){
			$data = array();
			//获取帮助中心文章
			$this->m_help = $this->load('help');
			$where = array('isactive'=>1);
			$items_help = $this->m_help->Where($where)->Limit("6")->Order(array('id'=>'DESC'))->Select();
			$data['items_help'] = $items_help;
			//获取banner图
			$this->m_banner = $this->load('banner');
			$items_banner = $this->m_banner->getValue();
			$data['items_banner'] = $items_banner;
			
			$data['title'] = "首页";
			$this->getView()->assign($data);
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
	}
}