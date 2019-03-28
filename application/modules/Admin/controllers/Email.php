<?php

/*
 * 功能：后台中心－邮箱设置
 * Author:资料空白
 * Date:20180509
 */

class EmailController extends AdminBasicController
{
    private $m_email;
	
	public function init()
    {
        parent::init();
		$this->m_email = $this->load('email');
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
		
		$where = array('isdelete'=>0);
		
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$total=$this->m_email->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$items=$this->m_email->Where($where)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
			
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
			$email = $this->m_email->SelectByID('',$id);
			$data['email'] = $email;
			$this->getView()->assign($data);
		}else{
            $this->redirect('/'.ADMIN_DIR."/email");
            return FALSE;
		}
    }
    public function addAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }

		$data = array();
		$this->getView()->assign($data);
    }	
	
	public function editajaxAction()
	{
		$method = $this->getPost('method',false);
		$id = $this->getPost('id',false);
		$mailaddress = $this->getPost('mailaddress',false);
		$mailpassword = $this->getPost('mailpassword',false);
		$sendmail = $this->getPost('sendmail',false);
		$sendname = $this->getPost('sendname',false);
		$host = $this->getPost('host',false);
		$port = $this->getPost('port',false);
		$isssl = $this->getPost('isssl');
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $mailaddress AND $mailpassword AND $sendmail AND $sendname AND $host AND $port AND is_numeric($isssl) AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$m = array(
					'mailaddress'=>$mailaddress,
					'mailpassword'=>$mailpassword,
					'sendmail'=>$sendmail,
					'sendname'=>$sendname,
					'host'=>$host,
					'port'=>$port,
					'isssl'=>$isssl
				);
				if($method == 'edit' AND $id>0){
					$isactive = $this->getPost('isactive');
					$m['isactive'] = $isactive;
					$u = $this->m_email->UpdateByID($m,$id);
					if($u){
						//更新缓存 
						$this->m_email->getConfig(1);
						$data = array('code' => 1, 'msg' => '更新成功');
					}else{
						$data = array('code' => 1003, 'msg' => '更新失败');
					}
				}else{
					$m['isactive'] = 1;
					$m['isdelete'] = 0;
					$id = $this->m_email->Insert($m);
					if($id>0){
						//更新缓存 
						$this->m_email->getConfig(1);
						$data = array('code' => 1, 'msg' => '新增成功');
					}else{
						$data = array('code' => 1003, 'msg' => '新增失败');
					}
				}
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}

    public function deleteAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$id = $this->get('id');
		$csrf_token = $this->getPost('csrf_token', false);
        if (FALSE != $id AND is_numeric($id) AND $id > 0) {
			if ($this->VerifyCsrfToken($csrf_token)) {
				$delete = $this->m_email->UpdateByID(array('isdelete'=>1),$id);
				if($delete){
					$data = array('code' => 1, 'msg' => '删除成功', 'data' => '');
				}else{
					$data = array('code' => 1003, 'msg' => '删除失败', 'data' => '');
				}
			} else {
                $data = array('code' => 1002, 'msg' => '页面超时，请刷新页面后重试!');
            }
        } else {
            $data = array('code' => 1001, 'msg' => '缺少字段', 'data' => '');
        }
       Helper::response($data);
    }	
}