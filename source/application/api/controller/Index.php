<?php

namespace app\api\controller;

use app\api\model\WxappPage;
use app\api\model\Goods as GoodsModel;

/**
 * 首页控制器
 * Class Index
 * @package app\api\controller
 */
class Index extends Controller
{
    /**
     * 首页diy数据
     * @return array
     * @throws \think\exception\DbException
     */
    public function page()
    {
        // 页面元素
        $wxappPage = WxappPage::detail();
        $items = $wxappPage['page_data']['array']['items'];
        $other = json_decode($wxappPage['page_other_data'],true);
        $other['indicatorDots'] = $other['indicatorDots'];
        // // 新品推荐
        $model = new GoodsModel;
        // $newest = $model->getNewList();
        // // 猜您喜欢
        // $best = $model->getBestList();

        // 家具服务
        $list = $model->getIndexList();        
        
        return $this->renderSuccess(compact('items', 'list','other'));
    }

}
