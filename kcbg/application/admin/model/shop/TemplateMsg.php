<?php

namespace app\admin\model\shop;

use think\Model;


class TemplateMsg extends Model
{


    // 表名
    protected $name = 'shop_template_msg';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'event_text'
    ];


    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2'), '3' => __('Type 3'), '4' => __('Type 4')];
    }

    public function getEventList()
    {
        return ['0' => __('Event 0'), '1' => __('Event 1'), '2' => __('Event 2'), '3' => __('Event 3'), '4' => __('Event 4')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? '');
        $list = $this->getTypeList();
        return $list[$value] ?? '';
    }


    public function getEventTextAttr($value, $data)
    {
        $value = $value ?: ($data['event'] ?? '');
        $list = $this->getEventList();
        return $list[$value] ?? '';
    }


}
