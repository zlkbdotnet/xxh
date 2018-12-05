layui.define(['layer', 'form','jquery','base64','laytpl'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var form = layui.form;
	var device = layui.device();
	var laytpl = layui.laytpl;
	var lodding;
	
	function createTime(v){
		var date = new Date();
		date.setTime(parseInt(v)*1000);
		var y = date.getFullYear();
		var m = date.getMonth()+1;
		m = m<10?'0'+m:m;
		var d = date.getDate();
		d = d<10?("0"+d):d;
		var h = date.getHours();
		h = h<10?("0"+h):h;
		var M = date.getMinutes();
		M = M<10?("0"+M):M;
		var s = date.getSeconds();
		s = s<10?("0"+s):s;
		var str = y+"-"+m+"-"+d+" "+h+":"+M+":"+s;
		return str;
	}
	
	function converStatus(data){
		var str = "";
		var s = data.status;
		switch(s)
		{
			case '0':
				oid = $.base64.encode(data.id);
				str = '<span class="layui-badge layui-bg-gray">待付款</span>';
				break;
			case '1':
				str = '<span class="layui-badge layui-bg-blue">待处理</span>';
				break;
			case '2':
				str = '<span class="layui-badge layui-bg-green">已完成</span>';
				str += ' <button class="view_kami layui-btn layui-btn-warm layui-btn-xs" data-orderid="'+data.orderid+'">提取卡密</button>';
				break;
			default:
				str = '<span class="layui-badge layui-bg-black">处理失败</span>';
				break;
		}
		return str;
	}
	
	$("#query-pane").on("click",".view_kami",function(event){
		event.preventDefault();
		var orderid = $(this).attr("data-orderid");
		$(this).attr({"disabled":"disabled"});
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/product/query/kami",
            data: { "csrf_token": TOKEN,'orderid':orderid},
			beforeSend: function () {
				lodding = layer.load();
			},
			complete: function () {
				layer.close(lodding);
			},
			error: function (data) {
				ayer.close(lodding);
			},
            success: function(res) {
                if (res.code == 1) {
					var html = "";
					var list = res.data;
					for (var i = 0, j = list.length; i < j; i++) {
						html += '<p>'+list[i]+'</p>';
					}
					layer.open({
						type: 1
						,title: '提取卡密'
						,offset: 'auto'
						,id: 'layerDemoauto' //防止重复弹出
						,content: '<div style="text-align: center;padding: 20px 100px;">'+html+'</div>'
						,btn: '关闭'
						,btnAlign: 'c' //按钮居中
						,shade: 0 //不显示遮罩
						,yes: function(){
						  layer.closeAll();
						}
					});
                } else {
					layer.msg(res.msg,{icon:2,time:5000});
                }
                return;
            }
        });
	});
	
	$('.loadcode').on('click', function(event) {
		event.preventDefault();
		$(this).attr('src','/Captcha?t=productquery&n=' + Math.random())
	});
	
	form.on('submit(query)', function(data){
		data.field.csrf_token = TOKEN;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/product/query/ajax/',
			type: 'POST',
			dataType: 'json',
			data: data.field,
		})
		.done(function(res) {
			if (res.code == '1') {
				if(device.weixin==true || device.android==true || device.ios==true){
					var getTpl = query_ajax_mobile.innerHTML
					,view = document.getElementById('query-ajax-mobile-view');
					laytpl(getTpl).render(res, function(html){
					  view.innerHTML = html;
					});
					$("#query-form").hide();
				}else{
					$("#query-table tbody").html("<tr></tr>");
					var html = "";
					var list = res.data;
					for (var i = 0, j = list.length; i < j; i++) {
						var orderstatus = converStatus(list[i]);
						html += '<tr><td><span id="orderid">'+list[i].orderid+'</span></td><td>'+list[i].productname+'</td><td>'+list[i].number+'</td><td>'+list[i].money+'</td><td>'+createTime(list[i].addtime)+'</td><td>'+orderstatus+'</td></tr>';
					}
					$("#query-table tbody").prepend(html);
					$("#query-table").show();
				}
				$(".view_kami").click(function(){});
				layer.msg(res.msg,{icon:1,time:5000});
			} else {
				$('.loadcode').attr('src','/Captcha?t=productquery&n=' + Math.random());
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
	
	exports('productquery',null)
});