layui.define(['layer', 'table', 'form','upload'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var table = layui.table;
	var form = layui.form;
	var upload = layui.upload;
	//拖拽上传
	upload.render({
		elem: '#import_cards'
		,url: '/'+ADMIN_DIR+'/productscard/importajax/'
		,auto: false
		,accept: 'file' //普通文件
		,exts: 'txt' //只允许txt文件
		,size: 100 //限制文件大小，单位 KB
		//,bindAction: '#startUploadfff'
		,done: function(res){
			//console.log(res)
		}
	});
	
	form.on('select(typeid)', function(data){
		if (data.value == 0) return;
		$.ajax({
			url: '/'+ADMIN_DIR+'/products/getlistbytid',
			type: 'POST',
			dataType: 'json',
			data: {'tid': data.value,'csrf_token':TOKEN},
			beforeSend: function () {
			},
			success: function (res) {
				if (res.code == '1') {
					var html = "";
					var list = res.data.products;
					for (var i = 0, j = list.length; i < j; i++) {
						html += '<option value='+list[i].id+'>'+list[i].name+'</option>';
					}
					$('#productlist').html("<option value=\"0\">请选择</option>" + html);
					form.render('select');
				} else {
					form.render('select');
					layer.msg(res.msg,{icon:2,time:5000});
				}
			}

		});
	});
	
	//导入
	form.on('submit(import)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		var formData = new FormData(document.getElementById("import_table"));
		$.ajax({
			url: '/'+ADMIN_DIR+'/productscard/importajax',
			type: 'POST',
			dataType: 'json',
			data:formData,
			processData: false,
            contentType: false,
		})
		.done(function(res) {
			if (res.code == '1') {
				layer.open({
					title: '提示',
					content: '导入成功',
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
	
	//导出
	form.on('submit(download)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$('#download_form').submit();
		layer.close(i);
	});

	table.render({
		elem: '#table',
		url: '/'+ADMIN_DIR+'/productscard/ajax',
		page: true,
		cellMinWidth:60,
		cols: [[
			{type: 'checkbox', fixed: 'left'},
			{field: 'id', title: 'ID', width:80},
			{field: 'name', title: '商品名'},
			{field: 'card', title: '卡密'},
			{field: 'addtime', title: '添加时间', width:200, templet: '#addtime',align:'center'},
			{field: 'oid', title: '状态', width:100, templet: '#status',align:'center'},
			{field: 'opt', title: '操作', width:100, toolbar: '#opt',align:'center'},
		]]
	});

    $('#deleteALL').on('click',function () {
        var checkStatus = table.checkStatus('table');
        var data=checkStatus.data;
		var ids=[];
		for(var i in data){
			ids.push(data[i].id);
		}
		if(ids.length>0){
			layer.confirm('确认删除选中卡密吗？', function(index) {
				var param = {'ids': ids};
				$.ajax({
					url: '/'+ADMIN_DIR+'/productscard/delete',//请求的url地址
					dataType: 'json',//返回的格式为json
					data: {'id': JSON.stringify(param),'csrf_token':TOKEN},//参数值
					type: "POST"
				})
				.done(function (data) {
				if (data.code == 1) {
					layer.msg(data.msg, {icon: 1});
					location.reload();
				} else {
					layer.msg(data.msg, {icon: 2});
					}
				})
			})
		}else{
			layer.msg('请选中需要删除的卡密',{icon: 2});
		}
	});
     $('#deleteempty').on('click',function () {
		layer.confirm('确认清空所有已删除的卡密吗？', function(index) {
			$.ajax({
				url: '/'+ADMIN_DIR+'/productscard/deleteempty',//请求的url地址
				dataType: 'json',//返回的格式为json
				data: {'method': "empty",'csrf_token':TOKEN},//参数值
				type: "POST"
			})
			.done(function (data) {
			if (data.code == 1) {
				layer.msg(data.msg, {icon: 1});
				location.reload();
			} else {
				layer.msg(data.msg, {icon: 2});
				}
			})
		})
	}); 
	
    form.on('submit(repair)', function(data){
		data.field.csrf_token = TOKEN;
		data.field.method = 'repair';
		var i = layer.load(2,{shade: [0.5,'#fff']});
			$.ajax({
				url: '/'+ADMIN_DIR+'/productscard/repairajax/',
				type: 'POST',
				dataType: 'json',
				data: data.field,
			})
			.done(function(res) {
				if (res.code == '1') {
					layer.open({
						type: 1
						,title: '修复数据'
						,offset: 'auto'
						,id: 'layerPayone' //防止重复弹出
						,content: '<div style="padding: 20px 100px;">'+res.msg+'</div>'
						,btn: '关闭'
						,btnAlign: 'c' //按钮居中
						,shade: 0.8 //不显示遮罩
						,yes: function(){
							location.reload();
						}
						,cancel: function(){ 
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
	//添加
	form.on('submit(add)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/'+ADMIN_DIR+'/productscard/addajax',
			type: 'POST',
			dataType: 'json',
			data: data.field,
		})
		.done(function(res) {
			if (res.code == '1') {
				layer.open({
					title: '提示',
					content: '新增成功',
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

	//批量添加
	form.on('submit(addplus)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/'+ADMIN_DIR+'/productscard/addajax',
			type: 'POST',
			dataType: 'json',
			data: data.field,
		})
		.done(function(res) {
			if (res.code == '1') {
				layer.open({
					title: '提示',
					content: '新增成功',
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
    form.on('submit(search)', function(data){
        table.reload('table', {
            url: '/'+ADMIN_DIR+'/productscard/ajax',
            where: data.field
        });
        return false;
    });
	exports('adminproductscard',null)
});