<?php

namespace app\store\model;

use app\common\model\WxappPage as WxappPageModel;

/**
 * 微信小程序diy页面模型
 * Class WxappPage
 * @package app\common\model
 */
class WxappPage extends WxappPageModel
{

    /**
     * 更新页面数据
     * @param $page_data
     * @return bool
     */
    public function edit($page_data)
    {
        // 删除wxapp缓存
        Wxapp::deleteCache();
        return $this->save(compact('page_data')) !== false;
    }

    public function editOther($other){
        $other['indicatorDots'] == "true" ? $other['indicatorDots'] = true : $other['indicatorDots'] = false;
        $data = json_encode($other);
        return $this->save([
            'page_other_data'=>$data
        ]) !==false;

    }

}
