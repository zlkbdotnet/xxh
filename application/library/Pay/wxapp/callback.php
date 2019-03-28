<?php
/**
 * File: wxapp.php
 * Functionality: 微信APP支付
 * Author: 资料空白
 * Date: 2018-09-05
 */
namespace Pay\wxapp;

use \Payment\Notify\PayNotifyInterface;

use \Pay\notify;

class callback implements PayNotifyInterface
{
	
	//处理返回回调callback
	public function notifyProcess(array $params)
	{
		if($params['body']=='wxapp'){
			$config = array('paymethod'=>$params['body'],'tradeid'=>$params['trade_no'],'paymoney'=>$params['total_amount'],'orderid'=>$params['out_trade_no'] );
			$notify = new \Pay\notify();
			$data = $notify->run($config);
		}else{
			$data =array('code'=>1002,'msg'=>'支付方式不对');
		}
		return $data;
	}
	
}