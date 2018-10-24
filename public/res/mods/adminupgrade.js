layui.define(['layer', 'form'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var form = layui.form;


	// 下载ZIP包
    function download() {
		var i = layer.load(2,{shade: [0.5,'#fff']});
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '/'+ADMIN_DIR+'/upgrade/getremotefile',
            timeout: 10000, //ajax请求超时时间10s
            data: {"csrf_token": TOKEN,'method':"download"}, //post数据
			beforeSend: function(XMLHttpRequest){
				
			},
            success: function (res, textStatus) {
				if (res.code == '1') {
					location.href = '/install/upgrade';
				}else{
					layer.msg(res.msg,{icon:2,time:5000});
				}
            },
			complete: function(XMLHttpRequest, textStatus){
				
			},
			error: function(){
				
			}
        });
    }

	form.on('submit(download)', function(data){
		download();
		return false;
	});
	exports('adminupgrade',null)
});