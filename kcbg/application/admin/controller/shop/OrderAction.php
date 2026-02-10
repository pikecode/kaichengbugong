<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 订单操作记录管理
 *
 * @icon fa fa-circle-o
 */
class OrderAction extends Backend
{

    /**
     * OrderAction模型对象
     * @var \app\admin\model\shop\OrderAction
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\OrderAction;

    }

}
