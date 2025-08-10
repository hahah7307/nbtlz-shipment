
{include file="public/header" /}

<style>
    .total {padding: 0 20px 0 0}
</style>
<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <div class="title">出运SKU明细列表</div>
        <form class="layui-form search-form" method="get">
            <div class="layui-inline w200">
                <input type="text" class="layui-input" name="keyword" value="{$keyword}" placeholder="">
            </div>
            <div class="layui-inline w100">
                <input type="text" class="layui-input" name="page_num" value="{$page_num}" placeholder="每页条数">
            </div>
            <div class="layui-inline">
                <button class="layui-btn" lay-submit lay-filter="Search"><i class="layui-icon">&#xe615;</i> 查询</button>
            </div>
        </form>

		<div class="layui-form">
			<table class="layui-table" lay-size="sm">
                <colgroup>
                    <col class="w80">
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>系统ID号</th>
                    <th>外销编号</th>
                    <th>仓库SKU</th>
                    <th>原始SKU</th>
                    <th>系统生成SKU</th>
                    <th>同步月份</th>
                    <th>同步日期</th>
                    <th>SKU序列号</th>
                    <th>同步时间</th>
                </tr>
                </thead>
                <tbody>
                {foreach name="list" item="v"}
                <tr>
                    <td class="tr">{$v.id}</td>
                    <td>{$v.export_no}</td>
                    <td>{$v.warehouse_sku}</td>
                    <td>{$v.origin_sku}</td>
                    <td>{$v.new_sku}</td>
                    <td class="tr">{$v.created_month}</td>
                    <td class="tr">{$v.created_date}</td>
                    <td class="tr">{$v.month_index}</td>
                    <td>{$v.notify_date}</td>
                </tr>
                {/foreach}
                </tbody>
			</table>
            {$list->render()}
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

    // 排序
	form.on('submit(Sort)', function(data){
		var text = $(this).text(), button = $(this);
		$('button').attr('disabled',true);
		button.text('请稍候...');
		$.ajax({
			type:'POST',url:"{:url('sort')}",data:data.field,dataType:'json',
			success:function(data){
				if(data.code === 1){
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
});
</script>

{include file="public/footer" /}
