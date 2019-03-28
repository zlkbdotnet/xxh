<?php
/**
 * File: zlkbcodepayqq.php
 * Functionality: 收款宝(资料空白)-QQ扫码支付
 * Author: 资料空白
 * Date: 2018-07-02
 */
namespace Pay\zlkbcodepayqq;
use \Pay\notify;

class zlkbcodepayqq
{
	private $apiHost="https://codepay.zlkb.net/api/order";
	private $paymethod ="zlkbcodepayqq";
	
	//处理请求
	public function pay($payconfig,$params)
	{
		try{
			$config =array(
				'version'=>1,
				'paymethod'=>3,
				'appid'=>$payconfig['app_id'],
				'ordersn'=>$params['orderid'],
				'subject'=>$params['productname'],
				'money'=>(float)$params['money'],
				'overtime'=>$payconfig['overtime'],
				'return_url' => $params['weburl']. "/query/auto/{$params['orderid']}.html",
				'notify_url' => $params['weburl'] . '/product/notify/?paymethod='.$this->paymethod,
			);
			if(isset($payconfig['configure3']) AND strlen($payconfig['configure3'])>0){
				$url = $payconfig['configure3'];
			}else{
				$url = $this->apiHost;
			}
			$config['sign'] = $this->_signParams($config,$payconfig['app_secret']);
			$curl_data =  $this->_curlPost($url,$config);
			$curl_data = json_decode($curl_data,true);
			if(is_array($curl_data)){
				if($curl_data['code']<1){
					return array('code'=>1002,'msg'=>$curl_data['msg'],'data'=>'');
				}else{
					$money = isset($curl_data['data']['money'])?$curl_data['data']['money']:$params['money'];
					if(isset($payconfig['configure4']) AND strlen($payconfig['configure4'])>0 AND $payconfig['configure4']>0){
						$result = array('type'=>1,'subjump'=>0,'paymethod'=>$this->paymethod,'url'=>$curl_data['data']['payurl'],'payname'=>$payconfig['payname'],'overtime'=>$payconfig['overtime'],'money'=>$money);
					}else{
						//计算关闭时间
						$closetime = (int)($curl_data['data']['closetime']-$curl_data['data']['servertime']-3);
						$result = array('type'=>0,'subjump'=>1,'subjumpurl'=>$curl_data['data']['qr_content'],'paymethod'=>$this->paymethod,'qr'=>$params['qrserver'].urlencode($curl_data['data']['qr_content']),'payname'=>$payconfig['payname'],'overtime'=>$closetime,'money'=>$money);
					}
					return array('code'=>1,'msg'=>'success','data'=>$result);
				}
			}else{
				return array('code'=>1001,'msg'=>"支付接口请求失败",'data'=>'');
			}
		} catch (\Exception $e) {
			return array('code'=>1000,'msg'=>$e->getMessage(),'data'=>'');
		}
	}
	
	
	//处理返回
	public function notify($payconfig)
	{
		file_put_contents(YEWU_FILE, CUR_DATETIME.'-'.json_encode($_POST).PHP_EOL, FILE_APPEND);
		if(!empty($_POST)){
			$params = $_POST;
			$newsign = $this->_signParams($params,$payconfig['app_secret']);
			
			if ($newsign != $params['sign']) { //不合法的数据 KEY密钥为你的密钥
				return 'error|Notify: auth fail';
			} else { //合法的数据
				//业务处理
				$config = array('paymethod'=>$this->paymethod,'tradeid'=>$params['orderid'],'paymoney'=>$params['money'],'orderid'=>$params['ordersn'] );
				$notify = new \Pay\notify();
				$data = $notify->run($config);
				if($data['code']>1){
					return 'error|Notify: '.$data['msg'];
				}else{
					return 'success';
				}
			}
		}else{
			return 'error|Notify: empty';
		}
	}
	
	
	private function _curlPost($url,$params){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,300); //设置超时
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;	
	}
	
	private function _signParams($params,$secret){
		$sign = $signstr = "";
		if(!empty($params)){
			ksort($params);
			reset($params);
			
			foreach ($params AS $key => $val) {
				if ($key == 'sign') continue;
				if ($signstr != '') {
					$signstr .= "&";
				}
				$signstr .= "$key=$val";
			}
			$sign = md5($signstr.$secret);
		}
		return $sign;
	}	
	
}
