<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 优惠券管理
 *
 * @icon fa fa-circle-o
 */
class Coupon extends Backend
{

    /**
     * Coupon模型对象
     * @var \app\admin\model\shop\Coupon
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\Coupon;
        $this->view->assign("resultList", $this->model->getResultList());
        $this->view->assign("isOpenList", $this->model->getIsOpenList());
        $this->view->assign("IsPrivateList", $this->model->getIsPrivateList());
        $this->view->assign("modeList", $this->model->getModeList());
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
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
            $this->relationSearch = true;
            list($where, $sort, $order, $offset, $limit, $page, $alias, $bind) = $this->buildparams();

            $aliasName = reset($alias);
            $list = $this->model
                ->field($aliasName . '.*,GROUP_CONCAT(n.name) condition_name')
                ->alias($aliasName)
                ->bind($bind)
                ->join('shop_coupon_condition n', 'FIND_IN_SET(n.id,' . $aliasName . '.condition_ids)', 'LEFT')
                ->where($where)
                ->order($sort, $order)
                ->group($aliasName . '.id')
                ->paginate($limit);
            foreach ($list as $index => $item) {
                $item['expired'] = $item['endtime'] < time() ? true : false;
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $config = get_addon_config('shop');
        $this->assignconfig('mobileurl', '');
        return $this->view->fetch();
    }


}
