<?php

namespace app\admin\model\shop;

use think\Model;
use traits\model\SoftDelete;

class Page extends Model
{

    use SoftDelete;

    // 表名
    protected $name = 'shop_page';
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
    protected static $config = [];

    protected static function init()
    {
        self::$config = $config = get_addon_config('shop');
        self::beforeInsert(function ($row) {
            if (!isset($row['admin_id']) || !$row['admin_id']) {
                $admin_id = session('admin.id');
                $row['admin_id'] = $admin_id ? $admin_id : 0;
            }
        });
        self::beforeWrite(function ($row) {
            //在更新之前对数组进行处理
            foreach ($row->getData() as $k => $value) {
                if (is_array($value) && is_array(reset($value))) {
                    $value = json_encode(self::getArrayData($value), JSON_UNESCAPED_UNICODE);
                } else {
                    $value = is_array($value) ? implode(',', $value) : $value;
                }
                $row->setAttr($k, $value);
            }
        });
        self::afterInsert(function ($row) {
            $row->save(['weigh' => $row['id']]);
        });
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
        $time = $data['createtime'] ?? time();

        $vars = [
            ':id'      => $data['id'],
            ':diyname' => $diyname,
            ':year'    => date("Y", $time),
            ':month'   => date("m", $time),
            ':day'     => date("d", $time)
        ];
        return addon_url('shop/page/index', $vars, static::$config['urlsuffix'], $domain);
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }

    public function getFlagList()
    {
        $config = get_addon_config('shop');
        return $config['flagtype'];
    }

    public function getFlagTextAttr($value, $data)
    {
        $value = $value ? $value : $data['flag'];
        $valueArr = explode(',', $value);
        $list = $this->getFlagList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    public static function getArrayData($data)
    {
        if (!isset($data['value'])) {
            $result = [];
            foreach ($data as $index => $datum) {
                $result['field'][$index] = $datum['key'];
                $result['value'][$index] = $datum['value'];
            }
            $data = $result;
        }
        $fieldarr = $valuearr = [];
        $field = isset($data['field']) ? $data['field'] : (isset($data['key']) ? $data['key'] : []);
        $value = isset($data['value']) ? $data['value'] : [];
        foreach ($field as $m => $n) {
            if ($n != '') {
                $fieldarr[] = $field[$m];
                $valuearr[] = $value[$m];
            }
        }
        return $fieldarr ? array_combine($fieldarr, $valuearr) : [];
    }
}
