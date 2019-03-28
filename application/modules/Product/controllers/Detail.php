<?php

/*
 * 功能：商品模块－商品独立页
 * Author:资料空白
 * Date:20180707
 */

class DetailController extends PcBasicController
{
	private $m_products;
	private $m_products_pifa;
    public function init()
    {
        parent::init();
		$this->m_products = $this->load('products');
		$this->m_products_pifa = $this->load('products_pifa');
    }

    public function indexAction()
    {
		$pid = $this->get('pid');
		if($pid AND is_numeric($pid) AND $pid>0){
			$product = $this->m_products->Where(array('id'=>$pid,'active'=>1,'isdelete'=>0))->SelectOne();
			if(!empty($product)){
				$data = array();
				//先拿折扣
				$data['pifa'] = "";
				if($this->config['discountswitch']){
					$pifa = $this->m_products_pifa->getPifa($pid);
					if(!empty($pifa)){
						$data['pifa'] = json_encode($pifa);
					}
				}
				//再拿附加
				if($product['addons']){
					$addons = explode(',',$product['addons']);
					$data['addons'] = $addons;
				}else{
					$data['addons'] = array();
				}
				
				//如果是密码商品
				if(strlen($product['password'])>0){
					if($this->config['tplproduct']=="default"){
						$tpl = "password";
					}else{
						$tpl = $this->config['tplproduct']."password";
					}
					
					if(file_exists(APP_PATH.'/application/modules/Product/views/detail/tpl/'.$tpl.'.html')){
						$data['product'] = $product;
						$data['title'] = $product['name']."_购买商品";
						if($this->config['tplproduct']=="default"){
							$this->display("tpl_".$tpl, $data);
							return FALSE;
						}else{
							$this->display("tpl_".$tpl, $data);
							return FALSE;
						}
					}else{
						$this->show_message('error','丢失模版','/product/');
						return FALSE; 
					}
				}else{
				//否则
					$data['product'] = $product;
					$data['title'] = $product['name']."_购买商品";
					if($this->config['tplproduct']=="default"){
						$this->getView()->assign($data);
					}else{
						$this->display("tpl_".$this->config['tplproduct'], $data);
						return FALSE;
					}
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