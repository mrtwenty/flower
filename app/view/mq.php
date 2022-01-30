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
                <div class="layui-bg-gray" style="padding:15px;">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md4">
                            <div class="layui-card">
                                <div class="layui-card-header">配置和实时状态</div>
                                <div class="layui-card-body">
                                    <table width="100%">
                                        <tr>
                                            <td>状态标识:</td>
                                            <td><?= $config['status'] ?></td>
                                            <td>实时状态:</td>
                                            <td><?= $info['mq_status'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>队列名:</td>
                                            <td><?= $config['name'] ?></td>
                                            <td>实时总数:</td>
                                            <td><?= $info['mq_len'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>失败列表:</td>
                                            <td><?= $config['fail_list'] ?></td>
                                            <td>实时总数:</td>
                                            <td><?= $info['fail_list_len']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>延迟队列:</td>
                                            <td><?= $config['delay_name'] ?></td>
                                            <td>实时总数:</td>
                                            <td><?= $info['mq_delay_len']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>消费组名:</td>
                                            <td><?= $config['group_name'] ?></td>
                                            <td>消费进程数:</td>
                                            <td><?= $config['consumer_num'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>重试次数:</td>
                                            <td><?= $config['try_fail_num'] ?></td>
                                            <td>重试间隔(秒):</td>
                                            <td><?= $config['try_fail_second'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>最大队列长度:</td>
                                            <td><?= $config['maxlen'] ?></td>
                                            <td>GC回收:</td>
                                            <td><?= $config['gc_probability'] ?>/<?= $config['gc_divisor'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md4">
                            <div class="layui-card">
                                <div class="layui-card-header">Stream信息</div>
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
                                                <td>length</td>
                                                <td><?= $info['mq_info']['length'] ?></td>
                                                <td>radix-tree-keys</td>
                                                <td><?= $info['mq_info']['radix-tree-keys'] ?></td>

                                            </tr>
                                            <tr>
                                                <td>radix-tree-nodes</td>
                                                <td><?= $info['mq_info']['radix-tree-nodes'] ?></td>
                                                <td>last-generated-id</td>
                                                <td><?= $info['mq_info']['last-generated-id'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>groups</td>
                                                <td><?= $info['mq_info']['groups'] ?></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md4">
                            <div class="layui-card">
                                <div class="layui-card-header">Stream的Groups信息</div>
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
                                                <td>name</td>
                                                <td>consumers</td>
                                                <td>pending</td>
                                                <td>last-delivered-id</td>
                                            </tr>
                                            <?php foreach ($info['group_info'] as $group) { ?>
                                                <tr>
                                                    <td><?= $group['name'] ?></td>
                                                    <td><?= $group['consumers'] ?></td>
                                                    <td><?= $group['pending'] ?></td>
                                                    <td><?= $group['last-delivered-id'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <fieldset class="layui-elem-field layui-field-title site-title">
                    <legend><a name="default">Consumers信息:</a></legend>
                </fieldset>
                <?php foreach ($info['consumers_info'] as $key => $group) { ?>
                    <h4>所属组: <?= $key; ?></h4>
                    <table class="layui-table">
                        <tr>
                            <td>name</td>
                            <td>pending</td>
                            <td>idle</td>
                        </tr>
                        <?php foreach ($group as $consumer) { ?>
                            <tr>
                                <td><?= $consumer['name'] ?></td>
                                <td><?= $consumer['pending'] ?></td>
                                <td><?= $consumer['idle'] ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>
                <fieldset class="layui-elem-field layui-field-title site-title">
                    <legend><a name="default">失败列表:</a></legend>
                </fieldset>
                <blockquote class="layui-elem-quote layui-text">
                    那些超过了重试次数，依然无法消费的消息，都存放在列表里，需要维护者手动处理。
                </blockquote>
                <h3>失败消息，最新10条</h3>
                <table class="layui-table">
                    <tr>
                        <td>时间</td>
                        <td>消息</td>
                    </tr>
                    <?php foreach ($info['fail_list_10'] as $fail_msg) { ?>
                        <tr>
                            <td><?= $fail_msg['time'] ?></td>
                            <td><?= $fail_msg['data'] ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <fieldset class="layui-elem-field layui-field-title site-title">
                    <legend><a name="default">延迟消息:</a></legend>
                </fieldset>
                <table class="layui-table">
                    <tr>
                        <td width="160">时间</td>
                        <td>消息</td>
                    </tr>
                    <?php foreach ($info['mq_delay_10'] as $delay_msg) { ?>
                        <tr>
                            <td><?= $delay_msg['time'] ?></td>
                            <td><?= $delay_msg['data'] ?></td>
                        </tr>
                    <?php } ?>
                </table>
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