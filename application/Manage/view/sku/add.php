
{include file="public/header" /}

<style>
    .sku-old {display: none}
</style>
<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
		<a href="{:session('manage.back_url')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">添加货号</div>
		<div class="layui-form">
			<div class="layui-form-item">
				<label class="layui-form-label">所属类目</label>
				<div class="layui-input-block w300">
					<select name="category_id" lay-verify="required">
						{foreach name="category" item="v"}
							<option value="{$v.id}" {if condition="$v.level lt 2"}disabled{/if}>{$v.category_name}</option>
						{/foreach}
					</select>
				</div>
			</div>
            <div class="layui-form-item">
                <label class="layui-form-label">所属属性</label>
                <div class="layui-input-block w300">
                    <select name="attribute_id" lay-verify="required">
                        {foreach name="attribute" item="va"}
                        <option value="{$va.id}" {if condition="$va.level lt 2"}disabled{/if}>{$va.attribute_name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">新货品</label>
                <div class="layui-input-inline w500">
                    <input type="radio" name="is_new" value="1" title="是" lay-filter="filter" checked>
                    <input type="radio" name="is_new" value="0" title="否" lay-filter="filter">
                </div>
            </div>
            <div class="layui-form-item sku-old">
                <label class="layui-form-label">货号序号</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="code" placeholder="请填写货号序号">
                </div>
            </div>
			<div class="layui-form-item">
				<label class="layui-form-label">货号名称</label>
				<div class="layui-input-inline w300">
					<input type="text" class="layui-input" name="name" placeholder="请填写货号名称">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">货号描述</label>
				<div class="layui-input-inline w300">
					<input type="text" class="layui-input" name="description" placeholder="请填写货号描述">
				</div>
			</div>
            <div class="layui-form-item">
                <label class="layui-form-label">装箱数</label>
                <div class="layui-input-inline w500">
                    <input type="radio" name="box" value="1" title="一箱装" checked>
                    <input type="radio" name="box" value="2" title="两箱装">
                    <input type="radio" name="box" value="3" title="三箱装">
                    <input type="radio" name="box" value="4" title="四箱装">
                    <input type="radio" name="box" value="5" title="五箱装">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">所属采购员</label>
                <div class="layui-input-block w300">
                    <select name="purchaser_id" lay-verify="required">
                        {foreach name="purchaser" item="vp"}
                        <option value="{$vp.id}" {if condition="$vp.id eq $user.id"}selected{/if}>{$vp.nickname}</option>
                        {/foreach}
                    </select>
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

    form.on('radio(filter)', function(data){
        if (data.value === "1") {
            $(".sku-old").hide();
        } else {
            $(".sku-old").show();
        }
    });

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
