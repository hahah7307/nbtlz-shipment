
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
		<a href="{:session('manage.back_url')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">编辑品类</div>
		<div class="layui-form">
			<div class="layui-form-item">
				<label class="layui-form-label">上级节点</label>
				<div class="layui-input-block w300">
					<select name="parent_id" lay-verify="required">
						{foreach name="category" item="v"}
							<option value="{$v.id}" {if condition="$v.id eq $info['parent_id']"}selected{/if}>{$v.category_name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">节点名称</label>
				<div class="layui-input-inline w300">
					<input type="text" class="layui-input" name="name" value="{$info.name}" placeholder="请填写节点名称">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">节点标识</label>
				<div class="layui-input-inline w300">
					<input type="text" class="layui-input" name="code" value="{$info.code}" placeholder="请填写节点标识">
				</div>
			</div>
			<div class="layui-form-item">
				<div class="layui-input-block">
					<button class="layui-btn w200" lay-submit lay-filter="formCoding">提交保存</button>
				</div>
			</div>
		</div>
    </div>
</div>
<script>
layui.use(['form', 'jquery'], function(){
	var $ = layui.jquery,
		form = layui.form;

	// 提交
	form.on('submit(formCoding)', function(data){
		var text = $(this).text(),
			button = $(this);
		$('button').attr('disabled',true);
		button.text('请稍候...');
        axios.post("{:url('edit', ['id' => $info['id']])}", data.field)
            .then(function (response) {
                let res = response.data;
                if (res.code === 1) {
                    layer.alert(res.msg,{icon:1,closeBtn:0,title:false,btnAlign:'c',},function(){
                        location.href = "{:url('index')}";
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
