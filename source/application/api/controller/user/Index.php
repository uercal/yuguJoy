<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;

/**
 * 个人中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Index extends Controller
{
    /**
     * 获取当前用户信息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();        
        return $this->renderSuccess(compact('userInfo'));
    }


    /**
     * 获取是否绑定手机
     * 
     */
    public function bindPhone(){
        // 当前用户信息
        $userInfo = $this->getUser();        
        if($userInfo['phone_number']==''){
            $res = 0;
        }else{
            $res = 1;
        }
        return $this->renderSuccess(compact('res'));
    }

}
