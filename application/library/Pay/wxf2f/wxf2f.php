<?php
/**
 * File: wxf2f.php
 * Functionality: 微信扫码支付
 * Author: 资料空白
 * Date: 2018-09-05
 */
namespace Pay\wxf2f;

use \Payment\Client\Charge;
use \Payment\Common\PayException;
use \Payment\Client\Notify;
use \Payment\Config;

use \Pay\wxf2f\callback;

class wxf2f
{
	private $paymethod ="wxf2f";
	//处理请求
	public function pay($payconfig,$params)
	{
		$config = [
			'use_sandbox' => false,
			'app_id' => $payconfig['app_id'],
			'mch_id' => $payconfig['configure3'],
			'md5_key' => $payconfig['app_secret'],
			'sign_type' => $payconfig['sign_type'],
			'app_cert_pem' => LIB_PATH.'Pay/'.$paymethod.'/pem/weixin_app_cert.pem',
			'app_key_pem' => LIB_PATH.'Pay/'.$paymethod.'/pem/weixin_app_key.pem',
			'fee_type'  => 'CNY',
			'redirect_url' => $params['weburl']. '/product/query/?zlkbmethod=auto&paymethod='.$this->paymethod.'&orderid='.$params['orderid'],
			'notify_url' => $params['weburl'] . '/product/notify/?paymethod='.$this->paymethod,
			'return_raw' => false
		];

		$data = [
			'body'    => $this->paymethod, 
			'subject'    => $params['productname'],
			'order_no'    => $params['orderid'],
			'timeout_express' => time() + 600,// 表示必须 600s 内付款
			'amount'    => $params['money'],
			'return_param' => '',
			'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
			'openid' => 'ohQeiwnNrAsfdsdf9VvmGFIhba--k',
			'product_id' => '',
			// 如果是服务商，请提供以下参数
			'sub_appid' => '',//微信分配的子商户公众账号ID
			'sub_mch_id' => '',// 微信支付分配的子商户号
		];
		try {
			$qr = Charge::run(Config::WX_CHANNEL_QR, $config, $data);
			if($qr){
				$result_params = array('type'=>0,'subjump'=>0,'paymethod'=>$this->paymethod,'qr'=>"/product/order/showqr/?url=".$qr,'payname'=>$payconfig['payname'],'overtime'=>$payconfig['overtime'],'money'=>$params['money']);
				return array('code'=>1,'msg'=>'success','data'=>$result_params);
			}else{
				return array('code'=>1002,'msg'=>'当面付生成失败','data'=>'');
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
			$callback = new \Pay\wxf2f\callback();
			return $ret = Notify::run("wx_charge", $payconfig,$callback);// 处理回调，内部进行了签名检查	
		} catch (\Exception $e) {
			return 'error|Exception:'.$e->getMessage();
		}
	}
	
}