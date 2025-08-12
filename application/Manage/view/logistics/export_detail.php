
{include file="public/header" /}

<style>
    .total {padding: 0 20px 0 0}
</style>
<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <a href="{:session('manage.back_url')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">出运明细列表</div>
        <form class="layui-form search-form" method="get">
            <div class="layui-inline">
                <a class="layui-btn layui-btn-normal" href="{:url('export_detail_export', ['id' => $id])}"><i class="layui-icon">&#xe655;</i> 导出</a>
            </div>
            <div class="layui-inline">
                <button type="button" class="layui-btn layui-btn-normal" lay-submit lay-filter="Copy"><i class="layui-icon">&#xe621;</i> 复制</button>
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

    // 复制
    form.on('submit(Copy)', function(data){
        const text = `{$newSku}`;
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';  // 防止页面跳动
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        console.log(textarea);

        textarea.focus();
        textarea.select();

        try {
            const success = document.execCommand('copy');
            if (success) {
                layer.msg('复制成功', {icon: 6});
            } else {
                layer.msg('复制失败，请手动复制', {icon: 5});
            }
        } catch (err) {
            layer.msg('复制失败，请手动复制', {icon: 5});
        }

        document.body.removeChild(textarea);

        return false;
	});
});
</script>

{include file="public/footer" /}
