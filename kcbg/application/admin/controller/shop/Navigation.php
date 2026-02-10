<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 导航配置
 *
 * @icon fa fa-circle-o
 */
class Navigation extends Backend
{

    /**
     * Navigation模型对象
     * @var \app\admin\model\shop\Navigation
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Navigation;

    }

}
