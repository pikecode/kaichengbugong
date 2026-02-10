<?php

namespace app\admin\model\shop;

use think\Model;


/**
 * 地区数据模型
 */
class Area extends Model
{

    // 表名
    protected $name = 'shop_area';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];

    protected static function init()
    {
        self::afterDelete(function ($row) {
            $areaList = self::where('pid', $row['id'])->select();
            foreach ($areaList as $index => $item) {
                $item->delete();
            }
        });
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }
}

