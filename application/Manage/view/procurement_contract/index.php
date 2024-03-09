
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <div class="title">采购合同列表</div>
        <form class="layui-form search-form" method="get">
            <div class="layui-inline w200">
                <input type="text" class="layui-input" name="keyword" value="{$keyword}" placeholder="合同号/SKU/产品名称">
            </div>
            <div class="layui-inline w100">
                <input type="text" class="layui-input" name="page_num" value="{$page_num}" placeholder="每页条数">
            </div>
            <div class="layui-inline">
                <button class="layui-btn" lay-submit lay-filter="Search"><i class="layui-icon">&#xe615;</i> 查询</button>
            </div>
            <div class="layui-inline">
                <a class="layui-btn layui-btn-normal" href="{:url('index')}"><i class="layui-icon">&#xe669;</i> 重置</a>
            </div>
        </form>

		<div class="layui-form">
            {if condition="in_array(7, $role) or $user.super"}
			<a class="layui-btn" href="{:url('add')}">添加</a>
            {/if}
			<table class="layui-table" lay-size="sm">
				<colgroup>
					<col width="200">
					<col>
					<col>
					<col>
                    {if condition="in_array(8, $role) or $user.super"}
					<col width="100">
                    {/if}
                    {if condition="$user.super"}
					<col width="80">
                    {/if}
				</colgroup>
				<thead>
					<tr>
						<th>合同编号</th>
						<th>Sku</th>
						<th>产品名称</th>
						<th>产品数量</th>
                        {if condition="in_array(8, $role) or $user.super"}
						<th>采购人员</th>
                        {/if}
                        {if condition="$user.super"}
						<th class="tc">操作</th>
                        {/if}
					</tr>
				</thead>
				<tbody>
					{foreach name="list" item="v"}
						<tr>
							<td>{$v.contract_no}</td>
							<td>{$v.product_sku}</td>
							<td>{$v.product_name}</td>
							<td>{$v.product_quantity}</td>
                            {if condition="in_array(8, $role) or $user.super"}
							<td>{$v.account.nickname}</td>
                            {/if}
                            {if condition="$user.super"}
                            <td class="tc">
                                <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-normal layui-btn-sm">编辑</a>
                            </td>
                            {/if}
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

    </div>
</div>
<script>
layui.use(['form', 'jquery'], function(){
	var $ = layui.jquery,
		form = layui.form;

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
