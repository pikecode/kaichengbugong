<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 订单商品管理
 *
 * @icon fa fa-circle-o
 */
class OrderGoods extends Backend
{

    /**
     * OrderGoods模型对象
     * @var \app\admin\model\shop\OrderGoods
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\OrderGoods;
        $this->view->assign("aftersalestateList", $this->model->getSalestateList());
        $this->view->assign("commentstateList", $this->model->getCommentstateList());
    }

}
