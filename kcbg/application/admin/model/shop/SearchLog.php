<?php

namespace app\admin\model\shop;

use think\Model;


class SearchLog extends Model
{

    // 表名
    protected $name = 'shop_search_log';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStateTextAttr($value, $data)
    {
        $value = $value ?: ($data['state'] ?? '');
        $list = $this->getStateList();
        return $list[$value] ?? '';
    }

}
