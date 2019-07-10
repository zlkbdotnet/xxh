<?php

/*
 * 功能：帮助文档
 * Author:资料空白
 * Date:20180508
 */

class HelpController extends IndexBasicController
{

    private $m_help;
	public function init()
    {
        parent::init();
		$this->m_help = $this->load('help');
    }

    public function indexAction()
    {
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;		
		
		$data = array();
		$where = array('isactive'=>1);
		$items = $this->m_help->Where($where)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
		$data['items'] = $items;
		
		
		$data['title'] = "帮助中心";
        $this->getView()->assign($data);
    }
	
	public function ajaxAction()
	{
		$where = array('isactive'=>1);
		
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$total=$this->m_help->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$items = $this->m_help->Where($where)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
			
            if (empty($items)) {
                $data = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
            } else {
                $data = array('code'=>0,'count'=>$total,'data'=>$items,'msg'=>'有数据');
            }
        } else {
            $data = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
        }
		Helper::response($data);
	}
	
	public function detailAction()
	{
		$id=$this->get('id');
		if(is_numeric($id) AND $id>0){
			$where = array('isactive'=>1);
			$items=$this->m_help->Where($where)->SelectByID('',$id);
			$data = array();
			$data['items'] = $items;
			$data['title'] = "帮助中心";
			$this->getView()->assign($data);
		}else{
            $this->redirect("/help/");
            return FALSE;
		}
	}
}