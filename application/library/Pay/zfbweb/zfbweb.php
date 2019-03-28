<?php
/**
 * File: zfbweb.php
 * Functionality: 支付宝电脑网站支付
 * Author: 资料空白
 * Date: 2018-6-8
 */
namespace Pay\zfbweb;

use \Payment\Client\Charge;
use \Payment\Common\PayException;
use \Payment\Client\Notify;
use \Payment\Config;

use \Pay\zfbweb\callback;

class zfbweb
{
	private $paymethod ="zfbweb";
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

		$data = [
			'order_no' => $params['orderid'],
			'amount' => $params['money'],
			'subject' => $params['productname'],
			'body' => $this->paymethod, 
		];
		try {
			$url = Charge::run(Config::ALI_CHANNEL_WEB, $config, $data);
			if($url){
				$result = array('type'=>1,'subjump'=>0,'paymethod'=>$this->paymethod,'url'=>$url,'payname'=>$payconfig['payname'],'overtime'=>$payconfig['overtime'],'money'=>$params['money']);
				return array('code'=>1,'msg'=>'success','data'=>$result);
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
			
			$config = [
				'use_sandbox' => false,
				'app_id' => $payconfig['app_id'],
				'sign_type' => $payconfig['sign_type'],
				'ali_public_key' => $payconfig['ali_public_key'],
				'rsa_private_key' => $payconfig['rsa_private_key'],
				'return_raw' => true
			];
			
			$callback = new \Pay\zfbweb\callback();
			$ret = Notify::run("ali_charge", $config,$callback);// 处理回调，内部进行了签名检查
			file_put_contents(YEWU_FILE, CUR_DATETIME.'-'.json_encode($ret).PHP_EOL, FILE_APPEND);
			var_dump($ret);
			exit();
		} catch (\Exception $e) {
			file_put_contents(YEWU_FILE, CUR_DATETIME.'-'.$e->getMessage().PHP_EOL, FILE_APPEND);
			exit;
		}
	}
	
}