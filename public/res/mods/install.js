layui.define(['layer', 'form','code'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var form = layui.form;
	
	layui.code();
	
	var len = $('.checkerror').length;
	if(len>0){
		$("#setpone").attr("href","javascript:;");
		$("#setpone").attr("class","layui-btn layui-btn-disabled");
	}
	
	form.verify({
		dbname:function(value){
			var reg= /^[A-Za-z\\0-9\\_\\\-]+$/;
			if (!reg.test(value)) {
				return '数据库名只能包含英文字母、中划线以及下划线';
			}
		}
	});
	//提交数据库
	form.on('submit(setptwo)', function(data){

		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/install/setptwo/ajax',
			type: 'POST',
			dataType: 'json',
			data: data.field,
		})
		.done(function(res) {
			if (res.code == '1') {
				layer.open({
					type: 1
					,offset: 'auto' 
					,id: 'result'
					,content: '<div style="padding: 20px 100px;">数据库安装成功</div>'
					,btn: '确定'
					,btnAlign: 'c' 
					,shade: 0 
					,yes: function(){
						location.href = '/install/last';
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

		return false; //阻止表单跳转。
	});

	form.on('submit(upgrade_button)', function(data){

		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/install/upgrade/ajax',
			type: 'POST',
			dataType: 'json',
			data: data.field,
		})
		.done(function(res) {
			if (res.code == '1') {
				layer.open({
					type: 1
					,offset: 'auto' 
					,id: 'result'
					,content: '<div style="padding: 20px 100px;">更新成功</div>'
					,btn: '确定'
					,btnAlign: 'c' 
					,shade: 0 
					,yes: function(){
						location.href = '/'+ADMIN_DIR;
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

		return false; //阻止表单跳转。
	});
	
	exports('install',null)
});