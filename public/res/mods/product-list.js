layui.define(['layer', 'table'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var table = layui.table;
	var device = layui.device();
	
	//分类id
	var tid = 0;
	if(typeof(TID)!="undefined"){
		tid = TID;
	}
	
	//密码分类
	if(typeof(PASSWORD_GROUP)!="undefined" && PASSWORD_GROUP >0){
			var html = '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;"><div class="layui-input-inline"><input type="password" id="grouppassword" name="grouppassword" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input"> </div></div>';
			layer.open({
				type: 1
				,title: false //不显示标题栏
				,closeBtn: true
				,area: '300px;'
				,shade: 0.8
				,id: 'group_password' //设定一个id，防止重复弹出
				,btn: ['提交','放弃']
				,btnAlign: 'c'
				,moveType: 1 //拖拽模式，0或者1
				,content: html
				,yes: function(layero){
					var pid = $("#pid").val(); 
					var grouppassword = $("#grouppassword").val(); 
					if(grouppassword.length>0){
						if(device.weixin || device.android || device.ios){
							table.render({
								elem: '#table',
								url: '/product/get',
								where: {"tid": tid,'password':grouppassword},
								page: true,
								cellMinWidth:60,
								cols: [[
									{field: 'name', title: '商品名称',templet: '#name',minWidth:120},
									{field: 'price', title: '单价', width:80,},
									{field: 'opt', title: '操作', width:80, templet: '#opt',align:'center',fixed: 'right'},
								]],
								done: function(res, curr, count){
									if(res.code>1){
										layer.msg(res.msg,{icon:2,time:5000});
									}else{
										layer.closeAll();
									}
								 }
							});
						}else{
							table.render({
								elem: '#table',
								url: '/product/get',
								where: {"tid": tid,'password':grouppassword},
								page: true,
								cellMinWidth:60,
								cols: [[
									{field: 'name', title: '商品名称',templet: '#name',minWidth:120},
									{field: 'price', title: '单价', width:80,},
									{field: 'qty', title: '库存', width:80, templet: '#qty',align:'center'},
									{field: 'auto', title: '发货模式', width:100, templet: '#auto',align:'center'},
									{field: 'opt', title: '操作', width:120, templet: '#opt',align:'center',fixed: 'right'},
								]],
								done: function(res, curr, count){
									if(res.code>1){
										layer.msg(res.msg,{icon:2,time:5000});
									}else{
										layer.closeAll();
									}
								 }
							});
						}
					}else{
						layer.msg("请输入密码",{icon:2,time:5000});
					}
				}	
				,btn2: function(index, layero){
					location.href = '/product/';
				}
				,cancel: function(){ 
					location.href = '/product/';
				}
			});
	
	}else{
		if(device.weixin || device.android || device.ios){
			table.render({
				elem: '#table',
				url: '/product/get',
				where: {"tid": tid},
				page: true,
				cellMinWidth:60,
				cols: [[
					{field: 'name', title: '商品名称',templet: '#name',minWidth:120},
					{field: 'price', title: '单价', width:80,},
					{field: 'opt', title: '操作', width:80, templet: '#opt',align:'center',fixed: 'right'},
				]]
			});
		}else{
			table.render({
				elem: '#table',
				url: '/product/get',
				where: {"tid": tid},
				page: true,
				cellMinWidth:60,
				cols: [[
					{field: 'name', title: '商品名称',templet: '#name',minWidth:120},
					{field: 'price', title: '单价', width:80,},
					{field: 'qty', title: '库存', width:80, templet: '#qty',align:'center'},
					{field: 'auto', title: '发货模式', width:100, templet: '#auto',align:'center'},
					{field: 'opt', title: '操作', width:120, templet: '#opt',align:'center',fixed: 'right'},
				]]
			});
		}	
	}
	
	//首页广告弹窗
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

	exports('product-list',null)
});