<?php

namespace app\admin\model\shop;

use think\Model;


class GoodsSkuSpec extends Model
{


    // 表名
    protected $name = 'shop_goods_sku_spec';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];

    public static function push($spec_list, $goods_id)
    {
        $data = [];
        $old_list = self::where('goods_id', $goods_id)->select(); //原有的
        $new_list = $spec_list; //现有的
        //匹配相等的不动
        foreach ($old_list as $key => $item) {
            foreach ($new_list as $index => $res) {
                if ($res['spec_id'] == $item['spec_id'] && $res['spec_value_id'] == $item['spec_value_id']) {
                    $res['id'] = $item['id'];
                    $data[$index] = $res; //保留出去
                    unset($old_list[$key]);
                    unset($new_list[$index]);
                }
            }
        }
        //不等的替换
        $k = 0;
        foreach ($new_list as $idx => $row) {
            if (isset($old_list[$k])) {
                $spec = $old_list[$k];
                $row['id'] = $spec['id'];
                $spec->spec_id = $row['spec_id'];
                $spec->spec_value_id = $row['spec_value_id'];
                $spec->save();
                unset($old_list[$k]);
            } else { //多余的
                $row['goods_id'] = $goods_id;
                $model = new self();
                $model->allowField(true);
                $model->save($row);
                $row['id'] = $model->id;
            }
            $data[$idx] = $row;
            $k++;
        }
        //或者老的多
        foreach ($old_list as $item) {
            $item->delete();
        }
        return $data;
    }
}
