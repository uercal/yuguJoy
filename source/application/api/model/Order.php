<?php

namespace app\api\model;

use think\Db;
use app\common\model\Order as OrderModel;
use app\common\exception\BaseException;

/**
 * 订单模型
 * Class Order
 * @package app\api\model
 */
class Order extends OrderModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'update_time'
    ];


    /**
     * 订单确认-立即购买
     * @param User $user
     * @param $goods_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function getBuyNow($user, $goods_id, $post_data)
    {        
        // 商品信息
        /* @var Goods $goods */
        $goods = Goods::detail($goods_id)->toArray();
        // 
        $totalPrice = 0;
        foreach ($post_data['spec'] as $key => $value) {
            $goods_price = $value['goods_price'];
            $num = $value['num'];
            $totalPrice += bcmul($goods_price, $num, 2);
        }

        return [
            'goods' => $goods,               // 商品详情
            'spec' => $post_data['spec'],           // 属性组合列表
            'order_total_price' => $totalPrice,    // 商品总金额 
            'order_pay_price' => $totalPrice,  // 实际支付金额   
            'server_time' => $post_data['server_time'],  //服务单时间
            'address_id' => $post_data['address_id'],   //地址
            'has_error' => $this->hasError(),
            'error_msg' => $this->getError(),
        ];
    }

    /**
     * 订单确认-购物车结算
     * @param $user
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCart($user)
    {
        $model = new Cart($user['user_id']);
        return $model->getList($user);
    }

    /**
     * 新增订单
     * @param $user_id
     * @param $order
     * @return bool
     * @throws \Exception
     */
    public function add($user_id, $order)
    {
        Db::startTrans();
        // 生成订单地址
        $tmp = Db::name('user_address')->where('address_id', '=', $order['address_id'])->find();
                        
        // 记录订单信息
        $this->save([
            'user_id' => $user_id,
            'wxapp_id' => self::$wxapp_id,
            'order_no' => $this->orderNo(),
            'total_price' => $order['order_total_price'],
            'pay_price' => $order['order_pay_price'],
            'server_time' => $order['server_time']
        ]);
        $this->address()->save([
            'name' => $tmp['name'],
            'phone' => $tmp['phone'],
            'province_id' => $tmp['province_id'],
            'city_id' => $tmp['city_id'],
            'region_id' => $tmp['region_id'],
            'detail' => $tmp['detail'],
            'user_id' => $tmp['user_id'],
            'wxapp_id' => $tmp['wxapp_id']
        ]);
        // 
        

        // 订单商品列表
        $goodsList = [];

        $goods = $order['goods'];
        foreach ($order['spec'] as $spec) {
            /* @var Goods $goods */                      
            // 
            if ($spec['num'] == 0) continue;
            $goodsList[] = [
                'user_id' => $user_id,
                'wxapp_id' => self::$wxapp_id,
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'] . "(" . $spec['spec_value'] . ")",
                'image_id' => $goods['image'][0]['image_id'],
                'spec_type' => $goods['spec_type'],
                'spec_sku_id' => $spec['spec_sku_id'],//
                'goods_spec_id' => $spec['goods_spec_id'],//
                'content' => $goods['content'],
                'goods_price' => $spec['goods_price'],
                'total_num' => $spec['num'],//
                'total_price' => bcmul($spec['goods_price'], $spec['num'], 2)
            ];
        }
        
        // 保存订单商品信息
        $this->goods()->saveAll($goodsList);
        Db::commit();
        return true;
    }

    /**
     * 用户中心订单列表
     * @param $user_id
     * @param string $type
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($user_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment':
                $filter['pay_status'] = 10;
                break;
            case 'delivery':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 10;
                break;
            case 'received':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                break;
            case 'done':
                $filter['pay_status'] = 20;
                $filter['order_status'] = 30;
        }
        $res = $this->with(['goods.goods','address'])
            ->where('user_id', '=', $user_id)
            ->where('order_status', '<>', 20)
            ->where($filter)
            ->order(['create_time' => 'desc'])
            ->select();
        return $res;
    }

    /**
     * 取消订单
     * @return bool|false|int
     * @throws \Exception
     */
    public function cancel()
    {
        if ($this['pay_status']['value'] === 20) {
            $this->error = '已付款订单不可取消';
            return false;
        }
        // 回退商品库存
        $this->backGoodsStock($this['goods']);
        return $this->save(['order_status' => 20]);
    }

    /**
     * 回退商品库存
     * @param $goodsList
     * @return array|false
     * @throws \Exception
     */
    private function backGoodsStock(&$goodsList)
    {
        $goodsSpecSave = [];        
        // 更新商品规格库存
        return !empty($goodsSpecSave) && (new GoodsSpec)->isUpdate()->saveAll($goodsSpecSave);
    }

    /**
     * 确认收货
     * @return bool|false|int
     */
    public function receipt()
    {
        if ($this['delivery_status']['value'] === 10 || $this['receipt_status']['value'] === 20) {
            $this->error = '该订单不合法';
            return false;
        }
        return $this->save([
            'receipt_status' => 20,
            'receipt_time' => time(),
            'order_status' => 30
        ]);
    }

    /**
     * 获取订单总数
     * @param $user_id
     * @param string $type
     * @return int|string
     */
    public function getCount($user_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment';
            $filter['pay_status'] = 10;
            break;
        case 'received';
        $filter['pay_status'] = 20;
        $filter['delivery_status'] = 20;
        $filter['receipt_status'] = 10;
        break;
}
return $this->where('user_id', '=', $user_id)
    ->where('order_status', '<>', 20)
    ->where($filter)
    ->count();
}

/**
 * 订单详情
 * @param $order_id
 * @param null $user_id
 * @return null|static
 * @throws BaseException
 * @throws \think\exception\DbException
 */
public static function getUserOrderDetail($order_id, $user_id)
{
    if (!$order = self::get([
        'order_id' => $order_id,
        'user_id' => $user_id,
        'order_status' => ['<>', 20]
    ], ['goods' => ['image', 'spec', 'goods'], 'address'])) {
        throw new BaseException(['msg' => '订单不存在']);
    }
    return $order;
}

/**
 * 判断商品库存不足 (未付款订单)
 * @param $goodsList
 * @return bool
 */
public function checkGoodsStatusFromOrder(&$goodsList)
{
    foreach ($goodsList as $goods) {
            // 判断商品是否下架
        if ($goods['goods']['goods_status']['value'] !== 10) {
            $this->setError('很抱歉，商品 [' . $goods['goods_name'] . '] 已下架');
            return false;
        }
    }
    return true;
}

/**
 * 设置错误信息
 * @param $error
 */
private function setError($error)
{
    empty($this->error) && $this->error = $error;
}

/**
 * 是否存在错误
 * @return bool
 */
public function hasError()
{
    return !empty($this->error);
}

}
