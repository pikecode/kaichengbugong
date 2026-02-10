<?php

namespace app\admin\model\shop;

use think\Model;


class GoodsAttr extends Model
{

    // 表名
    protected $name = 'shop_goods_attr';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];


    protected static function init()
    {
    }

    //判断是否已存在
    protected static function isInOldData($list, $info)
    {
        foreach ($list as $key => $item) {
            if ($item['attribute_id'] == $info['attribute_id'] && $item['value_id'] == $info['value_id']) {
                return $key;
            }
        }
        return false;
    }

    //添加商品属性
    public static function addGoodsAttr($param, $goods_id)
    {
        if (!$goods_id) {
            throw new \Exception('商品未找到');
        }
        //原有的
        $list = self::where('goods_id', $goods_id)->select();
        //现在的
        $data = [];
        foreach ($param as $aid => $item) {
            foreach ($item as $res) {
                if (!$res) {
                    continue;
                }
                $row = [
                    'attribute_id' => $aid,
                    'value_id'     => $res,
                    'goods_id'     => $goods_id,
                ];
                //原来已有
                $k = self::isInOldData($list, $row);
                if ($k !== false) {
                    unset($list[$k]);
                } else {
                    //原来没有
                    $data[] = $row;
                }
            }
        }
        //匹配不到的去掉
        foreach ($list as $info) {
            $info->delete();
        }
        //新的数据
        (new self())->saveAll($data);
    }
}
