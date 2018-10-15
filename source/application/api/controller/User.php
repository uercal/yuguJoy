<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use think\Cache;

/**
 * 用户管理
 * Class User
 * @package app\api
 */
class User extends Controller
{
    /**
     * 用户自动登录
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login()
    {
        $model = new UserModel;
        $user_id = $model->login($this->request->post());        
        $token = $model->getToken();
        return $this->renderSuccess(compact('user_id', 'token'));
    }

    public function doFavorite(){
        $input = input();
        $model = new UserModel;
        $res = $model->doFavorite($input);
        return $this->renderSuccess(compact('res'));
    }

    public function getFavorite(){
        $model = new UserModel;
        $user_id = input('user_id');        
        $data = $model->getFavorite($user_id);
        return $this->renderSuccess(compact('data'));
    }
}
