<?php

namespace app\store\controller;

use app\store\model\User as UserModel;

/**
 * 用户管理
 * Class User
 * @package app\store\controller
 */
class User extends Controller
{
    /**
     * 用户列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new UserModel;
        $res = $model->getList();  
        $list = $res['data'];
        $map = $res['map'];       
        return $this->fetch('index', compact('list','map'));
    }

}
