<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">首页数据设置</div>
                            </div>                            
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label">
                                    轮播图是否显示小黑点:
                                </label>
                                <div class="am-u-sm-9">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="other[indicatorDots]" value="true" data-am-ucheck <?php if($jsonData['indicatorDots']): ?> 
                                        checked <?php endif; ?> > 开启
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="other[indicatorDots]" value="false" data-am-ucheck <?php if(!$jsonData['indicatorDots']): ?> 
                                        checked <?php endif; ?> > 关闭
                                    </label>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label">
                                    已服务用户数量:
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="tpl-form-input" name="other[server_char]" value="<?= $jsonData['server_char'] ?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
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
        $('#my-form').superForm();

    });
</script>
