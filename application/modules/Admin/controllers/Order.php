<?php

/*
 * 功能：后台中心－订单
 * Author:资料空白
 * Date:20180509
 */

class OrderController extends AdminBasicController
{
	private $m_order;
	private $m_products;
	private $m_email_queue;
    public function init()
    {
        parent::init();
		$this->m_order = $this->load('order');
		$this->m_products = $this->load('products');
		$this->m_email_queue = $this->load('email_queue');
    }

    public function indexAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$data = array();
		$products=$this->m_products->Where(array('isdelete'=>0))->Order(array('sort_num'=>'DESC'))->Select();
		$data['products'] = $products;
		$this->getView()->assign($data);
    }

	//ajax
	public function ajaxAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		$where1 = array('isdelete'=>0);
		
		$orderid = $this->get('orderid');
		$email = $this->get('email',false);
		$status = $this->get('status');
		$pid = $this->get('pid');
		
        //查询条件
        $get_params = [
            'orderid' => $orderid,
            'email' => $email,
			'status' => $status,
			'pid' => $pid,
        ];   
        $where = $this->conditionSQL($get_params);
		
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$total = $this->m_order->Where($where1)->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$field = array('id','orderid','email','productname','addtime','status','paymoney','number');
			$items = $this->m_order->Field($field)->Where($where1)->Where($where)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
			
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
	
	public function viewAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$id = $this->get('id');
		if($id AND $id>0){
			$data = array();
			$order = $this->m_order->SelectByID('',$id);
			if(is_array($order) AND !empty($order)){
				$data['order'] = $order;
				$this->getView()->assign($data);
			}else{
				$this->redirect('/'.ADMIN_DIR."/order");
				return FALSE;
			}
		}else{
            $this->redirect('/'.ADMIN_DIR."/order");
            return FALSE;
		}
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
					$where = '(status=0 or status=2)';//已完成和未支付的才可以删
					$delete = $this->m_order->Where($where1)->Where($where)->Update(array('isdelete'=>1));
					if($delete){
						$data = array('code' => 1, 'msg' => '删除成功', 'data' => '');
					}else{
						$data = array('code' => 1003, 'msg' => '删除失败', 'data' => '');
					}
				}else{
					$ids = json_decode($id,true);
					if(isset($ids['ids']) AND !empty($ids['ids'])){
						$idss = implode(",",$ids['ids']);
						$where = "(status=0 or status=2) and id in ({$idss})";
						$delete = $this->m_order->Where($where)->Update(array('isdelete'=>1));
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
	
	public function payAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$id = $this->get('id');
		if($id AND $id>0){
			$data = array();
			$order = $this->m_order->SelectByID('',$id);
			if(is_array($order) AND !empty($order)){
				if($order['status']>0){
					$this->redirect('/'.ADMIN_DIR."/order/view/?id=".$order['id']);
					return FALSE;
				}else{
					$data['order'] = $order;
					$this->getView()->assign($data);
				}
			}else{
				$this->redirect('/'.ADMIN_DIR."/order");
				return FALSE;
			}
		}else{
            $this->redirect('/'.ADMIN_DIR."/order");
            return FALSE;
		}
    }
	
    public function payajaxAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$id = $this->get('id');
		$csrf_token = $this->getPost('csrf_token', false);
		
        if (FALSE != $id AND is_numeric($id) AND $id > 0) {
			if ($this->VerifyCsrfToken($csrf_token)) {
				$order = $this->m_order->SelectByID('',$id);
				if(is_array($order) AND !empty($order)){
					if($order['status']>0){
						$data = array('code' => 1, 'msg' => '订单已支付', 'data' => '');
					}else{
						//业务处理
						$config = array('paymethod'=>'admin','tradeid'=>0,'paymoney'=>$order['money'],'orderid'=>$order['orderid'] );
						$notify = new \Pay\notify();
						$data = $notify->run($config);
					}
				}else{
					$data = array('code' => 1002, 'msg' => '订单不存在', 'data' => '');
				}
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
        } else {
            $data = array('code' => 1000, 'msg' => '缺少字段', 'data' => '');
        }
       Helper::response($data);
    }
	
	public function sendAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$id = $this->get('id');
		if($id AND $id>0){
			$data = array();
			$order = $this->m_order->SelectByID('',$id);
			if(is_array($order) AND !empty($order)){
				if($order['status']=='1' OR $order['status']=='3'){
					$data['order'] = $order;
					$this->getView()->assign($data);
				}else{
					$this->redirect('/'.ADMIN_DIR."/order/view/?id=".$order['id']);
					return FALSE;
				}
			}else{
				$this->redirect('/'.ADMIN_DIR."/order");
				return FALSE;
			}
		}else{
            $this->redirect('/'.ADMIN_DIR."/order");
            return FALSE;
		}
    }
	
    public function sendajaxAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$id = $this->getPost('id');
		$kami = $this->getPost('kami');
		$csrf_token = $this->getPost('csrf_token', false);
		
        if (FALSE != $id AND is_numeric($id) AND $id > 0) {
			if ($this->VerifyCsrfToken($csrf_token)) {
				$order = $this->m_order->SelectByID('',$id);
				if(is_array($order) AND !empty($order)){
					if($order['status']=='1' OR $order['status']=='3'){
						//业务处理
						$kami = str_replace(array("\r","\n","\t"), "", $kami);
						$update = $this->m_order->Where(array('id'=>$id))->Where('status=1 or status=3')->Update(array('status'=>2,'kami'=>$kami));
						if($update){
							$m = array();
							//3.1.4.1通知用户,定时任务去执行
							if(isEmail($order['email'])){
								$content = '用户:' . $order['email'] . ',购买的商品['.$order['productname'].'],卡密是:'.$kami;
								$m[]=array('email'=>$order['email'],'subject'=>'商品购买成功','content'=>$content,'addtime'=>time(),'status'=>0);
							}
							//3.1.4.2通知管理员,定时任务去执行
							if(isEmail($this->config['admin_email'])){
								$content = '用户:' . $order['email'] . ',购买的商品['.$order['productname'].'],卡密发送成功';
								$m[]=array('email'=>$this->config['admin_email'],'subject'=>'用户购买商品','content'=>$content,'addtime'=>time(),'status'=>0);
							}
							if(!empty($m)){
								$this->m_email_queue->MultiInsert($m);
							}
							$data = array('code' => 1, 'msg' => '订单已处理', 'data' => '');
						}else{
							$data = array('code' => 1004, 'msg' => '处理失败', 'data' => '');
						}
					}else{
						$data = array('code' => 1, 'msg' => '订单状态不需要处理', 'data' => '');
					}
				}else{
					$data = array('code' => 1002, 'msg' => '订单不存在', 'data' => '');
				}
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
        } else {
            $data = array('code' => 1000, 'msg' => '缺少字段', 'data' => '');
        }
       Helper::response($data);
    }
	
    private function conditionSQL($param)
    {
        $condition = "1";
        if (isset($param['orderid']) AND empty($param['orderid']) === FALSE) {
            $condition .= " AND `orderid` LIKE '%{$param['orderid']}%'";
        }
        if (isset($param['email']) AND empty($param['email']) === FALSE) {
            $condition .= " AND `email` LIKE '%{$param['email']}%'";
        }
        if (isset($param['status']) AND $param['status']>-1 ) {
            $condition .= " AND `status` = {$param['status']}";
        }
        if (isset($param['pid']) AND empty($param['pid']) === FALSE AND $param['pid']>0 ) {
            $condition .= " AND `pid` = {$param['pid']}";
        }		
        return ltrim($condition, " AND ");
    }
}