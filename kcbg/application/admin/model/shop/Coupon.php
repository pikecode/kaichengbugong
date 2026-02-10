<?php

namespace app\admin\model\shop;

use think\Model;
use addons\shop\library\IntCode;

class Coupon extends Model
{


    // 表名
    protected $name = 'shop_coupon';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'result_text',
        'is_open_text',
        'url',
        'receive_times',
    ];
    protected static $config = [];

    protected static function init()
    {
        $config = get_addon_config('shop');
        self::$config = $config;
        self::beforeWrite(function ($row) {
            $changedData = $row->getChangedData();
            if (isset($changedData['receive_times'])) {
                list($begintime, $endtime) = explode(' - ', $changedData['receive_times']);
                $row['begintime'] = strtotime($begintime);
                $row['endtime'] = strtotime($endtime);
            }
        });
    }

    public function getUrlAttr($value, $data)
    {
        return $this->buildUrl($value, $data);
    }

    public function getFullurlAttr($value, $data)
    {
        return $this->buildUrl($value, $data, true);
    }

    private function buildUrl($value, $data, $domain = false)
    {
        $vars = [
            ':coupon' => IntCode::encode($data['id'])
        ];
        $suffix = static::$config['moduleurlsuffix']['coupon'] ?? static::$config['urlsuffix'];
        return addon_url('shop/coupon/show', $vars, $suffix, $domain);
    }

    public function getReceiveTimesAttr($value, $data)
    {
        return date("Y-m-d H:i:s", $data['begintime']) . ' - ' . date("Y-m-d H:i:s", $data['endtime']);
    }

    public function getResultList()
    {
        return ['0' => __('Result 0'), '1' => __('Result 1')];
    }

    public function getIsOpenList()
    {
        return ['0' => __('Is_open 0'), '1' => __('Is_open 1')];
    }

    public function getIsPrivateList()
    {
        return ['yes' => __('Is_private yes'), 'no' => __('Is_private no')];
    }

    public function getModeList()
    {
        return ['fixation' => __('Mode fixation'), 'dates' => __('Mode dates')];
    }


    public function getResultTextAttr($value, $data)
    {
        $value = $value ?: ($data['result'] ?? '');
        $list = $this->getResultList();
        return $list[$value] ?? '';
    }


    public function getIsOpenTextAttr($value, $data)
    {
        $value = $value ?: ($data['is_open'] ?? '');
        $list = $this->getIsOpenList();
        return $list[$value] ?? '';
    }

    public function getResultDataAttr($value)
    {
        if (!empty($value)) {
            return (array)json_decode($value, true);
        }
        return [];
    }

    public function setResultDataAttr($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
        return $value;
    }


}
