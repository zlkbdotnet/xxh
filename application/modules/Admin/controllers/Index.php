<?php

/*
 * 功能：后台中心－首页
 * Author:资料空白
 * Date:20180509
 */

class IndexController extends AdminBasicController
{
	private $github_url = "https://github.com/zlkbdotnet/zfaka/releases";
	private $remote_version = '';
	private $m_order;
	
    public function init()
    {
        parent::init();
		$this->m_order = $this->load('order');
    }

    public function indexAction()
    {
		if(file_exists(INSTALL_LOCK)){
			if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
				$this->redirect('/'.ADMIN_DIR."/login");
				return FALSE;
			}else{
				$version = @file_get_contents(INSTALL_LOCK);
				$version = str_replace(array("\r","\n","\t"), "", $version);
				$version = strlen(trim($version))>0?$version:'1.0.0';
				if(version_compare(trim($version), trim(VERSION), '<' )){
					$this->redirect("/install/upgrade");
					return FALSE;
				}else{
					//这里要查询待处理的订单
					$data = array();
					$field = array('id','orderid','email','productname','addtime','status','paymoney','number');
					$where = array('isdelete'=>0);
					$where1 = "status = 1 or status = 3";
					$order = $this->m_order->Field($field)->Where($where)->Where($where1)->Order(array('id'=>'DESC'))->Select();
					$data['order'] = $order;
					$this->getView()->assign($data);
				}
			}
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
    }

	public function updatecheckajaxAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$method = $this->getPost('method',false);
		if($method AND $method=='updatecheck'){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$up_version = $this->getSession('up_version');
				if(!$up_version){
					$up_version = $this->_getUpdateVersion();
					$this->setSession('up_version',$up_version);
				}
				if(version_compare(trim(VERSION), trim($up_version), '<' )){
					$params = array('update'=>1,'url'=>$this->github_url,'zip'=>"https://github.com/zlkbdotnet/zfaka/archive/{$up_version}.zip");
					$data = array('code' => 1, 'msg' => '有更新','data'=>$params);
				}else{
					$params = array('update'=>0,'url'=>$this->github_url,'remote_version'=>$this->remote_version);
					$data = array('code' => 1, 'msg' => '没有更新','data'=>$params);
				}
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}

	private function _getUpdateVersion()
	{
		$version = VERSION;
		try{
			$version_reg = '#<a href="/zlkbdotnet/zfaka/archive/(.*?).zip"#';//列表规则 
			$version_html= $this->_get_url_contents($this->github_url,array());
			$version_html=mb_convert_encoding($version_html, 'utf-8', 'gbk');
			preg_match_all($version_reg , $version_html , $cate_matches); 
			if(isset($cate_matches[1]) AND !empty($cate_matches[1])){
				$up_version = trim($cate_matches[1][0]);
				if(strlen($up_version)==5){
					$version = $up_version;
					$this->remote_version = $up_version;
				}
			}
		} catch(\Exception $e) {
			//
		}
		return $version;
	}
	
	private function _get_url_contents($url,$params='')
	{
		if(is_array($params) AND !empty($params)){
			$url .= "?";
			foreach ( $params as $field => $data ){
				$url .= "{$field}=". $data ."&";
			}
			$url = substr( $url, 0, 0 - 1 );	
		}
 
		$ip = rand(1,255).".".rand(1,255).".".rand(1,255).".".rand(1,255).""; 
		$headers=array();
		$headers['Accept-Language'] = "zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3";
		$headers['User-Agent'] = "Mozilla/5.0 (Windows NT 6.1; rv:38.0) Gecko/20100101 Firefox/38.0";
		$headers['X-FORWARDED-FOR'] =$ip;
		$headers['CLIENT-IP'] =$ip;
		$headerArr = array(); 
		foreach( $headers as $n => $v ) { 
			$headerArr[] = $n .':' . $v;  
		}	
 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com/");  
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer  
		curl_setopt($ch, CURLOPT_HTTPHEADER , $headerArr );  //构造IP
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120 ); // 设置超时限制防止死循环  
		curl_setopt($ch, CURLOPT_HEADER, 1 ); // 显示返回的Header区域内容  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回  	
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$html =  curl_exec($ch);
		curl_close($ch);
		return $html;
	}
}