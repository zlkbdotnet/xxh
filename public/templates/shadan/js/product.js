layui.define(['layer', 'form','laytpl','element'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var laytpl = layui.laytpl;
	var element = layui.element;
	var form = layui.form;
	var device = layui.device();
	
	//手机适配调整
	if(device.weixin || device.android || device.ios){
		$(".productname").addClass("layui-form-text");
		$(".layui-input-inline").attr("class", "layui-input-block");
	}
	
	//判断是否为数字
	function isNotANumber(inputData) {
	　　if (parseFloat(inputData).toString() == "NaN") {
	　　　　return false;
	　　} else {
	　　　　return true;
	　　}
	}
	
	//订单金额
    $("#number").on('input',function(e){
		var stockcontrol = Number($('#stockcontrol').val());
		var qty = Number($('#qty').val());
		var number_value = $('#number').val();
		var number = Number(number_value);
		
		if(isNotANumber(number_value)){
			if(number<1){
				number =1;
				$('#number').val(1);
			}
		}
		
		if(stockcontrol>0){
			if(number>qty){
				$('#number').val(qty);
				number = qty;
			}
		}
		var money = $('#money').val();
		var price = parseFloat($('#price').val());
		money = price*number;
		if(PIFA!=""){
			for (var i = 0, j = PIFA.length; i < j; i++) {
				var myqty = PIFA[i].qty;
				if(number>=myqty){
					money = money*PIFA[i].discount;
					break;
				}
			}
		}
		money = changeTwoDecimal_f(money);
		$('#money').val(money);
		form.render('select');
    });
	
	form.verify({
		numberCheck: function(value, item){ //value：表单的值、item：表单的DOM对象
			var qty = $('#qty').val();
			var number = $('#number').val();
			var stockcontrol = $('#stockcontrol').val();
			var limitorderqty = $('#limitorderqty').val();
			if(stockcontrol>0){
				if(parseInt(number) > parseInt(qty)){
					return '下单数量超出库存';
				}
			}
			if(parseInt(number)>limitorderqty){
				return '下单数量超限';
			}
		}
		,chapwd: function(value, item){ //value：表单的值、item：表单的DOM对象
			if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
				return '查询密码不能有特殊字符';
			}
		}
	});
	
	function changeTwoDecimal_f(x) {
		var f_x = parseFloat(x);
		if (isNaN(f_x)) {
			alert('function:changeTwoDecimal->parameter error');
			return false;
		}
		var f_x = Math.round(x * 100) / 100;
		var s_x = f_x.toString();
		var pos_decimal = s_x.indexOf('.');
		if (pos_decimal < 0) {
			pos_decimal = s_x.length;
			s_x += '.';
		}
		while (s_x.length <= pos_decimal + 2) {
			s_x += '0';
		}
		return s_x;
	}	
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
		var ispassword = $(data.elem).find('option:selected').data('type');
		if(ispassword>0){
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
					var grouppassword = $("#grouppassword").val(); 
					if(grouppassword.length>0){
						//远程请求验证
						$.ajax({
							url: '/product/get/proudctlist',
							type: 'POST',
							dataType: 'json',
							data: {'tid': data.value,'password':grouppassword,'csrf_token':TOKEN},
							beforeSend: function () {
							},
							success: function (res) {
								if (res.code == '1') {
									var html = "";
									var list = res.data.products;
									for (var i = 0, j = list.length; i < j; i++) {
										var mypassword = list[i].password;
										var type = 0;
										if(mypassword.length>0){
											type = 1;
										}
										html += '<option value='+list[i].id+' data-type="'+type+'">'+list[i].name+'</option>';
									}
									$('#productlist').html("<option value=\"0\">请选择</option>" + html);
									$('#price').val('');
									$('#qty').val('');
									$('#number').val('1');
									$('#prodcut_description').html('');
									$("#buy").attr("disabled","true");
									$("#addons").remove();
									form.render('select');
									autoHeight();
									layer.closeAll();
								} else {
									layer.msg(res.msg,{icon:2,time:5000});
								}
							},
						});
					}else{
						layer.msg("请输入密码",{icon:2,time:5000});
					}
				}	
				,btn2: function(index, layero){
					$(data.elem).find("option").eq(0).val("0");
					$(data.elem).find("option").eq(0).attr("selected",true);
					$('#productlist').html("");
					$('#price').val('');
					$('#qty').val('');
					$('#number').val('1');
					$('#prodcut_description').html('');
					$("#buy").attr("disabled","true");
					$("#addons").remove();
					form.render('select');
					$(data.elem).find("option").eq(0).attr("selected",false);
				}
				,cancel: function(){ 
					$(data.elem).find("option").eq(0).val("0");
					$(data.elem).find("option").eq(0).attr("selected",true);
					$('#productlist').html("");
					$('#price').val('');
					$('#qty').val('');
					$('#number').val('1');
					$('#prodcut_description').html('');
					$("#buy").attr("disabled","true");
					$("#addons").remove();
					form.render('select');
					$(data.elem).find("option").eq(0).attr("selected",false);
				}
			});
		}else{
			//远程请求验证
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
							var mypassword = list[i].password;
							var type = 0;
							if(mypassword.length>0){
								type = 1;
							}
							html += '<option value='+list[i].id+' data-type="'+type+'">'+list[i].name+'</option>';
						}
						$('#productlist').html("<option value=\"0\">请选择</option>" + html);
						$('#price').val('');
						$('#qty').val('');
						$('#number').val('1');
						$('#prodcut_description').html('');
						$("#buy").attr("disabled","true");
						$("#addons").remove();
						form.render('select');
						autoHeight();
					} else {
						layer.msg(res.msg,{icon:2,time:5000});
						$(data.elem).find("option").eq(0).val("0");
						$(data.elem).find("option").eq(0).attr("selected",true);
						$('#productlist').html("");
						$('#price').val('');
						$('#number').val('1');
						$('#qty').val('');
						$('#prodcut_description').html('');
						$("#buy").attr("disabled","true");
						$("#addons").remove();
						form.render('select');
						$(data.elem).find("option").eq(0).attr("selected",false);
					}
				},

			});
		}
	});

	form.on('select(productlist)', function(data){
		if (data.value == 0) return;
		var ispassword = $(data.elem).find('option:selected').data('type');
		if(ispassword>0){
			var html = '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;"><div class="layui-input-inline"><input type="password" id="productpassword" name="productpassword" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input"> </div></div>';
			layer.open({
				type: 1
				,title: false //不显示标题栏
				,closeBtn: true
				,area: '300px;'
				,shade: 0.8
				,id: 'product_password' //设定一个id，防止重复弹出
				,btn: ['提交','放弃']
				,btnAlign: 'c'
				,moveType: 1 //拖拽模式，0或者1
				,content: html
				,yes: function(layero){
					var productpassword = $("#productpassword").val(); 
					if(productpassword.length>0){
						//远程请求验证
						$.ajax({
							url: '/product/get/proudctinfo',
							type: 'POST',
							dataType: 'json',
							data: {'pid': data.value,'password':productpassword,'csrf_token':TOKEN},
							beforeSend: function () {
							},
							success: function (res) {
								if (res.code == '1') {
									var product = res.data.product;
									var html =""
									$('#price').val(product.price);
									$('#money').val(product.price);
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
									
									PIFA = res.data.pifa;
									
									$("#addons").remove();
									var list = res.data.addons;
									if(list.length>0){
										var addons = '<div id="addons">';
										for (var i = 0, j = list.length; i < j; i++) {
											addons += '<div class="layui-form-item"><label class="layui-form-label">'+list[i]+'</label><div class="layui-input-block"><input type="text" name="addons[]" id="addons'+i+'" class="layui-input" required lay-verify="required" placeholder=""></div></div>';
										}
										addons += "</div>";
										$('#product_input').append(addons);
									}
									$('#number').val('1');
									$('#prodcut_num').height('auto');
									form.render();
									autoHeight();
									layer.closeAll();
								} else {
									layer.msg(res.msg,{icon:2,time:5000});
								}
							}
						});
						
					}else{
						layer.msg("请输入密码",{icon:2,time:5000});
					}
				}	
				,btn2: function(index, layero){
					$(data.elem).find("option").eq(0).val("0");
					$(data.elem).find("option").eq(0).attr("selected",true);
					$('#price').val('');
					$('#qty').val('');
					$('#prodcut_description').html('');
					$("#buy").attr("disabled","true");
					$("#addons").remove();
					$('#number').val('1');
					form.render('select');
					$(data.elem).find("option").eq(0).attr("selected",false);
				}
				,cancel: function(){ 
					$(data.elem).find("option").eq(0).val("0");
					$(data.elem).find("option").eq(0).attr("selected",true);
					$('#price').val('');
					$('#qty').val('');
					$('#prodcut_description').html('');
					$("#buy").attr("disabled","true");
					$("#addons").remove();
					$('#number').val('1');
					form.render('select');
					$(data.elem).find("option").eq(0).attr("selected",false);
				}
			});
		}else{
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
						$('#money').val(product.price);
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
						PIFA = res.data.pifa;
						$("#addons").remove();
						var list = res.data.addons;
						if(list.length>0){
							var addons = '<div id="addons">';
							for (var i = 0, j = list.length; i < j; i++) {
								addons += '<div class="layui-form-item"><label class="layui-form-label">'+list[i]+'</label><div class="layui-input-block"><input type="text" name="addons[]" id="addons'+i+'" class="layui-input" required lay-verify="required" placeholder=""></div></div>';
							}
							addons += "</div>";
							$('#product_input').append(addons);
						}
						$('#prodcut_num').height('auto');
						$('#number').val('1');
						form.render();
						autoHeight();
					} else {
						layer.msg(res.msg,{icon:2,time:5000});
						$(data.elem).find("option").eq(0).val("0");
						$(data.elem).find("option").eq(0).attr("selected",true);
						$('#price').val('');
						$('#qty').val('');
						$('#number').val('1');
						$('#prodcut_description').html('');
						$("#buy").attr("disabled","true");
						$("#addons").remove();
						form.render('select');
						$(data.elem).find("option").eq(0).attr("selected",false);
					}
				}
			});
		}
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
	
	//密码商品
	if(typeof(PASSWORD_PRODUCT)!="undefined"){
		if(PASSWORD_PRODUCT >0){
			var html = '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;"><div class="layui-input-inline"><input type="password" id="productpassword" name="productpassword" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input"> </div></div>';
			layer.open({
				type: 1
				,title: false //不显示标题栏
				,closeBtn: true
				,area: '300px;'
				,shade: 0.8
				,id: 'product_password' //设定一个id，防止重复弹出
				,btn: ['提交','放弃']
				,btnAlign: 'c'
				,moveType: 1 //拖拽模式，0或者1
				,content: html
				,yes: function(layero){
					var pid = $("#pid").val(); 
					var productpassword = $("#productpassword").val(); 
					if(productpassword.length>0){
						//远程请求验证
						$.ajax({
							url: '/product/get/proudctinfo',
							type: 'POST',
							dataType: 'json',
							data: {'pid': pid,'password':productpassword,'csrf_token':TOKEN},
							beforeSend: function () {
							},
							success: function (res) {
								if (res.code == '1') {
									var product = res.data.product;
									var html =""
									$('#price').val(product.price);
									$('#money').val(product.price);
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
									var list = res.data.addons;
									if(list.length>0){
										var addons = '<div id="addons">';
										for (var i = 0, j = list.length; i < j; i++) {
											addons += '<div class="layui-form-item"><label class="layui-form-label">'+list[i]+'</label><div class="layui-input-block"><input type="text" name="addons[]" id="addons'+i+'" class="layui-input" required lay-verify="required" placeholder=""></div></div>';
										}
										addons += "</div>";
										$('#product_input').append(addons);
									}
									$('#prodcut_num').height('auto');
									form.render();
									autoHeight();
									layer.closeAll();
								} else {
									layer.msg(res.msg,{icon:2,time:5000});
								}
							}
						});
						
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
		}
	}	
	
	//查询批发优惠
	$('#view-youhui').on('click', function(event) {
		var getTpl = youhui_tpl.innerHTML;
		var youhui_html = "";
		laytpl(getTpl).render(PIFA, function(html){
			 youhui_html = html;
		});
		element.render('query-m-result');
		
		layer.open({
			type: 1
			,title: false
			,closeBtn: true
			,offset: "auto"
			,id: 'layerYouhuiAuto' //防止重复弹出
			,content: youhui_html
			,shade: 0 //不显示遮罩
			,yes: function(){
			  layer.closeAll();
			}
		});
	});
	exports('product',null)
});