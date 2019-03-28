<?php

/*
 * 功能：后台中心－邮件队列
 * Author:资料空白
 * Date:20180509
 */

class EmailqueueController extends AdminBasicController
{
	private $m_email_queue;
    public function init()
    {
        parent::init();
		$this->m_email_queue = $this->load('email_queue');
    }

    public function indexAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }

		$data = array();
		$this->getView()->assign($data);
    }

	//ajax
	public function ajaxAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		$where1 = "1";
		$status = $this->get('status');
		if(is_numeric($status) AND $status>-1){
			$where1 .= " AND status = {$status}"; 
		}
		
		$where = array('isdelete'=>0);
		
		
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$total=$this->m_email_queue->Where($where)->Where($where1)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$items=$this->m_email_queue->Where($where)->Where($where1)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
			
            if (empty($items)) {
                $data = array('code'=>1001,'count'=>0,'data'=>array(),'msg'=>'无数据');
            } else {
                $data = array('code'=>0,'count'=>$total,'data'=>$items,'msg'=>'有数据');
            }
        } else {
            $data = array('code'=>1000,'count'=>0,'data'=>array(),'msg'=>'无数据');
        }
		Helper::response($data);
	}
	
    public function deleteAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$id = $this->get('id',false);
		$csrf_token = $this->getPost('csrf_token', false);
        if ($csrf_token) {
			if ($this->VerifyCsrfToken($csrf_token)) {
				if($id AND is_numeric($id) AND $id>0){
					$where1 = array('id'=>$id);
					$delete = $this->m_email_queue->Where($where1)->Update(array('isdelete'=>1));
					if($delete){
						$data = array('code' => 1, 'msg' => '删除成功', 'data' => '');
					}else{
						$data = array('code' => 1003, 'msg' => '删除失败', 'data' => '');
					}
				}else{
					$ids = json_decode($id,true);
					if(isset($ids['ids']) AND !empty($ids['ids'])){
						$idss = implode(",",$ids['ids']);
						$where = "id in ({$idss})";
						$delete = $this->m_email_queue->Where($where)->Update(array('isdelete'=>1));
						if($delete){
							$data = array('code' => 1, 'msg' => '成功');
						}else{
							$data = array('code' => 1003, 'msg' => '删除失败');
						}
					}else{
						$data = array('code' => 1000, 'msg' => '请选中需要删除的订单');
					}
				}	
			} else {
                $data = array('code' => 1002, 'msg' => '页面超时，请刷新页面后重试!');
            }
        } else {
            $data = array('code' => 1001, 'msg' => '缺少字段', 'data' => '');
        }
       Helper::response($data);
    }
}