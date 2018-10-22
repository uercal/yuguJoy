<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-body  am-margin-bottom-lg">
                    <div class="am-u-sm-12">
                        <?php
                        // 计算当前步骤位置
                        $progress = 2;
                        $detail['pay_status']['value'] === 20 && $progress += 1;
                        $detail['delivery_status']['value'] === 20 && $progress += 1;
                        // $detail['receipt_status']['value'] === 20 && $progress += 1;
                        $detail['order_status']['value'] === 30 && $progress += 1;
                        ?>
                        <ul class="order-detail-progress progress-<?= $progress ?>">
                            <li>
                                <span>下单时间</span>
                                <div class="tip"><?= $detail['create_time'] ?></div>
                            </li>
                            <li>
                                <span>付款</span>
                                <?php if ($detail['pay_status']['value'] === 20) : ?>
                                    <div class="tip">
                                        付款于 <?= date('Y-m-d H:i:s', $detail['pay_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                            <li>
                                <span>受理</span>
                                <?php if ($detail['delivery_status']['value'] === 20) : ?>
                                    <div class="tip">
                                        受理于 <?= date('Y-m-d H:i:s', $detail['delivery_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>                            
                            <li>
                                <span>完成</span>
                                <?php if ($detail['order_status']['value'] === 30) : ?>
                                    <div class="tip">
                                        完成于 <?= date('Y-m-d H:i:s', $detail['receipt_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">基本信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>订单号</th>
                                <th>实付款</th>
                                <th>买家</th>
                                <th>交易状态</th>
                            </tr>
                            <tr>
                                <td><?= $detail['order_no'] ?></td>
                                <td>
                                    <p>￥<?= $detail['pay_price'] ?></p>
                                    <p class="am-link-muted">(含运费：￥<?= $detail['express_price'] ?>)</p>
                                </td>
                                <td>
                                    <p><img src="<?= $detail['user']['avatarUrl'] ?>" style="width:50px;"></p>
                                    <p><?= $detail['user']['nickName'] ?></p>
                                    <p class="am-link-muted">(用户id：<?= $detail['user']['user_id'] ?>)</p>
                                </td>
                                <td>
                                    <p>付款状态：
                                        <span class="am-badge
                                        <?= $detail['pay_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['pay_status']['text'] ?></span>
                                    </p>
                                    <p>受理状态：
                                        <span class="am-badge
                                        <?= $detail['delivery_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['delivery_status']['text'] ?></span>
                                    </p>                                    
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">服务信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>服务名称</th>                                                            
                                <th>单价</th>
                                <th>购买数量</th>
                                <th>服务总价</th>
                            </tr>
                            <?php foreach ($detail['goods'] as $goods) : ?>
                                <tr>
                                    <td class="goods-detail am-text-middle">
                                        <div class="goods-image">
                                            <img src="<?= $goods['image']['file_path'] ?>" alt="">
                                        </div>
                                        <div class="goods-info">
                                            <p class="goods-title"><?= $goods['goods_name'] ?></p>
                                            <p class="goods-spec am-link-muted">
                                                <?= $goods['goods_attr'] ?>
                                            </p>
                                        </div>
                                    </td>                                                               
                                    <td>￥<?= $goods['goods_price'] ?></td>
                                    <td>×<?= $goods['total_num'] ?></td>
                                    <td>￥<?= $goods['total_price'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="6" class="am-text-right">总计金额：￥<?= $detail['total_price'] ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">地址信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>姓名</th>
                                <th>电话</th>
                                <th>服务地址</th>
                            </tr>
                            <tr>
                                <td><?= $detail['address']['name'] ?></td>
                                <td><?= $detail['address']['phone'] ?></td>
                                <td>
                                    <?= $detail['address']['region']['province'] ?>
                                    <?= $detail['address']['region']['city'] ?>
                                    <?= $detail['address']['region']['region'] ?>
                                    <?= $detail['address']['detail'] ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($detail['pay_status']['value'] === 20) : ?>
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">付款信息</div>
                        </div>
                        <div class="am-scrollable-horizontal">
                            <table class="regional-table am-table am-table-bordered am-table-centered
                                am-text-nowrap am-margin-bottom-xs">
                                <tbody>
                                <tr>
                                    <th>应付款金额</th>
                                    <th>支付方式</th>
                                    <th>支付流水号</th>
                                    <th>付款状态</th>
                                    <th>付款时间</th>
                                </tr>
                                <tr>
                                    <td>￥<?= $detail['pay_price'] ?></td>
                                    <td>微信支付</td>
                                    <td><?= $detail['transaction_id'] ? : '--' ?></td>
                                    <td>
                                        <span class="am-badge
                                        <?= $detail['pay_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['pay_status']['text'] ?></span>
                                    </td>
                                    <td>
                                        <?= $detail['pay_time'] ? date('Y-m-d H:i:s', $detail['pay_time']) : '--' ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if ($detail['pay_status']['value'] === 20) : ?>
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">操作</div>
                        </div>

                        <?php if ($detail['delivery_status']['value'] === 10) : ?>
                            <!-- 去受理 -->
                            <form id="delivery" class="my-form am-form tpl-form-line-form" method="post"
                                  action="<?= url('order/delivery', ['order_id' => $detail['order_id']]) ?>">
                                <!-- <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">物流公司名称 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="order[express_company]"
                                               required>
                                        <small>如：顺丰速运、申通快递</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">物流单号 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="order[express_no]" required>
                                    </div>
                                </div> -->
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                        <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary">
                                            确认受理
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php else : ?>
                            <div class="am-scrollable-horizontal">
                                <table class="regional-table am-table am-table-bordered am-table-centered
                                am-text-nowrap am-margin-bottom-xs">
                                    <tbody>
                                    <tr>                                    
                                        <th>受理时间</th>
                                    </tr>
                                    <tr>                                        
                                        <td>
                                            <?= date('Y-m-d H:i:s', $detail['delivery_time']) ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif;
                        endif; ?>     

                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('.my-form').superForm();

    });
</script>
