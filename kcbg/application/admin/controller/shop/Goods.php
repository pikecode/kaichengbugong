<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\shop\GoodsAttr;
use app\admin\model\shop\Spec;
use app\admin\model\shop\GoodsSkuSpec;

/**
 * 商品管理
 *
 * @icon fa fa-circle-o
 */
class Goods extends Backend
{

    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'id,goods_sn,title,subtitle';

    /**
     * Goods模型对象
     * @var \app\admin\model\shop\Goods
     */
    protected $model = null;
    protected $sku_model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Goods;
        $this->sku_model = new \app\admin\model\shop\GoodsSku;

        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['Freight', 'Brand', 'Category'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 查看
     */
    public function select()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['Freight', 'Brand', 'Category'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    //检查属性skus 和 spec 是否对上
    protected function checkSku($skus, $spec)
    {
        foreach ($skus as $item) {
            if (!isset($item['skus']) || !is_array($item['skus']) || empty($item['skus'])) {
                throw new Exception('规格属性不能为空');
            }
            if (!isset($item['price']) || !isset($item['marketprice'])) {
                throw new Exception('请录入价格');
            }
            if (($item['marketprice'] > 0 || $item['price'] > 0 || $item['stocks'] > 0) && $item['marketprice'] <= $item['price']) {
                throw new Exception('市场价必须大于销售价');
            }
            foreach ($item['skus'] as $k => $v) {
                if (empty($v) && !is_numeric($v)) {
                    throw new Exception('规格【' . $v . '】属性值不能为空');
                }
                if (!isset($spec[$k]['value']) || (empty($spec[$k]['name']) && !is_numeric($spec[$k]['name']))) {
                    throw new Exception('规格【' . $v . '】名称不能为空');
                }
                foreach ($spec[$k]['value'] as $m => $n) {
                    if (stripos($n, ',') !== false) {
                        throw new Exception('规格【' . $v . '】属性值中不能包含,');
                    }
                }
                if (count($spec[$k]['value']) != count(array_unique($spec[$k]['value']))) {
                    throw new Exception('规格【' . $v . '】属性值中不能有重复值');
                }
                if (empty($spec[$k]['value']) || !in_array($v, $spec[$k]['value'])) {
                    throw new Exception('规格【' . $v . '】属性不匹配');
                }
            }
        }
    }

    protected function getSkuId($skus, $newSpec, $spec)
    {
        $arr = [];
        foreach ($skus as $index => $item) {
            $specArr = $spec[$index];
            foreach ($newSpec as $subindex => $subitem) {
                if ($subitem['spec_name'] == $specArr['name'] && $subitem['spec_value_value'] == $item) {
                    $arr[] = $subitem['id'];
                }
            }
        }
        sort($arr);
        return implode(',', $arr);
    }

    //添加商品属性
    protected function addGoodsSku($skus, $spec, $goods_id)
    {
        //属性入库
        $specList = Spec::push($spec);
        $newSpec = GoodsSkuSpec::push($specList, $goods_id);
        //匹配属性
        $list = $this->sku_model->where('goods_id', $goods_id)->select();
        $newData = [];
        $stocks = 0;
        foreach ($skus as $k => $sk) {
            $newSkuId = $this->getSkuId($sk['skus'], $newSpec, $spec);
            $newSkuData = [
                'goods_id'    => $goods_id,
                'sku_id'      => $newSkuId,
                'goods_sn'    => $sk['goods_sn'] ?? '',
                'image'       => $sk['image'] ?? '',
                'price'       => $sk['price'] ?? 0,
                'marketprice' => $sk['marketprice'] ?? 0,
                'stocks'      => $sk['stocks'] ?? 0,
            ];
            if (isset($list[$k])) {
                $row = $list[$k];
                $oldSkuIdsArr = explode(',', $row['sku_id']);
                sort($oldSkuIdsArr);
                $oldSkuId = implode(',', $oldSkuIdsArr);

                if ($oldSkuId == $newSkuId) {
                    //相等的更新
                    $row->save($newSkuData);
                } else {
                    //不等的
                    $row->save(array_merge($newSkuData, ['sales' => 0]));
                }
                unset($list[$k]);
            } else { //多余的
                $newData[] = array_merge($newSkuData, ['sales' => 0]);
            }
            $stocks = bcadd($stocks, $sk['stocks'] ?? 0);
        }
        if (!empty($newData)) {
            $this->sku_model->saveAll($newData);
        }
        //更新库存
        if (!empty($skus)) {
            $this->model->where('id', $goods_id)->update(['stocks' => $stocks, 'spectype' => 1]);
        } else {
            $this->model->where('id', $goods_id)->update(['spectype' => 0]);
        }
        //原来多的删除
        foreach ($list as $it) {
            $it->delete();
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    //商品规格
                    if (isset($params['skus']) && isset($params['spec'])) {
                        $params['skus'] = (array)json_decode($params['skus'], true);
                        $params['spec'] = (array)json_decode($params['spec'], true);
                        $this->checkSku($params['skus'], $params['spec']);
                        $this->addGoodsSku($params['skus'], $params['spec'], $this->model->id);
                    }
                    //商品属性
                    if (isset($params['attribute_ids'])) {
                        GoodsAttr::addGoodsAttr($params['attribute_ids'], $this->model->id);
                    }
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    //商品规格
                    if (isset($params['skus']) && isset($params['spec'])) {
                        $params['skus'] = (array)json_decode($params['skus'], true);
                        $params['spec'] = (array)json_decode($params['spec'], true);
                        $this->checkSku($params['skus'], $params['spec']);
                        $this->addGoodsSku($params['skus'], $params['spec'], $row->id);
                    }
                    //商品属性
                    if (isset($params['attribute_ids'])) {
                        GoodsAttr::addGoodsAttr($params['attribute_ids'], $row->id);
                    }
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        //查询属性输出
        $list = $this->sku_model->field("sku.*,GROUP_CONCAT(sp.name,':',sv.value ORDER BY sp.id asc) sku_attr")
            ->alias('sku')
            ->where('sku.goods_id', $row->id)
            ->join('shop_goods_sku_spec p', "FIND_IN_SET(p.id,sku.sku_id)", 'LEFT')
            ->join('shop_spec sp', 'sp.id=p.spec_id', 'LEFT')
            ->join('shop_spec_value sv', 'sv.id=p.spec_value_id', 'LEFT')
            ->group('sku.id')
            ->select();
        $this->view->assign("row", $row);
        $this->assignconfig('goods', $row);
        $this->assignconfig('goods_skus', $list);
        return $this->view->fetch();
    }


}
