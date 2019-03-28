<?php
/**
 * File: zfbf2f.php
 * Functionality: 支付宝面对面支付
 * Author: 资料空白
 * Date: 2018-6-8
 */
namespace Pay\zfbf2f;

use \Payment\Client\Charge;
use \Payment\Common\PayException;
use \Payment\Client\Notify;
use \Payment\Config;

use \Pay\zfbf2f\callback;

class zfbf2f
{
	private $paymethod ="zfbf2f";
	//处理请求
	public function pay($payconfig,$params)
	{
		$config = [
			'use_sandbox' => false,
			'app_id' => $payconfig['app_id'],
			'sign_type' => $payconfig['sign_type'],
			'ali_public_key' => $payconfig['ali_public_key'],
			'rsa_private_key' => $payconfig['rsa_private_key'],
			'return_url' => $params['weburl']. "/query/auto/{$params['orderid']}.html",
			'notify_url' => $params['weburl'] . '/product/notify/?paymethod='.$this->paymethod,
			'return_raw' => true
		];
	
		//20181217,支付宝当面付对subject不支持特殊字符 "="号
		$params['productname'] = str_replace("=","",$params['productname']);
		
		$data = [
			'order_no' => $params['orderid'],
			'amount' => $params['money'],
			'subject' => $params['productname'],
			'body' => $this->paymethod, 
		];
		try {
			$qr = Charge::run(Config::ALI_CHANNEL_QR, $config, $data);
			if($qr){
				$result_params = array('type'=>0,'subjump'=>1,'subjumpurl'=>$qr,'paymethod'=>$this->paymethod,'qr'=>$params['qrserver'].$qr,'payname'=>$payconfig['payname'],'overtime'=>$payconfig['overtime'],'money'=>$params['money']);
				return array('code'=>1,'msg'=>'success','data'=>$result_params);
			}else{
				return array('code'=>1002,'msg'=>'支付接口请求失败','data'=>'');
			}
		} catch (PayException $e) {
			return array('code'=>1001,'msg'=>$e->errorMessage(),'data'=>'');
		} catch (\Exception $e) {
			return array('code'=>1000,'msg'=>$e->getMessage(),'data'=>'');
		}
	}
	
	public function notify(array $payconfig)
	{
		try {
			file_put_contents(YEWU_FILE, CUR_DATETIME.'-'.json_encode($_POST).PHP_EOL, FILE_APPEND);
			unset($_POST['paymethod']);
			$callback = new \Pay\zfbf2f\callback();
			return $ret = Notify::run("ali_charge", $payconfig,$callback);// 处理回调，内部进行了签名检查	
		} catch (\Exception $e) {
			return 'error|Exception:'.$e->getMessage();
		}
	}
	
}