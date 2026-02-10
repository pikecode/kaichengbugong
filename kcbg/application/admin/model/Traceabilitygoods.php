<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Traceabilitygoods extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'traceabilitygoods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function traceability()
    {
        return $this->belongsTo('Traceability', 'traceability_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function getImageAttr($value, $data)
    {
        $value = $value;
        return cdnurl($value, true);
    }

    
}
