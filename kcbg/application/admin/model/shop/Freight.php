<?php

namespace app\admin\model\shop;

use think\Model;


class Freight extends Model
{


    // 表名
    protected $name = 'shop_freight';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text'
    ];


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    public function getFirstNumAttr($value, $data)
    {
        return isset($data['first_num']) ? $data['first_num'] : $data['num'];
    }

    public function getFirstPriceAttr($value, $data)
    {
        return isset($data['first_price']) ? $data['first_price'] : $data['price'];
    }

    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? '');
        $list = $this->getTypeList();
        return $list[$value] ?? '';
    }


}
