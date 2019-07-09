<?php

/*
 * 功能：后台中心－广告设置
 * Author:资料空白
 * Date:20190709
 */

class AdController extends AdminBasicController
{
    private $m_ad;
	
	public function init()
    {
        parent::init();
		$this->m_ad = $this->load('ad');
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
		
		$where = array();
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$total=$this->m_ad->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$field = array('id','name','label','isactive','addtime');
			$items = $this->m_ad->Field($field)->Where($where)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
			
            if (empty($items)) {
                $data = array('code'=>1002,'count'=>0,'data'=>array(),'msg'=>'无数据');
            } else {
                $data = array('code'=>0,'count'=>$total,'data'=>$items,'msg'=>'有数据');
            }
        } else {
            $data = array('code'=>1001,'count'=>0,'data'=>array(),'msg'=>'无数据');
        }
		Helper::response($data);
	}
	
    public function editAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$id = $this->get('id');
		if($id AND $id>0){
			$data = array();
			$items = $this->m_ad->SelectByID('',$id);
			$data['items'] = $items;
			$this->getView()->assign($data);
		}else{
            $this->redirect('/'.ADMIN_DIR."/ad");
            return FALSE;
		}
    }
    public function addAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }

		$data = array();
		$this->getView()->assign($data);
    }	
	
	public function editajaxAction()
	{
		$method = $this->getPost('method',false);
		$id = $this->getPost('id',false);
		$name = $this->getPost('name',false);
		$label = $this->getPost('label',false);
		$isactive = $this->getPost('isactive',false);
		$content = $this->getPost('content');
		$csrf_token = $this->getPost('csrf_token', false);
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $name AND $label AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$m = array(
					'name'=>$name,
					'label'=>$label,
					'isactive'=>$isactive
				);
				
				if($protocol == "add"){
					if(!$content OR empty($content)){
						$data = array('code' => 1000, 'msg' => '丢失参数');
						Helper::response($data);
					}
					$m['content'] = htmlspecialchars($content);
					$m['addtime'] = time();
					$id = $this->m_ad->Insert($m);
					if($id>0){
						$data = array('code' => 1, 'msg' => '新增成功');
					}else{
						$data = array('code' => 1003, 'msg' => '新增失败');
					}
				}else{
					if(is_numeric($id) AND $id>0){
						$m['content'] = htmlspecialchars($content);
						$u = $this->m_ad->UpdateByID($m,$id);
						if($id>0){
							$data = array('code' => 1, 'msg' => '更新成功');
						}else{
							$data = array('code' => 1003, 'msg' => '更新失败');
						}
					}else{
						$data = array('code' => 1000, 'msg' => '丢失参数');
					}
				}
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}

    public function deleteAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$id = $this->get('id');
		$csrf_token = $this->getPost('csrf_token', false);
        if (FALSE != $id AND is_numeric($id) AND $id > 0) {
			if ($this->VerifyCsrfToken($csrf_token)) {
				$delete = $this->m_ad->Where(array("id"=>$id))->DeleteOne();
				if($delete){
					$data = array('code' => 1, 'msg' => '删除成功', 'data' => '');
				}else{
					$data = array('code' => 1003, 'msg' => '删除失败', 'data' => '');
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