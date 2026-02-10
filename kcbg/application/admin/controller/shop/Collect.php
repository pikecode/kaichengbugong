<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 收藏管理
 *
 * @icon fa fa-circle-o
 */
class Collect extends Backend
{

    /**
     * Collect模型对象
     * @var \app\admin\model\shop\Collect
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Collect;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['User', 'Goods'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $index => $item) {
                if ($item->user) {
                    $item->user->visible(['id', 'nickname', 'avatar']);
                }
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


}
