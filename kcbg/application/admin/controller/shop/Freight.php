<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 运费模板
 *
 * @icon fa fa-circle-o
 */
class Freight extends Backend
{

    /**
     * Freight模型对象
     * @var \app\admin\model\shop\Freight
     */
    protected $model = null;
    protected $noNeedRight = ["getList"];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Freight;
        $this->view->assign("typeList", $this->model->getTypeList());
    }

    public function getList()
    {
        $list = $this->model->field('id,name')->select();
        return json($list);
    }

}
