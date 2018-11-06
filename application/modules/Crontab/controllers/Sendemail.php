<?php

/*
 * 功能：计划任务-定时发送邮件
 * Author:资料空白
 * Date:20180508
 */
class SendemailController extends BasicController
{
	private $m_email;
    private $m_email_queue;

    public function init()
    {
        parent::init();
        $iscli = $this->getRequest()->isCli();
        if ($iscli) {
            Yaf\Dispatcher::getInstance()->disableView();
			$this->m_email = $this->load('email');
			$this->m_email_queue = $this->load('email_queue');
        } else {
            exit();
        }
    }

    public function indexAction()
    {
        file_put_contents(CRONTAB_FILE, CUR_DATETIME . '-' . 'start' . PHP_EOL, FILE_APPEND);
        $results = array();
        //1.先查询
        $queue = $this->m_email_queue->Where(array('status' => 0))->Limit(10)->Select();
        if (is_array($queue) AND !empty($queue)) {
			$emainConfig = $this->m_email->getConfig();
			$config=array();
			$config['smtp_host'] = 'ssl://' . $emainConfig['host'];
			$config['smtp_user'] = $emainConfig['mailaddress'];
			$config['smtp_pass'] = $emainConfig['mailpassword'];
			$config['smtp_port'] = $emainConfig['port'];
			$config['sendmail'] = $emainConfig['sendmail'];
			$config['sendname'] = $emainConfig['sendname'];
			foreach($queue AS $q){
				if(isEmail($q['email'])){
					$results[] = $this->_send($config,$q);
				}
				sleep(1);
			}
        }
        if (!empty($results)) {
            file_put_contents(CRONTAB_FILE, CUR_DATETIME . '-' . json_encode($results) . PHP_EOL, FILE_APPEND);
        }
        file_put_contents(CRONTAB_FILE, CUR_DATETIME . '-' . 'end' . PHP_EOL, FILE_APPEND);
        exit();
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
				$data = array('code' => 1007, 'msg' => '失败'. getRawText($lib_email->print_debugger()));
			}
		} catch (\Exception $e) {
			$data = array('code' => 1006, 'msg' => $e->getMessage());
		}
		//2.记录发送结果
		if($data['code']>1){
			$this->m_email_queue->UpdateByID(array('status'=>-1,'sendresult'=>$data['msg']),$params['id']);
		}else{
			$this->m_email_queue->UpdateByID(array('sendtime'=>time(),'status'=>1,'sendresult'=>$data['msg']),$params['id']);
		}
		return $data;
	}
}