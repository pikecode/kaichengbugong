<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 搜索记录管理
 *
 * @icon fa fa-circle-o
 */
class SearchLog extends Backend
{

    /**
     * SearchLog模型对象
     * @var \app\admin\model\shop\SearchLog
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\SearchLog;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

}
