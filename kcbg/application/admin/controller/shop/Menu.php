<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use fast\Tree;

/**
 * 菜单管理
 *
 * @icon fa fa-circle-o
 */
class Menu extends Backend
{

    /**
     * Menu模型对象
     * @var \app\admin\model\shop\Menu
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Menu;

        $tree = Tree::instance();
        $tree->init(collection($this->model->order('weigh desc,id desc')->select())->toArray(), 'pid');
        $this->categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = [0 => ['type' => 'all', 'name' => __('None')]];
        foreach ($this->categorylist as $k => $v) {
            $categorydata[$v['id']] = $v;
        }
        $this->view->assign("parentList", $categorydata);
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $search = $this->request->request("search");
            //构造父类select列表选项数据
            $list = [];
            foreach ($this->categorylist as $k => $v) {
                if ($search) {
                    if (stripos($v['name'], $search) !== false) {
                        $list[] = $v;
                    }
                } else {
                    $list[] = $v;
                }
            }
            $total = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


}
