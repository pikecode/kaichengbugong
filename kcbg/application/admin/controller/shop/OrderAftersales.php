<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 退货单管理
 *
 * @icon fa fa-circle-o
 */
class OrderAftersales extends Backend
{

    /**
     * OrderAftersales模型对象
     * @var \app\admin\model\shop\OrderAftersales
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'id,OrderGoods.order_sn';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\OrderAftersales;
        $this->view->assign("typeList", $this->model->getTypeList());
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
            list($where, $sort, $order, $offset, $limit, $page, $alias, $bind) = $this->buildparams();
            $list = $this->model
                ->with(['User', 'OrderGoods'])
                ->alias(['order_goods' => 'OrderGoods'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $index => $item) {
                if ($item->user) {
                    $item->user->visible(['id', 'nickname', 'avatar']);
                }
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function edit($ids = null)
    {
        $order_goods_id = $this->request->param("order_goods_id");
        if ($order_goods_id) {
            $row = $this->model->where('order_goods_id', $order_goods_id)->order('id', 'desc')->find();
            if ($row) {
                $ids = $row['id'];
            }
        }
        return parent::edit($ids);
    }

}
