
{include file="public/header" /}

<style>
    .total {padding: 0 20px 0 0}
</style>
<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <div class="title">海外仓头程明细列表</div>
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
            <div class="layui-inline">
                <a class="layui-btn layui-btn-normal" href="{:url('warehouse_export', ['keyword' => $keyword])}"><i class="layui-icon">&#xe655;</i> 导出</a>
            </div>
        </form>

		<div class="layui-form">
            <span class="total">出运数量：{$sum|number_format=###}</span>
            <span class="total">出运金额：{$total|number_format=###, 2}</span>
			<table class="layui-table" lay-size="sm">
				<colgroup>
					<col>
					<col>
                    <col>
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
                        <th>外销合同号</th>
                        <th>易仓PO号</th>
                        <th>采购合同号</th>
                        <th>工厂代码</th>
                        <th>SKU</th>
                        <th>中文品名</th>
                        <th>采购单价</th>
                        <th>采购总数</th>
                        <th>采购合计</th>
                        <th>本票出运数量</th>
                        <th>本票出运合计</th>
					</tr>
				</thead>
				<tbody>
					{foreach name="list" item="v"}
						<tr>
							<td>{$v.remark}</td>
                            <td>{$v.po_code}</td>
                            <td>{$v.ref_no}</td>
                            <td>{$v.supplier_code}</td>
							<td>{$v.product_barcode}</td>
                            <td>{$v.product_title}</td>
                            <td class="tr">{$v.unit_price}</td>
                            <td class="tr">{$v.qty_expected}</td>
                            <td class="tr">{$v.payable_amount|number_format=###, 2}</td>
                            <td class="tr">{$v.sum}</td>
                            <td class="tr">{$v.sum * $v.unit_price|number_format=###, 2}</td>
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
});
</script>

{include file="public/footer" /}
