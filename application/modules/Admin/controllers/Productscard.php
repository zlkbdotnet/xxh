<?php

/*
 * 功能：后台中心－卡密管理
 * Author:资料空白
 * Date:20180509
 */

class ProductscardController extends AdminBasicController
{
	private $m_products_card;
	private $m_products_type;
	private $m_products;
    public function init()
    {
        parent::init();
		$this->m_products_card = $this->load('products_card');
		$this->m_products = $this->load('products');
		$this->m_products_type = $this->load('products_type');
    }

    public function indexAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }

		$data = array();
		$products = $this->m_products->Where(array('isdelete'=>0))->Order(array('sort_num'=>'DESC'))->Select();
		$data['products'] = $products;
		$this->getView()->assign($data);
    }

	//ajax
	public function ajaxAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		$card = $this->get('card',false);
		$active = $this->get('active');
		$pid = $this->get('pid');
        //查询条件
        $get_params = [
            'card' => $card,
			'active' => $active,
			'pid' => $pid,
        ];   
        $where = $this->conditionSQL($get_params);
		$where1 = $this->conditionSQL($get_params,'p1.');
		
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$total=$this->m_products_card->Where(array('isdelete'=>0))->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$sql ="SELECT p1.*,p2.name FROM `t_products_card` as p1 left join `t_products` as p2 on p1.pid=p2.id Where p1.isdelete=0 and {$where1} Order by p1.id desc LIMIT {$limits}";
			$items=$this->m_products_card->Query($sql);
			
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
	
    public function addAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$data = array();
		
		$order = array('sort_num' => 'DESC');
		$products_type = $this->m_products_type->Where(array('active'=>1,'isdelete'=>0))->Order($order)->Select();
		$data['products_type'] = $products_type;
		
		$this->getView()->assign($data);
    }

    public function addplusAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$data = array();
		
		$order = array('sort_num' => 'DESC');
		$products_type = $this->m_products_type->Where(array('active'=>1,'isdelete'=>0))->Order($order)->Select();
		$data['products_type'] = $products_type;
		
		$this->getView()->assign($data);
    }	
	
	public function addajaxAction()
	{
		$method = $this->getPost('method',false);
		$pid = $this->getPost('pid',false);
		$card = $this->getPost('card',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $pid AND $card AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if($method == 'add'){
					$card = getRawText($card,false);
					$m=array(
						'pid'=>$pid,
						'card'=>$card,
						'addtime'=>time(),
					);
					$u = $this->m_products_card->Insert($m);
					if($u){
						//新增商品数量
						$qty_m = array('qty' => 'qty+1');
						$this->m_products->Where(array('id'=>$pid,'stockcontrol'=>1))->Update($qty_m,TRUE);
						$data = array('code' => 1, 'msg' => '新增成功');
					}else{
						$data = array('code' => 1003, 'msg' => '新增失败');
					}
				}elseif($method == 'addplus'){
					//开始处理
					$m = array();
					$huiche=array("\n","\r");
					$replace='\r\n';
					$newTxtFileData=str_replace($huiche,$replace,$card); 
					$newTxtFileData_array = explode($replace,$newTxtFileData);
					foreach($newTxtFileData_array AS $line){
						if(strlen($line)>0){
							$line = getRawText($line,false);
							$m[]=array('pid'=>$pid,'card'=>$line,'addtime'=>time());
						}
					}
					if(!empty($m)){
						$u = $this->m_products_card->MultiInsert($m);
						if($u){
							//增加商品数量
							$addNum = count($m);
							$qty_m = array('qty' => 'qty+'.$addNum);
							$this->m_products->Where(array('id'=>$pid,'stockcontrol'=>1))->Update($qty_m,TRUE);
							$data = array('code' => 1, 'msg' => '成功');
						}else{
							$data = array('code' => 1004, 'msg' => '失败');
						}
					}else{
						$data = array('code' => 1003, 'msg' => '没有卡密存在','data'=>array());
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

	public function deleteAction()
	{
		$id = $this->get('id',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if($id AND is_numeric($id) AND $id>0){
					$delete = $this->m_products_card->UpdateByID(array('isdelete'=>1),$id);
					if($delete){
						//减少商品数量
						$cards = $this->m_products_card->SelectByID('pid',$id);
						$qty_m = array('qty' => 'qty-1');
						$this->m_products->Where(array('id'=>$cards['pid'],'stockcontrol'=>1))->Update($qty_m,TRUE);
						$data = array('code' => 1, 'msg' => '成功');
					}else{
						$data = array('code' => 1003, 'msg' => '删除失败');
					}
				}else{
					$ids = json_decode($id,true);
					if(isset($ids['ids']) AND !empty($ids['ids'])){
						$idss = implode(",",$ids['ids']);
						$where = "id in ({$idss})";
						$delete = $this->m_products_card->Where($where)->Update(array('isdelete'=>1));
						if($delete){
							foreach($ids['ids'] AS $idd){
								//减少商品数量
								$cards = $this->m_products_card->SelectByID('pid',$idd);
								$qty_m = array('qty' => 'qty-1');
								$this->m_products->Where(array('id'=>$cards['pid'],'stockcontrol'=>1))->Update($qty_m,TRUE);
							}
							$data = array('code' => 1, 'msg' => '成功');
						}else{
							$data = array('code' => 1003, 'msg' => '删除失败');
						}
					}else{
						$data = array('code' => 1000, 'msg' => '请选中需要删除的卡密');
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
	
	public function deleteemptyAction()
	{
		$method = $this->get('method',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if($method =="empty"){
					$this->m_products_card->Query("DELETE FROM `t_products_card` WHERE `isdelete` = 1");
					 $data = array('code' => 1, 'msg' => '清空已删除卡密');
				}else{
					 $data = array('code' => 1002, 'msg' => '方法错误');
				}
			} else {
                $data = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
    public function importAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$data = array();
		$order = array('sort_num' => 'DESC');
		$products_type = $this->m_products_type->Where(array('active'=>1,'isdelete'=>0))->Order($order)->Select();
		$data['products_type'] = $products_type;
		$this->getView()->assign($data);
    }
	
	public function importajaxAction(){
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		if(is_array($_FILES) AND !empty($_FILES) AND isset($_FILES['file'])){
			$pid = $this->getPost('pid');
			$csrf_token = $this->getPost('csrf_token', false);
			if(is_numeric($pid) AND $pid>0){
				if ($csrf_token AND $this->VerifyCsrfToken($csrf_token)) {
					try{
						$m = array();
						//读取文件
						$txtfile = $_FILES['file']['tmp_name'];
						$txtFileData = file_get_contents($txtfile);
						//处理编码问题
						$encoding = mb_detect_encoding($txtFileData, array('GB2312','GBK','UTF-16','UCS-2','UTF-8','BIG5','ASCII'));
						if($encoding != false){
							if($encoding=='CP936'){
								
							}else{
								$txtFileData = iconv($encoding, 'UTF-8', $txtFileData);
							}
						}else{
							$txtFileData = mb_convert_encoding ( $txtFileData, 'UTF-8','Unicode');
						}
						//开始处理
						$huiche=array("\n","\r");
						$replace='\r\n';
						$newTxtFileData=str_replace($huiche,$replace,$txtFileData); 
						$newTxtFileData_array = explode($replace,$newTxtFileData);
						foreach($newTxtFileData_array AS $line){
							if(strlen($line)>0){
								$line = getRawText($line,false);
								if(strlen($line)>0){
									$m[]=array('pid'=>$pid,'card'=>$line,'addtime'=>time());
								}
							}
						}
						if(!empty($m)){
							$u = $this->m_products_card->MultiInsert($m);
							if($u){
								//增加商品数量
								$addNum = count($m);
								$qty_m = array('qty' => 'qty+'.$addNum);
								$this->m_products->Where(array('id'=>$pid,'stockcontrol'=>1))->Update($qty_m,TRUE);
								$data = array('code' => 1, 'msg' => '成功');
							}else{
								$data = array('code' => 1004, 'msg' => '失败');
							}
						}else{
							$data = array('code' => 1003, 'msg' => '没有卡密存在','data'=>array());
						}
					}catch(\Exception $e) {
						$data = array('code' => 1002, 'msg' => $e->getMessage(),'data'=>array());
					}
				}else{
					$data = array('code' => 1005, 'msg' => '页面超时，请刷新页面后重试!','data'=>array());
				}
			}else{
				$data = array('code' => 1001, 'msg' => '请选择商品','data'=>array());
			}
		}else{
			$data = array('code' => 1000, 'msg' => '上传内容为空,请重新上传','data'=>array());
		}
		Helper::response($data);
	}
	
    public function downloadAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$data = array();
		$products=$this->m_products->Where(array('auto'=>1,'isdelete'=>0))->Order(array('id'=>'DESC'))->Select();
		$data['products'] = $products;
		$this->getView()->assign($data);
    }
	
	public function downloadajaxAction(){
		$pid = $this->getPost('pid');
		$csrf_token = $this->getPost('csrf_token', false);
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		
		if ($this->VerifyCsrfToken($csrf_token)) {
			if(is_numeric($pid) AND $pid>0){
				try{
					$active = $this->getPost('active');
					$get_params = [
						'active' => $active,
						'pid' => $pid,
					];  
					$where  = $this->conditionSQL($get_params);
					$cards = $this->m_products_card->Where(array('isdelete'=>0))->Where($where)->Select();
					if(!empty($cards)){
						$content = '';
						foreach($cards AS $card){
							$content .= $card['card']."\r\n";
						}
						$data = array('code' => 1, 'msg' => 'success','data'=>$content);
					}else{
						$data = array('code' => 1002, 'msg' => '没有卡密存在','data'=>array());
					}
				}catch(\Exception $e) {
					$data = array('code' => 1002, 'msg' => $e->getMessage(),'data'=>array());
				}
			}else{
				$data = array('code' => 1001, 'msg' => '请选择商品','data'=>array());
			}
		}else{
			$data = array('code' => 1003, 'msg' => '页面超时，请刷新页面后重试!','data'=>array());
		}
		
		$filename = '卡密下载_'.date("YmdHis").'.txt';
		if($data['code']>1){
			$content = '下载失败,失败原因：'.$data['msg'];
		}else{
			$content = $data['data'];
		}
		header("Content-Type:application/force-download");
		header("Accept-Ranges:bytes");
		header("Content-Disposition:attachment;filename=".$filename);
		header("Expires: 0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma:public");
		echo $content;
		exit();
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
				$field = array('id','card');
				$items = $this->m_products_card->Field($field)->Order(array('id'=>'DESC'))->Select();
				if (empty($items)) {
					$data = array('code' => 1004, 'msg' => '无数据，不需要修复');
				} else {
					foreach($items AS $item){
						$card = getRawText($item['card'],false);
						$m = array('card'=>$card);
						$this->m_products_card->UpdateByID($m,$item['id']);
						unset($card,$m);
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
    private function conditionSQL($param,$alias='')
    {
        $condition = "1";
        if (isset($param['card']) AND empty($param['card']) === FALSE) {
            $condition .= " AND {$alias}`card` LIKE '%{$param['card']}%'";
        }
        if (isset($param['active']) AND $param['active']>-1 ) {
            $condition .= " AND {$alias}`active` = {$param['active']}";
        }
        if (isset($param['pid']) AND empty($param['pid']) === FALSE AND $param['pid']>0 ) {
            $condition .= " AND {$alias}`pid` = {$param['pid']}";
        }		
        return ltrim($condition, " AND ");
    }
}