<?php

namespace app\admin\model\shop;

use addons\shop\library\RefundException;
use think\Exception;
use think\Model;
use traits\model\SoftDelete;
use app\admin\model\shop\OrderAction;
use addons\epay\library\Service;
use Yansongda\Pay\Pay;
use think\Log;
use addons\shop\model\TemplateMsg;
use addons\shop\model\UserCoupon;

class Order extends Model
{

    use SoftDelete;

    // 表名
    protected $name = 'shop_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'expiretime_text',
        'paytime_text',
        'refundtime_text',
        'shippingtime_text',
        'receivetime_text',
        'canceltime_text',
        'orderstate_text',
        'shippingstate_text',
        'paystate_text',
        'status_text'
    ];


    public function getOrderstateList()
    {
        return ['0' => __('Orderstate 0'), '1' => __('Orderstate 1'), '2' => __('Orderstate 2'), '3' => __('Orderstate 3'), '4' => __('Orderstate 4')];
    }

    public function getShippingstateList()
    {
        return ['0' => __('Shippingstate 0'), '1' => __('Shippingstate 1'), '2' => __('Shippingstate 2')];
    }

    public function getPaystateList()
    {
        return ['0' => __('Paystate 0'), '1' => __('Paystate 1')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Status normal'), 'hidden' => __('Status hidden')];
    }


    public function getExpiretimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['expiretime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPaytimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['paytime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getRefundtimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['refundtime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getShippingtimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['shippingtime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getReceivetimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['receivetime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getCanceltimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['canceltime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getOrderstateTextAttr($value, $data)
    {
        $value = $value ?: ($data['orderstate'] ?? '');
        $list = $this->getOrderstateList();
        return $list[$value] ?? '';
    }


    public function getShippingstateTextAttr($value, $data)
    {
        $value = $value ?: ($data['shippingstate'] ?? '');
        $list = $this->getShippingstateList();
        return $list[$value] ?? '';
    }


    public function getPaystateTextAttr($value, $data)
    {
        $value = $value ?: ($data['paystate'] ?? '');
        $list = $this->getPaystateList();
        return $list[$value] ?? '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }

    protected function setExpiretimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPaytimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setRefundtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setShippingtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setReceivetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setCanceltimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public static function init()
    {
        self::afterWrite(function ($row) {
            $changeData = $row->getChangedData();
            if (isset($changeData['paystate'])) {
                if ($changeData['paystate'] == 1) {
                    \addons\shop\model\OrderGoods::setGoodsSalesInc($row->order_sn);
                }
                OrderAction::push($row->order_sn, $changeData['paystate'] == 1 ? '更改订单为已支付' : '更改订单为未支付', '管理员');
            }
            if (isset($changeData['shippingstate'])) {
                switch ($changeData['shippingstate']) {
                    case 1:
                        //发送通知
                        TemplateMsg::sendTempMsg(1, $row->order_sn);
                        $memo = '订单发货成功';
                        break;
                    case 2:
                        $memo = '更改订单已收货';
                        break;
                    default:
                        $memo = '更改订单待发货';
                }
                OrderAction::push($row->order_sn, $memo, '管理员');
            }
            if (isset($changeData['orderstate'])) {
                $memo = '';
                switch ($changeData['orderstate']) {
                    case 0:
                        OrderAction::push($row->order_sn, '更改订单为正常', '管理员');
                        break;
                    case 1:
                        //已取消，库存恢复
                        \addons\shop\model\OrderGoods::setGoodsStocksInc($row->order_sn);
                        //恢复优惠券
                        UserCoupon::resetUserCoupon($row->user_coupon_id, $row->order_sn);
                        OrderAction::push($row->order_sn, '订单取消成功', '管理员');
                        break;
                    case 2:
                        OrderAction::push($row->order_sn, '更改订单为已失效', '管理员');
                        break;
                    case 3:
                        //结束，订单完成，给积分
                        $config = get_addon_config('shop');
                        if (isset($config['money_score']) && $config['money_score'] > 0 && $row->shippingstate == 2 && $row->paystate == 1) {
                            //减去退款金额
                            $refund = OrderAftersales::where('order_id', $row->id)->where('type', '<>', 3)->where('status', 2)->sum('refund');
                            $money = bcsub($row['payamount'], $refund, 2);
                            if ($money > 0) {
                                $score = bcmul($money, $config['money_score']);
                                \app\common\model\User::score($score, $row['user_id'], '完成订单奖励' . $score . '积分');
                            }
                        }
                        OrderAction::push($row->order_sn, '更改订单为已完成', '管理员');
                        break;
                }
            }
        });
    }

    /**
     * 退款
     */
    public static function refund($order_sn, $paytype, $payamount)
    {
        $config = Service::getConfig($paytype);
        try {
            $order = Order::getByOrderSn($order_sn);
            if ($paytype == 'wechat') {
                $response = Pay::wechat($config)->refund([
                    'type'          => in_array($order['method'], ['miniapp', 'app']) ? $order['method'] : '',
                    'out_trade_no'  => $order_sn,
                    'out_refund_no' => time(),
                    'total_fee'     => bcmul($order['payamount'], 100),
                    'refund_fee'    => bcmul($payamount, 100)
                ]);
                if (!$response['result_code'] || $response['result_code'] != 'SUCCESS') {
                    throw new \Exception(($response['err_code'] ?? '') . ':' . ($response['err_code_des'] ?? '未知退款错误'));
                }
            } elseif ($paytype == 'alipay') {
                $response = Pay::alipay($config)->refund([
                    'out_trade_no'  => $order_sn,
                    'refund_amount' => $payamount,
                ]);
                if (!$response['code'] || $response['code'] != '10000') {
                    throw new \Exception(($response['code'] ?? '') . ':' . ($response['msg'] ?? '未知退款错误'));
                }
            }
        } catch (\Exception $e) {
            Log::write("[shop][refund][{$order_sn}]同步退款失败，失败原因：" . $e->getMessage(), 'refund');
            throw new \Exception("同步退款失败，失败原因：" . $e->getMessage());
        }

        //发送通知
        TemplateMsg::sendTempMsg(2, $order_sn);
        return true;
    }

    public function User()
    {
        return $this->hasOne('\\app\\common\\model\\User', 'id', 'user_id', [], 'LEFT');
    }

    public function OrderGoods()
    {
        return $this->hasMany('OrderGoods', 'order_sn', 'order_sn');
    }

    public function OrderAction()
    {
        return $this->hasMany('OrderAction', 'order_sn', 'order_sn');
    }
}
