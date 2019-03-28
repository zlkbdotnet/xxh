layui.define(['layer','jquery','laytpl','element','flow'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var laytpl = layui.laytpl;
	var element = layui.element;
	var flow = layui.flow;
	var total_page = 2;
	var device = layui.device();
	
	function getProduct(p)
	{
		var limit = 8;
		var i = layer.load(2,{shade: [0.5,'#fff']});
		$.ajax({
			url: '/product/get/?limit='+limit+'&page='+p,
			type: 'POST',
			dataType: 'json',
			data: {"tid": 0},
		})
		.done(function(res) {
			if (res.code == '0') {
				var getTpl = null;
				if(device.weixin || device.android || device.ios){
					getTpl = product_list_two_mobile_tpl.innerHTML;
					$("#product-list-two-view").attr("class", "layui-row layui-col-space10");
				}else{
					getTpl = product_list_two_tpl.innerHTML;
					$("#product-list-two-view").attr("class", "layui-row layui-col-space30");
				}
				laytpl(getTpl).render(res, function(html){
					$("#product-list-two-view").append(html);
				});
				element.render('product-list-two-view');
				total_page = Math.ceil(parseInt(res.count)/limit);
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
	};
	
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
	
	//流媒体
	flow.load({
		elem: '#more'
		,done: function(page, next){
			getProduct(page);
			next('', page < total_page);  
		}
	});
	
	exports('product-list-two',null)
});