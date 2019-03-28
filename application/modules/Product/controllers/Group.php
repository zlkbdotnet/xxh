<?php
/*
 * 功能：商品－分组
 * Author:资料空白
 * Date:20181101
 */

class GroupController extends PcBasicController
{
	private $m_products_type;
    public function init()
    {
        parent::init();
		$this->m_products_type = $this->load('products_type');
    }

    public function indexAction()
    {
		$tid = $this->get('tid');
		if($tid AND is_numeric($tid) AND $tid>0){
			$products_type = $this->m_products_type->Where(array('id'=>$tid,'active'=>1,'isdelete'=>0))->Order($order)->SelectOne();
			if(!empty($products_type)){
				$data = array();
				//如果是密码分类
				if(strlen($products_type['password'])>0){
					$tpl = "password";
					if(file_exists(APP_PATH.'/application/modules/Product/views/group/tpl/'.$tpl.'.html')){
						$data['products_type'] = $products_type;
						$data['title'] = $products_type['name']."_分类下的商品列表";
						$this->display("tpl_".$tpl, $data);
						return FALSE;
					}else{
						$this->redirect("/product/");
						return FALSE;	
					}
				}else{
				//否则
					$data['products_type'] = $products_type;
					$data['title'] = $products_type['name']."_分类下的商品列表";
					$this->getView()->assign($data);
				}
			}else{
				$this->redirect("/product/");
				return FALSE;	
			}
		}else{
			$this->redirect("/product/");
			return FALSE;
		}
    }
}