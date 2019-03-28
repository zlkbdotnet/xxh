<?php

/*
 * 功能：会员中心－注册类
 * Author:资料空白
 * Date:20180528
 */

class RegisterController extends PcBasicController
{

    private $m_user;

    public function init()
    {
        parent::init();
        $this->m_user = $this->load('user');
    }

    public function indexAction()
    {
        if (false != $this->login AND false != $this->userid) {
            $this->redirect("/member/");
            return FALSE;
        }
		if(isset($this->config['registerswitch']) AND $this->config['registerswitch']<1){
            $this->redirect("/product/");
            return FALSE;
		}
		
		$data = array();
		$data['title'] = "注册";
        $this->getView()->assign($data);
    }

	public function ajaxAction()
	{
		if(isset($this->config['registerswitch']) AND $this->config['registerswitch']<1){
			$data = array('code' => 1000, 'msg' => '本系统关闭注册功能');
			Helper::response($data);
		}
		$email    = $this->getPost('email',false);
		$password = $this->getPost('password');
		$nickname = $this->getPost('nickname');
		$csrf_token = $this->getPost('csrf_token', false);

		if($email AND $password AND $nickname AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if(isEmail($email)){
					if(isset($this->config['yzmswitch']) AND $this->config['yzmswitch']>0){
						$vercode = $this->getPost('vercode');
						if($vercode){
							if(strtolower($this->getSession('registerCaptcha')) == strtolower($vercode)){
								$this->unsetSession('registerCaptcha');
							}else{
								$data=array('code'=>1004,'msg'=>'图形验证码错误');
								Helper::response($data);
							}
						}else{
							$data = array('code' => 1000, 'msg' => '丢失参数');
							Helper::response($data);
						}
					}

						//检查邮箱是否已经使用
					$checkEmailUser = $this->m_user->checkEmail($email);
					if(empty($checkEmailUser)){
						$nickname_string = new \Safe\MyString($nickname);
						$nickname = $nickname_string->trimall()->getValue();
						$m = array('email'=>$email,'password'=>$password,'nickname'=>$nickname);
						$newUser = $this->m_user->newRegister($m);
						if($newUser){
							$data = array('code' => 1, 'msg' =>'success');
						}else{
							$data = array('code' => 1002, 'msg' =>'注册失败');
						}
					}else{
						$data=array('code'=>1005,'msg'=>'邮箱已存在');
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