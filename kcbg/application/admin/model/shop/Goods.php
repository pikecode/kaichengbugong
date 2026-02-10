<?php

namespace app\admin\model\shop;

use think\Model;
use traits\model\SoftDelete;

class Goods extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'shop_goods';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'flag_text',
        'status_text',
        'url'
    ];

    public function getUrlAttr($value, $data)
    {
        return $this->buildUrl($value, $data);
    }

    private function buildUrl($value, $data, $domain = false)
    {
        $diyname = isset($data['diyname']) && $data['diyname'] ? $data['diyname'] : $data['id'];
        $catename = isset($this->category) && $this->category ? $this->category->diyname : 'all';
        $cateid = isset($this->category) && $this->category ? $this->category->id : 0;
        $time = $data['publishtime'] ?? time();
        $vars = [
            ':id'       => $data['id'],
            ':diyname'  => $diyname,
            ':category' => $cateid,
            ':catename' => $catename,
            ':cateid'   => $cateid,
            ':year'     => date("Y", $time),
            ':month'    => date("m", $time),
            ':day'      => date("d", $time),
        ];
        $config = get_addon_config('shop');
        $suffix = $config['moduleurlsuffix']['goods'] ?? $config['urlsuffix'];
        return addon_url('shop/goods/index', $vars, $suffix, $domain);
    }


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });

            // self::beforeWrite(function ($row) {
            //     if ($row['price'] > 0 && $row['price'] >= $row['marketprice']) {
            //         throw new \Exception('市场价格必须大于实际价格');
            //     }
            // });
    }

    public function setContentAttr($value)
    {
        return $value;
        //替换卡片信息
        return \addons\shop\library\Service::replaceSourceTpl($value);
    }

    public function getContentAttr($value, $data)
    {
        //组装卡片信息
        return \addons\shop\library\Service::formatSourceTpl($value);
    }

    public function getFlagList()
    {
        $config = get_addon_config('shop');
        return $config['flagtype'] ?? [];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden'), 'soldout' => __('Soldout')];
    }


    public function getFlagTextAttr($value, $data)
    {
        $value = $value ?: ($data['flag'] ?? '');
        $valueArr = explode(',', $value);
        $list = $this->getFlagList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }

    protected function setFlagAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    protected function setAttributeIdsAttr($value)
    {
        $ids = [];
        foreach ($value as $item) {
            foreach ($item as $id) {
                if ($id) {
                    $ids[] = $id;
                }
            }
        }
        return implode(',', $ids);
    }

    public function Freight()
    {
        return $this->belongsTo('Freight', 'freight_id', 'id', [], 'LEFT');
    }

    public function Brand()
    {
        return $this->belongsTo('Brand', 'brand_id', 'id', [], 'LEFT');
    }

    public function GoodsSku()
    {
        return $this->belongsTo('GoodsSku', 'id', 'goods_id', [], 'LEFT');
    }

    public function Category()
    {
        return $this->belongsTo('Category', 'category_id', 'id', [], 'LEFT');
    }
}
