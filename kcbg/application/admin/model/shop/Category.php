<?php

namespace app\admin\model\shop;

use think\Model;


class Category extends Model
{


    // 表名
    protected $name = 'shop_category';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'flag_text',
        'status_text',
        'url'
    ];
    protected static $config = [];


    protected static function init()
    {
        $config = get_addon_config('shop');
        self::$config = $config;

        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
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
        $diyname = isset($data['diyname']) && $data['diyname'] ? $data['diyname'] : $data['id'];
        $cateid = $data['id'] ?? 0;
        $catename = isset($data['diyname']) && $data['diyname'] ? $data['diyname'] : 'all';
        $time = $data['createtime'] ?? time();

        $vars = [
            ':id'       => $data['id'],
            ':diyname'  => $diyname,
            ':category' => $cateid,
            ':catename' => $catename,
            ':cateid'   => $cateid,
            ':year'     => date("Y", $time),
            ':month'    => date("m", $time),
            ':day'      => date("d", $time)
        ];
        if (isset($data['type']) && isset($data['outlink']) && $data['type'] == 'link') {
            return $this->getAttr('outlink');
        }
        $suffix = static::$config['moduleurlsuffix']['category'] ?? static::$config['urlsuffix'];
        return addon_url('shop/category/index', $vars, $suffix, $domain);
    }


    public function getFlagList()
    {
        return ['hot' => __('Hot'), 'index' => __('Index'), 'recommend' => __('Recommend')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
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

    public function Attribute()
    {
        return $this->belongsTo('Attribute', 'id', 'category_id', [], 'LEFT');
    }

}
