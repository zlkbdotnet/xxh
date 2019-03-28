<?php
/**
 * File: codepayqq.php
 * Functionality: 码支付-QQ扫码支付
 * Author: 资料空白
 * Date: 2018-07-02
 */
namespace Pay\codepayqq;
use \Pay\notify;

class codepayqq
{
	private $apiHost="http://api2.fateqq.com:52888/creat_order/?";
	private $paymethod ="codepayqq";
	
	//处理请求
	public function pay($payconfig,$params)
	{
		$config = array(
			"id" => (int)$payconfig['app_id'],//平台ID号
			"type" => 2,//支付方式
			"price" => (float)$params['money'],//原价
			"pay_id" => $params['orderid'], //可以是用户ID,站内商户订单号,用户名
			"param" => '',//自定义参数
			"act" => 0,//此参数即将弃用
			"outTime" => $payconfig['overtime'],//二维码超时设置
			"page" => 4,//订单创建返回JS 或者JSON
			"return_url" => $params['weburl']. "/query/auto/{$params['orderid']}.html",
			"notify_url" => $params['weburl'] . '/product/notify/?paymethod='.$this->paymethod,
			"style" =>1,//付款页面风格
			"pay_type" => 1,//支付宝使用官方接口
			"user_ip" => getClientIP(),//付款人IP
			"qrcode_url" =>'',//本地化二维码
			"chart" => trim(strtolower('utf-8'))//字符编码方式
			//其他业务参数根据在线开发文档，添加参数.文档地址:https://codepay.fateqq.com/apiword/
			//如"参数名"=>"参数值"
		);
		
		try{
			
			$back = $this->_create_link($config, $payconfig['app_secret']); //生成支付URL
			if (function_exists('file_get_contents')) { //如果开启了获取远程HTML函数 file_get_contents
				$codepay_json = file_get_contents($back['url']); //获取远程HTML
			} else if (function_exists('curl_init')) {
				$ch = curl_init(); //使用curl请求
				curl_setopt($ch, CURLOPT_URL, $back['url']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				$codepay_json = curl_exec($ch);
				curl_close($ch);
			}
			$codepay_data = json_decode($codepay_json,true);
			if(is_array($codepay_data)){
				if($codepay_data['status']<0){
					return array('code'=>1002,'msg'=>$codepay_data['msg'],'data'=>'');
				}else{
					$qr = $codepay_data ? $codepay_data['qrcode'] : '';
					$money = isset($codepay_data['money'])?$codepay_data['money']:$params['money'];
					//计算关闭时间
					$closetime = (int)($codepay_data['endTime']-$codepay_data['serverTime']-3);
					$result = array('type'=>0,'subjump'=>0,'paymethod'=>$this->paymethod,'qr'=>$qr,'payname'=>$payconfig['payname'],'overtime'=>$closetime,'money'=>$money);
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
		$params = $_POST;
		ksort($params); //排序post参数
		reset($params); //内部指针指向数组中的第一个元素
		$sign = '';
		foreach ($params AS $key => $val) {
			if ($val == '') continue;
			if ($key != 'sign') {
				if ($sign != '') {
					$sign .= "&";
					$urls .= "&";
				}
				$sign .= "$key=$val"; //拼接为url参数形式
				$urls .= "$key=" . urlencode($val); //拼接为url参数形式
			}
		}
		if (!$params['pay_no'] || md5($sign . $payconfig['app_secret']) != $params['sign']) { //不合法的数据 KEY密钥为你的密钥
			return 'error|Notify: auth fail';
		} else { //合法的数据
			//业务处理
			$config = array('paymethod'=>$this->paymethod,'tradeid'=>$params['pay_no'],'paymoney'=>$params['money'],'orderid'=>$params['pay_id'] );
			$notify = new \Pay\notify();
			$data = $notify->run($config);
			if($data['code']>1){
				return 'error|Notify: '.$data['msg'];
			}else{
				return 'success';
			}
		}
	}
	
	
	/**
	 * 加密函数
	 * @param $params 需要加密的数组
	 * @param $codepay_key //码支付密钥
	 * @param string $host //使用哪个域名
	 * @return array
	 */
	private function _create_link($params, $codepay_key, $host = "")
	{
		ksort($params); //重新排序$data数组
		reset($params); //内部指针指向数组中的第一个元素
		$sign = '';
		$urls = '';
		foreach ($params AS $key => $val) {
			if ($val == '') continue;
			if ($key != 'sign') {
				if ($sign != '') {
					$sign .= "&";
					$urls .= "&";
				}
				$sign .= "$key=$val"; //拼接为url参数形式
				$urls .= "$key=" . urlencode($val); //拼接为url参数形式
			}
		}

		$key = md5($sign . $codepay_key);//开始加密
		$query = $urls . '&sign=' . $key; //创建订单所需的参数
		$apiHost = ($host ? $host : $this->apiHost); //网关
		$url = $apiHost . $query; //生成的地址
		return array("url" => $url, "query" => $query, "sign" => $sign, "param" => $urls);
	}
}
