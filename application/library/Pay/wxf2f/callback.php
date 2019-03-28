<?php
/**
 * File: wxf2f.php
 * Functionality: 微信扫码支付
 * Author: 资料空白
 * Date: 2018-09-05
 */
namespace Pay\wxf2f;

use \Payment\Notify\PayNotifyInterface;

use \Pay\notify;

class callback implements PayNotifyInterface
{
	
	//处理返回回调callback
	public function notifyProcess(array $params)
	{
		$config = array('paymethod'=>"wxf2f",'tradeid'=>$params['transaction_id'],'paymoney'=>$params['total_fee'],'orderid'=>$params['out_trade_no']);
		$notify = new \Pay\notify();
		return	$data = $notify->run($config);
	}
	
}