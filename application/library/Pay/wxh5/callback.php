<?php
/**
 * File: wxh5.php
 * Functionality: 微信H5支付
 * Author: 资料空白
 * Date: 2018-09-05
 */
namespace Pay\wxh5;

use \Payment\Notify\PayNotifyInterface;

use \Pay\notify;

class callback implements PayNotifyInterface
{
	
	//处理返回回调callback
	public function notifyProcess(array $params)
	{
		$config = array('paymethod'=>'wxh5','tradeid'=>$params['transaction_id'],'paymoney'=>$params['total_fee'],'orderid'=>$params['out_trade_no']);
		$notify = new \Pay\notify();
		return	$data = $notify->run($config);;
	}
	
}