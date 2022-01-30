<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <title>FlowerMQ</title>
</head>

<body>
    <div class="layui-layout layui-layout-admin">
        <?php require 'common.php'; ?>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div style="padding: 15px;padding-bottom:50px;">
                <blockquote class="layui-elem-quote layui-text">
                    FlowerMQ是一个基于workerman和redis实现的消息队列小工具，用来实现代码解耦，异步执行。
                    当前版本: 项目地址: <a href="https://github.com/mrtwenty/flower" target="_blank">https://github.com/mrtwenty/flower</a>
                </blockquote>
                <div class="layui-bg-gray" style="padding:15px;">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md6">
                            <div class="layui-card">
                                <div class="layui-card-header">Flower MQ</div>
                                <div class="layui-card-body">
                                    <table width="100%">
                                        <colgroup>
                                            <col>
                                            <col>
                                            <col>
                                            <col>
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <td>Flower版本:</td>
                                                <td><?= $sys_info['flower']; ?></td>
                                                <td>PHP版本:</td>
                                                <td><?= $sys_info['php']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Workerman版本:</td>
                                                <td><?= $sys_info['workerman']; ?></td>
                                                <td>Redis版本:</td>
                                                <td><?= $redis_info['redis_version']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md6">
                            <div class="layui-card">
                                <div class="layui-card-header">Server</div>
                                <div class="layui-card-body">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td>redis_version</td>
                                                <td><?= $redis_info['redis_version']; ?></td>
                                                <td>tcp_port:</td>
                                                <td><?= $redis_info['tcp_port']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>os</td>
                                                <td><?= $redis_info['os']; ?></td>
                                                <td>config_file</td>
                                                <td><?= $redis_info['config_file']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md6">
                            <div class="layui-card">
                                <div class="layui-card-header">Stats</div>
                                <div class="layui-card-body">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td>total_connections_received</td>
                                                <td><?= $redis_info['total_connections_received']; ?></td>
                                                <td>total_commands_processed:</td>
                                                <td><?= $redis_info['total_commands_processed']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>instantaneous_ops_per_sec</td>
                                                <td><?= $redis_info['instantaneous_ops_per_sec']; ?></td>
                                                <td>keyspace_hits:</td>
                                                <td><?= $redis_info['keyspace_hits']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="layui-col-md6">
                            <div class="layui-card">
                                <div class="layui-card-header">Memory</div>
                                <div class="layui-card-body">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td>used_memory_rss_human</td>
                                                <td><?= $redis_info['used_memory_rss_human']; ?></td>
                                                <td>used_memory_human:</td>
                                                <td><?= $redis_info['used_memory_human']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>total_system_memory_human</td>
                                                <td><?= $redis_info['total_system_memory_human']; ?></td>
                                                <td>used_memory_peak_human:</td>
                                                <td><?= $redis_info['used_memory_peak_human']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md6">
                            <div class="layui-card">
                                <div class="layui-card-header">Client</div>
                                <div class="layui-card-body">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td>connected_clients</td>
                                                <td><?= $redis_info['connected_clients']; ?></td>
                                                <td>blocked_clients:</td>
                                                <td><?= $redis_info['blocked_clients']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="layui-footer">
            Flower Monitor
        </div>
    </div>
    <script src="/layui/layui.js"></script>
    <script>
        layui.use(['layer', 'form'], function() {
            var layer = layui.layer,
                form = layui.form;

            // layer.msg('Hello World');
        });
    </script>
</body>

</html>