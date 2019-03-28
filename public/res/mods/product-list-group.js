layui.define(['layer', 'table'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var table = layui.table;
	var device = layui.device();

	if(device.weixin || device.android || device.ios){
		table.render({
			elem: '#table',
			url: '/product/get/grouplist',
			page: true,
			cellMinWidth:60,
			cols: [[
				{field: 'name', title: '分类',templet: '#name',minWidth:120},
				{field: 'opt', title: '操作', width:80, templet: '#opt',align:'center',fixed: 'right'},
			]]
		});	
	}else{
		table.render({
			elem: '#table',
			url: '/product/get/grouplist',
			page: true,
			cellMinWidth:60,
			cols: [[
				{field: 'name', title: '分类',templet: '#name',width:120},
				{field: 'description', title: '描述'},
				{field: 'opt', title: '操作', width:80, templet: '#opt',align:'center',fixed: 'right'},
			]]
		});
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

	exports('product-list-group',null)
});