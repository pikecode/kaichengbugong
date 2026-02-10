<?php

namespace app\admin\model\shop;

use think\Model;
use traits\model\SoftDelete;


class Address extends Model
{

    use SoftDelete;

    // 表名
    protected $name = 'shop_address';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [];

    public static function init()
    {
        parent::init();
        self::beforeWrite(function ($row) {
            $changed = $row->getChangedData();
            if (isset($changed['isdefault']) && $changed['isdefault']) {
                $info = \app\admin\model\shop\Address::where('isdefault', 1)->where('user_id', $row['user_id'])->find();
                if ($info && (!isset($row['id']) || $info['id'] != $row['id'])) {
                    $info->isdefault = 0;
                    $info->save();
                }
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

    public function User()
    {
        return $this->hasOne('\\app\\common\\model\\User', 'id', 'user_id', [], 'LEFT');
    }

    public function Province()
    {
        return $this->hasOne('Area', 'id', 'province_id', [], 'LEFT');
    }

    public function City()
    {
        return $this->hasOne('Area', 'id', 'city_id', [], 'LEFT');
    }

    public function Area()
    {
        return $this->hasOne('Area', 'id', 'area_id', [], 'LEFT');
    }
}
