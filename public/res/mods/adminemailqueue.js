layui.define(['layer', 'table', 'form'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var table = layui.table;
	var form = layui.form;

	table.render({
		elem: '#table',
		url: '/'+ADMIN_DIR+'/emailqueue/ajax',
		page: true,
		cellMinWidth:60,
		cols: [[
			{type: 'checkbox', fixed: 'left'},
			{field: 'id', title: 'ID', width:80},
			{field: 'status', title: '状态', width:80, templet: '#status',align:'center'},
			{field: 'email', title: '收件人', minWidth:160},
			{field: 'subject', title: '主题', minWidth:160},
			{field: 'sendtime', title: '发送时间', width:200, templet: '#sendtime',align:'center'},
			{field: 'opt', title: '操作', templet: '#opt',align:'center',fixed: 'right', width: 160},
		]]
	});
    form.on('submit(search)', function(data){
        table.reload('table', {
            url: '/'+ADMIN_DIR+'/emailqueue/ajax',
            where: data.field
        });
        return false;
    });
	
	table.on('tool(table)', function(obj){
		var data = obj.data;
		if(obj.event === 'detail'){
			layer.msg('失败原因:'+ data.sendresult );
		}
	});
  
    $('#deleteALL').on('click',function () {
        var checkStatus = table.checkStatus('table');
        var data=checkStatus.data;
		var ids=[];
		for(var i in data){
			ids.push(data[i].id);
		}
		if(ids.length>0){
			layer.confirm('确认删除选中消息吗？', function(index) {
				var param = {'ids': ids};
				$.ajax({
					url: '/'+ADMIN_DIR+'/emailqueue/delete',//请求的url地址
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
			layer.msg('请选中需要删除的消息',{icon: 2});
		}
	});
	exports('adminemailqueue',null)
});