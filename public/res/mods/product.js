layui.define(['layer', 'form'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var form = layui.form;

	form.verify({
		numberCheck: function(value, item){ //value：表单的值、item：表单的DOM对象
			var qty = $('#qty').val();
			var number = $('#number').val();
			var stockcontrol = $('#stockcontrol').val();
			if(stockcontrol>0){
				if(parseInt(number) > parseInt(qty)){
					return '下单数量超限';
				}
			}
		}
		,chapwd: function(value, item){ //value：表单的值、item：表单的DOM对象
			if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
				return '查询密码不能有特殊字符';
			}
		}
	});	
	
    function htmlspecialchars_decode(str){
		if(str.length>0){
			str = str.replace(/&amp;/g, '&');
			str = str.replace(/&lt;/g, '<');
			str = str.replace(/&gt;/g, '>');
			str = str.replace(/&quot;/g, '"');
			str = str.replace(/&#039;/g, "'");
		}
        return str;  
    }
	
	function buyNumCheck(){
		var qty = $('#qty').val();
		var number = $('#number').val();
		var stockcontrol = $('#stockcontrol').val();
		if(stockcontrol>0){
			if(parseInt(number) > parseInt(qty)){
				return false;
			}
		}
		return true;
	}
	
	form.on('select(typeid)', function(data){
		if (data.value == 0) return;
		$.ajax({
			url: '/product/get/proudctlist',
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
					$('#price').val('');
					$('#qty').val('');
					$('#prodcut_description').html('');
					$("#buy").attr("disabled","true");
					$("#addons").remove();
					form.render('select');
					autoHeight();
				} else {
					$("#buy").attr("disabled","true");
					form.render('select');
					layer.msg(res.msg,{icon:2,time:5000});
				}
			}

		});
	});

	form.on('select(productlist)', function(data){
		if (data.value == 0) return;
		$.ajax({
			url: '/product/get/proudctinfo',
			type: 'POST',
			dataType: 'json',
			data: {'pid': data.value,'csrf_token':TOKEN},
			beforeSend: function () {
			},
			success: function (res) {
				if (res.code == '1') {
					var product = res.data.product;
					var html =""
					$('#price').val(product.price);
					if(product.stockcontrol>0){
						if(product.qty>0){
							$('#qty').val(product.qty);
							$("#buy").removeAttr("disabled");
						}else{
							$('#qty').val("库存不足");
							$("#buy").attr("disabled","true");
						}
					}else{
						$('#qty').val("不限量");
						$("#buy").removeAttr("disabled");
					}
					$('#stockcontrol').val(product.stockcontrol);
					if(product.auto>0){
						var str = '<p><span class="layui-badge layui-bg-green">自动发货</span></p>';
					}else{
						var str = '<p><span class="layui-badge layui-bg-black">手工发货</span></p>';
					}
					
					html = str + htmlspecialchars_decode(product.description);
					$('#prodcut_description').html(html);
					
					$("#addons").remove();
					var addons = '';
					var list = res.data.addons;
					for (var i = 0, j = list.length; i < j; i++) {
						addons += '<div id="addons"><div class="layui-form-item"><label class="layui-form-label">'+list[i]+'</label><div class="layui-input-block"><input type="text" name="addons[]" id="addons'+i+'" class="layui-input" required lay-verify="required" placeholder=""></div></div></div>';
					}
					$('#product_input').append(addons);
					$('#prodcut_num').height('auto');
					
					form.render();
					autoHeight();
				} else {
					layer.msg(res.msg,{icon:2,time:5000});
				}
			}
		});

	});

	form.on('submit(buy)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		
		if(buyNumCheck()){
			$.ajax({
				url: '/product/order/buy/',
				type: 'POST',
				dataType: 'json',
				data: data.field,
			})
			.done(function(res) {
				if (res.code == '1') {
					var oid = res.data.oid;
					if(oid.length>0){
						location.href = '/product/order/pay/?oid='+res.data.oid;
					}else{
						layer.msg("订单异常",{icon:2,time:5000});
					}
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
		}else{
			layer.msg("下单数量超限",{icon:2,time:5000});
			layer.close(i);
		}
		return false; 
	});

	//左右框高度
	function autoHeight(){
		var leftHeight = parseInt($('#prodcut_num').height());
		var rightHeight = parseInt($('#prodcut_description').height());
		if (leftHeight > rightHeight) {
			$('#prodcut_description').height(leftHeight);
		} else {
			$('#prodcut_num').height(rightHeight);
		}
	}

	//对商品描述再做一次补充解密处理
	/*var aName = window.location.pathname;
	if (aName.indexOf('/product/detail') >-1) {
		html = htmlspecialchars_decode($('#prodcut_description').text());
		$('#prodcut_description').html(html);
	}*/
	autoHeight();
	
	//首页广告弹窗
	if(LAYERAD.length>0){
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
			,content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">'+LAYERAD+'</div>'
		});
	}
	exports('product',null)
});