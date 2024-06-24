
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <a href="{:session('back_url', '', 'manage')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">说明书列表</div>

		<div class="layui-form">
            <button type="button" class="layui-btn  layui-btn-normal" id="excel">上传</button>
			<table class="layui-table" lay-size="sm">
				<colgroup>
					<col width="50">
					<col>
					<col>
                    <col width="200">
                    <col width="180">
					<col width="150">
				</colgroup>
				<thead>
					<tr>
						<th>ID</th>
						<th>图片名称</th>
						<th>图片路径</th>
                        <th>临时文件下载路径</th>
                        <th>过期时间</th>
						<th class="tc">操作</th>
					</tr>
				</thead>
				<tbody>
					{foreach name="list" item="v"}
						<tr>
							<td class="tr">{$v.id}</td>
							<td>{$v.file_name}</td>
							<td>{$v.file_path}</td>
                            <td><a href="{$v.file_tmp_url}">{$v.file_tmp_url}</a></td>
                            <td>{$v.file_tmp_expire}</td>
                            <td class="tc">
                                <button class="layui-btn layui-btn-normal layui-btn-sm" data-id="{$v.id}" lay-submit lay-filter="formCoding">生成文件</button>
                                <button data-id="{$v.id}" class="layui-btn layui-btn-sm layui-btn-danger ml0" lay-submit lay-filter="Detele">删除</button>
                            </td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
    </div>
</div>
<script>
layui.use(['form', 'jquery', 'upload'], function(){
	var $ = layui.jquery,
		form = layui.form,
        upload = layui.upload;

	// 排序
	form.on('submit(Sort)', function(data){
		var text = $(this).text(), button = $(this);
		$('button').attr('disabled',true);
		button.text('请稍候...');
		$.ajax({
			type:'POST',url:"{:url('sort')}",data:data.field,dataType:'json',
			success:function(data){
				if(data.code == 1){
					layer.alert(data.msg,{icon:1,closeBtn:0,title:false,btnAlign:'c'},function(){
						location.reload();
					});
				}else{
					layer.alert(data.msg,{icon:2,closeBtn:0,title:false,btnAlign:'c'},function(){
						layer.closeAll();
						$('button').attr('disabled',false);
						button.text(text);
					});
				}
			}
		});
		return false;
	});

	// 状态
	form.on('switch(formLock)', function(data){
		$('button').attr('disabled',true);
		$.ajax({
			type:'POST',url:"{:url('status')}",data:{id:data.value,type:'look'},dataType:'json',
			success:function(data){
				if(data.code == 0){
					layer.alert(data.msg,{icon:2,closeBtn:0,title:false,btnAlign:'c'},function(){
						location.reload();
					});
				}
			}
		});
	});

	// 删除
	form.on('submit(Detele)', function(data){
		var text = $(this).text(),
			button = $(this),
			id = $(this).data('id');
		layer.confirm('确定删除吗？',{icon:3,closeBtn:0,title:false,btnAlign:'c'},function(){
			$('button').attr('disabled',true);
			button.text('请稍候...');
			$.ajax({
				type:'POST',url:"{:url('delete')}",data:{id:id},dataType:'json',
				success:function(data){
					if(data.code == 1){
						layer.alert(data.msg,{icon:1,closeBtn:0,title:false,btnAlign:'c'},function(){
							location.reload();
						});
					}else{
						layer.alert(data.msg,{icon:2,closeBtn:0,title:false,btnAlign:'c'},function(){
							layer.closeAll();
							$('button').attr('disabled',false);
							button.text(text);
						});
					}
				}
			});
		});
	});

    // 导入
    let uploadInst = upload.render({
        elem: '#excel' //绑定元素
        , url: '/Manage/upload/instructions_upload' //上传接口
        , exts: 'xls|xlsx|csv|pdf'
        , data: {
            id: function () {
                return {$id};
            }
        }
        , multiple: true
        , before: function (obj) {
            layer.load(1);
        }
        , done: function (res) {
            //上传完毕回调
            console.log(res);
            if (res.code === 1) {
                location.href = "/Manage/Sku/instructions/id/" + {$id} + ".html";
            } else {
                layer.alert(res.msg, {icon: 2, closeBtn: 0, title: false, btnAlign: 'c'}, function () {
                    layer.closeAll();
                });
            }
        }
        , error: function () {
            //请求异常回调
        }
    })

    //监听提交
    form.on('submit(formCoding)', function(data){
        let text = $(this).text(),
            button = $(this),
            id = $(this).data('id');
        console.log(id);
        $('button').attr('disabled',true);
        button.text('请稍候...');
        axios.post("{:url('createTmpUrl')}", {id: id})
            .then(function (response) {
                let res = response.data;
                if (res.code === 1) {
                    layer.alert(res.msg,{icon:1,closeBtn:0,title:false,btnAlign:'c',},function(){
                        location.reload();
                    });
                } else {
                    layer.alert(res.msg,{icon:2,closeBtn:0,title:false,btnAlign:'c'},function(){
                        layer.closeAll();
                        $('button').attr('disabled',false);
                        button.text(text);
                    });
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        return false;
    });
});
</script>

{include file="public/footer" /}
