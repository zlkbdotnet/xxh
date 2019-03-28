<?php
/**
 * File: BasicPc.php
 * Functionality: Basic Controller(再整理)
 * Author: 资料空白
 * Date: 2016-3-8
 */

class PcBasicController extends BasicController
{
	protected $uinfo=array();
	protected $userid=0;
	protected $login=FALSE;
    public $serverPrivateKey = "MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAPiKwTi/M+PNwqvKI37LyTDWF3sRDHuOariVfBpIEv3976jViWDsTHNL5oxd+D2mRqdty3KM3SySItPE2DCQj/j7FoDn7Gz+F34GiqOmGKoRIBZy4N6C2P4d7G2x2DtMk2dMwg6/ZMzXumuyeziEXUMnPlpIomroaTCGWPr2/tmxAgMBAAECgYEAjK3FNoCLN2sUwDX3J2Ljqx/TRJZe0WTIJVh/WUTocxmT2KWdT94QW8ZfZZ4ez45ZOZWc7WasHflNez5U/BAnXLH89XmCuAWdCUqbkDm7fD76qa0gO0ScQrZQ34fTkBYaW2EAM40Mqd8rCAEuCBu6JVkP7wnaAU1MeQEvmVtv0H0CQQD848oh3WYoWZacUmq84udlnbycRAySka/J8/VImYVmQ2O/i4Y/GAZOeHtjrtfNZAtOxCbAkpnpmZfdgoIx3bd3AkEA+5lGwc5krprOHFVsJLiWLLpV+aFBPD5IrATaJ6X+l6EAxl1gUhaGlz85r9Jy6HCGi6Mv07gmPmgzUVjb+XsSFwJAKtzhEcRY4FXu9Sfy93juB4coxMOz7dPLm8tBs8Bxn9ekPH8FjgQgbYR2RXsJEML4N61/c/xlIfbqipzoPFN8GQJBAMnp+ZoBvFVQEUc12sMhjAu7QtJCcmsZhRLgFf+pvMcNQ+Tt/SYDw+HPsMkEuIkH/UJFJVXhPHfrAfwvtuHhveMCQQCMXoBkqc8GhjaXPvW0ZJ2IVu+5lo/YhCG4GY2YsLPysA7XMJMwojuETHwcuqMJ2fXvIxrlGTLjGJmV9Bi7nebO";
    public $serverPublicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQD4isE4vzPjzcKryiN+y8kw1hd7EQx7jmq4lXwaSBL9/e+o1Ylg7ExzS+aMXfg9pkanbctyjN0skiLTxNgwkI/4+xaA5+xs/hd+BoqjphiqESAWcuDegtj+Hextsdg7TJNnTMIOv2TM17prsns4hF1DJz5aSKJq6Gkwhlj69v7ZsQIDAQAB";
  	
	public function init(){	
		parent::init();
		$sysvars = $data = array();
		$this->config=$this->load('config')->getConfig();
		if((isset($this->config['web_name']) AND strlen($this->config['web_name'])>0)==false){
			$this->config['web_name'] = WEB_NAME;
		}
		$data['config']= $this->config;
		$sysvars['isHttps']=$this->isHttps=isHttps();
		$sysvars['isAjax']=$this->isAjax=isAjax();
		$sysvars['isGet']=$this->isGet=isGet();
		$sysvars['isPost']=$this->isPost=isPost();
		$sysvars['currentUrl']=stripHTML(str_replace('//', '/',$_SERVER['REQUEST_URI']));
		$sysvars['currentUrlSign']=md5(URL_KEY.$sysvars['currentUrl']);
		$data['sysvars']=$sysvars; 
        $uinfo = $this->getSession('uinfo');
		if(is_array($uinfo) AND !empty($uinfo) AND $uinfo['expiretime']>time()){
			$groupName=$this->load('user_group')->getConfig();
			$uinfo['groupName'] = $groupName[$uinfo['groupid']];
			$uinfo['expiretime'] = time() + 15*60;
			$this->setSession('uinfo',$uinfo);
			$data['login']=$this->login=true;
			$data['uinfo']= $this->uinfo=$uinfo;
			$this->userid=$uinfo['id'];
		}else{
			$data['login']=$this->login=false;
			$this->unsetSession('uinfo');
		}
		//防csrf攻击
		$data['csrf_token'] = $this->createCsrfToken();
        $this->getView()->assign($data);
	}

    //生成JWT token
    public function createToken()
    {
        $tokenKey = array(
            "iss" => "http://zlkb.net",  //jwt签发者
            "aud" => 'RPC',                     //接收jwt的一方
            "exp" => time() + 600,               //过期时间
        );
        return JWT::encode($tokenKey, self::readRSAKey($this->serverPrivateKey), 'RS256');
    }
    //为JWT准备的，证书处理函数
    private static function readRSAKey($key)
    {
        $isPrivate = strlen($key) > 500;
        if ($isPrivate) {
            $lastKey = chunk_split($key, 64, "\n");
            $lastKey = "-----BEGIN RSA PRIVATE KEY-----\n" . $lastKey . "-----END RSA PRIVATE KEY-----\n";
            return $lastKey;
        } else {
            $lastKey = chunk_split($key, 64, "\n");
            $lastKey = "-----BEGIN PUBLIC KEY-----\n" . $lastKey . "-----END PUBLIC KEY-----\n";
            return $lastKey;
        }
    }	
	public function show_message($code='',$msg='',$url='/'){
		$this->forward("Index",'Showmsg','index',array('code'=>$code,'msg'=>$msg,'url'=>$url));
		return FALSE; 
	}
	
	//生成csrftoken　防csrf攻击
    private function createCsrfToken(){
    	$csrf_token = $this->getSession('csrf_token');
		$isCreate = false;
		if($csrf_token){
			try {
				if(!isAjax()){
					$decoded = JWT::decode($csrf_token, self::readRSAKey($this->serverPublicKey), array('RS256'));
					$tokenKey = (array)$decoded;
					if (is_array($tokenKey) AND !empty($tokenKey)) {
						
		
					} else {
						$isCreate = true;
					}
				}
			}catch(\Exception $e){
				$isCreate = true;
			}
		}else{
			$isCreate = true;
		}
		
    	if($isCreate == true){
    		$csrf_token=$this->createToken(); 
			$this->setSession('csrf_token',$csrf_token);
    	}
		return $csrf_token;
	}
	//验证csrftoken 防csrf攻击
	public function VerifyCsrfToken($csrf_token=''){
		$csrf_token = $csrf_token?$csrf_token:$this->getPost('csrf_token',false);
		$session_csrf_token = $this->getSession('csrf_token',false); 
		if($session_csrf_token && $session_csrf_token==$csrf_token){
			try {
				$decoded = JWT::decode($csrf_token, self::readRSAKey($this->serverPublicKey), array('RS256'));
                $tokenKey = (array)$decoded;
                if (is_array($tokenKey) AND !empty($tokenKey)) {
					if(!isAjax()){
						$this->setSession('csrf_token','');
					}
					return true;
                } else {
                    return false;
                }
			}catch(\Exception $e){
				return false;
			}
		}else{
			return false;
		}
	}
}