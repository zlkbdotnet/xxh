<?php

/*
 * 功能：后台中心－商品管理
 * Author:资料空白
 * Date:20180509
 */

class ProductspifaController extends AdminBasicController
{
	private $m_products;
	private $m_products_pifa;
	
    public function init()
    {
        parent::init();
		$this->m_products = $this->load('products');
		$this->m_products_pifa = $this->load('products_pifa');
    }

    public function indexAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$id = $this->get('id');
		if($id AND $id>0){
			$data = array();
			$product=$this->m_products->SelectByID('',$id);
			$data['product'] = $product;
			$this->getView()->assign($data);
		}else{
            $this->redirect('/'.ADMIN_DIR."/products");
            return FALSE;
		}
    }

	//ajax
	public function ajaxAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$pid = $this->get('pid');
		
		if($pid AND is_numeric($pid) AND $pid>0){
			$where = array('pid'=>$pid,'isdelete'=>0);
			$total=$this->m_products_pifa->Where($where)->Total();
			
			if ($total > 0) {
				if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
					$pagenum = ($page - 1) * $limit;
				} else {
					$pagenum = 0;
				}
				
				$limits = "{$pagenum},{$limit}";
				
				$items=$this->m_products_pifa->Where($where)->Limit($limits)->Order(array('qty'=>'ASC'))->Select();
				if (empty($items)) {
					$data = array('code'=>1002,'count'=>0,'data'=>array(),'msg'=>'无数据');
				} else {
					$data = array('code'=>0,'count'=>$total,'data'=>$items,'msg'=>'有数据');
				}
			} else {
				$data = array('code'=>1001,'count'=>0,'data'=>array(),'msg'=>'无数据');
			}
		}else{
			$data = array('code'=>1001,'count'=>0,'data'=>array(),'msg'=>'参数错误');
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
		if($id AND is_numeric($id) AND $id>0){
			$data = array();
			$pifa = $this->m_products_pifa->SelectByID('',$id);
			if(!empty($pifa)){
				$product = $this->m_products->SelectByID('',$pifa['pid']);
				if(!empty($product)){
					$data['pifa'] = $pifa;
					$data['product'] = $product;
					$this->getView()->assign($data);
				}else{
					$this->redirect('/'.ADMIN_DIR."/products");
					return FALSE;
				}
			}else{
				$this->redirect('/'.ADMIN_DIR."/products");
				return FALSE;
			}
		}else{
            $this->redirect('/'.ADMIN_DIR."/products");
            return FALSE;
		}
    }
	
	
    public function addAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$pid = $this->get('pid');
		if($pid AND is_numeric($pid) AND $pid>0){
			$data = array();
			$product = $this->m_products->SelectByID('',$pid);
			if(!empty($product)){
				$data['product'] = $product;
				$data['pid'] = $pid;
				$this->getView()->assign($data);
			}else{
				$this->redirect('/'.ADMIN_DIR."/products");
				return FALSE;
			}
		}else{
            $this->redirect('/'.ADMIN_DIR."/products");
            return FALSE;
		}
    }
	
	public function editajaxAction()
	{
		$method = $this->getPost('method',false);
		$qty = $this->getPost('qty');
		$discount = $this->getPost('discount');
		$id = $this->getPost('id');
		$pid = $this->getPost('pid');
		$tag = $this->getPost('tag');
		$csrf_token = $this->getPost('csrf_token');
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $qty AND $discount AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if($qty<0){
					$data = array('code' => 1001, 'msg' => '数量有误');
					Helper::response($data);
				}
				
				if($discount<=0.00 OR $discount>=1.00){
					$data = array('code' => 1002, 'msg' => '折扣有误');
					Helper::response($data);	
				}
				
				$tag = getRawText($tag);
				
				$m = array(
					'qty'=>$qty,
					'discount'=>$discount,
					'tag'=>$tag,
				);
				if($method == 'edit' AND $id>0){
					$u = $this->m_products_pifa->UpdateByID($m,$id);
					if($u){
						$data = array('code' => 1, 'msg' => '更新成功');
					}else{
						$data = array('code' => 1003, 'msg' => '更新失败');
					}
				}elseif($method == 'add'){
					if($pid<0){
						$data = array('code' => 1001, 'msg' => '商品ID错误');
						Helper::response($data);
					}
					$m['addtime'] = time();
					$m['pid'] = $pid;
					$u = $this->m_products_pifa->Insert($m);
					if($u){
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
				$delete = $this->m_products_pifa->UpdateByID(array('isdelete'=>1),$id);
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