<?php

namespace app\store\controller\wxapp;

use app\store\controller\Controller;
use think\Db;


/**
 * 用户协议
 * Class help
 * @package app\store\controller\wxapp
 */
class Agree extends Controller
{

    /**
     * agree
     * @param $id
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        // 帮助详情
        $model = Db::name('agree')->select()->toArray();
        $model = $model[0];
        if (!$this->request->isAjax()) {
            return $this->fetch('edit', compact('model'));
        }
        // 更新记录        
        $agree = input()['agree'];        
        $res = Db::name('agree')->where('id', $agree['id'])->update([
            'content' => $agree['content']
        ]);
        if ($res !== false) {
            return $this->renderSuccess('更新成功', url('wxapp.agree/edit'));
        } else {
            $error = '更新失败';
            return $this->renderError($error);
        }

    }

}
