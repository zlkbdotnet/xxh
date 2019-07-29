<?php
/*
 * 功能：产品模块－默认首页
 * Author:资料空白
 * Date:20180509
 */

class IndexController extends ProductBasicController
{
	private $m_products_type;
	private $m_products;
    public function init()
    {
        parent::init();
		$this->m_products_type = $this->load('products_type');
		$this->m_products = $this->load('products');
    }

    public function indexAction()
    {
		if(file_exists(INSTALL_LOCK)){
			if($this->config['productswitch']>0){
				$data = array();
				//获取原始分类
				$products_type = $this->m_products_type->Where(array('active'=>1,'isdelete'=>0))->Order(array('sort_num' => 'DESC'))->Select();
				$data['products_type'] = $products_type;
				//获取有效的商品与有效的商品分类
				$products_type_active = array();
				$sql = "SELECT p1.*,p2.id AS products_type_id,p2.name AS products_type_name FROM `t_products` as p1 left join t_products_type as p2 on p1.typeid =p2.id where p1.active=1 and p1.isdelete=0 and p2.active=1 and p2.isdelete=0 order by p2.sort_num DESC, p1.sort_num DESC";
				$items = $this->m_products->Query($sql);
				if (empty($items)) {
					$data['products'] = array();
					$data['products_type_active'] = array();
				} else {
					//对密码与库存做特别处理
					foreach($items AS $k=>$p){
						$products_type_active[$p['products_type_id']] = array('id'=>$p['products_type_id'],'name'=>$p['products_type_name']);
						if(isset($p['password']) AND strlen($p['password'])>0){
							$items[$k]['password'] = "hidden";
						}
						if($p['qty_switch']>0){
							$items[$k]['qty'] = $p['qty_virtual'];
						}
					}
					$data['products_type_active'] = $products_type_active;
					$data['products'] = $items;
				}
				
				$data['title'] = "购买商品";
				if($this->tpl){
					$this->display($this->tpl, $data);
					return FALSE;
				}else{
					$this->getView()->assign($data);
				}
			}else{
				$this->show_message('error','产品页已关闭','/');
				return FALSE; 
			}
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
    }
}