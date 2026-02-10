<?php

namespace app\admin\model\shop;

use think\Model;


class UserCoupon extends Model
{


    // 表名
    protected $name = 'shop_user_coupon';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'is_used_text'
    ];


    public function getIsUsedList()
    {
        return ['1' => __('Is_used 1'), '2' => __('Is_used 2')];
    }


    public function getIsUsedTextAttr($value, $data)
    {
        $value = $value ?: ($data['is_used'] ?? '');
        $list = $this->getIsUsedList();
        return $list[$value] ?? '';
    }

    public function Coupon()
    {
        return $this->belongsTo('Coupon', 'coupon_id', 'id', '', 'LEFT');
    }

    public function User()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id', '', 'LEFT');
    }


}
