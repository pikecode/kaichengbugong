<?php

namespace app\admin\model;

use think\Model;


class Traceabilitygoodsvideo extends Model
{

    

    

    // 表名
    protected $name = 'traceabilitygoodsvideo';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'status_text',
        // 's_time_text',
        // 'e_time_text'
    ];

    
    

    
    public function getTypeList()
    {
        return ['0' => __('到供应商处溯源，全程回放'), '1' => __('精彩瞬间'), '2' => __('有封条，不掉包')];
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


    // public function getSTimeTextAttr($value, $data)
    // {
    //     $value = $value ? $value : (isset($data['s_time']) ? $data['s_time'] : '');
    //     return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    // }


    // public function getETimeTextAttr($value, $data)
    // {
    //     $value = $value ? $value : (isset($data['e_time']) ? $data['e_time'] : '');
    //     return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    // }

    protected function setTypeAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    // protected function setSTimeAttr($value)
    // {
    //     return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    // }

    // protected function setETimeAttr($value)
    // {
    //     return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    // }


    public function traceabilitygoods()
    {
        return $this->belongsTo('Traceabilitygoods', 'traceabilitygoods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
