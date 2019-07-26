<?php
/*
 * 功能：首页
 * Author:资料空白
 * Date:20190726
 */
class IndexController extends IndexBasicController
{
	private $m_help;
	private $m_banner;
	private $m_links;
	
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
			//获取友情链接
			$this->m_links = $this->load('links');
			$where2 = array('isactive'=>1);
			$where3 = "endtime=0 OR endtime>".CUR_TIMESTAMP;
			$items_links = $this->m_links->Where($where2)->Where($where3)->Order(array('sort_num'=>'DESC'))->Select();
			$data['items_links'] = $items_links;
			$data['title'] = "首页";
			$this->getView()->assign($data);
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
	}
}