<?php

/*
 * 功能：商品模块－商品独立页
 * Author:资料空白
 * Date:20180707
 */

class DetailController extends PcBasicController
{
	private $m_products;
    public function init()
    {
        parent::init();
		$this->m_products = $this->load('products');
    }

    public function indexAction()
    {
		$pid = $this->get('pid');
		if(is_numeric($pid) AND $pid>0){
			$product = $this->m_products->Where(array('id'=>$pid,'active'=>1,'isdelete'=>0))->SelectOne();
			if(!empty($product)){
				$data['product'] = $product;
				
				if($product['addons']){
					$addons = explode(',',$product['addons']);
					$data['addons'] = $addons;
				}else{
					$data['addons'] = array();
				}
				
				$data['title'] = $product['name']."_购买商品";
				$this->getView()->assign($data);
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