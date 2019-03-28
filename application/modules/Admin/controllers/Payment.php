<?php

/*
 * 功能：后台中心－支付设置
 * Author:资料空白
 * Date:20180509
 */

class PaymentController extends AdminBasicController
{
	private $m_payment;
    public function init()
    {
        parent::init();
		$this->m_payment = $this->load('payment');
    }

    public function indexAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }

		$data = array();
		$this->getView()->assign($data);
    }

	//ajax
	public function ajaxAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		$where = array();
		
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$total=$this->m_payment->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$items=$this->m_payment->Field(array('id','payment','alias','app_id','active'))->Where($where)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
			
            if (empty($items)) {
                $data = array('code'=>1002,'count'=>0,'data'=>array(),'msg'=>'无数据');
            } else {
                $data = array('code'=>0,'count'=>$total,'data'=>$items,'msg'=>'有数据');
            }
        } else {
            $data = array('code'=>1001,'count'=>0,'data'=>array(),'msg'=>'无数据');
        }
		Helper::response($data);
	}
	
    public function editAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$id = $this->get('id');
		if($id AND $id>0){
			$data = array();
			$item=$this->m_payment->SelectByID('',$id);
			$data['item'] =$item;
			if($item['alias'] AND file_exists(APP_PATH.'/application/modules/'.ADMIN_DIR.'/views/payment/tpl/'.$item['alias'].'.html')){
				$tpl = 'tpl_'.$item['alias'];
				$this->display($tpl, $data);
				return FALSE;
			}else{
				$this->getView()->assign($data);
			}
		}else{
            $this->redirect('/'.ADMIN_DIR."/payment");
            return FALSE;
		}
    }
	public function editajaxAction()
	{
		$method = $this->getPost('method',false);
		$id = $this->getPost('id',false);
		$payname = $this->getPost('payname',false);
		$payimage = $this->getPost('payimage',false);
		$sign_type = $this->getPost('sign_type',false);
		$app_id = $this->getPost('app_id',false);
		$app_secret = $this->getPost('app_secret',false);
		$ali_public_key = $this->getPost('ali_public_key',false);
		$rsa_private_key = $this->getPost('rsa_private_key',false);
		$configure3 = $this->getPost('configure3',false);
		$configure4 = $this->getPost('configure4',false);
		$active = $this->getPost('active',false);
		$overtime = $this->getPost('overtime',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		
		if($method AND $payname AND is_numeric($active) AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$m=array(
					'payname'=>$payname,
					'app_id'=>$app_id,
					'active'=>$active,
				);
				
				if(isset($payimage) AND strlen($payimage)>0){
					$m['payimage']=$payimage;
				}
				
				$sign=array('RSA','RSA2','MD5','HMAC-SHA256');
				if(isset($sign_type) AND strlen($sign_type)>0 AND in_array($sign_type,$sign)){
					$m['sign_type']=$sign_type;
				}
				if(isset($app_secret) AND strlen($app_secret)>0){
					$m['app_secret']=$app_secret;
				}
				if(isset($ali_public_key) AND strlen($ali_public_key)>0){
					$m['ali_public_key']=$ali_public_key;
				}
				if(isset($rsa_private_key) AND strlen($rsa_private_key)>0){
					$m['rsa_private_key']=$rsa_private_key;
				}
				if(isset($configure3) AND strlen($configure3)>0){
					$m['configure3']=$configure3;
				}
				if(isset($configure4) AND strlen($configure4)>0){
					$m['configure4']=$configure4;
				}
				if(isset($overtime) AND is_numeric($overtime)){
					$m['overtime']=$overtime;
				}
				if($method == 'edit' AND $id>0){
					$u = $this->m_payment->UpdateByID($m,$id);
					if($u){
						//更新缓存 
						$this->m_payment->getConfig(1);
						$data = array('code' => 1, 'msg' => '更新成功');
					}else{
						$data = array('code' => 1003, 'msg' => '更新失败');
					}
				}else{
					$data = array('code' => 1002, 'msg' => '未知方法');
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