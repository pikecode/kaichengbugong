<?php

namespace app\admin\model\shop;

use think\Model;
use think\Db;
use \app\common\model\User;
use \app\admin\model\shop\Exchange;

class ExchangeOrder extends Model
{

    // 表名
    protected $name = 'shop_exchange_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'status_text'
    ];

    public static function init()
    {

        self::afterWrite(function ($row) {
            $changeData = $row->getChangedData();
            //拒接回退积分
            if (isset($changeData['status']) && $changeData['status'] == 'rejected') {
                Db::transaction(function () use ($row) {
                    User::score($row['score'], $row['user_id'], '积分兑换已拒绝，积分退还');
                    //库存，销量回调
                    Exchange::where('id', $row['exchange_id'])->setInc('stocks', $row['nums']);
                    Exchange::where('id', $row['exchange_id'])->setDec('sales', $row['nums']);
                });
            }
        });
    }


    public function getTypeList()
    {
        return ['virtual' => __('Type virtual'), 'reality' => __('Type reality')];
    }

    public function getStatusList()
    {
        return ['created' => __('Created'), 'inprogress' => __('Inprogress'), 'rejected' => __('Rejected'), 'delivered' => __('Delivered'), 'completed' => __('Completed')];
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? '');
        $list = $this->getTypeList();
        return $list[$value] ?? '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }

    public function Exchange()
    {
        return $this->belongsTo('Exchange', 'exchange_id', 'id', [], 'LIKE');
    }

    public function User()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id', [], 'LEFT');
    }
}
