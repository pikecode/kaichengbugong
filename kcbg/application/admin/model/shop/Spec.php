<?php

namespace app\admin\model\shop;

use think\Model;
use addons\shop\model\SpecValue;
use app\admin\model\shop\SpecValue as ShopSpecValue;

class Spec extends Model
{


    // 表名
    protected $name = 'shop_spec';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];

    //新增属性和属性值
    public static function push($spec)
    {
        $spec_list = [];
        foreach ($spec as $key => $item) {
            $row = self::where('name', $item['name'])->find();
            if (!$row) {
                $row = new self();
                $row->save(['name' => $item['name']]);
            }
            $spec_data = ShopSpecValue::push($item['value'], $row);
            $spec_list = array_merge($spec_list, $spec_data);
        }
        return $spec_list;
    }
}
