
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <div class="title">外销编号列表</div>
        <form class="layui-form search-form" method="get">
            <div class="layui-inline w200">
                <input type="text" class="layui-input" name="keyword" value="{$keyword}" placeholder="外销编号/提单号/箱号/目的仓">
            </div>
            <div class="layui-inline w120">
                <select name="state" lay-verify="">
                    <option value="-1">状态</option>
                    <option value="1" {if condition="$state eq 1"}selected{/if}>待排柜</option>
                    <option value="2" {if condition="$state eq 2"}selected{/if}>待分仓</option>
                    <option value="3" {if condition="$state eq 3"}selected{/if}>待出运</option>
                    <option value="4" {if condition="$state eq 4"}selected{/if}>待到港</option>
                    <option value="5" {if condition="$state eq 5"}selected{/if}>待卸船</option>
                    <option value="6" {if condition="$state eq 6"}selected{/if}>待派送</option>
                    <option value="7" {if condition="$state eq 7"}selected{/if}>待上架</option>
                    <option value="8" {if condition="$state eq 8"}selected{/if}>已上架</option>
                </select>
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
			<a class="layui-btn" href="{:url('add')}">添加</a>
			<table class="layui-table" lay-size="sm">
				<colgroup>
					<col width="180">
					<col>
					<col>
					<col>
					<col>
					<col>
					<col>
					<col>
					<col>
					<col width="100">
					<col width="150">
				</colgroup>
				<thead>
					<tr>
						<th>外销编号</th>
						<th>始发港</th>
						<th>目的港</th>
						<th>提单号</th>
						<th>箱号</th>
						<th>船公司</th>
						<th>开船时间</th>
						<th>待到港时间</th>
						<th>创建时间</th>
						<th>跟单人员</th>
						<th class="tc">状态</th>
					</tr>
				</thead>
				<tbody>
					{foreach name="list" item="v"}
						<tr>
							<td>{$v.export_no}</td>
							<td>{$v.fromPort.name}({$v.fromPort.code})</td>
							<td>{$v.toPort.name}({$v.toPort.code}）</td>
							<td>{$v.bol_no}</td>
							<td>{$v.box_no}</td>
							<td>{$v.shipping_company}</td>
							<td>{$v.shipping_date}</td>
							<td>{$v.eta}</td>
							<td>{$v.created_at}</td>
							<td>{$v.account.nickname}</td>
							<td class="tc">
                                {if condition="$v.state eq 1"}
                                    <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-normal layui-btn-sm">待排柜</a>
                                {elseif condition="$v.state eq 2"/}
                                    <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-normal layui-btn-sm">待分仓</a>
                                {elseif condition="$v.state eq 3"/}
                                    <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-normal layui-btn-sm">待出运</a>
                                {elseif condition="$v.state eq 4"/}
                                    <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-normal layui-btn-sm">待到港</a>
                                {elseif condition="$v.state eq 5"/}
                                    <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-normal layui-btn-sm">待卸船</a>
                                {elseif condition="$v.state eq 6"/}
                                    <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-normal layui-btn-sm">待派送</a>
                                {elseif condition="$v.state eq 7"/}
                                    <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-normal layui-btn-sm">待上架</a>
                                {elseif condition="$v.state eq 8"/}
                                    <a href="{:url('edit', ['id' => $v.id])}" class="layui-btn layui-btn-sm">已上架</a>
                                {/if}
                                <a href="{:url('log', ['id' => $v.id])}" class="layui-btn layui-btn-sm">记录</a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

    </div>
</div>
<script>
layui.use(['form', 'jquery'], function(){
    let $ = layui.jquery,
        form = layui.form;

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
