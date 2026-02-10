<?php

namespace app\admin\model\shop;

use think\Model;


class Video extends Model
{

    

    

    // 表名
    protected $name = 'shop_video';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'status_text'
    ];
    

    
    public function getTypeList()
    {
        return ['0' => __('商品溯源'), '1' => __('精彩瞬间'), '2' => __('多视角回放')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $valueArr = explode(',', $value);
        $list = $this->getTypeList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setTypeAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }


    public function goods()
    {
        return $this->belongsTo('Goods', 'shop_goods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
