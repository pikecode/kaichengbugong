<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 商品规格管理
 *
 * @icon fa fa-circle-o
 */
class Spec extends Backend
{

    /**
     * Spec模型对象
     * @var \app\admin\model\shop\Spec
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Spec;

    }

}
