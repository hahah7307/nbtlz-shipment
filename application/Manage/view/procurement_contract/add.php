
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
		<a href="{:session('manage.back_url')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">添加采购合同</div>
		<div class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">供应商代码</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="supplier_code" placeholder="请填写供应商代码">
                </div>
            </div>
			<div class="layui-form-item">
                <label class="layui-form-label">产品SKU</label>
                <div class="layui-input-inline w300">
                    <select name="sku_id[]" lay-verify="required" class="sku-list">
                        {foreach name="sku" item="v"}
                        <option value="{$v.id}">{$v.sku}({$v.name})</option>
                        {/foreach}
                    </select>
                </div>
                <label class="layui-form-label">产品数量</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="product_quantity[]" placeholder="请填写产品数量">
                </div>
                <button class="layui-btn layui-btn-sm btn-lc" lay-submit lay-filter="AttrAdd">添加</button>
			</div>
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
	let $ = layui.jquery,
		form = layui.form;

    let dom = $('.sku-list').html();
    let domIndex = 0;
    console.log(dom);

    // 添加属性
    form.on('submit(AttrAdd)', function(data) {
        domIndex ++;
        let newDom = '<div class="layui-form-item"><label class="layui-form-label">产品SKU</label><div class="layui-input-inline w300"><select name="sku_id[' + domIndex + ']" lay-verify="required" class="sku-list">'
            + dom
            + '</select></div><label class="layui-form-label">产品数量</label><div class="layui-input-inline w300"><input type="text" class="layui-input" name="product_quantity[' + domIndex + ']" placeholder="请填写产品数量"></div><button class="layui-btn layui-btn-sm layui-btn-danger btn-lc" lay-submit lay-filter="attrDel">删除</button></div>';
        // let newDom = '<div class="layui-form-item product-sku-num"><div class="product-sku-num">' + dom + '<button class="layui-btn layui-btn-sm layui-btn-danger btn-lc" lay-submit lay-filter="attrDel">删除</button></div></div>';
        $("#sub-dom").before(newDom);
        form.render();
        // timeAdd();
        return false;
    });

    // 删除属性
    form.on('submit(attrDel)', function(data) {
        $(this).parent().remove();
    });

	// 提交
	form.on('submit(formCoding)', function(data){
		let text = $(this).text(),
			button = $(this);
		$('button').attr('disabled',true);
		button.text('请稍候...');
        $.ajax({
            type:'POST',url:"{:url('add', ['id' => $id])}",data:data.field,dataType:'json',
            success:function(data){
                if(data.code === 1){
                    layer.alert(data.msg,{icon:1,closeBtn:0,title:false,btnAlign:'c'},function(){
                        location.href = "{:url('index')}";
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
});
</script>

{include file="public/footer" /}
