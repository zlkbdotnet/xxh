<?php
/**
 * File: yzpay.php
 * Functionality: 有赞支付
 * Author: 资料空白
 * Date: 2018-6-8
 */
namespace Pay\yzpay;

use \Pay\yzpay\YzClient;
use \Pay\notify;

class yzpay
{
	private $paymethod ="yzpay";
	//处理请求
	public function pay($payconfig,$params)
	{
		try{
			$yzclient = new YzClient();
			$yzclient->setclientid($payconfig['app_id']);
			$yzclient->setclientsecret($payconfig['app_secret']);
			$yzclient->setkdtid($payconfig['configure3']);
			$yzclient->setqrprice($params['money']);
			$yzclient->setqrname("付费商品-".$params['orderid']);	
			
			$result = $yzclient->YzQrPayServie();	
			if(is_array($result) AND isset($result['response']['qr_code']) AND $result['response']['qr_code']){
				$result_params = array('type'=>0,'subjump'=>0,'paymethod'=>$this->paymethod,'qr'=>$params['qrserver'].urlencode($result['response']['qr_url']),'payname'=>$payconfig['payname'],'overtime'=>$payconfig['overtime'],'money'=>$params['money']);
				return array('code'=>1,'msg'=>'success','data'=>$result_params);
			} else {
				return array('code'=>1002,'msg'=>'支付接口请求失败','data'=>'');
			}
		} catch (\Exception $e) {
			return array('code'=>1000,'msg'=>$e->getMessage(),'data'=>'');
		}
	}
	
	public function notify(array $payconfig)
	{
		try {
			$POST_input = file_get_contents('php://input');
			$input_params = json_decode($POST_input, true);
			
			if($input_params['test'] != "true"){	//判断消息是否测试
				file_put_contents(YEWU_FILE, CUR_DATETIME.'-'.json_encode($input_params).PHP_EOL, FILE_APPEND);
			unset($_POST['paymethod']);
				$client_id = $payconfig['app_id'];							//应用的 client_id
				$client_secret = $payconfig['app_secret'];					//应用的 client_secret
				$sign = md5($client_id."".$input_params['msg']."".$client_secret);
				
				if($input_params['mode'] == "1" and $sign == $input_params['sign'] and $input_params['type'] == "trade_TradePaid")//判断消息推送的模式/消息是否伪造/消息的业务
				{
					#下面开始处理业务
					$imsg = json_decode(urldecode($input_params['msg']),true);
					$amount = $imsg['full_order_info']['orders']['0']['payment']; 	//交易金额
					$title = $imsg['full_order_info']['orders']['0']['title']; 		//交易标题
					$id = $input_params['id'];    										//有赞交易号
					$orderid = explode("-",$title)[1];
					$config = array('paymethod'=>$this->paymethod,'tradeid'=>$id,'paymoney'=>$amount,'orderid'=>$orderid );
					$notify = new \Pay\notify();
					$data = $notify->run($config);
					return '{"code":0,"msg":"success"}';
				}
			}else{
				return '{"code":0,"msg":"success"}'; 
			}
		} catch (\Exception $e) {
			return 'error|Exception:'.$e->getMessage();
		}
	}
	
}