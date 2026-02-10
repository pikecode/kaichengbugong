<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 服务保障
 *
 * @icon fa fa-circle-o
 */
class Guarantee extends Backend
{

    /**
     * Guarantee模型对象
     * @var \app\admin\model\shop\Guarantee
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Guarantee;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

}
