<?php

namespace app\admin\model\shop;

use think\Model;


class SpecValue extends Model
{


    // 表名
    protected $name = 'shop_spec_value';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];

    //添加属性
    public static function push($values, $spec)
    {
        $data = [];
        foreach ($values as $item) {
            $specValue = self::where('value', $item)->where('spec_id', $spec->id)->find();
            if (!$specValue) {
                $specValue = new self();
                $specValue->save(['value' => $item, 'spec_id' => $spec->id]);
            }
            $data[] = [
                'spec_id'          => $spec->id,
                'spec_name'        => $spec->name,
                'spec_value_id'    => $specValue->id,
                'spec_value_value' => $specValue->value
            ];
        }
        return $data;
    }
}
