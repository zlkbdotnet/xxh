<?php

/*
 * 功能：后台中心－商品管理
 * Author:资料空白
 * Date:20180509
 */

class ProductsController extends AdminBasicController
{
	private $m_products;
	private $m_products_type;
	private $m_products_card;
	
    public function init()
    {
        parent::init();
		$this->m_products = $this->load('products');
		$this->m_products_type = $this->load('products_type');
		$this->m_products_card = $this->load('products_card');
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
		
		$page = $this->get('page');
		$page = is_numeric($page) ? $page : 1;
		
		$limit = $this->get('limit');
		$limit = is_numeric($limit) ? $limit : 10;
		
		$where = array('isdelete'=>0);
		
		$total=$this->m_products->Where($where)->Total();
		
        if ($total > 0) {
            if ($page > 0 && $page < (ceil($total / $limit) + 1)) {
                $pagenum = ($page - 1) * $limit;
            } else {
                $pagenum = 0;
            }
			
            $limits = "{$pagenum},{$limit}";
			$sql = "SELECT p1.id,p1.name,p1.price,p1.qty,p1.auto,p1.active,p1.stockcontrol,p1.sort_num,p2.name as typename FROM `t_products` as p1 left join `t_products_type` as p2 on p1.typeid = p2.id WHERE p1.isdelete=0 Order by p1.id desc LIMIT {$limits}";
			$items=$this->m_products->Query($sql);
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
			$product=$this->m_products->SelectByID('',$id);
			$data['product'] = $product;
			
			$productstype=$this->m_products_type->Where(array('isdelete'=>0))->Order(array('sort_num'=>'DESC'))->Select();
			$data['productstype'] = $productstype;
			
			$this->getView()->assign($data);
		}else{
            $this->redirect('/'.ADMIN_DIR."/products");
            return FALSE;
		}
    }
	
	//图片管理
    public function imgurlAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect('/'.ADMIN_DIR."/login");
            return FALSE;
        }
		$id = $this->get('id');
		if($id AND $id>0){
			$data = array();
			$product=$this->m_products->SelectByID('',$id);
			$data['product'] = $product;
			$this->getView()->assign($data);
		}else{
            $this->redirect('/'.ADMIN_DIR."/products");
            return FALSE;
		}
    }
	
	public function imgurlajaxAction(){
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		if(is_array($_FILES) AND !empty($_FILES) AND isset($_FILES['file'])){
			if(isset($_FILES["file"]["error"]) AND $_FILES["file"]["error"]){
				$data = array('code' => 1000, 'msg' =>$_FILES["file"]["error"]);
				Helper::response($data); 
			}else{
				$pid = $this->getPost('pid');
				if(is_numeric($pid) AND $pid>0){
						try{
							$ext = pathinfo($_FILES['file']['name']);
							$ext = strtolower($ext['extension']);
							$tempFile = $_FILES['file']['tmp_name'];
							$targetPath  = UPLOAD_PATH.'/'.CUR_DATE;
							if( !is_dir($targetPath) ){
								mkdir($targetPath,0777,true);
							}
							$filename=date("His");
							$new_file_name = $filename.'.'.$ext;
							$targetFile = $targetPath .'/'. $new_file_name;
							move_uploaded_file($tempFile,$targetFile);
							if( !file_exists( $targetFile ) ){
								$data = array('code' => 1000, 'msg' => '上传失败');
							} elseif( !$imginfo=getimagesize($targetFile) ) {
								$data = array('code' => 1000, 'msg' => '上传失败,文件不存在 ');
							} else {
								if($imginfo[0]!=$imginfo[1]){
									//裁减图片
									if($imginfo[0]>$imginfo[1]){
										$w = $imginfo[1];
									}else{
										$w = $imginfo[0];
									}
									\Yaf\Loader::import(FUNC_PATH.'/F_Img.php');
									image_center_crop($targetFile, $w, $w, $targetFile);
								}
								
								$img = '/res/upload/'.CUR_DATE.'/'.$new_file_name;
								$data['code'] = 1;
								$data['img'] =$img ;
								//保存到数据库
								$m=array('imgurl'=>$img);
								$this->m_products->UpdateByID($m,$pid);
								$data = array('code' => 1, 'msg' => 'success');
							}
						}catch(\Exception $e) {
							$data = array('code' => 1002, 'msg' => $e->getMessage(),'data'=>array());
						}
				}else{
					$data = array('code' => 1001, 'msg' => '请选择商品','data'=>array());
				}
			}
		}else{
			$data = array('code' => 1000, 'msg' => '上传内容为空,请重新上传','data'=>array());
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
		$productstype=$this->m_products_type->Where(array('isdelete'=>0))->Order(array('sort_num'=>'DESC'))->Select();
		$data['productstype'] = $productstype;
		$this->getView()->assign($data);
    }
	public function editajaxAction()
	{
		$method = $this->getPost('method',false);
		$id = $this->getPost('id',false);
		$typeid = $this->getPost('typeid',false);
		$name = $this->getPost('name',false);
		$password = $this->getPost('password',false);
		$description = $this->getPost('description',false);
		$stockcontrol = $this->getPost('stockcontrol',false);
		$qty = $this->getPost('qty',false);
		$price = $this->getPost('price',false);
		$auto = $this->getPost('auto',false);
		$addons = $this->getPost('addons',false);
		$active = $this->getPost('active',false);
		$sort_num = $this->getPost('sort_num',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($method AND $typeid AND $name AND $description AND is_numeric($stockcontrol) AND is_numeric($qty) AND is_numeric($price) AND is_numeric($auto) AND is_numeric($active) AND is_numeric($sort_num) AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				if($price<0.01){
					$data = array('code' => 1000, 'msg' => '价格设置错误');
					Helper::response($data);
				}
				
				$description = str_replace(array("\r","\n","\t"), "", $description);
				$m = array(
					'typeid'=>$typeid,
					'name'=>$name,
					'password'=>$password,
					'description'=>htmlspecialchars($description),
					'stockcontrol'=>$stockcontrol,
					'qty'=>$qty,
					'price'=>$price,
					'auto'=>$auto,
					'addons'=>$addons,
					'active'=>$active,
					'sort_num'=>$sort_num,
				);
				if($method == 'edit' AND $id>0){
					//修正库存问题,如果不控制库存，库存默认为０
					if($stockcontrol<1){
						$m['qty'] = 0;
					}else{
						//修正库存问题,在更新商品时,如果是自动发货商品,库存不能修改
						if($auto>0){
							unset($m['qty']);
						}
					}
					$u = $this->m_products->UpdateByID($m,$id);
					if($u){
						$data = array('code' => 1, 'msg' => '更新成功');
					}else{
						$data = array('code' => 1003, 'msg' => '更新失败');
					}
				}elseif($method == 'add'){
					//修正库存问题,在添加新商品时,如果是自动发货商品,库存默认为0
					if($auto>0 OR $stockcontrol<1){
						$m['qty'] = 0;
					}
					$m['addtime'] = time();
					$m['imgurl'] = "";
					$u = $this->m_products->Insert($m);
					if($u){
						$data = array('code' => 1, 'msg' => '新增成功');
					}else{
						$data = array('code' => 1003, 'msg' => '新增失败');
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
	
	public function updateqtyajaxAction()
	{
		$pid = $this->getPost('pid',false);
		$csrf_token = $this->getPost('csrf_token', false);
		
		$data = array();
		
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		
		if($pid AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				//修正库存问题,在添加新商品时,如果是自动发货商品,库存默认为0
				$qty = $this->m_products_card->Where(array('pid'=>$pid,'active'=>0,'isdelete'=>0))->Total();
				$qty_m = array('qty' => $qty);
				$u = $this->m_products->Where(array('id'=>$pid,'auto'=>1,'stockcontrol'=>1))->Update($qty_m);
				if($u){
					$data = array('code' => 1, 'msg' => '成功');
				}else{
					$data = array('code' => 1003, 'msg' => '失败');
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
				//检查是否存在可用的卡密
				$qty = $this->m_products_card->Where(array('pid'=>$id,'active'=>0,'isdelete'=>0))->Total();
				if($qty>0){
					$data = array('code' => 1004, 'msg' => '存在可用卡密，请先导出并删除卡密', 'data' => '');
				}else{
					$where = 'active=0';//只有未激活的才可以删除
					$delete = $this->m_products->Where($where)->UpdateByID(array('isdelete'=>1),$id);
					if($delete){
						$data = array('code' => 1, 'msg' => '删除成功', 'data' => '');
					}else{
						$data = array('code' => 1003, 'msg' => '删除失败', 'data' => '');
					}
				}
			} else {
                $data = array('code' => 1002, 'msg' => '页面超时，请刷新页面后重试!');
            }
        } else {
            $data = array('code' => 1001, 'msg' => '缺少字段', 'data' => '');
        }
       Helper::response($data);
    }
	
    public function getlistbytidAction()
    {
		$tid = $this->getPost('tid');
		$csrf_token = $this->getPost('csrf_token', false);
		
		if($tid AND $csrf_token){
			if ($this->VerifyCsrfToken($csrf_token)) {
				$data = array();
				$order = array('sort_num' => 'DESC');
				$field = array('id', 'name');
				$products = $this->m_products->Field($field)->Where(array('typeid'=>$tid,'active'=>1,'isdelete'=>0))->Order($order)->Select();
				$data['products'] = $products;
				$result = array('code' => 1, 'msg' => 'success','data'=>$data);
			} else {
                $result = array('code' => 1001, 'msg' => '页面超时，请刷新页面后重试!');
            }
		}else{
			$result = array('code' => 1000, 'msg' => '参数错误');
		}
        Helper::response($result);
    }
	
}