/**

 @Name: ZFAKA平台框架

 */

layui.define(['layer', 'laytpl', 'form', 'element','table'], function(exports){

  var $ = layui.jquery
  ,layer = layui.layer
  ,laytpl = layui.laytpl
  ,form = layui.form
  ,element = layui.element
  ,upload = layui.upload
  ,table = layui.table
  ,device = layui.device()
  ,DISABLED = 'layui-btn-disabled';

	//阻止IE7以下访问
	if(device.ie && device.ie < 8){
		layer.alert('如果您非得使用 IE 浏览器访问本站点，那么请使用 IE8+');
	}
	
	layui.focusInsert = function(obj, str){
		var result, val = obj.value;
		obj.focus();
		if(document.selection){ //ie
			result = document.selection.createRange();
			document.selection.empty();
			result.text = str;
		} else {
			result = [val.substring(0, obj.selectionStart), str, val.substr(obj.selectionEnd)];
			obj.focus();
			obj.value = result.join('');
		}
	};

	console.group("欢迎使用ZFAKA开源开卡程序");
		console.log("github地址：https://github.com/zlkbdotnet/zfaka/");
		console.log("QQ交流群: 701035212");
		console.log("欢迎前来围观、吐槽、点赞、捐赠、STAR......");
	console.groupEnd();

	console.group("作者信息");
		console.log("网名：资料空白");
		console.log("博客：http://zlkb.net");
	console.groupEnd();
  //个人中心侧边导航
  var href = location.href + '/';
  var navItem = $('.fly-user-main .layui-nav .layui-nav-item');
  var navItemLen = navItem.length;
  for (var i = navItemLen - 1; i >= 0; i--) {
    var page = navItem.eq(i).find('a').attr('href');
    if (href.indexOf(page)>-1) {
      navItem.eq(i).addClass('layui-this').siblings().removeClass('layui-this');
      break;
    }
  }

  //数字前置补零
  layui.laytpl.digit = function(num, length, end){
    var str = '';
    num = String(num);
    length = length || 2;
    for(var i = num.length; i < length; i++){
      str += '0';
    }
    return num < Math.pow(10, length) ? str + (num|0) : num;
  };


  //加载特定模块
  /*if(layui.cache.page && layui.cache.page !== 'index'){
    var extend = {};
    extend[layui.cache.page] = layui.cache.page;
    layui.extend(extend);
    layui.use(layui.cache.page);
  }*/



  //手机设备的简单适配
	var treeMobile = $('.site-tree-mobile')
	,shadeMobile = $('.site-mobile-shade')

	treeMobile.on('click', function(){
		$('body').addClass('site-mobile');
		$('html,body').addClass('ovfHiden');
	});

	shadeMobile.on('click', function(){
		$('body').removeClass('site-mobile');
		$('html,body').removeClass('ovfHiden');
	});
  
	$('#main-menu-mobile-switch').on('click', function(){
		if($("#main-menu-mobile").is(":hidden")){
			$('body').addClass('main-menu-mobile_body');
			$('html,body').addClass('ovfHiden');
			var body_width = parseInt($('body').width());
			$("#main-menu-mobile").css("width",body_width);
			$('#main-menu-mobile').show();
		}else{
			$('body').removeClass('main-menu-mobile_body');
			$('html,body').removeClass('ovfHiden');
			$('#main-menu-mobile').hide();
		}
	});
	$('.site-mobile-shade').on('click', function(){
		$('body').removeClass('main-menu-mobile_body');
		$('html,body').removeClass('ovfHiden');
		$('#main-menu-mobile').hide();
	});
 	//全局删除信息提示
	table.on('tool(table)', function(obj) {
		var layEvent = obj.event;

		var url = $(this).data('href');
        var deltip = $(this).data('deltip') || '真的要删除该记录吗？';
		
		if (layEvent === 'del') { //删除
			layer.confirm(deltip, function(index) {
				layer.close(index);
				var loading = layer.load(2);
				$.ajax({
					url: url,
					type: 'POST',
					dataType: 'json',
					data: {'csrf_token':TOKEN}
				})
				.done(function(res) {
					if ( res.code == '1' ) {
                        obj.del();
                        layer.msg(res.msg,{icon:1})
                    } else {
                    	layer.msg(res.msg,{icon:2})
                    }
				})
				.fail(function() {
					layer.alert('error',{time:3000});
				})
				.always(function() {
					layer.close(loading);
				});
			});
		}else if (layEvent === 'dochange') {
			var loading = layer.load(2);
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {'csrf_token':TOKEN}
			})
			.done(function(res) {
				if ( res.code == '1' ) {
                    layer.msg(res.msg,{icon:1,time:1500},function(){location.reload();})
                } else {
                	layer.msg(res.msg,{icon:2,time:3000})
                }
			})
			.fail(function() {
				layer.alert('error',{icon:2},function(){location.reload();});
			})
			.always(function() {
				layer.close(loading);
			});
		}
	}); 
	
	//广告弹窗
	var layerad = $("#layerad").html(); 
	if(typeof(layerad)!="undefined"){
		if(layerad.length>0){
			layer.open({
				type: 1
				,title: false
				,closeBtn: false
				,area: '300px;'
				,shade: 0.8
				,id: 'zlkbAD'
				,btn: [ '关闭']
				,btnAlign: 'c'
				,moveType: 1 //拖拽模式，0或者1
				,content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">'+layerad+'</div>'
			});
		}
	}		
	
	exports('common',null);

});