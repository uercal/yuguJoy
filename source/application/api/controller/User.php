<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\common\model\WXBizDataCrypt;
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
        // $user = UserModel::getUser($token);
        // if($user['phone_number']!=''){
        //     $bindPhone = 1;
        // }else{
        //     $bindPhone = 0;
        // }
        return $this->renderSuccess(compact('user_id', 'token'));
    }

    public function phone()
    {        
        // 登陆
        $input = input();
        $model = new UserModel;              
        $token = input('token');
        $session = Cache::get($token);        
        $session_key = $session['session_key'];
        // 获取解密参数
        $encryptedData = $input['encrypted_data'];
        $iv = $input['iv'];
        
        $wxapp = \app\common\model\Wxapp::detail();
        $app_id = $wxapp['app_id'];
        // 解密
        $pc = new WXBizDataCrypt($app_id, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        $data = json_decode($data,true);        
        // 绑定手机号        
        if($data['phoneNumber']!=''){
            $bind_phone = $model->updatePhoneNumber($session['openid'],$data['phoneNumber']);        
        }else{
            $bind_phone = 0;
        }        
        return $this->renderSuccess(compact('bind_phone'));
    }

}
