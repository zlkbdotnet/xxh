layui.define(['layer', 'table', 'form','layedit','upload'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var table = layui.table;
	var form = layui.form;
	var layedit = layui.layedit;
	var upload = layui.upload;
	
	var edit_description=layedit.build('description',{
		tool: ['strong','italic','underline','|','del','left','center','right','link','unlink','face']
	});	 //建立编辑器
		
	table.render({
		elem: '#table',
		url: '/'+ADMIN_DIR+'/products/ajax',
		page: true,
		cellMinWidth:60,
		cols: [[
			{field: 'id', title: 'ID', width:80},
			{field: 'typename', title: '商品类型'},
			{field: 'name', title: '商品名称'},
			{field: 'price', title: '单价',width:80},
			{field: 'qty', title: '库存', width:80, templet: '#qty',align:'center'},
			{field: 'auto', title: '发货模式', width:100, templet: '#auto',align:'center'},
			{field: 'active', title: '是否销售', width:100, templet: '#active',align:'center'},
			{field: 'pifa', title: '批发', width:80,templet: '#pifa',align:'center'},
			{field: 'opt', title: '操作', width:120, templet: '#opt',align:'center'},
		]]
	});

	form.on('radio(stockcontrol)', function(data){
		if(data.value=='1'){
			var qty = $("#qty").attr("oldqty");
			$('#qty').val(qty);
			$("#qty").removeAttr("disabled");
		}else{
			$('#qty').val('0');
			$("#qty").attr("disabled","true");
		}
	});  
	
	form.on('radio(auto)', function(data){
		if(data.value=='1'){
			$('#addonsinput').hide();
		}else{
			$('#addonsinput').show();
		}
	});  
	
	//更新库存
	$("#products_form").on("click","#updateQty",function(event){
		event.preventDefault();
		var pid = $("#pid").val();
		$(this).attr({"disabled":"disabled"});
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '/'+ADMIN_DIR+'/products/updateqtyajax',
            data: { "csrf_token": TOKEN,'pid':pid},
            success: function(res) {
                if (res.code == 1) {
					layer.open({
						title: '提示',
						content: '更新成功',
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
                return;
            }
        });
	});
	
	//修改
	form.on('submit(edit)', function(data){
		layedit.sync(edit_description);
		data.field.csrf_token = TOKEN;
		data.field.description = layedit.getContent(edit_description);
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/'+ADMIN_DIR+'/products/editajax',
			type: 'POST',
			dataType: 'json',
			data: data.field,
		})
		.done(function(res) {
			if (res.code == '1') {
				layer.open({
					title: '提示',
					content: '修改成功',
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
            url: '/'+ADMIN_DIR+'/products/ajax',
            where: data.field
        });
        return false;
    });
	
	
	//普通图片上传
	var uploadInst = upload.render({
		elem: '#upload'
		,accept:'images'
		,acceptMime: 'image/*'
		,size:5120
		,auto:false
		,before: function(obj){
			//预读本地文件示例，不支持ie8
			obj.preview(function(index, file, result){
				//$('#btnbox').show();
				$("#btnbox").attr('disabled',false);
				$("#btnbox").removeClass("layui-btn-disabled");
				$('#upload').hide();
				$('#preview').attr('src', result); //图片链接（base64）
			});
		}
		,choose: function(obj){
			//预读本地文件示例，不支持ie8
			obj.preview(function(index, file, result){
				//$('#btnbox').show();
				$("#btnbox").attr('disabled',false);
				$("#btnbox").removeClass("layui-btn-disabled");
				$('#upload').hide();
				$('#preview').attr('src', result); //图片链接（base64）
			});
		}
	});	
	//上传
	form.on('submit(upload)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		var formData = new FormData(document.getElementById("products_imgurl_form"));
		$.ajax({
			url: '/'+ADMIN_DIR+'/products/imgurlajax',
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
					content: '上传成功',
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
	
	exports('adminproducts',null)
});