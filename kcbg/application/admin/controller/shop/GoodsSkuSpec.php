<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class GoodsSkuSpec extends Backend
{

    /**
     * GoodsSkuSpec模型对象
     * @var \app\admin\model\shop\GoodsSkuSpec
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\GoodsSkuSpec;

    }

}
