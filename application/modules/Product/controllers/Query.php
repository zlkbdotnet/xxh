<?php
/*
 * 功能：会员中心－个人中心
 * Author:资料空白
 * Date:20180509
 */

class QueryController extends PcBasicController
{
	private $m_order;
	private $method_array = array();
    public function init()
    {
        parent::init();
		$this->m_order = $this->load('order');
		$this->method_array = array('orderid','cookie','contact','auto');
    }

    public function indexAction()
    {
		$data = array();
		$zlkbmethod = $this->get("zlkbmethod");
		if(!in_array($zlkbmethod,$this->method_array)){
			$zlkbmethod = "contact";
		}
		
		if($zlkbmethod == "auto"){
			$data['order'] = $data['cnstatus'] = array();
			//如果有订单号过来，就是直接去自动查询页面
			$orderid  = $this->get('orderid');
			if($orderid){
				if (false != $this->login AND $this->userid) {
					$order_email = $this->uinfo['email'];
				}else{
					$order_email = $this->getSession('order_email');
				}
					
				if($order_email){
					$order = $this->m_order->Where(array('orderid'=>$orderid,'email'=>$order_email))->Where(array('isdelete'=>0))->SelectOne();
					if(!empty($order)){
						$data['order'] = $order;
						$data['cnstatus'] = array(0=>'<span class="layui-badge layui-bg-gray">待付款</span>',1=>'<span class="layui-badge layui-bg-blue">待处理</span>',2=>'<span class="layui-badge layui-bg-green">已完成</span>',3=>'<span class="layui-badge layui-bg-black">处理失败</span>');
					}
				}
			}
		}

		$data['title'] = "订单查询";
		if(file_exists(APP_PATH.'/application/modules/Product/views/query/tpl/'.$zlkbmethod.'.html')){
			$tpl = 'tpl_'.$zlkbmethod;
			$this->display($tpl, $data);
			return FALSE;
		}else{
			$this->getView()->assign($data);
		}
    }
	
	public function ajaxAction()
	{
		$zlkbmethod = $this->getPost('method');
		$csrf_token = $this->getPost('csrf_token', false);
		if($zlkbmethod AND $csrf_token){
			if(in_array($zlkbmethod,$this->method_array)){
				if($zlkbmethod == 'contact'){
					$chapwd    = $this->getPost('chapwd');
					if($chapwd){
						if ($this->VerifyCsrfToken($csrf_token)) {
							if(isset($this->config['orderinputtype']) AND $this->config['orderinputtype']=='2'){
								$qq = $this->getPost('qq');
								if($qq AND is_numeric($qq)){
									$email = $qq.'@qq.com';
								}else{
									$data = array('code' => 1006, 'msg' => '丢失参数');
									Helper::response($data);
								}
							}else{
								$email = $this->getPost('email',false);
								if($email AND isEmail($email)){
									$qq = '';
								}else{
									$data = array('code' => 1006, 'msg' => '丢失参数');
									Helper::response($data);
								}
							}
							
							if(isset($this->config['yzmswitch']) AND $this->config['yzmswitch']>0){
								$vercode = $this->getPost('vercode');
								if($vercode){
									if(strtolower($this->getSession('productqueryCaptcha')) == strtolower($vercode)){
										$this->unsetSession('productqueryCaptcha');
									}else{
										$data=array('code'=>1004,'msg'=>'图形验证码错误');
										Helper::response($data);
									}
								}else{
									$data = array('code' => 1000, 'msg' => '丢失参数');
									Helper::response($data);
								}
							}
							$starttime = strtotime("-1 month");
							$order = $this->m_order->Where(array('email'=>$email,'chapwd'=>$chapwd))->Where(array('isdelete'=>0))->Where("addtime>={$starttime}")->Order(array('id'=>'desc'))->Select();
							if(empty($order)){
								$data=array('code'=>1005,'msg'=>'订单不存在');
							}else{
								$data=array('code'=>1,'msg'=>'查询成功','data'=>$order,'count'=>count($order));
							}
						} else {
							$data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
						}
					}else{
						$data = array('code' => 1000, 'msg' => '丢失参数');
					}
				//订单号查询	
				}elseif($zlkbmethod == 'orderid'){
					$orderid    = $this->getPost('orderid');
					if($orderid){
						if ($this->VerifyCsrfToken($csrf_token)) {
							if(isset($this->config['yzmswitch']) AND $this->config['yzmswitch']>0){
								$vercode = $this->getPost('vercode');
								if($vercode){
									if(strtolower($this->getSession('productqueryCaptcha')) == strtolower($vercode)){
										$this->unsetSession('productqueryCaptcha');
									}else{
										$data=array('code'=>1004,'msg'=>'图形验证码错误');
										Helper::response($data);
									}
								}else{
									$data = array('code' => 1000, 'msg' => '丢失参数');
									Helper::response($data);
								}
							}
							$starttime = strtotime("-1 month");
							$order = $this->m_order->Where(array('orderid'=>$orderid))->Where(array('isdelete'=>0))->Where("addtime>={$starttime}")->Order(array('id'=>'desc'))->Select();
							if(empty($order)){
								$data=array('code'=>1005,'msg'=>'订单不存在');
							}else{
								$data=array('code'=>1,'msg'=>'查询成功','data'=>$order,'count'=>count($order));
							}
						} else {
							$data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
						}
					}else{
						$data = array('code' => 1000, 'msg' => '丢失参数');
					}
				}elseif($zlkbmethod == 'cookie'){
					//从浏览器中cookie中读取
					$orderid = $this->getCookie('oid');
					if($orderid){
						if ($this->VerifyCsrfToken($csrf_token)) {
							$l_encryption = new Encryption();
							$cookie_oid = $l_encryption->decrypt($orderid);
							$starttime = strtotime("-1 month");
							$order = $this->m_order->Where(array('orderid'=>$cookie_oid))->Where(array('isdelete'=>0))->Where("addtime>={$starttime}")->Order(array('id'=>'desc'))->Select();
							if(empty($order)){
								$data=array('code'=>1005,'msg'=>'订单不存在');
							}else{
								$data=array('code'=>1,'msg'=>'查询成功','data'=>$order,'count'=>count($order));
							}
						} else {
							$data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
						}
					}else{
						$data = array('code' => 1000, 'msg' => '没有订单记录');
					}
				}else{
					$data = array('code' => 1001, 'msg' => '未知的查询方式');
				}
			}else{
				$data = array('code' => 1001, 'msg' => '参数错误');
			}
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
	public function kamiAction()
	{
		$orderid    = $this->getPost('orderid');
		$csrf_token = $this->getPost('csrf_token', false);
		if($orderid AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$order = $this->m_order->Where(array('orderid'=>$orderid,'status'=>2))->SelectOne();
				if(empty($order)){
					$data=array('code'=>1005,'msg'=>'没有订单');
				}else{
					$card_mi_str = $order['kami'];
					$cards = explode(',',$card_mi_str);
					$data=array('code'=>1,'msg'=>'查询成功','data'=>$cards);
				}
			} else {
				$data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
	public function payAction()
	{
		$oid    = $this->getPost('oid');
		$csrf_token = $this->getPost('csrf_token', false);
		if($oid AND is_numeric($oid) AND $oid>0 AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$order = $this->m_order->Where(array('id'=>$oid,'isdelete'=>0))->SelectOne();
				if(empty($order)){
					$data=array('code'=>1002,'msg'=>'没有订单');
				}else{
					if($order['status']<1){
						$data = array('code' => 1003, 'msg' => '未支付');
					}else{
						$this->setSession('order_email',$order['email']);
						$this->clearCookie('oid');
						$l_encryption = new Encryption();
						$cookie_oid = $l_encryption->encrypt($order['orderid']);
						$this->setCookie('oid',$cookie_oid);
						$data = array('code' => 1, 'msg' => 'success','data'=>$order);
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
}