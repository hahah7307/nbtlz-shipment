
    <!-- 侧边菜单 -->
    <div class="layui-side layui-side-menu" id="layui-side-menu">
        <div class="layui-side-scroll">
            <a class="layui-logo" layui-href="/Manage">
                <span><img src="" height="40"></span>
            </a>
          
            <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                <li data-name="Home" class="layui-nav-item layui-nav-itemed">
                    <a layui-href="/Manage/Index/index.html" lay-tips="控制台" lay-direction="2">
                        <i class="layui-icon layui-icon-home"></i>
                        <cite>控制台</cite>
                    </a>
                </li>
                <li data-name="Storage" class="layui-nav-item">
                    <a layui-href="javascript:;" lay-tips="货号管理" lay-direction="2">
                        <i class="layui-icon iconfont icon-dingdan1"></i>
                        <cite>货号管理</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a layui-href="{:url('Sku/index')}">货号领取</a></dd>
                    </dl>
                </li>
                {if condition="$user.super eq 1"}
                <li data-name="Storage" class="layui-nav-item">
                    <a layui-href="javascript:;" lay-tips="基础" lay-direction="2">
                        <i class="layui-icon iconfont icon-jichugongneng"></i>
                        <cite>基础</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a layui-href="{:url('Category/index')}">品类管理</a></dd>
                    </dl>
                </li>
                <li data-name="Site" class="layui-nav-item">
                    <a layui-href="javascript:;" lay-tips="设置" lay-direction="2">
                        <i class="layui-icon layui-icon-set"></i>
                        <cite>设置</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a layui-href="{:url('Param/web')}">参数配置</a></dd>
                        <!-- <dd><a layui-href="{:url('Mail/index')}">邮件设置</a></dd> -->
                        {if condition="$user.super eq 1"}
                        <dd data-name="info">
                            <a layui-href="javascript:;">管理设置</a>
                            <dl class="layui-nav-child">
                                <dd><a layui-href="{:url('Admin/index')}">管理员</a></dd>
                                <dd><a layui-href="{:url('Admin/role')}">角色</a></dd>
                                {if condition="$user.manage eq 1"}
                                <dd><a layui-href="{:url('Admin/node')}">节点</a></dd>
                                {/if}
                            </dl>
                        </dd>
                        {/if}
                    </dl>
                </li>
                {/if}
            </ul>
        </div>
    </div>
    <script type="text/javascript">
    layui.use(['jquery'], function(){
        var $ = layui.jquery;

        if ('{$userMenu}') {
            $("#layui-side-menu").html('{$userMenu}');
            $(".layui-nav-bar").remove();
        }

        $("#layui-side-menu a").click(function(){
            $('dd').removeClass('layui-this');
            if ($(this).attr('layui-href') != 'javascript:;') {
                $(this).parent('dd').addClass('layui-this');
            }
            var html = $("#layui-side-menu").html(),
                href = $(this).attr('layui-href');

            $.ajax({
                type:'POST',url:"{:url('Index/initMenu')}",data:{"info": html},dataType:'json',
                success:function(data){
                    if(data.code == 1){
                        location.href = href;
                    }
                }
            });
        });
    });
    </script>
