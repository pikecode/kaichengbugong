<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use app\admin\model\shop\Spec;
use app\admin\model\shop\GoodsSkuSpec;
use app\admin\model\shop\Goods;
use think\Db;

/**
 * 商品SKU管理
 *
 * @icon fa fa-circle-o
 */
class GoodsSku extends Backend
{

    /**
     * GoodsSku模型对象
     * @var \app\admin\model\shop\GoodsSku
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\GoodsSku;
    }

    //添加、编辑商品规格
    public function add()
    {

        $goods_id = $this->request->param('goods_id');
        if (!$goods_id) {
            $this->error('参数错误');
        }
        $goods = Goods::get($goods_id);
        if (empty($goods)) {
            $this->error('商品不存在');
        }
        if ($this->request->isPost()) {
            $skus = $this->request->post('skus/a', '', 'trim,xss_clean');
            $spec = $this->request->post('spec/a', '', 'trim,xss_clean');
            if (empty($skus)) {
                $this->model->where('goods_id', $goods_id)->delete();
                $goods->spectype = 0;
                $goods->save();
                $this->success('保存成功');
            }
            //坚持skus 和 spec 是否对上
            foreach ($skus as $item) {
                if (!isset($item['skus']) || !is_array($item['skus']) || empty($item['skus'])) {
                    $this->error('规格属性不能为空');
                }
                if (!isset($item['price']) || !isset($item['marketprice'])) {
                    $this->error('请录入价格');
                }
                if (($item['marketprice'] > 0 || $item['price'] > 0 || $item['stocks'] > 0) && $item['marketprice'] <= $item['price']) {
                    $this->error('市场价必须大于销售价');
                }
                foreach ($item['skus'] as $k => $v) {
                    if (empty($v)) {
                        $this->error('规格属性不能为空');
                    }
                    if (!isset($spec[$k]['value']) || empty($spec[$k]['name'])) {
                        $this->error('规格名称不能为空');
                    }
                    if (empty($spec[$k]['value']) || !in_array($v, $spec[$k]['value'])) {
                        $this->error('规格属性不匹配');
                    }
                }
            }

            // 启动事务
            Db::startTrans();
            try {
                //属性入库
                $spec_list = Spec::push($spec);
                $new_spec = GoodsSkuSpec::push($spec_list, $goods_id);
                //匹配属性
                $list = $this->model->where('goods_id', $goods_id)->select();
                $newData = [];
                $stocks = 0;
                foreach ($skus as $k => $sk) {
                    if (isset($list[$k])) {
                        $row = $list[$k];
                        $sku_ids1 = explode(',', $row['sku_id']);
                        sort($sku_ids1);
                        $sku_ids2 = $this->getSkuId($sk['skus'], $new_spec);
                        //相等的更新
                        if (implode(',', $sku_ids1) == $sku_ids2) {
                            $row->save([
                                'goods_sn'    => isset($sk['goods_sn']) ? $sk['goods_sn'] : '',
                                'image'       => isset($sk['image']) ? $sk['image'] : '',
                                'price'       => isset($sk['price']) ? $sk['price'] : '',
                                'marketprice' => isset($sk['marketprice']) ? $sk['marketprice'] : '',
                                'stocks'      => isset($sk['stocks']) ? $sk['stocks'] : ''
                            ]);
                            //不等的
                        } else {
                            $row->save([
                                'goods_sn'    => isset($sk['goods_sn']) ? $sk['goods_sn'] : '',
                                'image'       => isset($sk['image']) ? $sk['image'] : '',
                                'price'       => isset($sk['price']) ? $sk['price'] : '',
                                'marketprice' => isset($sk['marketprice']) ? $sk['marketprice'] : '',
                                'stocks'      => isset($sk['stocks']) ? $sk['stocks'] : '',
                                'sku_id'      => $this->getSkuId($sk['skus'], $new_spec),
                                'sales'       => 0
                            ]);
                        }
                        unset($list[$k]);
                    } else { //多余的
                        $newData[] = [
                            'goods_id'    => $goods_id,
                            'goods_sn'    => isset($sk['goods_sn']) ? $sk['goods_sn'] : '',
                            'image'       => isset($sk['image']) ? $sk['image'] : '',
                            'price'       => isset($sk['price']) ? $sk['price'] : '',
                            'marketprice' => isset($sk['marketprice']) ? $sk['marketprice'] : '',
                            'stocks'      => isset($sk['stocks']) ? $sk['stocks'] : '',
                            'sku_id'      => $this->getSkuId($sk['skus'], $new_spec),
                        ];
                    }
                    $stocks = bcadd($stocks, isset($sk['stocks']) ? $sk['stocks'] : 0);
                }

                if (!empty($newData)) {
                    $this->model->saveAll($newData);
                }
                $goods->save(['stocks' => $stocks, 'spectype' => (empty($skus) ? 0 : 1)]);
                //原来多的删除
                foreach ($list as $it) {
                    $it->delete();
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error($e->getMessage());
            }

            $this->success('添加成功');
        }
        //查询属性输出
        $list = $this->model->field("sku.*,GROUP_CONCAT(sp.name,':',sv.value ORDER BY sp.id asc) sku_attr")
            ->alias('sku')
            ->where('sku.goods_id', $goods_id)
            ->join('shop_goods_sku_spec p', "FIND_IN_SET(p.id,sku.sku_id)", 'LEFT')
            ->join('shop_spec sp', 'sp.id=p.spec_id', 'LEFT')
            ->join('shop_spec_value sv', 'sv.id=p.spec_value_id', 'LEFT')
            ->group('sku.id')
            ->select();

        $this->assignconfig('goods_skus', $list);
        $this->assignconfig('goods', $goods);
        return $this->view->fetch();
    }


    protected function getSkuId($skus, $new_spec)
    {
        $arr = [];
        foreach ($skus as $item) {
            $arr[] = $new_spec[$item]['id'];
        }
        sort($arr);
        return implode(',', $arr);
    }
}
