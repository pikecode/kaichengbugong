<?php

namespace app\admin\model\shop;

use think\Model;


class ElectronicsOrder extends Model
{


    // 表名
    protected $name = 'shop_electronics_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'paytype_text',
        'is_notice_text',
        'is_return_temp_text',
        'is_send_message_text',
        'is_return_sign_bill_text',
        'exp_type_text'
    ];


    public function getPaytypeList()
    {
        return ['1' => __('Paytype 1'), '2' => __('Paytype 2'), '3' => __('Paytype 3'), '4' => __('Paytype 4')];
    }

    public function getIsNoticeList()
    {
        return ['0' => __('Is_notice 0'), '1' => __('Is_notice 1')];
    }

    public function getIsReturnTempList()
    {
        return ['0' => __('Is_return_temp 0'), '1' => __('Is_return_temp 1')];
    }

    public function getIsSendMessageList()
    {
        return ['0' => __('Is_send_message 0'), '1' => __('Is_send_message 1')];
    }

    public function getIsReturnSignBillList()
    {
        return ['0' => __('Is_return_sign_bill 0'), '1' => __('Is_return_sign_bill 1')];
    }

    public function getExpTypeList()
    {
        return ['1' => __('Exp_type 1')];
    }


    public function getPaytypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['paytype'] ?? '');
        $list = $this->getPaytypeList();
        return $list[$value] ?? '';
    }


    public function getIsNoticeTextAttr($value, $data)
    {
        $value = $value ?: ($data['is_notice'] ?? '');
        $list = $this->getIsNoticeList();
        return $list[$value] ?? '';
    }


    public function getIsReturnTempTextAttr($value, $data)
    {
        $value = $value ?: ($data['is_return_temp'] ?? '');
        $list = $this->getIsReturnTempList();
        return $list[$value] ?? '';
    }


    public function getIsSendMessageTextAttr($value, $data)
    {
        $value = $value ?: ($data['is_send_message'] ?? '');
        $list = $this->getIsSendMessageList();
        return $list[$value] ?? '';
    }


    public function getIsReturnSignBillTextAttr($value, $data)
    {
        $value = $value ?: ($data['is_return_sign_bill'] ?? '');
        $list = $this->getIsReturnSignBillList();
        return $list[$value] ?? '';
    }

    public function getExpTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['exp_type'] ?? '');
        $list = $this->getExpTypeList();
        return $list[$value] ?? '';
    }


    public function Shipper()
    {
        return $this->hasOne('Shipper', 'id', 'shipper_id', [], 'LEFT');
    }

}
