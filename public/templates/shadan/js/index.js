layui.define(['layer', 'carousel','laytpl','element'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var laytpl = layui.laytpl;
	var element = layui.element;
	var device = layui.device();
	
	
	var carousel = layui.carousel;
	  carousel.render({
		elem: '#carousel_index'
		,width: '100%' //设置容器宽度
		,arrow: 'always' //始终显示箭头
	});

	exports('index',null)
});