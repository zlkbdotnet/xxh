<?php

/*
 * 功能：会员中心－忘记密码类
 * Author:资料空白
 * Date:20150909
 */

class ForgetpwdController extends PcBasicController
{
	private $m_user;
	private $m_email_code;
    private $m_email;
	
	public function init()
    {
        parent::init();
		$this->m_user = $this->load('user');
		$this->m_email_code = $this->load('email_code');
		$this->m_email = $this->load('email');
    }

    public function indexAction()
    {
        if (false != $this->login AND $this->userid) {
            $this->redirect("/member/");
            return FALSE;
        }
		if(isset($this->config['forgetpwdswitch']) AND $this->config['forgetpwdswitch']<1){
            $this->redirect("/product/");
            return FALSE;
		}
		$data['title'] = "找回密码";
		$this->getView()->assign($data);
    }
	
	public function resetAction()
	{
		if(isset($this->config['forgetpwdswitch']) AND $this->config['forgetpwdswitch']<1){
            $this->redirect("/product/");
            return FALSE;
		}
        $key = $this->get('key', false);
		$data = array();
        if (false != $key) {
			$key=base64_decode($key);
            $key_array = explode('-', $key);
            if (isset($key_array[2]) AND false != $key_array[2]) {
                $code = $key_array[0];
                $id = (int)$key_array[1];
				$email = $key_array[2];
				
                if (false != $code AND is_numeric($id) AND $id > 0 AND isEmail($email)) {
                    //从数据库中读取
                    $where = array('email' => $email, 'id' => $id, 'code' => $code, 'status' => 1,'action'=>'forgetpwd');
                    $email_code = $this->m_email_code->Where($where)->SelectOne();
                    if (!empty($email_code)) {
						if($email_code['checkedStatus']>0){
							$data = array('code'=>1001,'msg'=>'该重置密码链接已失效，请重新校验您的信息');
						}else{
							$data['code'] = 1;
							$data['msg'] = 'success';
							$data['email'] = $email;
							$data['emailcode'] = $code;
						}
                    }else{
						$data = array('code'=>1000,'msg'=>'非法链接，请重新校验您的信息');
					}
                }else{
					$data = array('code'=>1000,'msg'=>'非法链接，请重新校验您的信息');
				}
            }
        }else{
			$data = array('code'=>1000,'msg'=>'非法链接，请重新校验您的信息');
		}
		$data['title'] = "找回密码";
		$this->getView()->assign($data);
	}
	
	//找回密码 2.重设
	public function resetajaxAction()
	{
		if(isset($this->config['forgetpwdswitch']) AND $this->config['forgetpwdswitch']<1){
			$data = array('code' => 1000, 'msg' => '本系统关闭密码重置功能');
			Helper::response($data);
		}
		$email = $this->getPost('email');
		$code = $this->getPost('code');
		$password = $this->getPost('password');
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
		if($email AND $code AND $password AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
                //从数据库中读取
                $where = array('email' => $email, 'code' => $code, 'status' => 1,'action'=>'forgetpwd' ,'checkedStatus'=>0);
                $email_code = $this->m_email_code->Where($where)->SelectOne();
                if (!empty($email_code)) {
					$change = $this->m_user->changePWD($email_code['userid'], $password);
					if($change){
						$data = array('code' => 1, 'msg' => 'success');
					}else{
						$data = array('code' => 1003, 'msg' => '修改失败');
					}
					$this->m_email_code->UpdateByID(array('checkedStatus'=>1),$email_code['id']);
                }else{
					$data = array('code' => 1002, 'msg' => '验证失败');
				}
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
	//找回密码 1.验证邮箱
	public function ajaxAction()
	{
		if(isset($this->config['forgetpwdswitch']) AND $this->config['forgetpwdswitch']<1){
			$data = array('code' => 1000, 'msg' => '本系统关闭密码重置功能');
			Helper::response($data);
		}
		$email    = $this->getPost('email');
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
		if($email AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if(isEmail($email)){
					if(isset($this->config['yzmswitch']) AND $this->config['yzmswitch']>0){
						$vercode = $this->getPost('vercode');
						if($vercode){
							if(strtolower($this->getSession('forgetpwdCaptcha')) == strtolower($vercode)){
								$this->unsetSession('forgetpwdCaptcha');
							}else{
								$data=array('code'=>1004,'msg'=>'图形验证码错误');
								Helper::response($data);
							}
						}else{
							$data = array('code' => 1000, 'msg' => '丢失参数');
							Helper::response($data);
						}
					}	
					

						$checkEmailUser = $this->m_user->checkEmail($email);
						if(!empty($checkEmailUser)){
								//1.查询该用户当天找回密码次数
								$startTime = strtotime(date('Y-m-d 0:0:0'));
								$endTime   = strtotime(date('Y-m-d 23:59:59'));
								$email_code = $this->m_email_code->Where(array('userid' => $checkEmailUser['id'],'action'=>'forgetpwd'))->Where("addtime>{$startTime} and  addtime<{$endTime}")->Total();
								if ($email_code>4) {
									$data = array('code' => 1002, 'msg' =>'找回密码次数过多，请明天再试');
								}else{
									//2.如果不存在则写入
									$m = array(
										'action'=>'forgetpwd',
										'userid' => $checkEmailUser['id'],
										'email' => $email,
										'code' => getRandom(8, 5),
										'ip' =>getClientIP(),
										'result'=>'',
										'addtime' => time(),
										'status' => 0,
										'checkedStatus'=>0
									);
									$m['id'] = $this->m_email_code->Insert($m);
									
									//3.发送邮件
									try {
										$key=base64_encode("{$m['code']}-{$m['id']}-{$email}");
										$str = "key={$key}";
										$url = siteUrl($this->config['weburl'], "/member/forgetpwd/reset", $str);
										$content = '用户' . $email . ',请点击此链接重置密码<a href="' . $url . '">' . $url . '</a>';
										$emainConfig = $this->m_email->getConfig();
										$config=array();
										$config['smtp_host'] = 'ssl://' . $emainConfig['host'];
										$config['smtp_user'] = $emainConfig['mailaddress'];
										$config['smtp_pass'] = $emainConfig['mailpassword'];
										$config['smtp_port'] = $emainConfig['port'];
										$lib_email = new Email($config);
										$lib_email->from($emainConfig['sendmail'], $emainConfig['sendname']);
										$lib_email->to($email);
										$lib_email->subject('密码重置通知!');
										$lib_email->message($content);
										$isSend = $lib_email->send();
										if($isSend){
											$data = array('code' => 1, 'msg' => '邮件发送成功，请稍候！');
										}else{
											$data = array('code' => 1007, 'msg' => '失败'.strip_tags ($lib_email->print_debugger()));
										}
									} catch (\Exception $e) {
										$data = array('code' => 1006, 'msg' => $e->getMessage());
									}
									
									//4.记录发送失败
									if($data['code']>1){
										$this->m_email_code->UpdateByID(array('status'=>0,'result'=>$data['msg']),$m['id']);
									}else{
										$this->m_email_code->UpdateByID(array('status'=>1,'result'=>$data['msg']),$m['id']);
									}
								}
								
						}else{
							$data = array('code' => 1002, 'msg' =>'邮箱不存在');
						}
				} else {
					$data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
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