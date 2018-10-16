<?php

namespace app\store\model;

use app\common\model\Order as OrderModel;
use think\Request;
use think\Config;
use app\store\model\Wxapp;

/**
 * 订单管理
 * Class Order
 * @package app\store\model
 */
class Order extends OrderModel
{
    /**
     * 订单列表
     * @param $filter
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($filter)
    {
        return $this->with(['goods.image', 'address', 'user'])
            ->where($filter)
            ->order(['create_time' => 'desc'])->paginate(10, false, [
                'query' => Request::instance()->request()
            ]);
    }

    /**
     * 确认发货
     * @param $data
     * @return bool|false|int
     */
    public function delivery($data)
    {
        if ($this['pay_status']['value'] === 10
            || $this['delivery_status']['value'] === 20) {
            $this->error = '该订单不合法';
            return false;
        }

        // $this->sendMessage($data);

        return $this->save([
            'express_url' => $data['express_url'],
            'express_file_id' => $data['express_file_id'],
            'delivery_status' => 20,
            'delivery_time' => time(),
        ]);
    }







    /**
     * 确认发货后通知用户
     */
    public function sendMessage($data)
    {        
        $infos = $this->toArray();
        $data = $this->with(['user'])->select()->toArray();
        $user = $data[0]['user'];
        $open_id = $user['open_id'];
        $nick_name = $user['nickName'];
        $config = Wxapp::detail()->toArray();
        $app_id = $config['app_id'];
        $app_secret = $config['app_secret'];
        // 获取 access_token
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
        $token = file_get_contents($url);
        $access_token = json_decode($token, true);
        $access_token = $access_token['access_token'];        
        // 
        $data = [
            "touser" => $open_id,
            "template_id" => "DKbT4ERHHB8nfzyxDxGWQLetrzIXW-WsM7aNeFeLUWg",
            "page" => "index",
            "form_id"=>"wx161548173540330f4fade0402318420392",
            "data" => [
                "keyword1" => [
                    "value" => $nick_name
                ],
                "keyword2" => [
                    "value" => $infos["goods"][0]['goods_name']
                ],
                "keyword3" => [
                    "value" => $infos["goods"][0]['total_num']
                ],
                "keyword4" => [
                    "value" => date('Y-m-d H:i:s', time()),
                ],
                "keyword5" => [
                    "value" => date('Y-m-d H:i:s', time()),
                ],
                "keyword6" => [
                    "value" => "已发货"
                ],
                "keyword7" => [
                    "value" => "请进入小程序，所有订单，查看相应产品"
                ]
            ],
            "emphasis_keyword" => ""
        ];

        $data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $access_token);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        halt($tmpInfo);
        return $tmpInfo;        

    }

}
