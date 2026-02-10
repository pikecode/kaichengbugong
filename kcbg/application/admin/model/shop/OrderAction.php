<?php

namespace app\admin\model\shop;

use think\Model;


class OrderAction extends Model
{


    // 表名
    protected $name = 'shop_order_action';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];

    public static function push($order_sn, $memo, $operator = '系统')
    {
        self::create([
            'order_sn' => $order_sn,
            'operator' => $operator,
            'memo'     => $memo
        ]);
        return true;
    }
}
