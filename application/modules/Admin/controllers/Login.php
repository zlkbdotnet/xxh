<?php

/*
 * 功能：后台中心－登录类
 * Author:资料空白
 * Date:20180528
 */

class LoginController extends AdminBasicController
{
	private $m_admin_user;

    public function init()
    {
        parent::init();
		$this->m_admin_user = Helper::load('admin_user');
    }

    public function indexAction()
    {
		if(file_exists(INSTALL_LOCK)){
			if ($this->AdminUser) {
				$this->redirect('/'.ADMIN_DIR);
				return FALSE;
			}
			$data = array();
			$this->getView()->assign($data);
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
    }
	
	
	public function ajaxAction()
	{
		$email    = $this->getPost('email');
		$password = $this->getPost('password');
		$csrf_token = $this->getPost('csrf_token', false);
		
		if($email AND $password AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if(isEmail($email)){
					if(isset($this->config['adminyzmswitch']) AND $this->config['adminyzmswitch']>0){
						$vercode = $this->getPost('vercode');
						if($vercode){
							if(strtolower($this->getSession('adminloginCaptcha')) == strtolower($vercode)){
								$this->unsetSession('adminloginCaptcha');
							}else{
								$data=array('code'=>1004,'msg'=>'图形验证码错误');
								Helper::response($data);
							}
						}else{
							$data = array('code' => 1000, 'msg' => '丢失参数');
							Helper::response($data);
						}
					}					

					$resultAdminUser = $this->m_admin_user->checkLogin($email,$password);
					if($resultAdminUser){
						$this->setLogin($resultAdminUser);
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