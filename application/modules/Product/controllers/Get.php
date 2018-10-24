<?php

/*
 * 功能：会员中心－个人中心
 * Author:资料空白
 * Date:20180509
 */

class GetController extends PcBasicController
{
	private $m_products;
    public function init()
    {
        parent::init();
		$this->m_products = $this->load('products');
    }
	
    public function indexAction()
    {
		$where = array('active'=>1,'isdelete'=>0);
		$total=$this->m_products->Where($where)->Total();
        if ($total > 0) {
			$page = $this->get('page');
			$page = is_numeric($page) ? $page : 1;
			
			$limit = $this->get('limit');
			$limit = is_numeric($limit) ? $limit : 10;
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			
			$sql = "SELECT p1.* FROM `t_products` as p1 left join t_products_type as p2 on p1.typeid =p2.id where p1.active=1 and p1.isdelete=0 order by p2.sort_num DESC, p1.sort_num DESC LIMIT {$limits}";
			$items = $this->m_products->Query($sql);
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
	
    public function proudctlistAction()
    {
		$tid = $this->getPost('tid');
		$csrf_token = $this->getPost('csrf_token', false);
		
		if($tid AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$data = array();
				$order = array('sort_num' => 'DESC');
				$field = array('id', 'name');
				$products = $this->m_products->Field($field)->Where(array('typeid'=>$tid,'active'=>1,'isdelete'=>0))->Order($order)->Select();
				$data['products'] = $products;
				$result = array('code' => 1, 'msg' => 'success','data'=>$data);
			} else {
                $result = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$result = array('code' => 1000, 'msg' => '参数错误');
		}
        Helper::response($result);
    }
	
	
	public function proudctinfoAction()
	{
		$pid = $this->getPost('pid');
		$csrf_token = $this->getPost('csrf_token', false);
		if($pid AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$data = array();
				$field = array('id', 'name', 'price','auto', 'qty', 'stockcontrol', 'description','addons');
				$product = $this->m_products->Field($field)->Where(array('id'=>$pid))->SelectOne();
				$data['product'] = $product;
				
				if($product['addons']){
					$addons = explode(',',$product['addons']);
					$data['addons'] = $addons;
				}else{
					$data['addons'] = array();
				}
				$result = array('code' => 1, 'msg' => 'success','data'=>$data);
			} else {
                $result = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$result = array('code' => 1000, 'msg' => '参数错误');
		}
        Helper::response($result);
	}
}