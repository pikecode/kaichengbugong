<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 快递公司
 *
 * @icon fa fa-circle-o
 */
class Shipper extends Backend
{

    /**
     * Shipper模型对象
     * @var \app\admin\model\shop\Shipper
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Shipper;
    }

}
