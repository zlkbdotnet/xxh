<?php

/*
 * 功能：会员中心－登录类
 * Author:资料空白
 * Date:20180528
 */

class LoginController extends PcBasicController
{
	private $m_user;
	private $m_user_login_logs;
	
    public function init()
    {
        parent::init();
		$this->m_user = $this->load('user');
		$this->m_user_login_logs = $this->load('user_login_logs');
    }

    public function indexAction()
    {
        if (false != $this->login AND $this->userid) {
            $this->redirect("/member/");
            return FALSE;
        }
		if(isset($this->config['loginswitch']) AND $this->config['loginswitch']<1){
            $this->redirect("/product/");
            return FALSE;
		}
		$data = array();
		$data['title'] = "登录";
        $this->getView()->assign($data);
    }
	
	
	public function ajaxAction()
	{
		if(isset($this->config['loginswitch']) AND $this->config['loginswitch']<1){
			$data = array('code' => 1000, 'msg' => '本系统关闭登录功能');
			Helper::response($data);
		}
		$email    = $this->getPost('email',false);
		$password = $this->getPost('password',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		if($email AND $password AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if(isEmail($email)){
					if(isset($this->config['yzmswitch']) AND $this->config['yzmswitch']>0){
						$vercode = $this->getPost('vercode',false);
						if($vercode){
							if(strtolower($this->getSession('loginCaptcha')) == strtolower($vercode)){
								$this->unsetSession('loginCaptcha');
							}else{
								$data=array('code'=>1004,'msg'=>'图形验证码错误');
								Helper::response($data);
							}
						}else{
							$data = array('code' => 1000, 'msg' => '丢失参数');
							Helper::response($data);
						}
					}

					$checkUser = $this->m_user->checkLogin($email,$password);
					if($checkUser){
						//写入登录日志 
						$m=array('userid'=>$checkUser['id'],'ip'=>getClientIP(),'addtime'=>time());
						$this->m_user_login_logs->Insert($m);
						$data = array('code' => 1, 'msg' =>'success');
					}else{
						$data = array('code' => 1002, 'msg' =>'账户密码错误');
					}
				}else{
					 $data = array('code' => 1003, 'msg' => '邮箱账户有误!');
				}
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
}