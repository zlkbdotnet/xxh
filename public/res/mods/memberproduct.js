layui.define(['layer', 'table','base64'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var table = layui.table;

	table.render({
		elem: '#order',
		url: '/member/product/ajax',
		page: true,
		cellMinWidth:60,
		cols: [[
			{field: 'id', title: 'ID', width:80},
			{field: 'productname', title: '订单名称',minWidth:200},
			{field: 'price', title: '单价',width:80},
			{field: 'number', title: '数量',width:80},
			{field: 'money', title: '金额',width:80},
			{field: 'addtime', title: '下单时间', width:150, templet: '#addtime',align:'center'},
			{field: 'status', title: '状态',templet: '#status',width:80},
			{field: 'opt', title: '操作',templet: '#opt',width:120},
		]]
	});

	exports('memberproduct',null)
});