
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <a href="{:url('factory_claim')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">添加工厂索赔</div>
        <div class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">支付月份</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" id="month" name="month" value="{$month}" placeholder="核算月份">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">支付币种</label>
                <div class="layui-input-inline w300">
                    <input type="radio" name="currency" value="CNY" title="CNY" checked>
                    <input type="radio" name="currency" value="USD" title="USD">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">仓库SKU</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="sku" placeholder="请填写仓库SKU">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">总费用</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="total" placeholder="请填写总费用">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">类型</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="type" placeholder="请填写类型">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-inline w300">
                    <textarea name="content" placeholder="请输入内容" class="layui-textarea"></textarea>
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
    layui.use(['form', 'jquery', 'laydate'], function(){
        let $ = layui.jquery,
            form = layui.form,
            laydate = layui.laydate;

        //执行一个laydate实例
        laydate.render({
            elem: '#month' //指定元素
            ,type: 'month'
        });

        //监听提交
        form.on('submit(formCoding)', function(data){
            let text = $(this).text(),
                button = $(this);
            $('button').attr('disabled',true);
            button.text('请稍候...');
            axios.post("{:url('add')}", data.field)
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
