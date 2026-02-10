<?php

namespace app\admin\model;

use think\Model;

class Ingredientreplayitemvideo extends Model
{


    // 表名
    protected $name = 'ingredientreplayitemvideo';

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
        return ['0' => __('配料全程回放'), '1' => __('精彩瞬间'), '2' => __('有封条，不掉包')];
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


    public function getImageAttr($value, $data)
    {
        return cdnurl($value, true);
    }

    protected function setTypeAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    protected function setRiqiAttr($value)
    {
        return $value === '' ? null : $value;
    }


    public function ingredientreplayitem()
    {
        return $this->belongsTo('Ingredientreplayitem', 'ingredientreplayitem_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
