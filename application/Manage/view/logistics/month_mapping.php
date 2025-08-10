
{include file="public/header" /}

<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="right">
        <div class="title">基础月份映射列表</div>

		<div class="layui-form">
			<table class="layui-table" lay-size="sm">
				<colgroup>
					<col>
					<col>
					<col>
					<col>
				</colgroup>
				<thead>
					<tr>
						<th class="tl">字符月份</th>
						<th class="tr">数值月份</th>
						<th class="tl">前置SKU月份代码</th>
						<th class="tl">SKU月份索引代码</th>
					</tr>
				</thead>
				<tbody>
					{foreach name="list" item="v"}
						<tr>
							<td class="tl">{$v.month_char}</td>
							<td class="tr">{$v.month_int}</td>
							<td class="tl">{$v.month_code_pre}</td>
							<td class="tl">{$v.month_code_index}</td>
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
