<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\common\model\WXBizDataCrypt;

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

    public function phone()
    {        
        // 登陆
        $input = input();
        $model = new UserModel;
        $session = $model->login($this->request->post(), true);
        $session_key = $session['session_key'];        
        // 获取解密参数
        $encryptedData = $input['encrypted_data'];
        $iv = $input['iv'];
        $token = $model->getToken();
        $wxapp = \app\common\model\Wxapp::detail();
        $app_id = $wxapp['app_id'];
        // 解密
        $pc = new WXBizDataCrypt($app_id, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        $data = json_decode($data,true);
        return $this->renderSuccess(compact('data'));
    }

}
