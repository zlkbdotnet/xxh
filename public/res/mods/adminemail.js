layui.define(['layer', 'form', 'table'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var form = layui.form;
	var table = layui.table;

	table.render({
		elem: '#table',
		url: '/'+ADMIN_DIR+'/email/ajax',
		page: true,
		cellMinWidth:60,
		cols: [[
			{field: 'id', title: 'ID', width:80},
			{field: 'mailaddress', title: '邮箱账号'},
			{field: 'sendname', title: '发件人', minWidth:200},
			{field: 'isssl', title: '使用SSL', width:100, templet: '#isssl',align:'center'},
			{field: 'port', title: '端口', width:100, align:'center'},
			{field: 'isactive', title: '是否激活', width:100, templet: '#isactive',align:'center'},
			{field: 'opt', title: '操作', width:120, templet: '#opt',align:'center'}
		]]
	});

	//修改资料
	form.on('submit(email)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/'+ADMIN_DIR+'/email/editajax',
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

	exports('adminemail',null)
});