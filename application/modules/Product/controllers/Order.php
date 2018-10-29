<?php

/*
 * 功能：会员中心－个人中心
 * Author:资料空白
 * Date:20180509
 */

class OrderController extends PcBasicController
{
	private $m_products;
	private $m_order;
	private $m_user;
	private $m_payment;
	
    public function init()
    {
        parent::init();
		$this->m_products = $this->load('products');
		$this->m_order = $this->load('order');
		$this->m_user = $this->load('user');
		$this->m_payment = $this->load('payment');
    }

    public function buyAction()
    {
		//下订单
		$pid = ceil($this->getPost('pid'));
		$number = ceil($this->getPost('number'));
		$chapwd = $this->getPost('chapwd');
		$addons = $this->getPost('addons');
		$csrf_token = $this->getPost('csrf_token', false);
		
		if(is_numeric($pid) AND $pid>0 AND is_numeric($number) AND $number>0  AND $chapwd AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if(isset($this->config['orderinputtype']) AND $this->config['orderinputtype']=='2'){
					if($this->login AND $this->userid){
						$email = $this->uinfo['email'];
						$qq = '';
					}else{
						$qq = $this->getPost('qq');
						if($qq AND is_numeric($qq)){
							$email = $qq.'@qq.com';
						}else{
							$data = array('code' => 1006, 'msg' => '丢失参数');
							Helper::response($data);
						}
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
				
				
				$product = $this->m_products->Where(array('id'=>$pid,'active'=>1,'isdelete'=>0))->SelectOne();
				if(!empty($product)){
					$myip = getClientIP();
					
					//库存控制
					if($product['stockcontrol']==1 AND $product['qty']<1){
						$data = array('code' => 1003, 'msg' => '库存不足');
						Helper::response($data);
					}
					if($product['stockcontrol']==1 AND $number>$product['qty']){
						$data = array('code' => 1004, 'msg' => '库存不足');
						Helper::response($data);
					}
					
					$starttime = strtotime(date("Y-m-d"));
					$endtime = strtotime(date("Y-m-d 23:59:59"));
					//进行同一ip，下单未付款的处理判断
					if(isset($this->config['limitiporder']) AND $this->config['limitiporder']>0){
						$total = $this->m_order->Where(array('ip'=>$myip,'status'=>0,'isdelete'=>0))->Where("addtime>={$starttime} and addtime<={$endtime}")->Total();
						if($total>$this->config['limitiporder']){
							$data = array('code' => 1005, 'msg' => '处理失败,您有太多未付款订单了');
							Helper::response($data);
						}
					}

					//进行同一email，下单未付款的处理判断
					if(isset($this->config['limitemailorder']) AND $this->config['limitemailorder']>0){
						$total = $this->m_order->Where(array('email'=>$email,'status'=>0,'isdelete'=>0))->Where("addtime>={$starttime} and addtime<={$endtime}")->Total();
						if($total>$this->config['limitemailorder']){
							$data = array('code' => 1006, 'msg' => '处理失败,您有太多未付款订单了');
							Helper::response($data);
						}
					}
					
					//对附加输入进行判断
					if($product['addons']){
						$p_addons = explode(',',$product['addons']);
						if(count($p_addons)>count($addons)){
							$data = array('code' => 1006, 'msg' => '自定义内容不能为空!');
							Helper::response($data);
						}
						$o_addons = '';
						foreach($addons AS $k=>$addon){
							$o_addons .= $p_addons[$k].":".$addon.";";
						}
					}else{
						$o_addons = '';
					}
					
					
					//记录用户uid
					if($this->login AND $this->userid){
						$userid = $this->userid;
					}else{
						$uinfo = $this->m_user->Where(array('email'=>$email))->SelectOne();
						if(!empty($uinfo)){
							$userid = $uinfo['id'];
						}else{
							$userid = 0;
						}
					}
					
					//生成orderid
					$prefix = isset($this->config['orderprefix'])?$this->config['orderprefix']:'zlkb';
					$orderid = $prefix. date('Y') . date('m') . date('d') . date('H') . date('i') . date('s') . mt_rand(10000, 99999);
					
					//开始下单，入库
					$m=array(
						'orderid'=>$orderid,
						'userid'=>$userid,
						'email'=>$email,
						'qq'=>$qq,
						'pid'=>$pid,
						'productname'=>$product['name'],
						'price'=>$product['price'],
						'number'=>$number,
						'money'=>$product['price']*$number,
						'chapwd'=>$chapwd,
						'ip'=>$myip,
						'status'=>0,
						'addons'=>$o_addons,
						'addtime'=>time(),
					);
					$id=$this->m_order->Insert($m);
					if($id>0){
						$oid = base64_encode($id);
						$data = array('code' => 1, 'msg' => '下单成功','data'=>array('oid'=>$oid));	
					}else{
						$data = array('code' => 1003, 'msg' => '订单异常');
					}
				}else{
					$data = array('code' => 1002, 'msg' => '商品不存在');
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
		$data = array();
		$oid = $this->get('oid',false);
		$ooid = $this->get('ooid',false);
		$id = 0;
		if($oid OR $ooid){
			if($oid){
				$oid = (int)base64_decode($oid);
				if(is_numeric($oid) AND $oid>0){
					$id = $oid;
				}
			}else{
				if(is_numeric($ooid) AND $ooid>0){
					$id = $ooid;
				}
			}
			
			if($id>0){
				$order = $this->m_order->Where(array('id'=>$id,'isdelete'=>0))->SelectOne();
				if(!empty($order)){
					//获取支付方式
					$payments = $this->m_payment->getConfig();
					$data['order']=$order;
					$data['payments']=$payments;
					$data['code']=1;
				}else{
					$data['code']=1002;
					$data['msg']='订单不存在';
				}
			}else{
				$data['code']=1001;
				$data['msg']='订单不存在';
			}
		}else{
			$data['code']=1001;
			$data['msg']='订单不存在';
		}
		$data['title'] = "订单支付";
		$this->getView()->assign($data);
	}
	
	public function payajaxAction()
	{
		$paymethod = $this->getPost('paymethod');
		$oid = $this->getPost('oid');
		$csrf_token = $this->getPost('csrf_token');
		if($paymethod AND $oid AND $csrf_token){
			$payments = $this->m_payment->getConfig();
			if(isset($payments[$paymethod]) AND !empty($payments[$paymethod])){
				$payconfig = $payments[$paymethod];
				if($payconfig['active']>0){
					//获取订单信息
					$order = $this->m_order->Where(array('id'=>$oid,'isdelete'=>0))->SelectOne();
					if(is_array($order) AND !empty($order)){
						if($order['status']>0){
							$data = array('code' => 1004, 'msg' => '订单已支付成功');
						}else{
							try{
								//这里对有订单超时处理的支付渠道进行特别处理
								/*if($payconfig['overtime']>0){
									if(($order['addtime']+$payconfig['overtime'])<time()){
										//需要重新生成订单再提交
										//生成orderid
										$prefix = isset($this->config['orderprefix'])?$this->config['orderprefix']:'zlkb';
										$new_orderid = $prefix. date('Y') . date('m') . date('d') . date('H') . date('i') . date('s') . mt_rand(10000, 99999);
										$u = $this->m_order->UpdateByID(array('orderid'=>$new_orderid),$oid);
										if($u){
											$orderid = $new_orderid;
										}else{
											$data = array('code' => 1006, 'msg' =>"订单超时关闭");
											Helper::response($data);
										}
									}else{
										$orderid = $order['orderid'];
									}
								}else{
									$orderid = $order['orderid'];
								}*/
								$orderid = $order['orderid'];
								$payclass = "\\Pay\\".$paymethod."\\".$paymethod;
								$PAY = new $payclass();
								$params =array('orderid'=>$orderid,'money'=>$order['money'],'productname'=>$order['productname'],'weburl'=>$this->config['weburl']);
								$data = $PAY->pay($payconfig,$params);
							} catch (\Exception $e) {
								$data = array('code' => 1005, 'msg' => $e->getMessage());
							}
						}
					}else{
						$data = array('code' => 1003, 'msg' => '订单不存在');
					}
				}else{
					$data = array('code' => 1002, 'msg' => '支付渠道已关闭');
				}
			}else{
				$data = array('code' => 1001, 'msg' => '支付渠道异常');
			}
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
	//支付宝当面付生成二维码
	public function showqrAction()
	{
        $url = $this->get('url',true);
		if($url){
			//增加安全判断
			if(isset($_SERVER['HTTP_REFERER'])){
				$referer_url = parse_url($_SERVER['HTTP_REFERER']);
				$web_url = parse_url($this->config['weburl']);
				if($referer_url['host']!=$web_url['host']){
					echo 'fuck you!';exit();
				}
			}
			try{
				\PHPQRCode\QRcode::png($url);
				exit();
			} catch (\Exception $e) {
				echo $e->getMessage();
				exit();
			}
		}else{
			echo '参数丢失';
			exit();
		}
	}
}