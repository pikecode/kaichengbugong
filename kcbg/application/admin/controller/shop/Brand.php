<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 品牌管理
 *
 * @icon fa fa-circle-o
 */
class Brand extends Backend
{

    /**
     * Brand模型对象
     * @var \app\admin\model\shop\Brand
     */
    protected $model = null;
    protected $noNeedRight = ["getList"];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Brand;

    }

    public function getList()
    {
        $list = $this->model->field('id,name')->select();
        return json($list);
    }

}
