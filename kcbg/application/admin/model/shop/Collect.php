<?php

namespace app\admin\model\shop;

use think\Model;


class Collect extends Model
{


    // 表名
    protected $name = 'shop_collect';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];


    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }

    public function User()
    {
        return $this->hasOne('\\app\\common\\model\\User', 'id', 'user_id', [], 'LEFT');
    }

    public function Goods()
    {
        return $this->hasOne('Goods', 'id', 'goods_id', [], 'LEFT');
    }
}
