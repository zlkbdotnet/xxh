<?php

/*
 * 功能：会员中心－个人中心
 * Author:资料空白
 * Date:20180509
 */

class GetController extends PcBasicController
{
	private $m_products;
	private $m_products_type;
	private $m_products_pifa;
    public function init()
    {
        parent::init();
		$this->m_products = $this->load('products');
		$this->m_products_type = $this->load('products_type');
		$this->m_products_pifa = $this->load('products_pifa');
    }
	
    public function indexAction()
    {
		$tid = $this->get('tid');
		if($tid AND is_numeric($tid) AND $tid>0){
			//1.先查询是否为密码分类
			$products_type = $this->m_products_type->Where(array('id'=>$tid,'active'=>1,'isdelete'=>0))->SelectOne();
			if(!empty($products_type)){
				if(strlen($products_type['password'])>0){
					$password = $this->get('password');
					if(!$password){
						$data = array('code'=>1000,'count'=>0,'data'=>array(),'msg'=>'参数错误');
						Helper::response($result);
					}
					if($products_type['password']!=$password){
						$data = array('code'=>1000,'count'=>0,'data'=>array(),'msg'=>'密码错误');
						Helper::response($data);
					}
				}
					
				$where = array('active'=>1,'isdelete'=>0,'typeid'=>$tid);
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
					
					$sql = "SELECT p1.* FROM `t_products` as p1 left join t_products_type as p2 on p1.typeid =p2.id where p1.active=1 and p1.isdelete=0 and p1.typeid ={$tid} order by p2.sort_num DESC, p1.sort_num DESC LIMIT {$limits}";
					$items = $this->m_products->Query($sql);
					if (empty($items)) {
						$data = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
					} else {
						//对密码进行特别处理
						if(!empty($items)){
							foreach($items AS $k=>$p){
								if(isset($p['password']) AND strlen($p['password'])>0){
									$items[$k]['password'] = "hidden";
								}
							}
						}
						$data = array('code'=>0,'count'=>$total,'data'=>$items,'msg'=>'有数据');
					}
				} else {
					$data = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
				}
			}else{
				$data = array('code'=>1000,'count'=>0,'data'=>array(),'msg'=>'分类不存在');
			}
			Helper::response($data);
		}else{
			$where = array('active'=>1,'isdelete'=>0);
			$total=$this->m_products->Where($where)->Total();
			if ($total > 0) {
				$page = $this->get('page');
				$page = is_numeric($page) ? $page : 1;
				
				$limit = $this->get('limit');
				$limit = is_numeric($limit) ? $limit : 10;
				if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
					$pagenum = ($page - 1) * $limit;
					$limits = "{$pagenum},{$limit}";
					$sql = "SELECT p1.* FROM `t_products` as p1 left join t_products_type as p2 on p1.typeid =p2.id where p1.active=1 and p1.isdelete=0 order by p2.sort_num DESC, p1.sort_num DESC LIMIT {$limits}";
					$items = $this->m_products->Query($sql);
					if (empty($items)) {
						$data = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
					} else {
						//对密码进行特别处理
						if(!empty($items)){
							foreach($items AS $k=>$p){
								if(isset($p['password']) AND strlen($p['password'])>0){
									$items[$k]['password'] = "hidden";
								}
							}
						}
						$data = array('code'=>0,'count'=>$total,'data'=>$items,'msg'=>'有数据');
					}
				} else {
					$data = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
					Helper::response($data);
				}
			} else {
				$data = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
			}
			Helper::response($data);
		}
    }
	
    public function grouplistAction()
    {
		$where = array('active'=>1,'isdelete'=>0);
		$total=$this->m_products_type->Where($where)->Total();
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
			$order = array('sort_num' => 'DESC');
			$items = $this->m_products_type->Where(array('active'=>1,'isdelete'=>0))->Order($order)->Limit($limits)->Select();
			if(!empty($items)){
						//对密码进行特别处理
						if(!empty($items)){
							foreach($items AS $k=>$p){
								if(isset($p['password']) AND strlen($p['password'])>0){
									$items[$k]['password'] = "hidden";
								}
							}
						}
				$result = array('code'=>0,'count'=>$total,'data'=>$items,'msg'=>'有数据');
			}else{
				
				$result = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
			}
		}else{
			 $result = array('code'=>0,'count'=>0,'data'=>array(),'msg'=>'无数据');
		}
        Helper::response($result);
    }	
	
    public function proudctlistAction()
    {
		$tid = $this->getPost('tid');
		$csrf_token = $this->getPost('csrf_token', false);
		
		if($tid AND is_numeric($tid) AND $tid>0 AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				//1.先查询是否为密码分类
				$products_type = $this->m_products_type->Where(array('id'=>$tid,'active'=>1,'isdelete'=>0))->SelectOne();
				if(!empty($products_type)){
					if(strlen($products_type['password'])>0){
						$password = $this->getPost('password');
						if(!$password){
							$result = array('code' => 1000, 'msg' => '参数错误');
							Helper::response($result);
						}
						if($products_type['password']!=$password){
							$result = array('code' => 1002, 'msg' => '密码错误');
							Helper::response($result);
						}
					}
					
					$data = array();
					$order = array('sort_num' => 'DESC');
					$field = array('id', 'name','password');
					$products = $this->m_products->Field($field)->Where(array('typeid'=>$tid,'active'=>1,'isdelete'=>0))->Order($order)->Select();
					//对密码进行特别处理
					if(!empty($products)){
						foreach($products AS $k=>$p){
							if(isset($p['password']) AND strlen($p['password'])>0){
								$products[$k]['password'] = "hidden";
							}
						}
					}
					$data['products'] = $products;
					$result = array('code' => 1, 'msg' => 'success','data'=>$data);
				}else{
					$result = array('code' => 1002, 'msg' => '分类不存在');
				}
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
		if($pid AND is_numeric($pid) AND $pid>0 AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$data = array();
				$field = array('id', 'name', 'price','auto', 'qty', 'stockcontrol', 'description','addons','password');
				$product = $this->m_products->Field($field)->Where(array('id'=>$pid))->SelectOne();
				if(!empty($product)){
					if(strlen($product['password'])>0){
						$password = $this->getPost('password');
						if(!$password){
							$result = array('code' => 1000, 'msg' => '参数错误');
							Helper::response($result);
						}
						if($product['password']!=$password){
							$result = array('code' => 1002, 'msg' => '密码错误');
							Helper::response($result);
						}
					}
					//隐藏这个密码字段
					unset($product['password']);
					//先拿折扣
					$data['pifa'] = "";
					if($this->config['discountswitch']){
						$pifa = $this->m_products_pifa->getPifa($pid);
						if(!empty($pifa)){
							$data['pifa'] = $pifa;
						}
					}
					
					$data['product'] = $product;	
					if($product['addons']){
						$addons = explode(',',$product['addons']);
						$data['addons'] = $addons;
					}else{
						$data['addons'] = array();
					}
					$result = array('code' => 1, 'msg' => 'success','data'=>$data);
					
				}else{
					$result = array('code' => 1002, 'msg' => '商品不存在');
				}
			} else {
                $result = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$result = array('code' => 1000, 'msg' => '参数错误');
		}
        Helper::response($result);
	}
}