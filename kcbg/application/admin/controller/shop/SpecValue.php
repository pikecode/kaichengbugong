<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 规格数据管理
 *
 * @icon fa fa-circle-o
 */
class SpecValue extends Backend
{

    /**
     * SpecValue模型对象
     * @var \app\admin\model\shop\SpecValue
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\SpecValue;

    }

}
