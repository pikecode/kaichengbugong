<?php

namespace app\admin\model\shop;

use think\Model;


class Comment extends Model
{

    // 表名
    protected $name = 'shop_comment';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];

    protected static function init()
    {
        self::afterWrite(function ($row) {
            $changedData = $row->getChangedData();
            if (isset($changedData['status']) && $row->goods) {
                if ($changedData['status'] == 'normal') {
                    $row->goods->setInc('comments');
                } else {
                    if ($row->goods->comments > 0) {
                        $row->goods->setDec('comments');
                    }
                }
            }
        });
        self::afterDelete(function ($row) {
            if ($row['pid']) {
                \addons\shop\model\Comment::where('id', $row['pid'])->setDec('comments');
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

    public function Goods()
    {
        return $this->hasOne('Goods', 'id', 'goods_id', [], 'LEFT');
    }

}
