
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <a href="{:session('manage.back_url')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">编辑采购合同</div>
        <div class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">出口国家</label>
                <div class="layui-input-block w300">
                    <select name="state" lay-verify="required">
                        <option value="US" {if condition="$info.state eq 'US'"}selected{/if}>US（美国）</option>
                        <option value="SA" {if condition="$info.state eq 'SA'"}selected{/if}>SA（欧洲）</option>
                        <option value="GB" {if condition="$info.state eq 'GB'"}selected{/if}>GB（英国）</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">供应商代码</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="supplier_code" value="{$info.supplier_code}" disabled placeholder="请填写供应商代码">
                </div>
            </div>
            {foreach name="info.sku" item="v" key="k"}
            <div class="layui-form-item">
                <label class="layui-form-label">产品SKU</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="sku[]" value="{$v.sku}" placeholder="请填写产品SKU">
                </div>
                <label class="layui-form-label">产品数量</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="product_quantity[]" value="{$v.product_quantity}" placeholder="请填写产品数量">
                </div>
                {if condition="$k eq 0"}
                <button class="layui-btn layui-btn-sm btn-lc" lay-submit lay-filter="AttrAdd">添加</button>
                {else/}
                <button class="layui-btn layui-btn-sm layui-btn-danger btn-lc" lay-submit lay-filter="attrDel">删除</button>
                {/if}
            </div>
            {/foreach}
            <div class="layui-form-item" id="sub-dom">
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

    let domIndex = 0;
    // 添加属性
    form.on('submit(AttrAdd)', function(data) {
        domIndex ++;
        let newDom = '<div class="layui-form-item"><label class="layui-form-label">产品SKU</label><div class="layui-input-inline w300"><input type="text" class="layui-input" name="sku[' + domIndex + ']" placeholder="请填写产品SKU"></div><label class="layui-form-label">产品数量</label><div class="layui-input-inline w300"><input type="text" class="layui-input" name="product_quantity[' + domIndex + ']" placeholder="请填写产品数量"></div><button class="layui-btn layui-btn-sm layui-btn-danger btn-lc" lay-submit lay-filter="attrDel">删除</button></div>';
        $("#sub-dom").before(newDom);
        form.render();
        return false;
    });

    // 删除属性
    form.on('submit(attrDel)', function(data) {
        $(this).parent().remove();
    });

	// 提交
	form.on('submit(formCoding)', function(data){
		var text = $(this).text(),
			button = $(this);
		$('button').attr('disabled',true);
		button.text('请稍候...');
        axios.post("{:url('edit', ['id' => $info['id']])}", data.field, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(function (response) {
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
        }).catch(function (error) {
            console.log(error);
        });
		return false;
	});
});
</script>

{include file="public/footer" /}
