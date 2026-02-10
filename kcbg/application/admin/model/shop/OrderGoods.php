<?php

namespace app\admin\model\shop;

use think\Model;


class OrderGoods extends Model
{


    // 表名
    protected $name = 'shop_order_goods';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'salestate_text',
        'commentstate_text'
    ];


    public function getUrlAttr($value, $data)
    {
        return $this->buildUrl($value, $data);
    }

    private function buildUrl($value, $data, $domain = false)
    {
        $diyname = isset($data['diyname']) && $data['diyname'] ? $data['diyname'] : $data['goods_id'];
        $time = $data['publishtime'] ?? time();
        $vars = [
            ':id'      => $data['goods_id'],
            ':cateid'  => '0',
            ':diyname' => $diyname,
            ':year'    => date("Y", $time),
            ':month'   => date("m", $time),
            ':day'     => date("d", $time),
        ];
        $config = get_addon_config('shop');
        $suffix = $config['moduleurlsuffix']['goods'] ?? $config['urlsuffix'];
        return addon_url('shop/goods/index', $vars, $suffix, $domain);
    }


    public function getSalestateList()
    {
        return ['0' => __('Salestate 0'), '1' => __('Salestate 1'), '2' => __('Salestate 2'), '3' => __('Salestate 3'), '4' => __('Salestate 4'), '5' => __('Salestate 5'), '6' => __('Salestate 6')];
    }

    public function getCommentstateList()
    {
        return ['0' => __('Commentstate 0'), '1' => __('Commentstate 1')];
    }


    public function getSalestateTextAttr($value, $data)
    {
        $value = $value ?: ($data['salestate'] ?? '');
        $list = $this->getSalestateList();
        return $list[$value] ?? '';
    }


    public function getCommentstateTextAttr($value, $data)
    {
        $value = $value ?: ($data['commentstate'] ?? '');
        $list = $this->getCommentstateList();
        return $list[$value] ?? '';
    }

    public function Order()
    {
        return $this->belongsTo('Order', 'order_sn', 'order_sn', [], 'LEFT');
    }
}
