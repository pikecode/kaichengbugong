<?php

namespace app\admin\model\shop;

use think\Model;


class OrderElectronics extends Model
{

    // 表名
    protected $name = 'shop_order_electronics';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];

    //报错电子面单记录
    public static function push($res, $order_sn, $customer_name, $customer_pwd)
    {
        if (isset($res['Success']) && $res['Success']) { //成功
            (new self)->save([
                'order_sn'       => $order_sn,
                'customer_name'  => $customer_name,
                'customer_pwd'   => $customer_pwd,
                'print_template' => $res['PrintTemplate'],
                'kdn_order_code' => $res['Order']['KDNOrderCode'],
                'logistic_code'  => $res['Order']['LogisticCode'],
                'shipper_code'   => $res['Order']['ShipperCode'],
                'order'          => json_encode($res['Order'], JSON_UNESCAPED_UNICODE)
            ]);
        }
    }
}
