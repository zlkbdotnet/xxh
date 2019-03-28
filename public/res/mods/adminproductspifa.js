layui.define(['layer', 'table', 'form'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var table = layui.table;
	var form = layui.form;
	var pid = $('#pid').val();
		
	table.render({
		elem: '#table',
		url: '/'+ADMIN_DIR+'/productspifa/ajax',
		page: true,
		cellMinWidth:60,
		where: {"pid": pid},
		cols: [[
			{field: 'qty', title: '数量',width:80},
			{field: 'discount', title: '折扣',width:80},
			{field: 'tag', title: '备注'},
			{field: 'opt', title: '操作', width:120, templet: '#opt',align:'center'},
		]]
	});
	
	//修改
	form.on('submit(edit)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/'+ADMIN_DIR+'/productspifa/editajax',
			type: 'POST',
			dataType: 'json',
			data: data.field,
		})
		.done(function(res) {
			if (res.code == '1') {
				layer.open({
					title: '提示',
					content: '提交成功',
					btn: ['确定'],
					yes: function(index, layero){
					    location.reload();
					},
					cancel: function(){ 
					    location.reload();
					}
				});
			} else {
				layer.msg(res.msg,{icon:2,time:5000});
			}
		})
		.fail(function() {
			layer.msg('服务器连接失败，请联系管理员',{icon:2,time:5000});
		})
		.always(function() {
			layer.close(i);
		});

		return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
	});
	exports('adminproductspifa',null)
});