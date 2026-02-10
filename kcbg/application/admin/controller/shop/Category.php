<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;
use fast\Tree;

/**
 * 分类管理
 *
 * @icon fa fa-circle-o
 */
class Category extends Backend
{

    /**
     * Category模型对象
     * @var \app\admin\model\shop\Category
     */
    protected $model = null;
    protected $noNeedRight = ["getList"];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Category;
        $this->view->assign("flagList", $this->model->getFlagList());
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
            //如果发送的来源是Selectpage，则转发到Selectpage
            $noKeyField = $this->request->param('noKeyField');
            $keyValue = $this->request->param('keyValue');

            if ($this->request->request('keyField') && $noKeyField != 100) {
                if (empty($keyValue)) {
                    $list = $this->selectpage()->getContent();
                    $list = (array)json_decode($list, true);
                    $list['list'] = array_merge([['id' => 0, 'name' => '无', 'pid' => 0]], $list['list']);
                    return json($list);
                }
                return $this->selectpage();
            }

            $categoryList = collection($this->model->with(['Attribute'])->order('weigh desc,id desc')->where(function ($query) use ($keyValue) {
                if ($keyValue != '') {
                    $query->where('id', 'IN', $keyValue);
                }
            })->select())->toArray();

            $total = count($categoryList);

            if ($total > 1) {
                $tree = Tree::instance();
                $tree->init($categoryList, 'pid');
                $categoryList = $tree->getTreeList($tree->getTreeArray(0));
            }

            if ($noKeyField == 100) {
                foreach ($categoryList as &$item) {
                    if (isset($item['spacer']) && $item['spacer']) {
                        $item['name'] = str_replace($item['spacer'], '', $item['name']);
                    }
                }
                return json([
                    "total" => $total,
                    "list"  => $categoryList
                ]);
            }

            return json([
                "total" => $total,
                "rows"  => $categoryList
            ]);
        }
        return $this->view->fetch();
    }


    public function getList()
    {
        $tree = Tree::instance();
        $tree->init(collection($this->model->order('weigh desc,id desc')->select())->toArray(), 'pid');
        $list = $tree->getTreeList($tree->getTreeArray(0), 'name');
        return json($list);
    }
}
