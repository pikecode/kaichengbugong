<?php

namespace app\admin\model\shop;

use think\Model;
use app\admin\model\shop\OrderAction;
use addons\shop\model\TemplateMsg;

class OrderAftersales extends Model
{

    // 表名
    protected $name = 'shop_order_aftersales';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'status_text',
    ];


    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2'), '3' => __('Type 3')];
    }

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? '');
        $list = $this->getTypeList();
        return $list[$value] ?? '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }


    public static function init()
    {
        self::afterWrite(function ($row) {
            $changeData = $row->getChangedData();
            $order = $row->order;
            if (isset($changeData['status']) && $order) {
                if ($changeData['status'] == 2) {
                    if ($row['type'] == 1) {
                        //仅退款

                        //已通过，是否同步退款
                        $config = get_addon_config('shop');
                        if ($config['order_refund_sync']) {
                            //执行同步退款
                            \app\admin\model\shop\Order::refund($order->order_sn, $order->paytype, $row->refund);
                        }

                        //已退款
                        \app\admin\model\shop\OrderGoods::where('id', $row['order_goods_id'])->update(['salestate' => 4]);

                        //未发货状态下才减库存
                        if ($order->shippingstate == 0) {
                            \addons\shop\model\OrderGoods::setGoodsStocksInc($order->order_sn); //库存增
                            \addons\shop\model\OrderGoods::setGoodsSalesDec($order->order_sn); //销量减
                        }

                        //记录操作
                        OrderAction::push($order->order_sn, '同意订单退款', '管理员');

                        $count = \app\admin\model\shop\OrderGoods::where('order_sn', $order['order_sn'])->where('salestate', 'not in', [4, 5])->count();
                        if (!$count) {
                            $order->refundtime = time();
                            $order->orderstate = 3;
                            $order->save();
                        } else {
                            $order->orderstate = 0;
                            $order->save();
                        }
                    } elseif ($row['type'] == 2) {
                        //退货退款

                        //退货中
                        \app\admin\model\shop\OrderGoods::where('id', $row['order_goods_id'])->update(['salestate' => 3]);
                        $order->refundtime = time();
                        $order->save();
                    }
                }
                //拒绝
                if ($changeData['status'] == 3) {
                    \app\admin\model\shop\OrderGoods::where('id', $row['order_goods_id'])->update(['salestate' => 6]);
                    //记录操作
                    OrderAction::push($order->order_sn, '拒绝订单退款', '管理员');
                    //发送通知
                    TemplateMsg::sendTempMsg(3, $order->order_sn);
                    $order->orderstate = 0;
                    $order->save();
                }
            }
        });
    }

    public function User()
    {
        return $this->hasOne('\\app\\common\\model\\User', 'id', 'user_id', [], 'LEFT')->setEagerlyType(0);
    }

    public function OrderGoods()
    {
        return $this->hasOne('OrderGoods', 'id', 'order_goods_id', [], 'LEFT')->setEagerlyType(0);
    }

    public function Order()
    {
        return $this->hasOne('Order', 'id', 'order_id', [], 'LEFT')->setEagerlyType(0);
    }
}
