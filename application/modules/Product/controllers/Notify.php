<?php
/*
 * 功能：产品中心-支付回调(异步处理)
 * Author:资料空白
 * Date:20180509
 */
class NotifyController extends PcBasicController
{
	private $m_payment;
    public function init()
    {
        parent::init();
		$this->m_payment = $this->load('payment');
    }

    public function indexAction()
    {
		$paymethod = $this->get('paymethod');
		$payments = $this->m_payment->getConfig();
		if(isset($payments[$paymethod]) AND !empty($payments[$paymethod])){
			try {
				$payconfig = $payments[$paymethod];
				$payclass = "\\Pay\\".$paymethod."\\".$paymethod;
				$PAY = new $payclass();
				echo $result = $PAY->notify($payconfig);
				file_put_contents(YEWU_FILE, CUR_DATETIME.'-result:'.$result.PHP_EOL, FILE_APPEND);
				exit();
			} catch (\Exception $e) {
				file_put_contents(YEWU_FILE, CUR_DATETIME.'-result:'.$e->getMessage().PHP_EOL, FILE_APPEND);
				echo 'error|Exception:'.$e->getMessage();exit();
			}
		}else{
			file_put_contents(YEWU_FILE, CUR_DATETIME.'-Paymethod is null'.PHP_EOL, FILE_APPEND);
			echo 'error|Paymethod is null';exit();
		}
	}
}