<?php

/*
 * 功能：后台中心－首页
 * Author:资料空白
 * Date:20180509
 */

class SettingController extends AdminBasicController
{
	private $m_config;
    public function init()
    {
        parent::init();
		$this->m_config = $this->load('config');
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
		
		$total=$this->m_config->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$field = array('id','name','updatetime','tag');
			$items=$this->m_config->Field($field)->Where($where)->Limit($limits)->Order(array('id'=>'DESC'))->Select();
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
			$item=$this->m_config->SelectByID('',$id);
			$data['item'] = $item;
			if($item['name'] AND file_exists(APP_PATH.'/application/modules/'.ADMIN_DIR.'/views/setting/tpl/'.$item['name'].'.html')){
				$tpl = 'tpl_'.$item['name'];
				$this->display($tpl, $data);
				return FALSE;
			}else{
				$this->getView()->assign($data);
			}
		}else{
            $this->redirect('/'.ADMIN_DIR."/setting");
            return FALSE;
		}
    }
	public function editajaxAction()
	{
		$method = $this->getPost('method',false);
		$id = $this->getPost('id',false);
		$name = $this->getPost('name',false);
		$value = $this->getPost('value',false);
		$tag = $this->getPost('tag',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $name AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$tag = getRawText($tag,false);
				if($value OR is_numeric($value)){
					$m=array(
						'name'=>$name,
						'value'=>htmlspecialchars($value),
						'tag'=>htmlspecialchars($tag),
					);
				}else{
					$m=array(
						'name'=>$name,
						'value'=>"",
						'tag'=>htmlspecialchars($tag),
					);
				}

				if($method == 'edit' AND $id>0){
					$u = $this->m_config->UpdateByID($m,$id);
					if($u){
						//更新缓存 
						$this->m_config->getConfig(1);
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
	
	public function repairajaxAction()
	{
		$method = $this->getPost('method',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$field = array('id','name','updatetime','tag');
				$items = $this->m_config->Field($field)->Order(array('id'=>'DESC'))->Select();
				if (empty($items)) {
					$data = array('code' => 1004, 'msg' => '无数据，不需要修复');
				} else {
					foreach($items AS $item){
						$tag = getRawText($item['tag'],false);
						$m = array('tag'=>htmlspecialchars($tag));
						$this->m_config->UpdateByID($m,$item['id']);
						unset($tag,$m);
					}
					$data = array('code' => 1, 'msg' => '修复完成');
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