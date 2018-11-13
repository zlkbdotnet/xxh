<?php

/*
 * 功能：后台中心－产品分组
 * Author:资料空白
 * Date:20180509
 */

class ProductstypeController extends AdminBasicController
{
	private $m_products_type;
    private $m_products;
	
	public function init()
    {
        parent::init();
		$this->m_products_type = $this->load('products_type');
		$this->m_products = $this->load('products');
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
		
		$total=$this->m_products_type->Where(array('isdelete'=>0))->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$items=$this->m_products_type->Where(array('isdelete'=>0))->Where($where)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
			
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
			$item=$this->m_products_type->SelectByID('',$id);
			$data['item'] =$item;
			$this->getView()->assign($data);
		}else{
            $this->redirect('/'.ADMIN_DIR."/productstype");
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
		$method = $this->getPost('method');
		$id = $this->getPost('id');
		$name = $this->getPost('name');
		$description = $this->getPost('description',false);
		$password = $this->getPost('password',false);
		$sort_num = $this->getPost('sort_num',false);
		$active = $this->getPost('active',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $name AND is_numeric($sort_num) AND is_numeric($active) AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$m=array(
					'name'=>$name,
					'sort_num'=>$sort_num,
					'description'=>$description,
					'password'=>$password,
					'active'=>$active,
				);
				if($method == 'edit' AND $id>0){
					$u = $this->m_products_type->UpdateByID($m,$id);
					if($u){
						//更新缓存 
						//$this->m_products_type->getConfig(1);
						$data = array('code' => 1, 'msg' => '更新成功');
					}else{
						$data = array('code' => 1003, 'msg' => '更新失败');
					}
				}elseif($method == 'add'){
					$u = $this->m_products_type->Insert($m);
					if($u>0){
						//更新缓存 
						//$this->m_products_type->getConfig(1);
						$data = array('code' => 1, 'msg' => '新增成功');
					}else{
						$data = array('code' => 1003, 'msg' => '新增失败');
					}
				}else{
					$data = array('code' => 1002, 'msg' => '未知方法');
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
				//检查是否存在可用的商品
				$qty = $this->m_products->Where(array('typeid'=>$id,'active'=>1,'isdelete'=>0))->Total();
				if($qty>0){
					$data = array('code' => 1004, 'msg' => '当前分类下存在可用商品，请先处理', 'data' => '');
				}else{
					$where = 'active=0';//只有未激活的才可以删除
					$delete = $this->m_products_type->Where($where)->UpdateByID(array('isdelete'=>1),$id);
					if($delete){
						$data = array('code' => 1, 'msg' => '删除成功', 'data' => '');
					}else{
						$data = array('code' => 1003, 'msg' => '删除失败', 'data' => '');
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