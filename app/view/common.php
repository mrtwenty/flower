<div class="layui-header">
    <div class="layui-logo layui-hide-xs layui-bg-black">Flower MQ</div>
    <ul class="layui-nav layui-layout-right">
        <li class="layui-nav-item layui-hide layui-show-md-inline-block">
            <a href="javascript:;">
                <?= $user_info['username']; ?>
            </a>
            <dl class="layui-nav-child">
                <dd><a href="/logout">退出</a></dd>
            </dl>
        </li>
    </ul>
</div>

<div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
        <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
        <ul class="layui-nav layui-nav-tree" lay-filter="test">
            <li class="layui-nav-item"><a href="/index">系统信息</a></li>
            <?php foreach ($mq_list as $mq) { ?>
                <li class="layui-nav-item"><a href="/mq?name=<?= $mq ?>"><?= strtoupper($mq); ?> 监控 </a></li>
            <?php } ?>
        </ul>
    </div>
</div>