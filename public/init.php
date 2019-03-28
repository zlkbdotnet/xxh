<?php
if(defined('APP_PATH')) {
	//增加环境判断,争取在安装过程中给小白们更多提示
	//1.判断application.ini.new是否修改为application.ini
	if(!file_exists(APP_PATH.'/conf/application.ini')){
		echo "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>警告：</strong><br>请按照安装要求将application.ini.new修改为 application.ini</div>";
		exit();
	}
	//2.判断是否安装yaf扩展
	if (!extension_loaded('yaf')){
		echo "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>警告：</strong><br>请按照安装要求安装YAF扩展</div>";
		exit();	
	}
	//3.获取yaf扩展设置
	if(ini_get('yaf.use_namespace')!="1"){
		echo "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;width:100%;padding:10px;color:#cc0000;\"><strong>警告：</strong><br>请按照安装要求配置yaf.use_namespace=1</div>";
		exit();	
	}
}