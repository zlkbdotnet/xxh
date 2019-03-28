<?php

/*
 * 功能：会员中心－个人中心
 * Author:资料空白
 * Date:20180509
 */

class ProfilesController extends PcBasicController
{
    private $m_user;
	
	public function init()
    {
        parent::init();
		$this->m_user = $this->load('user');
    }

    public function indexAction()
    {
        if ($this->login==FALSE AND !$this->userid) {
            $this->redirect("/member/login");
            return FALSE;
        }
		$data = array();
		$uinfo = $this->m_user->SelectByID('nickname,email,qq,tag,createtime',$this->userid);
		$data['uinfo'] = $this->uinfo = array_merge($this->uinfo, $uinfo);
		$data['title'] = "我的资料";
        $this->getView()->assign($data);
    }

	public function profilesajaxAction()
	{
		$nickname = $this->getPost('nickname',false);
		$qq = $this->getPost('qq',false);
		$tag = $this->getPost('tag',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->login==FALSE AND !$this->userid) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($nickname AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$nickname_string = new \Safe\MyString($nickname);
				$nickname = $nickname_string->trimall()->getValue();
				
				$qq_string = new \Safe\MyString($qq);
				$qq = $qq_string->trimall()->getValue();
				
				$this->m_user->UpdateByID(array('nickname'=>$nickname,'qq'=>$qq,'tag'=>$tag),$this->userid);
				$data = array('code' => 1, 'msg' => '更新成功');
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
	
	
	public function passwordAction()
	{
        if ($this->login==FALSE AND !$this->userid) {
            $this->redirect("/member/login");
            return FALSE;
        }
		$data = array();
		$data['title'] = "密码";
        $this->getView()->assign($data);
	}
	
	public function passwordajaxAction()
	{
		$password = $this->getPost('password',false);
		$oldpassword = $this->getPost('oldpassword',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->login==FALSE AND !$this->userid) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($password AND $oldpassword AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if ($oldpassword !== $password) {
					if (strlen($password) < 6 ) {
						$data = array('code' => 1002, 'msg' => '密码过于简单,密码至少6位');
					} else {
						$check = $this->m_user->checkLogin($this->uinfo['email'], $oldpassword);
						if ($check) {

								$update = $this->m_user->changePWD($this->userid, $password);
								if ($update) {
									$data = array('code' => 1, 'msg' => '修改密码成功');
									$this->unsetSession('uinfo');
								} else {
									$data = array('code' => 1004, 'msg' => '数据更新异常');
								}

						} else {
							$data = array('code' => 1003, 'msg' => '原始密码不正确');
						}
					}
				} else {
					$data = array('code' => 1001, 'msg' => '新旧密码不能相同');
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