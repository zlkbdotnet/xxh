<?php
/**
 * File: notify.php
 * Functionality: 支付返回处理
 * Author: 资料空白
 * Date: 2018-6-8
 */
namespace Pay;

class notify
{
	//处理返回
	public function run(array $params)
	{
		//支付渠道
		$paymethod = $params['paymethod'];
		//订单号
		$tradeid = $params['tradeid'];
		//支付金额
		$paymoney = $params['paymoney'];
		//本站订单号
		$orderid = $params['orderid'];
		
		$m_order =  \Helper::load('order');
		$m_products_card = \Helper::load('products_card');
		$m_email_queue = \Helper::load('email_queue');
		$m_products = \Helper::load('products');
		$m_config = \Helper::load('config');
		$web_config = $m_config->getConfig();
		
		try{
			//1. 通过orderid,查询order订单
			$order = $m_order->Where(array('orderid'=>$orderid))->SelectOne();
			if(!empty($order)){
				if($order['status']>0){
					$data =array('code'=>1,'msg'=>'订单已处理,请勿重复推送');
					return $data;
				}else{
					if($paymoney < $order['money']){
						//原本检测支付金额是否与订单金额一致,但由于码支付这样的收款模式导致支付金额有时会与订单不一样,所以这里进行小于判断;
						//所以,在这里如果存在类似码支付这样的第三方支付辅助工具时,有变动金额时,一定要做递增不能递减
						$data =array('code'=>1005,'msg'=>'支付金额小于订单金额');
						return $data;
					}
					
					//2.先更新支付总金额
					$update = array('status'=>1,'paytime'=>time(),'tradeid'=>$tradeid,'paymethod'=>$paymethod,'paymoney'=>$paymoney);
					$u = $m_order->Where(array('orderid'=>$orderid,'status'=>0))->Update($update);
					if(!$u){
						$data =array('code'=>1004,'msg'=>'更新失败');
						return $data;
					}else{ 
						//3.开始进行订单处理
						$product = $m_products->SelectByID('auto,stockcontrol,qty',$order['pid']);
						if(!empty($product)){
							if($product['auto']>0){
								//3.自动处理
								//查询通过订单中记录的pid，根据购买数量查询密码,修复
								if($product['stockcontrol']>0){
									$Limit = $order['number'];
								}else{
									$Limit = 1;
								}
								$cards = $m_products_card->Where(array('pid'=>$order['pid'],'active'=>0,'isdelete'=>0))->Limit($Limit)->Select();
								if(is_array($cards) AND !empty($cards) AND count($cards)==$Limit){
									//3.1 库存充足,获取对应的卡id,密码
									$card_mi_array = array_column($cards, 'card');
									$card_mi_str = implode(',',$card_mi_array);
									$card_id_array = array_column($cards, 'id');
									$card_id_str = implode(',',$card_id_array);
									//3.1.2 进行密码处理,如果进行了库存控制，就开始处理
									if($product['stockcontrol']>0){
										//3.1.2.1 直接进行密码与订单的关联
										$m_products_card->Where("id in ({$card_id_str})")->Where(array('active'=>0))->Update(array('active'=>1));
										//3.1.2.2 然后进行库存清减
										$qty_m = array('qty' => 'qty-'.$order['number']);
										$m_products->Where(array('id'=>$order['pid'],'stockcontrol'=>1))->Update($qty_m,TRUE);
										$kucunNotic=";当前商品库存剩余:".($product['qty']-$order['number']);
									}else{
										//3.1.2.3不进行库存控制时,自动发货商品是不需要减库存，也不需要取消密码；因为这种情况下的密码是通用的；
										$kucunNotic="";
									}
									//3.1.3 更新订单状态,同时把密码写到订单中
									$m_order->Where(array('orderid'=>$orderid,'status'=>1))->Update(array('status'=>2,'kami'=>$card_mi_str));
									//3.1.4 把邮件通知写到消息队列中，然后用定时任务去执行即可
									$m = array();
									//3.1.4.1通知用户,定时任务去执行
									if(isset($web_config['emailswitch']) AND $web_config['emailswitch']>0){
										if(isEmail($order['email'])){
											$content = '用户:' . $order['email'] . ',购买的商品['.$order['productname'].'],密码是:'.$card_mi_str;
											$m[]=array('email'=>$order['email'],'subject'=>'商品购买成功','content'=>$content,'addtime'=>time(),'status'=>0);
										}	
									}
									//3.1.4.2通知管理员,定时任务去执行
									if(isEmail($web_config['adminemail'])){
										$content = '用户:' . $order['email'] . ',购买的商品['.$order['productname'].'],密码发送成功'.$kucunNotic;
										$m[]=array('email'=>$web_config['adminemail'],'subject'=>'用户购买商品','content'=>$content,'addtime'=>time(),'status'=>0);
									}
									
									if(!empty($m)){
										$m_email_queue->MultiInsert($m);
										if($web_config['emailsendtypeswitch']>0){
											$send_email = new \Sendemail();
											$send_email->send($m);
										}
									}
									$data =array('code'=>1,'msg'=>'自动发卡');
								}else{
									//3.2 这里说明库存不足了，干脆就什么都不处理，直接记录异常，同时更新订单状态
									$m_order->Where(array('orderid'=>$orderid,'status'=>1))->Update(array('status'=>3));
									file_put_contents(YEWU_FILE, CUR_DATETIME.'-'.'库存不足，无法处理'.PHP_EOL, FILE_APPEND);
									//3.2.3邮件通知写到消息队列中，然后用定时任务去执行即可
									$m = array();
									//3.2.3.1通知用户,定时任务去执行
									if(isset($web_config['emailswitch']) AND $web_config['emailswitch']>0){
										if(isEmail($order['email'])){
											$content = '用户:' . $order['email'] . ',购买的商品['.$order['productname'].'],由于库存不足暂时无法处理,管理员正在拼命处理中....请耐心等待!';
											$m[] = array('email'=>$order['email'],'subject'=>'商品购买成功','content'=>$content,'addtime'=>time(),'status'=>0);
										}
									}
									//3.2.3.2通知管理员,定时任务去执行
									if(isEmail($web_config['adminemail'])){
										$content = '用户:' . $order['email'] . ',购买的商品['.$order['productname'].'],由于库存不足暂时无法处理,请尽快处理!';
										$m[] = array('email'=>$web_config['adminemail'],'subject'=>'用户购买商品','content'=>$content,'addtime'=>time(),'status'=>0);
									}
									
									if(!empty($m)){
										$m_email_queue->MultiInsert($m);
										if($web_config['emailsendtypeswitch']>0){
											$send_email = new \Sendemail();
											$send_email->send($m);
										}
									}
									$data =array('code'=>1,'msg'=>'库存不足,无法处理');
								}
							}else{
								//4.手工操作
								//4.1如果商品有进行库存控制，就减库存
								if($product['stockcontrol']>0){
									$qty_m = array('qty' => 'qty-'.$order['number']);
									$m_products->Where(array('id'=>$order['pid'],'stockcontrol'=>1))->Update($qty_m,TRUE);
								}
								//4.2邮件通知写到消息队列中，然后用定时任务去执行即可
								$m = array();
								//4.2.1通知用户,定时任务去执行
								if(isset($web_config['emailswitch']) AND $web_config['emailswitch']>0){
									if(isEmail($order['email'])){
										$content = '用户:' . $order['email'] . ',购买的商品['.$order['productname'].'],属于手工发货类型，管理员即将联系您....请耐心等待!';
										$m[] = array('email'=>$order['email'],'subject'=>'商品购买成功','content'=>$content,'addtime'=>time(),'status'=>0);
									}
								}
								//4.2.2通知管理员,定时任务去执行
								if(isEmail($web_config['adminemail'])){
									$content = '用户:' . $order['email'] . ',购买的商品['.$order['productname'].'],属于手工发货类型，请尽快联系他!';
									if($order['addons']){
										$content .='订单附加信息：'.$order['addons'];
									}
									$m[] = array('email'=>$web_config['adminemail'],'subject'=>'用户购买商品','content'=>$content,'addtime'=>time(),'status'=>0);
								}
								if(!empty($m)){
									$m_email_queue->MultiInsert($m);
									if($web_config['emailsendtypeswitch']>0){
										$send_email = new \Sendemail();
										$send_email->send($m);
									}
								}
								$data =array('code'=>1,'msg'=>'手工订单');
							}
						}else{
							$data =array('code'=>1003,'msg'=>'订单对应商品不存在');
						}
					}
				}
			}else{
				$data =array('code'=>1003,'msg'=>'订单号不存在');
			}
		} catch(\Exception $e) {
			file_put_contents(YEWU_FILE, CUR_DATETIME.'-reuslt:-notify'.$e->getMessage().PHP_EOL, FILE_APPEND);
			$data =array('code'=>1001,'msg'=>$e->getMessage());
		}
		return $data;
	}
}