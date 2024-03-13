
{include file="public/header" /}

<style>
    .layui-content {margin-top: 10px; color: #6d737b}
</style>
<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
		<a href="{:session('manage.back_url')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">编辑外销编号</div>
		<div class="container">
			<div class="layui-form-item">
				<label class="layui-form-label">外销编号</label>
				<div class="layui-input-inline w300">
                    <div class="layui-content">
                        {$info.export_no}
                    </div>
				</div>
			</div>
            <div class="layui-form-item">
                <label class="layui-form-label">始发港</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.fromPort.name}({$info.fromPort.code})</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">目的港</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.toPort.name}({$info.toPort.code})</div>
                </div>
            </div>
            {if condition="$info.container_date"}
            <div class="layui-form-item">
                <label class="layui-form-label">排柜时间</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.container_date}</div>
                </div>
            </div>
            {/if}
            {if condition="$info.compartment_date"}
            <div class="layui-form-item">
                <label class="layui-form-label">分仓时间</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.compartment_date}</div>
                </div>
            </div>
            {/if}
            {if condition="$info.shipping_date"}
            <div class="layui-form-item">
                <label class="layui-form-label">开船时间</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.shipping_date}</div>
                </div>
            </div>
            {/if}
            {if condition="$info.arrival_date or $info.eta"}
            <div class="layui-form-item">
                {if condition="$info.arrival_date"}
                <label class="layui-form-label">实际到港</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.arrival_date}</div>
                </div>
                {/if}
                {if condition="$info.eta"}
                <label class="layui-form-label">预计到港</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.eta}</div>
                </div>
                {/if}
            </div>
            {/if}
            {if condition="$info.unloading_date"}
            <div class="layui-form-item">
                <label class="layui-form-label">卸船时间</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.unloading_date}</div>
                </div>
            </div>
            {/if}
            {if condition="$info.dispatch_date or $info.etd"}
            <div class="layui-form-item">
                {if condition="$info.dispatch_date"}
                <label class="layui-form-label">实际派送</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.dispatch_date}</div>
                </div>
                {/if}
                {if condition="$info.etd"}
                <label class="layui-form-label">预计派送</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.etd}</div>
                </div>
                {/if}
            </div>
            {/if}
            {if condition="$info.grounding_date"}
            <div class="layui-form-item">
                <label class="layui-form-label">上架时间</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.grounding_date}</div>
                </div>
            </div>
            {/if}
            {if condition="$info.discard_date"}
            <div class="layui-form-item">
                <label class="layui-form-label">废弃时间</label>
                <div class="layui-input-inline w300">
                    <div class="layui-content">{$info.discard_date}</div>
                </div>
            </div>
            {/if}
		</div>

        <div class="title"></div>
        <div class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block w300">
                    <select name="state" lay-verify="required">
                        <option value="0" {if condition="$info.state eq 0"}selected{/if}>已废弃</option>
                        <option value="1" {if condition="$info.state eq 1"}selected{/if}>待排柜</option>
                        <option value="2" {if condition="$info.state eq 2"}selected{/if}>待分仓</option>
                        <option value="3" {if condition="$info.state eq 3"}selected{/if}>待出运</option>
                        <option value="4" {if condition="$info.state eq 4"}selected{/if}>待到港</option>
                        <option value="5" {if condition="$info.state eq 5"}selected{/if}>待卸船</option>
                        <option value="6" {if condition="$info.state eq 6"}selected{/if}>待派送</option>
                        <option value="7" {if condition="$info.state eq 7"}selected{/if}>待上架</option>
                        <option value="8" {if condition="$info.state eq 8"}selected{/if}>已上架</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">异常</label>
                <div class="layui-input-block w300">
                    <select name="abnormal">
                        <option></option>
                        {foreach name="abnormal" key="k" item="v"}
                        <option value="{$k}">{$v}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            {if condition="$info.state eq 2"}
            <div class="layui-form-item">
                <label class="layui-form-label">目的仓</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="warehouse" value="{$info.warehouse}" placeholder="请填写目的仓">
                </div>
            </div>
            {/if}
            {if condition="$info.state eq 3"}
            <div class="layui-form-item">
                <label class="layui-form-label">提单号</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="bol_no" value="{$info.bol_no}" placeholder="请填写提单号">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">箱号</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" name="box_no" value="{$info.box_no}" placeholder="请填写箱号">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">船公司</label>
                <div class="layui-input-inline w300">
                    <select name="shipping_company">
                        <option value="">请选择</option>
                        <option value="COSCO" {if condition="$info.shipping_company eq COSCO"}selected{/if}>COSCO</option>
                        <option value="EMC" {if condition="$info.shipping_company eq EMC"}selected{/if}>EMC</option>
                        <option value="OOCL" {if condition="$info.shipping_company eq OOCL"}selected{/if}>OOCL</option>
                        <option value="CMA" {if condition="$info.shipping_company eq CMA"}selected{/if}>CMA</option>
                    </select>
                </div>
            </div>
            {/if}
            {if condition="$info.state eq 3 or $info.state eq 4"}
            <div class="layui-form-item">
                <label class="layui-form-label">预计到港</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" id="eta" name="eta" placeholder="请选择时间" value="{$info.eta}">
                </div>
            </div>
            {/if}
            {if condition="$info.state eq 6"}
            <div class="layui-form-item">
                <label class="layui-form-label">预计派送</label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" id="etd" name="etd" placeholder="请选择时间" value="{$info.etd}">
                </div>
            </div>
            {/if}
            <div class="layui-form-item">
                <label class="layui-form-label">
                    {if condition="$info.state eq 1"}
                        排柜时间
                    {elseif condition="$info.state eq 2"/}
                        分仓时间
                    {elseif condition="$info.state eq 3"/}
                        开船时间
                    {elseif condition="$info.state eq 4"/}
                        实际到港
                    {elseif condition="$info.state eq 5"/}
                        卸船时间
                    {elseif condition="$info.state eq 6"/}
                        实际派送
                    {elseif condition="$info.state eq 7"/}
                        上架时间
                    {elseif condition="$info.state eq 8"/}
                        操作时间
                    {/if}
                </label>
                <div class="layui-input-inline w300">
                    <input type="text" class="layui-input" id="time" name="time" placeholder="不选择默认为当前时间">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">采购</label>
                <div class="layui-input-inline w500">
                    {foreach name="procure" item="p"}
                        <input type="checkbox" name="procure[]" title="{$p.account.nickname}" value="{$p.user_id}" lay-skin="primary" {if condition="in_array($p.user_id, $procure_group)"}checked{/if}>
                    {/foreach}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-inline w300">
                    <textarea name="content" placeholder="备注" class="layui-textarea">{$info.content}</textarea>
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

    // 显示日期选择器
    laydate.render({
        elem: '#eta',
        type: 'datetime'
    });

    // 显示日期选择器
    laydate.render({
        elem: '#etd',
        type: 'datetime'
    });

    // 显示日期选择器
    laydate.render({
        elem: '#time',
        type: 'datetime'
    });

	// 提交
	form.on('submit(formCoding)', function(data){
		let text = $(this).text(),
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
                        location.href = "{:session('manage.back_url')}";
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
