
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <a href="{:session('manage.back_url')}" class="layui-btn layui-btn-danger layui-btn-sm fr"><i class="layui-icon">&#xe603;</i>返回上一页</a>
        <div class="title">操作记录</div>

		<div class="layui-form">
			<table class="layui-table" lay-size="sm">
				<colgroup>
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
						<th class="tc">状态</th>
						<th class="tc">异常与备注</th>
						<th>预计时间</th>
						<th>操作时间</th>
						<th>操作人员</th>
						<th>操作IP</th>
					</tr>
				</thead>
				<tbody>
					{foreach name="list" item="v"}
						<tr>
							<td>{$v.export.export_no}</td>
							<td class="tc">
                                {if condition="$v.export_state eq 1"}
                                    待排柜
                                {elseif condition="$v.export_state eq 2"/}
                                    待分仓
                                {elseif condition="$v.export_state eq 3"/}
                                    待出运
                                {elseif condition="$v.export_state eq 4"/}
                                    待到港
                                {elseif condition="$v.export_state eq 5"/}
                                    待卸船
                                {elseif condition="$v.export_state eq 6"/}
                                    待派送
                                {elseif condition="$v.export_state eq 7"/}
                                    待上架
                                {elseif condition="$v.export_state eq 8"/}
                                    已上架
                                {elseif condition="$v.export_state eq 0"/}
                                    已废弃
                                {/if}
                            </td>
                            <td>
                                {if condition="!empty($v['abnormal'])"}
                                    <p class="red">{$v.abnormal}</p>
                                {/if}
                            </td>
							<td>{$v.et_date}</td>
							<td>{$v.created_at}</td>
							<td>{$v.account.nickname}</td>
							<td>{$v.created_ip}</td>
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

	});
</script>

{include file="public/footer" /}
