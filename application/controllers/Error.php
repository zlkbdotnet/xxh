<?php
/*
 * 功能：异常处理
 * Author:资料空白
 * Date:20180604
 */
class ErrorController extends IndexBasicController
{

    public function errorAction($exception)
	{
		$msg=$exception->getMessage();
		$ip=getClientIP();
		$time=date('Y-m-d H:i:s');
		if($ip){
			file_put_contents(REQUEST_FILE, $time.'-'.$ip.'-'.$msg.PHP_EOL, FILE_APPEND);	
			$data['title']='系统异常';
			$this->getView()->assign($data);
		}else{
			file_put_contents(LOG_FILE, $time.'-'.$msg.PHP_EOL, FILE_APPEND);
			exit();
		}
    }
}