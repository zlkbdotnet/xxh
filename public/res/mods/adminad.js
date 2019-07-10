layui.define(['layer', 'form', 'table'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var form = layui.form;
	var table = layui.table;

	table.render({
		elem: '#table',
		url: '/'+ADMIN_DIR+'/ad/ajax',
		page: true,
		cellMinWidth:60,
		cols: [[
			{field: 'id', title: 'ID', width:80},
			{field: 'name', title: '广告位'},
			{field: 'title', title: '广告标题', minWidth:200},
			{field: 'label', title: '广告标签', minWidth:200},
			{field: 'isactive', title: '是否激活', width:100, templet: '#isactive',align:'center'},
			{field: 'opt', title: '操作', width:120, templet: '#opt',align:'center'}
		]]
	});


	//修改资料
	form.on('submit(edit)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/'+ADMIN_DIR+'/ad/editajax',
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

	exports('adminad',null)
});