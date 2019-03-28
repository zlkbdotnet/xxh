<?php

/*
 * 功能：计发送邮件
 * Author:资料空白
 * Date:20180508
 */
class Sendemail
{
	private $m_email;
    private $m_email_queue;

    public function __construct()
    {
		$this->m_email = \Helper::load('email');
		$this->m_email_queue = \Helper::load('email_queue');
    }

    public function send($params)
    {
		$emainConfig = $this->m_email->getConfig();
		$config = array();
		if($emainConfig['isssl']>0){
			$config['smtp_host'] = 'ssl://' . $emainConfig['host'];
		}else{
			$config['smtp_host'] = $emainConfig['host'];
		}
		$config['smtp_user'] = $emainConfig['mailaddress'];
		$config['smtp_pass'] = $emainConfig['mailpassword'];
		$config['smtp_port'] = $emainConfig['port'];
		$config['sendmail'] = $emainConfig['sendmail'];
		$config['sendname'] = $emainConfig['sendname'];
		foreach($params AS $q){
			if(isEmail($q['email'])){
				$results[] = $this->_send($config,$q);
			}
			sleep(1);
		}
		return true;
    }
	
	
	private function _send($config,$params)
	{
		//发送邮件
		try {
			$lib_email = new Email($config);
			$lib_email->from($config['sendmail'], $config['sendname']);
			$lib_email->to($params['email']);
			$lib_email->subject($params['subject']);
			$lib_email->message($params['content']);
			$isSend = $lib_email->send();
			if($isSend){
				$data = array('code' => 1, 'msg' => '邮件发送成功，请稍候！');
			}else{
				$data = array('code' => 1007, 'msg' => '失败'.getRawText($lib_email->print_debugger()));
			}
		} catch (\Exception $e) {
			$data = array('code' => 1006, 'msg' => $e->getMessage());
		}
		//2.记录发送结果
		if($data['code']>1){
			$this->m_email_queue->Where($params)->Update(array('status'=>2,'sendresult'=>$data['msg']));
		}else{
			$this->m_email_queue->Where($params)->Update(array('status'=>1,'sendresult'=>$data['msg'],'sendtime'=>time()));
		}
		return $data;
	}
}