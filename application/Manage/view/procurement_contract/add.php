
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
		<a href="{:session('manage.back_url')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">添加采购合同</div>
		<div class="layui-form">
			<div class="layui-form-item">
				<label class="layui-form-label">产品SKU</label>
				<div class="layui-input-block w300">
					<select name="sku_id" lay-verify="required">
						{foreach name="sku" item="v"}
							<option value="{$v.id}">{$v.sku}({$v.name})</option>
						{/foreach}
					</select>
				</div>
			</div>
            <div class="layui-form-item">
                <label class="layui-form-label">产品名称</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="product_name" placeholder="请填写产品名称">
                </div>
            </div>
			<div class="layui-form-item">
				<label class="layui-form-label">产品数量</label>
				<div class="layui-input-inline w300">
					<input type="text" class="layui-input" name="product_quantity" placeholder="请填写产品数量">
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
		let text = $(this).text(),
			button = $(this);
		$('button').attr('disabled',true);
		button.text('请稍候...');
        axios.post("{:url('add', ['id' => $id])}", data.field)
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
