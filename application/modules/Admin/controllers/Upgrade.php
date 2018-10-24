<?php

/*
 * 功能：后台中心－升级
 * Author:资料空白
 * Date:20180921
 */
set_time_limit(0);
class UpgradeController extends AdminBasicController
{
	private $github_url = "https://github.com/zlkbdotnet/zfaka/releases";
	private $remote_version = '';
	private $up_version = '';
    public function init()
    {
        parent::init();
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
					$up_version = $this->getSession('up_version');
					if(!$up_version){
						$up_version = $this->_getUpdateVersion();
						$this->setSession('up_version',$up_version);
					}
					if(version_compare(trim(VERSION), trim($up_version), '<' )){
						$data = array('url'=>$this->github_url,'up_version'=>$up_version,'zip'=>"https://github.com/zlkbdotnet/zfaka/archive/{$up_version}.zip");
						$this->getView()->assign($data);
					}else{
						$this->redirect('/'.ADMIN_DIR);
						return FALSE;
					}
				}
			}
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
    }

	public function getremotefileAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$method = $this->getPost('method',false);
		if($method AND $method=='download'){
				$up_version = $this->getSession('up_version');
				if(!$up_version){
					$up_version = $this->_getUpdateVersion();
					$this->setSession('up_version',$up_version);
				}
				if(version_compare(trim(VERSION), trim($up_version), '<' )){
					$url = "https://github.com/zlkbdotnet/zfaka/archive/{$up_version}.zip";
					$up = $this->_download($url,TEMP_PATH);
					if($up){
						\Yaf\Loader::import(FUNC_PATH.'/F_File.php');
						$this->_unzip(TEMP_PATH."/{$up_version}.zip",TEMP_PATH);
						xCopy(TEMP_PATH.'/zfaka-'.$up_version,APP_PATH, 1);
						$data = array('code' => 1, 'msg' => 'ok');
					}else{
						$data = array('code' => 1000, 'msg' => '下载失败');
					}
				}else{
					$data = array('code' => 1000, 'msg' => '有没升级包');
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
	

	private function _download($url,$folder = "")
	{
		if($folder AND !is_dir($folder)){
			mkdir($folder,0777);
		}	
		
		$file = fopen($url, 'rb');
		if ($file){
			// 获取文件大小
			$filesize = -1;
			$headers = get_headers($url, 1);
			if ((!array_key_exists("Content-Length", $headers))){
				 $filesize = 0; 
			}
			$filesize = $headers["Content-Length"];
			$newfname = $folder . basename($url);
			//正式下载
			$newf = fopen ($newfname, "wb");
			$downlen = 0;
			if($newf){
				while(!feof($file))
				{
					$data = fread($file, 1024 * 8 );	//默认获取8K
					$downlen += strlen($data);	// 累计已经下载的字节数
					fwrite($newf, $data, 1024 * 8 );
					ob_flush();
					flush();
				}
				fclose($file);
				fclose($newf);
				return true;
			}
		}else{
			return false;
		}	
	}

	private function _unzip($file = '1.1.4.zip',$folder = "")
	{
		$zip = new ZipArchive;
		if ($zip->open($file) === TRUE) {//中文文件名要使用ANSI编码的文件格式
			$zip->extractTo($folder);//提取全部文件
			$zip->close();
			return true;
		} else {
			return false;
		}
	}
}