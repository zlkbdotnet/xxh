<?php
namespace Pay\yzpay;
class YzClient
{
	protected $clientid;
	protected $clientsecret;
	protected $kdtid;
	protected $qrprice;
	protected $qrname;
	
	public function setclientid($clientid)
	{
		$this->Clientid = $clientid;
	}
	public function setclientsecret($clientsecret)
	{
		$this->Clientsecret = $clientsecret;
	}
	public function setkdtid($kdtid)
	{
		$this->Kdtid = $kdtid;
	}
	public function setqrprice($qrprice)
	{
		$this->Qrprice = $qrprice * 100;
	}
	public function setqrname($qrname)
	{
		$this->Qrname = $qrname;
	}

	public function YzQrPayServie() 
	{
		//获取应用Token
		$CommonConfigs = array(
			'client_id'=>$this->Clientid,     						//有赞云颁发给开发者的应用ID
			'client_secret'=>$this->Clientsecret, 					//有赞云颁发给开发者的应用secret
			'kdt_id'=>$this->Kdtid,        							//授权给该应用的店铺id，控制台里可查看
			'grant_type'=>'silent'      							//授与方式（固定为 “silent”）
		);	
		$token = $this->curlPost('https://open.youzan.com/oauth/token',$CommonConfigs);
		$token = json_decode($token,true);
		
		//获取支付二维码
		$Configs = array(
			'access_token'=>$token['access_token'],     			//应用Token
			'qr_name'=>$this->Qrname,          						//收款理由
			'qr_price'=>$this->Qrprice,                             //价格（单位 元）
			'qr_type'=>'QR_TYPE_DYNAMIC'                            //二维码类型   
		);		
		$result = $this->curlPost('https://open.youzan.com/api/oauthentry/youzan.pay.qrcode/3.0.0/create',$Configs);
		return json_decode($result,true);
	}

	public function curlPost($url = '', $postData = '')
	{
	    /**
		* 模拟post进行url请求
		* @param string $url
		* @param string $postData
		*/
		$ch = curl_init();								//初始化curl
		curl_setopt($ch, CURLOPT_URL, $url);			//设置抓取的url	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, true);			//设置post方式提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);//设置post数据
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); 			//设置cURL允许执行的最长秒数
		
		//https请求 不验证证书和host
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}