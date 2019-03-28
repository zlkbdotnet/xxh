<?php

/*
 * 功能：后台中心－统计报表
 * Author:资料空白
 * Date:20180509
 */

class ReportController extends AdminBasicController
{
    private $m_order;
	
	public function init()
    {
        parent::init();
		$this->m_order = $this->load('order');
    }

    public function indexAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$data = array();
		$data['title'] = "统计报表";
		
		//当日统计
		$today_report = array();
		$starttime = strtotime(date("Y-m-d"));
		$endtime = strtotime(date("Y-m-d 23:59:59"));
		$sql ="SELECT count(*) AS total,sum(money) AS shouru FROM `t_order` Where isdelete=0 AND status>0 AND addtime>={$starttime} AND addtime<={$endtime}";
		$total_result = $this->m_order->Query($sql);
		if(is_array($total_result) AND !empty($total_result)){
			$today_report['total'] = $total_result[0]['total'];
			$today_report['money'] = number_format($total_result[0]['shouru'],2,".",".");
		}else{
			$today_report['total'] = 0;
			$today_report['money'] = 0.00;
		}
		$data['today_report'] = $today_report;
		//本周统计
		$week_report = array();
		$starttime = mktime(0,0,0,date('m'),date('d')-date('w')+1,date('y')); 
		$endtime = strtotime(date("Y-m-d 23:59:59"));
		$sql ="SELECT count(*) AS total,sum(money) AS shouru FROM `t_order` Where isdelete=0 AND status>0 AND addtime>={$starttime} AND addtime<={$endtime}";
		$total_result = $this->m_order->Query($sql);
		if(is_array($total_result) AND !empty($total_result)){
			$week_report['total'] = $total_result[0]['total'];
			$week_report['money'] = number_format($total_result[0]['shouru'],2,".",".");
		}else{
			$week_report['total'] = 0;
			$week_report['money'] = 0.00;
		}
		$data['week_report'] = $week_report;
		//当月统计
		$month_report = array();
		$firstday = date('Y-m-01', strtotime(date("Y-m-d")));
		$lastday = date('Y-m-d', strtotime("{$firstday} +1 month -1 day"));
		$firstday = strtotime($firstday);
		$lastday = strtotime($lastday);
		
		$sql ="SELECT count(*) AS total,sum(money) AS shouru FROM `t_order` Where isdelete=0 AND status>0 AND addtime>={$firstday} AND addtime<={$lastday}";
		$total_result = $this->m_order->Query($sql);
		if(is_array($total_result) AND !empty($total_result)){
			$month_report['total'] = $total_result[0]['total'];
			$month_report['money'] = number_format($total_result[0]['shouru'],2,".",".");
		}else{
			$month_report['total'] = 0;
			$month_report['money'] = 0.00;
		}
		$data['month_report'] = $month_report;
		//总计
		$total_report = array();
		$sql ="SELECT count(*) AS total,sum(money) AS shouru FROM `t_order` Where isdelete=0 AND status>0";
		$total_result = $this->m_order->Query($sql);
		if(is_array($total_result) AND !empty($total_result)){
			$total_report['total'] = $total_result[0]['total'];
			$total_report['money'] = number_format($total_result[0]['shouru'],2,".",".");
		}else{
			$total_report['total'] = 0;
			$total_report['money'] = 0.00;
		}
		$data['total_report'] = $total_report;
		
        $this->getView()->assign($data);
    }


}